<?php

use App\Http\Controllers\CustomerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('invoices')->group(function () {
    Route::get('/', [InvoiceController::class, 'index']);
    Route::get('/search', [InvoiceController::class, 'search']);
    Route::get('/create', [InvoiceController::class, 'create']);
    Route::post('/store', [InvoiceController::class, 'store']);
    Route::get('/show/{id}', [InvoiceController::class, 'show']);
    Route::get('/edit/{id}', [InvoiceController::class, 'edit']);
    Route::post('/update/{id}', [InvoiceController::class, 'update']);
    Route::get('/delete/{id}', [InvoiceController::class, 'destroy']);
    Route::get('/delete_items/{id}', [InvoiceController::class, 'destroy_items']);
});

Route::prefix('customers')->group(function () {
    Route::get('/', [CustomerController::class, 'index']);
});

Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
});
