<?php

namespace App\Repositories;

use App\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Types\Mixed_;

class UserRepository implements UserRepositoryInterface
{

    /** save User
     * @param string $email
     * @param string $username
     * @param string $password
     * @return User
     */
    public function save(string $email, string $username, string $password)
    {
        $result = null;

        try {
            if (!empty($email) && !empty($username) && !empty($password))
                $result = User::create([
                    'email' => $email,
                    'username' => $username,
                    'password' => $password,
                ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
        //dd([$email,$username,$password]);
        return $result;

    }


    public function getById(int $id): User
    {
        // TODO: Implement getById() method.
    }

    public function update(int $id, array $contentToUpdate): User
    {
        // TODO: Implement update() method.
    }

    public function delete(int $id): bool
    {
        // TODO: Implement delete() method.
    }
}
