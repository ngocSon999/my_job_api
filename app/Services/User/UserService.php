<?php

namespace App\Services\User;

use App\Services\BaseService;
use App\Repositories\User\UserRepositoryInterface;
use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService extends BaseService
{
    public function __construct(UserRepositoryInterface $userRepository)
    {
        parent::__construct($userRepository);
    }

    /**
     * @throws Exception
     */
    public function registerUser(array $data): array
    {
        DB::beginTransaction();
        try {
            $user = $this->repository->create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'phone'    => $data['phone'],
                'password' => Hash::make($data['password']),
            ]);

            event(new Registered($user));

            $token = $user->createToken('auth_token')->plainTextToken;

            DB::commit();

            return [
                'user'         => $user,
                'access_token' => $token,
                'token_type'   => 'Bearer'
            ];
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }


    public function updateProfile($id, array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->repository->update($id, $data);
    }

    public function getUserByEmail($email)
    {
        return $this->repository->findByEmail($email);
    }

    public function getUserByPhone($phone)
    {
        return $this->repository->findByPhone($phone);
    }

    public function loginUser(array $credentials): array
    {
        $user = $this->repository->findByEmail($credentials['email']);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new HttpException(401, 'Thông tin đăng nhập không chính xác.');
        }

        if (!$user->hasVerifiedEmail()) {
            throw new HttpException(403, 'Tài khoản của bạn chưa được xác thực email. Vui lòng kiểm tra hộp thư!');
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ];
    }
}
