<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;
use RuntimeException;

class CloudinaryService
{
    protected Cloudinary $cloudinary;

    public function __construct()
    {
        $cloudinaryUrl = config('services.cloudinary.url');

        if (!$cloudinaryUrl) {
            throw new RuntimeException('Cloudinary URL is not configured.');
        }

        $this->cloudinary = new Cloudinary($cloudinaryUrl);
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function uploadImage(UploadedFile $file, array $options = []): array
    {
        $folder = $options['folder'] ?? config('services.cloudinary.upload_folder');

        $uploadOptions = array_merge([
            'folder' => $folder,
            'resource_type' => 'image',
            'overwrite' => true,
        ], $options);

        return $this->cloudinary->uploadApi()
            ->upload($file->getRealPath(), $uploadOptions)
            ->getArrayCopy();
    }
}
