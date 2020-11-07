<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function login(Request $request)
    {
        $validate = $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $data = User::where('email', $validate['email'])->first();
        if ($data != null) {
            $password = Crypt::decrypt($data['password']);
            if ($validate['password'] == $password) {
                $api_token = 'WEEBDEV|' . Str::random(64);
                User::where('id', $data['id'])
                    ->update([
                        'api_token' => $api_token
                    ]);

                $response = [
                    'status' => 200,
                    'message' => 'success',
                    'data' => [
                        'api_token' => $api_token,
                        'name' => $data['name'],
                        'email' => $data['email']
                    ]
                ];

                $response['data']['email']  = rtrim($response['data']['email']);
                return response()->json($response, 200);
            } else {
                $response = [
                    'status' => 400,
                    'message' => 'password not match with our record!',
                    'data' => $validate['email']
                ];
                return response()->json($response, 400);
            }
        } else {
            $response = [
                'status' => 400,
                'message' => 'email not match with our record!',
                'data' => $validate['email']
            ];
            return response()->json($response, 400);
        }
    }
}
