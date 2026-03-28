<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait HasSearch
{
    #[Scope]
    public function filters(Builder $query, array $filters): Builder
    {
        return $query->when($filters['search'] ?? null, function (Builder $query, string $search) {
            $query->where(function (Builder $query) use ($search) {
                foreach ($this->getSearchable() as $column) {
                    $query->orWhere($column, 'like', "%{$search}%");
                }
            });
        });
    }

    protected function getSearchable(): array
    {
        if (isset($this->searchable)) {
            return $this->searchable;
        }

        return [];
    }
}
