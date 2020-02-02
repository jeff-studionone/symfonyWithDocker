<?php

use App\Kernel;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

require dirname(__DIR__).'/config/bootstrap.php';

Debug::enable();
$kernel = new Kernel('dev', true);

$request = Request::createFromGlobals();
Request::setTrustedProxies(['127.0.0.1', $request->server->get('REMOTE_ADDR')], Request::HEADER_X_FORWARDED_ALL);
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
