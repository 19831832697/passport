<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('reg','user\UserController@register');//注册通行
Route::post('login','user\UserController@login');//登录通行
Route::post('center','user\UserController@center');//登录通行
Route::post('cart','goods\GoodsController@cart');//登录通行
Route::get('zPay','alipay\AlipayController@zPay');//支付通行
//Route::get('test','alipay\AlipayController@test');//支付通行