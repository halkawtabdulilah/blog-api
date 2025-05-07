<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Models\Category;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    protected $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }


    /**
     * Get a paginated list of categories with optional search and sorting.
     *
     * @OA\Get(
     *     path="/api/categories",
     *     tags={"Categories"},
     *     summary="List all categories",
     *     description="Returns paginated categories with optional filtering and sorting",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term for category names",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Field to sort by (default: id)",
     *         required=false,
     *         @OA\Schema(type="string", default="id")
     *     ),
     *     @OA\Parameter(
     *         name="direction",
     *         in="query",
     *         description="Sort direction (asc/desc)",
     *         required=false,
     *         @OA\Schema(type="string", default="asc", enum={"asc", "desc"})
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
     *                 @OA\Items(ref="#/components/schemas/Category")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request) {

        [
            'page' => $page,
            'limit' => $limit,
            'search' => $search,
            'orderBy' => $orderBy,
            'direction' => $direction,
        ] = PaginationHelper::getPaginationParams($request);

        $query = Category::query();

        if ($search) {
            $query->where('name', 'LIKE', "%$search%");
        }

        // Apply sorting
        $query->orderBy($orderBy, $direction);

        // Paginate results
        $categories = $query->paginate($limit, ['*'], 'page', $page);

        // Return response with pagination meta
        return response()->json([
            'meta' => [
                'page' => $categories->currentPage(),
                'pages' => $categories->lastPage(),
                'limit' => (int) $limit,
                'total' => $categories->total(),
            ],
            'data' => $categories->items()
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/category",
     *     tags={"Categories"},
     *     summary="Create a new category",
     *     description="Creates a new category with the provided details",
     *     operationId="createCategory",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Category data",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"name", "slug"},
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     maxLength=255,
     *                     example="Electronics",
     *                     description="Unique name of the category"
     *                 ),
     *                 @OA\Property(
     *                     property="slug",
     *                     type="string",
     *                     maxLength=255,
     *                     example="electronics",
     *                     description="Unique URL slug for the category"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     maxLength=255,
     *                     example="All electronic devices",
     *                     description="Optional description of the category"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Category created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="id",
     *                 type="integer",
     *                 example=1
     *             ),
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="Electronics"
     *             ),
     *             @OA\Property(
     *                 property="slug",
     *                 type="string",
     *                 example="electronics"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 example="All electronic devices"
     *             ),
     *             @OA\Property(
     *                 property="created_at",
     *                 type="string",
     *                 format="date-time"
     *             ),
     *             @OA\Property(
     *                 property="updated_at",
     *                 type="string",
     *                 format="date-time"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="The given data was invalid."
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     type="array",
     *                     @OA\Items(
     *                         type="string",
     *                         example="The name has already been taken."
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="slug",
     *                     type="array",
     *                     @OA\Items(
     *                         type="string",
     *                         example="The slug has already been taken."
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request) {
        $validated = $request->validate([
            "name" => "required|string|max:255|unique:categories,name",
            "slug" => "required|string|max:255|unique:categories,slug",
            "description" => "nullable|string|max:255",
        ]);

        $category = Category::create($validated);

        return response()->json($category, 201);

    }

    /**
     * Display a single category by ID
     *
     * @OA\Get(
     *     path="/api/categories/{id}",
     *     tags={"Categories"},
     *     summary="Get a single category",
     *     description="Returns category details by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Category")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Category not found")
     *         )
     *     )
     * )
     */
    public function show(Request $request, Category $category) {
        return response()->json($category);
    }

    /**
     * Update a category's details.
     *
     * @OA\Put(
     *     path="/api/categories/{category}",
     *     tags={"Categories"},
     *     summary="Update a category",
     *     description="Updates specified fields of a category",
     *     @OA\Parameter(
     *         name="category",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", maxLength=255, example="Electronics"),
     *             @OA\Property(property="slug", type="string", maxLength=255, example="electronics"),
     *             @OA\Property(property="description", type="string", maxLength=255, example="Tech gadgets", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Category updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     type="array",
     *                     @OA\Items(type="string", example="The name has already been taken.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Category not found")
     *         )
     *     )
     * )
     */
    public function update(Request $request, Category $category) {
        $validated = $request->validate([
            "name" => "nullable|string|max:255|unique:categories,name",
            "slug" => "nullable|string|max:255|unique:categories,slug",
            "description" => "nullable|string|max:255",
        ]);

        $changedFields = $this->activityLogService->getUpdatedFields(Category::class, $category, $validated);

        $categoryIsUpdated = $category->update($validated);

        if($categoryIsUpdated) {
            $this->activityLogService->logActivity("UPDATE", Category::class, $category->id, "John Doe", $changedFields);
        }

        return response()->json(['message' => 'Category updated successfully']);

    }

    /**
     * Delete a category.
     *
     * @OA\Delete(
     *     path="/api/categories/{category}",
     *     tags={"Categories"},
     *     summary="Delete a category",
     *     description="Permanently removes a category from the system",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="category",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Category deleted successfully",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Category not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function destroy(Request $request, Category $category) {
        $category->delete();

        return response()->noContent();
    }
}
