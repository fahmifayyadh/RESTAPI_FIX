<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function kunjunganadmin(){
        $kunjungan = Place::where('active', 1)
            ->leftJoin('visitations','visitations.place_id', '=', 'places.id' )
            ->select('places.name as destinasi', 'visitations.visitor', 'visitations.date')
            ->orderBy('visitations.date','asc')->get();
    }
}
