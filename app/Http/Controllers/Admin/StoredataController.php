<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

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


    }



}
