<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use App\Http\Controllers\Candidate\UserController;
use App\Http\Controllers\Candidate\DashboardController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Employer\CompanyController;
use App\Http\Controllers\Employer\JobController as EmployerJobController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\JobPostController;
use App\Http\Controllers\JobController;

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Candidate\JobApplicationController;


// admin 

Route::prefix('admin')->group(function () {
    Route::get('/job-moderation', [AdminController::class, 'index'])->name('api.admin.job.moderation.index');
    Route::post('/job/{id}/approve', [AdminController::class, 'approve'])->name('api.admin.job.approve');
    Route::post('/job/{id}/reject', [AdminController::class, 'reject'])->name('api.admin.job.reject');
    Route::get('/jobs/pending', [AdminController::class, 'pending'])->name('api.admin.job.pending');
    Route::get('/jobs/approved', [AdminController::class, 'approved'])->name('api.admin.job.approved');
    Route::get('/jobs/rejected', [AdminController::class, 'rejected'])->name('api.admin.job.rejected');
    Route::get('/job/{id}', [AdminController::class, 'show'])->name('api.admin.job.show');
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



Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('/myjobs', EmployerJobController::class);
});





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


Route::middleware(['auth:sanctum'])->group(function () {
    // Job Applications
    Route::post('/jobs/{job}/apply', [JobApplicationController::class, 'apply']);
    Route::get('/jobs/{job}/applications', [JobApplicationController::class, 'getJobApplications']);
    Route::get('/applications/{id}/resume', [JobApplicationController::class, 'downloadResume']);
    Route::patch('/applications/{application}', [JobApplicationController::class, 'updateStatus']);
});




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
Route::get('/latest-jobs', [JobController::class, 'latestJobs']);
});

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/job-types', [JobController::class, 'jobTypes']);



Route::delete('users/{userId}/resumes/{resumeId}', [UserController::class, 'deleteCV']);

Route::middleware('auth:sanctum')->get('/user/resumes', [UserController::class, 'userResumes']);

Route::middleware('auth:sanctum')->get('/candidate/dashboard', [DashboardController::class, 'dashboard']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
    Route::get('/subscription-status', [SubscriptionController::class, 'status']);
});


