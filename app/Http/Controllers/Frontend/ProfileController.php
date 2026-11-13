<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /** Show the profile page */
    public function profile()
    {
        $user = Auth::user();
        return view('frontend.profile.index', compact('user'));
    }

    /** Update basic profile information */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image && file_exists(public_path('uploads/profile/' . $user->profile_image))) {
                unlink(public_path('uploads/profile/' . $user->profile_image));
            }
            $filename = time() . '_' . $request->file('profile_image')->getClientOriginalName();
            $request->file('profile_image')->move(public_path('uploads/profile'), $filename);
            $validated['profile_image'] = $filename;
        }

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    /** Change user password */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($validated['new_password'])]);

        return back()->with('success', 'Password changed successfully.');
    }
}
