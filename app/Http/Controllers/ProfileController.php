<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        try {
            $user = $request->user();
            $oldData = [
                'username' => $user->username,
                'email' => $user->email
            ];

            // Fill the user model with validated data
            $user->fill($request->validated());

            // Check if email was changed and reset verification if needed
            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
                Log::info('User email changed, verification reset', [
                    'user_id' => $user->id,
                    'old_email' => $oldData['email'],
                    'new_email' => $user->email
                ]);
            }

            // Check if username was changed
            if ($user->isDirty('username')) {
                Log::info('User username changed', [
                    'user_id' => $user->id,
                    'old_username' => $oldData['username'],
                    'new_username' => $user->username
                ]);
            }

            // Save the changes
            $user->save();

            Log::info('Profile updated successfully', [
                'user_id' => $user->id,
                'changes' => $user->getDirty()
            ]);

            return Redirect::route('profile.edit')
                ->with('status', 'profile-updated')
                ->with('message', 'Profile updated successfully!');

        } catch (\Exception $e) {
            Log::error('Profile update failed', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return Redirect::route('profile.edit')
                ->with('error', 'Failed to update profile. Please try again.')
                ->withInput();
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        try {
            $request->validateWithBag('userDeletion', [
                'password' => ['required', 'current_password'],
            ]);

            $user = $request->user();
            $userId = $user->id;
            $username = $user->username;

            // Log the account deletion
            Log::warning('User account deletion initiated', [
                'user_id' => $userId,
                'username' => $username,
                'email' => $user->email
            ]);

            // Logout the user
            Auth::logout();

            // Delete the user (this will cascade to related data)
            $user->delete();

            // Invalidate and regenerate session
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            Log::info('User account deleted successfully', [
                'user_id' => $userId,
                'username' => $username
            ]);

            return Redirect::to('/')
                ->with('status', 'account-deleted')
                ->with('message', 'Your account has been permanently deleted.');

        } catch (\Exception $e) {
            Log::error('Account deletion failed', [
                'user_id' => $request->user()->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return Redirect::route('profile.edit')
                ->with('error', 'Failed to delete account. Please try again.');
        }
    }
}
