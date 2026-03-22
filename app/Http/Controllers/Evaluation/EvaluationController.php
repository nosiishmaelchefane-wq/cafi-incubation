<?php

namespace App\Http\Controllers\Evaluation;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Screening\ScreeningController;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    public function index(){
        return view('evaluation.index');
    }
}
