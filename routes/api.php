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
use App\Http\Controllers\Candidate\DashboardController;
use App\Http\Controllers\Employer\CompanyController;
use App\Http\Controllers\Admin\JobPostController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\Employer\JobController as EmployerJobController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Candidate\JobApplicationController;
use App\Http\Controllers\Admin\JobModerationController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\AdminController;

// Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Email Verification
Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = User::findOrFail($id);

    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
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

// Public Job Routes
Route::get('/jobs', [JobController::class, 'index']);
Route::get('/jobs/{id}', [JobController::class, 'show']);
Route::get('/latest-jobs', [JobController::class, 'latestJobs']);
Route::get('/job-types', [JobController::class, 'jobTypes']);
Route::get('/categories', [CategoryController::class, 'index']);

// Authenticated Routes
Route::middleware(['auth:sanctum'])->group(function () {
    // User Routes
    Route::get('/user', function (Request $request) {
        return $request->user()->load(['profile', 'socialLinks', 'resumes']);
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // User Profile Routes
    Route::apiResource('users', UserController::class);
    Route::post('/users/{user}', [UserController::class, 'update']);
    Route::post('/user/verify-password', [UserController::class, 'verifyPassword']);
    Route::put('/user/password', [UserController::class, 'updatePassword']);
    Route::post('/users/{user}/resumes', [UserController::class, 'uploadResume']);
    Route::delete('users/{userId}/resumes/{resumeId}', [UserController::class, 'deleteCV']);
    Route::get('/user/resumes', [UserController::class, 'userResumes']);

    // Candidate Dashboard
    Route::get('/candidate/dashboard', [DashboardController::class, 'dashboard']);

    // Company Routes
    Route::apiResource('/companies', CompanyController::class);
    Route::get('/companies/profile', [CompanyController::class, 'getCompanyProfile']);

    // Job Application Routes
    Route::post('/jobs/{job}/apply', [JobApplicationController::class, 'apply']);
    Route::get('/applications', [JobApplicationController::class, 'getApplications']);
    Route::get('/jobs/{job}/applications', [JobApplicationController::class, 'getJobApplications']);
    Route::get('/applications/{id}/resume', [JobApplicationController::class, 'downloadResume']);
    Route::patch('/applications/{application}', [JobApplicationController::class, 'updateStatus']);

    // Subscription Routes
    Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
    Route::get('/subscription-status', [SubscriptionController::class, 'status']);

    // Job Routes
    Route::apiResource('/jobs', JobController::class);
});

// Employer-specific Routes
Route::middleware(['auth:sanctum', 'role:employer'])->group(function () {
    Route::post('/jobs', [EmployerJobController::class, 'store']);
});

// Admin Routes
Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/job-moderation', [AdminController::class, 'index'])->name('api.admin.job.moderation.index');
    Route::post('/job/{id}/approve', [AdminController::class, 'approve'])->name('api.admin.job.approve');
    Route::post('/job/{id}/reject', [AdminController::class, 'reject'])->name('api.admin.job.reject');
    Route::get('/jobs/pending', [AdminController::class, 'pending'])->name('api.admin.job.pending');
    Route::get('/jobs/approved', [AdminController::class, 'approved'])->name('api.admin.job.approved');
    Route::get('/jobs/rejected', [AdminController::class, 'rejected'])->name('api.admin.job.rejected');
    Route::get('/job/{id}', [AdminController::class, 'show'])->name('api.admin.job.show');
    
    Route::get('/job-posts', [JobPostController::class, 'index']);
    Route::get('/job-posts/{id}', [JobPostController::class, 'show']);
    Route::post('/job-posts/{id}/approve', [JobPostController::class, 'approve']);
    Route::post('/job-posts/{id}/reject', [JobPostController::class, 'reject']);
});