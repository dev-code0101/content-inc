<x-admin-layout>
    @push('head')
        <style>
            #parent_id::-ms-expand {
                display: none;
            }
        </style>
    @endpush

    <div class="w-full bg-gray-50 flex flex-col">
        <main class="flex-grow p-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-semibold text-gray-800">Add Category</h1>
                    <p class="text-gray-500">Create a new category and organize your content.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="history.back()"
                        class="inline-flex items-center px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-lg border border-gray-200 hover:bg-gray-50 transition shadow-sm">
                        <i class="fas fa-arrow-left mr-2"></i> Back
                    </button>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-100">
                <div class="p-6 md:p-8">
                    @if ($errors->any())
                        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                            <div class="font-semibold mb-1">Please fix the following:</div>
                            <ul class="list-disc ms-5 space-y-1">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.category.store') }}" class="space-y-8"
                        id="categoryForm">
                        @csrf

                        <div class="grid gap-6 md:grid-cols-2">
                            <x-admin.input label="Category Name" name="name" placeholder="e.g. News" icon="fas fa-tag"
                                required data-slug-field="name"
                                data-slug-route="{{ route('admin.category.getslug') }}" />

                            <x-admin.input label="Category Slug" name="slug" placeholder="e.g. news" icon="fas fa-link"
                                required />

                            <x-admin.select name="parent_id" label="Parent Category" :options="$categories" :value="old('parent_id')"
                                placeholder="— None (Main Category) —" icon="fas fa-sitemap" />
                        </div>
                </div>
                
                <div
                    class="sticky bottom-0 bg-white/80 backdrop-blur supports-[backdrop-filter]:bg-white/60 border-t border-gray-100 -mx-6 md:-mx-8 px-6 md:px-8 py-4">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.category.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-lg border border-gray-200 hover:bg-gray-50 transition shadow-sm">
                            Cancel
                        </a>
                        <button type="submit"
                            class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg shadow hover:bg-blue-700 transition">
                            <i class="fas fa-save mr-2"></i> Save Category
                        </button>
                    </div>
                </div>
                </form>
            </div>
    </div>

    <div class="mt-6 text-xs text-gray-500">
        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-gray-100">Tip</span>
        Slugs are usually generated from the name; you can tweak them before saving.
    </div>
    </main>
    </div>

    @push('scripts')
        @include('admin.partials.slugify-script')
    @endpush
</x-admin-layout>
