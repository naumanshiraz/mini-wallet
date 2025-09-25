<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TransactionController;

Route::middleware('auth:sanctum')->group(function () {
  Route::get('/user', function (Request $request) {
    return $request->user();
  });
  Route::get('/transactions', [TransactionController::class, 'index']);
  Route::post('/transactions', [TransactionController::class, 'store']);
});