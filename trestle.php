<?php

namespace Trestle;

function enableErrors() : void
{
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(-1);
}

class RequestException extends \Exception
{
    public $arg;

    public Function __construct(string $msg, string $arg)
    {
        parent::__construct($msg);
        $this->arg = $arg;
    }
}

final class Request
{
    private $method;
    private $uri;
    private $getData;
    private $postData;

    public function __construct(string $method, string $uri, array $getData, array $postData)
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->getData = $getData;
        $this->postData = $postData;
    }

    public static function create(?string $uri = null) : Request
    {
        if ($uri === null)
        {
            if (isset($_SERVER['PATH_INFO']))
            {
                $uri = $_SERVER['PATH_INFO'];
            }
            else
            {
                $uri = "/";
            }
        }
        return new Request($_SERVER['REQUEST_METHOD'], $uri, $_GET, $_POST);
    }

    public function getQueryParam(string $name) : ?string
    {
        if (isset($this->getData[$name]))
        {
            return $this->getData[$name];
        }
        else
        {
            throw new RequestException("Query parameter does not exist", $name);
        }
    }

    public function getPostField(string $name) : ?string
    {
        if (isset($this->postData[$name]))
        {
            return $this->postData[$name];
        }
        else
        {
            throw new RequestException("POST form data field does not exist", $name);
        }
    }

    public function getUri() : string
    {
        return $this->uri;
    }

    public function getRequestType() : string
    {
        return $this->method;
    }

    public function getQueryParams() : array
    {
        return $this->getData;
    }

    public function getPostData() : array
    {
        return $this->postData;
    }
}

final class Response
{
    private $content = "";
    private $headers = [];

    public $status = 200;
    public $contentType = 'text/html';
    public $filename = null;

    public function forbidden() : void
    {
        $this->status = 403;
    }

    public function header(string $name, string $value) : void
    {
        $this->headers[] = [$name, $value];
    }

    public function write($content) : void
    {
        $this->content .= $content;
    }

    public function clearContent() : void
    {
        $this->content = "";
    }

    public function clearHeaders() : void
    {
        $this->headers = [];
    }

    public function clear() : void
    {
        $this->clearContent();
        $this->clearHeaders();
    }

    public function getContent() : string
    {
        return $this->content;
    }

    public function getHeaders() : array
    {
        return $this->headers;
    }
}

interface MiddlewareInterface
{
    public function handle(Request $request, Response $response) : bool;
}

interface ExceptionHandlerInterface
{
    public function handleException(\Exception $ex, Response $response) : void;
}

final class Application
{
    private $middleware = [];
    private $exceptionHandler = null;

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

    public function run(Request $request) : void
    {
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
    }
}
