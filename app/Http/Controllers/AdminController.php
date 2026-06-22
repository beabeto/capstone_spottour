<?php

namespace App\Http\Controllers;

use App\Models\User;

class AdminController extends Controller
{
    public function users()
    {
        $users = User::where('role','user')->get();

        return view(
            'admin.users',
            compact('users')
        );
    }

    public function owners()
    {
        $owners = User::where('role','owner')->get();

        return view(
            'admin.owners',
            compact('owners')
        );
    }
}