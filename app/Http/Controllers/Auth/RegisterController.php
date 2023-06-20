<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required','string'],
            'email' => ['required','string','email','unique:users,email'],
            'password' => ['required','confirmed',Password::defaults()],
            'role_id' => ['required',Rule::in(2,3)]
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);
        return response()->json([
            'access_token' => $user->createToken('client')->plainTextToken
        ]);
    }
}
