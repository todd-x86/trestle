<?php

namespace Trestle;

interface RequestInterface
{
    public function getQueryParam(string $name) : string;
    public function getPostField(string $name) : string;
    public function getUri() : string;
    public function getRequestType() : string;
    public function getQueryParams() : array;
    public function getPostData() : array;
}
