<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Storage;



function store_login_logs($log_file_name, $user_info, $username, $is_logged_in_correctly) {
    if (Storage::disk('local')->exists($log_file_name)) {
        Storage::disk('local')->append($log_file_name, $username . ", " . date("Y.m.d H:i:s") . ", " . $is_logged_in_correctly . ", " . $user_info);
    } else {
        Storage::disk('local')->put($log_file_name, "user_name, date, is_logged_correctly, user_info \r\n" . $username . ", " . date("Y.m.d H:i:s") . ", " . $is_logged_in_correctly . ", " . $user_info);
    }
}

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $log_file_name = 'loging_in_logs.csv';

        $fields = $request->validate(['name' => 'required|string', 'password' => 'required|string']);

        store_login_logs($log_file_name, $request->server('HTTP_USER_AGENT'), $fields['name'], true);

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
        $log_file_name = 'loging_in_logs.csv';

        $fields = $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = Users::where('name', $fields['name'])->first();

        store_login_logs($log_file_name, $request->server('HTTP_USER_AGENT'), $fields['name'], $user ? "1" : "0");

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