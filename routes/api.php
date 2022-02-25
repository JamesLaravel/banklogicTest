<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [UserController::class, 'createUser']);
Route::post('login', [UserController::class, 'login']);

Route::middleware('auth:employee-users', 'scope:employee-users')->group(function(){
    Route::post('account/opening', [AccountController::class, 'openAccount']);
    Route::post('account/transfer', [AccountController::class, 'makeTransfer']);
    Route::get('account/balance/{account_no}', [AccountController::class, 'getBalance']);
    Route::get('account/transaction/history/{account_no}', [AccountController::class, 'transactions']);
});
