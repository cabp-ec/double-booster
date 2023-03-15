<?php

declare(strict_types=1);

namespace App\Services;

class FileStorageService extends BaseService
{
    private string $basePath;

    public function __construct(
        protected array $resourcePath,
        protected array $config = []
    )
    {
        parent::__construct($config);

        $keys = array_keys($resourcePath);
        $this->basePath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . $keys[0] . DIRECTORY_SEPARATOR;
    }

    public function getLines(string $relPath, bool $useFullPath = false): array
    {
        $path = $useFullPath ? $relPath : ($this->basePath . $relPath);

        if (!file_exists($path)) {
            return [];
        }

        return file($path);
    }

    public function countFilesInDir(string $relPath): int
    {
        $path = $this->basePath . $relPath;
        $files = glob($path . '*');

        return $files ? count($files) : 0;
    }

    public function writeFile(string $relPath, string $contents): int
    {
        $path = $this->basePath . $relPath;
        return file_put_contents($path, $contents);
    }

    public function fileExists(string $relPath): bool
    {
        $path = $this->basePath . $relPath;
        return file_exists($path);
    }

    public function filePathLookUp(string $relPath, string $fileNamePattern): false|string
    {
        $path = $this->basePath . $relPath;
        $files = glob($path . $fileNamePattern);

        return $files[0] ?? false;
    }
}
