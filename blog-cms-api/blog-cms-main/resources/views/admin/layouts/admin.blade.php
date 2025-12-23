<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog CMS - Admin Panel</title>
    <meta name="author" content="Yasser Elgammal">
    <meta name="description" content="Admin panel for managing blog content and settings.">
    <!-- Tailwind -->
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    @stack('head')
</head>

<body class="bg-gray-100 font-family-karla">

    <!-- Sidebar -->
    <aside
        class="hidden sm:block fixed inset-y-0 left-0 w-64 shadow-xl overflow-y-auto z-40 bg-gradient-to-b from-blue-700 via-blue-600 to-indigo-700">
        <div class="p-4">
            <a href="{{ route('admin.index') }}"
                class="text-white text-3xl font-semibold uppercase hover:text-gray-300">
                @can('admin-only')
                    Admin
                @else
                    Writer
                @endcan
            </a>
            <button onclick="location.href='{{ route('admin.post.create') }}';"
                class="w-full bg-white cta-btn font-semibold py-2 mt-2 rounded-lg shadow-lg hover:shadow-xl hover:bg-gray-300 flex items-center justify-center">
                <i class="fas fa-plus mr-2"></i> New Post
            </button>
        </div>

        <nav class="text-white text-base font-semibold">
            <a href="{{ route('admin.index') }}"
                class="{{ request()->routeIs('admin.index') ? 'active-nav-link' : 'opacity-75 hover:opacity-100' }} flex items-center py-3 pl-6 nav-item">
                <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
            </a>
            @can('admin-only')
                <a href="{{ route('admin.category.index') }}"
                    class="{{ request()->routeIs('*.category.*') ? 'active-nav-link' : 'opacity-75 hover:opacity-100' }} flex items-center py-3 pl-6 nav-item">
                    <i class="fas fa-sticky-note mr-3"></i> Categories
                </a>
            @endcan
            <a href="{{ route('admin.post.index') }}"
                class="{{ request()->routeIs('*.post.*') ? 'active-nav-link' : 'opacity-75 hover:opacity-100' }} flex items-center py-3 pl-6 nav-item">
                <i class="fas fa-newspaper mr-3"></i> Posts
            </a>
            <a href="{{ route('admin.tag.index') }}"
                class="{{ request()->routeIs('*.tag.*') ? 'active-nav-link' : 'opacity-75 hover:opacity-100' }} flex items-center py-3 pl-6 nav-item">
                <i class="fas fa-tag mr-3"></i> Tags
            </a>
            @can('admin-only')
                <a href="{{ route('admin.page.index') }}"
                    class="{{ request()->routeIs('*.page.*') ? 'active-nav-link' : 'opacity-75 hover:opacity-100' }} flex items-center py-3 pl-6 nav-item">
                    <i class="far fa-file mr-3"></i> Pages
                </a>
                <a href="{{ route('admin.role.index') }}"
                    class="{{ request()->routeIs('*.role.*') ? 'active-nav-link' : 'opacity-75 hover:opacity-100' }} flex items-center py-3 pl-6 nav-item">
                    <i class="fas fa-user-shield mr-3"></i> Roles
                </a>
                <a href="{{ route('admin.user.index') }}"
                    class="{{ request()->routeIs('*.user.*') ? 'active-nav-link' : 'opacity-75 hover:opacity-100' }} flex items-center py-3 pl-6 nav-item">
                    <i class="fas fa-users mr-3"></i> Users
                </a>
                <a href="{{ route('admin.setting.index') }}"
                    class="{{ request()->routeIs('*.setting.*') ? 'active-nav-link' : 'opacity-75 hover:opacity-100' }} flex items-center py-3 pl-6 nav-item">
                    <i class="fas fa-wrench mr-3"></i> Settings
                </a>
            @endcan
        </nav>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                class="absolute bottom-0 w-full upgrade-btn text-white flex items-center justify-center py-4 hover:bg-blue-800 transition">
                <i class="fas fa-arrow-circle-left mr-2"></i> Sign Out
            </button>
        </form>
    </aside>

    <!-- Wrapper -->
    <div class="w-full sm:pl-64 flex flex-col min-h-screen">

        <!-- Fixed Header -->
        <header
            class="fixed top-0 left-0 sm:left-64 right-0 bg-white border-b border-gray-200 z-30 flex items-center justify-between py-3 px-6 shadow-md">
            <!-- Search -->
            <form action="{{ route('admin.post.search') }}" method="GET"
                class="relative text-lg text-gray-800 rounded w-full max-w-md">

                <div class="flex flex-col border-b border-teal-500 py-2 relative">
                    <div class="flex items-center">
                        <input class="bg-transparent border-none mr-3 px-2 leading-tight focus:outline-none w-full"
                            type="text" placeholder="Search..." name="search" value="{{ old('search') }}">

                        <button type="submit" class="absolute right-0 top-0 mt-3 mr-4">
                            <svg class="h-4 w-4 fill-current" viewBox="0 0 56.966 56.966">
                                <path d="M55.146,51.887L41.588,37.786c3.486-4.144,5.396-9.358,5.396-14.786
                    c0-12.682-10.318-23-23-23s-23,10.318-23,23
                    s10.318,23,23,23c4.761,0,9.298-1.436,13.177-4.162
                    l13.661,14.208c0.571,0.593,1.339,0.92,2.162,0.92
                    c0.779,0,1.518-0.297,2.079-0.837
                    C56.255,54.982,56.293,53.08,55.146,51.887z" />
                            </svg>
                        </button>
                    </div>

                    @error('search')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </form>


            <!-- Profile Menu -->
            <div x-data="{ isOpen: false }" class="relative ml-4">
                <button @click="isOpen = !isOpen"
                    class="relative z-10 w-10 h-10 rounded-full overflow-hidden border-2 border-gray-300 hover:border-gray-400 focus:outline-none transition">
                    <img src="{{ $user_avatar }}">
                </button>
                <div x-show="isOpen" class="absolute right-0 mt-2 w-32 bg-white rounded-lg shadow-lg py-2">
                    <a href="{{ route('admin.account.index') }}"
                        class="block px-4 py-2 account-link hover:text-white">Account</a>
                    <a href="https://yasserelgammal.github.io" target="_blank"
                        class="block px-4 py-2 account-link hover:text-white">Support</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="block px-4 py-2 account-link hover:text-white w-full text-left">Sign Out</button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto pt-20 px-4 sm:px-6 bg-gray-50 pb-20">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="fixed bottom-0 left-0 sm:left-64 right-0 bg-white text-right p-4 border-t z-30">
            ControlPanel by <a target="_blank" href="https://davidgrzyb.com" class="underline">David Grzyb</a> |
            Developed by <a target="_blank" href="https://yasserelgammal.github.io" class="underline">Yasser
                Elgammal</a>.
        </footer>

    </div>

    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js"
        integrity="sha512-STof4xm1wgkfm7heWqFJVn58Hm3EtS31XFaagaa8VMReCXAkQnJZ+jEy8PCC/iT18dFy95WcExNHFTqLyp72eQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    @if (session('message'))
        <script>
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right"
            };
            toastr.success("{{ session('message') }}");
        </script>
    @endif

    @if (session('error'))
        <script>
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right"
            };
            toastr.error("{{ session('error') }}");
        </script>
    @endif
    @stack('scripts')
</body>

</html>
