<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index() {
        return User::with(['posts', 'followers', 'following'])->get();
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        return User::create($validated);
    }

    public function show(User $user) {
        return $user->load(['posts', 'followers', 'following']);
    }

    public function update(Request $request, User $user) {
        $user->update($request->only(['username', 'email']));
        return $user;
    }

    public function destroy(User $user) {
        $user->delete();
        return response()->noContent();
    }
}

