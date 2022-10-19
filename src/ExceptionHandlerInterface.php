<?php

namespace Trestle;

interface ExceptionHandlerInterface
{
    public function handleException(\Exception $ex, Response $response);
}
