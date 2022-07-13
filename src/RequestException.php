<?php

namespace Trestle;

class RequestException extends \Exception
{
    public $arg;

    public function __construct(string $msg, string $arg)
    {
        parent::__construct($msg);
        $this->arg = $arg;
    }
}
