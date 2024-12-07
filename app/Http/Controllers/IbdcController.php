<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class IbdcController extends Controller
{
    public function get_meter_disco(request $request)
    {

        $url = env('IBDCURL');
        $databody = array(

        );
        $body = json_encode($databody);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url."get_electric_disco.php?response_format=json",
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

        if($status == "00"){

            return response()->json([
                'status' => true,
                'discos_types' => $var->bundles
            ],200);
        }

    }


    public function validate_ibdc_meter(request $request)
    {

        $url = env('IBDCURL');
        $trx = "EIBD".random_int(0000000000, 9999999999);
        $meterNo = $request->meter_no;
        $disco_type = $request->disco_type;
        $vendor_code =  env('IBDCVENDORCODE');

        $databody = array(

        );
        $body = json_encode($databody);
        $curl = curl_init();
        curl_setopt_array($curl, array(

            CURLOPT_URL => $url."get_meter_info.php?vendor_code=$vendor_code&reference_id=$trx&meter=$meterNo&disco=$disco_type&response_format=json&hash=GENERATED_HASH",
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

        if($status == "00"){

        }else{

            return response()->json([
                'status' => false,
                'message' => $var->message ?? null,
            ], 422);

        }


    }
}
