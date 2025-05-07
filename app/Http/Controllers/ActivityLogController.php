<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/activities",
     *     tags={"Activity Logs"},
     *     summary="List activity logs with filters",
     *     description="Returns paginated activity logs with action, entity type, and actor filtering",
     *     @OA\Parameter(
     *         name="action",
     *         in="query",
     *         description="Filter by action type",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="entity_type",
     *         in="query",
     *         description="Filter by entity type (e.g., 'post', 'category')",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="actor",
     *         in="query",
     *         description="Filter by actor name or ID",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="orderBy",
     *         in="query",
     *         description="Field to sort by",
     *         required=false,
     *         @OA\Schema(type="string", enum={"created_at", "updated_at"}, default="created_at")
     *     ),
     *     @OA\Parameter(
     *         name="direction",
     *         in="query",
     *         description="Sort direction",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"}, default="desc")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, default=1)
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=100, default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="page", type="integer", example=1),
     *                 @OA\Property(property="pages", type="integer", example=5),
     *                 @OA\Property(property="limit", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=50)
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/ActivityLog")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request) {

        [
            'page' => $page,
            'limit' => $limit,
            'orderBy' => $orderBy,
            'direction' => $direction,
        ] = PaginationHelper::getPaginationParams($request);

        $action = $request->query('action');

        $entityType = $request->query('entity_type'); // e.g., "category" or "post"
        $modelClass = 'App\\Models\\' . ucfirst($entityType);

        $actor = $request->query('actor');

        $query = ActivityLog::query()
            ->when($action, function ($q) use ($action) {
                $q->where('action', 'LIKE', "%$action%");
            })
            ->when($modelClass, function ($q) use ($modelClass) {
                $q->where('entity_type', 'LIKE', "%$modelClass%");
            })
            ->when($actor, function ($q) use ($actor) {
                $q->where('actor', 'LIKE', "%$actor%");
            });

        $query->orderBy($orderBy, $direction);
        $query->with(['category' => function ($query) {
            $query->select('id', 'name', 'slug');
        }]);

        $activities = $query->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'meta' => [
                'page' => $activities->currentPage(),
                'pages' => $activities->lastPage(),
                'limit' => (int) $limit,
                'total' => $activities->total(),
            ],
            'data' => $activities->items()
        ]);
    }


    public function revertToLogVersion() {

    }
}
