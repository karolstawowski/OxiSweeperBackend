<?php

namespace App\Http\Controllers;

use App\Models\Scores;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class ScoresController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Scores::with(['userId' => function ($query) {
            $query->select('id', 'name');
        }])->get(['id', 'score', 'difficulty_level', 'user_id']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
}