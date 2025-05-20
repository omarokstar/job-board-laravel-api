<?php

namespace App\Http\Controllers\Candidate;


use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserSocialLinks;
use App\Models\UserResume;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{


public function uploadResume(Request $request, $userId)
{
    $user = User::findOrFail($userId);
    
    $validator = Validator::make($request->all(), [
        'resumes.*' => 'required|file|mimes:pdf,doc,docx|max:5120'
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    $uploadedResumes = [];
    foreach ($request->file('resumes') as $resumeFile) {
        $path = $resumeFile->store('resumes', 'public');
        $resume = $user->resumes()->create([
            'name' => $resumeFile->getClientOriginalName(),
            'path' => $path,
            'size' => $resumeFile->getSize(),
            'extension' => $resumeFile->getClientOriginalExtension()
        ]);
        $uploadedResumes[] = $resume;
    }

    return response()->json([
        'message' => 'Resumes uploaded successfully',
        'resumes' => $uploadedResumes
    ]);
}
















// Add these methods to your existing UserController
public function verifyPassword(Request $request)
{
    $request->validate([
        'current_password' => 'required|string'
    ]);

    return response()->json([
        'valid' => Hash::check($request->current_password, $request->user()->password)
    ]);
}

public function updatePassword(Request $request)
{
    $validator = Validator::make($request->all(), [
        'current_password' => 'required|string',
        'password' => 'required|string|min:8|confirmed',
        'password_confirmation' => 'required|string|min:8'
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    $user = $request->user();

    if (!Hash::check($request->current_password, $user->password)) {
        return response()->json(['message' => 'Current password is incorrect'], 422);
    }

    $user->password = Hash::make($request->password);
    $user->save();

    return response()->json(['message' => 'Password updated successfully']);
}
















































    public function index()
    {
        $users = User::with(['profile', 'socialLinks', 'resumes'])->get();
        return response()->json($users);
    }

    // Store new user + related data + files upload
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|string|min:6',

            // Optional validations
            'phone'    => 'nullable|string',
            'website'  => 'nullable|url',
            'profile_photo' => 'nullable|image|max:2048', // image max 2MB
            'resumes.*'    => 'nullable|file|mimes:pdf,doc,docx|max:5120', // resumes max 5MB
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Upload profile photo if exists
        $profilePhotoPath = null;
        if ($request->hasFile('profile_photo')) {
            $profilePhotoPath = $request->file('profile_photo')->store('profile_photos', 'public');
        }

        // Create user
        $user = User::create([
            'name'                  => $request->name,
            'email'                 => $request->email,
            'password'              => Hash::make($request->password),
            'phone'                 => $request->phone,
            'website'               => $request->website,
            'profile_photo_path'    => $profilePhotoPath,
            'professional_title'    => $request->professional_title,
        ]);

        // Create profile if data exists
        if ($request->filled('date_of_birth') || $request->filled('education') || $request->filled('nationality')) {
            $user->profile()->create($request->only([
                'nationality', 'date_of_birth', 'gender',
                'marital_status', 'education', 'experience', 'biography'
            ]));
        }

        // Create social links if data exists
        if ($request->filled('linkedin') || $request->filled('github') || $request->filled('twitter') || $request->filled('facebook')) {
            $user->socialLinks()->create($request->only([
                'linkedin', 'twitter', 'github', 'facebook'
            ]));
        }

        // Upload resumes (CVs) if exists
        if ($request->hasFile('resumes')) {
            foreach ($request->file('resumes') as $resumeFile) {
                $path = $resumeFile->store('resumes', 'public');
                $user->resumes()->create([
                    'name' => $resumeFile->getClientOriginalName(),
                    'path' => $path,
                    'size' => $resumeFile->getSize(),
                    'extension' => $resumeFile->getClientOriginalExtension(),
                ]);
            }
        }

        return response()->json(['message' => 'User created successfully', 'user' => $user->load(['profile', 'socialLinks', 'resumes'])], 201);
    }

    // Show specific user with relations
    public function show($id)
    {
        $user = User::with(['profile', 'socialLinks', 'resumes'])->find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($user);
    }

    // Update user + related data + files upload
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'User not found'], 404);

        // Validate (email unique except current user)
        $validator = Validator::make($request->all(), [
            'name'     => 'sometimes|required|string|max:255',
            'email'    => 'sometimes|required|email|unique:users,email,'.$user->id,
            'phone'    => 'nullable|string',
            'website'  => 'nullable|url',
            'profile_photo' => 'nullable|image|max:2048',
            'resumes.*'    => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Update user fields if present
        $user->update($request->only([
            'name', 'email', 'phone', 'website', 'professional_title'
        ]));

        // Handle profile photo update (upload new, delete old)
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $user->profile_photo_path = $path;
            $user->save();
        }

        // Update or create profile
        if ($request->hasAny(['nationality', 'date_of_birth', 'gender', 'marital_status', 'education', 'experience', 'biography'])) {
            $user->profile()->updateOrCreate(
                [], // find by user_id in relation
                $request->only([
                    'nationality', 'date_of_birth', 'gender',
                    'marital_status', 'education', 'experience', 'biography'
                ])
            );
        }

        // Update or create social links
        if ($request->hasAny(['linkedin', 'twitter', 'github', 'facebook'])) {
            $user->socialLinks()->updateOrCreate(
                [],
                $request->only(['linkedin', 'twitter', 'github', 'facebook'])
            );
        }

        // Upload new resumes (add to existing)
        if ($request->hasFile('resumes')) {
            foreach ($request->file('resumes') as $resumeFile) {
                $path = $resumeFile->store('resumes', 'public');
                $user->resumes()->create([
                    'name' => $resumeFile->getClientOriginalName(),
                    'path' => $path,
                    'size' => $resumeFile->getSize(),
                    'extension' => $resumeFile->getClientOriginalExtension(),
                ]);
            }
        }

        return response()->json(['message' => 'User updated successfully', 'user' => $user->load(['profile', 'socialLinks', 'resumes'])]);
    }

    // Delete user and related data
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'User not found'], 404);

        // Delete profile photo file if exists
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        // Delete resumes files
        foreach ($user->resumes as $resume) {
            Storage::disk('public')->delete($resume->path);
        }

        $user->delete(); // cascade deletes profile, socialLinks, resumes

        return response()->json(['message' => 'User deleted successfully']);
    }

    // Delete a specific resume (CV)
    public function deleteCV($userId, $resumeId)
    {
        $cv = UserResume::where('user_id', $userId)->where('id', $resumeId)->first();
        if (!$cv) {
            return response()->json(['message' => 'CV not found'], 404);
        }

        Storage::disk('public')->delete($cv->path);
        $cv->delete();

        return response()->json(['message' => 'CV deleted successfully']);
    }


// 
public function userResumes(Request $request)
{
    $user = $request->user();
    $resumes = $user->resumes()->get(); 
    return response()->json($resumes);
}

}
