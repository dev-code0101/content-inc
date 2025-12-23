<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Tag;

class TagController extends Controller
{
    public function getPostsPerTags($tag)
    {
        $posts = Tag::whereName($tag)->firstOrFail()
            ->publishedPosts()->latest()->paginate(config('app.num_items_per_page'));

        return view('front.tag', compact('posts', 'tag'));
    }
}
