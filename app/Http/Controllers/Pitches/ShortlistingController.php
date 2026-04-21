<?php

namespace App\Http\Controllers\Pitches;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShortlistingController extends Controller
{
    public function index(){
        return view('pitches.index');
    }
}
