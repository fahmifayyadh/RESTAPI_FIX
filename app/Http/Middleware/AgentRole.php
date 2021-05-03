<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;

class AgentRole
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
        $token = Crypt::decrypt($request->header);
      } catch (\Exception $e) {
        return response()->json(['error'=>'Unauthorized'], 401);
      }

      $data = explode('+',$token);
      $user = User::where('email', $data[0])->first();

      if ($user->role != 'agent' || $user->role !='admin') {
        return response()->json(['error'=>'page Not found'], 400);
      }

        return $next($request);
    }
    }
}
