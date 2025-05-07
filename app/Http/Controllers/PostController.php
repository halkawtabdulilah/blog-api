<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Models\Post;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class PostController extends Controller
{

    protected $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    /**
     * @OA\Get(
     * path="/api/post",
     * tags={"Posts"},
     * summary="List posts with filters",
     * description="Returns paginated posts with search, sorting, and category filtering",
     * @OA\Parameter(
     * name="search",
     * in="query",
     * required=false,
     * @OA\Schema(type="string")
     * ),
     * @OA\Parameter(
     * name="category",
     * in="query",
     * description="Filter by category ID",
     * required=false,
     * @OA\Schema(type="integer", example=1)
     * ),
     * @OA\Parameter(
     * name="orderBy",
     * in="query",
     * required=false,
     * @OA\Schema(type="string", enum={"title", "created_at"}, default="created_at")
     * ),
     * @OA\Parameter(
     * name="direction",
     * in="query",
     *     required=false,
     *     @OA\Schema(type="string", enum={"asc", "desc"}, default="desc")
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
     *                 @OA\Items(ref="#/components/schemas/Post")
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

        $category = $request->input('category');

        $query = Post::query()
            ->when($search, function ($q) use ($search) {
                $q->where('title', 'LIKE', "%$search%");
            })
            ->when($category, function ($q) use ($category) {
                $q->where('category_id', $category);
            });

        $query->orderBy($orderBy, $direction);
        $query->with(['category' => function ($query) {
            $query->select('id', 'name', 'slug');
        }]);

        $posts = $query->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'meta' => [
                'page' => $posts->currentPage(),
                'pages' => $posts->lastPage(),
                'limit' => (int) $limit,
                'total' => $posts->total(),
            ],
            'data' => $posts->items()
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/post",
     *     tags={"Posts"},
     *     summary="Create a new post",
     *     description="Creates a new blog post with the provided details",
     *     operationId="createPost",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Post data",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"title", "content", "author", "category_id"},
     *                 @OA\Property(
     *                     property="title",
     *                     type="string",
     *                     maxLength=255,
     *                     example="My First Post",
     *                     description="Unique title of the post"
     *                 ),
     *                 @OA\Property(
     *                     property="content",
     *                     type="string",
     *                     example="This is the post content...",
     *                     description="Full post content"
     *                 ),
     *                 @OA\Property(
     *                     property="author",
     *                     type="string",
     *                     maxLength=255,
     *                     example="John Doe",
     *                     description="Name of the post author"
     *                 ),
     *                 @OA\Property(
     *                     property="category_id",
     *                     type="integer",
     *                     example=1,
     *                     description="ID of an existing category"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Post created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="title", type="string", example="My First Post"),
     *             @OA\Property(property="content", type="string", example="This is the post content..."),
     *             @OA\Property(property="author", type="string", example="John Doe"),
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(
     *                 property="category",
     *                 ref="#/components/schemas/Post"
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
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
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
     *                     property="title",
     *                     type="array",
     *                     @OA\Items(
     *                         type="string",
     *                         example="The title field is required."
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="category_id",
     *                     type="array",
     *                     @OA\Items(
     *                         type="string",
     *                         example="The selected category id is invalid."
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request) {

        $validated = $request->validate([
            "title" => "required|string|max:255",
            "content" => "required|string",
            "author" => "required|string|max:255",
            "category_id" => "required|exists:categories,id",
        ]);

        $post = Post::create($validated);

        if($post) {
            $this->activityLogService->logActivity("CREATE", Post::class, $post->id, "John Doe");
        }

        return response()->json($post, 201);

    }

    /**
     * @OA\Get(
     *     path="/api/post/{id}",
     *     tags={"Posts"},
     *     summary="Get a single post",
     *     description="Returns detailed information about a specific post including its category",
     *     operationId="getPostById",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the post to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="title", type="string", example="Sample Post Title"),
     *             @OA\Property(property="content", type="string", example="Full post content here..."),
     *             @OA\Property(property="author", type="string", example="John Doe"),
     *             @OA\Property(property="category_id", type="integer", example=2),
     *             @OA\Property(
     *                 property="category",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=2),
     *                 @OA\Property(property="name", type="string", example="Technology"),
     *                 @OA\Property(property="slug", type="string", example="tech")
     *             ),
     *             @OA\Property(
     *                 property="created_at",
     *                 type="string",
     *                 format="date-time",
     *                 example="2023-05-01T12:00:00Z"
     *             ),
     *             @OA\Property(
     *                 property="updated_at",
     *                 type="string",
     *                 format="date-time",
     *                 example="2023-05-01T12:30:00Z"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Post not found")
     *         )
     *     )
     * )
     */
    public function show(Request $request, Post $post)
    {

        if($post) {
            $this->activityLogService->logActivity("READ", Post::class, $post->id, "John Doe");
        }

        return response()->json($post->load(['category' => function ($query) {
            $query->select('id', 'name', 'slug');
        }]));
    }

    /**
     * Update a post's details.
     *
     * @OA\Patch(
     *     path="/api/post/{post}",
     *     tags={"Posts"},
     *     summary="Update a post",
     *     description="Partially update a post's title, content, or category",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="post",
     *         in="path",
     *         required=true,
     *         description="Post ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", maxLength=255, nullable=true, example="Updated Title"),
     *             @OA\Property(property="content", type="string", nullable=true, example="Updated content"),
     *             @OA\Property(property="category_id", type="integer", nullable=true, example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Post updated successfully")
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
     *                     property="category_id",
     *                     type="array",
     *                     @OA\Items(type="string", example="The selected category id is invalid.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function update(Request $request, Post $post)
    {

        $validated = $request->validate([
            "title" => "nullable|string|max:255",
            "content" => "nullable|string",
            "category_id" => "nullable|exists:categories,id",
        ]);

        $changedFields = $this->activityLogService->getUpdatedFields(Post::class, $post, $validated);

        $postIsUpdated = $post->update($validated);

        if($postIsUpdated) {
            // TODO: replace dummy actor with real users
            $this->activityLogService->logActivity("UPDATE", Post::class, $post->id, "John Doe", $changedFields);
        }

        return response()->json(['message' => 'Post updated successfully']);
    }

    /**
     * Delete a post.
     *
     * @OA\Delete(
     *     path="/api/post/{post}",
     *     tags={"Posts"},
     *     summary="Delete a post",
     *     description="Soft Deletes a post from the system",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="post",
     *         in="path",
     *         required=true,
     *         description="Post ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Post deleted successfully",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Post not found")
     *         )
     *     )
     * )
     */
    public function destroy(Request $request, Post $post)
    {
        $postIsDeleted = $post->delete();

        if($postIsDeleted) {
            $this->activityLogService->logActivity("DELETE", Post::class, $post->id, "John Doe");
        }

        return response()->noContent();
    }

}
