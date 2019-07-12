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
    Route::get('projects/{project}/edit', ['as' => 'projects.edit', 'uses' => 'ProjectController@edit']);
    Route::patch('projects/{project}', ['as' => 'projects.update', 'uses' => 'ProjectController@update']);

    Route::get('projects/{project}/create-requirement', ['as' => 'requirements.create', 'uses' => 'RequirementController@create']);
    Route::post('projects/{project}/requirement', ['as' => 'requirements.store', 'uses' => 'RequirementController@store']);
    Route::get('requirements/{project?}', ['as' => 'requirements.projectRequirement', 'uses' => 'RequirementController@projectRequirement']);
    Route::get('requirements/{requirement}/edit', ['as' => 'requirements.edit', 'uses' => 'RequirementController@edit']);
    Route::patch('requirements/{requirement}', ['as' => 'requirements.update', 'uses' => 'RequirementController@update']);

    Route::get('time-entries', ['as' => 'timeEntries.index', 'uses' => 'TimeEntryController@index']);
    Route::get('time-entries/create', ['as' => 'timeEntries.create', 'uses' => 'TimeEntryController@create']);
    Route::post('time-entries', ['as' => 'timeEntries.store', 'uses' => 'TimeEntryController@store']);
    Route::get('time-entries/{timeEntry}/edit', ['as' => 'timeEntries.edit', 'uses' => 'TimeEntryController@edit']);
    Route::patch('time-entries/{timeEntry}/update', ['as' => 'timeEntries.update', 'uses' => 'TimeEntryController@update']);

    Route::get('todos/{requirement?}', ['as' => 'todos.index', 'uses' => 'TodoController@index']);
    Route::post('requirements/{requirement}/todo', ['as' => 'todos.store', 'uses' => 'TodoController@store']);
    Route::patch('todos/{todo}/update', ['as' => 'todos.update', 'uses' => 'TodoController@update']);
});
