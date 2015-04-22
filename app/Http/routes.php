<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');

Route::group(['prefix' => 'api'], function()
{

	Route::resource('user','API\UserController', ['except' =>['create', 'edit', 'destroy']]);
	Route::resource('categories','API\CategoriesController', ['except' =>['create', 'edit']]);
	Route::resource('expenses','API\ExpensesController', ['except' =>['create', 'edit']]);
	Route::resource('friends','API\FriendsController', ['except' =>['create', 'update', 'edit']]);
    Route::post('auth/login', 'API\AuthController@login');
    Route::post('auth/reset', 'API\AuthController@reset');


});

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);