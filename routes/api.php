<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('sanctum.auth');

Route::middleware('sanctum.auth')->group(function () {
  Route::get('/user', function (Request $request) {
    return $request->user();
  });
  Route::get('/transactions', [TransactionController::class, 'index']);
  Route::post('/transactions', [TransactionController::class, 'store']);
});