<x-admin-layout>
    <div class="w-full min-h-screen bg-gray-50 flex flex-col">
        <main class="flex-grow p-8">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-semibold text-gray-800">Posts</h1>
                    <p class="text-gray-500">Search results in posts.</p>
                </div>

                <a href="{{ route('admin.post.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg border border-gray-300 shadow-sm hover:bg-gray-200 transition">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Posts
                </a>
            </div>

            @if ($posts->isNotEmpty())
                <!-- Result summary -->
                <div class="mb-4 flex items-center gap-2 rounded-lg border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                    <i class="fas fa-search"></i>
                    <span>
                        Found
                        <span class="font-semibold">{{ $posts->total() }}</span>
                        result(s) in posts.
                    </span>
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
                                    <th class="py-4 px-6">Added By</th>
                                    <th class="py-4 px-6 text-right">Actions</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100">
                                @foreach ($posts as $post)
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
                                                <span class="text-gray-400 text-xs">—</span>
                                            @endif
                                        </td>

                                        <!-- Tags -->
                                        <td class="py-3 px-6">
                                            <div class="flex flex-wrap gap-1">
                                                @forelse ($post->tags as $tag)
                                                    <a href="{{ route('tag.show', $tag->name) }}"
                                                       class="inline-flex items-center text-xs font-medium px-2 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 transition">
                                                        <i class="fas fa-tag mr-1"></i>{{ $tag->name }}
                                                    </a>
                                                @empty
                                                    <span class="text-gray-400 text-xs">No tags</span>
                                                @endforelse
                                            </div>
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                        {!! $posts->links() !!}
                    </div>
                </div>
            @else
                <!-- Empty state -->
                <div
                    class="flex flex-col items-center justify-center rounded-xl border border-red-100 bg-red-50 px-6 py-10 text-center text-red-700">
                    <i class="fas fa-search-minus text-3xl mb-3"></i>
                    <h2 class="text-lg font-semibold mb-1">No results found</h2>
                    <p class="text-sm mb-3">Sorry, we couldn't find any posts matching your search. Try adjusting your filters or keywords.</p>
                    <a href="{{ route('admin.post.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-white text-red-700 text-sm font-medium rounded-lg border border-red-200 hover:bg-red-100 transition">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Posts
                    </a>
                </div>
            @endif
        </main>
    </div>
</x-admin-layout>
