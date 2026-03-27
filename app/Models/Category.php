<?php

declare(strict_types=1);

namespace App\Models;

//use App\Http\Traits\DateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
  //  use DateTrait;

    protected $fillable = [
        'name',
        'description',
    ];
}
