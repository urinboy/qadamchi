<?php
namespace Qadamchi\Support;

/**
 * Mini logger (PSR-3 ga mos imzo, Composer'siz).
 * B versiyasida monolog/monolog ga almashtiriladi — usullar nomi mos.
 */
class Logger
{
    protected string $path;

    public function __construct(?string $path = null)
    {
        $this->path = $path ?? __DIR__ . '/../../storage/logs/qadamchi.log';
    }

    protected function write(string $level, string $message): void
    {
        $dir = dirname($this->path);
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $date = date('Y-m-d H:i:s');
        @file_put_contents($this->path, "[$date] $level: $message\n", FILE_APPEND);
    }

    public function emergency($message, array $context = []): void { $this->write('EMERGENCY', $this->interpolate($message, $context)); }
    public function alert($message, array $context = []): void     { $this->write('ALERT', $this->interpolate($message, $context)); }
    public function critical($message, array $context = []): void  { $this->write('CRITICAL', $this->interpolate($message, $context)); }
    public function error($message, array $context = []): void     { $this->write('ERROR', $this->interpolate($message, $context)); }
    public function warning($message, array $context = []): void   { $this->write('WARNING', $this->interpolate($message, $context)); }
    public function notice($message, array $context = []): void    { $this->write('NOTICE', $this->interpolate($message, $context)); }
    public function info($message, array $context = []): void      { $this->write('INFO', $this->interpolate($message, $context)); }
    public function debug($message, array $context = []): void     { $this->write('DEBUG', $this->interpolate($message, $context)); }

    /** Eski API bilan moslik: Logger::log($msg) */
    public static function log($message): void
    {
        (new static())->write('INFO', (string) $message);
    }

    protected function interpolate($message, array $context): string
    {
        $replace = [];
        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = (string) $val;
        }
        return strtr((string) $message, $replace);
    }
}