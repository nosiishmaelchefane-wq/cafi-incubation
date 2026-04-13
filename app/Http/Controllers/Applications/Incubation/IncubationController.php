<?php

namespace App\Http\Controllers\Applications\Incubation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IncubationController extends Controller
{
   public function show($id){
    return view('applications.incubation.show', [
        'id' => $id
    ]);
}
}
