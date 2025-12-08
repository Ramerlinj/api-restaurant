<?php

namespace App\Modules\Menu\Services;

use App\Modules\Menu\Models\Ingredient;
use App\Services\CloudinaryService;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class IngredientService
{
    public function __construct(
        private readonly DatabaseManager $db,
        private readonly CloudinaryService $cloudinary,
    ) {
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function store(array $payload): Ingredient
    {
        return $this->db->transaction(function () use ($payload) {
            /** @var UploadedFile|null $image */
            $image = Arr::pull($payload, 'image');

            $imageData = null;

            if ($image instanceof UploadedFile) {
                $imageData = $this->formatUploadResponse(
                    $this->cloudinary->uploadImage($image, [
                        'folder' => config('services.cloudinary.upload_folder', 'api-restaurant') . '/ingredients',
                    ])
                );
            }

            $payload['image_url'] = $imageData;
            $payload['available'] = $payload['available'] ?? false;

            return Ingredient::create($payload);
        });
    }

    public function list(): Collection
    {
        return Ingredient::query()
            ->orderBy('name')
            ->get();
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function update(Ingredient $ingredient, array $payload): Ingredient
    {
        return $this->db->transaction(function () use ($ingredient, $payload) {
            /** @var UploadedFile|null $image */
            $image = Arr::pull($payload, 'image');

            if ($image instanceof UploadedFile) {
                $payload['image_url'] = $this->formatUploadResponse(
                    $this->cloudinary->uploadImage($image, [
                        'folder' => config('services.cloudinary.upload_folder', 'api-restaurant') . '/ingredients',
                        'public_id' => $ingredient->image_url['public_id'] ?? null,
                        'overwrite' => true,
                        'invalidate' => true,
                    ])
                );
            }

            $ingredient->fill($payload);
            $ingredient->save();

            return $ingredient->refresh();
        });
    }

    /**
     * @param  array<string, mixed>  $uploadResponse
     * @return array<string, mixed>
     */
    private function formatUploadResponse(array $uploadResponse): array
    {
        return [
            'public_id' => $uploadResponse['public_id'] ?? null,
            'secure_url' => $uploadResponse['secure_url'] ?? null,
            'url' => $uploadResponse['url'] ?? null,
            'format' => $uploadResponse['format'] ?? null,
            'resource_type' => $uploadResponse['resource_type'] ?? null,
            'bytes' => $uploadResponse['bytes'] ?? null,
            'width' => $uploadResponse['width'] ?? null,
            'height' => $uploadResponse['height'] ?? null,
        ];
    }
}
