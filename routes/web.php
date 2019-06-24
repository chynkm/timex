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

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/home', 'HomeController@index')->name('home');

    Route::get('projects', ['as' => 'projects.index', 'uses' => 'ProjectController@index']);
    Route::get('projects/create', ['as' => 'projects.create', 'uses' => 'ProjectController@create']);
    Route::post('projects', ['as' => 'projects.store', 'uses' => 'ProjectController@store']);
    Route::get('projects/{project}', ['as' => 'projects.show', 'uses' => 'ProjectController@show']);

    Route::post('projects/{project}/requirement', ['as' => 'projects.requirement', 'uses' => 'ProjectRequirementController@store']);
});
