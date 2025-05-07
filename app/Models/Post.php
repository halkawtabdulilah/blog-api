<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "title",
        "content",
        "author",
        "category_id",
    ];

    public function logs(): MorphMany
    {
        return $this->morphMany('ActivityLog', 'loggable');
    }

    public function category(): BelongsTo {
        return $this->belongsTo(Category::class);
    }

}
