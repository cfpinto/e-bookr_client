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

Route::get('/', 'Ebookr\Client\Http\Controllers\PageController@home')->name('home');
Route::get('houses', 'Ebookr\Client\Http\Controllers\RoomController@index')->name('rooms.index');
Route::get('houses/{slug}', 'Ebookr\Client\Http\Controllers\RoomController@slug')->name('rooms.show');
Route::post('form/contact', 'Ebookr\Client\Http\Controllers\FormController@contact')->name('forms.contact');
Route::resource('bookings', 'Ebookr\Client\Http\Controllers\BookingController');
Route::get('{slug}', 'Ebookr\Client\Http\Controllers\PageController@page')->name('pages.show');
