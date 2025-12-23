<x-admin-layout>
    <div class="w-full min-h-screen bg-gray-50 flex flex-col">
        <main class="flex-grow p-8">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-semibold text-gray-800">Pages</h1>
                    <p class="text-gray-500">Browse and manage your blog pages.</p>
                </div>

                <button
                    onclick="location.href='{{ route('admin.page.create') }}';"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg shadow hover:bg-blue-700 transition">
                    <i class="fas fa-plus mr-2"></i> Add Page
                </button>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm text-gray-700">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs font-semibold tracking-wider">
                            <tr>
                                <th class="py-4 px-6">#</th>
                                <th class="py-4 px-6">Title</th>
                                <th class="py-4 px-6">Added By</th>
                                <th class="py-4 px-6 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse ($pages as $page)
                                <tr class="hover:bg-gray-50 transition">
                                    <!-- ID -->
                                    <td class="py-3 px-6 font-medium text-gray-800">
                                        {{ $page->id }}
                                    </td>

                                    <!-- Title -->
                                    <td class="py-3 px-6">
                                        <div class="max-w-xs truncate" title="{{ $page->name }}">
                                            {{ $page->name }}
                                        </div>
                                    </td>

                                    <!-- Added By -->
                                    <td class="py-3 px-6">
                                        {{ optional($page->user)->name ?? '—' }}
                                    </td>

                                    <!-- Actions -->
                                    <td class="py-3 px-6 text-right space-x-2 whitespace-nowrap">
                                        <a href="{{ route('admin.page.edit', $page->id) }}"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-700 bg-green-100 rounded-md hover:bg-green-200 transition">
                                            <i class="fas fa-edit mr-1"></i> Edit
                                        </a>

                                        <form method="POST" class="inline"
                                            action="{{ route('admin.page.destroy', $page->id) }}"
                                            onsubmit="return confirm('Are you sure you want to delete this page?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 rounded-md hover:bg-red-200 transition">
                                                <i class="fas fa-trash mr-1"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-10 text-center text-gray-500">
                                        No pages found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Footer / Pagination -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    {!! $pages->links() !!}
                </div>
            </div>
        </main>
    </div>
</x-admin-layout>
