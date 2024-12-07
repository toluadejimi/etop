<?php


use App\Models\Terminal;


if (!function_exists('uptoken')) {

    function uptoken()
    {


        $upapikey = env('UPAPIKEY');
        $upurl = env('UPURL');
        $data = array(

            "requestType" => "inbound",
            "data" => [
                "email" => env('UPEMAIL'),
                "password" => env('UPPASS'),
            ],

        );

        $post_data = json_encode($data);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "$upurl",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $post_data,
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                "X-Api-Key: $upapikey",
                'Content-Type: application/json'
            ),
        ));

        $var = curl_exec($curl);
        curl_close($curl);
        $var = json_decode($var);
        $status = $var->status ?? null;

        $token = $var->data->accessToken;

        if($status == true){
            return $token;
        }




    }
}


if (!function_exists('GetTermainalDetails')) {

    function GetTerminalDetails($SerialNo)
    {
        $details = Terminal::where('serialNumber', $SerialNo)->first() ?? null;
        unset($details->id);
        unset($details->createdAt);
        unset($details->updatedAt);
        return $details;

    }
}


if (!function_exists('error_response')) {

    function error_response($message)
    {
        return response()->json([
            'success' => false,
            'terminal' => null,
            'terminals' => null,
            'error' => $message,
        ], 200);
    }
}


if (!function_exists('error_pin_response')) {

    function error_pin_response($message)
    {
        return response()->json([
            'success' => false,
            'error' => $message,
        ], 200);
    }
}


if (!function_exists('user_balance')) {

    function user_balance($SerialNo)
    {
        $balance = Terminal::where('serialNumber', $SerialNo)->first()->accountBalance ?? null;
        return $balance;

    }
}


if (!function_exists('send_notification')) {

    function send_notification($message)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.telegram.org/bot6140179825:AAGfAmHK6JQTLegsdpnaklnhBZ4qA1m2c64/sendMessage?chat_id=1316552414',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'chat_id' => "1316552414",
                'text' => $message,

            ),
            CURLOPT_HTTPHEADER => array(),
        ));

        $var = curl_exec($curl);
        curl_close($curl);

        $var = json_decode($var);
    }
}





function generateHash($vendor_code, $meter, $reference_id, $disco, $amount, $access_token, $pub_key, $priv_key)
{
    $combined_string = $vendor_code . "|" . $reference_id . "|" . $meter . "|" . $disco . "|" . $amount . "|" . $access_token . "|" . $pub_key;
    $computed_hash = hash_hmac("sha1", $combined_string, $priv_key);

    return $computed_hash;
}

