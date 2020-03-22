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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', 'MainController@index');

Route::get('/clear', 'MainController@clearAll');

Route::get('/love', 'MainController@sheepLove');

Route::get('/kill', 'MainController@sheepKill');

Route::get('/move', 'MainController@sheepMove');

Route::post('/add/', 'MainController@addSheep');

Route::get('/stat', 'MainController@stat');

Route::get('/statistic', 'MainController@statAdd');

Route::get('/command', 'MainController@command');