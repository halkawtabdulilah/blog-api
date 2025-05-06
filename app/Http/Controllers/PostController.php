<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{

    public function index(Request $request)
    {

    }

    /**
     * @OA\Post(
     *     path="/api/posts",
     *     tags={"Posts"},
     *     summary="Create a new post",
     *     description="Creates a new blog post with the provided details",
     *     operationId="createPost",
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
     */
    public function store(Request $request) {
        $validated = $request->validate([
            "title" => "required|string|max:255|unique:posts,title",
            "content" => "required|string",
            "author" => "required|string|max:255",
            "category_id" => "required|exists:categories,id",
        ]);

        $post = Post::create($validated);

        return response()->json($post, 201);

    }

    public function show(Request $request, Post $post)
    {

    }

    public function update(Request $request, Post $post)
    {

    }

    public function destroy(Request $request, Post $post)
    {

    }

}
