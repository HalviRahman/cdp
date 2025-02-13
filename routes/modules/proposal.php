<?php

use App\Http\Middleware\EnsureAppKey;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProposalController as ApiController;
use App\Http\Controllers\ProposalController as WebController;
use App\Http\Middleware\ViewShare;

# API
Route::prefix('api/v1')->as('api.')->middleware(['api', EnsureAppKey::class, 'auth:api'])->group(function () {
    Route::apiResource('proposals', ApiController::class);
});

# WEB
Route::middleware(['web', ViewShare::class, 'auth'])->group(function () {
    Route::get('proposals/print', [WebController::class, 'exportPrint'])->name('proposals.print');
    Route::get('proposals/pdf', [WebController::class, 'pdf'])->name('proposals.pdf');
    Route::get('proposals/csv', [WebController::class, 'csv'])->name('proposals.csv');
    Route::get('proposals/json', [WebController::class, 'json'])->name('proposals.json');
    Route::get('proposals/excel', [WebController::class, 'excel'])->name('proposals.excel');
    Route::get('proposals/import-excel-example', [WebController::class, 'importExcelExample'])->name('proposals.import-excel-example');
    Route::post('proposals/import-excel', [WebController::class, 'importExcel'])->name('proposals.import-excel');
    Route::resource('proposals', WebController::class);
});
