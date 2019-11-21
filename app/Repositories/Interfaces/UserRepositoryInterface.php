<?php

namespace App\Repositories\Interfaces;

use  \App\User;

interface UserRepositoryInterface
{

    public function save(string $email, string $username, string $password);

    public function createPicture(int $id): string;
}
