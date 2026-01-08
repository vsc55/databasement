<?php

namespace App\Services\Backup;

class CompressorFactory
{
    public function __construct(
        private readonly ShellProcessor $shellProcessor
    ) {}

    /**
     * Create a compressor instance based on configuration.
     *
     * @throws \InvalidArgumentException If the compression method is not supported
     */
    public function make(?string $method = null, ?int $level = null): CompressorInterface
    {
        $method = $method ?? config('backup.compression', 'gzip');
        $level = $level ?? (int) config('backup.compression_level');

        return match ($method) {
            'gzip' => new GzipCompressor($this->shellProcessor, $level),
            'zstd' => new ZstdCompressor($this->shellProcessor, $level),
            default => throw new \InvalidArgumentException(
                "Unsupported compression method: {$method}. Supported methods: gzip, zstd"
            ),
        };
    }
}
