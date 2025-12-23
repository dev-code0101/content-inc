<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @isset($title)
            {{ ucfirst($title) }} -
        @endisset
        {{ config('app.name') }}
    </title>

    @vite(['resources/css/blog.css', 'resources/js/blog.js'])

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        .card-hover {
            transition: .2s ease;
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.06);
        }
    </style>
    <!-- Icons -->
</head>

<body class="bg-gray-100">
    {{-- Flash Messages --}}
    @if (Session::has('message') || Session::has('error'))
        <div class="max-w-7xl mx-auto px-6 mt-6" x-data="{ show: true }" x-show="show">
            <div class="rounded-lg border bg-white shadow p-4 flex items-start gap-3">
                <i
                    class="fas {{ Session::has('message') ? 'fa-check text-green-600' : 'fa-exclamation-triangle text-red-600' }} mt-1"></i>
                <p class="text-gray-700 text-sm flex-1">
                    {{ Session::get('message') ?? Session::get('error') }}
                </p>
                <button @click="show=false">
                    <i class="fas fa-times text-gray-400 hover:text-gray-600"></i>
                </button>
            </div>
        </div>
    @endif

    {{-- Top Bar --}}
    <nav class="w-full bg-blue-600 border-b shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-between">

            <div class="flex items-center gap-6">
                {{-- Logo --}}
                <a href="{{ route('webhome') }}"
                    class="font-semibold text-white text-lg hover:text-slate-100 transition-colors">
                    {{ config('app.name') }}
                </a>

                {{-- Links --}}
                <ul class="hidden md:flex items-center gap-4 text-sm font-medium text-slate-100">
                    @foreach ($pages_nav as $page)
                        <li>
                            <a href="{{ route('page.show', $page->slug) }}"
                                class="transition-colors
                                {{ request()->routeIs('page.show') && request('slug') == $page->slug
                                    ? 'text-white font-semibold'
                                    : 'text-slate-100/90 hover:text-white' }}">
                                {{ $page->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="flex items-center gap-4">

                {{-- Social Icons --}}
                <div class="hidden md:flex items-center gap-3">
                    @if ($setting->url_fb)
                        <a href="{{ $setting->url_fb }}" target="_blank" class="text-white/80 hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    d="M22 12.07C22 6.48 17.52 2 12 2S2 6.48 2 12.07C2 17.1 5.66 21.24 10.44 22v-6.99H7.9v-2.94h2.54V9.84c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.23.2 2.23.2v2.45h-1.25c-1.23 0-1.62.77-1.62 1.56v1.88h2.76l-.44 2.94h-2.32V22C18.34 21.24 22 17.1 22 12.07z" />
                            </svg>
                        </a>
                    @endif

                    @if ($setting->url_insta)
                        <a href="{{ $setting->url_insta }}" target="_blank" class="text-white/80 hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    d="M7 2C4.243 2 2 4.243 2 7v10c0 2.757 2.243 5 5 5h10c2.757 0 5-2.243 5-5V7c0-2.757-2.243-5-5-5H7zm10 2a3 3 0 0 1 3 3v10a3 3 0 0 1-3 3H7a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h10zm-5 3a5 5 0 1 0 0 10 5 5 0 0 0 0-10zm0 2a3 3 0 1 1 0 6 3 3 0 0 1 0-6zm4.5-.25a1.25 1.25 0 1 0 0-2.5 1.25 1.25 0 0 0 0 2.5z" />
                            </svg>
                        </a>
                    @endif

                    @if ($setting->url_twitter)
                        <a href="{{ $setting->url_twitter }}" target="_blank" class="text-white/80 hover:text-white">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M22.46 6c-.77.35-1.6.58-2.46.69A4.29 4.29 0 0 0 21.86 4a8.59 8.59 0 0 1-2.72 1.04A4.28 4.28 0 0 0 11.14 8c0 .34.04.68.12 1A12.17 12.17 0 0 1 3.16 4.9a4.28 4.28 0 0 0 1.32 5.71 4.27 4.27 0 0 1-1.94-.54v.05a4.28 4.28 0 0 0 3.43 4.2 4.29 4.29 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.97A8.59 8.59 0 0 1 2 19.54a12.1 12.1 0 0 0 6.56 1.92c7.88 0 12.2-6.53 12.2-12.2 0-.19 0-.38-.01-.57A8.72 8.72 0 0 0 22.46 6z" />
                            </svg>
                        </a>
                    @endif

                    @if ($setting->url_linkedin)
                        <a href="{{ $setting->url_linkedin }}" target="_blank" class="text-white/80 hover:text-white">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M6.94 6.5A2.44 2.44 0 1 1 7 1.62a2.44 2.44 0 0 1-.06 4.88zM4.5 8h5v12h-5zm7 0h4.8v1.75h.07c.67-1.27 2.32-2.6 4.78-2.6C22.44 7.15 24 9 24 12.36V20h-5v-7.1c0-1.84-.66-3.1-2.3-3.1-1.25 0-2 .84-2.33 1.66-.12.3-.15.71-.15 1.13V20h-5z" />
                            </svg>
                        </a>
                    @endif
                </div>

                {{-- Auth --}}
                @auth
                    {{-- Admin Dashboard --}}
                    @can('admin-login')
                        <a href="{{ route('admin.index') }}"
                            class="px-4 py-2 bg-green-500 text-white text-sm font-semibold rounded-lg shadow-sm
                        hover:bg-green-600 transition">
                            Dashboard
                        </a>
                    @endcan

                    {{-- Logout --}}
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button
                            class="px-4 py-2 bg-red-500 text-white text-sm font-semibold rounded-lg shadow
                        hover:bg-red-600 transition">
                            Logout
                        </button>
                    </form>
                @else
                    {{-- Register --}}
                    <a href="{{ route('register') }}"
                        class="px-4 py-2 bg-blue-100 text-blue-800 text-sm font-semibold rounded-lg shadow-sm
                    hover:bg-blue-200 transition">
                        Register
                    </a>

                    {{-- Login --}}
                    <a href="{{ route('login') }}"
                        class="px-4 py-2 bg-blue-500 text-white text-sm font-semibold rounded-lg shadow
hover:bg-blue-400 transition">
                        Login
                    </a>

                @endauth

            </div>
        </div>
    </nav>


    {{-- Site Header --}}
    <header class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-6 py-10 text-center">
            <h1 class="text-4xl font-bold text-gray-800">{{ $setting->site_name }}</h1>
            <p class="mt-2 text-gray-500 text-sm">
                {{ $setting->description }}
            </p>
        </div>
    </header>

    {{-- Topics --}}
    <div class="bg-white border-b py-3">
        <div class="max-w-7xl mx-auto px-6">
            @include('front.partials.category-menu', [
                'categories' => $categories,
                'level' => 0,
                'orientation' => 'horizontal',
            ])
        </div>
    </div>

    {{-- MAIN CONTENT + SIDEBAR --}}
    <div class="max-w-7xl mx-auto px-6 py-10">
        <div class="flex flex-col lg:flex-row gap-8">

            {{-- MAIN CONTENT --}}
            {{ $slot }}

            {{-- SIDEBAR --}}
            @if (!request()->routeIs('page.show'))
                <aside class="w-full lg:w-1/3 flex flex-col space-y-6 lg:top-24 lg:self-start">

                    {{-- About --}}
                    <div class="bg-white rounded-xl border shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">About Us</h3>
                        <p class="text-gray-600 text-sm">{{ $setting->about }}</p>
                    </div>

                    {{-- Tags --}}
                    <div class="bg-white rounded-xl border shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Tags</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($tags as $tag)
                                <a href="{{ route('tag.show', $tag->name) }}"
                                    class="px-3 py-1 text-xs border rounded-full bg-gray-50 text-gray-700 hover:bg-gray-100">
                                    #{{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Top Writers --}}
                    <div class="bg-white rounded-xl border shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Top Writers</h3>

                        @forelse ($top_users as $top)
                            <div class="flex items-center gap-3 py-2">
                                <img src="{{ $top->avatar }}" class="w-10 h-10 rounded-full object-cover border">
                                <div class="flex-1">
                                    <p class="text-sm text-gray-800 font-medium">{{ $top->name }}</p>
                                </div>
                                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">
                                    {{ $top->posts_count }} posts
                                </span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No writers found.</p>
                        @endforelse
                    </div>

                </aside>
            @endif

        </div>
    </div>

    {{-- FOOTER --}}
    <footer class="bg-white border-t mt-10">
        <div class="max-w-7xl mx-auto px-6 py-8 text-center text-sm text-gray-600">
            <p class="mb-4">
                @foreach ($pages_footer as $page)
                    <a href="{{ route('page.show', $page->slug) }}"
                        class="px-3 transition-colors
           {{ request()->routeIs('page.show') && request('slug') == $page->slug
               ? 'text-gray-900 font-semibold'
               : 'hover:text-gray-800' }}">
                        {{ $page->name }}
                    </a>
                @endforeach
            </p>

            <p class="text-gray-500 text-xs">
                &copy; {{ $setting->copy_rights }}
            </p>
        </div>
    </footer>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js"></script>
</body>

</html>
