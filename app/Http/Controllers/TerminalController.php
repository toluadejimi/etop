<?php

namespace App\Http\Controllers;

use App\Models\Terminal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TerminalController extends Controller
{







    public function create_terminal(request $request)
    {

        $SerialNo = $request->header('serialnumber');
        $ter = Terminal::where('serialNumber', $request->serialNumber)->first() ?? null;
        if ($ter == null) {

            $term = new Terminal();
            $term->tid = $request->tid;
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

            return response()->json([
                'status' => true,
                'terminal' => $term,
                'terminals' => null,
                'error' => null
            ], 200);

        }


        if ($ter != null) {
            $message = "Terminal Alreay  Exist";
            return error_response($message);
        }

    }

    public function delete_terminal(request $request)
    {

        $SerialNo = $request->header('serialnumber');

        $ter = Terminal::where('serialNumber', $SerialNo)->first() ?? null;

        if($ter == null){

            $message = "Terminal has been deleted";
            return error_response($message);

        }

        Terminal::where('serialNumber', $SerialNo)->delete();

        return response()->json([
            'status' => true,
            'terminal' =>"Terminal with Serial No -| $SerialNo | has been successfully deleted",
            'terminals' => null,
            'error' => null
        ], 200);





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


public
function get_terminal_details(request $request)
{

    $SerialNo = $request->header('serialnumber');

    $data = GetTerminalDetails($SerialNo);

    if ($data == null) {
        $message = "Terminal Not Found";
        return error_response($message);
    }

    return response()->json([
        'status' => true,
        'terminal' => $data,
        'terminals' => null,
        'error' => null
    ], 200);

}

public
function reset_pin(request $request)
{

    $newpin = $request->newPin;
    $oldpin = $request->oldPin;


    $SerialNo = $request->header('serialnumber');

    if ($SerialNo == null) {
        $message = "Serial Number can not be empty";
        return error_response($message);
    }

    $check_user = Terminal::where('serialNumber', $SerialNo)->first();
    if ($check_user == null) {
        $message = "Terminal not found";
        return error_response($message);
    }

    $get_pin = Terminal::where('serialNumber', $SerialNo)->first()->pin ?? null;
    $pin_ck = $request->newPin;


    if (Hash::check($oldpin, $get_pin)) {
        if (Hash::check($pin_ck, $get_pin)) {
            $message = "Please choose another pin";
            return error_pin_response($message);

        }

        $pin = Hash::make($newpin);
        Terminal::where('serialNumber', $SerialNo)->update(['pin' => $pin]);
        return response()->json([
            'success' => true,
            'error' => null,
        ], 200);
        return error_response($message);

    } else {
        $message = "Incorrect old pin";
        return error_pin_response($message);
    }


}

public
function verify_pin(request $request)
{

    $SerialNo = $request->header('serialnumber');

    if ($SerialNo == null) {
        $message = "Serial Number can not be empty";
        return error_response($message);
    }

    $check_user = Terminal::where('serialNumber', $SerialNo)->first();
    if ($check_user == null) {
        $message = "Terminal not found";
        return error_response($message);
    }

    $oldpin = $request->pin;
    $get_pin = Terminal::where('serialNumber', $SerialNo)->first()->pin ?? null;
    if (Hash::check($oldpin, $get_pin)) {
        return response()->json([
            'success' => true,
            'error' => null,
        ], 200);

    } else {
        $message = "Incorrect pin";
        return error_pin_response($message);
    }


}


}
