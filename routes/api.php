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
<<<<<<< HEAD
use App\Http\Controllers\Candidate\UserController;

=======
use App\Http\Controllers\Employer\CompanyController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\Candidate\JobApplicationController;
>>>>>>> fc4dd0fe32e814643a60c7bea8874b17e85626f9
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
    Route::apiResource('/companies', CompanyController::class);
});



Route::get('/jobs', [JobController::class, 'index']);
Route::get('/jobs/{id}', [JobController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
Route::post('/jobs/{id}/apply', [JobApplicationController::class, 'apply']);
});
