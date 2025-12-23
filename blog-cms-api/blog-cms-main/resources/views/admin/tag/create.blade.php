<x-admin-layout>
    <div class="w-full min-h-screen bg-gray-50 flex flex-col">
        <main class="flex-grow p-8">

            {{-- Header --}}
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-semibold text-gray-800">Add Tag</h1>
                    <p class="text-gray-500">Create a new tag to categorize your content.</p>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.tag.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-lg border border-gray-200 hover:bg-gray-50 transition shadow-sm">
                        <i class="fas fa-arrow-left mr-2"></i> Back to list
                    </a>
                </div>
            </div>

            {{-- Card --}}
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

                    {{-- Form --}}
                    <form method="POST" action="{{ route('admin.tag.store') }}" class="space-y-8" id="tagForm">
                        @csrf

                        <div class="grid gap-6 md:grid-cols-2">
                            <x-admin.input
                                label="Tag Name"
                                name="name"
                                :value="old('name')"
                                required
                                icon="tag"
                                placeholder="e.g. Laravel"
                            />
                        </div>

                        <input type="hidden" name="user_id" value="{{ auth()->id() }}">

                        {{-- Sticky Footer --}}
                        <div
                            class="sticky bottom-0 bg-white/80 backdrop-blur supports-[backdrop-filter]:bg-white/60 border-t border-gray-100 -mx-6 md:-mx-8 px-6 md:px-8 py-4">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.tag.index') }}"
                                   class="inline-flex items-center px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-lg border border-gray-200 hover:bg-gray-50 transition shadow-sm">
                                    Cancel
                                </a>

                                <button type="submit"
                                    class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg shadow hover:bg-blue-700 transition">
                                    <i class="fas fa-save mr-2"></i> Add Tag
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>

            {{-- Hint --}}
            <div class="mt-6 text-xs text-gray-500">
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-gray-100">Tip</span>
                Choose short, meaningful tag names for easier filtering.
            </div>

        </main>
    </div>
</x-admin-layout>
