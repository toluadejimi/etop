<?php

namespace App\Http\Controllers;

use App\Models\MeterToken;
use App\Models\PosLog;
use App\Models\Terminal;
use Illuminate\Http\Request;

class IbdcController extends Controller
{
    public function get_meter_disco(request $request)
    {

        $url = env('IBDCURL');
        $databody = array();
        $body = json_encode($databody);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url . "get_electric_disco.php?response_format=json",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/json',
            ),
        ));


        $var = curl_exec($curl);
        curl_close($curl);
        $var = json_decode($var);
        $status = $var->status ?? null;

        if ($status == "00") {

            return response()->json([
                'status' => true,
                'discos_types' => $var->bundles
            ], 200);
        }

    }


    public function validate_ibdc_meter(request $request)
    {

        $url = env('IBDCURL');
        $trx = "EIBD" . random_int(0000000000, 9999999999);
        $meterNo = $request->meter_no;
        $disco_type = $request->disco_type;

        $pub_key = env('IBDCPUBKEY');
        $priv_key = env('IBDCPRIVKEY');

        $trx = str_pad(mt_rand(0, 999999999999), 12, '0', STR_PAD_LEFT); // Generate a 12-digit reference ID

        $vendor_code = env('IBDCVENDORCODE');

        $hash = generateHashVerify($vendor_code, $meterNo, $trx, $disco_type, $pub_key, $priv_key);


        $databody = array();
        $body = json_encode($databody);
        $curl = curl_init();
        curl_setopt_array($curl, array(

            CURLOPT_URL => $url . "get_meter_info.php?vendor_code=$vendor_code&reference_id=$trx&meter=$meterNo&disco=$disco_type&response_format=json&hash=$hash",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/json',
            ),
        ));


        $var = curl_exec($curl);
        curl_close($curl);
        $var = json_decode($var);
        $status = $var->status ?? null;
        $message = $var->message ?? null;

        if ($status == "00" && $message = "OK") {
            return response()->json([
                'status' => true,
                'access_token' => $var->access_token,
                'name' => $var->customer->name,
                'address' => $var->customer->address,
                'util' => $var->customer->util,
                'minimumAmount' => $var->customer->minimumAmount,
            ], 200);

        } else {

            return response()->json([
                'status' => false,
                'message' => $var->message ?? null,
            ], 422);

        }


    }



    public function buy_token(request $request)
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
        $action = $request->meter_info['action'];
        $access_token = $request->meter_info['access_token'];
        $disco_type = $request->meter_info['disco_type'];
        $phone = $request->meter_info['phone'];
        $email = $request->meter_info['email'];
        $meterNo = $request->meter_info['meter_no'];



        if($action == "ibdc"){

            $url = env('IBDCURL');
            $pub_key = env('IBDCPUBKEY');
            $priv_key = env('IBDCPRIVKEY');
            $trx = str_pad(mt_rand(0, 999999999999), 12, '0', STR_PAD_LEFT); // Generate a 12-digit reference ID
            $vendor_code =  env('IBDCVENDORCODE');
            $hash = generateHash($vendor_code, $meterNo, $trx, $disco_type, $amount, $access_token, $pub_key, $priv_key);

            $databody = array(

            );

            $body = json_encode($databody);
            $curl = curl_init();
            curl_setopt_array($curl, array(

                CURLOPT_URL => $url."vend_power.php?vendor_code=$vendor_code&reference_id=$trx&meter=$meterNo&access_token=$access_token&disco=$disco_type&phone=$phone&email=$email&response_format=json&hash=$hash&amount=$amount",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Content-Type: application/json',
                ),
            ));

            $var = curl_exec($curl);
            curl_close($curl);
            $var = json_decode($var);
            $status = $var->status ?? null;
            $message = $var->message ?? null;

            dd($var);



            if($status == "00" && $message == "Successful" ){

                $met = new MeterToken();
                $met->eletic_company = "ibdc";
                $met->disco_type = $disco_type;
                $met->meter_no = $meterNo;
                $met->ref = $trx;
                $met->amount = $amount;
                $met->units = $var->units;
                $met->meter_token = $var->meter_token;
                $met->address = $var->address;
                $met->status = 2;
                $met->note = "successful";
                $met->SerialNo = $SerialNo;
                $met->rrn = $RRN;

                $met->save();

                $meter['wallet_balance'] = $var->wallet_balance;
                $meter['ref'] = $var->ref;
                $meter['amount'] = $var->amount;
                $meter['units'] = $var->units;
                $meter['meter_token'] = $var->meter_token;
                $meter['address'] = $var->address;
                $meter['message'] = "successful";


            }elseif($status == "00" && $message != "Successful"){



                $met = new MeterToken();
                $met->eletic_company = "ibdc";
                $met->disco_type = $disco_type;
                $met->meter_no = $meterNo;
                $met->ref = $trx;
                $met->amount = $amount;
                $met->units = $var->units ?? null;
                $met->meter_token = $var->meter_token ?? null;
                $met->address = $var->address ?? null;
                $met->status = 1;
                $met->note = $message;
                $met->SerialNo = $SerialNo;
                $met->rrn = $RRN;
                $met->save();


                $meter['wallet_balance'] = null;
                $meter['ref'] = null;
                $meter['amount'] = null;
                $meter['units'] = null;
                $meter['meter_token'] = null;
                $meter['message'] = $message;

            }

        }



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

        $user_id = Terminal::where('serialNumber', $SerialNo)->first()->user_id ?? null;
        $bank_id = Terminal::where('serialNumber', $SerialNo)->first()->bank_id ?? null;


        // Get the current time
        $current_time = time();
        $one_hour_later = $current_time + 3600; // 3600 seconds = 1 hour
        $created_at = date('Y-m-d H:i:s', $one_hour_later);


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
        $trasnaction->createdAt = $created_at;
        $trasnaction->updatedAt = $created_at;
        $trasnaction->user_id = $user_id;
        $trasnaction->bank_id = $bank_id;
        $trasnaction->save();


