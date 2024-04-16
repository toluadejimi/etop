<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BankController;
use App\Http\Controllers\Admin\StoredataController;
use App\Http\Controllers\Admin\DashboardController;

use App\Http\Controllers\TerminalController;
use App\Http\Controllers\PosTrasnactionController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



//other database
Route::post('store-user', [StoredataController::class, 'store_user']);
Route::post('store-terminal', [StoredataController::class, 'store_terminal']);
Route::post('update-terminal', [StoredataController::class, 'update_terminal']);
Route::post('update-user', [StoredataController::class, 'update_user']);
Route::post('store-transaction', [StoredataController::class, 'store_transaction']);
Route::post('create-bank', [StoredataController::class, 'create_bank']);
Route::post('update-bank', [StoredataController::class, 'update_bank']);
Route::post('delete_bank', [StoredataController::class, 'delete_bank']);




Route::group(['prefix' => 'admin'], function () {
    Route::post('login', [AdminController::class, 'admin_login']);

    Route::group(['middleware' => ['auth:api', 'acess']], function () {


        //Banks
        Route::post('create-bank', [BankController::class, 'create_bank']);
        Route::post('update-bank', [BankController::class, 'update_bank']);
        Route::post('delete-bank', [BankController::class, 'delete_bank']);


        //Dashboard
        Route::get('dashboard', [DashboardController::class, 'dashboard_data']);









        Route::post('create-user', [AdminController::class, 'create_user']);
        Route::post('get-all-transactions', [AdminController::class, 'get_all_transactions']);
        Route::post('transaction-filter', [AdminController::class, 'get_all_transaction_by_filter']);
        Route::post('create-terminal', [AdminController::class, 'create_terminal']);
        Route::post('update-terminal', [AdminController::class, 'update_terminal']);
        Route::post('update-user', [AdminController::class, 'update_user']);
        Route::get('list-users', [AdminController::class, 'get_users']);


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

