<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiMiddleware
{
    private $logger;

    public function __construct()
    {
        $this->logger = customerLoggerHandle('request');
    }

    public function handle(Request $request, Closure $next)
    {
        $msgUniqueKey = "[" . md5(uniqid('api_request_', true)) . "]";

        $this->logger->info($msgUniqueKey . $request->getPathInfo() . "[Request][" . $request->getMethod() . "]", $request->all());
        $this->logger->info($msgUniqueKey . $request->getPathInfo() . "[header][" . $request->getMethod() . "]", $request->header());

        $headers = [
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age'           => '60',
            'Access-Control-Allow-Methods'     => 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Headers'     => 'Origin, X-Requested-With, Content-Type, Accept, Authorization',
        ];

        if ($request->getMethod() == "OPTIONS") {
            return response()->make('ok', 200, $headers);
        }

        $response = $next($request);
        foreach ($headers as $key => $value) {
            $response->header($key, $value);
        }

        if (is_array(json_decode($response->content(), true))) {
            $this->logger->info($msgUniqueKey . $request->getPathInfo() . "[Response][" . $request->getMethod() . "]", json_decode($response->content(), true));
        } else {
            $this->logger->info($msgUniqueKey . $request->getPathInfo() . "[Response][" . $request->getMethod() . "]", [$response->content()]);
        }
        $this->logger->info("=============================================================");

        return $response;
    }
}
