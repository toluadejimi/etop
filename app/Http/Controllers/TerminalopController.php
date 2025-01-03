<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Terminal;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TerminalopController extends Controller
{


    public function view_terminal(request $request)
    {

        $data['ter'] = Terminal::where('id', $request->t_id)->first();
        $data['zones'] = Zone::where('user_id', Auth::id())->get();

        return view('terminal', $data);


    }

    public function set_geofence(Request $request)
    {


        $geo = Terminal::where('serialNumber', $request->serial_no)->update(
            [
                'geo_fence_id' => $request->zone_id
            ]

        );

        dd($request->serial_no,$request->zone_id );

        return back()->with('message', "Zone has been updated for geofence");




    }

    public function  delete_terminal(request $request)
    {

        if (Auth::user()->role == 1 || Auth::user()->role == 2) {

            $ck = Terminal::where('serialNumber', $request->serialNumber)->first();
            if($ck == null){

                return response()->json([

                    'status' => true,
                    'message' => "Terminal not found"

                ], 422);

            }

            Terminal::where('serialNumber', $request->serialNumber)->delete();

            return response()->json([

                'status' => true,
                'message' => "Terminal has been successfully deleted"

            ], 200);



        } else {

            return response()->json([
                'status' => false,
                'message' => "You dont have permission to create a terminal"
            ], 422);
        }
    }




    public
    function get_terminal_details(request $request)
    {

        $SerialNo = $request->header('serialnumber');
        $data = GetTerminalDetails($SerialNo);

        $bank_id = Terminal::where('serialNumber', $SerialNo)->first()->bank_id ?? null;
        $min_amount = Bank::where('id', $bank_id)->first()->min_amount ?? null;

        $lat = $request->latitude;
        $lng = $request->longitude;

        $deviceLat = $request->latitude;
        $deviceLng = $request->longitude;

        if ($data == null) {
            $message = "Terminal Not Found";
            return error_response($message);
        }




        if($lng == null || $lng == null ){
            $geofence = true;
        }else{

            $zone_id = Terminal::where('serialNumber', $SerialNo)->first()->geo_fence_id;
            $data2['zone'] = Zone::where('id', $zone_id)->first();
            $geofenceCoordinates = [
                [
                    'lat' => $data2['zone']->lat_1 ?? 0,
                    'lng' => $data2['zone']->lng_1 ?? 0,
                ],
                [
                    'lat' => $data2['zone']->lat_2 ?? 0,
                    'lng' => $data2['zone']->lng_2 ?? 0,
                ],
                [
                    'lat' => $data2['zone']->lat_3 ?? 0,
                    'lng' => $data2['zone']->lng_3 ?? 0,
                ],
                [
                    'lat' => $data2['zone']->lat_4 ?? 0,
                    'lng' => $data2['zone']->lng_4 ?? 0,
                ]
            ];


            $geofence = $this->isLocationInsideGeofence($deviceLat, $deviceLng, $geofenceCoordinates);


        }


        return response()->json([
            'status' => true,
            'terminal' => $data,
            'geofence' => $geofence,
            'min_amount' => $min_amount,
            'terminals' => null,
            'error' => null
        ], 200);

    }



    private function isLocationInsideGeofence($lat, $lng, $geofenceCoordinates)
    {
        $polygon = [];
        foreach ($geofenceCoordinates as $coordinate) {
            $polygon[] = [$coordinate['lat'], $coordinate['lng']];
        }
        return $this->pointInPolygon($lat, $lng, $polygon);
    }


    private function pointInPolygon($lat, $lng, $polygon)
    {


        $inside = false;
        $n = count($polygon);
        $x = $lat;
        $y = $lng;
        $p1x = $polygon[0][0];
        $p1y = $polygon[0][1];
        for ($i = 1; $i <= $n; $i++) {
            $p2x = $polygon[$i % $n][0];
            $p2y = $polygon[$i % $n][1];
            if ($y > min($p1y, $p2y)) {
                if ($y <= max($p1y, $p2y)) {
                    if ($x <= max($p1x, $p2x)) {
                        if ($p1y != $p2y) {
                            $xinters = ($y - $p1y) * ($p2x - $p1x) / ($p2y - $p1y) + $p1x;
                        }
                        if ($p1x == $p2x || $x <= $xinters) {
                            $inside = !$inside;
                        }
                    }
                }
            }
            $p1x = $p2x;
            $p1y = $p2y;
        }

        return $inside;
    }


    public  function reset_pin(request $request)
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

public function verify_pin(request $request)
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
