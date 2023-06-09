<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Rules\Password;

class UserController extends Controller
{
    // public function register(Request $request)
    // {
    //     try {
    //         $request->validate([
    //             'name' => ['required', 'string' . 'max:255'],
    //             'email' => ['required', 'string' . 'max:255', 'unique:users'],
    //             'phone' => ['required', 'string', 'max:13'],
    //             'roles' => ['required', 'string'],
    //             'password' => ['required', 'string', new Password],
    //         ]);

    //         User::create([
    //             'name' => $request->name,
    //             'email' => $request->email,
    //             'phone' => $request->phone,
    //             'roles' => $request->roles,
    //             'password' => Hash::make($request->password),
    //         ]);

    //         $user = User::where('email', $request->email)->first();
    //         $tokenResult = $user->createToken('authToken')->plainTextToken;
    //         return ResponseFormatter::success([
    //             'access_token' => $tokenResult,
    //             'token_type' => 'Bearer',
    //             'user' => $user,
    //         ], 'Success to Register');
    //     } catch (Exception $error) {
    //         return ResponseFormatter::error([
    //             'message' => 'Something went wrong',
    //             'error' => $error
    //         ], 'Authenticated Failed', 500);
    //     }
    // }

    public function register(Request $request)
    {
        try {
            $request->validate(
                [
                    'name' => ['required', 'string', 'max:40'],
                    'phone' => ['required', 'string', 'max:13'],
                    'address' => ['required', 'string', 'max:255'],
                    'city' => ['required', 'string', 'max:255'],
                    'roles' => ['required', 'string'],
                    'email' => ['email', 'required'],
                    'password' => ['string', 'required', new Password],
                ]
            );

            User::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'roles' => $request->roles,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user = User::where('email', $request->email)->first();
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user,
            ], 'Success to Register');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Authenticated Failed', 500);
        }
    }
}
