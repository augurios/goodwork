<?php

namespace App\Base\Repositories;

use App\Base\Models\Subject;

class SubjectRepository
{
    protected $model;

    public function __construct(Subject $subject)
    {
        $this->model = $subject;
    }

    public function getAllSubjects()
    {
        return $this->model->get();
    }

    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }
}
