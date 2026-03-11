<?php

namespace Tests\Feature\Auth;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesCompanyContext;
use Tests\TestCase;

class AuthenticatedAreaTest extends TestCase
{
    use CreatesCompanyContext;
    use RefreshDatabase;

    public function test_authenticated_user_only_accesses_records_from_their_company(): void
    {
        $firstContext = $this->createCompanyContext([
            'company_name' => 'Empresa Um',
            'company_email' => 'empresa-um@example.com',
            'user_email' => 'um@example.com',
        ]);

        $secondContext = $this->createCompanyContext([
            'company_name' => 'Empresa Dois',
            'company_email' => 'empresa-dois@example.com',
            'user_email' => 'dois@example.com',
        ]);

        $foreignProduct = Product::create([
            'company_id' => $firstContext['company']->id,
            'category_id' => null,
            'name' => 'Produto de outra empresa',
            'sale_unit' => 'un',
            'yield_quantity' => 1,
            'profit_margin_type' => 'percentage',
            'profit_margin_value' => 30,
            'use_global_margin' => false,
            'calculated_unit_cost' => 10,
            'suggested_sale_price' => 13,
            'notes' => null,
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($secondContext['user'])
            ->get(route('products.edit', $foreignProduct->id));

        $response->assertNotFound();
    }
}