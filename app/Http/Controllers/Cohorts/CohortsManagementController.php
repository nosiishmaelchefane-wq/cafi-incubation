<?php

namespace App\Http\Controllers\Cohorts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CohortsManagementController extends Controller
{
    public function index()
    {
        return view('cohorts.index');
    }

    public function show(){
        return  view('cohorts.show');
    }
}
