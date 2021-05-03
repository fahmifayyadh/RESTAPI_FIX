<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pic;
use App\Models\User;
use App\Models\Place;
use Illuminate\Support\Facades\DB;

class PicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($skip =0, $take=20)
    {
        $pic = Pic::leftJoin('users',  'users.id', '=','pics.user_id')
        ->leftJoin('places', 'places.id', '=', 'pics.place_id')
        ->select('pics.id as pic_id','users.id as user_id', 'users.username as username', 'users.email as email', 'places.id as place_id', 'places.name as name', 'places.location as location')
        ->orderBy('place_id', 'asc')->skip($skip)->take($take)->get();

        return response()->json(['pic' => $pic], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $user = User::where('active', 1)->get();
      $place = Place::where('active', 1)->get();

      return response()->json(['user'=> $user, 'place' => $place], 200);
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
            'user_id' => 'integer|required',
            'place_id' => 'integer|required',
        ]);

        DB::beginTransaction();

        try {
          Pic::create([
            'user_id' => $request->user_id,
            'place_id' => $request->place_id
          ]);
        } catch (\Exception $e) {
          DB::rollback();
        }


        DB::commit();
        return response()->json(['success'=>'success create data'], 200);
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
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pic = Pic::findOrFail($id);
        $user = User::where('active', 1)->get();
        $place = Place::where('active', 1)->get();

        return response()->json(['selected' => $pic, 'user'=> $user, 'place' => $place], 200);
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
            'user_id' => 'integer|required',
            'place_id' => 'integer|required',
        ]);

        $pic = Pic::findOrFail($id);
        DB::beginTransaction();
        try {
          $pic->update([
            'user_id' => $request->user_id,
            'place_id' => $request->place_id
          ]);
        } catch (\Exception $e) {
          DB::rollback();
        }


        DB::commit();
        return response()->json(['success'=>'success create data'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pic = Pic::findOrFail($id);
        DB::beginTransaction();
        try {
          $pic->delete();
        } catch (\Exception $e) {
          DB::rollback();
          return response()->json(['error'=>'error create data'], 500);
        }
        DB::commit();
        return response()->json(['success'=>'success create data'], 200);

    }
}
