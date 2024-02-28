<?php

use App\Http\Controllers\TerminalController;
use App\Http\Controllers\PosTrasnactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'v1'], function () {

    Route::get('get-details', [TerminalController::class, 'get_terminal_details']);
    Route::post('initiate-transaction', [PosTrasnactionController::class, 'EtopPosLogs']);
    Route::any('get-logged-data', [PosTrasnactionController::class, 'get_logged_data']);
    Route::post('reset-pin', [TerminalController::class, 'reset_pin']);
    Route::post('verify-pin', [TerminalController::class, 'verify_pin']);
    Route::any('get-all-logged-data', [PosTrasnactionController::class, 'get_all_by_serial_logged_data']);
    Route::any('get-all-logged-data/get-all-transactions', [PosTrasnactionController::class, 'get_all_transaction_by_filter']);
    Route::any('complete-transaction', [PosTrasnactionController::class, 'EtopPos']);


});

