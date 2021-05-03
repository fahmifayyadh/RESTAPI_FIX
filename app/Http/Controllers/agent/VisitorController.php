<?php

namespace App\Http\Controllers\agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Visitation;
use App\Models\UserVisit;
use App\Models\User;
use App\Models\Place;
use App\Models\Pic;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VisitorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$place_id ,$skip=0, $take=20)
    {
      $token = Crypt::decrypt($request->header);
      $data = explode('+',$token);
      $user = User::where('email', $data[0])->first();

      if (empty(Pic::where('place_id', $place_id)->where('user_id', $user->id)->first()) && $user->role != 'admin') {
        return response()->json(['error' => 'you dont have access for this place'], 400);
      }

    $visitor = Visitation::where('place_id', $place_id)->where('user_id', $user->id)
    ->leftJoin('places', 'places.id', '=', 'visitations.place_id')
      ->select('visitations.id as id', 'visitations.user_id', 'places.id as place_id', 'places.name as name_place', 'places.location', 'visitations.visitor', 'visitations.date')
      ->orderBy('date', 'desc')->skip($skip)->take($take)->get();

      return response()->json(['Visitation'=> $visitor], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
      $token = Crypt::decrypt($request->header);
      $data = explode('+',$token);
      $user = User::where('email', $data[0])->first();

      if ($user->role != 'admin') {
        $place = Pic::where('user_id', $user->id)
        ->leftJoin('places', 'places.id', '=', 'pics.place_id')
        ->where('places.active', 1)->select('places.id', 'name', 'location')
        ->get();
      }else{
        $place = Pic::leftJoin('places', 'places.id', '=', 'pics.place_id')
        ->where('places.active', 1)->select('places.id', 'name', 'location')
        ->get();
      }


      return response()->json(['place'=> $place],200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $place_id)
    {
      $this->validate($request, [
            'visitor' => 'integer|required',
        ]);

        $token = Crypt::decrypt($request->header);
        $data = explode('+',$token);
        $user = User::where('email', $data[0])->first();

        if (empty(Pic::where('place_id', $place_id)->where('user_id', $user->id)->first())) {
          return response()->json(['error'=>'you are not have permission'], 400);
        }

        DB::beginTransaction();
        try {
          $visit = Visitation::create([
            'place_id' => $place_id,
            'user_id' => $user->id,
            'visitor' => $request->visitor,
            'date' => Carbon::now()
          ]);
        } catch (\Exception $e) {
          DB::rollback();
          return response()->json(['error'=> 'error create data'], 500);
        }
        DB::commit();
        return response()->json(['success' => 'success create'], 200);
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
    public function edit(Request $request, $place_id, $id)
    {
      $token = Crypt::decrypt($request->header);
      $data = explode('+',$token);
      $user = User::where('email', $data[0])->first();

      if (empty(Pic::where('place_id', $place_id)->where('user_id', $user->id)->first())) {
        return response()->json(['error'=>'you are not have permission'], 400);
      }

      $place = Place::where('id', $place_id)
      ->select('id', 'name', 'location')->first();
      $visitor = Visitation::findOrFail($id);
      return response()->json(['place' => $place, 'Visitation'=>$visitor], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$place_id, $id)
    {
      $this->validate($request, [
            'visitor' => 'integer|required',
        ]);

        $token = Crypt::decrypt($request->header);
        $data = explode('+',$token);
        $user = User::where('email', $data[0])->first();

        if (empty(Pic::where('place_id', $place_id)->where('user_id', $user->id)->first())) {
          return response()->json(['error'=>'you are not have permission'], 400);
        }
        $visitor = Visitation::findOrFail($id);

        DB::beginTransaction();
        try {
          $visitor->update([
            'visitor' => $request->visitor,
          ]);
        } catch (\Exception $e) {
          DB::rollback();
          return response()->json(['error'=> 'error update data'], 500);
        }
        DB::commit();
        return response()->json(['success'=>'success update data'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $place_id, $id)
    {
      $token = Crypt::decrypt($request->header);
      $data = explode('+',$token);
      $user = User::where('email', $data[0])->first();

      if (empty(Pic::where('place_id', $place_id)->where('user_id', $user->id)->first())) {
        return response()->json(['error'=>'you are not have permission'], 400);
      }
      if (empty(Visitation::where('id', $id)->where('place_id', $place_id)->where('user_id', $user->id)->first())) {
        return response()->json(['error'=>'you are not have permission'], 400);
      }

      $visitor = Visitation::findOrFail($id);
      DB::beginTransaction();
      if (!empty($visitor->user_visits_id)) {
        $userVisit = UserVisit::findOrFail($visitor->user_visits_id);
        try {
          $userVisit->delete();
        } catch (\Exception $e) {
          DB::rollback();
          return response()->json(['error' => 'error delete data user visit'], 500);
        }

      }
      try {
        $visitor->delete();
      } catch (\Exception $e) {
        DB::rollback;
        return response()->json(['error' => 'error edit data'], 500);
      }

      DB::commit();
      return response()->json(['success'=>'success delete data'], 200);
    }
}
