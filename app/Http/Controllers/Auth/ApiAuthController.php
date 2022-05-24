<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Exception;


// Unliike others Auth controller, this one is suitable for API calls
// and has customizable response.
class ApiAuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:3', 'confirmed'],
            'role' => ['required', 'in:author,reader'],
            'receive_notifications' => ['nullable', 'boolean']
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $user = null; // For scope visibility.

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'api_token' => Str::random(60),
                'is_author' => $request->role === 'author',
                'receives_notifications' => $request->receive_notifications
            ]);
        } catch(Exception $e) {
            return response()->json('Unknown error occured. Please try again later.', 422);
        }

        return response()->json(\App\Http\Resources\User::make($user), 200);
    }

    public function login(Request $request)
    {
        $email = $request->email;

        if (Auth::attempt (['email' => $email, 'password' => $request->password])) {
            return response()->json(\App\Http\Resources\User::make(
                User::where('email', $email)->first()
            ), 200);
        }

        return response()->json('Invalid login credentials.', 422);
    }
}
