<?php

namespace App\Exception;

/**
 * Thrown when an importer for a given carrier is not found.
 */
class InvalidRoleException extends \Exception
{
    /**
     * @param string          $message  error message
     * @param int             $code     error code
     * @param \Throwable|null $previous previous exception
     */
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
