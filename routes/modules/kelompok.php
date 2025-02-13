<?php

use App\Http\Middleware\EnsureAppKey;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KelompokController as ApiController;
use App\Http\Controllers\KelompokController as WebController;
use App\Http\Middleware\ViewShare;

# API
Route::prefix('api/v1')->as('api.')->middleware(['api', EnsureAppKey::class, 'auth:api'])->group(function () {
    Route::apiResource('kelompoks', ApiController::class);
});

# WEB
Route::middleware(['web', ViewShare::class, 'auth'])->group(function () {
    Route::get('kelompoks/print', [WebController::class, 'exportPrint'])->name('kelompoks.print');
    Route::get('kelompoks/pdf', [WebController::class, 'pdf'])->name('kelompoks.pdf');
    Route::get('kelompoks/csv', [WebController::class, 'csv'])->name('kelompoks.csv');
    Route::get('kelompoks/json', [WebController::class, 'json'])->name('kelompoks.json');
    Route::get('kelompoks/excel', [WebController::class, 'excel'])->name('kelompoks.excel');
    Route::get('kelompoks/import-excel-example', [WebController::class, 'importExcelExample'])->name('kelompoks.import-excel-example');
    Route::post('kelompoks/import-excel', [WebController::class, 'importExcel'])->name('kelompoks.import-excel');
    Route::resource('kelompoks', WebController::class);
});
