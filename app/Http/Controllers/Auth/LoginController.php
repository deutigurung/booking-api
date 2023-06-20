<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $user = User::where('email',$request->get('email'))->first();
        if(!$user || !Hash::check($request->get('password') , $user->password))
        {
            // HTTP_UNPROCESSABLE_ENTITY  code 422 which is exception code
            return response()->json([
                'error' => 'Invalid credentials'
            ],422);
        }
        $expiresAt = $request->get('remember') ? null : now()->addMinutes(config('session.lifetime'));
        $token = 'client'.'expireAt:'.$expiresAt;
        return response()->json([
            'access_token' => $user->createToken($token)->plainTextToken
        ],201);
    }
}
