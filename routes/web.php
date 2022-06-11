<?php

/** @var \Laravel\Lumen\Routing\Router $router */
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BahanBakuController;
use App\Http\Controllers\MutasiBahanBakuController;
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

$router->group(['prefix'=>'auth'], function() use ($router){
    $router->post('register','AuthController@register');
    $router->post('login','AuthController@login');
});


$router->group(['prefix'=>'bahanbaku', 'middleware'=> ['auth','logrequest']], function() use ($router){
    $router->post('tambahdatabahanbaku','BahanBakuController@tambah');
    $router->post('tambahmutasibahanbaku','MutasiBahanBakuController@tambah');
    $router->post('getbahanbaku','BahanBakuController@GetBahanBaku');
    $router->post('updatebahanbaku','BahanBakuController@UpdateBahanBaku');
    // $router->get('/', function () use ($router) {
    //     return 'ok';
    // });
});
$router->group(['prefix'=>'mutasibahanbaku', 'middleware'=> ['auth','logrequest']], function() use ($router){
    $router->post('tambahmutasibahanbaku','MutasiBahanBakuController@tambah');
    // $router->get('/', function () use ($router) {
    //     return 'ok';
    // });
});

$router->group(['prefix'=>'setengahjadi', 'middleware'=> ['auth','logrequest']], function() use ($router){
    $router->post('tambahmastersetengahjadi','SetengahJadiController@tambahmastersetengahjadi');
    $router->post('tambahkomposisisetengahjadi','SetengahJadiController@komposisi_setengahjadi');
});