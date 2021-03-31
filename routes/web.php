<?php

$router->get('/', function () use ($router) {
    return;
});

// $router->get('/key', function () {
//     return \Illuminate\Support\Str::random(32);
// });

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
$router->get('/galleries', 'GalleryController@index');
$router->get('/galleries/{id}', 'GalleryController@show');
$router->post('/galleries', 'GalleryController@insert');
$router->post('/galleries/{id}', 'GalleryController@update');
$router->delete('/galleries/{id}', 'GalleryController@delete');

// article api
$router->get('/articles/search', 'ArticleController@search');
$router->get('/articles', 'ArticleController@index');
$router->get('/articles/{slug}', 'ArticleController@show');
$router->post('/articles', 'ArticleController@insert');
$router->post('/articles/{id}', 'ArticleController@update');
$router->delete('/articles/{id}', 'ArticleController@delete');

// user api
$router->get('/users/check-auth', 'UserController@authorization');
$router->get('/users', 'UserController@index');
$router->get('/users/{id}', 'UserController@show');
$router->post('/users', 'UserController@insert');
$router->put('/users/{id}', 'UserController@update');
$router->delete('/users/{id}', 'UserController@delete');

//  auth api
$router->post('/login', 'AuthController@login');

//  jumbotron image
$router->get('/image-management/images', 'ImageController@index');
$router->post('/image-management/images', 'ImageController@insert');
$router->get('/image-management/images/{id}', 'ImageController@show');
$router->post('/image-management/images/{id}', 'ImageController@update');
$router->delete('/image-management/images/{id}', 'ImageController@delete');

// send mail
$router->post('/notification-management/email', 'EmailController@index');
