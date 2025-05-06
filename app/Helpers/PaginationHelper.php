<?php

namespace App\Helpers;

use Illuminate\Http\Request;

class PaginationHelper
{
    /**
     * Get the pagination parameters.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public static function getPaginationParams(Request $request): array
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $search = $request->input('search', null);
        $orderBy = $request->input('sort', 'id');
        $direction = $request->input('direction', 'asc');

        // Validate sorting direction
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        return compact('page', 'limit', 'search', 'orderBy', 'direction');
    }
}
