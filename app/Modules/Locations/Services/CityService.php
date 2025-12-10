<?php

namespace App\Modules\Locations\Services;

use App\Models\User;
use App\Modules\Locations\Models\City;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Collection;

class CityService
{
    public function __construct(private readonly DatabaseManager $db)
    {
    }

    public function list(): Collection
    {
        return City::query()
            ->orderBy('name')
            ->get();
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function store(User $actor, array $payload): City
    {
        $this->ensureAdmin($actor);

        return $this->db->transaction(static fn() => City::create($payload));
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function update(User $actor, City $city, array $payload): City
    {
        $this->ensureAdmin($actor);

        return $this->db->transaction(static function () use ($city, $payload) {
            $city->fill($payload);
            $city->save();

            return $city->refresh();
        });
    }

    public function delete(User $actor, City $city): void
    {
        $this->ensureAdmin($actor);

        $this->db->transaction(static fn() => $city->delete());
    }

    private function ensureAdmin(?User $actor): void
    {
        if (!($actor?->hasAdminPrivileges())) {
            throw new AuthorizationException('Only admins can manage cities.');
        }
    }
}
