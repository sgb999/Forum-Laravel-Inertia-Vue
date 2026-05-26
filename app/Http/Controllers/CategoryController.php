<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use Inertia\Response;
use Inertia\ResponseFactory;

class CategoryController extends Controller
{
    /**
     * @return  Response|ResponseFactory
     */
    public function show() : Response|ResponseFactory
    {
        return inertia('categories/Show', ['categories' => Category::all()]);
    }
}
