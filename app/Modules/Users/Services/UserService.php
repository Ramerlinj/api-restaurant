<?php

namespace App\Modules\Users\Services;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(private readonly DatabaseManager $db)
    {
    }

    public function update(User $actor, User $target, array $payload): User
    {
        return $this->db->transaction(function () use ($actor, $target, $payload) {
            if (!$actor->hasAdminPrivileges()) {
                throw new AuthorizationException('Only admins can update users.');
            }

            if (($target->isAdmin() || $target->isSuperAdmin()) && !$actor->isSuperAdmin()) {
                throw new AuthorizationException('Only superadmin can update admin users.');
            }

            if (array_key_exists('role', $payload)) {
                $this->ensureCanModifyRole($actor, $target, $payload['role']);
            }

            $attributes = Arr::only($payload, ['name', 'surname', 'email', 'phone', 'password', 'role']);

            if (array_key_exists('password', $attributes)) {
                $attributes['password'] = Hash::make($attributes['password']);
            }

            $target->fill($attributes);
            $target->save();

            return $target->refresh();
        });
    }

    public function delete(User $actor, User $target): void
    {
        if (!$actor->hasAdminPrivileges()) {
            throw new AuthorizationException('Only admins can delete users.');
        }

        if (($target->isAdmin() || $target->isSuperAdmin()) && !$actor->isSuperAdmin()) {
            throw new AuthorizationException('Only superadmin can delete admin users.');
        }

        $this->db->transaction(function () use ($target) {
            $target->tokens()->delete();
            $target->delete();
        });
    }

    protected function ensureCanModifyRole(User $actor, User $target, string $newRole): void
    {
        if (!$actor->hasAdminPrivileges()) {
            throw new AuthorizationException('Only admins can modify roles.');
        }

        $requiresSuperAdmin = $target->isAdmin()
            || $target->isSuperAdmin()
            || $newRole === User::ROLE_SUPERADMIN;

        if ($requiresSuperAdmin && !$actor->isSuperAdmin()) {
            throw new AuthorizationException('Only superadmin can modify admin roles.');
        }
    }
}
