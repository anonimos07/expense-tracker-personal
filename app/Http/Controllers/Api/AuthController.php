<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Services\DefaultDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request){
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        
        DefaultDataService::createFor($user);

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('auth')->plainTextToken,
        ],201);
    }

        public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 422);
        }

        return response()->json([
            'user'  => $user,
            'token' => $user->createToken('auth')->plainTextToken,
        ]);
    }

    public function logou(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->noContent();
    }
}
