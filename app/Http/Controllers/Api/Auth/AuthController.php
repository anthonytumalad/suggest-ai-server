<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {
    }

    public function register(Request $request): JsonResponse
    {
        $validated = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:30', 'unique:users', 'regex:/^[a-zA-Z0-9._-]+$/'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ])->validate();

        ['user' => $user, 'token' => $token] = $this->authService->register($validated);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token->plainTextToken,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = Validator::make($request->all(), [
            'identity' => ['required', 'string'],
            'password' => ['required', 'string'],
        ])->validate();

        ['user' => $user, 'token' => $token] = $this->authService->login(
            $validated['identity'],
            $validated['password']
        );

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token->plainTextToken,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request);

        return response()->json(['message' => 'Logged out']);
    }

    public function logout_all(Request $request): JsonResponse
    {
        $this->authService->logout_all($request);

        return response()->json(['message' => 'Logged out from all devices']);
    }

    public function user(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }
}
