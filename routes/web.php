<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExtraCostController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\PackagingController;
use App\Http\Controllers\ProductChannelPriceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductPackagingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicCalculatorController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\RecipeItemController;
use App\Http\Controllers\SalesChannelController;
use App\Http\Controllers\SalesChannelFeeController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/calculadora', [PublicCalculatorController::class, 'show'])->name('calculator.public');
Route::get('/calculadora/simular', [PublicCalculatorController::class, 'simulate'])->name('calculator.simulate');
Route::redirect('/calculadora-preco-venda', '/calculadora', 301);

Route::view('/termos-de-uso', 'legal.terms')->name('terms');
Route::view('/uso-de-dados', 'legal.data-usage')->name('data-usage');

Route::get('/dashboard', [DashboardController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/dashboard/quick-price', [DashboardController::class, 'calculate'])->name('dashboard.quick-price');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/ingredients', [IngredientController::class, 'index'])->name('ingredients.index');
    Route::get('/ingredients/create', [IngredientController::class, 'create'])->name('ingredients.create');
    Route::get('/ingredients/search', [IngredientController::class, 'search'])->name('ingredients.search');
    Route::post('/ingredients', [IngredientController::class, 'store'])->name('ingredients.store');
    Route::get('/ingredients/{ingredient}/edit', [IngredientController::class, 'edit'])->name('ingredients.edit');
    Route::put('/ingredients/{ingredient}', [IngredientController::class, 'update'])->name('ingredients.update');
    Route::delete('/ingredients/{ingredient}', [IngredientController::class, 'destroy'])->name('ingredients.destroy');

    Route::get('/packagings', [PackagingController::class, 'index'])->name('packagings.index');
    Route::get('/packagings/create', [PackagingController::class, 'create'])->name('packagings.create');
    Route::post('/packagings', [PackagingController::class, 'store'])->name('packagings.store');
    Route::get('/packagings/{packaging}/edit', [PackagingController::class, 'edit'])->name('packagings.edit');
    Route::put('/packagings/{packaging}', [PackagingController::class, 'update'])->name('packagings.update');
    Route::delete('/packagings/{packaging}', [PackagingController::class, 'destroy'])->name('packagings.destroy');

    Route::get('/sales-channels', [SalesChannelController::class, 'index'])->name('sales-channels.index');
    Route::get('/sales-channels/create', [SalesChannelController::class, 'create'])->name('sales-channels.create');
    Route::post('/sales-channels', [SalesChannelController::class, 'store'])->name('sales-channels.store');
    Route::get('/sales-channels/{salesChannel}/edit', [SalesChannelController::class, 'edit'])->name('sales-channels.edit');
    Route::put('/sales-channels/{salesChannel}', [SalesChannelController::class, 'update'])->name('sales-channels.update');
    Route::delete('/sales-channels/{salesChannel}', [SalesChannelController::class, 'destroy'])->name('sales-channels.destroy');

    Route::post('/sales-channel-fees', [SalesChannelFeeController::class, 'store'])->name('sales-channel-fees.store');
    Route::put('/sales-channel-fees/{salesChannelFee}', [SalesChannelFeeController::class, 'update'])->name('sales-channel-fees.update');
    Route::delete('/sales-channel-fees/{salesChannelFee}', [SalesChannelFeeController::class, 'destroy'])->name('sales-channel-fees.destroy');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    Route::post('/product-channel-prices', [ProductChannelPriceController::class, 'store'])->name('product-channel-prices.store');
    Route::put('/product-channel-prices/{productChannelPrice}', [ProductChannelPriceController::class, 'update'])->name('product-channel-prices.update');
    Route::delete('/product-channel-prices/{productChannelPrice}', [ProductChannelPriceController::class, 'destroy'])->name('product-channel-prices.destroy');

    Route::post('/product-packagings', [ProductPackagingController::class, 'store'])->name('product-packagings.store');
    Route::put('/product-packagings/{productPackaging}', [ProductPackagingController::class, 'update'])->name('product-packagings.update');
    Route::delete('/product-packagings/{productPackaging}', [ProductPackagingController::class, 'destroy'])->name('product-packagings.destroy');

    Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.index');
    Route::get('/recipes/create', [RecipeController::class, 'create'])->name('recipes.create');
    Route::post('/recipes', [RecipeController::class, 'store'])->name('recipes.store');
    Route::get('/recipes/{recipe}', [RecipeController::class, 'show'])->name('recipes.show');
    Route::get('/recipes/{recipe}/edit', [RecipeController::class, 'edit'])->name('recipes.edit');
    Route::put('/recipes/{recipe}', [RecipeController::class, 'update'])->name('recipes.update');
    Route::delete('/recipes/{recipe}', [RecipeController::class, 'destroy'])->name('recipes.destroy');
    Route::post('/recipes/{recipe}/recalculate', [RecipeController::class, 'recalculate'])->name('recipes.recalculate');

    Route::post('/recipe-items', [RecipeItemController::class, 'store'])->name('recipe-items.store');
    Route::put('/recipe-items/{recipeItem}', [RecipeItemController::class, 'update'])->name('recipe-items.update');
    Route::delete('/recipe-items/{recipeItem}', [RecipeItemController::class, 'destroy'])->name('recipe-items.destroy');

    Route::post('/extra-costs', [ExtraCostController::class, 'store'])->name('extra-costs.store');
    Route::put('/extra-costs/{extraCost}', [ExtraCostController::class, 'update'])->name('extra-costs.update');
    Route::delete('/extra-costs/{extraCost}', [ExtraCostController::class, 'destroy'])->name('extra-costs.destroy');

    Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';
