<?php

namespace App\OpenApi;

/**
 * @OA\Schema(
 *     schema="Post",
 *     type="object",
 *     required={"title", "content", "author", "category_id"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=1,
 *         description="Auto-incremented post ID"
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         maxLength=255,
 *         example="Getting Started with Laravel",
 *         description="Post title"
 *     ),
 *     @OA\Property(
 *         property="content",
 *         type="string",
 *         example="This is a detailed guide about Laravel...",
 *         description="Main post content (HTML/markdown)"
 *     ),
 *     @OA\Property(
 *         property="author",
 *         type="string",
 *         maxLength=255,
 *         example="Jane Doe",
 *         description="Author display name"
 *     ),
 *     @OA\Property(
 *         property="category_id",
 *         type="integer",
 *         example=2,
 *         description="ID of the associated category"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         example="2023-05-10T08:00:00Z",
 *         description="Record creation timestamp"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         example="2023-05-12T09:15:00Z",
 *         description="Last update timestamp"
 *     ),
 *     @OA\Property(
 *         property="deleted_at",
 *         type="string",
 *         format="date-time",
 *         nullable=true,
 *         example=null,
 *         description="Soft deletion timestamp"
 *     )
 * )
 */
class PostSchema {}
