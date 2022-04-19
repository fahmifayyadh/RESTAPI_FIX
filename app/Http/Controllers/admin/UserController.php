<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($skip=0, $take=20)
    {
        $user  = User::orderBy('created_at', 'desc')->skip($skip)->take($take)->get();
        return response()->json(['user' => $user], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $this->validate($request, [
            'username' => 'string|required',
            'email' => 'email|required',
            'password' => 'string|min:5|required',
            'role' => 'integer|required',
        ]);

        $pwd = Hash::make($request->password);
        $api_token = $token = Crypt::encrypt($request->email.'+'.$pwd);
        DB::beginTransaction();
        try {
          $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => $pwd,
            'role' => $request->role,
            'api_token' => $api_token,
          ]);
        } catch (\Exception $e) {
          DB::rollback();
          return response()->json(['error' => 'error create user, check your input'], 500);
        }
        DB::commit();
        return response()->json(['success' => 'success create user'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return response()->json(['user'=> $user], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      $this->validate($request, [
            'username' => 'string|required',
            'email' => 'email|required',
            'password' => 'string|min:5',
            'role' => 'integer|required',
        ]);

        DB::beginTransaction();
        $user = User::findOrFail($id);
        try {
          $user->update([
            'username' => $request->username,
            'email' => $request->email,
            'role' => $request->role,
            // 'api_token' => $api_token,
          ]);
          if (!empty($request->password)) {
            $pwd = Hash::make($request->password);
            // $api_token = $token = Crypt::encrypt($request->email.'+'.$pwd);
            $user->update([
              'password' => $pwd
            ]);
          }
        } catch (\Exception $e) {
          DB::rollback();
          return response()->json(['error' => 'error create user, check your input'], 500);
        }
        DB::commit();
        return response()->json(['success' => 'success create user'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        DB::beginTransaction();
        try {
          $user->update([
            'active' => 0
          ]);
        } catch (\Exception $e) {
          DB::rollback;
          return response()->json(['error'=>'error delete data'], 500);
        }
        DB::commit();
        return response()->json(['success'=> 'success delete data'], 200);

    }
}
