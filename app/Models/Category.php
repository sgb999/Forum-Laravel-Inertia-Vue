<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\{Table, Fillable};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Table(name: 'categories')]
#[Fillable(['name', 'description'])]
class Category extends Model
{
    use HasFactory;
}
