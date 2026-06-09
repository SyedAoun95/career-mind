<?php

namespace App\Services;

/**
 * Lightweight file logger for ML-service integration failures.
 *
 * Writes to storage/logs/ml_errors.log with ISO-8601 timestamps.
 * Auto-creates the log directory if it does not exist.
 */
class MlLogger
{
    private static ?string $logPath = null;

    private static function path(): string
    {
        if (self::$logPath === null) {
            $dir = __DIR__ . '/../../storage/logs';
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }
            self::$logPath = $dir . '/ml_errors.log';
        }
        return self::$logPath;
    }

    /**
     * Append a single log line.
     *
     * @param string $level   INFO | WARN | ERROR
     * @param string $message Human-readable description
     * @param array  $context Optional key-value context (endpoint, curl error, etc.)
     */
    public static function log(string $level, string $message, array $context = []): void
    {
        $timestamp = date('c'); // ISO-8601
        $contextStr = $context ? ' ' . json_encode($context, JSON_UNESCAPED_SLASHES) : '';
        $line = sprintf("[%s] %s: %s%s\n", $timestamp, strtoupper($level), $message, $contextStr);

        @file_put_contents(self::path(), $line, FILE_APPEND | LOCK_EX);
    }

    public static function error(string $message, array $context = []): void
    {
        self::log('ERROR', $message, $context);
    }

    public static function warn(string $message, array $context = []): void
    {
        self::log('WARN', $message, $context);
    }

    public static function info(string $message, array $context = []): void
    {
        self::log('INFO', $message, $context);
    }
}
