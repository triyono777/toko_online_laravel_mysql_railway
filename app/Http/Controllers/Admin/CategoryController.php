<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Schema::hasTable('categories')
            ? Category::query()->withCount('products')->orderBy('name')->get()
            : collect();

        return view('admin.categories.index', compact('categories'));
    }
}
