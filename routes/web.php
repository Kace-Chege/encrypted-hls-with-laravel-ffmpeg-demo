<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

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

Route::get('/video/playlist/{playlist}', function ($playlist) {
    return FFMpeg::dynamicHLSPlaylist()
        ->fromDisk('public')
        ->open("videos/{$playlist}")
        ->setKeyUrlResolver(function ($key) {
            return route('video.key', ['key' => $key]);
        })
        ->setPlaylistUrlResolver(function ($playlist) {
            return route('video.playlist', ['playlist' => $playlist]);
        })
        ->setMediaUrlResolver(function ($media) {
            return Storage::disk('public')->url("videos/{$media}");
        });
})->name('video.playlist');

Route::get('/video/key/{key}', function ($key) {
    return Storage::disk('secrets')->download($key);
})->name('video.key');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
