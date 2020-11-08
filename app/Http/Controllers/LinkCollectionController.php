<?php

namespace App\Http\Controllers;

use App\Models\LinkCollection;
use Illuminate\Http\Request;

class LinkCollectionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    public function index()
    {
        $response = [
            'status' => 200,
            'messages' => 'success',
            'data' => LinkCollection::all()
        ];

        for ($i = 0; $i <= count($response['data']) - 1; $i++) {
            $response['data'][$i]['link']  = rtrim($response['data'][$i]['link']);
        }

        return response()->json($response, 200);
    }

    public function show($id)
    {
        $data = $this->checkID($id, LinkCollection::class);
        if ($data[1] != 0) {
            return response()->json($data[0], $data[1]);
        }

        $response = [
            'status' => 200,
            'messages' => 'success',
            'data' => LinkCollection::find($id)
        ];

        return response()->json($response, 200);
    }

    public function insert(Request $request)
    {

        $validate = $this->validate($request, [
            'link_for' => 'required|unique:link_collection,link_for',
            'link' => 'required|min:5'
        ]);


        if (LinkCollection::insert([
            'link_for' => $validate['link_for'],
            'link' => $validate['link']
        ])) {
            $response = [
                'status' => 200,
                'message' => 'success',
                'result' => $validate
            ];

            return response()->json($response, 200);
        } else {
            $response = [
                'status' => '400',
                'message' => 'failed to insert data!',
                'result' => $validate
            ];
            return response()->json($response, 400);
        }
    }

    public function update(Request $request, $id)
    {
        $data = $this->checkID($id, LinkCollection::class);
        if ($data[1] != 0) {
            return response()->json($data[0], $data[1]);
        }

        $validate = $this->validate($request, [
            'link_for' => 'required',
            'link' => 'required|min:5'
        ]);

        $update = LinkCollection::where('id', $id)
            ->update([
                'link_for' => $validate['link_for'],
                'link' => $validate['link']
            ]);

        if ($update) {
            $response = [
                'status' => 200,
                'message' => 'success',
                'result' => $validate
            ];
            return response()->json($response, 200);
        } else {
            $response = [
                'status' => '400',
                'message' => 'failed to update data!',
                'result' => $validate
            ];
            return response()->json($response, 400);
        }
    }
}
