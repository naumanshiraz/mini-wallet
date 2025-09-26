<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class SanctumAuth
{
  public function handle(Request $request, Closure $next): Response
  {
    \Log::info('SanctumAuth: Starting authentication');

    if (!$token = $request->bearerToken()) {
      \Log::warning('SanctumAuth: No bearer token found');
      return response()->json(['message' => 'No authentication token provided'], 401);
    }

    \Log::info('SanctumAuth: Token found', ['token_prefix' => substr($token, 0, 10)]);

    $accessToken = PersonalAccessToken::findToken($token);

    if (!$accessToken) {
      \Log::warning('SanctumAuth: Token not found in database');
      return response()->json(['message' => 'Invalid authentication token'], 401);
    }

    \Log::info('SanctumAuth: Token valid', ['user_id' => $accessToken->tokenable_id]);

    $user = $accessToken->tokenable;

    if (!$user) {
      \Log::error('SanctumAuth: User not found for token');
      return response()->json(['message' => 'User associated with token not found'], 401);
    }

    auth()->guard('sanctum')->setUser($user);
    auth()->setUser($user);

    $request->setUserResolver(function () use ($user) {
      return $user;
    });

    \Log::info('SanctumAuth: User authenticated successfully', [
      'user_id' => $user->id,
      'auth_check' => auth()->check(),
      'auth_id' => auth()->id()
    ]);

    return $next($request);
  }
}