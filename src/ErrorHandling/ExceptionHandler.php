<?php
/**
 * @copyright (c) 2016 Quicken Loans Inc.
 *
 * For full license information, please view the LICENSE distributed with this source code.
 */

namespace QL\Panthor\ErrorHandling;

use ErrorException;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class ExceptionHandler implements ExceptionHandlerInterface
{
    use SlimRenderingTrait;

    /**
     * @var ContentHandlerInterface
     */
    private $handler;

    /**
     * @var ServerRequestInterface
     */
    private $defaultRequest;

    /**
     * @var ResponseInterface
     */
    private $defaultResponse;

    /**
     * @param ContentHandlerInterface $handler
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    public function __construct(ContentHandlerInterface $handler, ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->handler = $handler;

        $this->defaultRequest = $request;
        $this->defaultResponse = $response;
    }

    /**
     * Handle a throwable, and return whether it was handled and the remaining stack should be aborted.
     *
     * @param Exception|Throwable $throwable
     *
     * @return bool
     */
    public function handle($throwable)
    {
        if ($throwable instanceof Exception) {
            $response = $this->handler->handleException($this->defaultRequest, $this->defaultResponse, $throwable);
        } elseif ($throwable instanceof Throwable) {
            $response = $this->handler->handleThrowable($this->defaultRequest, $this->defaultResponse, $throwable);
        } else {
            return false;
        }

        if ($response instanceof ResponseInterface) {
            $this->renderResponse($response);
            return true;
        } else {
            return false;
        }
    }
}
