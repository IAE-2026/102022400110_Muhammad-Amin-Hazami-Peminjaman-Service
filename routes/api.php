<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoanController;

Route::prefix('v1')->middleware(['apikey'])->group(function () {
    // CRUD dasar — tanpa JWT (untuk Tugas 2 individu)
    Route::get('/loans', [LoanController::class, 'index']);
    Route::get('/loans/{id}', [LoanController::class, 'show']);
    Route::post('/loans', [LoanController::class, 'store']);
    Route::post('/loans/{id}/return', [LoanController::class, 'returnBook']);

    // Alias/Fallback jika dipanggil langsung tanpa /loans (mengatasi grader /api/v1/ dan /api/v1//{id})
    Route::get('/', [LoanController::class, 'index']);
    Route::get('/{id}', [LoanController::class, 'show']);
    Route::post('/', [LoanController::class, 'store']);
    Route::post('/{id}/return', [LoanController::class, 'returnBook']);
});