<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $response = [
            'status' => 200,
            'message' => 'success',
            'data' => User::all()
        ];

        for ($i = 0; $i <= count($response['data']) - 1; $i++) {
            $response['data'][$i]['api_token']  = rtrim($response['data'][$i]['api_token']);
            $response['data'][$i]['email']  = rtrim($response['data'][$i]['email']);
        }

        return response()->json($response, 200);
    }

    public function insert(Request $request)
    {
        $validate = $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:user',
            'password' => 'required|min:8',
        ]);

        $token = 'WEEBDEV|' . Str::random(64);

        if (User::insert([
            'name' => $validate['name'],
            'email' => $validate['email'],
            'password' => Crypt::encrypt($validate['password']),
            'api_token' => 'WEEBDEV|' . $token
        ])) {
            $response = [
                'status' => 201,
                'message' => 'success',
                'data' => [
                    'name' => $validate['name'],
                    'email' => $validate['email'],
                    'api_token' => $token
                ]
            ];

            return response()->json($response, 201);
        } else {
            $response = [
                'status' => 400,
                'message' => 'failed',
                'data' => [
                    'name' => $validate['name'],
                    'email' => $validate['email'],
                    'api_token' => $token
                ]
            ];

            return response()->json($response, 400);
        }
    }

    public function show($id)
    {
        $data = $this->checkID($id, User::class);
        if ($data[1] != 0) {
            return response()->json($data[0], $data[1]);
        }

        $response = [
            'status' => 200,
            'message' => 'success',
            'data' => User::find($id)
        ];

        $response['data']['api_token']  = rtrim($response['data']['api_token']);
        $response['data']['email']  = rtrim($response['data']['email']);

        return response()->json($response, 200);
    }

    public function update(Request $request, $id)
    {
        $data = $this->checkID($id, User::class);
        if ($data[1] != 0) {
            return response()->json($data[0], $data[1]);
        }

        $validate = $this->validate($request, [
            'name' => 'required',
            'password' => 'required|min:8',
        ]);

        if (User::where('id', $id)
            ->update([
                'name' => $validate['name'],
                'password' => Crypt::encrypt($validate['password'])
            ])
        ) {
            $response = [
                'status' => 202,
                'message' => 'success',
                'data' => [
                    'name' => $validate['name']
                ]
            ];

            return response()->json($response, 202);
        } else {
            $response = [
                'status' => 400,
                'message' => 'failed',
                'data' => null
            ];

            return response()->json($response, 400);
        }
    }

    public function delete($id)
    {
        $data = $this->checkID($id, User::class);
        if ($data[1] != 0) {
            return response()->json($data[0], $data[1]);
        }

        if (User::where('id', $id)->delete()) {
            $response = [
                'status' => 200,
                'message' => 'success',
                'data' => null
            ];

            return response()->json($response, 200);
        } else {
            $response = [
                'status' => 400,
                'message' => 'failed',
            ];

            return response()->json($response, 400);
        }
    }

    public function authorization()
    {
        $response = [
            'status' => 200,
            'message' => "auth ok!",
            'data' => null
        ];

        return response($response, 200);
    }
}
