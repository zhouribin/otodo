<?php

namespace App\Http\Middleware;


use App\Constants\ErrorMsgConstants;
use Closure;
use Illuminate\Http\Request;

class CheckSign
{
    private $logger;

    protected $result = [
        "statusCode" => ErrorMsgConstants::SIGN_ERROR,
        "message"    => "签名错误",
        "data"       => [],
    ];

    public function __construct()
    {
        $this->logger = customerLoggerHandle('checkSign');
    }

    public function handle(Request $request, Closure $next)
    {
        if (!$request->has('sign') || !$this->checkVerifySign($request->all())) {
            return $this->returnResponse();
        }

        return $next($request);
    }

    /**
     * @param array $params
     * @return bool
     */
    private function checkVerifySign(array $params)
    {
        $sign = $params['sign'];
        $verifySign = md5(customBuildQuery($params) . env('API_SIGN'));
        $this->logger->debug('checkVerifySign', [$sign, $verifySign]);
        return $sign == $verifySign;
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    private function returnResponse()
    {
        return response($this->result, 200);
    }
}
