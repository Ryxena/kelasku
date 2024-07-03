<?php

namespace App\Http\Controllers;

use App\Helper\FCM;
use App\Helper\FirebaseHelper;
use App\Models\User;
use App\Helper\ApiResult;
use App\Models\Friendship;

use Illuminate\Http\Request;

use function PHPUnit\Framework\isEmpty;

class FriendshipController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseHelper $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function getFriendList()
    {
        $user = auth()->user();

        $acceptedFriends = $user->acceptedFriends()
            ->with('school')
            ->get()
            ->each(function ($friend) {
                $friend->school_name = $friend->school->name;
                unset($friend->school_id, $friend->school);
            })
            ->makeHidden('pivot');

        $acceptedByOthers = $user->acceptedByOthers()
            ->with('school')
            ->get()
            ->each(function ($friend) {
                $friend->school_name = $friend->school->name;
                unset($friend->school_id, $friend->school);
            })
            ->makeHidden('pivot');

        $allFriends = $acceptedFriends->merge($acceptedByOthers);

        if ($allFriends->isEmpty()) {
            return ApiResult::Response(404, "Not Found", null);
        }

        return ApiResult::Response(200, "Success", $allFriends);
    }

    public function detail($id)
    {
        $friend = User::where('id', $id)->first();
        if($friend) {
            return ApiResult::Response(200, "Success", $friend);
        }
        return ApiResult::Response(404, "Not Found", null);
    }

    public function getPendingFriend()
    {
        $user = auth()->user();
        $sentRequests = $user->pendingFriendRequestsSent()
            ->with('school')
            ->get()
            ->each(function ($friend) {
                $friend->school_name = $friend->school->name;
                unset($friend->school_id, $friend->school);
            })
            ->makeHidden(['pivot', 'created_at', 'updated_at']);

        $receivedRequests = $user->pendingFriendRequestsReceived()
            ->with('school')
            ->get()
            ->each(function ($friend) {
                $friend->school_name = $friend->school->name;
                unset($friend->school_id, $friend->school);
            })
            ->makeHidden(['pivot', 'created_at', 'updated_at']);

        $allPendingRequests = $sentRequests->merge($receivedRequests);

        if ($allPendingRequests->isEmpty()) {
            return ApiResult::Response(404, "Not Found", null);
        }

        return ApiResult::Response(200, "Success", $allPendingRequests);
    }

    public function potentialFriends()
    {
        $user = auth()->user();

        $friendsAndPending = $user->friends()->pluck('friend_id')->toArray();
        $pendingAndFriends = $user->pendingFriendRequests()->pluck('user_id')->toArray();
        $allConnectedUserIds = array_unique(array_merge($friendsAndPending, $pendingAndFriends));

        $potentialFriends = User::whereNotIn('id', $allConnectedUserIds)
            ->where('role', '!=', 'admin')
            ->with('school')
            ->get()
            ->each(function ($user) {
                $user->school_name = $user->school->name;
                unset($user->school_id, $user->school);
            });

        if ($potentialFriends->isEmpty()) {
            return ApiResult::Response(404, "Not Found", null);
        }

        return ApiResult::Response(200, "Success", $potentialFriends);
    }



    public function addFriend(Request $request)
    {
        $user = auth()->user();
        $friendId = $request->id;

        if ($user->id == $friendId) {
            return ApiResult::Response(422, "Can't add yourself", null);
        }

        if ($user->friends()->where('users.id', $friendId)->exists()) {
            return ApiResult::Response(422, "Friendship already exists", null);
        }

        $user->friends()->attach($friendId, ['status' => 'pending']);

        return ApiResult::Response(200, "Success sending friend request", null);
    }


    public function accept(Request $request)
    {
        $user = auth()->user();
        $friend = $user->pendingFriendRequestsReceived()
            ->where('users.id', $request->id)
            ->with('school')
            ->first();

        if (!$friend) {
            return ApiResult::Response(404, "Error: Friend request not found", null);
        }

        $friend->school_name = $friend->school->name;
        $friend->pivot->status = 'accepted';
        $friend->pivot->save();

        $friend = $friend->makeHidden(['created_at', 'updated_at', 'pivot']);

        unset($friend->school_id, $friend->school);

        return ApiResult::Response(200, "Friend request accepted", $friend);
    }




    public function reject(Request $request)
    {
        $user = auth()->user();
        $friend = $user->pendingFriendRequestsReceived()
            ->where('users.id', $request->id)
            ->with('school')
            ->first();

        if (!$friend) {
            return ApiResult::Response(404, "Error: Friend request not found", null);
        }

        $friend->school_name = $friend->school->name;
        $friend->pivot->status = 'rejected';
        $friend->pivot->save();

        $friend = $friend->makeHidden(['created_at', 'updated_at', 'pivot']);

        unset($friend->school_id, $friend->school);

        return ApiResult::Response(200, "Friend request rejected", $friend);
    }

    public function destroy($friendId)
    {
        $user = auth()->user();
        $user->friends()->detach($friendId);

        return ApiResult::Response(200, "Pertemanan berhasil dihapus", null);
    }

    public function colek(Request $request)
    {
        $user = auth()->user();
        $target = User::where("phone", $request->phone)->first();

        if (!$target) {
            return ApiResult::Response(404, "Target user not found", null);
        }

        $token = $target->device_token;
        $title = 'Kamu di colek xD';
        $body = "Hei $target->name kamu dicolek oleh $user->name";

        $result = $this->firebaseService->sendNotification($token, $title, $body);

        if ($result) {
            return ApiResult::Response(200, "Notification sent successfully", null);
        } else {
            return ApiResult::Response(500, "Failed to send notification", null);
        }
    }


}
