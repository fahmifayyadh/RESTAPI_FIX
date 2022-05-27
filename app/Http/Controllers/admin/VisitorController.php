<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Visitation;
use App\Models\UserVisit;
use App\Models\User;
use App\Models\Place;
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
    public function index($skip=0, $take=20)
    {
        $visitor = Visitation::leftJoin('users', 'users.id', '=', 'visitations.user_id')
        ->leftJoin('places', 'places.id', '=', 'visitations.place_id')
        ->select('visitations.id as id', 'visitations.user_id', 'users.username', 'places.id as place_id', 'places.name as name_place', 'places.location', 'visitations.visitor', 'visitations.date')
        ->orderBy('date', 'desc')->skip($skip)->take($take)->get();

        return response()->json(['Visitation'=> $visitor], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $place = Place::where('active', 1)
        ->select('id', 'name', 'location')->get();

        return response()->json(['place'=> $place],200);
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
            'place_id' => 'integer|required',
            'visitor' => 'integer|required',
          'user_visit' => 'integer',
            'date' => 'date|required',
        ]);

        $token = Crypt::decrypt($request->header);
        $data = explode('+',$token);
        $user = User::where('email', $data[0])->first();

        DB::beginTransaction();
        try {
          $visit = Visitation::create([
            'place_id' => $request->place_id,
            'user_id' => $user->id,
            'visitor' => $request->visitor,
            'user_visits_id' => $request->user_visit,
            'date' => Carbon::createFromFormat('Y-m-d', $request->date)
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
    public function edit($id)
    {
        $place = Place::where('active', 1)
        ->select('id', 'name', 'location')->get();
        $visitor = Visitation::findOrFail($id);
        $agent = User::where('id',$visitor->user_id)->select('id', 'username')->first();
        return response()->json(['place' => $place, 'Visitation'=>$visitor, 'agent' => $agent], 200);
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
            'place_id' => 'integer|required',
            'visitor' => 'integer|required',
            'date' => 'date|required',
        ]);

      $visitor = Visitation::findOrFail($id);

      DB::beginTransaction();
      try {
        $visitor->update([
          'place_id' => $request->place_id,
          'visitor' => $request->visitor,
          'date' => $request->date,
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
    public function destroy($id)
    {
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
