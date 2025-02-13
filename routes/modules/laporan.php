<?php

use App\Http\Middleware\EnsureAppKey;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LaporanController as ApiController;
use App\Http\Controllers\LaporanController as WebController;
use App\Http\Middleware\ViewShare;

# API
Route::prefix('api/v1')->as('api.')->middleware(['api', EnsureAppKey::class, 'auth:api'])->group(function () {
    Route::apiResource('laporans', ApiController::class);
});

# WEB
Route::middleware(['web', ViewShare::class, 'auth'])->group(function () {
    Route::get('laporans/print', [WebController::class, 'exportPrint'])->name('laporans.print');
    Route::get('laporans/pdf', [WebController::class, 'pdf'])->name('laporans.pdf');
    Route::get('laporans/csv', [WebController::class, 'csv'])->name('laporans.csv');
    Route::get('laporans/json', [WebController::class, 'json'])->name('laporans.json');
    Route::get('laporans/excel', [WebController::class, 'excel'])->name('laporans.excel');
    Route::get('laporans/import-excel-example', [WebController::class, 'importExcelExample'])->name('laporans.import-excel-example');
    Route::post('laporans/import-excel', [WebController::class, 'importExcel'])->name('laporans.import-excel');
    Route::resource('laporans', WebController::class);
});
