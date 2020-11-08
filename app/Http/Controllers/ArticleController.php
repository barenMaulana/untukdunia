<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show', 'search']]);
    }

    public function index()
    {
        $response = [
            'status' => 200,
            'message' => 'success',
            'data' => Article::all()
        ];

        for ($i = 0; $i <= count($response['data']) - 1; $i++) {
            $response['data'][$i]['image']  = rtrim($response['data'][$i]['image']);
        }

        return response()->json($response, 200);
    }

    public function show($id)
    {
        $data = $this->checkID($id, Article::class);
        if ($data[1] != 0) {
            return response()->json($data[0], $data[1]);
        }

        $response = [
            'status' => 200,
            'message' => 'success',
            'data' => Article::find($id)
        ];

        $response['data']['image']  = rtrim($response['data']['image']);

        return response()->json($response, 200);
    }

    public function insert(Request $request)
    {
        $validate = $this->validate($request, [
            'article_title' => 'required|min:3',
            'article_content' => 'required|min:10',
            'article_sub_content' => 'required|max:40',
            'category' => 'min:3',
            'image' => 'required|image|mimes:jpg,png,jpeg,svg|max:700'
        ]);

        $file_name = time();
        $extension = $request->image->extension();
        $request->file('image')->move('storage/article/', $file_name . '.' . $extension);
        $file_url = url('storage/article' . '/' . $file_name . '.' . $extension);

        if (Article::insert([
            'article_title' => $validate['article_title'],
            'article_content' => $validate['article_content'],
            'article_sub_content' => $validate['article_sub_content'],
            'category' => $validate['category'],
            'image' => $file_url
        ])) {
            $response = [
                'status' => 201,
                'message' => 'success',
                'data' => [
                    'article_title' => $validate['article_title'],
                    'article_content' => $validate['article_content'],
                    'article_sub_content' => $validate['article_sub_content'],
                    'category' => $validate['category'],
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
        $data = $this->checkID($id, Article::class);
        if ($data[1] != 0) {
            return response()->json($data[0], $data[1]);
        }

        $validate = [];
        $file_url = '';
        if ($request->hasFile('image')) {
            $validate = $this->validate($request, [
                'article_title' => 'required|min:3',
                'article_content' => 'required|min:10',
                'article_sub_content' => 'required|max:40',
                'category' => 'min:3',
                'image' => 'required|image|mimes:jpg,png,jpeg|max:700'
            ]);

            $file_name = time();
            $extension = $request->image->extension();
            $request->file('image')->move('storage/article/', $file_name . '.' . $extension);
            $file_url = url('storage/article' . '/' . $file_name . '.' . $extension);
        } else {
            $validate = $this->validate($request, [
                'article_title' => 'required|min:3',
                'article_content' => 'required|min:10',
                'article_sub_content' => 'required|max:40',
                'category' => 'min:3',
                'old_pict' => 'required'
            ]);
            $file_url = $validate['old_pict'];
        }

        if (Article::where('id', $id)
            ->update([
                'article_title' => $validate['article_title'],
                'article_content' => $validate['article_content'],
                'article_sub_content' => $validate['article_sub_content'],
                'category' => $validate['category'],
                'image' => $file_url
            ])
        ) {
            $response = [
                'status' => 200,
                'message' => 'success',
                'data' => [
                    'article_title' => $validate['article_title'],
                    'article_content' => $validate['article_content'],
                    'article_sub_content' => $validate['article_sub_content'],
                    'category' => $validate['category'],
                    'image' => $file_url
                ]
            ];
            return response()->json($response, 200);
        } else {
            $response = [
                'status' => 400,
                'message' => 'failed update article!',
                'data' => $validate
            ];
            return response()->json($response, 400);
        }
    }

    public function delete($id)
    {
        $data = $this->checkID($id, Article::class);
        if ($data[1] != 0) {
            return response()->json($data[0], $data[1]);
        }

        if (Article::where('id', $id)->delete()) {
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

    public function search(Request $request)
    {
        $keyword = $request->K;

        if ($keyword === null) {
            $response = [
                'status' => 400,
                'message' => 'need keyword parameter!',
                'data' => null
            ];

            return response($response, 400);
        } else if ($keyword == "") {
            $response = [
                'status' => 200,
                'message' => 'success',
                'data' => Article::all()
            ];

            for ($i = 0; $i <= count($response['data']) - 1; $i++) {
                $response['data'][$i]['image']  = rtrim($response['data'][$i]['image']);
            }

            return response()->json($response, 200);
        }

        $search = Article::where('article_title', 'like', '%' . $keyword . '%')->get();
        if (count($search) == 0) {
            $response = [
                'status' => 200,
                'message' => 'not found any data with that keyword!',
                'data' => null
            ];

            return response()->json($response, 200);
        } else if (count($search) != 0) {
            $response = [
                'status' => 200,
                'message' => 'success',
                'data' => $search
            ];

            for ($i = 0; $i <= count($response['data']) - 1; $i++) {
                $response['data'][$i]['image']  = rtrim($response['data'][$i]['image']);
            }

            return response()->json($response, 200);
        } else {
            $response = [
                'status' => 200,
                'message' => 'success',
                'data' => Article::all()
            ];

            for ($i = 0; $i <= count($response['data']) - 1; $i++) {
                $response['data'][$i]['image']  = rtrim($response['data'][$i]['image']);
            }

            return response()->json($response, 200);
        }
    }
}
