<?php

namespace App\Repository;

use App\Repository\IBase;

use App\Traits\ResponseTrait;
use Symfony\Component\HttpFoundation\Response;

class Repo implements IBase
{
    use ResponseTrait;
    private $model;
    public function __construct($model)
    {
        return $this->model = app($model);
    }

    public function index(): Response
    {
        return $this->apiResponse('success', $this->model->all());
    }

    public function create(array $request): Response
    {
        // throw exception();
        $createdUser = $this->model->create($request);

        if (!$createdUser) {
            return $this->apiResponse('Failed to create', null, false);
        }
        return $this->apiResponse('Success', $createdUser, 200);
    }

    public function update(array $request, int $id): Response
    {
        $model = $this->model->find($id);
        if (!$model) {
            return $this->apiResponse('Failed to find model', null, false);
        }
        if ($model->update($request)) {

            return $this->apiResponse('success', $model);
        } else {
            return $this->apiResponse('Failed to update model', null, false);
        }
    }

    public function delete(int $id): bool
    {
        $model = $this->model->find($id);
        if (!$model) {
            return false;
        }
        $model->delete($model);
        return true;
    }
}
