<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PosLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function get_all_transactions(request $request)
    {
        $get_all_transaction = PosLog::latest()->where('user_id', Auth::id())->get();
        return response()->json([
            'status' => true,
            'data' => $get_all_transaction
        ], 200);

    }

    public function get_logged_data(request $request)
    {

        $SerialNo = $request->header('serialnumber');
        $account_balance = user_balance($SerialNo);
        $RRN = $request->RRN;

        if ($SerialNo == null) {
            $message = "Serial Number can not be empty";
            return error_response($message);
        }

        $data = PosLog::select('terminalID', 'amount', 'transactionType', 'RRN', 'pan', 'cardName', 'serialNO', 'respCode', 'respCode', 'createdAt', 'updatedAt')
            ->where('RRN', $RRN)->first() ?? null;

        if ($data == null) {
            $message = 'Transaction not found';
            error_response($message);
        }


        return response()->json([
            'status' => true,
            'loggedData' => $data,
            'allLoggedData' => null,
            'error' => null
        ], 200);


    }

    public function get_all_transaction_by_filter(request $request)
    {


        $rrn = $request->rrn;
        $startofday = $request->from;
        $endofday = $request->to;
        $limit = $request->limit;
        if ($limit == null) {
            $limit = 50;
        } else {
            $limit = $request->limit;
        }
        $type = $request->type;


        if ($startofday != null && $endofday != null) {

            $data = PosLog::latest()->whereBetween('createdAt', [$startofday . ' 00:00:00', $endofday . ' 23:59:59'])
                ->where('user_id', Auth::id())->take($limit)->get() ?? null;

            return response()->json([
                'success' => true,
                'transaction' => $data,

            ], 200);


        }

        if ($startofday == null && $endofday == null && $rrn != null) {

            $data = PosLog::where('RRN', $rrn)->where('user_id', Auth::id())->take($limit)->get() ?? null;

            return response()->json([
                'success' => true,
                'transaction' => $data,

            ], 200);


        }


        if ($startofday != null && $endofday != null) {

            $data = PosLog::latest()->whereBetween('createdAt', [$startofday . ' 00:00:00', $endofday . ' 23:59:59'])
                ->where('user_id', Auth::id())->take($limit)->get() ?? null;

            return response()->json([
                'success' => true,
                'transaction' => $data,

            ], 200);


        }

        if ($startofday != null && $endofday == null) {

            $data = PosLog::latest()->wheredate('createdAt', $startofday)->where('user_id', Auth::id())->take($limit)->get() ?? null;

            return response()->json([
                'success' => true,
                'transaction' => $data,

            ], 200);


        }


        if ($rrn != null && $startofday != null && $endofday != null) {

            $data = PosLog::whereBetween('createdAt', [$startofday . ' 00:00:00', $endofday . ' 23:59:59'])
                ->where([
                    'RRN' => $rrn,
                    'amount' => $amount,
                ])->take($limit)->get() ?? null;


            if ($data->isEmpty()) {

                return response()->json([
                    'success' => true,
                    'transaction' => [],
                ], 200);


            }

            return response()->json([
                'success' => false,
                'transaction' => $data,

            ], 200);


        }


        return response()->json([
            'success' => true,
            'transaction' => [],

        ], 200);


    }



}
