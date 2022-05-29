<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});
// $router->get("/key", function()
// {
//   return \Illuminate\Support\Str::random(32);
// });

// $router->post("/register", "AuthController@register");

$router->post("/login", "AuthController@login");

$router->group(['middleware' => (['auth','role.admin']), 'prefix' => 'admin'], function () use ($router) {
  $router->post('/place/create', 'admin\PlaceController@store');
  $router->put("/place/{id}/update", "admin\PlaceController@update");
  $router->get("/place/{id}/update", "admin\PlaceController@edit");
  $router->delete('/place/{id}/delete', 'admin\PlaceController@destroy');
  $router->get("/place/{skip}/{take}", "admin\PlaceController@index");

  $router->post('/user/create', 'admin\UserController@store');
    $router->get('/user/{id}/update', 'admin\UserController@edit');
    $router->put('/user/{id}/update', 'admin\UserController@update');
  $router->delete('/user/{id}/delete', 'admin\UserController@destroy');
  $router->get('/user/{skip}/{take}', 'admin\UserController@index');

  $router->get('/pic/create', 'admin\PicController@create');
  $router->post('/pic/create', 'admin\PicController@store');
  $router->get('/pic/{id}/update', 'admin\PicController@edit');
  $router->put('/pic/{id}/update', 'admin\PicController@update');
  $router->delete('/pic/{id}/delete', 'admin\PicController@destroy');
  $router->get('/pic/{skip}/{take}', 'admin\PicController@index');

  $router->get('/visit/create', 'admin\VisitorController@create');
  $router->post('/visit/create', 'admin\VisitorController@store');
  $router->get('/visit/{id}/update', 'admin\VisitorController@edit');
  $router->put('/visit/{id}/update', 'admin\VisitorController@update');
  $router->delete('/visit/{id}/delete', 'admin\VisitorController@destroy');
  $router->get('/visit/{skip}/{take}', 'admin\VisitorController@index');

    $router->group(['prefix'=>'country'], function () use ($router){
        $router->put('/{id}/update', 'admin\CountryController@update');
        $router->delete('/{id}/delete', 'admin\CountryController@delete');
    });
});

$router->group(['middleware' => (['auth','role.agent']),'prefix' => 'agent'], function () use ($router) {
  $router->get('/visit/{place_id}/create', 'agent\VisitorController@create');
  $router->post('/visit/{place_id}/create', 'agent\VisitorController@store');
  $router->get('/visit/{place_id}/{id}/update', 'agent\VisitorController@edit');
  $router->put('/visit/{place_id}/{id}/update', 'agent\VisitorController@update');
  $router->delete('/visit/{place_id}/{id}/delete', 'agent\VisitorController@destroy');
  $router->get('/visit/{place_id}/{skip}/{take}', 'agent\VisitorController@index');


  $router->group(['prefix'=>'visitor'], function () use ($router){
      $router->get('/{skip}/{take}', 'admin\UserVisitController@index');
      $router->post('/create', 'admin\UserVisitController@create');
      $router->put('/{id}/update', 'admin\UserVisitController@update');
      $router->delete('/{id}/delete', 'admin\UserVisitController@delete');
      $router->get('/{id}/detail/', 'admin\UserVisitController@detail');
  });

    $router->group(['prefix'=>'country'], function () use ($router){
        $router->get('/', 'admin\CountryController@index');
        $router->post('/create', 'admin\CountryController@create');
    });
});

$router->group(['middleware' => 'auth'], function () use ($router) {
  $router->get("/user", "UserController@index");

});
