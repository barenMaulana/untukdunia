<?php

$router->get('/', function () use ($router) {
    return "<h1>WEEBDEV</h1> ";
});

$router->get('/key', function () {
    return \Illuminate\Support\Str::random(32);
});

// link_collection api
$router->get('/links', 'LinkCollectionController@index');
$router->get('/links/{id}', 'LinkCollectionController@show');
$router->post('/links', 'LinkCollectionController@insert');
$router->put('/links/{id}', 'LinkCollectionController@update');

// product api
$router->get('/product', 'ProductController@index');
$router->get('/product/{id}', 'ProductController@show');
$router->post('/product', 'ProductController@insert');
$router->post('/product/{id}', 'ProductController@update');
$router->delete('/product/{id}', 'ProductController@delete');

// gallery api
$router->get('/gallery', 'GalleryController@index');
$router->get('/gallery/{id}', 'GalleryController@show');
$router->post('/gallery', 'GalleryController@insert');
$router->post('/gallery/{id}', 'GalleryController@update');
$router->delete('/gallery/{id}', 'GalleryController@delete');

// article api
$router->get('/article', 'ArticleController@index');
$router->get('/article/{id}', 'ArticleController@show');
$router->post('/article', 'ArticleController@insert');
$router->post('/article/{id}', 'ArticleController@update');
$router->delete('/article/{id}', 'ArticleController@delete');
$router->get('/article/search', 'ArticleController@search');

// user api
$router->get('/user', 'UserController@index');
$router->get('/user/{id}', 'UserController@show');
$router->post('/user', 'UserController@insert');
$router->put('/user/{id}', 'UserController@update');
$router->delete('/user/{id}', 'UserController@delete');

//  auth api
$router->post('/login', 'AuthController@login');
