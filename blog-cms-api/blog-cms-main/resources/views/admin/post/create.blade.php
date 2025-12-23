<x-admin-layout>
    @push('head')
        <style>
            /* Optional: hide default select arrow on some browsers */
            select::-ms-expand {
                display: none;
            }
        </style>
        {{-- CDN styles --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jodit@latest/es2021/jodit.fat.min.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css">
    @endpush

    <div class="w-full bg-gray-50 flex flex-col">
        <main class="flex-grow p-8">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-semibold text-gray-800">Add Post</h1>
                    <p class="text-gray-500">Create a new post and publish it to your blog.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="history.back()"
                        class="inline-flex items-center px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-lg border border-gray-200 hover:bg-gray-50 transition shadow-sm">
                        <i class="fas arrow-left mr-2"></i> Back
                    </button>
                </div>
            </div>

            <!-- Card -->
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

                    <form method="POST" action="{{ route('admin.post.store') }}" enctype="multipart/form-data"
                        class="space-y-8" id="postForm">
                        @csrf

                        {{-- 1/2: Title / Slug / Category / Status --}}
                        <div class="grid gap-6 md:grid-cols-2">
                            {{-- Title --}}
                            <x-admin.input label="Title" name="title" :value="old('title')"
                                placeholder="Title of the post" icon="fas fa-heading" required data-slug-field="title"
                                data-slug-route="{{ route('admin.post.getslug') }}" />

                            {{-- Slug --}}
                            <x-admin.input label="Slug" name="slug" :value="old('slug')"
                                placeholder="ex: my-first-post" icon="fas fa-link" required />

                            {{-- Category --}}
                            <x-admin.select name="category_id" label="Category" :options="$categories" :value="old('category_id')"
                                placeholder="— Select category —" icon="fas fa-sitemap" />

                            {{-- Published --}}
                            <x-admin.select name="status" label="Published" :options="[1 => 'Yes', 0 => 'No']" :value="old('status', 1)"
                                placeholder="— Choose —" icon="fas fa-eye" />
                        </div>

                        {{-- 1/2: Tags / Image --}}
                        <div class="grid gap-6 md:grid-cols-2">
                            {{-- Tags (multi-select) --}}
                            <div>
                                <label for="tags" class="block mb-2 text-sm font-medium text-gray-900">Tags</label>
                                <select id="tags" name="tags[]" multiple
                                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                                    size="6">
                                    @foreach ($tags as $id => $label)
                                        <option value="{{ $id }}" @selected(in_array($id, (array) old('tags', [])))>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-2 text-xs text-gray-500">Select multiple tags.</p>
                                @error('tags')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Image --}}
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900">Image</label>
                                <input type="file" id="myimage" name="image"
                                    class="block w-full text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-gray-100 hover:file:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                <p class="mt-2 text-xs text-gray-500">JPG/PNG, recommended &le; 2MB.</p>
                            </div>
                        </div>

                        {{-- FULL WIDTH: Content (Jodit) --}}
                        <div>
                            <label for="editor" class="block mb-2 text-sm font-medium text-gray-900">Content</label>
                            <textarea id="editor" name="content">{{ old('content') }}</textarea>
                            @error('content')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Sticky Footer --}}
                        <div
                            class="sticky bottom-0 bg-white/80 backdrop-blur supports-[backdrop-filter]:bg-white/60 border-t border-gray-100 -mx-6 md:-mx-8 px-6 md:px-8 py-4">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.post.index') }}"
                                    class="inline-flex items-center px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-lg border border-gray-200 hover:bg-gray-50 transition shadow-sm">
                                    Cancel
                                </a>
                                <button type="submit"
                                    class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg shadow hover:bg-blue-700 transition">
                                    <i class="fas fa-save mr-2"></i> Save Post
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-6 text-xs text-gray-500">
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-gray-100">Tip</span>
                Slugs can auto-generate from the title; tweak before saving if needed.
            </div>
        </main>
    </div>

    @push('scripts')
        {{-- slugify --}}
        @include('admin.partials.slugify-script')
        {{-- Tom Select --}}
        @include('admin.partials.tom-select')
        {{-- Jodit --}}
        @include('admin.partials.jodit-editor')
    @endpush
</x-admin-layout>
