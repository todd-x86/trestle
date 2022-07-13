<?php

namespace Trestle;

class PHPException extends \Exception
{
    public $type;
    public $line;
    public $file;
    public $message;

    public function __construct(string $type, string $file, string $line, string $message)
    {
        parent::__construct("Fatal error ({$file}:{$line}): {$message}");
        $this->type = $type;
        $this->line = $line;
        $this->file = $file;
        $this->message = $message;
    }
}
