<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InteractionController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (Không cần đăng nhập)
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Route thông báo yêu cầu xác thực email (cho API thường trả về lỗi 403/401)
Route::get('/email/verify', function () {
    return response()->json(['message' => 'Vui lòng xác thực email của bạn để tiếp tục.'], 403);
})->name('verification.notice');

// Xác thực email (Route này dùng Signed URL gửi qua mail nên không để middleware auth cứng ở đây)
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify');


/*
|--------------------------------------------------------------------------
| Protected Routes (Phải có tài khoản & Đã xác thực Email)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    // Lấy thông tin user hiện tại
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Gửi lại email xác thực (chỉ cho người đã login nhưng chưa verify)
    Route::post('/email/resend', [AuthController::class, 'resendVerificationEmail'])
        ->withoutMiddleware('verified');

    // Quản lý bài đăng (Posts)
    Route::prefix('posts')->group(function () {
        Route::get('/my-posts', [PostController::class, 'myPosts']);
        Route::get('/', [PostController::class, 'index']);
        Route::get('/{id}', [PostController::class, 'show']);
        Route::post('/', [PostController::class, 'store']);
        Route::put('/{id}', [PostController::class, 'update']);
        Route::delete('/{id}', [PostController::class, 'destroy']);
    });

    // Quản lý tương tác/kết nối (Interactions)
    Route::prefix('interactions')->group(function () {
        Route::get('/', [InteractionController::class, 'index']);
        Route::get('/{id}', [InteractionController::class, 'show']);
        Route::post('/', [InteractionController::class, 'store']);
        Route::put('/{id}', [InteractionController::class, 'update']);
        Route::delete('/{id}', [InteractionController::class, 'destroy']);
    });
});
