<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
  public function login(Request $request)
  {
    $request->validate([
      'email' => 'required|email',
      'password' => 'required'
    ]);

    $throttleKey = strtolower($request->email) . '|' . $request->ip();

    if(RateLimiter::tooManyAttempts($throttleKey, 5)) {
      $seconds = RateLimiter::availableIn($throttleKey);
      throw ValidationException::withMessages([
        'email' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik."
      ]);
    }

    if(!Auth::attempt($request->only('email', 'password'))) {
      RateLimiter::hit($throttleKey, 60);
      throw ValidationException::withMessage([
        'email' => 'Email atau password salah.'
      ]);
    }

    RateLimiter::clear($throttleKey);

    $user = Auth::user();
    $token = $user->createToken('auth_token')->plainTextToken;

    Cache::put("user_session:{$user->id}", $user, now()->addHours(2));

    return response()->json([
      'message' => 'Login berhasil',
      'token' => $token,
      'user' => $user
    ]);
  }

  public function logout(Request $request)
  {
    $user = $request->user();

    Cache::forget("user_session:{$user->id}");
    $request->user()->currentAccessToken()->delete();

    return response()->json(['message' => 'Logout Berhasil']);
  }

  public function me(Request $request)
  {
      $user = $request->user();

      // Ambil dari Redis cache dulu, kalau gak ada baru dari DB
      $cached = Cache::get("user_session:{$user->id}");

      return response()->json($cached ?? $user);
  }
}