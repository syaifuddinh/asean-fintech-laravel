<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request) {
        $fields = $request->validate([
            "email" => "required|string",
            "password" => "required|string"
        ]);

        $user = User::whereEmail($fields["email"])->first();

        if(!$user || !Hash::check($fields["password"], $user->password)) {
            return response()->json([
                "success" => false,
                "message" => "Invalid credential"
            ]);
        }

        $token = $user->createToken("myapptoken")->plainTextToken;

        $data = [
            "token" => $token,
            "user" => $user
        ];

        return response()->json([
            "success" => true,
            "message" => "You are successfully logged in",
            "data" => $data
        ]);
    }

    public function logout(Request $request) {
        auth()->user()->tokens()->delete();

        return response()->json([
            "success" => true,
            "message" => "Logged in"
        ]);
    }
}
