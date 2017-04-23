<?php

namespace Igsem\ApiExceptions;

use Igsem\ApiExceptions\Exceptions\ApiExceptionInterface;

/**
 * Created by PhpStorm.
 * User: juliuskoronci
 * Date: 23/04/2017
 * Time: 11:04
 */
class ApiExceptions
{
    const PRODUCTION = false;
    const DEVELOPMENT = true;

    /** @var  LoggerInterface */
    private $logger;

    /** @var  \Swift_Mailer */
    private $mailer;

    /** @var  bool */
    private $environment;

    public function __construct(LoggerInterface $logger, $environment = self::PRODUCTION, \Swift_Mailer $mailer = null)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->environment = $environment;
    }

    /**
     * Register handlers
     */
    public function register()
    {
        /** if development enable error displaying */
        ini_set('display_errors', $this->environment);
        /** report every error, notice, warning */
        error_reporting(E_ALL);
        /** Register error handler */
        set_error_handler([$this, 'handleErrors']);
        /** Register exception handler */
        set_exception_handler([$this, 'handleExceptions']);
    }

    /**
     * Custom Exception handler
     *
     * @param \Throwable $exception
     */
    public function handleExceptions(\Throwable $exception)
    {
        if (!$exception instanceof ApiExceptionInterface) {
            $this->logger->logException($exception);
            $this->sendMessage($exception->getMessage());
            $this->apiResponse(null, null, [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);
        }
        $this->apiResponse($exception->getCode(), $exception->getMessage(), $exception->getDebug());
    }

    /**
     * Custom error handler
     *
     * @param int $errorNumber
     * @param string $errorString
     * @param string $errorFile
     * @param int $errorLine
     */
    public function handleErrors(int $errorNumber, string $errorString, string $errorFile, int $errorLine)
    {
        $message = sprintf(
            '[%s] [%s] %s - %s',
            $errorNumber,
            $errorLine,
            $errorString,
            $errorFile
        );
        $this->logger->logError($message);
        $this->sendMessage($message);
    }

    /**
     * @param int $code
     * @param string $message
     * @param array $debug
     */
    private function apiResponse(int $code = 500, string $message = 'Server error, check logs for further info!', array $debug = [])
    {
        http_response_code($code);
        $response = [
            'message' => $message
        ];
        if (self::DEVELOPMENT === $this->environment) {
            $response['debug'] = $debug;
        }
        echo json_encode($response);

        exit;
    }

    /**
     * Send an email if a configured mailer is provided
     * @param string $message
     */
    private function sendMessage(string $message)
    {
        if (!$this->mailer) {
            return;
        }
        $m = new \Swift_Message('server error', $message);
        $this->mailer->send($m);
    }
}