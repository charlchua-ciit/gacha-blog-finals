<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index() {
        return Notification::with('user')->get();
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string',
            'is_read' => 'boolean',
        ]);

        return Notification::create($validated);
    }

    public function show(Notification $notification) {
        return $notification->load('user');
    }

    public function update(Request $request, Notification $notification) {
        $notification->update($request->only(['message', 'is_read']));
        return $notification;
    }

    public function destroy(Notification $notification) {
        $notification->delete();
        return response()->noContent();
    }
}
