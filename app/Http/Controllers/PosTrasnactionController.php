<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Charge;
use App\Models\PosLog;
use Defuse\Crypto\Key;
use App\Models\Terminal;
use Defuse\Crypto\Crypto;
use App\Models\SuperAgent;
use App\Models\Transaction;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PosTrasnactionController extends Controller
{

    public function get_all_transaction(request $request)
    {
        $SerialNo = $request->header('serialnumber');
        $data = PosLog::latest()->where('SerialNo', $SerialNo)->take('20')->get() ?? null;
        //$transactionType = $request->transactionType;
        $mer = Terminal::where('serialNumber', $SerialNo)->first() ?? null;
        if ($data->isEmpty()) {
            return response()->json([
                'success' => false,
                'transaction' => [],
                'allTransaction' => null,
                'message' => null,
                'mid' => $mer->mid,
                'merchantDetails' => [
                    'merchantName' => $mer->merchantName,
                    'serialnumber' => $SerialNo,
                    'mid' => $mer->mid,
                    'tid' => $mer->tid,
                    'merchantaddress' => $mer->merchantaddress
                ],
                'error' => null,
            ], 200);

        }


        return response()->json([
            'success' => true,
            'transaction' => [],
            'allTransaction' =>  $data,
            'message' => null,
            'mid' => $mer->mid,
            'merchantDetails' => [
                'merchantName' => $mer->merchantName,
                'serialnumber' => $SerialNo,
                'mid' => $mer->mid,
                'tid' => $mer->tid,
                'merchantaddress' => $mer->merchantaddress
            ],
            'error' => null,
        ], 200);
    }


    public function EtopPosLogs(request $request)
    {

        $SerialNo = $request->header('serialnumber');
        $account_balance = user_balance($SerialNo);
        $RRN = $request->RRN;
        $STAN = $request->STAN;
        $accountBalance = $account_balance;
        $acquiringInstitutionIdCode = $request->acquiringInstitutionIdCode;
        $authCode = $request->authCode;
        $cardCardSequenceNum = $request->cardCardSequenceNum;
        $cardExpireData = $request->cardExpireData;
        $forwardingInstCode = $request->forwardingInstCode;
        $merchantNo = $request->institutionData['merchantNo'];
        $amount = $request->institutionData['amount'];
        $accountType = $request->institutionData['accountType'];
        $merchantName = $request->institutionData['merchantName'];
        $tid = $request->institutionData['tid'];
        $pan = $request->pan;
        $pinBlock = $request->pinBlock;
        $receiptNumber = $request->receiptNumber;
        $respCode = $request->respCode;
        $responseMessage = $request->responseMessage;
        $status = $request->status;
        $successResponse = $request->successResponse;
        $systemTraceAuditNo = $request->systemTraceAuditNo;
        $terminalId = $request->terminalId;
        $transactionDate = $request->transactionDate;
        $transactionDateTime = $request->transactionDateTime;
        $transactionTime = $request->transactionTime;
        $transactionType = $request->transactionType;
        $cardName = $request->cardName;
        $userID = $request->UserID;


        if ($SerialNo == null) {
            $message = "Serial Number can not be empty";
            return error_response($message);
        }


        $SerialNo = Terminal::where('serialNumber', $SerialNo)->first()->serialNumber ?? null;
        if ($SerialNo == null) {
            $message = "No user attached to the serial number | $SerialNo";
            return error_response($message);
        }

        $rrn = PosLog::where('RRN', $request->RRN)->first()->log_status ?? null;
        if ($rrn == 1) {
            return response()->json([
                'status' => true,
                'message' => 'Transaction already successful',
            ], 422);

        }

        $trasnaction = new PosLog();
        $trasnaction->RRN = $RRN;
        $trasnaction->STAN = $STAN;
        $trasnaction->accountBalance = $accountBalance;
        $trasnaction->acquiringInstitutionIdCode = $acquiringInstitutionIdCode;
        $trasnaction->authCode = $authCode;
        $trasnaction->cardCardSequenceNum = $cardCardSequenceNum;
        $trasnaction->cardExpireData = $cardExpireData;
        $trasnaction->forwardingInstCode = $forwardingInstCode;
        $trasnaction->merchantNo = $merchantNo;
        $trasnaction->amount = $amount;
        $trasnaction->accountType = $accountType;
        $trasnaction->tid = $tid;
        $trasnaction->merchantName = $merchantName;
        $trasnaction->pan = $pan;
        $trasnaction->pinBlock = $pinBlock;
        $trasnaction->receiptNumber = $receiptNumber;
        $trasnaction->respCode = $respCode;
        $trasnaction->responseMessage = $responseMessage;
        $trasnaction->status = $status;
        $trasnaction->successResponse = $successResponse;
        $trasnaction->systemTraceAuditNo = $systemTraceAuditNo;
        $trasnaction->terminalId = $terminalId;
        $trasnaction->transactionDate = $transactionDate;
        $trasnaction->transactionDateTime = $transactionDateTime;
        $trasnaction->transactionTime = $transactionTime;
        $trasnaction->transactionType = $transactionType;
        $trasnaction->cardName = $cardName;
        $trasnaction->SerialNo = $SerialNo;
        $trasnaction->save();

        $mer = Terminal::where('serialNumber', $SerialNo)->first() ?? null;


        return response()->json([
                'status' => true,
                'message' => "Transaction initiated successfully",
        ], 200);
    }

    public function EtopPos(request $request)
    {

        $SerialNo = $request->header('serialnumber');
        $account_balance = user_balance($SerialNo);
        $RRN = $request->RRN;
        $STAN = $request->STAN;
        $accountBalance = $account_balance;
        $acquiringInstitutionIdCode = $request->acquiringInstitutionIdCode;
        $authCode = $request->authCode;
        $cardCardSequenceNum = $request->cardCardSequenceNum;
        $cardExpireData = $request->cardExpireData;
        $forwardingInstCode = $request->forwardingInstCode;
        $merchantNo = $request->institutionData['merchantNo'];
        $amount = $request->institutionData['amount'];
        $accountType = $request->institutionData['accountType'];
        $merchantName = $request->institutionData['merchantName'];
        $tid = $request->institutionData['tid'];
        $pan = $request->pan;
        $pinBlock = $request->pinBlock;
        $receiptNumber = $request->receiptNumber;
        $respCode = $request->respCode;
        $responseMessage = $request->responseMessage;
        $status = $request->status;
        $successResponse = $request->successResponse;
        $systemTraceAuditNo = $request->systemTraceAuditNo;
        $terminalId = $request->terminalId;
        $transactionDate = $request->transactionDate;
        $transactionDateTime = $request->transactionDateTime;
        $transactionTime = $request->transactionTime;
        $transactionType = $request->transactionType;
        $cardName = $request->cardName;
        $userID = $request->UserID;


        if ($SerialNo == null) {
            $message = "Serial Number can not be empty";
            return error_response($message);
        }

        $rrn = PosLog::where('RRN', $request->RRN)->first()->log_status ?? null;
        if ($rrn == 1) {
            return response()->json([
                'status' => true,
                'message' => 'Transaction already successful',
            ], 422);

        }


        $SerialNo = Terminal::where('serialNumber', $SerialNo)->first()->serialNumber ?? null;
        if ($SerialNo == null) {
            $message = "No user attached to the serial number | $SerialNo";
            return error_response($message);
        }


        $trx = PosLog::where('RRN', $request->RRN)->where('log_status', 0)->update([
            'log_status' => 1,
        ]) ?? null;



        $trasnaction = new PosLog();
        $trasnaction->RRN = $RRN;
        $trasnaction->STAN = $STAN;
        $trasnaction->accountBalance = $accountBalance;
        $trasnaction->acquiringInstitutionIdCode = $acquiringInstitutionIdCode;
        $trasnaction->authCode = $authCode;
        $trasnaction->cardCardSequenceNum = $cardCardSequenceNum;
        $trasnaction->cardExpireData = $cardExpireData;
        $trasnaction->forwardingInstCode = $forwardingInstCode;
        $trasnaction->merchantNo = $merchantNo;
        $trasnaction->amount = $amount;
        $trasnaction->accountType = $accountType;
        $trasnaction->tid = $tid;
        $trasnaction->merchantName = $merchantName;
        $trasnaction->pan = $pan;
        $trasnaction->pinBlock = $pinBlock;
        $trasnaction->receiptNumber = $receiptNumber;
        $trasnaction->respCode = $respCode;
        $trasnaction->responseMessage = $responseMessage;
        $trasnaction->status = $status;
        $trasnaction->successResponse = $successResponse;
        $trasnaction->systemTraceAuditNo = $systemTraceAuditNo;
        $trasnaction->terminalId = $terminalId;
        $trasnaction->transactionDate = $transactionDate;
        $trasnaction->transactionDateTime = $transactionDateTime;
        $trasnaction->transactionTime = $transactionTime;
        $trasnaction->transactionType = $transactionType;
        $trasnaction->cardName = $cardName;
        $trasnaction->SerialNo = $SerialNo;
        $trasnaction->save();

        $mer = Terminal::where('serialNumber', $SerialNo)->first() ?? null;


        return response()->json([
            'newTransaction' => [
                'success' => true,
                'transaction' => $trasnaction,
            ],
            'merchantName' => $mer->merchantName,
            'mid' => $mer->mid,
            'allTransaction' => null,
            'message' => "Transaction initiated successfully",
            'merchantDetails' => [
                'merchantName' => $mer->merchantName,
                'serialnumber' => $mer->serialNumber,
                'mid' => $mer->mid,
                'tid' => $mer->tid,
                'merchantaddress' => $mer->merchantaddress
            ],
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

    public function get_all_by_serial_logged_data(request $request)
    {

        $SerialNo = $request->header('serialnumber');
        $account_balance = user_balance($SerialNo);


        $data = PosLog::select('Id', 'terminalID', 'amount', 'transactionType', 'RRN', 'pan', 'cardName', 'serialNO', 'responseCode',
            'respCode')->where('SerialNo', $SerialNo)->get() ?? null;

        if ($data == null) {
            $message = 'Transaction not found';
            error_response($message);
        }

        return response()->json([
            'success' => true,
            'loggedData' => null,
            'allLoggedData' => $data,
            'error' => null,
        ], 200);


    }


    public function get_all_transaction_by_filter(request $request)
    {

        $SerialNo = $request->header('serialnumber');
        $rrn = $request->rrn;
        $amount = $request->amount;
        $startofday = $request->startofday;
        $endofday = $request->endofday;
        $page = $request->page;
        $limit = $request->limit;
        if($limit == null){
            $limit = 20;
        }else{
            $limit = $request->limit;
        }


        $transactionType = $request->transactionType;
        $mer = Terminal::where('serialNumber', $SerialNo)->first()  ?? null;
        if ($rrn != null && $amount != null && $startofday != null && $endofday != null) {

            $data = PosLog::whereBetween('createdAt', [$startofday . ' 00:00:00', $endofday . ' 23:59:59'])
            ->where([
                'RRN' => $rrn,
                'amount' => $amount,
            ])->take($limit)->get() ?? null;


            if ($data->isEmpty() ) {

                return response()->json([
                    'success' => false,
                    'transaction' => [],
                    'allTransaction' => null,
                    'message' => null,
                    'mid' => $mer->mid,
                    'merchantDetails' => [
                        'merchantName' => $mer->merchantName,
                        'serialnumber' => $SerialNo,
                        'mid' => $mer->mid,
                        'tid'=> $mer->tid,
                        'merchantaddress'=>$mer->merchantaddress
                    ],
                    'error' => null,
                ], 200);



            }

            return response()->json([
                'success' => false,
                'transaction' => $data,
                'allTransaction' => null,
                'message' => null,
                'mid' => $mer->mid,
                'merchantDetails' => [
                    'merchantName' => $mer->merchantName,
                    'serialnumber' => $SerialNo,
                    'mid' => $mer->mid,
                    'tid'=> $mer->tid,
                    'merchantaddress'=>$mer->merchantaddress
                ],
                'error' => null,
            ], 200);


        }

        if ($startofday != null && $endofday != null) {


            $data = Transaction::whereBetween('createdAt', [$startofday . ' 00:00:00', $endofday . ' 23:59:59'])
                ->where('SerialNo', $SerialNo)->take(20)->get() ?? null;

            if ($data->isEmpty() ) {

                return response()->json([
                    'success' => false,
                    'transaction' => [],
                    'allTransaction' => null,
                    'message' => null,
                    'mid' => $mer->mid,
                    'merchantDetails' => [
                        'merchantName' => $mer->merchantName,
                        'serialnumber' => $SerialNo,
                        'mid' => $mer->mid,
                        'tid'=> $mer->tid,
                        'merchantaddress'=>$mer->merchantaddress
                    ],
                    'error' => null,
                ], 200);



            }

            if ($data == null) {

                return response()->json([
                    'success' => false,
                    'transaction' => [],
                    'allTransaction' => null,
                    'message' => null,
                    'mid' => $mer->mid,
                    'merchantDetails' => [
                        'merchantName' => $mer->merchantDetails,
                        'serialnumber' => $SerialNo,
                        'mid' => $mer->mid,
                        'tid'=> $mer->tid,
                        'merchantaddress'=>$mer->merchantaddress
                    ],
                    'error' => null,
                ], 200);



            }


            return response()->json([
                'success' => true,
                'transaction' => $data,
                'allTransaction' => null,
                'message' => null,
                'mid' => $mer->mid,
                'merchantDetails' => [
                    'merchantName' => $mer->merchantName,
                    'serialnumber' => $SerialNo,
                    'mid' => $mer->mid,
                    'tid'=> $mer->tid,
                    'merchantaddress'=>$mer->merchantaddress
                ],
                'error' => null,
            ], 200);

        }


    }






    public function eod_transactions(request $request)
    {


        if ($request->date == null || $request->user_id == null) {


            return response()->json([
                'status' => false,
                'message' => "Date or User_id Can not be null"

            ], 500);
        }


        $today = $request->date;
        $transaction = Transaction::select('e_ref', 'amount', 'sender_name', 'created_at', 'status')->where('user_id', $request->user_id)->whereDate('created_at', $today)->get();
        $terminalNo = Terminal::where('user_id', $request->user_id)->first()->serial_no;
        $merchantName = Terminal::where('user_id', $request->user_id)->first()->merchantName;
        $merchantNo = Terminal::where('user_id', $request->user_id)->first()->merchantNo;
        $totalTransaction = Transaction::where('user_id', $request->user_id)->whereDate('created_at', $today)->count();
        $totalSuccess = Transaction::whereDate('created_at', $today)
            ->where([
                'user_id' => $request->user_id,
                'status' => 1
            ])->count();


        $totalFail = Transaction::whereDate('created_at', $today)
            ->where([
                'user_id' => $request->user_id,
                'status' => 4
            ])->count();

        $totalPurchaseAmount = Transaction::whereDate('created_at', $today)
            ->where([
                'user_id' => $request->user_id,
                'status' => 1
            ])->sum('amount');


        return response()->json([
            'status' => true,
            'reportDatetime' => date('Y-m-d h:i:s'),
            'terminalNo' => $terminalNo,
            'merchantName' => $merchantName,
            'merchantNo' => $merchantNo,
            'totalTransaction' => (int)$totalTransaction,
            'totalSuccess' => $totalSuccess,
            'totalFail' => $totalFail,
            'totalPurchaseAmount' => $totalPurchaseAmount,
            'transaction' => $transaction

        ], 200);
    }
}