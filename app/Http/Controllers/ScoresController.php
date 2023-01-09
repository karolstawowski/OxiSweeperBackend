<?php

namespace App\Http\Controllers;

use App\Models\Scores;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class ScoresController extends Controller
{
    public function index()
    {
        return Scores::with(['userId' => function ($query) {
            $query->select('id', 'name');
        }])->orderBy('difficulty_level', 'desc')->orderBy('score', 'asc')->get(['id', 'score', 'difficulty_level', 'user_id']);
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'score' => 'required|integer|numeric',
            'user_token' => 'required|string',
            'difficulty_level' => 'required|in:1,2,3',
            'id' => 'optional'
        ]);

        $token = PersonalAccessToken::findToken($fields['user_token'])->first();

        $user = $token->tokenable;

        if (!$user) {
            return response([
                'message' => 'User with provided ID does not exist.'
            ], 401);
        }

        Scores::create([
            'score' => $fields['score'],
            'user_id' => $user->id,
            'difficulty_level' => $fields['difficulty_level']
        ]);

        return response([
            'message' => 'New score has been added successfully.'
        ], 200);
    }

    public function search($user_token, $diff_level)
    {
        $token = PersonalAccessToken::findToken($user_token)->first();

        $user = $token->tokenable;

        if (!$user) {
            return response([
                'message' => 'User with provided user token does not exist.'
            ], 401);
        }

        $score = Scores::where('user_id', "=", $user->id)->where('difficulty_level', '=', $diff_level)->orderBy('score', 'asc')->limit(1)->get('score')->first();

        if (is_null($score)) {
            return response([
                'message' => -1
            ], 200);
        }

        return $score;
    }
}