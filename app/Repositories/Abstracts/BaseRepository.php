<?php


namespace App\Repositories\Abstracts;


use App\Repositories\Interfaces\IRepository;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements IRepository
{
    protected $model;

    protected function __construct(Model $model)
    {
        $this->model = $model;
    }

}
