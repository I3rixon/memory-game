<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;

class GameController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'clicks' => 'required|integer|min:0',
        ]);

        $game = Game::create([
            'user_id' => $request->user()->id,
            'clicks' => $request->clicks,
        ]);

        return response()->json(['success' => true, 'game_id' => $game->id]);
    }
}
