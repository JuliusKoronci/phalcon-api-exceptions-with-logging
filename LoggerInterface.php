<?php

namespace Igsem\ApiExceptions;
/**
 * Interface LoggerInterface
 *
 * Since every framework has their own logger and not really following PSR 3 we
 * enforce wrapping the logger with our custom simplified interface.
 *
 */
interface LoggerInterface
{
    /**
     * @param string $errorMessage
     */
    public function logError(string $errorMessage);

    /**
     * @param \Throwable $exception
     */
    public function logException(\Throwable $exception);
}