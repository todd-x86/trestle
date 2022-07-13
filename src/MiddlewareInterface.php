<?php

namespace Trestle;

interface MiddlewareInterface
{
    public function handle(Request $request, Response $response) : bool;
}
