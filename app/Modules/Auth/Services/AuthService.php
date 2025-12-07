<?php

namespace App\Modules\Auth\Services;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\NewAccessToken;

class AuthService
{
    public function __construct(private readonly DatabaseManager $db)
    {
    }

    /**
     * Attempt to authenticate the user and issue an access token.
     *
     * @throws ValidationException
     */
    public function authenticate(string $email, string $password, ?string $deviceName = null): array
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        $token = $this->createToken($user, $deviceName);

        return [
            'user' => $user,
            'access_token' => $token->plainTextToken,
            'token_type' => 'Bearer',
        ];
    }

    public function register(array $payload): array
    {
        return $this->db->transaction(function () use ($payload) {
            $user = User::create([
                'name' => $payload['name'],
                'surname' => $payload['surname'],
                'email' => $payload['email'],
                'password' => Hash::make($payload['password']),
                'phone' => $payload['phone'] ?? null,
            ]);

            $token = $this->createToken($user, $payload['device_name'] ?? null);

            return [
                'user' => $user,
                'access_token' => $token->plainTextToken,
                'token_type' => 'Bearer',
            ];
        });
    }

    public function update(User $user, array $payload): User
    {
        return $this->db->transaction(function () use ($user, $payload) {
            $attributes = [];

            foreach (['name', 'surname', 'email', 'phone', 'password', 'role'] as $field) {
                if (array_key_exists($field, $payload)) {
                    $attributes[$field] = $payload[$field];
                }
            }

            if (array_key_exists('role', $attributes) && !$user->isSuperAdmin()) {
                throw new AuthorizationException('Only superadmins can change their role.');
            }

            if (array_key_exists('password', $attributes)) {
                $attributes['password'] = Hash::make($attributes['password']);
            }

            $user->fill($attributes);
            $user->save();

            return $user->refresh();
        });
    }

    public function logout(?User $user): void
    {
        if (!$user) {
            return;
        }

        $user->currentAccessToken()?->delete();
    }

    public function delete(User $user): void
    {
        $this->db->transaction(function () use ($user) {
            $user->tokens()->delete();
            $user->delete();
        });
    }

    protected function createToken(User $user, ?string $deviceName = null): NewAccessToken
    {
        $name = $deviceName ?: sprintf('api-token-%s', now()->timestamp);

        return $user->createToken($name);
    }
}
