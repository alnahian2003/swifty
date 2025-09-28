<?php

declare(strict_types=1);

namespace Swifty\Exceptions;

use Exception;

/**
 * Exception thrown when validation fails
 */
class ValidationException extends Exception
{
    private array $errors;

    public function __construct(array $errors = [], string $message = "Validation failed", int $code = 0, ?Exception $previous = null)
    {
        $this->errors = $errors;
        parent::__construct($message, $code, $previous);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}