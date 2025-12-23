<x-blog-layout title="{{ $category_name }}">

    <section class="w-full lg:w-2/3 flex flex-col">
    <div class="space-y-6">

        {{-- Category header --}}
        <header class="mb-2">
            <h2 class="text-2xl font-semibold text-gray-800">
                Category: <span class="font-bold">{{ $category_name }}</span>
            </h2>
            @if ($posts_count > 0)
                <p class="text-sm text-gray-500 mt-1">
                    {{ $posts_count }} post{{ $posts_count > 1 ? 's' : '' }} in this category
                </p>
            @endif
        </header>

        <!-- Posts Section -->
            <x-post-list :posts="$posts" />
        </div>
    </section>

</x-blog-layout>
