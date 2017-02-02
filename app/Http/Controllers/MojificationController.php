<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mojification;
use App\User;

class MojificationController extends Controller
{
    public function index($user)
    {
    	$user = User::find($user);

    	$userMojifications = [];

    	// Find all the mojifications the user has sent
    	$sentMojifications = Mojification::whereSender($user->id)->get();
    	foreach ($sentMojifications as $key => $sentMojification) {
    		$sentMojification->role = 'sender';
    		$sentMojification->name = User::find($sentMojification->receiver)->name;
            $sentMojification->userId = User::find($sentMojification->receiver)->id;
    		array_push($userMojifications, $sentMojification);
    	}

    	// Find all the mojifications the user has received
    	$receivedMojifications = Mojification::whereReceiver($user->id)->get();
    	foreach ($receivedMojifications as $key => $receivedMojification) {
    		$receivedMojification->role = 'receiver';
    		$receivedMojification->name = User::find($receivedMojification->sender)->name;
            $receivedMojification->userId = User::find($receivedMojification->sender)->id;
    		array_push($userMojifications, $receivedMojification);
    	}

    	// Find friends that the user has not yet been in contact with.
    	$userFriends = []; 
    	foreach ($user->fb_friends as $fbFriend) {
    		$friend = User::whereFbId($fbFriend)->get();
    		array_push($userFriends, $friend[0]);
    	}

    	foreach ($userFriends as $key => $userFriend) {
    		foreach ($userMojifications as $userMojification) {
    			if ($userFriend->id == $userMojification->sender || $userFriend->id == $userMojification->receiver) {
    				unset($userFriends[$key]);
    			}
    		}
    	}
    	$noMojificationsYet = $userFriends;
    	foreach ($noMojificationsYet as $userFriend) {
    		$userFriend->role = null;
    		$userFriend->userId = $userFriend->id;

    		unset($userFriend->id);
    		unset($userFriend->avatar);
    		unset($userFriend->fb_id);
    		unset($userFriend->fb_friends);
    		unset($userFriend->created_at);
    		unset($userFriend->updated_at);

    		array_push($userMojifications, $userFriend);
    	}

    	// Sort mojifications by newest
    	usort($userMojifications, function($a, $b) {
    		return strtotime($b->updated_at) - strtotime($a->updated_at);
		});

        return response()->json($userMojifications);
    }

    public function store(Request $request)
    {
    	// Check if friends (the sender and the receiver of the mojification) has been in contact before,
        // if so update record.
    	$mojification = Mojification::where('sender', '=', $request->sender)
                                    ->where('receiver', '=', $request->receiver)
                                    ->first();
    	if (!empty($mojification)) {
            $mojification->update([
                'sender' => $request->sender,
                'receiver' => $request->receiver,
                'emoji' => $request->emoji
            ]);

            return $mojification;
    	}

        $mojification = Mojification::where('sender', '=', $request->receiver)
            ->where('receiver', '=', $request->sender)
            ->first();
        if (!empty($mojification)) {
            $mojification->update([
                'sender' => $request->sender,
                'receiver' => $request->receiver,
                'emoji' => $request->emoji
            ]);

            return $mojification;
        }

        // If the friends (the sender and the receiver of the mojification) has not been in contact before,
        // create new record.
        $mojification = Mojification::create([
        	'sender' => $request->sender,
        	'receiver' => $request->receiver,
            'emoji' => $request->emoji
       	]);

        return $mojification;
    }
}
