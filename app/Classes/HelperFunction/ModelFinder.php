<?php

namespace App\Classes\HelperFunction;

use Illuminate\Database\Eloquent\Model;

class ModelFinder
{
    public static function findOrNull(string $modelClass, $value, string $column = 'id'): ?Model
    {
        if (!is_subclass_of($modelClass, Model::class)) {
            throw new \Exception("Invalid model class: {$modelClass}");
        }

        return $modelClass::where($column, $value)->first();
    }
}
