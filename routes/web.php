<?php

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

$router->post('auth/login', 'UsuarioController@authenticate');
$router->group(
    ['middleware' => 'jwt.auth'],
    function() use ($router) {
        $router->post('/usuario', 'UsuarioController@store');
        $router->get('/usuarios', 'UsuarioController@index');
        $router->get('/usuario/{id}', 'UsuarioController@show');
        $router->put('/usuario/{id}', 'UsuarioController@update');
        $router->delete('/usuario/{id}', 'UsuarioController@destroy');
    }
);
