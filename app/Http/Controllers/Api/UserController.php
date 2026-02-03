<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized!, only Admin can View Users list'], 403);
        }
        $users = User::all();
        return UserResource::collection($users);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized!, only Admin can create a user'], 403);
        }
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'nullable|string|min:6',
            'phone' => 'nullable|string|max:20|unique:users',
            'role' => 'required|in:admin,sales,quality,production',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // added image validation
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $avatarPath = $avatar->store('user_profile', 'public'); // Store in user_profile folder
            $validatedData['avatar'] = $avatarPath; // Save the path in the database
        }

        // Hash the password if it's provided
        if (isset($validatedData['password'])) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        }

        // Create the user with validated data
        $user = User::create($validatedData);
        return response()->json('User created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        return new UserResource($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $authUser = auth()->user();

        // Allow only admins or the user themselves to update their data
        if ($authUser->role !== 'admin' && $authUser->id !== $user->id) {
            return response()->json(['error' => 'Unauthorized!'], 403);
        }

        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20|unique:users,phone,' . $user->id,
            'role' => 'sometimes|required|in:admin,sales,quality,production',
            'status' => 'sometimes|required|in:active,inactive',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // added image validation
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete the old avatar if exists
            if ($user->avatar) {
                $oldAvatarPath = storage_path('app/public/' . $user->avatar);
                if (file_exists($oldAvatarPath)) {
                    unlink($oldAvatarPath); // Remove the old avatar file
                }
            }

            // Store new avatar
            $avatar = $request->file('avatar');
            $avatarPath = $avatar->store('user_profile', 'public'); // Store in user_profile folder
            $validatedData['avatar'] = $avatarPath; // Save the path in the database
        }

        // Check if password is provided, then hash it
        if ($request->filled('password')) {
            $validatedData['password'] = bcrypt($request->password);
        } else {
            // Prevent password from being updated
            unset($validatedData['password']);
        }

        // Update the user with validated data
        $user->update($validatedData);

        return response()->json([
            'message' => 'User updated successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Get the authenticated user
        $authUser = auth()->user();

        // Ensure that the authenticated user is an admin
        if ($authUser->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized, only admin can delete users'], 403);
        }

        // Find the user to be deleted
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Delete the user's avatar if exists
        if ($user->avatar) {
            $avatarPath = public_path($user->avatar);
            if (File::exists($avatarPath)) {
                File::delete($avatarPath);
            }
        }

        // Delete the user
        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
