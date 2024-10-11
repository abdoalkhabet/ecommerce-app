<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PersonalAccessToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\MediaLibrary\MediaCollections\Models\Media;


class ProfileController extends Controller
{
    public function form()
    {
        return view('profile');
    }
    public function update(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'User not authenticated'], 401);
            }

            // Validate the incoming request
            $validatedData = $request->validate([
                'phone' => 'required|string|max:15',
                // 'gender' => 'required|string|in:male,female',
                'address' => 'required|string|max:255',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'current_password' => 'nullable|required_with:new_password|string',
                'new_password' => 'nullable|required_with:current_password|string|confirmed|min:8',
            ]);

            // Update the user with the validated data
            $user->update($request->only(['phone', 'name', 'address']));

            // Handle the photo upload if it exists
            if ($request->hasFile('photo')) {
                $user->clearMediaCollection('user_photos');
                $user->addMediaFromRequest('photo')->toMediaCollection('user_photos');
            }
            if ($request->filled('current_password') && $request->filled('new_password')) {
                if (!Hash::check($request->input('current_password'), $user->password)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Current password is incorrect',
                    ], 422);
                }
                $user->update([
                    'password' => Hash::make($request->input('new_password')),
                ]);
            }
            $photo = $user->getFirstMedia('user_photos');
            $photoUrl = $photo ? $photo->getUrl() : null;
            return response()->json(
                [
                    'message' => 'Profile updated successfully',
                    'user' => $user->makeHidden(['media']),
                    'photo' => $photoUrl,

                ],
                200
            );
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation Error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
