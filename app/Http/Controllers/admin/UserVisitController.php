<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\UserVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserVisitController extends Controller
{
    public function index($skip=0, $take =15){
        $visitor = UserVisit::orderBy('name', 'desc')->skip($skip)->take($take)->get();
        return response()->json(['user_visit' =>$visitor], 200);

    }
    public function create(Request $request){
        $this->validate($request, [
            'name' => 'string|required',
            'identity' => 'integer|required',
            'identity_number' => 'string|required',
            'province' => 'required',
            'district' => 'required',
            'overseas' => 'integer|required',
            'country' => 'string',
        ]);
        DB::beginTransaction();
        try {
            UserVisit::create([
                'name' => $request->name,
                'identity' => $request->identity,
                'indentity_number' => $request->indentity_number,
                'province' => $request->province,
                'district' => $request->district,
                'overseas' => $request->overseas,
                'country' => $request->country,
            ]);
        }catch ( \Exception $exception){
            DB::rollBack();
            return response()->json(['message'=>$exception], 400);
        }
        DB::commit();
        return response()->json(['message' =>' success create visitor user data'], 200);
    }

    public function detail($id){
        $visitor = findOrFail($id);
        return response()->json(['user_visitor' => $visitor], 200);
    }

    public function update(Request $request, $id){
        $this->validate($request, [
            'name' => 'string|required',
            'identity' => 'integer|required',
            'identity_number' => 'string|required',
            'province' => 'required',
            'district' => 'required',
            'overseas' => 'integer|required',
            'country' => 'string',
        ]);
        $visitor = findOrFail($id);
        DB::beginTransaction();
        try {
            $visitor->update([
                'name' => $request->name,
                'identity' => $request->identity,
                'indentity_number' => $request->indentity_number,
                'province' => $request->province,
                'district' => $request->district,
                'overseas' => $request->overseas,
                'country' => $request->country
            ]);
        }catch ( \Exception $exception){
            DB::rollBack();
            return response()->json(['message'=>'error update data visitor user data'], 400);
        }
        DB::commit();
        return response()->json(['message' =>' success update visitor user data'], 200);
    }

    public function delete($id){
        $visit = findOrFail($id);
        DB::beginTransaction();
        try {
            $visit->update([
                'active' => 0
            ]);
        }catch (\Exception $exception){
            DB::rollBack();
            return response()->json(['message'=> 'error delete data'], 400);
        }
        DB::commit();
        return response()->json(['message' => 'success delete data user visitor'], 200);
    }
}
