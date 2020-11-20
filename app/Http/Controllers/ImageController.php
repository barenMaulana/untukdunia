<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;

class ImageController extends Controller
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
            'message' => 'success',
            'data' => Image::all()
        ];

        for ($i = 0; $i <= count($response['data']) - 1; $i++) {
            $response['data'][$i]['image']  = rtrim($response['data'][$i]['image']);
        }

        return response()->json($response, 200);
    }

    public function show($id)
    {
        $data = $this->checkID($id, Image::class);
        if ($data[1] != 0) {
            return response()->json($data[0], $data[1]);
        }

        $response = [
            'status' => 200,
            'message' => 'success',
            'data' => Image::find($id)
        ];

        $response['data']['image'] = rtrim($response['data']['image']);

        return response()->json($response, 200);
    }

    public function insert(Request $request)
    {
        $validate = $this->validate($request, [
            'image' => 'required|image|mimes:jpg,png,jpeg,svg|max:700'
        ]);
        $file_name = time();
        $extension = $request->image->extension();
        $request->file('image')->move('storage/image/', $file_name . '.' . $extension);
        $file_url = url('storage/image' . '/' . $file_name . '.' . $extension);

        if (Image::insert([
            'image' => $file_url
        ])) {
            $response = [
                'status' => 201,
                'message' => 'success',
                'data' => [
                    'image' => $file_url
                ]
            ];
            return response()->json($response, 201);
        } else {
            $response = [
                'status' => 400,
                'message' => 'failed',
                'data' => $validate
            ];
            return response()->json($response, 400);
        }
    }

    public function update(Request $request, $id)
    {
        $data = $this->checkID($id, Image::class);
        if ($data[1] != 0) {
            return response()->json($data[0], $data[1]);
        }

        $validate = [];
        $file_url = '';
        if ($request->hasFile('image')) {
            $validate = $this->validate($request, [
                'image' => 'required|image|mimes:jpg,png,jpeg,svg|max:700'
            ]);

            $file_name = time();
            $extension = $request->image->extension();
            $request->file('image')->move('storage/gallery/', $file_name . '.' . $extension);
            $file_url = url('storage/gallery' . '/' . $file_name . '.' . $extension);
        } else {
            $validate = $this->validate($request, [
                'old_pict' => 'required'
            ]);

            $file_url = $validate['old_pict'];
        }

        if (Image::where('id', $id)
            ->update([
                'image' => $file_url
            ])
        ) {
            $response = [
                'status' => 200,
                'message' => 'success',
                'data' => [
                    'image' => $file_url
                ]
            ];
            return response()->json($response, 200);
        } else {
            $response = [
                'status' => 400,
                'message' => 'failed update gallery!',
                'data' => $validate
            ];
            return response()->json($response, 400);
        }
    }

    public function delete($id)
    {
        $data = $this->checkID($id, Image::class);
        if ($data[1] != 0) {
            return response()->json($data[0], $data[1]);
        }

        if (Image::where('id', $id)->delete()) {
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
                'data' => null
            ];
            return response()->json($response, 400);
        }
    }
}
