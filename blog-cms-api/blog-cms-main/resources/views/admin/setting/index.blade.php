<x-admin-layout>
    <div class="w-full min-h-screen bg-gray-50 flex flex-col">
        <main class="flex-grow p-8">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-semibold text-gray-800">Site Settings</h1>
                    <p class="text-gray-500">Manage global configuration and public site information.</p>
                </div>

                <a href="{{ url()->previous() }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg border border-gray-300 shadow-sm hover:bg-gray-200 transition">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </div>

            <!-- Card -->
            <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-100">
                <div class="p-6 md:p-8">

                    {{-- Last Edit Info --}}
                    @if ($setting->updated_at)
                        <div
                            class="mb-6 flex items-center gap-2 rounded-lg border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                            <i class="fas fa-info-circle"></i>
                            <span>
                                Last updated at
                                <span class="font-semibold">
                                    {{ $setting->updated_at->format('Y-m-d H:i') }}
                                </span>
                            </span>
                        </div>
                    @endif

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

                    <form method="POST" action="{{ route('admin.setting.update', $setting->id) }}" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <div class="grid gap-6 md:grid-cols-2">
                            {{-- Site Name --}}
                            <x-admin.input label="Site Name" name="site_name" :value="old('site_name', $setting->site_name)" required icon="fas fa-globe"
                                placeholder="Your site name" />

                            {{-- Description --}}
                            <x-admin.input label="Site Description" name="description" :value="old('description', $setting->description)" required
                                icon="fas fa-align-left" placeholder="Short description for SEO and meta tags" />

                            {{-- About --}}
                            <x-admin.input label="About" name="about" :value="old('about', $setting->about)" required icon="fas fa-info-circle"
                                placeholder="Short about text" />

                            {{-- Copy Rights --}}
                            <x-admin.input label="Footer Copyright" name="copy_rights" :value="old('copy_rights', $setting->copy_rights)" required
                                icon="fas fa-copyright" placeholder="© 2025 Your Company. All rights reserved." />

                            {{-- Instagram --}}
                            <x-admin.input label="Instagram URL" name="url_insta" :value="old('url_insta', $setting->url_insta)" required
                                icon="fab fa-instagram" placeholder="https://instagram.com/your-account" />

                            {{-- Twitter / X --}}
                            <x-admin.input label="Twitter URL" name="url_twitter" :value="old('url_twitter', $setting->url_twitter)" required
                                icon="fab fa-twitter" placeholder="https://twitter.com/your-account" />

                            {{-- LinkedIn --}}
                            <x-admin.input label="LinkedIn URL" name="url_linkedin" :value="old('url_linkedin', $setting->url_linkedin)" required
                                icon="fab fa-linkedin" placeholder="https://www.linkedin.com/company/your-company" />

                            {{-- Facebook --}}
                            <x-admin.input label="Facebook URL" name="url_fb" :value="old('url_fb', $setting->url_fb)" required
                                icon="fab fa-facebook" placeholder="https://facebook.com/your-page" />

                            {{-- Contact Email --}}
                            <x-admin.input label="Contact Email" name="contact_email" type="email" :value="old('contact_email', $setting->contact_email)"
                                required icon="fas fa-envelope" placeholder="support@example.com" />
                        </div>

                        {{-- Sticky Footer Actions --}}
                        <div
                            class="sticky bottom-0 bg-white/80 backdrop-blur-sm border-t border-gray-100 -mx-6 md:-mx-8 px-6 md:px-8 py-4">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ url()->previous() }}"
                                    class="inline-flex items-center px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-lg border border-gray-300 hover:bg-gray-50 transition">
                                    Cancel
                                </a>

                                <button type="submit"
                                    class="inline-flex items-center px-5 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg shadow hover:bg-green-700 transition">
                                    <i class="fas fa-save mr-2"></i>
                                    Update Settings
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>

            <div class="mt-6 text-xs text-gray-500">
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-gray-100">Tip</span>
                These settings affect the public-facing parts of your website, including SEO, footer, and contact info.
            </div>
        </main>
    </div>
</x-admin-layout>
