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
//Route::post('login','user\UserController@login');//登录通行
Route::post('center','user\UserController@center');//登录通行
Route::post('cart','goods\GoodsController@cart');//登录通行
Route::get('zPay','alipay\AlipayController@zPay');//支付通行
Route::post('notify','alipay\AlipayController@notify');//异步回调
Route::get('aliReturn','alipay\AlipayController@aliReturn');//同步回调



Route::post('reg_do','login\LoginController@reg_do');
Route::post('login_do','login\LoginController@login_do');
Route::get('login_list','login\LoginController@login');
Route::post('loginDo','login\LoginController@loginDo');
Route::get('come','login\LoginController@come');


Route::post('login','user\LoginController@login')->middleware('time');
Route::get('hou','user\HouController@hou');
Route::post('usertime','user\HouController@usertime');
