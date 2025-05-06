<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request) {}

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
    public function show(Request $request) {}
    public function update(Request $request) {}
    public function delete(Request $request) {}
}
