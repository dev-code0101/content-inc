<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    // Get Category by it's slug
    public function getCategoryBySlug($slug)
    {
        //Get active posts only in current Category
        $category = Category::whereSlug($slug)->firstOrFail();
        $posts_count = $category->publishedPosts()->count();
        $posts = $category->publishedPosts()->latest()->paginate(config('app.num_items_per_page'));
        $category_name = $category->name;

        return view('front.category', compact('posts', 'posts_count','category_name'));
    }
}