//        try {
//
//            $curl = curl_init();
//            $data = array(
//
//                'RRN' => $RRN,
//                'STAN' => $STAN,
//                'accountBalance' => $Balance ?? $accountBalance,
//                'acquiringInstitutionIdCode' => $acquiringInstitutionIdCode,
//                'authCode' => $authCode,
//                'cardCardSequenceNum' => $cardCardSequenceNum,
//                'cardExpireData' => $cardExpireData,
//                'forwardingInstCode' => $forwardingInstCode,
//                'merchantNo' => $merchantNo,
//                'amount' => $amount,
//                'accountType' => $accountType,
//                'tid' => $tid,
//                'merchantName' => $merchantName,
//                'pan' => $pan,
//                'pinBlock' => $pinBlock,
//                'receiptNumber' => $receiptNumber,
//                'respCode' => $respCode,
//                'responseMessage' => $responseMessage,
//                'status' => $status,
//                'successResponse' => $successResponse,
//                'systemTraceAuditNo' => $systemTraceAuditNo,
//                'terminalId' => $terminalId,
//                'transactionDate' => $transactionDate,
//                'transactionDateTime' => $transactionDateTime,
//                'transactionTime' => $transactionTime,
//                'transactionType' => $transactionType,
//                'cardName' => $cardName,
//                'SerialNo' => $SerialNo,
//                'createdAt' => $created_at,
//                'updatedAt' => $created_at,
//                'user_id' => $user_id,
//                'bank_id' => $bank_id,

