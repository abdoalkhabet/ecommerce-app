<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Models\User;
use App\Models\PersonalAccessToken;
use Exception;


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
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // Update the user with the validated data
            $user->update($request->only(['phone', 'name']));

            // Handle the photo upload if it exists
            if ($request->hasFile('photo')) {
                $user->clearMediaCollection('user_photos');
                $user->addMediaFromRequest('photo')->toMediaCollection('user_photos');
            }
            $photo = $user->getFirstMedia('user_photos');
            $photoUrl = $photo ? $photo->getUrl() : null;
            return response()->json(
                [
                    'message' => 'Profile updated successfully',
                    'user' => $user,
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