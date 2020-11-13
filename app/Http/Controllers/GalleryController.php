<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gallery;

class GalleryController extends Controller
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
            'data' => Gallery::all()
        ];

        for ($i = 0; $i <= count($response['data']) - 1; $i++) {
            $response['data'][$i]['image_title']  = rtrim($response['data'][$i]['image_title']);
            $response['data'][$i]['article_link']  = rtrim($response['data'][$i]['article_link']);
            $response['data'][$i]['image']  = rtrim($response['data'][$i]['image']);
        }

        return response()->json($response, 200);
    }

    public function show($id)
    {
        $data = $this->checkID($id, Gallery::class);
        if ($data[1] != 0) {
            return response()->json($data[0], $data[1]);
        }

        $response = [
            'status' => 200,
            'message' => 'success',
            'data' => Gallery::find($id)
        ];

        $response['data']['image_title']  = rtrim($response['data']['image_title']);
        $response['data']['article_link']  = rtrim($response['data']['article_link']);
        $response['data']['image']  = rtrim($response['data']['image']);

        return response()->json($response, 200);
    }

    public function insert(Request $request)
    {
        $validate = $this->validate($request, [
            'image_title' => 'required|min:5',
            'article_link' => 'nullable',
            'image' => 'required|image|mimes:jpg,png,jpeg,svg|max:700'
        ]);
        $file_name = time();
        $extension = $request->image->extension();
        $request->file('image')->move('storage/gallery/', $file_name . '.' . $extension);
        $file_url = url('storage/gallery' . '/' . $file_name . '.' . $extension);

        if (Gallery::insert([
            'image_title' => $validate['image_title'],
            'article_link' => $validate['article_link'],
            'image' => $file_url
        ])) {
            $response = [
                'status' => 201,
                'message' => 'success',
                'data' => [
                    'image_title' => $validate['image_title'],
                    'article_link' => $validate['article_link'],
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
        $data = $this->checkID($id, Gallery::class);
        if ($data[1] != 0) {
            return response()->json($data[0], $data[1]);
        }

        $validate = [];
        $file_url = '';
        if ($request->hasFile('image')) {
            $validate = $this->validate($request, [
                'image_title' => 'required|min:5',
                'article_link' => 'nullable',
                'image' => 'required|image|mimes:jpg,png,jpeg,svg|max:700'
            ]);

            $file_name = time();
            $extension = $request->image->extension();
            $request->file('image')->move('storage/gallery/', $file_name . '.' . $extension);
            $file_url = url('storage/gallery' . '/' . $file_name . '.' . $extension);
        } else {
            $validate = $this->validate($request, [
                'image_title' => 'required|min:5',
                'article_link' => 'nullable',
                'old_pict' => 'required'
            ]);

            $file_url = $validate['old_pict'];
        }

        if (Gallery::where('id', $id)
            ->update([
                'image_title' => $validate['image_title'],
                'article_link' => $validate['article_link'],
                'image' => $file_url
            ])
        ) {
            $response = [
                'status' => 200,
                'message' => 'success',
                'data' => [
                    'image_title' => $validate['image_title'],
                    'article_link' => $validate['article_link'],
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
        $data = $this->checkID($id, Gallery::class);
        if ($data[1] != 0) {
            return response()->json($data[0], $data[1]);
        }

        if (Gallery::where('id', $id)->delete()) {
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
