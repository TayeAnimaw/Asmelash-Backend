<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Mail\SendPasswordResetEmail;
use App\Models\EmailPreference;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function getProfile()
    {
        $user = request()->user();

        return response()->json([
            "success" => true,
            // "user " =>UserResource::collection($user),           
            "data" => [
                "user" => $user,

            ]
        ]);
    }


    public function uploadAvatar(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(["error" => "User not found"], 404);
        }

        $validated = $request->validate([
            "avatar" => ["required", "image", "mimes:jpeg,png,jpg,gif"]
        ]);

        // Store in storage/app/public/user_profile
        $path = $request->file('avatar')->store('user_profile', 'public');

        // Generate the public URL
        $avatarUrl = Storage::url($path);

        // Update user avatar
        $user->avatar = $path;
        $user->save();

        return response()->json([
            'message' => 'Profile picture uploaded successfully',
            'profile_picture' => $avatarUrl
        ], 200);
    }
    public function updateAvatar(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(["error" => "User not found"], 404);
        }

        // Validate the request
        $validated = $request->validate([
            "avatar" => ["required", "image", "mimes:jpeg,png,jpg,gif"]
        ]);

        // Delete old image if exists
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Store the new avatar in 'storage/app/public/user_profile'
        $path = $request->file('avatar')->store('user_profile', 'public');

        // Generate the public URL
        $avatarUrl = Storage::url($path);

        // Update the user's avatar
        $user->avatar = $path;
        $user->save();

        return response()->json([
            'message' => 'Profile picture updated successfully',
            'profile_picture' => $avatarUrl
        ], 200);
    }





    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            // "email" => ["sometimes", "email", "unique:users,email," . $user->id]
            "name" => "sometimes|string",
            // "last_name" => "sometimes|string",
            "phone" => "sometimes|string|unique:users,phone," . $user->id,
            "email" => "sometimes|email|unique:users,email," . $user->id,
        ]);
        $response = $user->update($validated);
        return $user;
    }
}
