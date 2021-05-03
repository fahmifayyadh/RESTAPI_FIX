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

$router->group(['middleware' => (['auth','role.admin'])], function () use ($router) {
  $router->get("/place/{skip}/{take}", "admin\PlaceController@index");
  $router->post('/place/create', 'admin\PlaceController@store');
  $router->put("/place/{id}/update", "admin\PlaceController@update");
  $router->delete('/place/{id}/delete', 'admin\PlaceController@destroy');

  $router->get('/user/{skip}/{take}', 'admin\UserController@index');
  $router->post('/user/create', 'admin\UserController@store');
  
});

$router->group(['middleware' => 'auth'], function () use ($router) {
  $router->get("/user", "UserController@index");
});
