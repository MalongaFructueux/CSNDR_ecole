<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        return User::all();
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user && password_verify($request->password, $user->password)) {
            return response()->json(['success' => true, 'user' => $user]);
        }
        return response()->json(['success' => false, 'message' => 'Invalid credentials']);
    }
}
