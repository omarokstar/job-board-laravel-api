<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use App\Http\Controllers\Candidate\UserController;
use App\Http\Controllers\Employer\CompanyController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\JobPostController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\Candidate\JobApplicationController;


// admin 
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/job-posts', [JobPostController::class, 'index']);
    Route::get('/job-posts/{id}', [JobPostController::class, 'show']);
    Route::post('/job-posts/{id}/approve', [JobPostController::class, 'approve']);
    Route::post('/job-posts/{id}/reject', [JobPostController::class, 'reject']);
    Route::get('/dashboard', [AdminController::class, 'dashboard']);

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
    // Route::get('/user', function (Request $request) {
    //     return $request->user();
    // });

    Route::post('/logout', [AuthController::class, 'logout']);
Route::apiResource('users', UserController::class);

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
});
  
// cv
Route::delete('users/{userId}/resumes/{resumeId}', [UserController::class, 'deleteCV']);

