<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\StoredataController;
use App\Http\Controllers\TerminalController;
use App\Http\Controllers\PosTrasnactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('store-user', [StoredataController::class, 'store_user']);
Route::post('store-terminal', [StoredataController::class, 'store_terminal']);
Route::post('update-terminal', [StoredataController::class, 'update_terminal']);




Route::group(['prefix' => 'admin'], function () {
    Route::post('login', [AdminController::class, 'admin_login']);
    Route::post('create-user', [AdminController::class, 'create_user']);

    Route::group(['middleware' => ['auth:api', 'acess']], function () {

        Route::post('create-user', [AdminController::class, 'create_user']);
        Route::post('get-all-transactions', [AdminController::class, 'get_all_transactions']);
        Route::post('transaction-filter', [AdminController::class, 'get_all_transaction_by_filter']);
        Route::post('create-terminal', [AdminController::class, 'create_terminal']);
        Route::post('update-terminal', [AdminController::class, 'update_terminal']);






    });



    });




Route::group(['prefix' => 'v1'], function () {

    Route::get('get-details', [TerminalController::class, 'get_terminal_details']);
    Route::post('initiate-transaction', [PosTrasnactionController::class, 'EtopPosLogs']);
    Route::any('get-logged-data', [PosTrasnactionController::class, 'get_logged_data']);
    Route::post('reset-pin', [TerminalController::class, 'reset_pin']);
    Route::post('verify-pin', [TerminalController::class, 'verify_pin']);
    Route::any('get-all-logged-data', [PosTrasnactionController::class, 'get_all_by_serial_logged_data']);
    Route::any('get-all-transactions', [PosTrasnactionController::class, 'get_all_transaction']);


    Route::any('get-all-logged-data/get-all-transactions', [PosTrasnactionController::class, 'get_all_transaction_by_filter']);
    Route::any('complete-transaction', [PosTrasnactionController::class, 'EtopPos']);

    Route::any('create-terminal', [TerminalController::class, 'create_terminal']);

    Route::any('all-terminals', [TerminalController::class, 'view_all_terminal']);

    Route::any('delete-terminal', [TerminalController::class, 'delete_terminal']);




});

