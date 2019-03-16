<?php

namespace Kapi\Middleware;

use Closure;
use Config;
use Exception;
use Kapi\Model\ApiModel;

class KapiWare
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
        // $bearerToken = " ";
        // if(!empty($request->header(Config::get('kapi.barearTokenName')))){
        //   $bearer  = explode(" ", $request->header(Config::get('kapi.barearTokenName')));
        //   $bearerToken = $bearer[1];
        // //  return response()->json($bearerToken);
        // }


        // check api app
        $ApiApps = ApiModel::where('key',$request->header(Config::get('kapi.app.key') ? Config::get('kapi.app.key') : 'kapi_key'))
                              ->where('secret',$request->header(Config::get('kapi.app.secret') ? Config::get('kapi.app.secret') : 'kapi_secret'))
                              ->where('block',false)
                              ->where('active',true)
                              ->where('app_type','app')
                              ->where(function ($query){
                                if (Config::get('kapi.approval')) {
                                  $query->where('approve',true);
                                }
                              })
                              ->first();


        if(!empty($ApiApps)) {
          return $next($request);
        } else {
          return response()->json('unauthorized app',401);
        }
      } catch (\Exception $e) {
        return response()->json("Internal server error",500);
      }
    }
}
