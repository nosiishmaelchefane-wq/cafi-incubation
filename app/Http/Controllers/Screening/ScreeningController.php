<?php

namespace App\Http\Controllers\Screening;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ScreeningController extends Controller
{
    public function index(){
        return view('screening.index');
    }
}
