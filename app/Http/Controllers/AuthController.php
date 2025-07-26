<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function Login(Request $request){
        $payload = $request->validate([
            "username"=>"required|string",
            "password"=>"required|string"
        ]);

        $user = User::where("username", $payload["username"])->first();
        if (!Hash::check($payload["password"],$user["password"])){
            return response()->json(
                [
                    "message"=>"Wrong username or password"
                ], 
                401
            );
        }
        $token = $user->createToken("auth_token")->plainTextToken;

        return response()->json([
            "message"=>"Login success",
            "token"=>$token,
            "user"=>$user
        ]);
    }

    public function Register(Request $request){
        $payload = $request->validate([
            "full_name"=>"required|string",
            "bio"=>"required|max:100",
            "username"=>"required|min:3|unique:users,username|alpha_num",
            "password"=>"required|string|min:6",
            "is_private"=>"required|boolean"
        ]);
        
        $payload["password"] = Hash::make($payload["password"]);

        $user = User::create($payload);

        $token = $user->createToken("auth_token")->plainTextToken;

        return response()->json([
            "message"=>"Register success",
            "token"=>$token,
            "user"=>$user
        ]);
    }

    public function Logout(Request $request){
        $status = $request->user()->tokens()->delete();
        return response()->json([
            "message"=>"logout success"
        ]);
    }
}
