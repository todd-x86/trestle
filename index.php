<?php

require(__DIR__ . "/trestle.php");

class PrintMiddleware implements \Trestle\MiddlewareInterface
{
    public function handle(\Trestle\Request $request, \Trestle\Response $response) : bool
    {
        print_r($request);
        return true;
    }
}

class AuthMiddleware implements \Trestle\MiddlewareInterface
{
    public function handle(\Trestle\Request $request, \Trestle\Response $response) : bool
    {
        if ($request->getUri() == "/admin")
        {
            $response->status = 403;
            $response->write("<h1>Forbidden</h1>");
            return false;
        }
        return true;
    }
}

\Trestle\enableErrors();

$app = new \Trestle\Application();
$app->addMiddleware(new AuthMiddleware());
$app->addMiddleware(new PrintMiddleware());
$app->run(\Trestle\Request::create());
