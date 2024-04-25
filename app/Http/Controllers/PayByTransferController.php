<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PayByTransferController extends Controller
{

    public function register_merchant(request $request)
    {

        $token = uptoken();


        dd($token);

    }

    public function webhook_notification(request $request){

        $id =  $request->id;
        $createdDate = $request->createdDate;
        $updatedDate = $request->updatedDate;
        $status = $request->status;
        $amount = $request->amount;
        $merchantName = $request->merchantName;
        $senderName = $request->senderName;
        $clientId = $request->clientId;
        $terminalId = $request->terminalId;
        $accountNo = $request->accountNo;
        $bankCode = $request->bankCode;
        $bankName = $request->bankName;
        $bankCode = $request->bankCode;
        $transactionRef = $request->transactionRef;
        $reference = $request->reference;
        $accountType = $request->accountType;
        $notifiede = $request->notified;
        $gatewayAmount = $request->gatewayAmount;
        $transactionCreditLogs_id = $request->transactionCreditLogs['id'];
        $transactionCreditLogs_createdDate = $request->transactionCreditLogs['createdDate'];
        $transactionCreditLogs_updatedDate = $request->transactionCreditLogs['updatedDate'];
        $transactionCreditLogs_transactions = $request->transactionCreditLogs['transactions'];
        $transactionCreditLogs_transactionStatus = $request->transactionCreditLogs['transactionStatus'];
        $transactionCreditLogs_amount = $request->transactionCreditLogs['amount'];
        $transactionCreditLogs_gatewayAmount = $request->transactionCreditLogs['gatewayAmount'];
        $transactionCreditLogs_status = $request->transactionCreditLogs['status'];
        $transactionCreditLogs_transactionRef = $request->transactionCreditLogs['transactionRef'];
        $transactionCreditLogs_rrn = $request->transactionCreditLogs['rrn'];




        $parametersJson = json_encode($request->all());
        $headers = json_encode($request->headers->all());
        $ip = $request->ip();
        $result = " Header========> " . $headers . "\n\n Body========> " . $parametersJson . "\n\n Message========> " . $message . "\n\nIP========> " . $ip;
        send_notification($result);


    }


}
