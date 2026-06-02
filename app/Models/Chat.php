<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\{Table, Fillable};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table(name: 'chats')]
#[Fillable(['user_id_1', 'user_id_2'])]
class Chat extends Model
{
    use HasFactory;

    public function user1() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_1');
    }

    public function user2() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_2');
    }
}
