<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

abstract class QueryFilter
{
    protected Builder $builder;
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        foreach ($this->request->all() as $name => $value) {
            if ($value === null || $value === '') {
                continue; // On ignore les champs vides
            }

            $method = Str::camel($name);

            if (method_exists($this, $method)) {
                // Gestion des listes séparées par des virgules
                if (is_string($value) && str_contains($value, ',')) {
                    $value = explode(',', $value);
                }
                $this->{$method}($value);
            }
        }

        return $this->builder;
    }
}
