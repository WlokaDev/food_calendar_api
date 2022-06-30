<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(\App\Http\Middleware\SettingMiddleware::class)->group(function () {
    Route::get('/products/{product}/get-image', [\App\Http\Controllers\v1\ProductsController::class, 'getImage']);
    Route::get('/products', [\App\Http\Controllers\v1\ProductsController::class, 'index']);
    Route::get('/recipes/{recipe}/feedbacks', [\App\Http\Controllers\v1\RecipeFeedbacksController::class, 'index']);
    Route::get('/images/{image}', [\App\Http\Controllers\v1\ImagesController::class, 'show'])->name('images.show');

    Route::controller(\App\Http\Controllers\v1\RecipesController::class)
        ->prefix('recipes')
        ->group(function() {
            Route::get('latest', 'getLastAddedRecipe');
            Route::get('/', 'index');
        });

    Route::controller(\App\Http\Controllers\v1\Auth\AuthController::class)
        ->middleware('guest')
        ->prefix('auth')
        ->group(function() {
            Route::post('/register', 'register');
            Route::post('/email-available', 'checkEmailIsAvailable');
            Route::post('/login', 'login');
            Route::post('/forgot-password', 'forgotPassword');
            Route::post('/reset-password', 'resetPassword');
            Route::get('/verify-email', 'verifyEmail');
        });

    Route::controller(\App\Http\Controllers\v1\Auth\SocialAuthController::class)
        ->middleware('guest')
        ->prefix('auth')
        ->group(function() {
            Route::post('/auth/redirect', 'auth');
            Route::post('/auth/callback', 'callback');
        });

    Route::get('/categories', [\App\Http\Controllers\v1\CategoriesController::class, 'index']);
    Route::get('/categories/{category}/show-icon', [\App\Http\Controllers\v1\CategoriesController::class, 'showIcon'])->name('categories.show_icon');

    Route::middleware('auth:sanctum')->group(function () {
        // Products
        Route::apiResource('products', \App\Http\Controllers\v1\ProductsController::class)->except('index');

        // Recipes
        Route::apiResource('recipes', \App\Http\Controllers\v1\RecipesController::class)->except('index');
        Route::delete('/recipes/{recipe}/images/{image}', [\App\Http\Controllers\v1\RecipesController::class, 'destroyImage']);
        Route::apiResource('/recipes/{recipe}/feedbacks', \App\Http\Controllers\v1\RecipeFeedbacksController::class)->except('show', 'index');

        // Calendar
        Route::post('/recipe-calendar/draw', [\App\Http\Controllers\v1\RecipeCalendarsController::class, 'drawRecipes']);

        // Users
        Route::controller(\App\Http\Controllers\v1\UserPreferencesController::class)
            ->prefix('users')
            ->group(function() {
                Route::post('/preferences', 'store');
                Route::post('/excluded-products', 'updateListOfExcludedProducts');
                Route::get('/excluded-products', 'showListOfExcludedProducts');
                Route::get('/preferences', 'show');
            });


        Route::prefix('admin-panel')->group(function () {
            Route::get('/changes-for-accept', [\App\Http\Controllers\v1\AdminPanel\AcceptableChangesController::class, 'index']);
            Route::post('/changes-for-accept/{acceptable_change}/accept', [\App\Http\Controllers\v1\AdminPanel\AcceptableChangesController::class, 'accept']);
            Route::post('/changes-for-accept/{acceptable_change}/reject', [\App\Http\Controllers\v1\AdminPanel\AcceptableChangesController::class, 'reject']);
        });
    });
});


