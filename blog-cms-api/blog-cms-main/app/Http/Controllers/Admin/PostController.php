<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\{PostRequest, SearchRequest};
use App\Models\{Category, Post, Tag};
use App\Traits\SlugCreater;
use Illuminate\Http\Request;

class PostController extends Controller
{
    use SlugCreater;

    public function __construct()
    {
        $this->authorizeResource(Post::class, 'post');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::with([
            'category:id,name',
            'user:id,name',
            'tags' => function ($query) {
                $query->take(2);
            },
        ])->withCount(['comments', 'tags'])->latest()->paginate(15);

        return view('admin.post.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::pluck('name', 'id')->toArray();
        $tags = Tag::pluck('name', 'id')->toArray();

        return view('admin.post.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        $post_data = $request->safe()->except('image');

        if ($request->hasfile('image')) {
            $post_data['image'] = $request->file('image')->store('images/posts');
        }

        $post = Post::create($post_data);

        if ($request->has('tags')) {
            $post->tags()->attach($request->tags);
        }

        return to_route('admin.post.index')->with('message', trans('admin.post_created'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        $categories = Category::pluck('name', 'id')->toArray();
        $tags = Tag::pluck('name', 'id')->toArray();

        return view('admin.post.edit', compact('post', 'categories', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PostRequest $request, Post $post)
    {
        $post_data = $request->safe()->except('image');

        if ($request->hasfile('image')) {
            $post_data['image'] = $request->file('image')->store('images/posts');
        }

        $post->update($post_data);

        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }

        return to_route('admin.post.index')->with('message', trans('admin.post_updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();

        return back()->with('message', trans('admin.post_deleted'));
    }

    public function getSlug(Request $request)
    {
        $slug = $this->createSlug($request, Post::class);

        return response()->json(['slug' => $slug]);
    }

    public function search(SearchRequest $request)
    {
        $searchedText = $request->validated()['search'];

        $posts = Post::query()
            ->with(['category', 'user', 'tags'])
            ->where(function ($query) use ($searchedText) {
                $query->where('title', 'LIKE', "%{$searchedText}%")
                    ->orWhere('content', 'LIKE', "%{$searchedText}%");
            })->paginate(10);

        // Return the search view with the results
        return view('admin.post.search', compact('posts'));
    }
}
