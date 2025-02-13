<?php

use App\Http\Middleware\EnsureAppKey;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\JadwalController as ApiController;
use App\Http\Controllers\JadwalController as WebController;
use App\Http\Middleware\ViewShare;

# API
Route::prefix('api/v1')->as('api.')->middleware(['api', EnsureAppKey::class, 'auth:api'])->group(function () {
    Route::apiResource('jadwals', ApiController::class);
});

# WEB
Route::middleware(['web', ViewShare::class, 'auth'])->group(function () {
    Route::get('jadwals/print', [WebController::class, 'exportPrint'])->name('jadwals.print');
    Route::get('jadwals/pdf', [WebController::class, 'pdf'])->name('jadwals.pdf');
    Route::get('jadwals/csv', [WebController::class, 'csv'])->name('jadwals.csv');
    Route::get('jadwals/json', [WebController::class, 'json'])->name('jadwals.json');
    Route::get('jadwals/excel', [WebController::class, 'excel'])->name('jadwals.excel');
    Route::get('jadwals/import-excel-example', [WebController::class, 'importExcelExample'])->name('jadwals.import-excel-example');
    Route::post('jadwals/import-excel', [WebController::class, 'importExcel'])->name('jadwals.import-excel');
    Route::resource('jadwals', WebController::class);
});
