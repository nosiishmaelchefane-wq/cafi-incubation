<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index()
    {
        return view ('user-management.users.index');
    }

    public function show($id)
    {
        $user = \App\Models\User::findOrFail($id);

        return view('user-management.users.show', compact('user'));
    }
}
