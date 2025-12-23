<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Post;

class HomeController extends Controller
{
    // Home Page
    public function __invoke()
    {
        // Get the active posts with (Category and User) details
        $posts = Post::published()->with(['category:id,name,slug', 'user:id,name'])
            ->latest()
            ->paginate(config('app.num_items_per_page'));

        return view('front.index', compact('posts'));
    }
}
