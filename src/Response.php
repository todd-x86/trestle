<?php

namespace Trestle;

final class Response
{
    private $content = "";
    private $headers = [];

    public $status = 200;
    public $contentType = 'text/html';
    public $filename = null;

    public function forbidden()
    {
        $this->status = 403;
    }

    public function header(string $name, string $value)
    {
        $this->headers[] = [$name, $value];
    }

    public function write(string $content)
    {
        $this->content .= $content;
    }

    public function clearContent()
    {
        $this->content = "";
    }

    public function clearHeaders()
    {
        $this->headers = [];
    }

    public function clear()
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