//            );
//
//            $post_data = json_encode($data);
//            curl_setopt_array($curl, array(
//                CURLOPT_URL => 'https://etopmerchant.com/api/store-transaction',
//                CURLOPT_RETURNTRANSFER => true,
//                CURLOPT_ENCODING => '',
//                CURLOPT_MAXREDIRS => 10,
//                CURLOPT_TIMEOUT => 0,
//                CURLOPT_FOLLOWLOCATION => true,
//                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//                CURLOPT_CUSTOMREQUEST => 'POST',
//                CURLOPT_POSTFIELDS => $post_data,
//                CURLOPT_HTTPHEADER => array(
//                    'Content-Type: application/json'
//                ),
//            ));
//
//            $var = curl_exec($curl);
//            curl_close($curl);
//
//
//        } catch (QueryException $e) {
//            echo "$e";
//        }

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
            'meter' => $meter ?? null
        ], 200);
    }



    public function reprint_token(request $request)
    {
        $RRN = $request->RRN;

       $token =  MeterToken::where('rrn', $RRN)->first() ?? null;
       $message = "RRN not found";
       if($token == null){
           $meter['wallet_balance'] = null;
           $meter['ref'] = null;
           $meter['amount'] = null;
           $meter['units'] = null;
           $meter['meter_token'] = null;
           $meter['address'] = null;
           $meter['message'] = $message;

           return response()->json([
               'status' => false,
               'meter' => $meter ?? null
           ], 422);

       }else{

           $meter['wallet_balance'] = $token->wallet_balance;
           $meter['ref'] = $token->ref;
           $meter['amount'] = $token->amount;
           $meter['units'] = $token->units;
           $meter['meter_token'] = $token->meter_token;
           $meter['address'] = $token->address;
           $meter['message'] = "successful";

           return response()->json([
              "status" => true,
               'meter' => $meter
           ], 200);

       }

    }
    public function retry_token(request $request)
    {

        $RRN = $request->RRN;
        $token =  MeterToken::where('rrn', $RRN)->first() ?? null;
        $message = "RRN not found";
        if($token == null){
            $meter['wallet_balance'] = null;
            $meter['ref'] = null;
            $meter['amount'] = null;
            $meter['units'] = null;
            $meter['meter_token'] = null;
            $meter['address'] = null;
            $meter['message'] = $message;

            return response()->json([
                'status' => false,
                'meter' => $meter
            ], 422);

        }

        if($token == 2){
            $message = "Token already generated";
            $meter['wallet_balance'] = $token->wallet_balance;
            $meter['ref'] = $token->ref;
            $meter['amount'] = $token->amount;
            $meter['units'] = $token->units;
            $meter['meter_token'] = $token->meter_token;
            $meter['address'] = $token->address;
            $meter['message'] = $message;

            return response()->json([
                'status' => true,
                'meter' => $meter
            ], 200);

        }

        $token =  MeterToken::where('rrn', $RRN)->first() ?? null;
        $status = PosLog::where('RRN', $RRN)->first()->status ?? null;
        if($status == null){
            $message = "Transaction not successful";
            if($token == null){
                $meter['wallet_balance'] = null;
                $meter['ref'] = null;
                $meter['amount'] = null;
                $meter['units'] = null;
                $meter['meter_token'] = null;
                $meter['address'] = null;
                $meter['message'] = $message;
                return response()->json([
                    'status' => false,
                    'meter' => $meter ?? null
                ], 422);
            }

            if($status != 1 ){
                $message = "Transaction not successful";
                $meter['wallet_balance'] = null;
                $meter['ref'] = null;
                $meter['amount'] = null;
                $meter['units'] = null;
                $meter['meter_token'] = null;
                $meter['address'] = null;
                $meter['message'] = $message;
                return response()->json([
                    'status' => false,
                    'meter' => $meter ?? null
                ], 422);
            }



        }else{

            $meterNo = $token->meter_no;
            $disco_type = $token->disco_type;
            $amount = $token->amount;
            $access_token = get_token($meterNo, $disco_type);
            $url = env('IBDCURL');
            $pub_key = env('IBDCPUBKEY');
            $priv_key = env('IBDCPRIVKEY');
            $trx = str_pad(mt_rand(0, 999999999999), 12, '0', STR_PAD_LEFT); // Generate a 12-digit reference ID
            $vendor_code =  env('IBDCVENDORCODE');
            $hash = generateHash($vendor_code, $meterNo, $trx, $disco_type, $amount, $access_token, $pub_key, $priv_key);

            $databody = array(

            );

            $body = json_encode($databody);
            $curl = curl_init();
            curl_setopt_array($curl, array(

                CURLOPT_URL => $url."vend_power.php?vendor_code=$vendor_code&reference_id=$trx&meter=$meterNo&access_token=$access_token&disco=$disco_type&phone=$phone&email=$email&response_format=json&hash=$hash&amount=$amount",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Content-Type: application/json',
                ),
            ));

            $var = curl_exec($curl);
            curl_close($curl);
            $var = json_decode($var);
            $status = $var->status ?? null;
            $message = $var->message ?? null;

            if($status == "00" && $message == "Successful" ){

                MeterToken::where('rrn', $RRN)->update([
                    'units' => $var->units,
                    'meter_token' => $var->meter_token,
                    'address' => $var->address,
                    'status' => 2,
                    'note' => "successful"
                ]);

                $meter['wallet_balance'] = $var->wallet_balance;
                $meter['ref'] = $var->ref;
                $meter['amount'] = $var->amount;
                $meter['units'] = $var->units;
                $meter['meter_token'] = $var->meter_token;
                $meter['address'] = $var->address;
                $meter['message'] = "successful";

                return response()->json([
                    'status'=> true,
                    'meter' => $meter ?? null
                ], 200);


        }else{

                MeterToken::where('rrn', $RRN)->update([
                    'status' => 1,
                    'note' => $message
                ]);


                $meter['wallet_balance'] = null;
                $meter['ref'] = null;
                $meter['amount'] = null;
                $meter['units'] = null;
                $meter['meter_token'] = null;
                $meter['message'] = $message;


                return response()->json([
                    'status'=> true,
                    'meter' => $meter ?? null
                ], 422);

            }

        }







    }



    public function get_all_transaction(request $request)
    {

        date_default_timezone_set('UTC');

        if ($request->rrn != null) {

            $SerialNo = $request->header('serialnumber');
            $data = PosLog::where('RRN', $request->rrn)->get() ?? null;
            unset($data->created_at);
            unset($data->updated_at);

            $totalSuccessAmount = PosLog::where('RRN', $request->rrn)->where('respCode', "00")->sum('amount');
            $totalFailedAmount = PosLog::where('RRN', $request->rrn)->where('respCode', "2934")->sum('amount');
            $totalTransactionAmount = $totalSuccessAmount + $totalFailedAmount;

            $token = MeterToken::latest()->select(
                'ref',
                'amount',
                'units',
                'meter_token',
                'address',
            )->where('SerialNo', $SerialNo)->get() ?? null;
            unset($data->created_at);
            unset($data->updated_at);



            $mer = Terminal::where('serialNumber', $SerialNo)->first() ?? null;
            if ($data->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'transaction' => [],
                    'allTransaction' => null,
                    'totalSuccessAmount' => null,
                    'totalFailedAmount' => null,
                    'totalTransactionAmount' => null,
                    'message' => "No Record Found",
                    'mid' => $mer->mid,
                    'merchantDetails' => [
                        'merchantName' => $mer->merchantName,
                        'serialnumber' => $SerialNo,
                        'mid' => $mer->mid,
                        'tid' => $mer->tid,
                        'merchantaddress' => $mer->merchantaddress
                    ],
                    'meter' => $token,
                    'error' => null,
                ], 200);

            } else {

                return response()->json([
                    'success' => true,
                    'transaction' => [],
                    'allTransaction' => $data,
                    'totalSuccessAmount' => $totalSuccessAmount,
                    'totalFailedAmount' => $totalFailedAmount,
                    'totalTransactionAmount' => $totalTransactionAmount,
                    'message' => null,
                    'mid' => $mer->mid,
                    'merchantDetails' => [
                        'merchantName' => $mer->merchantName,
                        'serialnumber' => $SerialNo,
                        'mid' => $mer->mid,
                        'tid' => $mer->tid,
                        'merchantaddress' => $mer->merchantaddress
                    ],
                    'meter' => $token,
                    'error' => null,
                ], 200);
            }


        }

        if ($request->startofday == null && $request->endofday == null) {

            $SerialNo = $request->header('serialnumber');

            $data = PosLog::latest()
                ->where('pos_logs.SerialNo', $SerialNo)
                ->join('meter_tokens', 'meter_tokens.SerialNo', '=', 'pos_logs.SerialNo') // Join condition remains the same
                ->select(
                    'pos_logs.*',
                    'meter_tokens.ref',
                    'meter_tokens.amount',
                    'meter_tokens.units',
                    'meter_tokens.meter_token',
                    'meter_tokens.address',
                    'meter_tokens.meter_no'
                )
                ->take(50)
                ->get() ?? null;



            $totalSuccessAmount = PosLog::where('SerialNo', $SerialNo)->where('respCode', "00")->sum('amount');
            $totalFailedAmount = PosLog::where('SerialNo', $SerialNo)->where('respCode', "2934")->sum('amount');
            $totalTransactionAmount = $totalSuccessAmount + $totalFailedAmount;

            $token = MeterToken::latest()->select(
                'ref',
                'amount',
                'units',
                'meter_token',
                'address',
            )->where('SerialNo', $SerialNo)->get() ?? null;

            unset($data->created_at);
            unset($data->updated_at);


            $mer = Terminal::where('serialNumber', $SerialNo)->first() ?? null;
            if ($data->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'transaction' => [],
                    'allTransaction' => null,
                    'totalSuccessAmount' => null,
                    'totalFailedAmount' => null,
                    'totalTransactionAmount' => null,
                    'message' => "No Record Found",
                    'mid' => $mer->mid,
                    'merchantDetails' => [
                        'merchantName' => $mer->merchantName,
                        'serialnumber' => $SerialNo,
                        'mid' => $mer->mid,
                        'tid' => $mer->tid,
                        'merchantaddress' => $mer->merchantaddress
                    ],
                    'meter' => $token,
                    'error' => null,
                ], 200);

            } else {

                return response()->json([
                    'success' => true,
                    'transaction' => [],
                    'allTransaction' => $data,
                    'totalSuccessAmount' => $totalSuccessAmount,
                    'totalFailedAmount' => $totalFailedAmount,
                    'totalTransactionAmount' => $totalTransactionAmount,
                    'message' => null,
                    'mid' => $mer->mid,
                    'merchantDetails' => [
                        'merchantName' => $mer->merchantName,
                        'serialnumber' => $SerialNo,
                        'mid' => $mer->mid,
                        'tid' => $mer->tid,
                        'merchantaddress' => $mer->merchantaddress
                    ],
                    'meter' => [],
                    'error' => null,
                ], 200);
            }


        }


        if ($request->startofday != null && $request->endofday == null) {
            $SerialNo = $request->header('serialnumber');
            $data = PosLog::latest()->where('SerialNo', $SerialNo)->whereDate('createdAt', $request->startofday)->get() ?? null;
            unset($data->created_at);
            unset($data->updated_at);

            $totalSuccessAmount = PosLog::where(['SerialNo' => $SerialNo, 'respCode' => "00"])->whereDate('createdAt', $request->startofday)->sum('amount');
            $totalFailedAmount = PosLog::where(['SerialNo' => $SerialNo, 'respCode' => "007890"])->whereDate('createdAt', $request->startofday)->sum('amount');
            $totalTransactionAmount = $totalSuccessAmount + $totalFailedAmount;

            $token = MeterToken::latest()->select(
                'ref',
                'amount',
                'units',
                'meter_token',
                'address',
            )->where('SerialNo', $SerialNo)->whereDate('createdAt', $request->startofday)->get() ?? null;
            unset($data->created_at);
            unset($data->updated_at);


            $mer = Terminal::where('serialNumber', $SerialNo)->first() ?? null;
            if ($data->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'transaction' => [],
                    'allTransaction' => null,
                    'totalSuccessAmount' => null,
                    'totalFailedAmount' => null,
                    'totalTransactionAmount' => null,
                    'message' => "No Record Found",
                    'mid' => $mer->mid,
                    'merchantDetails' => [
                        'merchantName' => $mer->merchantName,
                        'serialnumber' => $SerialNo,
                        'mid' => $mer->mid,
                        'tid' => $mer->tid,
                        'merchantaddress' => $mer->merchantaddress
                    ],
                    'error' => null,
                    'meter' => $token,

                ], 200);

            } else {

                return response()->json([
                    'success' => true,
                    'transaction' => [],
                    'allTransaction' => $data,
                    'totalSuccessAmount' => $totalSuccessAmount,
                    'totalFailedAmount' => $totalFailedAmount,
                    'totalTransactionAmount' => $totalTransactionAmount,
                    'message' => null,
                    'mid' => $mer->mid,
                    'merchantDetails' => [
                        'merchantName' => $mer->merchantName,
                        'serialnumber' => $SerialNo,
                        'mid' => $mer->mid,
                        'tid' => $mer->tid,
                        'merchantaddress' => $mer->merchantaddress
                    ],
                    'meter' => $token,
                    'error' => null,
                ], 200);
            }

        }


        if ($request->startofday == null && $request->endofday != null) {
            $SerialNo = $request->header('serialnumber');
            $data = PosLog::latest()->where('SerialNo', $SerialNo)->whereDate('createdAt', $request->endofday)->get() ?? null;
            unset($data->created_at);
            unset($data->updated_at);

            $totalSuccessAmount = PosLog::where(['SerialNo' => $SerialNo, 'respCode' => "00"])->whereDate('createdAt', $request->endofday)->sum('amount');
            $totalFailedAmount = PosLog::where(['SerialNo' => $SerialNo, 'respCode' => "007890"])->whereDate('createdAt', $request->endofday)->sum('amount');
            $totalTransactionAmount = $totalSuccessAmount + $totalFailedAmount;

            $token = MeterToken::latest()->select(
                'ref',
                'amount',
                'units',
                'meter_token',
                'address',
            )->where('SerialNo', $SerialNo)->whereDate('createdAt', $request->endofday)->get() ?? null;
            unset($data->created_at);
            unset($data->updated_at);


            $mer = Terminal::where('serialNumber', $SerialNo)->first() ?? null;
            if ($data->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'transaction' => [],
                    'allTransaction' => null,
                    'totalSuccessAmount' => null,
                    'totalFailedAmount' => null,
                    'totalTransactionAmount' => null,
                    'message' => "No Record Found",
                    'mid' => $mer->mid,
                    'merchantDetails' => [
                        'merchantName' => $mer->merchantName,
                        'serialnumber' => $SerialNo,
                        'mid' => $mer->mid,
                        'tid' => $mer->tid,
                        'merchantaddress' => $mer->merchantaddress
                    ],
                    'meter' => $token,
                    'error' => null,

                ], 200);

            } else {

                return response()->json([
                    'success' => true,
                    'transaction' => [],
                    'allTransaction' => $data,
                    'totalSuccessAmount' => $totalSuccessAmount,
                    'totalFailedAmount' => $totalFailedAmount,
                    'totalTransactionAmount' => $totalTransactionAmount,
                    'message' => null,
                    'mid' => $mer->mid,
                    'merchantDetails' => [
                        'merchantName' => $mer->merchantName,
                        'serialnumber' => $SerialNo,
                        'mid' => $mer->mid,
                        'tid' => $mer->tid,
                        'merchantaddress' => $mer->merchantaddress
                    ],
                    'meter' => $token,
                    'error' => null,
                ], 200);
            }

        }


        if ($request->startofday != null && $request->endofday != null) {
            $SerialNo = $request->header('serialnumber');
            $data = PosLog::where('SerialNo', $SerialNo)->whereBetween('createdAt', [$request->startofday . ' 00:00:00', $request->endofday . ' 23:59:59'])->get() ?? null;
            unset($data->created_at);
            unset($data->updated_at);

            $totalSuccessAmount = PosLog::where(['SerialNo' => $SerialNo, 'respCode' => "00"])->whereBetween('createdAt', [$request->startofday . ' 00:00:00', $request->endofday . ' 23:59:59'])->sum('amount');
            $totalFailedAmount = PosLog::where(['SerialNo' => $SerialNo, 'respCode' => "007890"])->whereBetween('createdAt', [$request->startofday, $request->endofday])->sum('amount');
            $totalTransactionAmount = $totalSuccessAmount + $totalFailedAmount;

            $token = MeterToken::latest()->select(
                'ref',
                'amount',
                'units',
                'meter_token',
                'address',
            )->where('SerialNo', $SerialNo)->whereBetween('createdAt', [$request->startofday . ' 00:00:00', $request->endofday . ' 23:59:59'])->get() ?? null;
            unset($data->created_at);
            unset($data->updated_at);


            $mer = Terminal::where('serialNumber', $SerialNo)->first() ?? null;
            if ($data->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'transaction' => [],
                    'allTransaction' => null,
                    'totalSuccessAmount' => null,
                    'totalFailedAmount' => null,
                    'totalTransactionAmount' => null,
                    'message' => "No Record Found",
                    'mid' => $mer->mid,
                    'merchantDetails' => [
                        'merchantName' => $mer->merchantName,
                        'serialnumber' => $SerialNo,
                        'mid' => $mer->mid,
                        'tid' => $mer->tid,
                        'merchantaddress' => $mer->merchantaddress
                    ],
                    'meter' => $token,
                    'error' => null,
                ], 200);

            } else {

                return response()->json([
                    'success' => true,
                    'transaction' => [],
                    'allTransaction' => $data,
                    'totalSuccessAmount' => $totalSuccessAmount,
                    'totalFailedAmount' => $totalFailedAmount,
                    'totalTransactionAmount' => $totalTransactionAmount,
                    'message' => null,
                    'mid' => $mer->mid,
                    'merchantDetails' => [
                        'merchantName' => $mer->merchantName,
                        'serialnumber' => $SerialNo,
                        'mid' => $mer->mid,
                        'tid' => $mer->tid,
                        'merchantaddress' => $mer->merchantaddress
                    ],
                    'meter' => $token,
                    'error' => null,
                ], 200);
            }


        }

        return response()->json([
            'success' => false,
            'transaction' => [],
            'allTransaction' => null,
            'totalSuccessAmount' => null,
            'totalFailedAmount' => null,
            'totalTransactionAmount' => null,
            'message' => "Sommthing Went Wrong",
            'mid' => null,
            'merchantDetails' => [
                'merchantName' => null,
                'serialnumber' => null,
                'mid' => null,
                'tid' => null,
                'merchantaddress' => null
            ],
            'meter' => [],
            'error' => true,
        ], 200);


    }






}
