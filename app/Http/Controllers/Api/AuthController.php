<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\CustomPasswordResetMail;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // Handle user registration
    public function register(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            "email" => "required|email|unique:users,email",
            "name" => "required|string",
            "phone" => "required|string|unique:users,phone",
            "password" => "required|confirmed",
            "token" => "sometimes",
        ]);

        // Create a new user
        $user = User::create([
            "name" => $validated["name"],
            "email" => $validated["email"],
            "password" => Hash::make($validated["password"]),
            "phone" => $validated["phone"],
        ]);

        // Create a token for the new user
        $token = $user->createToken("auth_token")->plainTextToken;

        // Return the token and user data as a response
        return response()->json(["user" => $user, "token" => $token], 201);
    }
    // Handle user login

    // Handle user login
    public function login(Request $request)
    {
        $validated = $request->validate([
            "identifier" => "required", // Can be email or phone
            "password" => "required",
            "token" => "sometimes",
        ]);
    
        // Check if the identifier is an email or phone number
        $user = User::where("email", $validated["identifier"])
            ->orWhere("phone", $validated["identifier"])
            ->first();
    
        // Validate user existence and password
        if (!$user || !Hash::check($validated["password"], $user->password)) {
            throw ValidationException::withMessages([
                "identifier" => ["Invalid credentials"],
            ]);
        }
    
        // ❌ Prevent inactive users from logging in
        if ($user->status !== "active") {
            return response()->json([
                "error" => "Your account is inactive. Please contact an admin."
            ], 403);
        }
    
        // ✅ Only active users get an authentication token
        $token = $user->createToken("auth_token")->plainTextToken;
    
        return response()->json([
            "message" => "You are logged in successfully",
            "token" => $token,
            "user" => $user
        ], 200);       
    }



    //user logout
    public function logout(Request $request)
    {
        //     // Get the current authenticated user
        $user = $request->user();

        // Revoke all tokens for the user
        $user->tokens()->delete();

        // Return a success response
        return response()->json(["message" => "Logged out successfully"], 200);
    }
    //   /**
    //  * Handle Forgot Password Request (Send Reset Link)
    //  */
    public function forgotPassword(Request $request)
    {       
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Get the email address from the request
        $email = $request->only('email');

        // Send the reset password link with custom mail
        $status = Password::sendResetLink($email, function ($user, $token) {
            // Send a custom email
            Mail::to($user->email)->send(new CustomPasswordResetMail($token, $user->email));
        });

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'We have e-mailed your password reset link! Please check your email. '], 200)
            : response()->json(['error' => 'Unable to send reset link'], 400);
    }

    /**
     * Handle Reset Password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed|min:8',
        ]);

        // Reset password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => ' Your Password has been reset successfully!'], 200)
            : response()->json(['error' => 'Invalid token or email'], 400);
    }
    /**
     * Handle password change for the authenticated user
     */
    public function changePassword(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'current_password' => 'required',  // Current password
            'new_password' => 'required|confirmed|min:8',  // New password and confirmation
        ]);

        // Get the authenticated user
        $user = $request->user();

        // Check if the current password matches the stored password
        if (!Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The provided password does not match our records.'],
            ]);
        }

        // Update the password with the new one
        $user->password = Hash::make($validated['new_password']);
        $user->save();

        // Return a success response
        return response()->json(['message' => 'Password changed successfully'], 200);
    }
}
