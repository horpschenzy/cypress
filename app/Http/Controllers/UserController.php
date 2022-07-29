<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Activity;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function users()
    {
        $users = User::with('activities')->where('role', 'USER')->get();
        return view('pages.users', compact('users'));
    }
    
    public function viewUser($id)
    {
        $user = User::with('activities')->where('role', 'USER')->where('id', $id)->first();
        return view('pages.user-activities', compact('user'));
    }
}
