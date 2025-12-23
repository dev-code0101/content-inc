<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Category, Post, Tag, User};

class AdminController extends Controller
{
    public function __invoke()
    {
        $categories = Category::count();
        $posts = Post::count();
        $tags = Tag::count();
        $users = User::count();
        $news_letter_users = User::where('news_letter', true)->count();

        $statistics  = [
            ['label' => 'Categories', 'value' => $categories],
            ['label' => 'Posts', 'value' => $posts],
            ['label' => 'Tags', 'value' => $tags],
            ['label' => 'Users', 'value' => $users],
            ['label' => 'Newsletter Subscribers', 'value' => $news_letter_users],
        ];

        return view('admin.index', compact('statistics'));
    }
}
