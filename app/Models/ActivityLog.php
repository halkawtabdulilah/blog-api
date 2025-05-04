<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "type",
        "entity_type",
        "entity_id",
        "changed_fields", //json of the updated fields
        "actor"
    ];



    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }

}
