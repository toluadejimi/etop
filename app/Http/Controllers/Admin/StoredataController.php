<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Terminal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StoredataController extends Controller
{

    public function store_user(request $request)
    {

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

        return response([
            'status' => true,
            'message'=>'data stored'
        ], 200);


    }

    public function store_terminal(request $request)
    {
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

        return response([
            'status' => true,
            'message'=>'data stored'
        ], 200);


    }

    public function update_terminal(request $request)
    {

        Terminal::where('serialNumber', $request->serialNumber)->update(['user_id' => $request->user_id]);
        return response([
            'status' => true,
            'message'=>'data updated'
        ], 200);


    }




}
