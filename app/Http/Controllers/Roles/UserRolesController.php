<?php

namespace App\Http\Controllers\Roles;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserRolesController extends Controller
{
    public function index(){
        return view('user-management.roles.index');
    }
}
