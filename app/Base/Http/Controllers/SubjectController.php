<?php

namespace App\Base\Http\Controllers;

use App\Base\Repositories\SubjectRepository;
use App\Base\Http\Requests\SubjectStoreRequest;

class SubjectController extends Controller
{
    private $repository;

    public function __construct(SubjectRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $subjects = $this->repository->getAllSubjects();

        return response()->json([
            'status'     => 'success',
            'subjects' => $subjects,
        ]);
    }

    public function store(SubjectStoreRequest $request)
    {
        $subject = $this->repository->create($request->all());

        return $this->successResponse(
            'misc.New subject has been created',
            'subject',
            $subject,
            201
        );
    }
}
