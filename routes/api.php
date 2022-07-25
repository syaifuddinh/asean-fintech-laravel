<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Auth\AuthController;

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

$router->post("/login", [AuthController::class, "login"]);

Route::group(["middleware" => 'auth:sanctum'], function($query) use ($router) {
    $router->post("/logout", [AuthController::class, "logout"]);
});

Route::group(["prefix" => "product", "middleware" => 'auth:sanctum'], function($query) use ($router) {
    $router->get("/", [ProductController::class, "index"]);
    $router->get("/group-by-category", [ProductController::class, "indexByCategory"]);
    $router->get("/{id}", [ProductController::class, "show"]);
});

Route::group(["prefix" => "product"], function($query) use ($router) {
    $router->get("/group-by-category", [ProductController::class, "indexByCategory"]);
});

 