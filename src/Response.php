<?php

namespace Trestle;

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

    public function write(string $content) : void
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
