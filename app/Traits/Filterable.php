<?php
// app/Traits/Filterable.php
namespace App\Traits;

use Illuminate\Http\Request;

trait Filterable
{
    public function scopeApplyFilters($query, Request $request, array $fields)
    {
        foreach ($fields as $field) {
            if ($request->filled($field)) {
                $query->where($field, 'like', "%{$request->$field}%");
            }
        }

        if ($request->filled('sort_by')) {
            $query->orderBy($request->sort_by, $request->get('order', 'asc'));
        }

        return $query;
    }
}
