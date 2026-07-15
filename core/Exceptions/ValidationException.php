<?php
namespace Qadamchi\Exceptions;

use Qadamchi\Exceptions\QadamchiException;

/**
 * Validatsiya xatoligi — xatolar to'plami bilan.
 * Validator/FormRequest xato bo'lganda throw qiladi.
 */
class ValidationException extends QadamchiException
{
    protected array $errors;

    public function __construct(array $errors, string $message = 'Validatsiya xatoligi')
    {
        $this->errors = $errors;
        parent::__construct($message);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}