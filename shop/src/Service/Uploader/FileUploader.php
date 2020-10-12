<?php

declare(strict_types=1);

namespace App\Service\Uploader;

use League\Flysystem\FilesystemInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private FilesystemInterface $storage;
    private string $basUrl;

    public function __construct(FilesystemInterface $storage, string $basUrl)
    {
        $this->storage = $storage;
        $this->basUrl = $basUrl;
    }

    public function upload(UploadedFile $file): File
    {
        $path = $this->path();
        $name = $this->name() . '.' . $file->getClientOriginalExtension();

        return $this->writeFile($file, $path, $name);
    }

    public function uploadFile(\SplFileInfo $file): File
    {
        $path = $this->path();
        $name = $this->name() . '.' . $file->getExtension();

        return $this->writeFile($file, $path, $name);
    }

    public function generateUrl(string $path): string
    {
        return $this->basUrl . '/' . $path;
    }

    public function remove(string $path, string $name): void
    {
        $this->storage->delete($path . '/' . $name);
    }

    private function writeFile(\SplFileInfo $file, $path, $name)
    {
        $this->storage->createDir($path);
        $stream = fopen($file->getRealPath(), 'rb+');
        $this->storage->writeStream($path . '/' . $name, $stream);
        fclose($stream);

        return new File($path, $name, $file->getSize());
    }

    private function path(): string
    {
        return date('Y/m/d');
    }

    private function name(): string
    {
        return Uuid::uuid4()->toString();
    }
}
