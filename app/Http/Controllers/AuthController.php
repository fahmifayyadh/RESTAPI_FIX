<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class AuthController extends Controller
{
    public function login(Request $request)
    {
      $this->validate($request, [
            'email' => 'required|max:255',
            'password' => 'required|min:6'
        ]);
        $user = User::where('email', $request->email)->first();

      if (User::where('email', $request->email)->where('active', 1)->first() && Hash::check($request->password, $user->password)) {
        $token = Crypt::encrypt($user->email.'+'.$user->password);
        $user->update([
          'api_token' => $token,
        ]);
        return response()->json(['auth'=> (['api_token' => $token, 'user' => $user])], 200);
      }


      return response()->json(['error'=> 'error authentication'], 401);
    }
}
