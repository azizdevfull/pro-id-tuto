<?php

use App\Http\Controllers\OAuth2Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/oauth/proid/{isMobileOrWeb}/login', [OAuth2Controller::class, 'redirectToProId']);
Route::get('/oauth/proid/{isMobileOrWeb}/callback', [OAuth2Controller::class, 'handleProIdCallback']);
