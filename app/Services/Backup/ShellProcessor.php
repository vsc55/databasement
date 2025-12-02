<?php

namespace App\Services\Backup;

use App\Exceptions\ShellProcessFailed;
use App\Models\BackupJob;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class ShellProcessor
{
    private ?BackupJob $logger = null;

    public function setLogger(BackupJob $logger): void
    {
        $this->logger = $logger;
    }

    public function process(string $command): string
    {
        $process = Process::fromShellCommandline($command);
        $process->setTimeout(null);

        // Mask sensitive data in command line for logging
        $sanitizedCommand = $this->sanitizeCommand($command);
        $startTime = microtime(true);
        $process->run();

        $output = $process->getOutput();
        $errorOutput = $process->getErrorOutput();
        $exitCode = $process->getExitCode();

        // Log the command and result
        if ($this->logger) {
            $combinedOutput = trim($output."\n".$errorOutput);
            $this->logger->logCommand($sanitizedCommand, $combinedOutput, $exitCode, $startTime);
        }

        if (! $process->isSuccessful()) {
            Log::error($command."\n".$errorOutput);
            throw new ShellProcessFailed($errorOutput);
        }

        return $output;
    }

    private function sanitizeCommand(string $command): string
    {
        // Mask passwords in MySQL/PostgreSQL commands
        $patterns = [
            '/--password=[^\s]+/' => '--password=***',
            '/-p[^\s]+/' => '-p***',
            '/PGPASSWORD=[^\s]+/' => 'PGPASSWORD=***',
        ];

        foreach ($patterns as $pattern => $replacement) {
            $command = preg_replace($pattern, $replacement, $command);
        }

        return $command;
    }
}
