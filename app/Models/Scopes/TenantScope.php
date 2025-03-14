<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $table = $model->getTable();

        if (Auth::check()) {
            $builder->where("$table.tenant_id", Auth::user()->tenant_id);
        }
    }
}
