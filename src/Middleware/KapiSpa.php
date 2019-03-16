<?php

namespace Kapi\Middleware;

use Closure;
use Config;
use Exception;
use Kapi\Model\ApiModel;

class KapiSpa
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
      try {
        // barear token
        $bearerToken = " ";
        if(!empty($request->header(Config::get('kapi.spa.barearTokenName')))){
          $bearer  = explode(" ", $request->header(Config::get('kapi.spa.barearTokenName')));
          $bearerToken = $bearer[1];
        //  return response()->json($bearerToken);
        }
        if(($bearerToken === Config::get('kapi.spa.barearToken'))) {
          return $next($request);
        } else {
          return response()->json('unauthorized',401);
        }
      } catch (\Exception $e) {
        return response()->json("sorry,can't access");
      }
    }
}
