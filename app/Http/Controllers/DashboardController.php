<?php

namespace App\Http\Controllers;

use App\Models\ExtraCost;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\Recipe;
use App\Repositories\SalesChannelRepository;
use App\Services\QuickPriceCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use InvalidArgumentException;

class DashboardController extends Controller
{
    public function __construct(
        protected SalesChannelRepository $salesChannelRepository,
        protected QuickPriceCalculatorService $quickPriceCalculatorService,
    ) {}

    public function show(Request $request): View
    {
        return $this->buildDashboardView($request);
    }

    public function calculate(Request $request): View
    {
        $validator = Validator::make($request->all(), [
            'product_name' => ['nullable', 'string', 'max:255'],
            'recipe_total_cost' => ['required', 'numeric', 'min:0'],
            'yield_quantity' => ['required', 'numeric', 'gt:0'],
            'packaging_cost' => ['nullable', 'numeric', 'min:0'],
            'other_costs' => ['nullable', 'numeric', 'min:0'],
            'profit_margin_percentage' => ['required', 'numeric', 'min:0'],
            'sales_channel_id' => ['nullable', 'integer'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->buildDashboardView($request, $validator->validated());
    }

    protected function buildDashboardView(Request $request, array $calculatorInput = []): View
    {
        $companyId = (int) $request->user()->company_id;
        $company = $request->user()->company()->with('setting')->firstOrFail();
        $salesChannels = $this->salesChannelRepository->getActiveByCompany($companyId);

        $calculatorResult = null;
        $calculatorError = null;

        if ($calculatorInput !== []) {
            $salesChannel = null;

            if (! empty($calculatorInput['sales_channel_id'])) {
                $salesChannel = $this->salesChannelRepository->findById(
                    (int) $calculatorInput['sales_channel_id'],
                    $companyId,
                );
            }

            try {
                $calculatorResult = $this->quickPriceCalculatorService->calculate(
                    $company,
                    $calculatorInput,
                    $salesChannel,
                );
            } catch (InvalidArgumentException $exception) {
                $calculatorError = $exception->getMessage();
            }
        }

        $metrics = [
            ['label' => 'Ingredientes ativos', 'value' => Ingredient::where('company_id', $companyId)->where('is_active', true)->count(), 'caption' => 'Base da sua ficha tecnica e custo de producao.'],
            ['label' => 'Produtos cadastrados', 'value' => Product::where('company_id', $companyId)->count(), 'caption' => 'Itens vendidos com margem, rendimento e embalagem.'],
            ['label' => 'Receitas estruturadas', 'value' => Recipe::where('company_id', $companyId)->count(), 'caption' => 'Composicoes prontas para calculo e revisao rápida.'],
            ['label' => 'Custos extras', 'value' => ExtraCost::where('company_id', $companyId)->count(), 'caption' => 'Gas, perdas, taxas e demais impactos indiretos.'],
        ];

        $quickLinks = [
            ['title' => 'Cadastrar ingrediente', 'description' => 'Monte a base dos insumos com custo de compra e unidade padrao.', 'route' => route('ingredients.create')],
            ['title' => 'Criar produto', 'description' => 'Defina margem, rendimento e prepare o item para formacao de preço.', 'route' => route('products.create')],
            ['title' => 'Montar receita', 'description' => 'Vincule ingredientes, custos extras e embalagem para fechar o custo real.', 'route' => route('recipes.create')],
        ];

        return view('dashboard', [
            'metrics' => $metrics,
            'quickLinks' => $quickLinks,
            'salesChannels' => $salesChannels,
            'calculatorInput' => $calculatorInput,
            'calculatorResult' => $calculatorResult,
            'calculatorError' => $calculatorError,
        ]);
    }
}
