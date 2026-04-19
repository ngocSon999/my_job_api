<?php

use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/email/verify', function () {
    return response()->json(['message' => 'Vui lòng xác thực email của bạn.'], 401);
})->name('verification.notice');


Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = User::findOrFail($id);

    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        return response()->json(['message' => 'Link xác thực không hợp lệ.'], 403);
    }

    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email này đã được xác thực từ trước.']);
    }

    $user->markEmailAsVerified();

    return response()->json(['message' => 'Xác thực email thành công!']);
})->name('verification.verify');

// gửi lại email xác thực
Route::post('/email/resend', [AuthController::class, 'resendVerificationEmail']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});
