<?php

namespace Tests\Feature;

use App\Models\SalesChannel;
use App\Models\SalesChannelFee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesCompanyContext;
use Tests\TestCase;

class DashboardQuickPriceCalculatorTest extends TestCase
{
    use CreatesCompanyContext;
    use RefreshDatabase;

    public function test_dashboard_quick_price_calculator_displays_channel_adjusted_values(): void
    {
        $context = $this->createCompanyContext();
        $company = $context['company'];
        $user = $context['user'];
        $user->forceFill(['email_verified_at' => now()])->save();

        $salesChannel = SalesChannel::create([
            'company_id' => $company->id,
            'name' => 'iFood',
            'slug' => 'ifood',
            'notes' => null,
            'is_active' => true,
        ]);

        SalesChannelFee::create([
            'company_id' => $company->id,
            'sales_channel_id' => $salesChannel->id,
            'name' => 'Comissao',
            'type' => 'percentage',
            'value' => 12,
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('dashboard.quick-price'), [
                'product_name' => 'Brigadeiro',
                'recipe_total_cost' => 40,
                'yield_quantity' => 10,
                'packaging_cost' => 0,
                'other_costs' => 0,
                'profit_margin_percentage' => 100,
                'sales_channel_id' => $salesChannel->id,
            ]);

        $response->assertOk();
        $response->assertSee('Brigadeiro');
        $response->assertSee('4,00');
        $response->assertSee('8,00');
        $response->assertSee('9,09');
        $response->assertSee('40,00');
        $response->assertSee('12,00%');
    }
}
