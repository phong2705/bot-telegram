<?php

use Illuminate\Support\Facades\Route;

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

Route::any('/Notify', [App\Http\Controllers\Webhooks::class, 'gitlabWebhook']);


Route::get('/get-post-chat-id', [App\Http\Controllers\Webhooks::class, 'getPostChatIdAPI']);

Route::get('/', [App\Http\Controllers\Telegram::class, 'index']);
Route::get('/add-chat-id', [App\Http\Controllers\Telegram::class, 'create']);
Route::post('/add-chat-id', [App\Http\Controllers\Telegram::class, 'store']);