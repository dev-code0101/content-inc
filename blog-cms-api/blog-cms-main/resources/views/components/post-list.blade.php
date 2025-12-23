@props(['posts'])

@forelse ($posts as $post)
    <article class="card-hover bg-white rounded-2xl shadow-sm overflow-hidden mb-6 border border-gray-100">
        <a href="{{ route('post.show', $post->slug) }}" class="block overflow-hidden">
            <img src="{{ $post->image }}"
                class="w-full h-64 object-cover hover:scale-[1.02] transform transition-all duration-300"
                alt="{{ $post->title }}">
        </a>

        <div class="p-6">
            {{-- Category --}}
            <a href="{{ route('category.show', $post->category->slug) }}"
                class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold uppercase tracking-wide border border-blue-100 mb-3">
                <i class="fas fa-folder-open mr-1 text-[11px]"></i>
                {{ $post->category->name }}
            </a>

            {{-- Title --}}
            <a href="{{ route('post.show', $post->slug) }}"
                class="block text-2xl font-bold text-gray-900 hover:text-blue-700 mb-2 leading-snug">
                {{ $post->title }}
            </a>

            {{-- Meta --}}
            <div class="flex items-center gap-3 text-xs sm:text-sm text-gray-500 mb-3">
                <div class="flex items-center gap-2">
                    <img src="{{ $post->user->avatar }}" alt="{{ $post->user->name }}"
                        class="w-8 h-8 rounded-full object-cover border border-gray-300">
                    <span class="font-medium text-gray-700">{{ $post->user->name }}</span>
                </div>


                <span class="text-gray-300">•</span>

                <div class="flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-gray-400"></i>
                    <span>{{ $post->created_at }}</span>
                </div>
            </div>

            {{-- Short content --}}
            <p class="text-sm text-gray-700 leading-relaxed mb-3">
                {{ \Illuminate\Support\Str::limit(strip_tags($post->content), 150) }}
            </p>

            {{-- Read more --}}
            <a href="{{ route('post.show', $post->slug) }}"
                class="inline-flex items-center text-sm font-semibold text-gray-700 hover:text-black">
                Continue Reading
                <i class="fas fa-arrow-right ml-2 text-xs"></i>
            </a>
        </div>
    </article>
@empty
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center">
        <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
        <p class="text-gray-600 text-base">No posts have been added yet.</p>
    </div>
@endforelse

{{-- Pagination --}}
<div class="pt-4 border-t border-gray-100">
    <div class="flex justify-center">
        {!! $posts->links() !!}
    </div>
</div>
