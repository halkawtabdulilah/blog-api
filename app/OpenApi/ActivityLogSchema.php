<?php

namespace App\OpenApi;

/**
 * @OA\Schema(
 *     schema="ActivityLog",
 *     type="object",
 *     description="Activity log entry",
 *     required={"id", "action", "entity_type", "entity_id", "actor", "created_at"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="Unique identifier for the log entry",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="action",
 *         type="string",
 *         description="Action performed (e.g., created, updated, deleted)",
 *         example="updated"
 *     ),
 *     @OA\Property(
 *         property="entity_type",
 *         type="string",
 *         description="Type of the affected entity",
 *         example="App\\Models\\Post"
 *     ),
 *     @OA\Property(
 *         property="entity_id",
 *         type="integer",
 *         description="ID of the affected entity",
 *         example=42
 *     ),
 *     @OA\Property(
 *         property="changed_fields",
 *         type="object",
 *         description="JSON object showing before/after values of changed fields",
 *         nullable=true,
 *         example={
 *             "title": {
 *                 "before": "Old Title",
 *                 "after": "New Title"
 *             },
 *             "content": {
 *                 "before": "Old content",
 *                 "after": "Updated content"
 *             }
 *         }
 *     ),
 *     @OA\Property(
 *         property="actor",
 *         type="string",
 *         description="User or system that performed the action",
 *         example="john"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="When the action was logged",
 *         example="2023-05-15T12:34:56Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="When the log entry was last updated",
 *         example="2023-05-15T12:34:56Z"
 *     )
 * )
 */
class ActivityLogSchema {}
