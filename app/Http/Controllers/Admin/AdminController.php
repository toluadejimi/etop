<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PosLog;
use App\Models\Terminal;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;

class AdminController extends Controller
{





    public function admin_login(request $request)
    {

        if(Auth::attempt([
            'email' => $request->email, 'password' => $request->password],
            $request->get('remember'))){

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


        if(Auth::user()->role != 1){
            return response()->json([
                'status' => false,
                'message' => "You dont have permission to create a user"
            ], 422);
        }

        if(Auth::user()->status == 1){
            return response()->json([
                'status' => false,
                'message' => "You dont have permission to create a user"
            ], 422);
        }




        $usr_status = User::where('email', $request->email)->first()->email ?? null;
        if($usr_status == $request->email){
                return response()->json([
                    'status' => false,
                    'message' => "User Already Exist"
                ], 422);
        }else{


            $user = new User();
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->role = $request->role;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->password = bcrypt($request->password);
            $user->hos_no= $request->hos_no;
            $user->gender =$request->gender;
            $user->address_line1 = $request->address_line1;
            $user->state = $request->state;
            $user->lga = $request->lga;
            $user->save();

            dd('000');

            DB::connection('second_db')->table('users')->insert([
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
            ]);




                return response()->json([
                    'status' => true,
                    'message' => "User Created Successfully"
                ], 200);

        }


    }


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
                ->where('user_id',Auth::id())->take($limit)->get() ?? null;

            return response()->json([
                'success' => true,
                'transaction' => $data,

            ], 200);


        }

        if ($startofday == null && $endofday == null && $rrn != null) {

            $data = PosLog::where('RRN', $rrn)->where('user_id',Auth::id())->take($limit)->get() ?? null;

            return response()->json([
                'success' => true,
                'transaction' => $data,

            ], 200);


        }




        if ($startofday != null && $endofday != null) {

            $data = PosLog::latest()->whereBetween('createdAt', [$startofday . ' 00:00:00', $endofday . ' 23:59:59'])
                ->where('user_id',Auth::id())->take($limit)->get() ?? null;

            return response()->json([
                'success' => true,
                'transaction' => $data,

            ], 200);


        }

        if ($startofday != null && $endofday == null) {

            $data = PosLog::latest()->wheredate('createdAt', $startofday)->where('user_id',Auth::id())->take($limit)->get() ?? null;

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
