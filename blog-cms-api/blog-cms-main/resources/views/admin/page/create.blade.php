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
    @endpush
    <div class="w-full min-h-screen bg-gray-50 flex flex-col">
        <main class="flex-grow p-8">

            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-semibold text-gray-800">Add Page</h1>
                    <p class="text-gray-500">Create a new static page for your website.</p>
                </div>

                <button onclick="location.href='{{ route('admin.page.index') }}'"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg border border-gray-300 shadow-sm hover:bg-gray-200 transition">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to List
                </button>
            </div>

            <!-- Card -->
            <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-100">
                <div class="p-6 md:p-8">

                    {{-- Validation Errors --}}
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

                    <form method="POST" action="{{ route('admin.page.store') }}" class="space-y-8">
                        @csrf

                        <div class="grid gap-6 md:grid-cols-2">
                            {{-- Page Name --}}
                            <x-admin.input label="Page Name" name="name" :value="old('name')" required icon="fas fa-file"
                                placeholder="Page name" data-slug-field="name"
                                data-slug-route="{{ route('admin.page.getslug') }}" />

                            {{-- Slug --}}
                            <x-admin.input label="Slug" name="slug" :value="old('slug')" required icon="fas fa-link"
                                placeholder="e.g. about-us">
                                <p class="mt-1 text-xs text-gray-500">
                                    URL-friendly version (lowercase, hyphens only).
                                </p>
                            </x-admin.input>

                            {{-- Navbar --}}
                            <x-admin.select label="Show in Navbar" name="navbar" :options="[0 => 'No', 1 => 'Yes']" :value="old('navbar', 0)"
                                icon="bars" />

                            {{-- Footer --}}
                            <x-admin.select label="Show in Footer" name="footer" :options="[0 => 'No', 1 => 'Yes']" :value="old('footer', 0)"
                                icon="fas fa-shoe-prints" />
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
                            class="sticky bottom-0 bg-white/80 backdrop-blur-sm border-t border-gray-100 -mx-6 md:-mx-8 px-6 md:px-8 py-4">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.page.index') }}"
                                    class="inline-flex items-center px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-lg border border-gray-300 hover:bg-gray-50 transition">
                                    Cancel
                                </a>

                                <button type="submit"
                                    class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg shadow hover:bg-blue-700 transition">
                                    <i class="fas fa-save mr-2"></i>
                                    Create Page
                                </button>
                            </div>
                        </div>

                    </form>

                </div>
            </div>

        </main>
    </div>

    {{-- Slugify --}}
    @push('scripts')
        @include('admin.partials.slugify-script')
        {{-- Tom Select --}}
        @include('admin.partials.jodit-editor')
    @endpush
</x-admin-layout>
