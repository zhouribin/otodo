<?php

namespace App\Http\Middleware;

use App\Constants\ErrorMsgConstants;
use App\Exceptions\ServiceException;
use App\Models\AppUser;
use Closure;
use Illuminate\Http\Request;

class AuthJWT
{

    protected $result = [
        "statusCode" => ErrorMsgConstants::TOKEN_ERROR,
        "message"    => "登录过期!请尝试重新登录",
        "data"       => [],
    ];

    /**
     * Handle an incoming request.
     *
     * @param  Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            /** @var AppUser $authUser */
            $authUser = auth('api')->user();
            if (!isset($authUser)) {
                throw new ServiceException(ErrorMsgConstants::TOKEN_ERROR, "登录过期!请尝试重新登录");
            }

        } catch (\Exception $e) {
            return response($this->result, 200);
        }

        return $next($request);
    }
}
