<?php

namespace App\Models\Scopes\Global;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ActiveScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): Builder
    {
        if(!request()->routeIs('admin.*')){
            return $builder->where("status", StatusEnum::true->status());
        }
        return $builder;
    }
}
