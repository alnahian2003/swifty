<?php

declare(strict_types=1);

namespace Swifty\Controllers;

use Swifty\Exceptions\ValidationException;
use Swifty\Exceptions\DatabaseException;

/**
 * Base controller with common functionality
 */
abstract class BaseController
{
    /**
     * Set CORS headers
     */
    protected function setCorsHeaders(): void
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        header("Content-Type: application/json; charset=UTF-8");
    }

    /**
     * Get JSON input data
     */
    protected function getJsonInput(): ?array
    {
        $input = file_get_contents("php://input");
        
        if (empty($input)) {
            return null;
        }

        $data = json_decode($input, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->sendError("Invalid JSON data", 400);
            return null;
        }

        return $data;
    }

    /**
     * Send JSON success response
     */
    protected function sendSuccess(array $data = [], int $statusCode = 200): void
    {
        http_response_code($statusCode);
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Send JSON error response
     */
    protected function sendError(string $message, int $statusCode = 400, array $errors = []): void
    {
        http_response_code($statusCode);
        
        $response = ['error' => $message];
        
        if (!empty($errors)) {
            $response['validation_errors'] = $errors;
        }

        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Handle exceptions and send appropriate response
     */
    protected function handleException(\Exception $e): void
    {
        if ($e instanceof ValidationException) {
            $this->sendError($e->getMessage(), 422, $e->getErrors());
        } elseif ($e instanceof DatabaseException) {
            error_log("Database error: " . $e->getMessage());
            $this->sendError("Internal server error", 500);
        } else {
            error_log("Unexpected error: " . $e->getMessage());
            $this->sendError("Internal server error", 500);
        }
    }

    /**
     * Validate required fields in request data
     */
    protected function validateRequiredFields(array $data, array $requiredFields): void
    {
        $missing = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            throw new ValidationException(
                array_combine($missing, array_fill(0, count($missing), 'This field is required')),
                'Missing required fields'
            );
        }
    }

    /**
     * Get ID from request (either from URL parameter or JSON data)
     */
    protected function getId(): ?int
    {
        // Try to get from URL parameter first
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            return (int) $_GET['id'];
        }

        // Try to get from JSON data
        $data = $this->getJsonInput();
        if ($data && isset($data['id']) && is_numeric($data['id'])) {
            return (int) $data['id'];
        }

        return null;
    }
}