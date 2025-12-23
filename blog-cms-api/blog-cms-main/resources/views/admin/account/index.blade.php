<x-admin-layout>
    <div class="w-full min-h-screen bg-gray-50 flex flex-col">
        <main class="flex-grow p-8">

            {{-- Header --}}
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-semibold text-gray-800">User Profile</h1>
                    <p class="text-gray-500">Update your public information and social profiles.</p>
                </div>

                <a href="{{ url()->previous() }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg border border-gray-300 shadow-sm hover:bg-gray-200 transition">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
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

                    <form method="POST" action="{{ route('admin.account.update', $user->id) }}"
                        enctype="multipart/form-data" class="space-y-8">
                        @csrf
                        @method('PUT')

                        {{-- Avatar section --}}
                        <div class="flex flex-col md:flex-row items-center gap-6 mb-4">
                            <div class="flex flex-col items-center">
                                <img src="{{ $user->avatar }}"
                                    class="rounded-full shadow h-32 w-32 object-cover border border-gray-200"
                                    alt="Avatar">

                                <p class="mt-2 text-xs text-gray-500">
                                    Recommended size: square image, at least 256x256.
                                </p>
                            </div>

                            <div class="w-full md:w-auto">
                                <label for="avatar" class="block mb-2 text-sm font-medium text-gray-900">
                                    Choose New Image
                                </label>
                                <input
                                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    id="avatar" name="avatar" type="file" accept="image/*">
                                <p class="mt-1 text-xs text-gray-500">
                                    Leave empty to keep the current avatar.
                                </p>
                            </div>
                        </div>

                        {{-- Social Links --}}
                        <div class="grid gap-6 md:grid-cols-2">
                            <x-admin.input label="Instagram URL" name="url_insta"
                                placeholder="https://instagram.com/username" icon="fab fa-instagram" :value="old('url_insta', $user->url_insta)"
                                required />

                            <x-admin.input label="Twitter URL" name="url_twitter"
                                placeholder="https://twitter.com/username" icon="fab fa-twitter" :value="old('url_twitter', $user->url_twitter)"
                                required />

                            <x-admin.input label="LinkedIn URL" name="url_linkedin"
                                placeholder="https://linkedin.com/in/username" icon="fab fa-linkedin" :value="old('url_linkedin', $user->url_linkedin)"
                                required />

                            <x-admin.input label="Facebook URL" name="url_fb"
                                placeholder="https://facebook.com/username" icon="fab fa-facebook" :value="old('url_fb', $user->url_fb)"
                                required />
                        </div>

                        {{-- Bio --}}
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900" for="bio">
                                Bio
                            </label>
                            <textarea
                                class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                       focus:ring-blue-500 focus:border-blue-500 block p-2.5"
                                id="bio" name="bio" rows="4" required>{{ old('bio', $user->bio) }}</textarea>
                        </div>

                        {{-- Sticky Footer Actions --}}
                        <div
                            class="sticky bottom-0 bg-white/80 backdrop-blur-sm border-t border-gray-100
                                   -mx-6 md:-mx-8 px-6 md:px-8 py-4">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ url()->previous() }}"
                                    class="inline-flex items-center px-4 py-2 bg-white text-gray-700 text-sm font-medium
                                          rounded-lg border border-gray-300 hover:bg-gray-50 transition">
                                    Cancel
                                </a>

                                <button type="submit"
                                    class="inline-flex items-center px-5 py-2.5 bg-green-600 text-white text-sm font-medium
                                               rounded-lg shadow hover:bg-green-700 transition">
                                    <i class="fas fa-save mr-2"></i>
                                    Update Profile
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

            <div class="mt-6 text-xs text-gray-500">
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-gray-100">Tip</span>
                Use real and consistent social URLs to help users find you easily.
            </div>
        </main>
    </div>
</x-admin-layout>
