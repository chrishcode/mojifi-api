<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
    public function store(Request $request)
    {
        // Check if user exists, if so return user
        $user = User::where('fb_id', '=', $request->fbId)->first();
        if ($user) {
            return $user;
        }

        // If user does not exist, create a new user
        $user = User::create([
            'name' => $request->name,
            'avatar' => 'none',
            'fb_id' => $request->fbId,
            'fb_friends' => $request->fbFriends
        ]);

        return $user;
    }
}
