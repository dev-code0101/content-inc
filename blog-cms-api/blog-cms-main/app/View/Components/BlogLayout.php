<?php

namespace App\View\Components;

use App\Models\Category;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Tag;
use App\Models\User;
use Illuminate\View\Component;

class BlogLayout extends Component
{
    public $title;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($title = null)
    {
        $this->title = $title;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $categories = Category::whereNull('parent_id')
            ->with('childrenRecursive')
            ->orderBy('name')
            ->get();
        $top_users = User::withCount('posts')->orderByDesc('posts_count')->take(5)->get();
        $setting = Setting::first();
        $pages = Page::select('id', 'name', 'slug', 'navbar', 'footer')->latest('id')->get();
        $pages_nav = $pages->where('navbar', true);
        $pages_footer = $pages->where('footer', true);
        $tags = Tag::whereHas('posts', function ($q) {
            $q->published();
        })->get();

        return view('layouts.blog', compact('categories', 'top_users', 'setting', 'pages_nav', 'pages_footer', 'tags'));
    }
}
