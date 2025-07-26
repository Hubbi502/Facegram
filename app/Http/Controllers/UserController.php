<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function Follow(string $username, Request $request){
        $user = User::where("username",$username)->first();

        if(!$user){
            return response()->json([
                "message"=>"user not found"
            ], 404);
        }

        $following = Follow::where("following_id", $user->id)->first();
        $status = $user->is_private ? false : true;

        if($following){
            return response()->json([
                "message"=> "You are already followed",
                "status"=> $status ? "following": "requested"

            ], 422);
        }


        if($user->username == $request->user()->username){
            return response()->json([
                "message"=>"You are not allowed to follow yourself"
            ], 422);
        }

        Follow::create([
            "follower_id" => $request->user()->id,
            "following_id"=> $user['id'],
            "is_accepted"=> $status
        ]);

        return response()->json([
            "message"=>"Follow success",
            "status"=>$status ? "following": "requested"
        ]);
    }

    public function Unfollow(string $username, Request $request){
        $user = User::where("username",$username)->first();
        if(!$user){
            return response()->json([
                "message"=>"user not found"
            ], 404);
        }


        $following = Follow::where("following_id", $user->id)->first();
        if(!$following){
            return response()->json([
                "message"=> "You are not following the user"
            ], 422);
        }

        $following->delete();

        return response()->json([], 204);

    }

    public function GetFollowing (Request $request){
        $followings = Follow::where("follower_id", $request->user()->id)->get();

        $userFollowing = [];
        foreach($followings as $following){
            $user = User::find($following->follower_id);
            $user['is_accepted'] = $following->is_accepted;
            array_push($userFollowing, $user);
        }

        return response()->json([
            "following"=>$userFollowing
        ]);
    }

    public function GetFollower (string $username, Request $request){
        $user = User::where("username", $username)->first();
        if(!$user){
            return response()->json([
                "message"=>"user not found"
            ], 404);
        }
        $followings = Follow::where("following_id", $user->id)
        ->get();



        $userFollowing = [];
        foreach($followings as $following){
            $user = User::find($following->follower_id);
            $user['is_accepted'] = $following->is_accepted;
            array_push($userFollowing, $user);
        }

        return response()->json([
            "follower"=>$userFollowing
        ]);
    }

    public function AcceptFollow(string $username,Request $request){
        $user = User::where("username", $username)->first();

        if(!$user){
            return response()->json([
                "message"=>"user not found"
            ], 404);
        }

        $followings = Follow::where("following_id", $request->user()->id)
        ->where("follower_id", $user->id)
        ->get();

        
        foreach($followings as $following){
            
            if($following->is_accepted == true){
                return response()->json([
                    "message"=>"Follow request is already accepted"
                ], 422);
            }
            $following->update([
                "is_accepted" => true
            ]);
        }
        if (empty($followings->items)){
            return response()->json([
                "message"=> "The user is not following you",
            ], 422);
        }
        return response()->json([
            "message"=> "Follow request accepted",
        ]);

    }

    public function GetDetailUser(string $username){
        $currentUser = Auth::user();


        $user = User::where("username", $username)
        ->first();

        $following = Follow::where("follower_id", $user->id)->get();
        $follower = Follow::where("following_id", $user->id)->get();
        
        $postPermission = Follow::where("follower_id", $currentUser->id)
        ->where("following_id", $user->id)
        ->get();
        $posts = Post::with("post_attachment")->where("user_id", $currentUser->id)->get();
        $user['post']=$posts;
        $user['post_count']=$posts->count();
        $user['following_count']=$following->count();
        $user['follower_count']=$follower->count();
        if($user->is_private == true && ($currentUser->id != $user->id) && (empty($postPermission->items))){
            $user['post']="user is private and you not following";
        }

        return response()->json([$user]);
    }

}
