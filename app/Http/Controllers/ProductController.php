<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
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
            'data' => Product::all()
        ];

        for ($i = 0; $i <= count($response['data']) - 1; $i++) {
            $response['data'][$i]['image_title']  = rtrim($response['data'][$i]['image_title']);
            $response['data'][$i]['image']  = rtrim($response['data'][$i]['image']);
        }

        return response()->json($response, 200);
    }

    public function show($id)
    {
        $data = $this->checkID($id, Product::class);
        if ($data[1] != 0) {
            return response()->json($data[0], $data[1]);
        }

        $response = [
            'status' => 200,
            'message' => 'success',
            'data' => Product::find($id)
        ];

        $response['data']['image_title']  = rtrim($response['data']['image_title']);
        $response['data']['image']  = rtrim($response['data']['image']);

        return response()->json($response, 200);
    }

    public function insert(Request $request)
    {
        $validate = $this->validate($request, [
            'image_title' => 'required|min:5',
            'image' => 'required|image|mimes:jpg,png,jpeg,svg|max:700'
        ]);
        $file_name = time();
        $extension = $request->image->extension();
        $request->file('image')->move('storage/product/', $file_name . '.' . $extension);
        $file_url = url('storage/product' . '/' . $file_name . '.' . $extension);

        if (Product::insert([
            'image_title' => $validate['image_title'],
            'image' => $file_url
        ])) {
            $response = [
                'status' => 201,
                'message' => 'success',
                'data' => [
                    'image_title' => $validate['image_title'],
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
        $data = $this->checkID($id, Product::class);
        if ($data[1] != 0) {
            return response()->json($data[0], $data[1]);
        }

        $validate = [];
        $file_url = '';
        if ($request->hasFile('image')) {
            $validate = $this->validate($request, [
                'image_title' => 'required|min:5',
                'image' => 'required|image|mimes:jpg,png,jpeg|max:700'
            ]);

            $file_name = time();
            $extension = $request->image->extension();
            $request->file('image')->move('storage/product/', $file_name . '.' . $extension);
            $file_url = url('storage/product' . '/' . $file_name . '.' . $extension);
        } else {
            $validate = $this->validate($request, [
                'image_title' => 'required|min:5',
                'old_pict' => 'required'
            ]);

            $file_url = $validate['old_pict'];
        }

        if (Product::where('id', $id)
            ->update([
                'image_title' => $validate['image_title'],
                'image' => $file_url
            ])
        ) {
            $response = [
                'status' => 200,
                'message' => 'success',
                'data' => [
                    'image_title' => $validate['image_title'],
                    'image' => $file_url
                ]
            ];
            return response()->json($response, 200);
        } else {
            $response = [
                'status' => 400,
                'message' => 'failed update product!',
                'data' => $validate
            ];
            return response()->json($response, 400);
        }
    }

    public function delete($id)
    {
        $data = $this->checkID($id, Product::class);
        if ($data[1] != 0) {
            return response()->json($data[0], $data[1]);
        }

        if (Product::where('id', $id)->delete()) {
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
