<?php

require(__DIR__ . "/trestle.php");

class PrintMiddleware implements \Trestle\MiddlewareInterface
{
    public function handle(\Trestle\Request $request, \Trestle\Response $response) : bool
    {
        print_r($request);
        throw new Exception("FOO");
        return true;
    }
}

\Trestle\enableErrors();

$app = new \Trestle\Application();
$app->addMiddleware(new PrintMiddleware());
$app->addMiddleware(new PrintMiddleware());
$app->run(\Trestle\Request::create());
