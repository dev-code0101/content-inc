<x-blog-layout title="{{ $post_title }}">

    <section class="w-full lg:w-2/3 flex flex-col mx-auto space-y-10">

        {{-- Main Post --}}
        <article class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden">

            {{-- Image --}}
            @if ($post->image)
                <img src="{{ $post->image }}" alt="{{ $post->title }}" width="1000" height="500"
                    class="w-full h-72 sm:h-96 object-cover">
            @endif

            <div class="p-6 sm:p-8 space-y-4">

                {{-- Category --}}
                <a href="{{ route('category.show', $post->category->slug) }}"
                    class="inline-block px-4 py-1.5 bg-indigo-50 text-indigo-700 border border-indigo-200
                           rounded-full text-xs uppercase font-bold tracking-wide">
                    {{ $post->category->name }}
                </a>

                {{-- Title --}}
                <h1 class="text-3xl font-bold text-gray-900 leading-tight">
                    {{ $post->title }}
                </h1>

                {{-- Meta --}}
                <p class="text-sm text-gray-500">
                    By
                    <span class="font-semibold text-gray-700">{{ $post->user->name }}</span>
                    <span class="mx-1">•</span>
                    {{ $post->created_at }}
                </p>

                {{-- Content --}}
                <div class="prose max-w-none">
                    {!! $post->content !!}
                </div>

            </div>
        </article>

        {{-- Author --}}
        <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-6 flex gap-6">
            <img src="{{ $post->user->avatar }}" class="w-20 h-20 rounded-full object-cover border border-gray-200">

            <div class="flex-1">
                <h3 class="text-xl font-semibold text-gray-900">{{ $post->user->name }}</h3>
                <p class="text-gray-600 text-sm mt-1">{{ $post->user->bio }}</p>

                <div class="flex items-center gap-4 text-lg text-gray-500 mt-3">
                    @if ($post->user->url_fb)
                        <a href="{{ $post->user->url_fb }}" class="hover:text-blue-600"><i
                                class="fab fa-facebook"></i></a>
                    @endif
                    @if ($post->user->url_insta)
                        <a href="{{ $post->user->url_insta }}" class="hover:text-pink-600"><i
                                class="fab fa-instagram"></i></a>
                    @endif
                    @if ($post->user->url_twitter)
                        <a href="{{ $post->user->url_twitter }}" class="hover:text-sky-500"><i
                                class="fab fa-twitter"></i></a>
                    @endif
                    @if ($post->user->url_linkedin)
                        <a href="{{ $post->user->url_linkedin }}" class="hover:text-blue-700"><i
                                class="fab fa-linkedin"></i></a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Comment Form --}}
        @auth
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-6 space-y-3">

                <h3 class="text-xl font-semibold">Add a comment</h3>

                <form method="POST" action="{{ route('post.comment', $post) }}" class="space-y-3">
                    @csrf

                    <textarea name="body" rows="5"
                        class="w-full border rounded-xl p-3 text-sm bg-gray-50 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Write your comment..."></textarea>

                    <button class="px-5 py-2 bg-indigo-600 text-white rounded-xl font-medium hover:bg-indigo-700">
                        Submit
                    </button>
                </form>

            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-3xl p-6 text-center text-yellow-900">
                Please <a href="{{ route('login') }}" class="font-bold underline">login</a> to comment.
            </div>
        @endauth


        {{-- Comments --}}
        <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-6">

            <h3 class="text-xl font-semibold mb-4">Comments</h3>

            @forelse ($post->comments as $comment)
                <div class="border-b last:border-0 py-4">

                    <p class="font-semibold text-gray-800">{{ $comment->user->name }}</p>
                    <p class="text-sm text-gray-700">{{ $comment->body }}</p>

                    <div class="flex justify-between items-center mt-1">
                        <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>

                        @can('delete', $comment)
                            <form method="POST" action="{{ route('comment.destroy', $comment->id) }}"
                                onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 text-xs hover:underline">Delete</button>
                            </form>
                        @endcan
                    </div>

                </div>
            @empty
                <p class="text-gray-500 text-sm">No comments yet.</p>
            @endforelse

        </div>

        {{-- Prev / Next --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Prev --}}
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-5">
                @if ($post->previous)
                    <p class="text-xs font-semibold text-gray-500 mb-1"><i class="fas fa-arrow-left mr-1"></i> Previous
                    </p>
                    <a href="{{ route('post.show', $post->previous->slug) }}"
                        class="text-gray-900 font-medium hover:text-indigo-600">
                        {{ $post->previous->title }}
                    </a>
                @else
                    <p class="text-gray-400 text-sm">This is the first post.</p>
                @endif
            </div>

            {{-- Next --}}
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-5 text-right">
                @if ($post->next)
                    <p class="text-xs font-semibold text-gray-500 mb-1">Next <i class="fas fa-arrow-right ml-1"></i></p>
                    <a href="{{ route('post.show', $post->next->slug) }}"
                        class="text-gray-900 font-medium hover:text-indigo-600">
                        {{ $post->next->title }}
                    </a>
                @else
                    <p class="text-gray-400 text-sm">This is the last post.</p>
                @endif
            </div>

        </div>

    </section>

</x-blog-layout>
