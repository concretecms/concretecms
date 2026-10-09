<?php

declare(strict_types=1);

namespace Concrete\Core\Error;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Represents an error that can be safely shown to users, answered with an HTTP status code of its
 * own instead of the 500 of a failure nobody expected.
 */
class UserMessageHttpException extends UserMessageException implements HttpExceptionInterface
{
    /**
     * @var int
     */
    private $statusCode;

    public function __construct(string $message, int $statusCode, ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->statusCode = $statusCode;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface::getStatusCode()
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface::getHeaders()
     */
    public function getHeaders(): array
    {
        return [];
    }
}
