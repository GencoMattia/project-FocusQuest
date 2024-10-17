<?php

use App\Http\Controllers\Api\ApiMomentController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route for user registration (no authentication required)
Route::post('register', [ApiUserController::class, 'create'])->name('user.register');

// Authentication routes protected by the 'api' middleware (JWT-based authentication)
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function () {
    Route::post('login', [ApiAuthController::class, 'login'])->name('auth.login');
    Route::post('logout', [ApiAuthController::class, 'logout'])->name('auth.logout');
    Route::post('refresh', [ApiAuthController::class, 'refresh'])->name('auth.refresh');
    Route::post('me', [ApiAuthController::class, 'me'])->name('auth.me');
});

// User-related routes, protected by JWT authentication
Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'users'
], function () {
    // Read
    Route::get('/show', [ApiUserController::class, 'show'])->name('user.show');
    // Update
    Route::put('/update', [ApiUserController::class, 'update'])->name('user.update');
    // Delete
    Route::delete('/destroy', [ApiUserController::class, 'destroy'])->name('user.destroy');
});

// Task-related routes, protected by JWT authentication
Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'tasks'
], function () {
    // Create
    Route::post('/create', [ApiTaskController::class, 'store'])->name('create.new.task');

    // Read
    Route::get('/index', [ApiTaskController::class, 'getUserTask'])->name('user.task.list');
    Route::get('/form-data', [ApiTaskController::class, 'getFormData'])->name('get-task-form-data');
    Route::get('/top-priority', [ApiTaskController::class, 'getTopPriorityTask'])->name('user.priority.task');
    Route::get('/suggest-tasks', [ApiTaskController::class, 'suggestTasks'])->name('user.suggest.tasks');
    Route::get('/{id}/show', [ApiTaskController::class, 'show'])->name('show.task');

    // Update
    Route::patch('/{id}/status', [ApiTaskController::class, 'modifyTaskStatus'])->name('modify.task.status');
});

// Moment-related routes, protected by JWT authentication
Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'moments'
], function () {
    // Create
    Route::post('/tasks/{id}/create', [ApiMomentController::class, 'store'])->name('create.new.moment');

    // Read
    Route::get('/form-data', [ApiMomentController::class, 'getFormData'])->name('get-moment-form-data');
});
