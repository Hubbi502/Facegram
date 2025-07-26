<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(["prefix"=>"/v1"],function ()
{

    Route::group(["prefix"=>"/auth"],function (){
        Route::post("/login", [AuthController::class, "Login"]);
        Route::post("/register", [AuthController::class, "Register"]);
        Route::post("/logout", [AuthController::class, "Logout"])->middleware("auth:sanctum");
    });

    Route::middleware("auth:sanctum")->group(function(){
        Route::apiResource("/posts", PostController::class);

        Route::group(["prefix"=>"/users"], function (){
            Route::get("/{username}", [UserController::class, "GetDetailUser"]);
            Route::post("/{username}/follow",[UserController::class, "Follow"]);
            Route::post("/{username}/unfollow",[UserController::class, "Unfollow"]);
            Route::post("/{username}/accept", [UserController::class, "AcceptFollow"]);
            Route::get("/{username}/follower",[UserController::class, "GetFollower"]);
        });

        Route::get("/following",[UserController::class, "GetFollowing"]);
    });


}
);
