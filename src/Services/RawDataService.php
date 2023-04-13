<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Interfaces\ServiceInterface;

class RawDataService implements ServiceInterface
{
    public function __construct(private readonly string $dataPath)
    {
    }

    public function fromFile(string $fileName, int|string $key = null): array
    {
        $path = $this->dataPath . "$fileName.json";
        $path = str_replace('//', '/', $path);
        $data = json_decode(file_get_contents($path), true);

        if ($key) {
            return $data[$key];
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function healthCheck(): bool
    {
        // TODO: Implement healthCheck() method.
        return false;
    }
}
