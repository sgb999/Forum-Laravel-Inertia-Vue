<?php

declare(strict_types=1);

namespace App\Models;

use App\Http\Traits\BelongsToUserTrait; //use App\Http\Traits\DateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use BelongsToUserTrait;
    use HasFactory;
    //use DateTrait;

    protected $fillable = [
        'title',
        'content',
        'user_id',
        'category_id',
    ];

    public function category() : BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function comments() : HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
