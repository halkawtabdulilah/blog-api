<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    protected $casts = [
        'date' => 'datetime',
    ];

    public function logs(): MorphMany
    {
        return $this->morphMany('ActivityLog', 'loggable');
    }

}
