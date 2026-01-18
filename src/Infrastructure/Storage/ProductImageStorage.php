<?php

namespace App\Infrastructure\Storage;
final readonly class ProductImageStorage
{
    public function __construct(private string $uploadDir) {}

    public function delete(?string $filename): void
    {
        if (!$filename) return;

        $path = rtrim($this->uploadDir, '/') . '/' . $filename;

        if (is_file($path)) {
            unlink($path);
        }
    }
}
