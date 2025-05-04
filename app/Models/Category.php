<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{

    use HasFactory, SoftDeletes;

    protected $fillable = [
        "name",
        "slug",
        "description",
    ];


    public function logs(): MorphMany
    {
        return $this->morphMany('ActivityLog', 'loggable');
    }

}
