<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class ActivityLogService {

    /**
     * Log activity (CRUD operation) to the activity logs.
     *
     * This method is used to create a new activity log entry in the database, which records
     * details about an action performed on a specific entity by a user (actor).
     *
     * @param string $action The type of action performed (e.g., CREATE, UPDATE, DELETE).
     * @param string $entityType The class name of the entity (e.g., App\Models\Category).
     * @param int $entityId The ID of the entity being acted upon.
     * @param string $actor The name or identifier of the actor performing the action.
     * @param array $changedFields The fields that were changed during an update action. Default is an empty array.
     *
     * @return ActivityLog The created activity log instance.
     */
    public function logActivity(string $action, string $entityType, int $entityId, string $actor, array $changedFields = []): ActivityLog
    {

    // TODO: refactor actor to id
        return ActivityLog::create([
            'action' => $action,
            "entity_type" => $entityType,
            "entity_id" => $entityId,
            "changed_fields" => $changedFields,
            "actor" => $actor,
        ]);
    }

    /**
     * Get the Updated Fields for an entity during Update requests
     *
     * Compares the fields of the old data with the new data and returns
     * an associative array with the changed fields that are later stored
     * in activity_logs.
     *
     * @param string $entityType The class name of the model (e.g., App\Models\Category).
     * @param Model $oldData The original model instance before the update.
     * @param array $data The new data used to update the model.
     *
     * @return array An associative array of changed fields, where the keys are the field names,
     *               and the values are arrays containing "before" and "after" values.
    */
    public function getUpdatedFields(string $entityType, Model $oldData, array $data): array {

        if (!class_exists($entityType)) {
            throw new InvalidArgumentException("Model class '$entityType' does not exist.");
        }

        $modelObject = new $entityType();
        $modelFillables = $modelObject->getFillable();

        $changedFields = [];

        foreach($modelFillables as $fillable) {
            if (array_key_exists($fillable, $data) && $data[$fillable] != $oldData[$fillable]) {
                $changedFields[$fillable] = [
                    "before" => $oldData[$fillable],
                    "after" => $data[$fillable],
                ];
            }
        }

        return $changedFields;

    }
}
