<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactControllers\ContactController;
use App\Http\Controllers\ContactControllers\GoogleContactController;
use App\Http\Controllers\OpenAI\SupportBotController;
use App\Http\Controllers\OpenAI\ResumeOptimizerController;
use App\Http\Controllers\OpenAI\CodeReviewController;
use App\Http\Controllers\OpenAI\EmailWriterController;
use App\Http\Controllers\OpenAI\SeoBlogWriterController;
use App\Http\Controllers\OpenAI\ProductDescriberController;
use App\Http\Controllers\OpenAI\LanguageCoachController;
use App\Http\Controllers\OpenAI\SummarizerController;

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
Route::middleware('guest')->group(function () {
    Route::post('login', [\App\Http\Controllers\Auth\AuthController::class, 'login']);
});

Route::apiResource('suppliers', \App\Http\Controllers\SupplierController::class);
Route::apiResource('warehouses', \App\Http\Controllers\WarehouseController::class);
Route::apiResource('products', \App\Http\Controllers\ProductController::class);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', function (Request $request) {
        return $request->user();
    });

    /*
     |--------------------------------------------------------------------------
     | Google Contact Routes
     |--------------------------------------------------------------------------
     */
    Route::prefix('google-contacts')->group(function () {
        Route::get('/all', [GoogleContactController::class, 'list']);
        Route::get('/search/{email}', [GoogleContactController::class, 'search']);
        Route::post('/contacts/update', [GoogleContactController::class, 'updateContactByEmail']);
    });

    /*
   |--------------------------------------------------------------------------
   | Contacts Routes
   |--------------------------------------------------------------------------
   */
    Route::prefix('contacts')->group(function () {
        Route::get('', [ContactController::class, 'list']);
        Route::post('', [ContactController::class, 'create']);
        Route::put('{id}', [ContactController::class, 'update']);
        Route::delete('{id}', [ContactController::class, 'delete']);

        Route::get('name/{id}', [ContactController::class, 'getName']);
    });

    Route::prefix('openai')->group(function () {
        Route::post('support/ask', [SupportBotController::class, 'ask']);
        Route::post('resume/optimize', [ResumeOptimizerController::class, 'optimize']);
        Route::post('code/review', [CodeReviewController::class, 'review']);
        Route::post('email/generate', [EmailWriterController::class, 'generate']);
        Route::post('blog/generate', [SeoBlogWriterController::class, 'generate']);
        Route::post('product/describe', [ProductDescriberController::class, 'generate']);
        Route::post('language/practice', [LanguageCoachController::class, 'practice']);
        Route::post('summarize', [SummarizerController::class, 'summarize']);
    });
});
