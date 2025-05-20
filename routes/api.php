<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Models\User;

use App\Models\UserProfile;
use App\Models\UserSocialLinks;
use App\Models\UserResume;
use Illuminate\Auth\Events\Verified;
use App\Http\Controllers\Candidate\UserController;

use App\Http\Controllers\Employer\CompanyController;
// use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\JobPostController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Candidate\JobApplicationController;
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');


// admin 
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/job-posts', [JobPostController::class, 'index']);
    Route::get('/job-posts/{id}', [JobPostController::class, 'show']);
    Route::post('/job-posts/{id}/approve', [JobPostController::class, 'approve']);
    Route::post('/job-posts/{id}/reject', [JobPostController::class, 'reject']);
    // Route::get('/dashboard', [AdminController::class, 'dashboard']);

});
// auth

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])->name('login');


Route::get('/email/verify/{id}/{hash}', function (
    Request $request,
    $id,
    $hash
) {
    $user = User::findOrFail($id);

    if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        abort(403, 'Invalid verification link.');
    }

    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email already verified.']);
    }

    $user->markEmailAsVerified();
    event(new Verified($user));

    return response()->json(['message' => 'Email verified successfully.']);
})->middleware(['signed'])->name('verification.verify');




Route::post('/email/resend', function (Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email already verified.']);
    }

    $request->user()->sendEmailVerificationNotification();

    return response()->json(['message' => 'Verification link sent.']);
})->middleware(['auth:sanctum'])->name('verification.send');



Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('users', UserController::class);
    Route::delete('users/{userId}/resumes/{resumeId}', [UserController::class, 'deleteCV']);
    Route::post('/user/verify-password', [UserController::class, 'verifyPassword']);
    Route::put('/user/password', [UserController::class, 'updatePassword']);
     Route::post('/users/{user}/resumes', [UserController::class, 'uploadResume']);
     Route::post('/users/{user}', [UserController::class, 'update']);

});

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user()->load(['profile', 'socialLinks', 'resumes']);
});


// Route::put('/user/password', [UserController::class, 'updatePassword']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/companies/profile', [CompanyController::class, 'getCompanyProfile']);
});

// company
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('/companies', CompanyController::class);
});



// jobs
Route::get('/jobs', [JobController::class, 'index']);
Route::get('/jobs/{id}', [JobController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
Route::post('/jobs/{id}/apply', [JobApplicationController::class, 'apply']);
Route::get('/applications', [JobApplicationController::class, 'getApplications']);
});
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/job-types', [JobController::class, 'jobTypes']);
  
// cv
Route::delete('users/{userId}/resumes/{resumeId}', [UserController::class, 'deleteCV']);

Route::middleware('auth:sanctum')->get('/user/resumes', [UserController::class, 'userResumes']);
