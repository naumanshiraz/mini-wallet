<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
  public function register(Request $request)
  {
    $fields = $request->validate([
      'name' => 'required|string|max:80',
      'email' => 'required|email|unique:users,email',
      'password' => 'required|string|min:6|confirmed',
    ]);

    $user = User::create([
      'name' => $fields['name'],
      'email' => $fields['email'],
      'password' => Hash::make($fields['password']),
      'balance' => 1000.00, // default starting balance for demo
    ]);

    $token = $user->createToken('wallet_token')->plainTextToken;
    return response()->json([
      'user' => $user,
      'token' => $token,
    ], 201);
  }

  public function login(Request $request)
  {
    $fields = $request->validate([
      'email' => 'required|email',
      'password' => 'required|string',
    ]);
    $user = User::where('email', $fields['email'])->first();
    if (!$user || !Hash::check($fields['password'], $user->password)) {
      return response()->json(['message' => 'Invalid credentials'], 401);
    }
    $token = $user->createToken('wallet_token')->plainTextToken;
    return response()->json([
      'user' => $user,
      'token' => $token,
    ]);
  }

  public function logout(Request $request)
  {
    $user = auth()->guard('sanctum')->user();

    if (!$user) {
      return response()->json(['message' => 'No authenticated user found'], 401);
    }

    auth()->logout();

    return response()->json(['message' => 'Logged out']);
  }
}
