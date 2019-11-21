<?php

namespace App\Repositories;

use App\Repositories\Abstracts\BaseRepository;
use App\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Utils\ImageManipulator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * Repository constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

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
                $result = $this->model->create([
                    'email' => $email,
                    'username' => $username,
                    'password' => $password,
                ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
        return $result;

    }

    /**
     * Update user info
     * @param int $id
     * @param array $contentToUpdate
     * @return |null
     */
    public function update(int $id, array $contentToUpdate)
    {
        $result = null;

        try {
            $user = $this->model->Find($id);
            foreach ($contentToUpdate as $key => $value)
                $user->$key = $value;

            $result = $user->save();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
        return $result;
    }

    /**
     * Delete user
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $result = false;
        try {
            $result = $this->model->destroy($id) > 0;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
        return $result;
    }

    /**
     * Create avatar Picture
     * @param int $id
     * @return string
     */
    public function createPicture(int $id): string
    {
        $result = null;
        try {
            $filename = $id . ".jpg";

            // Create identicon
            ImageManipulator::createIdenticon($id, $filename);

            // put it in storage using avatar
            $result = Storage::put('public/avatars/' . $filename, File::get($filename));
            unlink($filename);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
        return $result;


    }
}
