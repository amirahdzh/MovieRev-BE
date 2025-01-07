<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use App\Models\OtpCode;
use App\Mail\RegisterMail;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function generateOtpCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $userData = User::where('email', $request->email)->first();

        // check if user exist
        if (!$userData) {
            return response()->json([
                "message" => "User not found"
            ], 404);
        }

        $userData->generateOtpCode();

        Mail::to($userData->email)->queue(new RegisterMail($userData));

        return response()->json([
            "message" => "berhasil generate ulang otp code",
            "data" => $userData
        ]);
    }

    public function verification(Request $request)
    {
        $request->validate([
            'otp' => 'required'
        ]);

        // check if otp code exist in otp_codes table
        $otp_code = OtpCode::where('otp', $request->otp)->first();
        if (!$otp_code) {
            return response()->json([
                "message" => "OTP not found"
            ], 404);
        }

        // check if OTP expired or not
        $now = Carbon::now();
        if ($now > $otp_code->valid_until) {
            return response()->json([
                "message" => "Expired. Please regenerate"
            ], 400);
        }

        // update User
        $user = User::find($otp_code->user_id);
        $user->email_verified_at = $now;

        $user->save();

        $otp_code->delete();

        $token = JWTAuth::fromUser($user);

        return response()->json([
            "message" => "Account verification success",
            "user" => $user,
            "token" => $token
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $roleUser = Role::where('name', 'user')->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $roleUser->id,
        ]);

        $user->generateOtpCode();

        $token = JWTAuth::fromUser($user);

        Mail::to($user->email)->queue(new RegisterMail($user));

        return response()->json([
            "message" => "Register berhasil",
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function getUser()
    {
        $user = auth()->user();
        // Eager load profile dan role
        $currentUser = User::with(['profile', 'role'])->find($user->id);

        return response()->json([
            "message" => "berhasil mendapatkan user",
            "user" => [
                'id' => $currentUser->id,
                'name' => $currentUser->name,
                'email' => $currentUser->email,
                'role' => $currentUser->role ? $currentUser->role->name : 'Unknown Role',
                'profile' => $currentUser->profile,
                'created_at' => $currentUser->created_at,
                'updated_at' => $currentUser->updated_at,
            ]
        ]);
    }

    public function login(Request $request)
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'User invalid'
            ], 401);
        }

        $userData = User::with('role')->where('email', $request['email'])->first();

        $token = JWTAuth::fromUser($userData);

        return response()->json([
            "message" => "Login berhasil",
            'user' => [
                'id' => $userData->id,
                'name' => $userData->name,
                'email' => $userData->email,
                'email_verified_at' => $userData->email_verified_at,
                'role' => [
                    'id' => $userData->role->id,
                    'name' => $userData->role->name
                ],
                'created_at' => $userData->created_at,
                'updated_at' => $userData->updated_at,
            ],
            'token' => $token,
        ], 201);
    }

    public function updateUser(Request $request)
    {
        // Retrieve the authenticated user
        $user = Auth::user();

        // Check if the user is authenticated
        if (!$user) {
            return response()->json([
                "message" => "User not authenticated"
            ], 401);
        }

        // $data = $request->validated();

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|required|string|min:6|confirmed',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Update user information
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        // Save the updated user information
        $user->save();

        return response()->json([
            "message" => "Akun berhasil diupdate",
            'user' => $user,
        ], 200);
    }

    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }
}
