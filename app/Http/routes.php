<?php

Route::group(['prefix' => 'api/v2', 'middleware' => 'cors'], function()
{

    Route::group(['middleware' => 'couch.auth'], function()
    {
        Route::resource('user','V2\UserController', ['except' =>['create', 'edit', 'destroy']]);
        Route::resource('expenses','V2\ExpensesController');
        Route::get('categories/dashboard',['as' => 'api.v2.categories.dashboard', 'uses' => 'V2\CategoriesController@dashboard']);
        Route::resource('categories','V2\CategoriesController');
        Route::resource('categories.expenses','V2\CategoriesExpensesController', ['except' =>['create', 'edit']]);
        Route::resource('friends','V2\FriendsController');
        Route::post('seed/categories', ['as' => 'api.v2.seed.categories', 'uses' => 'V2\SeederController@categories']);
        Route::post('seed/friends', ['as' => 'api.v2.seed.friends', 'uses' => 'V2\SeederController@friends']);
        Route::post('seed/expenses', ['as' => 'api.v2.seed.expenses', 'uses' => 'V2\SeederController@expenses']);
    });

    Route::post('auth/login', ['as' => 'api.v2.auth.login', 'uses' => 'V2\AuthController@login']);
    Route::post('auth/register', ['as' => 'api.v2.auth.register', 'uses' => 'V2\AuthController@register']);
    Route::delete('auth/logout', ['as' => 'api.v2.auth.logout', 'uses' => 'V2\AuthController@logout']);
    Route::post('auth/recover', ['as' => 'api.v2.auth.recover', 'uses' => 'V2\AuthController@recover']);

    Route::controllers([
        'password' => 'Auth\PasswordController',
    ]);

});