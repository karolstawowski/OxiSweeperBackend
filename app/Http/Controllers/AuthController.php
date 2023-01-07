<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate(['name' => 'required|string', 'password' => 'required|string']);

        $user = Users::create(['name' => $fields['name'], 'password' => bcrypt($fields['password'])]);

        $token = $user->createToken('apptoken')->plainTextToken;

        $role = DB::table('users')
            ->join('user_roles', 'users.user_role_id', '=', 'user_roles.id')
            ->where('users.id', '=', $user->id)
            ->select('user_roles.role_name')
            ->first()
            ->role_name;

        $response = [
            'user' => $user,
            'token' => $token,
            'role' => $role
        ];

        return response($response, 201);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return ['message' => 'Logged out'];
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = Users::where('name', $fields['name'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Incorrect username or password.'
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $role = DB::table('users')
            ->join('user_roles', 'users.user_role_id', '=', 'user_roles.id')
            ->where('users.id', '=', $user->id)
            ->select('user_roles.role_name')
            ->first()
            ->role_name;

        $response = [
            'user' => $user,
            'token' => $token,
            'role' => $role
        ];

        return response($response, 201);
    }

    public function getRole(Request $request)
    {
        $fields = $request->validate([
            'token' => 'required|string',
        ]);

        $token = PersonalAccessToken::findToken($fields['token'])->first();

        $user = $token->tokenable;

        $role = DB::table('users')
            ->join('user_roles', 'users.user_role_id', '=', 'user_roles.id')
            ->where('users.id', '=', $user->id)
            ->select('user_roles.role_name')
            ->first()
            ->role_name;

        $response = ['role' => $role];

        return response($response, 201);
    }
}