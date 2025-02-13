<?php

use App\Http\Middleware\EnsureAppKey;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProgramStudiController as ApiController;
use App\Http\Controllers\ProgramStudiController as WebController;
use App\Http\Middleware\ViewShare;

# API
Route::prefix('api/v1')->as('api.')->middleware(['api', EnsureAppKey::class, 'auth:api'])->group(function () {
    Route::apiResource('program-studis', ApiController::class);
});

# WEB
Route::middleware(['web', ViewShare::class, 'auth'])->group(function () {
    Route::get('program-studis/print', [WebController::class, 'exportPrint'])->name('program-studis.print');
    Route::get('program-studis/pdf', [WebController::class, 'pdf'])->name('program-studis.pdf');
    Route::get('program-studis/csv', [WebController::class, 'csv'])->name('program-studis.csv');
    Route::get('program-studis/json', [WebController::class, 'json'])->name('program-studis.json');
    Route::get('program-studis/excel', [WebController::class, 'excel'])->name('program-studis.excel');
    Route::get('program-studis/import-excel-example', [WebController::class, 'importExcelExample'])->name('program-studis.import-excel-example');
    Route::post('program-studis/import-excel', [WebController::class, 'importExcel'])->name('program-studis.import-excel');
    Route::resource('program-studis', WebController::class);
});
