<?php

namespace App\Repositories\Interfaces;

use  \App\User;

interface UserRepositoryInterface
{
    public function getById(int $id): User;

    public function save(string $email, string $username, string $password);

    public function update(int $id, array $contentToUpdate): User;

    public function delete(int $id): bool;
}
