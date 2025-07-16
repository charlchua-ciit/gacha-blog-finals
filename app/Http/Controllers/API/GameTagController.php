<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\GameTag;
use Illuminate\Http\Request;

class GameTagController extends Controller
{
    public function index() {
        return GameTag::with('posts')->get();
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'tag_name' => 'required|string|unique:game_tags',
        ]);

        return GameTag::create($validated);
    }

    public function show(GameTag $gameTag) {
        return $gameTag->load('posts');
    }

    public function update(Request $request, GameTag $gameTag) {
        $gameTag->update($request->only('tag_name'));
        return $gameTag;
    }

    public function destroy(GameTag $gameTag) {
        $gameTag->delete();
        return response()->noContent();
    }
}

