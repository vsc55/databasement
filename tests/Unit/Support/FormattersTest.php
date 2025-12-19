<?php

use App\Support\Formatters;

test('humanDuration returns null for null input', function () {
    expect(Formatters::humanDuration(null))->toBeNull();
});

test('humanDuration formats milliseconds under 1 second', function () {
    expect(Formatters::humanDuration(0))->toBe('0ms')
        ->and(Formatters::humanDuration(1))->toBe('1ms')
        ->and(Formatters::humanDuration(500))->toBe('500ms')
        ->and(Formatters::humanDuration(999))->toBe('999ms');
});

test('humanDuration formats seconds under 1 minute', function () {
    expect(Formatters::humanDuration(1000))->toBe('1s')
        ->and(Formatters::humanDuration(1500))->toBe('1.5s')
        ->and(Formatters::humanDuration(30000))->toBe('30s')
        ->and(Formatters::humanDuration(59000))->toBe('59s');
});

test('humanDuration formats minutes and seconds', function () {
    expect(Formatters::humanDuration(60000))->toBe('1m 0s')
        ->and(Formatters::humanDuration(90000))->toBe('1m 30s')
        ->and(Formatters::humanDuration(125000))->toBe('2m 5s')
        ->and(Formatters::humanDuration(3661000))->toBe('61m 1s');
});

test('humanFileSize returns 0 B for null or zero input', function () {
    expect(Formatters::humanFileSize(null))->toBe('0 B')
        ->and(Formatters::humanFileSize(0))->toBe('0 B');
});

test('humanFileSize formats bytes', function () {
    expect(Formatters::humanFileSize(1))->toBe('1 B')
        ->and(Formatters::humanFileSize(512))->toBe('512 B')
        ->and(Formatters::humanFileSize(1024))->toBe('1024 B')
        ->and(Formatters::humanFileSize(1025))->toBe('1 KB');
});

test('humanFileSize formats kilobytes', function () {
    expect(Formatters::humanFileSize(1536))->toBe('1.5 KB')
        ->and(Formatters::humanFileSize(1048577))->toBe('1 MB');
});

test('humanFileSize formats megabytes and above', function () {
    expect(Formatters::humanFileSize(1572864))->toBe('1.5 MB')
        ->and(Formatters::humanFileSize(1073741825))->toBe('1 GB')
        ->and(Formatters::humanFileSize(1099511627777))->toBe('1 TB');
});

test('humanDate returns null for null input', function () {
    expect(Formatters::humanDate(null))->toBeNull();
});

test('humanDate formats Carbon instances', function () {
    $date = \Carbon\Carbon::create(2025, 12, 19, 16, 44, 0);
    expect(Formatters::humanDate($date))->toBe('Dec 19, 2025, 16:44');
});

test('humanDate formats DateTime instances', function () {
    $date = new \DateTime('2025-12-19 16:44:00');
    expect(Formatters::humanDate($date))->toBe('Dec 19, 2025, 16:44');
});

test('humanDate formats string dates', function () {
    expect(Formatters::humanDate('2025-12-19 16:44:00'))->toBe('Dec 19, 2025, 16:44')
        ->and(Formatters::humanDate('2025-01-05 09:05:00'))->toBe('Jan 5, 2025, 09:05');
});

test('humanDate handles single digit days correctly', function () {
    $date = \Carbon\Carbon::create(2025, 1, 5, 9, 5, 0);
    expect(Formatters::humanDate($date))->toBe('Jan 5, 2025, 09:05');
});
