<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoanController;

Route::prefix('v1')->middleware(['apikey'])->group(function () {
    // CRUD dasar — tanpa JWT (untuk Tugas 2 individu)
    Route::get('/loans', [LoanController::class, 'index']);
    Route::get('/loans/{id}', [LoanController::class, 'show']);
    Route::post('/loans', [LoanController::class, 'store']);

    Route::post('/loans/{id}/return', [LoanController::class, 'returnBook']);
});