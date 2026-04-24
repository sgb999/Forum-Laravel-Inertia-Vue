<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use Inertia\Response;
use Inertia\ResponseFactory;
use Throwable;

class CategoryController extends Controller
{
    /**
     * @return  Response|ResponseFactory
     *
     * @throws Throwable
     */
    public function index() : Response|ResponseFactory
    {
        return inertia('Category/Show', ['categories' => Category::all()->toResourceCollection()]);
    }
}
