<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Storage;



function store_login_logs($log_file_name, $username, $ip_address, $is_logged_in_correctly, $user_info)
{
    $header_row = "date, user_name, ip_address, is_logged_correctly, user_info \r\n";
    $data_row = date("Y.m.d H:i:s") . ", " . $username . ", " . $ip_address . ", " . $is_logged_in_correctly . ", " . $user_info;

    if (Storage::disk('local')->exists($log_file_name)) {
        Storage::disk('local')->append($log_file_name, $data_row);
    } else {
        Storage::disk('local')->put($log_file_name, $header_row . $data_row);
    }
}

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $log_file_name = 'loging_in_logs.csv';

        $fields = $request->validate(['name' => 'required|string', 'password' => 'required|string']);

        store_login_logs($log_file_name, $fields['name'], $request->ip(), true, $request->server('HTTP_USER_AGENT'));

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

        store_login_logs($log_file_name, $fields['name'], $request->ip(), $user ? "1" : "0", $request->server('HTTP_USER_AGENT'));

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