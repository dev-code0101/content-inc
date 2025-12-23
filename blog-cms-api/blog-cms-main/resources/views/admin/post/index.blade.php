<x-admin-layout>
    <div class="w-full min-h-screen bg-gray-50 flex flex-col">
        <main class="flex-grow p-8">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-semibold text-gray-800">Posts</h1>
                    <p class="text-gray-500">Browse and manage your posts.</p>
                </div>

                @can('create', 'App\Models\Post')
                    <button onclick="location.href='{{ route('admin.post.create') }}';"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg shadow hover:bg-blue-700 transition">
                        <i class="fas fa-plus mr-2"></i> Add Post
                    </button>
                @endcan
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm text-gray-700">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs font-semibold tracking-wider">
                            <tr>
                                <th class="py-4 px-6">#</th>
                                <th class="py-4 px-6">Title</th>
                                <th class="py-4 px-6">Category</th>
                                <th class="py-4 px-6">Tags</th>
                                <th class="py-4 px-6">Views</th>
                                <th class="py-4 px-6">Added By</th>
                                <th class="py-4 px-6 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse ($posts as $post)
                                <tr class="hover:bg-gray-50 transition">
                                    <!-- ID -->
                                    <td class="py-3 px-6 font-medium text-gray-800">
                                        {{ $post->id }}
                                    </td>

                                    <!-- Title -->
                                    <td class="py-3 px-6">
                                        <div class="max-w-xs truncate" title="{{ $post->title }}">
                                            {{ $post->title }}
                                        </div>
                                    </td>

                                    <!-- Category -->
                                    <td class="py-3 px-6">
                                        @if ($post->category)
                                            <span
                                                class="inline-flex items-center text-xs font-medium px-2 py-1 rounded-full bg-purple-50 text-purple-700">
                                                <i class="fas fa-folder-open mr-1"></i> {{ $post->category->name }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>

                                    <!-- Tags (show up to 3, then +N) -->
                                    <td class="py-3 px-6">
                                        <div class="flex flex-wrap gap-1">
                                            @forelse ($post->tags as $tag)
                                                <a href="{{ route('tag.show', $tag->name) }}"
                                                    class="inline-flex items-center text-xs font-medium px-2 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 transition">
                                                    <i class="fas fa-tag mr-1"></i>{{ $tag->name }}
                                                </a>
                                            @empty
                                                <span class="text-gray-400">No Tags</span>
                                            @endforelse

                                            @if (($remaining = $post->tags_count - $post->tags->count()) > 0)
                                                <span
                                                    class="inline-flex items-center text-xs font-medium px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                                                    +{{ $remaining }} more
                                                </span>
                                            @endif

                                        </div>
                                    </td>

                                    <!-- Views -->
                                    <td class="py-3 px-6">
                                        <span
                                            class="inline-flex items-center text-xs font-medium px-2 py-1 rounded bg-gray-100 text-gray-700">
                                            <i class="fas fa-eye mr-1"></i> {{ number_format($post->views) }}
                                        </span>
                                    </td>

                                    <!-- Added By -->
                                    <td class="py-3 px-6">
                                        {{ optional($post->user)->name ?? '—' }}
                                    </td>

                                    <!-- Actions -->
                                    <td class="py-3 px-6 text-right space-x-2 whitespace-nowrap">
                                        @can('update', $post)
                                            <a href="{{ route('admin.post.edit', $post->id) }}"
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-700 bg-green-100 rounded-md hover:bg-green-200 transition">
                                                <i class="fas fa-edit mr-1"></i> Edit
                                            </a>
                                        @endcan

                                        @can('delete', $post)
                                            <form method="POST" class="inline"
                                                action="{{ route('admin.post.destroy', $post->id) }}"
                                                onsubmit="return confirm('Are you sure you want to delete this post?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 rounded-md hover:bg-red-200 transition">
                                                    <i class="fas fa-trash mr-1"></i> Delete
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-10 text-center text-gray-500">
                                        No posts found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Footer / Pagination -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    {!! $posts->onEachSide(1)->links() !!}
                </div>
            </div>
        </main>
    </div>
</x-admin-layout>
