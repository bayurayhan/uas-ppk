<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    protected $memberId;
    protected $adminId;

    public function __construct()
    {
        $this->memberId = Role::where("access_level", "1")->first()->id;
        $this->adminId = Role::where("access_level", "0")->first()->id;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();

        return response()->json([
            'message' => "All users listed in database",
            'users' => $users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function register()
    {
        request()->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'bio' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => request()->name,
            'email' => request()->email,
            'password' => Hash::make(request()->password),
            'bio' => request()->bio,
            'role_id' => $this->memberId,
            'email_verified_at' => now(),
        ]);

        return response()->json([
            'data' => $user,
            'message' => 'User created successfully.'
        ])->header('Content-Type', 'application/json');
    }

    public function showProfile()
    {
        $user = request()->user();

        return response()->json([
            'message' => "Profile for " . $user->name,
            'data' => $user
        ]);
    }

    /**
     * Edit profile
     * 
     * This request required authentication
     */
    public function editProfile()
    {
        $user = request()->user();

        // User can only update name and bio fields

        // Validate user input
        request()->validate([
            'name' => 'required|string|max:255',
            'bio' => 'required|string',
        ]);

        $user->name = request()->name;
        $user->bio = request()->bio;
        $user->save();

        return response()->json([
            'message' => "Profile for " . $user->name . " updated successfully",
            'data' => $user
        ]);
    }

    /**
     * Change password
     * 
     * This request required authentication
     */
    public function changePassword()
    {
        $user = request()->user();

        // Validate user input
        request()->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user->password = Hash::make(request()->password);
        $user->save();

        return response()->json([
            'message' => "Password for " . $user->name . " updated successfully",
            'data' => $user
        ]);
    }
}
