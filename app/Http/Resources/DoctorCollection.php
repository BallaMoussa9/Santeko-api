<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DoctorCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        $data = $this->collection;

        // Vérifie si c'est un Paginator (avec méthodes currentPage, total, etc.)
        if (method_exists($this->resource, 'currentPage')) {
            $meta = [
                'current_page' => $this->resource->currentPage(),
                'from' => $this->resource->firstItem(),
                'last_page' => $this->resource->lastPage(),
                'per_page' => $this->resource->perPage(),
                'to' => $this->resource->lastItem(),
                'total' => $this->resource->total(),
            ];

            $links = [
                'first' => $this->resource->url(1),
                'last' => $this->resource->url($this->resource->lastPage()),
                'prev' => $this->resource->previousPageUrl(),
                'next' => $this->resource->nextPageUrl(),
            ];
        } else {
            $meta = null;
            $links = null;
        }

        return [
            'data' => $data,
            'meta' => $meta,
            'links' => $links,
        ];
    }
}
