<?php

namespace Trestle;

interface MiddlewareInterface
{
    public function handle(RequestInterface $request, Response $response) : bool;
}
