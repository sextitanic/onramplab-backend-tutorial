<?php

namespace App\Services\Leave;

use Illuminate\Support\Str;

class TypeFactory
{
    public function create(string $type)
    {
        $className = Str::studly($type);
        return app('App\Services\Leave\Types\\' . $className);
    }
}
