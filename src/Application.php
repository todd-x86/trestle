<?php

namespace Trestle;

final class Application
{
    private $middleware = [];
    private $exceptionHandler = null;

    public function enableErrors() : void
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(-1);
    }

    public function addMiddleware(MiddlewareInterface $middleware) : void
    {
        $this->middleware[] = $middleware;
    }

    public function setExceptionHandler(ExceptionHandlerInterface $handler) : void
    {
        $this->exceptionHandler = $handler;
    }

    private function sendResponse(Response $response) : void
    {
        // Send headers
        header($_SERVER['SERVER_PROTOCOL'].' '.$response->status);
        header('Content-Type: '.$response->contentType);
        if ($response->filename !== null)
        {
            header(sprintf('Content-Disposition: attachment; filename="%s"', $response->filename));
        }
        foreach ($response->getHeaders() as $header)
        {
            header($header[0].': '.$header[1]);
        }

        // Send content
        print $response->getContent();
    }

    private function handleException(\Exception $ex) : void
    {
        $response = new Response();
        if ($this->exceptionHandler !== null)
        {
            $this->exceptionHandler->handleException($ex, $response);
        }
        else
        {
            $response->write("<h1>Exception Encountered</h1>");
            $response->write("<pre>".$ex."</pre>");
            $response->status = 500;
        }
        $this->sendResponse($response);
    }

    public function run(Request $request) : void
    {
        $app = $this;
        register_shutdown_function(function() use ($app)
        {
            // Check for error when script shuts down
            $error = \error_get_last();
            if ($error !== null)
            {
                $this->handleException(new PHPException($error['type'], $error['file'], $error['line'], $error['message']));
            }
        });

        $response = new Response();

        // Pass through each middleware
        try
        {
            foreach ($this->middleware as $mw)
            {
                if (!$mw->handle($request, $response))
                {
                    break;
                }
            }

            // Send response back
            $this->sendResponse($response);
        }
        catch (\Exception $ex)
        {
            $this->handleException($ex);
        }
    }
}
