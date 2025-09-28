<?php

declare(strict_types=1);

namespace Swifty\Exceptions;

use Exception;

/**
 * Exception thrown when database operations fail
 */
class DatabaseException extends Exception
{
    public function __construct(string $message = "Database operation failed", int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}