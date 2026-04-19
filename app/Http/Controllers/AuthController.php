<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\User\UserService;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }


    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => [
                'required',
                'string',
                'regex:/^(0|84)(3|5|7|8|9)([0-9]{8})$/',
                'unique:users'
            ],
            'password' => 'required|string|min:6|max:32|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $result = $this->userService->registerUser($request->all());

            return response()->json([
                'message'      => 'Đăng ký thành công!',
                'access_token' => $result['access_token'],
                'token_type'   => $result['token_type'],
                'user'         => $result['user'],
                'code'         => 200
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'error'   => 'Đăng ký không thành công!',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6|max:32',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $result = $this->userService->loginUser($request->only('email', 'password'));

            return response()->json([
                'message' => 'Đăng nhập thành công!',
                'access_token' => $result['access_token'],
                'token_type' => $result['token_type'],
                'user' => $result['user'],
                'code' => 200
            ], 200);
        } catch (HttpException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'is_verified' => !($e->getStatusCode() === 403)
            ], $e->getStatusCode());

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Đã có lỗi xảy ra hệ thống.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function resendVerificationEmail(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Không tìm thấy người dùng với email này.'], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email này đã được xác thực rồi.'], 200);
        }

        event(new Registered($user));

        return response()->json([
            'message' => 'Link xác thực mới đã được gửi vào hòm thư của bạn!'
        ], 200);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Đăng xuất thành công!',
            'code' => 200
        ], 200);
    }
}
