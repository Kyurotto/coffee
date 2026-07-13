<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Password;
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
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * AJAX / JSON profile update used by the inline dashboard modal.
     */
    public function ajaxUpdate(Request $request): JsonResponse
    {
        $user = Auth::user();

        $rules = [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ];

        if ($request->filled('new_password')) {
            $rules['current_password'] = ['required'];
            $rules['new_password']     = ['required', 'confirmed', Password::min(8)];
        }

        $validated = $request->validate($rules, [
            'name.required'             => 'Name is required.',
            'email.required'            => 'Email is required.',
            'email.email'               => 'Please enter a valid email.',
            'email.unique'              => 'That email is already taken.',
            'current_password.required' => 'Enter your current password to change it.',
            'new_password.required'     => 'New password is required.',
            'new_password.confirmed'    => 'Passwords do not match.',
            'new_password.min'          => 'New password must be at least 8 characters.',
        ]);

        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'errors' => ['current_password' => ['Current password is incorrect.']],
                ], 422);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->name  = $validated['name'];
        $user->email = $validated['email'];
        $user->save();

        return response()->json([
            'success' => true,
            'name'    => $user->name,
            'email'   => $user->email,
            'initial' => strtoupper(substr($user->name, 0, 1)),
        ]);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
