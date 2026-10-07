<?php

// Mencatat kejadian penting/error ke storage/logs/app.log.
// Detail teknis hanya masuk ke log, TIDAK ditampilkan ke pengguna.
// Jangan pernah menulis password atau data sensitif ke log.
class Logger
{
    public static function error(string $context, ?Throwable $e = null): void
    {
        $line = date('Y-m-d H:i:s') . ' - [ERROR] ' . $context;

        if ($e !== null) {
            $line .= ' | ' . get_class($e) . ': ' . $e->getMessage()
                  . ' (' . basename($e->getFile()) . ':' . $e->getLine() . ')';
        }

        self::tulis($line);
    }

    private static function tulis(string $line): void
    {
        $dir = __DIR__ . '/../storage/logs';

        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        // Hapus baris baru agar satu kejadian = satu baris log
        $line = str_replace(["\r", "\n"], ' ', $line);

        // Jika log gagal ditulis, jangan sampai membuat aplikasi ikut error
        @error_log($line . PHP_EOL, 3, $dir . '/app.log');
    }
}
