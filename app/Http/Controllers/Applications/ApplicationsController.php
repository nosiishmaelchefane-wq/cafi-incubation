<?php

namespace App\Http\Controllers\Applications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApplicationsController extends Controller
{
    public function index(){
        return view ('applications.index');
    }
}
