<?php

namespace Trestle;

final class Request
{
    private $method;
    private $uri;
    private $getData;
    private $postData;

    public function __construct(string $method, string $uri, array $getData, array $postData)
    {
        $this->method = $method;
        $this->uri = self::filterUri($uri);
        $this->getData = $getData;
        $this->postData = $postData;
    }

    public static function filterUri(string $uri) : string
    {
        $filtered = "";
        $index = 0;
        
        // Skip extra '/'
        while ($index != strlen($uri) && $uri[$index] == '/')
        {
            ++$index;
        }

        while ($index != strlen($uri))
        {
            $filtered .= "/";

            // URI segment
            while ($index != strlen($uri) && $uri[$index] != '/')
            {
                $filtered .= $uri[$index];
                ++$index;
            }
            
            // Skip extra '/'
            while ($index != strlen($uri) && $uri[$index] == '/')
            {
                ++$index;
            }
        }
        if ($filtered !== "")
        {
            return $filtered;
        }
        else
        {
            return '/';
        }
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
