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
Route::group(
    ['middleware' => ['web']],
    function () {
        Route::get('/', 'Ebookr\Client\Http\Controllers\PageController@home')->name('home');
        Route::get('houses', 'Ebookr\Client\Http\Controllers\RoomController@index')->name('rooms.index');
        Route::get('houses/{slug}', 'Ebookr\Client\Http\Controllers\RoomController@slug')->name('rooms.show');
        Route::post('form/contact', 'Ebookr\Client\Http\Controllers\FormController@contact')->name('forms.contact');
        Route::resource('bookings', 'Ebookr\Client\Http\Controllers\BookingController');
        Route::get('{slug}', 'Ebookr\Client\Http\Controllers\PageController@page')->name('pages.show');
        Route::get(
            'locale/{locale}',
            function ($locale) {
                if (in_array($locale, config('voyager.multilingual.locales'))) {
                    session()->flush();
                    app()->setLocale($locale);
                    session()->put(\Ebookr\Client\Http\Middleware\Locale::SESSION_KEY, $locale);
                }

                return redirect()
                    ->back(302, ['Cache-Control' => 'no-store, no-cache, must-revalidate'])
                    ->withInput(['key' => mt_rand(100000, 900000)]);
            }
        )->name('locale.set');
    }
);
