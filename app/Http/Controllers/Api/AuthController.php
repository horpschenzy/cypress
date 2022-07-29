<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Activity;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ApiLoginRequest;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user =  User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'api_token' => Str::random(70),
        ]);
        if ($user) {
            //Assign Activities to User`
            $activityIds = Activity::where('user', 'ALL USERS')->get()->pluck('id');
            $user->activities()->sync($activityIds);
            return response()->json(['status' => true, 'message' => 'Registration successful'], 201);
        }
        return response()->json(['status' => false, 'message' => 'Registration not successful'], 422);
    }

    public function login(ApiLoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (!auth()->attempt($credentials)) {
            return response()->json(['status' => false, 'message' => 'Invalid Email or Password'], 401);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'access_token' => auth()->user()->api_token,
                'token_type' => 'bearer',
            ]
        ]);
    }
}
