<?php

use App\Http\Controllers\Api\ApiTaskController;
use App\Http\Controllers\Api\ApiUserController;
use App\Http\Controllers\Api\AuthController as ApiAuthController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('sign-in', [ApiUserController::class, 'create'])->name('user.create');

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', [ApiAuthController::class, 'login']);  // Specifica il controller con la nuova sintassi
    Route::post('logout', [ApiAuthController::class, 'logout']);
    Route::post('refresh', [ApiAuthController::class, 'refresh']);
    Route::post('me', [ApiAuthController::class, 'me']);

    //! ROUTE PER INSERIRE UNA NUOVA TASK
    //ottenere i dati per il form
});

Route::get('get-form-data', [ApiTaskController::class, 'getFormData'])->name('get-form-data');
Route::post('create-new-task', [ApiTaskController::class, 'create'])->name('create.new.task');
