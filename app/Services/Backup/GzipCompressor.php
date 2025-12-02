<?php

namespace App\Services\Backup;

class GzipCompressor
{
    public function __construct(
        private ShellProcessor $shellProcessor
    ) {}

    public function compress(string $inputPath): string
    {
        $this->shellProcessor->process($this->getCompressCommandLine($inputPath));

        return $this->getCompressedPath($inputPath);
    }

    public function decompress(string $compressedFile): string
    {
        $this->shellProcessor->process($this->getDecompressCommandLine($compressedFile));

        // gzip -d removes .gz suffix from the file
        $decompressedFile = $this->getDecompressedPath($compressedFile);

        if (! file_exists($decompressedFile)) {
            throw new \RuntimeException('Decompression failed: output file not found');
        }

        return $decompressedFile;
    }

    public function getCompressCommandLine(string $inputPath): string
    {
        return 'gzip '.escapeshellarg($inputPath);
    }

    public function getDecompressCommandLine(string $outputPath): string
    {
        return 'gzip -d '.escapeshellarg($outputPath);
    }

    public function getCompressedPath(string $inputPath): string
    {
        return $inputPath.'.gz';
    }

    public function getDecompressedPath(string $inputPath): string
    {
        return preg_replace('/\.gz$/', '', $inputPath);
    }
}
