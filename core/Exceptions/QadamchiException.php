<?php
namespace Qadamchi\Exceptions;

use Exception;
use Qadamchi\Support\Logger;

/**
 * Framework exception'lari bazasi — report() orqali log yozadi.
 */
abstract class QadamchiException extends Exception
{
    public function report(?Logger $logger = null): void
    {
        if ($logger) {
            $logger->error('{class}: {message}', [
                'class'   => get_class($this),
                'message' => $this->getMessage(),
            ]);
        }
    }
}