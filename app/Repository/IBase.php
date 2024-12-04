<?php

namespace App\Repository;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface IBase
{
    public function index(): Response;
    public function create(array $request): Response;
    public function update(array $request, int $id): Response;
    public function delete(int $id): bool;
    // public function gg();
}
