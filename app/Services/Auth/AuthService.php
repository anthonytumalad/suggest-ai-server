<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final readonly class AuthService 
{
    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        event(new Registered($user));

        $token = $user->createToken('api');

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function login(string $identity, string $password): array
    {
        $user = User::where('email', $identity)
            ->orWhere('username', $identity)
            ->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $user->tokens()->delete();

        $token = $user->createToken('api');

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logout(Request $request): void
    {
        $request->user()?->currentAccessToken()?->delete();
    }

    public function logout_all(Request $request): void
    {
        $request->user()?->tokens()->delete();
    }
}