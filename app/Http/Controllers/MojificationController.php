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
    		$sentMojification->receiverName = User::find($sentMojification->receiver)->name;
    		array_push($userMojifications, $sentMojification);
    	}

    	// Find all the mojifications the user has received
    	$receivedMojifications = Mojification::whereReceiver($user->id)->get();
    	foreach ($receivedMojifications as $key => $receivedMojification) {
    		$receivedMojification->role = 'receiver';
    		$receivedMojification->senderName = User::find($receivedMojification->sender)->name;
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
    		unset($userFriend->id);
    		unset($userFriend->avatar);
    		unset($userFriend->fb_id);
    		unset($userFriend->fb_friends);
    		unset($userFriend->created_at);
    		unset($userFriend->updated_at);
    		$userFriend->role = null;
    		array_push($userMojifications, $userFriend);
    	}

    	// Sort mojifications by newest
    	usort($userMojifications, function($a, $b) {
    		return strtotime($b->created_at) - strtotime($a->created_at);
		});

    	return $userMojifications;
    }
}
