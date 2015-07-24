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

//Route::get('home', 'HomeController@index');
//
//Route::group(['prefix' => 'api'], function()
//{
//
//	Route::resource('user','API\UserController', ['except' =>['create', 'edit', 'destroy']]);
//	Route::get('categories/expenses','API\CategoriesController@expenses');
//	Route::resource('categories','API\CategoriesController', ['except' =>['create', 'edit']]);
//	Route::resource('expenses','API\ExpensesController', ['except' =>['create', 'edit']]);
//	Route::resource('categories.expenses','API\CategoriesExpensesController', ['except' =>['create', 'edit']]);
//	Route::resource('friends','API\FriendsController', ['except' =>['create', 'update', 'edit']]);
//   Route::post('auth/login', ['as' => 'api.auth.login', 'uses' => 'API\AuthController@login']);
//   Route::get('auth/refresh', ['as' => 'api.auth.refresh', 'uses' => 'API\AuthController@refreshToken']);
//   Route::post('auth/reset', ['as' => 'api.auth.reset', 'uses' => 'API\AuthController@reset']);
//
//
//});

Route::group(['prefix' => 'api/v2', 'middleware' => 'cors'], function()
{

    Route::group(['middleware' => 'couch.auth'], function()
    {
        Route::resource('user','V2\UserController', ['except' =>['create', 'edit', 'destroy']]);
        Route::resource('expenses','V2\ExpensesController');
        Route::resource('categories','V2\CategoriesController');
        Route::resource('categories.expenses','V2\CategoriesExpensesController', ['except' =>['create', 'edit']]);
        Route::resource('friends','V2\FriendsController');
        Route::post('seed/categories', ['as' => 'api.v2.seed.categories', 'uses' => 'V2\SeederController@categories']);
        Route::post('seed/friends', ['as' => 'api.v2.seed.friends', 'uses' => 'V2\SeederController@friends']);
        Route::post('seed/expenses', ['as' => 'api.v2.seed.expenses', 'uses' => 'V2\SeederController@expenses']);
    });

    Route::post('auth/login', ['as' => 'api.v2.auth.login', 'uses' => 'V2\AuthController@login']);
    Route::delete('auth/logout', ['as' => 'api.v2.auth.logout', 'uses' => 'V2\AuthController@logout']);
    Route::post('auth/reset', ['as' => 'api.v2.auth.reset', 'uses' => 'V2\AuthController@reset']);

});