<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\SalesChannel;
use App\Models\SalesChannelFee;
use Illuminate\Database\Seeder;

class SalesChannelSeeder extends Seeder
{
    public function run(): void
    {
        $channelTemplates = [
            [
                'name' => 'Loja / Balcao',
                'slug' => 'loja-balcao',
                'notes' => 'Venda direta sem taxas de marketplace.',
                'is_active' => true,
                'fees' => [],
            ],
            [
                'name' => 'WhatsApp',
                'slug' => 'whatsapp',
                'notes' => 'Canal direto para pedidos fechados fora de marketplace.',
                'is_active' => true,
                'fees' => [],
            ],
            [
                'name' => 'iFood Entrega',
                'slug' => 'ifood-entrega',
                'notes' => 'Exemplo inicial. Ajuste as taxas conforme o contrato vigente da sua loja.',
                'is_active' => true,
                'fees' => [
                    [
                        'name' => 'Comissao do canal',
                        'type' => 'percentage',
                        'value' => 27.00,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Taxa fixa por pedido',
                        'type' => 'fixed',
                        'value' => 0.00,
                        'is_active' => false,
                    ],
                ],
            ],
            [
                'name' => 'iFood Balcao',
                'slug' => 'ifood-balcao',
                'notes' => 'Exemplo inicial. Ajuste as taxas conforme o contrato vigente da sua loja.',
                'is_active' => true,
                'fees' => [
                    [
                        'name' => 'Comissao do canal',
                        'type' => 'percentage',
                        'value' => 12.00,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Taxa fixa por pedido',
                        'type' => 'fixed',
                        'value' => 0.00,
                        'is_active' => false,
                    ],
                ],
            ],
        ];

        Company::query()->each(function (Company $company) use ($channelTemplates) {
            foreach ($channelTemplates as $template) {
                $salesChannel = SalesChannel::firstOrCreate(
                    [
                        'company_id' => $company->id,
                        'slug' => $template['slug'],
                    ],
                    [
                        'name' => $template['name'],
                        'notes' => $template['notes'],
                        'is_active' => $template['is_active'],
                    ],
                );

                foreach ($template['fees'] as $feeTemplate) {
                    SalesChannelFee::firstOrCreate(
                        [
                            'company_id' => $company->id,
                            'sales_channel_id' => $salesChannel->id,
                            'name' => $feeTemplate['name'],
                        ],
                        [
                            'type' => $feeTemplate['type'],
                            'value' => $feeTemplate['value'],
                            'is_active' => $feeTemplate['is_active'],
                        ],
                    );
                }
            }
        });
    }
}