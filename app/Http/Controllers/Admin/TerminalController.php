<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Terminal;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TerminalController extends Controller
{
    public function create_terminal(request $request)
    {


        if ($request->user_id == null || $request->serialNumber == null) {

            return response()->json([
                'status' => false,
                'message' => "User ID or Serial Number can not be null"
            ], 422);

        }


        if (Auth::user()->role == 1 || Auth::user()->role == 2) {


            $ter = Terminal::where('serialNumber', $request->serialNumber)->first() ?? null;
            if ($ter == null) {

                $term = new Terminal();
                $term->tid = $request->tid;
                $term->user_id = $request->user_id;
                $term->ip = $request->ip;
                $term->port = $request->port;
                $term->ssl = $request->ssl;
                $term->compKey1 = $request->compKey1;
                $term->compKey2 = $request->compKey2;
                $term->baseUrl = $request->baseurl;
                $term->logoUrl = $request->logoUrl;
                $term->serialNumber = $request->serialNumber;
                $term->merchantName = $request->merchantName;
                $term->mid = $request->mid;
                $term->merchantaddress = $request->merchantaddress;
                $term->pin = Hash::make($request->pin);
                $term->save();


                try {

                    $curl = curl_init();
                    $data = array(

                        'tid' => $request->tid,
                        'user_id' => $request->user_id,
                        'ip' => $request->ip,
                        'port' => $request->port,
                        'ssl' => $request->ssl,
                        'compKey1' => $request->compKey1,
                        'compKey2' => $request->compKey2,
                        'baseUrl' => $request->baseurl,
                        'logoUrl' => $request->logoUrl,
                        'serialNumber' => $request->serialNumber,
                        'merchantName' => $request->merchantName,
                        'mid' => $request->mid,
                        'merchantaddress' => $request->merchantaddress,
                        'pin' => Hash::make($request->pin),

                    );
                    $post_data = json_encode($data);

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://etopmerchant.com/api/store-terminal',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => $post_data,
                        CURLOPT_HTTPHEADER => array(
                            'Content-Type: application/json'
                        ),
                    ));

                    $var = curl_exec($curl);
                    curl_close($curl);


                } catch (QueryException $e) {
                    echo "$e";
                }


                return response()->json([
                    'status' => true,
                    'message' => "Terminal has been created successfully"
                ], 200);

            }


            if ($ter != null) {

                return response()->json([
                    'status' => false,
                    'message' => "Terminal Account Already  Exist"
                ], 422);


            }

        } else {

            return response()->json([
                'status' => false,
                'message' => "You dont have permission to create a terminal"
            ], 422);
        }
    }

    public function update_terminal(request $request)
    {


        if ($request->user_id == null || $request->serialNumber == null) {

            return response()->json([
                'status' => false,
                'message' => "User ID or Serial Number can not be null"
            ], 422);

        }

        if (Auth::user()->role == 1 || Auth::user()->role == 2) {

            $ter = Terminal::where('serialNumber', $request->serialNumber)->first() ?? null;
            if ($ter != null) {

                Terminal::where('serialNumber', $request->serialNumber)->update(['user_id' => $request->user_id]);

                try {

                    $curl = curl_init();
                    $data = array(
                        'user_id' => $request->user_id,
                        'serialNumber' => $request->serialNumber,
                    );
                    $post_data = json_encode($data);

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://etopmerchant.com/api/update-terminal',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => $post_data,
                        CURLOPT_HTTPHEADER => array(
                            'Content-Type: application/json'
                        ),
                    ));

                    $var = curl_exec($curl);
                    curl_close($curl);


                } catch (QueryException $e) {
                    echo "$e";
                }

                return response()->json([
                    'status' => true,
                    'message' => "User has been successfully attached"
                ], 200);

            } else {

                return response()->json([
                    'status' => false,
                    'message' => "Terminal Not Found"
                ], 422);

            }


        } else {

            return response()->json([
                'status' => false,
                'message' => "You dont have permission to update a terminal"
            ], 422);
        }
    }


    public
    function view_all_terminal(request $request)
    {


        $ter = Terminal::all() ?? null;
        if ($ter == null) {
            $message = "No Terminal Found";
            return error_response($message);

        }


        return response()->json([
            'status' => true,
            'terminal' => null,
            'terminals' => $ter,
            'error' => null
        ], 200);


    }





}