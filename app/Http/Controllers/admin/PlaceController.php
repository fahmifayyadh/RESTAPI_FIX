<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Place;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PlaceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($skip=0, $take=20)
    {
        $place = Place::where('active', 1)->skip($skip)->take($take)->get();

        return response()->json(['data' => $place], 200);
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
            'name' => 'string|required',
            'image' => 'image',
            'location' => 'string|required',
            'fee_local' => 'integer',
            'fee_inter' => 'integer',
        ]);

      $image = $request->image;
      $extension = $image->getClientOriginalExtension();
      $filenametostore = md5(Carbon::now()) . '.' . $extension;

      DB::beginTransaction();
      try {
        $place = Place::create([
          'name' => $request->name,
          'image' => $image->storeAs('public/place', $filenametostore),
          'location' => $request->location,
          'fee_local' => $request->fee_local,
          'fee_inter' => $request->fee_inter,
        ]);
      } catch (\Exception $e) {
        DB::roleback();
        return response()->json(['error'=> 'error create data'], 500);
      }

      DB::commit();
      return response()->json(['success' => 'success create data'], 200);
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
        $place = Place::findOrFail($id);
        return response()->json(['place'=>$place], 200);
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
            'name' => 'string|required',
            'image' => 'image',
            'location' => 'string|required',
            'fee_local' => 'integer',
            'fee_inter' => 'integer',
            'active' => 'boolean|required'
        ]);
        $place = Place::findOrFail($id);

        DB::beginTransaction();
        try {
          $place->update([
            'name' => $request->name,
            // 'image',
            'location' => $request->location,
            'fee_local' => $request->fee_local,
            'fee_inter' => $request->fee_inter,
            'active' => $request->active,
          ]);
        } catch (\Exception $e) {
          DB::roleback();
          return response()->json(['error' => 'error update data'], 500);
        }



        if (!empty($request->image)) {

          $image = $request->image;
          $extension = $image->getClientOriginalExtension();
          $filenametostore = md5(Carbon::now()) . '.' . $extension;
          try{
            if (!empty($place->image)) {
              Storage::delete($place->image);
            }
            $place->update([
              'image' => $image->storeAs('public/place', $filenametostore),
            ]);
          } catch (\Exception $e) {
            DB::roleback();
            return response()->json(['error' => 'error update image'], 500);
          }
        }
        DB::commit();
        return response()->json(['success'=> 'data updated' ,'data' => $place], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $place = Place::findOrFail($id);
        $place->update([
          'active' => 0
        ]);

        return response()->json(['success' => 'data success delete'], 200);
    }
}
