## Response generation, Exception handling and Logging for API's

Usable in Phalcon, Lumen and any other PHP app suporting php 7+

Your code:
``` 
throw new TokenException('Token did not pass verification')
```
Your response:
```
Status: 401 Unauthorized
{"message":"Invalid Tokenn","debug":{}}
```
Automatically catches all your errors and exceptions and returns a nice API friendly message. 
It provides a list of exceptions to use in your API, these resolve to standard HTTP STATUS CODES with a 
default message.

All exceptions which do not implement Igsem\ApiExceptions\Exceptions\ApiExceptionInterface return a 500 status code and 
log the message into a custom log file. 

You can register your own exception, the only requirement is to implement ApiExceptionInterface.

You can customize every message and code plus send additional debug info for development env.

### What it does

- returns API friendly responses
- provides an API for creating custom responses
- logs all unexpected errors and exceptions
- can send emails if unexpected exception or error occurs
- provides additional debug info in development
- hides all unexpected errors and exceptions in production
- handles almost all non 200 responses which may occur in an API

### Installation

In your front controller or when bootstrapping your application just register the handler:

```
use Igsem\ApiExceptions\ApiExceptions;

$apiExceptions = new ApiExceptions($logger, ApiExceptions::DEVELOPMENT, $mySwiftmailer);
$apiExceptions->register();
```

The environment by default is set to production and no mailer is required. The library supports only swiftmailer.

The Logger is a little tricky one. As everyone implements their own logger usually and most of the time they are not 
PSR 3 compatible, therefore we decided to require our custom interface. You can pass in any Loger until it implements
 
```Igsem\ApiExceptions\LoggerInterface```

We are providing a Logger class for Phalcon by default.

This is an example of our logger creation:
```
use Igsem\ApiExceptions\Loggers\PhalconLogger;

/**
 * Initializes the logger
 */
protected function initLogger()
{
    /** @var \Phalcon\Config $config */
    $config = $this->diContainer->getShared('config');
    $path = $config->get('logger')->toArray()['path'];

    $this->diContainer->setShared('logger', new PhalconLogger($path));
}
```

It only requires a path to the log folder. There are only 3 methods available:

- logError
- logException
- logMessage

The logger will create separate files with debug info, proper formats and info required for debugging. 

You can still use your logger and just extend it with logError and logException just to let the library know how to 
handle these cases.

!If you decide to use the library, please remove the try catch block around your application. 
The library registers its own error and exception handlers.

### Custom exception example

```
<?php

namespace Igsem\ApiExceptions\Exceptions;


/**
 * Created by PhpStorm.
 * User: juliuskoronci
 * Date: 23/04/2017
 * Time: 11:54
 */
class InvalidParametersException extends \Exception implements ApiExceptionInterface
{
    /** @var array - used to pass additional info for dev env */
    private $debug;

    /**
     * TokenException constructor.
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     * @param array $debug
     */
    public function __construct(
        $message = StatusCodes::INVALID_PARAMETERS_MESSAGE,
        $code = StatusCodes::INVALID_PARAMETERS_CODE,
        \Throwable $previous = null,
        $debug = []
    )
    {
        /**
         * Set Default if null provided because of debug
         */
        $message = $message??StatusCodes::INVALID_PARAMETERS_MESSAGE;
        $code = $code??StatusCodes::INVALID_PARAMETERS_CODE;

        parent::__construct($message, $code, $previous);
        $this->debug = $debug;
    }

    /**
     * @return array
     */
    public function getDebug(): array
    {
        return $this->debug;
    }

    /**
     * @param array $debug
     */
    public function setDebug(array $debug = [])
    {
        $this->debug = $debug;
    }
}
```

In the same way, you can create your own custom exceptions. If they implement ApiExceptionInterface, they will be 
caught and the correct response will be returned.

### List of available exceptions

- AccessDeniedException
- BadRequestException
- InvalidCredentialException
- InvalidParametersException
- NotFoundException
- TokenException
- UnAuthorizedException