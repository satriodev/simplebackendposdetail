<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;

class LogRequestRoute
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
        $response = $next($request);
        $authuser= $request->header('Authorization');
        $authuser = explode (' ', $authuser);
        if ($authuser[0] != 'Bearer') {
            return null;
        }
    
        else{
            $token= $authuser[1];
        try {
                 $user = JWT::decode($token, new Key (env('JWT_SECRET'), 'HS256'));
               
                $log = [
                    'URI' => $request->getUri(),
                    'METHOD' => $request->getMethod(),
                    'JWT'=> $user,
                    'REQUEST_BODY' => $request->all(),
                    'RESPONSE' => $response->getContent()
                ];
    
                Log::info($log);
                return $response;
            } 
        catch (\Throwable $th) {
                return null;
            }

        }
       

       
    }
}