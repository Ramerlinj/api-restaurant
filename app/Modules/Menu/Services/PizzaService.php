<?php

namespace App\Modules\Menu\Services;

use App\Modules\Menu\Models\Pizza;
use App\Services\CloudinaryService;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class PizzaService
{
    public function __construct(
        private readonly DatabaseManager $db,
        private readonly CloudinaryService $cloudinary,
    ) {
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function store(array $payload): Pizza
    {
        return $this->db->transaction(function () use ($payload) {
            /** @var UploadedFile|null $image */
            $image = Arr::pull($payload, 'image');

            $payload['image_url'] = $image instanceof UploadedFile
                ? $this->formatUploadResponse($this->uploadImage($image))
                : null;

            $payload['is_recommended'] = $payload['is_recommended'] ?? false;

            return Pizza::create($payload);
        });
    }

    public function list(): Collection
    {
        return Pizza::query()
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function update(Pizza $pizza, array $payload): Pizza
    {
        return $this->db->transaction(function () use ($pizza, $payload) {
            /** @var UploadedFile|null $image */
            $image = Arr::pull($payload, 'image');

            if ($image instanceof UploadedFile) {
                $payload['image_url'] = $this->formatUploadResponse(
                    $this->uploadImage($image, [
                        'public_id' => $pizza->image_url['public_id'] ?? null,
                        'overwrite' => true,
                        'invalidate' => true,
                    ])
                );
            }

            $pizza->fill($payload);
            $pizza->save();

            return $pizza->refresh();
        });
    }

    /**
     * @param  array<string, mixed>  $options
     */
    private function uploadImage(UploadedFile $image, array $options = []): array
    {
        $folder = config('services.cloudinary.upload_folder', 'api-restaurant') . '/pizzas';

        return $this->cloudinary->uploadImage($image, array_merge([
            'folder' => $folder,
        ], $options));
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
