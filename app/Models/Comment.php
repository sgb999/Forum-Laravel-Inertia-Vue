<?php

declare(strict_types=1);

namespace App\Models;

use App\Http\Traits\BelongsToUserTrait; //use App\Http\Traits\DateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use BelongsToUserTrait;
    use HasFactory;
   // use DateTrait;

    protected $fillable = [
        'comment',
        'post_id',
        'user_id',
    ];

    public function post() : BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
