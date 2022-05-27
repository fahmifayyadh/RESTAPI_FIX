<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CountryController extends Controller
{
    public function index(){
        $country = Country::where('active', 1)->select('id', 'name')->get();
        return response()->json(['country' => $country], 200);
    }
    public function create(Request $request ){
        DB::beginTransaction();
        try {
            Country::create([
                'name' => $request->name,
            ]);
        }catch (\Exception $exception){
            DB::rollBack();
            return response()->json(['message'=>'error create data'], 400);
        }
        DB::commit();
        return response()->json(['message' => 'success create data'], 200);
    }

    public function update(Request $request ,$id){
        $this->validate($request, [
            'name' => 'string|required',
        ]);
        $country = Country::findOrFail($id);

//        DB::beginTransaction();
//        try {
            $country->update([
                'name' => $request->name,
            ]);
//        }catch (\Exception $exception){
//            DB::rollBack();
//            return response()->json(['message'=>'error update data'], 400);
//        }
//        DB::commit();
        return response()->json(['message' => 'success update data'], 200);
    }
    public function delete($id){
        $country = Country::findOrFail($id);

        DB::beginTransaction();
        try {
            $country->update([
                'active' => 0,
            ]);
        }catch (\Exception $exception){
            DB::rollBack();
            return response()->json(['message'=>'error delete data'], 400);
        }
        DB::commit();
        return response()->json(['message' => 'success delete data'], 200);
    }
}
