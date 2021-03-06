<?php

use App\Http\Controllers\ZohoAPIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/make-account', [ZohoAPIController::class,'createAccount']);
Route::get('/create-auth-token/{code?}',[ZohoAPIController::class,'createAuthToken']);
Route::get('/create-campaign',[ZohoAPIController::class,'createCampaign']);
