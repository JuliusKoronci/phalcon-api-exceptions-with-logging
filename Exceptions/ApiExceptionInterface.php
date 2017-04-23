<?php
namespace Igsem\ApiExceptions\Exceptions;
/**
 * Interface ApiExceptionInterface
 *
 * Used to distinguish between exceptions. We want to send response messages only if valid exception is thrown
 */
interface ApiExceptionInterface
{
    public function getDebug(): array;
    public function setDebug(array $debug = []);
}