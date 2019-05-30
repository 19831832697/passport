<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class Time
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $uip=$_SERVER['REMOTE_ADDR'];
        $path = $_SERVER['REQUEST_URI'];
        $key="list_".$uip.$path;
        $num=Redis::incr($key);
        Redis::expire($key,60);
        if($num>20){
            $res=[
                'code'=>1,
                'msg'=>'调用频繁，一分钟后重试'
            ];
            die(json_encode($res,JSON_UNESCAPED_UNICODE));
        }
        Redis::expire($key,60);
        return $next($request);
    }
}
