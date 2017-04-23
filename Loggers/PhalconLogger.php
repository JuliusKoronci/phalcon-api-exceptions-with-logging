<?php
/**
 * Created by PhpStorm.
 * User: juliuskoronci
 * Date: 23/04/2017
 * Time: 11:38
 */

namespace Igsem\ApiExceptions\Loggers;

use Igsem\ApiExceptions\LoggerInterface;
use Phalcon\Logger\Adapter\File as FileAdapter;
use Phalcon\Logger;

/**
 * Class PhalconLogger
 * @package Application\Igsem\ApiExceptions\Loggers
 */
class PhalconLogger implements LoggerInterface
{
    /** @var string */
    private $logDir;

    /**
     * PhalconLogger constructor.
     * @param string $logDir
     */
    public function __construct(string $logDir)
    {
        $this->logDir = $logDir;
    }

    /**
     * @param string $errorMessage
     */
    public function logError(string $errorMessage)
    {
        $this->logMessage($errorMessage, 'error', Logger::CRITICAL);
    }

    /**
     * @param \Throwable $exception
     */
    public function logException(\Throwable $exception)
    {
        $message = sprintf(
            '[%s] [%s] [%s] %s - %s',
            $exception->getLine(),
            $exception->getCode(),
            $exception->getMessage(),
            $exception->getTraceAsString(),
            $exception->getFile()
        );

        $this->logMessage($message, 'exception', Logger::ERROR);
    }

    /**
     * @param string $message
     * @param string $filename
     * @param int $logLevel
     */
    public function logMessage(string $message, string $filename = 'debug', int $logLevel = Logger::DEBUG)
    {
        $logger = new FileAdapter($this->logDir . '/' . date('Y-m-d-H-i') . '-' . $filename . '.log');

        $logger->log($message, $logLevel);
    }
}