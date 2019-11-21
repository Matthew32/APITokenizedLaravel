<?php

namespace App\Repositories\Interfaces;


interface IRepository
{


    public function update(int $id, array $contentToUpdate);

    public function delete(int $id): bool;

}
