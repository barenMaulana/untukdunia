<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
            'data' => Article::all(),
        ];

        for ($i = 0; $i <= count($response['data']) - 1; $i++) {
            $response['data'][$i]['image']  = rtrim($response['data'][$i]['image']);
            $response['data'][$i]['category'] = explode(',', $response['data'][$i]['category']);
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
        $response['data']['category'] = explode(',', $response['data']['category']);


        return response()->json($response, 200);
    }

    public function insert(Request $request)
    {
        $validate = $this->validate($request, [
            'article_title' => 'required|min:3',
            'article_content' => 'required|min:100',
            'article_sub_content' => 'required|max:2000',
            'image' => 'required|image|mimes:jpg,png,jpeg,svg|max:8000'
        ]);

        if($this->isIsset($validate['article_title']) > 0){
            $response = [
                'status' => 400,
                'message' => 'judul tersebut sudah digunakan!',
            ];
            return response()->json($response, 400);
        }

        $file_name = time();
        $extension = $request->image->extension();
        $request->file('image')->move('storage/article/', $file_name . '.' . $extension);
        $file_url = url('storage/article' . '/' . $file_name . '.' . $extension);
        $slug = Str::of($validate['article_title'])->slug('-');

        $category = '';
        if ($request->category == null) {
            $category = 'education';
        } else {
            $category = $request->category;
        }

        if (Article::insert([
            'article_title' => $validate['article_title'],
            'article_content' => $validate['article_content'],
            'article_sub_content' => $validate['article_sub_content'],
            'category' => $category,
            'slug' => $slug,
            'image' => $file_url
        ])) {
            $category = explode(',', $category);
            $response = [
                'status' => 201,
                'message' => 'success',
                'data' => [
                    'article_title' => $validate['article_title'],
                    'article_content' => $validate['article_content'],
                    'article_sub_content' => $validate['article_sub_content'],
                    'category' => $category,
                    'slug' => $slug,
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
                'article_sub_content' => 'required|max:150',
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
                'article_sub_content' => 'required|max:8000',
                'old_pict' => 'required'
            ]);
            $file_url = $validate['old_pict'];
        }

        $category = '';
        if ($request->category == null) {
            $category = 'education';
        } else {
            $category = $request->category;
        }

        if($this->isIsset($validate['article_title']) > 2){
            $response = [
                'status' => 400,
                'message' => 'judul tersebut sudah digunakan!',
            ];
            return response()->json($response, 400);
        }

        if (Article::where('id', $id)
            ->update([
                'article_title' => $validate['article_title'],
                'article_content' => $validate['article_content'],
                'article_sub_content' => $validate['article_sub_content'],
                'category' => $category,
                'slug' => Str::of($validate['article_title'])->slug('-'),
                'image' => $file_url
            ])
        ) {
            $category = explode(',', $category);
            $response = [
                'status' => 200,
                'message' => 'success',
                'data' => [
                    'article_title' => $validate['article_title'],
                    'article_content' => $validate['article_content'],
                    'article_sub_content' => $validate['article_sub_content'],
                    'category' => $category,
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
                $response['data'][$i]['category'] = explode(',', $response['data'][$i]['category']);
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

    public function isIsset(String $title){
        $search = Article::where('article_title', $title)->get();
        // var_dump(count($search));die;
        // var_dump($title);die;
            if (count($search) == 1) {
                return 2;
            }else if(count($search) > 0){
                return 1;
            }
            else{
                return 0;
            }
    } 
}