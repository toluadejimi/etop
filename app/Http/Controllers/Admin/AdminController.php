<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PosLog;
use App\Models\Terminal;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{


    public function admin_login(request $request)
    {

        if (Auth::attempt([
            'email' => $request->email, 'password' => $request->password],
            $request->get('remember'))) {

            $user['user'] = Auth::user();
            $user['token'] = auth()->user()->createToken('API Token')->accessToken;

            return response()->json([
                'status' => true,
                'data' => $user

            ], 200);

        } else {

            return response()->json([
                'status' => false,
                'message' => "Email or Password is Incorrect"

            ], 422);

        }

    }


    public function create_user(request $request)
    {


        if (Auth::user()->role != 1) {
            return response()->json([
                'status' => false,
                'message' => "You dont have permission to create a user"
            ], 422);
        }

        if (Auth::user()->status == 1) {
            return response()->json([
                'status' => false,
                'message' => "You dont have permission to create a user"
            ], 422);
        }


        $usr_status = User::where('email', $request->email)->first()->email ?? null;
        if ($usr_status == $request->email) {
            return response()->json([
                'status' => false,
                'message' => "User Already Exist"
            ], 422);
        } else {


            $user = new User();
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->role = $request->role;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->password = bcrypt($request->password);
            $user->hos_no = $request->hos_no;
            $user->gender = $request->gender;
            $user->address_line1 = $request->address_line1;
            $user->state = $request->state;
            $user->lga = $request->lga;
            $user->save();


            try {

                $curl = curl_init();
                $data = array(
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'role' => $request->role,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'hos_no' => $request->hos_no,
                    'gender' => $request->gender,
                    'address_line1' => $request->address_line1,
                    'state' => $request->state,
                    'lga' => $request->lga,

                );
                $post_data = json_encode($data);

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://etopmerchant.com/api/store-user',
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
                'message' => "User Created Successfully"
            ], 200);

        }


    }


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
    function get_all_transactions(request $request)
    {
        $get_all_transaction = PosLog::latest()->where('user_id', Auth::id())->get();
        return response()->json([
            'status' => true,
            'data' => $get_all_transaction
        ], 200);

    }


    public
    function get_logged_data(request $request)
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

    public
    function get_all_by_serial_logged_data(request $request)
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


    public
    function get_all_transaction_by_filter(request $request)
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
