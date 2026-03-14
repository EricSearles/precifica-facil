<?php

namespace Database\Seeders;

use App\Models\Billing\BillingPlan;
use Illuminate\Database\Seeder;

class BillingPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'code' => BillingPlan::CODE_STARTER,
                'name' => 'Plano Iniciante',
                'description' => 'Ideal para quem esta comecando e precisa estruturar a precificacao basica.',
                'price_cents' => 1490,
                'currency' => 'BRL',
                'billing_cycle' => 'monthly',
                'trial_days' => 14,
                'is_active' => true,
                'metadata' => [
                    'audience' => [
                        'doceiras',
                        'quem esta comecando',
                        'producao pequena',
                    ],
                    'limits' => [
                        'ingredients' => 20,
                        'products' => 15,
                        'recipes' => 15,
                        'users' => 1,
                    ],
                    'features' => [
                        'basic_price_calculation' => true,
                        'profit_margin' => true,
                        'extra_costs' => true,
                        'recipes' => true,
                        'sales_channels' => false,
                        'marketplace_fees' => false,
                        'multiple_users' => false,
                        'advanced_reports' => false,
                        'dashboard_basic' => false,
                        'dashboard_advanced' => false,
                        'cost_history' => false,
                        'pdf_export' => false,
                        'multiple_channels' => false,
                    ],
                    'highlights' => [
                        'calculo automatico',
                        'custo unitario',
                        'preco sugerido',
                        'cadastro de receitas',
                    ],
                ],
            ],
            [
                'code' => BillingPlan::CODE_PROFESSIONAL,
                'name' => 'Plano Profissional',
                'description' => 'Plano mais importante para operacoes regulares e vendas em marketplaces.',
                'price_cents' => 2590,
                'currency' => 'BRL',
                'billing_cycle' => 'monthly',
                'trial_days' => 14,
                'is_active' => true,
                'metadata' => [
                    'audience' => [
                        'producao regular',
                        'quem vende em marketplaces',
                        'pequenas empresas',
                    ],
                    'limits' => [
                        'ingredients' => 200,
                        'products' => 200,
                        'recipes' => 200,
                        'users' => 3,
                    ],
                    'features' => [
                        'basic_price_calculation' => true,
                        'profit_margin' => true,
                        'extra_costs' => true,
                        'recipes' => true,
                        'sales_channels' => true,
                        'marketplace_fees' => true,
                        'multiple_users' => true,
                        'advanced_reports' => false,
                        'dashboard_basic' => true,
                        'dashboard_advanced' => false,
                        'cost_history' => false,
                        'pdf_export' => false,
                        'multiple_channels' => true,
                    ],
                    'highlights' => [
                        'canais de venda',
                        'taxas marketplace',
                        'precificacao por canal',
                        'custos extras avancados',
                        'dashboard basico',
                    ],
                ],
            ],
            [
                'code' => BillingPlan::CODE_BUSINESS,
                'name' => 'Plano Negocio',
                'description' => 'Para operacoes estruturadas, equipe e necessidade de maior visibilidade gerencial.',
                'price_cents' => 7990,
                'currency' => 'BRL',
                'billing_cycle' => 'monthly',
                'trial_days' => 14,
                'is_active' => true,
                'metadata' => [
                    'audience' => [
                        'docerias estruturadas',
                        'pequenas fabricas',
                        'quem tem equipe',
                    ],
                    'limits' => [
                        'ingredients' => null,
                        'products' => null,
                        'recipes' => null,
                        'users' => 10,
                    ],
                    'features' => [
                        'basic_price_calculation' => true,
                        'profit_margin' => true,
                        'extra_costs' => true,
                        'recipes' => true,
                        'sales_channels' => true,
                        'marketplace_fees' => true,
                        'multiple_users' => true,
                        'advanced_reports' => true,
                        'dashboard_basic' => true,
                        'dashboard_advanced' => true,
                        'cost_history' => true,
                        'pdf_export' => true,
                        'multiple_channels' => true,
                    ],
                    'highlights' => [
                        'relatorios completos',
                        'historico de custos',
                        'dashboard avancado',
                        'exportacao PDF',
                        'multiplos canais',
                    ],
                ],
            ],
        ];

        foreach ($plans as $plan) {
            BillingPlan::query()->updateOrCreate(
                ['code' => $plan['code']],
                $plan,
            );
        }
    }
}
