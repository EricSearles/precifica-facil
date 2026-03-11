<?php

namespace Tests\Feature;

use App\Models\ExtraCost;
use App\Models\Ingredient;
use App\Models\Packaging;
use App\Models\Product;
use App\Models\ProductPackaging;
use App\Models\Recipe;
use App\Models\RecipeItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesCompanyContext;
use Tests\TestCase;

class RecipeRecalculationTest extends TestCase
{
    use CreatesCompanyContext;
    use RefreshDatabase;

    public function test_recipe_recalculation_updates_recipe_and_product_costs(): void
    {
        $context = $this->createCompanyContext();
        $company = $context['company'];
        $user = $context['user'];

        $product = Product::create([
            'company_id' => $company->id,
            'category_id' => null,
            'name' => 'Brigadeiro',
            'sale_unit' => 'un',
            'yield_quantity' => 4,
            'profit_margin_type' => 'percentage',
            'profit_margin_value' => 50,
            'use_global_margin' => false,
            'calculated_unit_cost' => 0,
            'suggested_sale_price' => 0,
            'notes' => null,
            'is_active' => true,
        ]);

        $recipe = Recipe::create([
            'company_id' => $company->id,
            'product_id' => $product->id,
            'name' => 'Receita Brigadeiro',
            'yield_quantity' => 4,
            'yield_unit' => 'un',
            'ingredients_cost_total' => 0,
            'extra_cost_total' => 0,
            'packaging_cost_total' => 0,
            'recipe_total_cost' => 0,
            'unit_cost' => 0,
            'suggested_sale_price' => 0,
            'preparation_method' => null,
            'notes' => null,
        ]);

        $ingredient = Ingredient::create([
            'company_id' => $company->id,
            'name' => 'Chocolate',
            'brand' => 'Marca Teste',
            'purchase_unit' => 'kg',
            'purchase_quantity' => 2,
            'purchase_price' => 10,
            'base_unit' => 'kg',
            'base_quantity' => 2,
            'unit_cost' => 0,
            'notes' => null,
            'is_active' => true,
        ]);

        RecipeItem::create([
            'company_id' => $company->id,
            'recipe_id' => $recipe->id,
            'ingredient_id' => $ingredient->id,
            'quantity_used' => 3,
            'unit_used' => 'un',
            'unit_cost_snapshot' => 0,
            'total_cost' => 0,
        ]);

        ExtraCost::create([
            'company_id' => $company->id,
            'product_id' => $product->id,
            'recipe_id' => $recipe->id,
            'description' => 'Gas',
            'type' => 'fixed',
            'value' => 5,
        ]);

        $packaging = Packaging::create([
            'company_id' => $company->id,
            'name' => 'Caixa',
            'unit_cost' => 2,
            'notes' => null,
        ]);

        ProductPackaging::create([
            'company_id' => $company->id,
            'product_id' => $product->id,
            'packaging_id' => $packaging->id,
            'quantity' => 2,
            'total_cost' => 4,
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('recipes.recalculate', $recipe->id));

        $response->assertRedirect(route('recipes.show', $recipe->id));

        $recipe->refresh();
        $product->refresh();
        $item = $recipe->items()->firstOrFail();

        $this->assertSame('5.00', $item->unit_cost_snapshot);
        $this->assertSame('15.00', $item->total_cost);
        $this->assertSame('15.00', $recipe->ingredients_cost_total);
        $this->assertSame('5.00', $recipe->extra_cost_total);
        $this->assertSame('4.00', $recipe->packaging_cost_total);
        $this->assertSame('24.00', $recipe->recipe_total_cost);
        $this->assertSame('6.00', $recipe->unit_cost);
        $this->assertSame('9.00', $recipe->suggested_sale_price);
        $this->assertSame('6.00', $product->calculated_unit_cost);
        $this->assertSame('9.00', $product->suggested_sale_price);
    }
}