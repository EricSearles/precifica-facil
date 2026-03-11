<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductChannelPrice;
use App\Models\SalesChannel;
use App\Models\SalesChannelFee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesCompanyContext;
use Tests\TestCase;

class ProductChannelPricingTest extends TestCase
{
    use CreatesCompanyContext;
    use RefreshDatabase;

    public function test_product_channel_price_is_calculated_and_persisted(): void
    {
        $context = $this->createCompanyContext();
        $company = $context['company'];
        $user = $context['user'];

        $product = Product::create([
            'company_id' => $company->id,
            'category_id' => null,
            'name' => 'Produto Canal',
            'sale_unit' => 'un',
            'yield_quantity' => 1,
            'profit_margin_type' => 'percentage',
            'profit_margin_value' => 0,
            'use_global_margin' => false,
            'calculated_unit_cost' => 10,
            'suggested_sale_price' => 10,
            'notes' => null,
            'is_active' => true,
        ]);

        $salesChannel = SalesChannel::create([
            'company_id' => $company->id,
            'name' => 'iFood Entrega',
            'slug' => 'ifood-entrega',
            'notes' => null,
            'is_active' => true,
        ]);

        SalesChannelFee::create([
            'company_id' => $company->id,
            'sales_channel_id' => $salesChannel->id,
            'name' => 'Comissao',
            'type' => 'percentage',
            'value' => 20,
            'is_active' => true,
        ]);

        SalesChannelFee::create([
            'company_id' => $company->id,
            'sales_channel_id' => $salesChannel->id,
            'name' => 'Taxa fixa',
            'type' => 'fixed',
            'value' => 2,
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('product-channel-prices.store'), [
                'product_id' => $product->id,
                'sales_channel_id' => $salesChannel->id,
                'desired_net_value' => 10,
            ]);

        $response->assertRedirect(route('products.edit', $product->id));

        $price = ProductChannelPrice::query()
            ->where('product_id', $product->id)
            ->where('sales_channel_id', $salesChannel->id)
            ->firstOrFail();

        $this->assertSame('10.00', $price->reference_price);
        $this->assertSame('10.00', $price->desired_net_value);
        $this->assertSame('3.00', $price->percentage_fee_total);
        $this->assertSame('2.00', $price->fixed_fee_total);
        $this->assertSame('5.00', $price->fee_total);
        $this->assertSame('15.00', $price->channel_price);
        $this->assertSame('10.00', $price->net_value);
    }

    public function test_saved_channel_price_is_recalculated_when_product_price_changes(): void
    {
        $context = $this->createCompanyContext([
            'default_profit_margin' => 50,
        ]);
        $company = $context['company'];

        $product = Product::create([
            'company_id' => $company->id,
            'category_id' => null,
            'name' => 'Produto Atualizavel',
            'sale_unit' => 'un',
            'yield_quantity' => 1,
            'profit_margin_type' => 'percentage',
            'profit_margin_value' => 0,
            'use_global_margin' => false,
            'calculated_unit_cost' => 10,
            'suggested_sale_price' => 10,
            'notes' => null,
            'is_active' => true,
        ]);

        $salesChannel = SalesChannel::create([
            'company_id' => $company->id,
            'name' => 'Marketplace',
            'slug' => 'marketplace',
            'notes' => null,
            'is_active' => true,
        ]);

        SalesChannelFee::create([
            'company_id' => $company->id,
            'sales_channel_id' => $salesChannel->id,
            'name' => 'Comissao',
            'type' => 'percentage',
            'value' => 20,
            'is_active' => true,
        ]);

        ProductChannelPrice::create([
            'company_id' => $company->id,
            'product_id' => $product->id,
            'sales_channel_id' => $salesChannel->id,
            'reference_price' => 10,
            'desired_net_value' => 10,
            'percentage_fee_total' => 2,
            'fixed_fee_total' => 0,
            'fee_total' => 2,
            'channel_price' => 12.5,
            'net_value' => 10,
        ]);

        $product->profit_margin_type = 'percentage';
        $product->profit_margin_value = 50;
        $product->calculated_unit_cost = 20;
        $product->suggested_sale_price = 30;

        app(\App\Services\ProductService::class)->update($product, [
            'category_id' => null,
            'name' => $product->name,
            'sale_unit' => $product->sale_unit,
            'yield_quantity' => $product->yield_quantity,
            'profit_margin_type' => 'percentage',
            'profit_margin_value' => 50,
            'use_global_margin' => false,
            'notes' => null,
            'is_active' => true,
        ]);

        $price = ProductChannelPrice::query()->firstOrFail();

        $this->assertSame('30.00', $price->reference_price);
        $this->assertSame('30.00', $price->desired_net_value);
        $this->assertSame('37.50', $price->channel_price);
        $this->assertSame('7.50', $price->fee_total);
        $this->assertSame('30.00', $price->net_value);
    }
}