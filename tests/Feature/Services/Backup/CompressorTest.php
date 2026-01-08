<?php

use App\Services\Backup\CompressorFactory;
use App\Services\Backup\GzipCompressor;
use App\Services\Backup\ZstdCompressor;
use Tests\Support\TestShellProcessor;

beforeEach(function () {
    $this->shellProcessor = new TestShellProcessor;
});

test('compressor generates correct compress command', function (string $method, int $level, string $expectedCommand) {
    $factory = new CompressorFactory($this->shellProcessor);
    $compressor = $factory->make($method, $level);

    expect($compressor->getCompressCommandLine('/path/to/dump.sql'))->toBe($expectedCommand);
})->with([
    'gzip default' => ['gzip', 6, "gzip -6 '/path/to/dump.sql'"],
    'gzip level 1' => ['gzip', 1, "gzip -1 '/path/to/dump.sql'"],
    'zstd default' => ['zstd', 6, "zstd -6 --rm '/path/to/dump.sql'"],
]);

test('compressor generates correct decompress command', function (string $method, string $expectedCommand) {
    $factory = new CompressorFactory($this->shellProcessor);
    $compressor = $factory->make($method, 6);

    expect($compressor->getDecompressCommandLine('/path/to/dump.sql.ext'))->toBe($expectedCommand);
})->with([
    'gzip' => ['gzip', "gzip -d '/path/to/dump.sql.ext'"],
    'zstd' => ['zstd', "zstd -d --rm '/path/to/dump.sql.ext'"],
]);

test('compressor returns correct compressed path and extension', function (string $method, string $expectedExt) {
    $factory = new CompressorFactory($this->shellProcessor);
    $compressor = $factory->make($method, 6);

    expect($compressor->getCompressedPath('/path/to/dump.sql'))->toBe("/path/to/dump.sql.{$expectedExt}")
        ->and($compressor->getExtension())->toBe($expectedExt);
})->with([
    'gzip' => ['gzip', 'gz'],
    'zstd' => ['zstd', 'zst'],
]);

test('factory creates correct compressor from config', function (string $method, string $expectedClass) {
    config([
        'backup.compression' => $method,
        'backup.compression_level' => 6,
    ]);

    $factory = new CompressorFactory($this->shellProcessor);
    $compressor = $factory->make();

    expect($compressor)->toBeInstanceOf($expectedClass);
})->with([
    'gzip' => ['gzip', GzipCompressor::class],
    'zstd' => ['zstd', ZstdCompressor::class],
]);

test('factory throws exception for unsupported compression method', function () {
    $factory = new CompressorFactory($this->shellProcessor);

    expect(fn () => $factory->make('lz4', 6))
        ->toThrow(\InvalidArgumentException::class, 'Unsupported compression method: lz4');
});

test('compression level is clamped to valid range', function (string $method, int $inputLevel, int $expectedLevel) {
    $factory = new CompressorFactory($this->shellProcessor);
    $compressor = $factory->make($method, $inputLevel);

    $command = $compressor->getCompressCommandLine('/path/to/dump.sql');
    expect($command)->toContain("-{$expectedLevel}");
})->with([
    'gzip level 0 clamped to 1' => ['gzip', 0, 1],
    'gzip level 10 clamped to 9' => ['gzip', 10, 9],
    'zstd level 0 clamped to 1' => ['zstd', 0, 1],
    'zstd level 20 clamped to 19' => ['zstd', 20, 19],
]);

test('compressor executes compress and returns path', function (string $method) {
    $testFile = '/tmp/test_dump.sql';
    file_put_contents($testFile, 'test data');

    $factory = new CompressorFactory($this->shellProcessor);
    $compressor = $factory->make($method);

    $compressedPath = $compressor->compress($testFile);

    expect($compressedPath)->toEndWith('.'.$compressor->getExtension())
        ->and(file_exists($compressedPath))->toBeTrue();

    unlink($compressedPath);
})->with(['gzip', 'zstd']);
