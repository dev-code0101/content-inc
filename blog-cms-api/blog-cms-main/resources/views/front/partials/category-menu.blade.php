@props([
    'categories' => collect(),
    'level' => 0,
    'orientation' => 'horizontal',
])

<style>
    [x-cloak] {
        display: none !important
    }
</style>

<ul class="{{ $level === 0
        ? 'flex flex-col sm:flex-row items-center justify-center text-sm font-bold uppercase px-6 py-2'
        : 'mt-2 space-y-1 pl-3' }}">

    @forelse ($categories as $category)

        <li class="relative group"
            x-data="{
                open: false,
                timeout: null,
                show() {
                    clearTimeout(this.timeout);
                    this.open = true;
                },
                hide() {
                    this.timeout = setTimeout(() => {
                        this.open = false;
                    }, 350);
                }
            }"
            @mouseenter="show()"
            @mouseleave="hide()">

            <a href="{{ route('category.show', $category->slug) }}"
               class="hover:bg-gray-200 rounded py-2 px-4 mx-2 inline-block flex items-center">
                {{ $category->name }}

                @if ($category->childrenRecursive->isNotEmpty())
                    <svg class="w-4 h-4 ml-1 transition-transform duration-200"
                         :class="{ 'rotate-180': open }"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                @endif
            </a>

            {{-- DESKTOP DROPDOWN --}}
            @if ($category->childrenRecursive->isNotEmpty())
                <div x-cloak
                     class="hidden sm:block absolute left-0 top-full z-30 bg-white shadow-lg rounded-lg py-2 w-auto min-w-max whitespace-nowrap"
                     x-show="open"
                     @mouseenter="show()"
                     @mouseleave="hide()"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95">

                    @include('front.partials.category-menu', [
                        'categories' => $category->childrenRecursive,
                        'level' => $level + 1,
                        'orientation' => 'vertical',
                    ])
                </div>


                {{-- MOBILE ACCORDION --}}
                <div class="sm:hidden mt-2">

                    <button
                        type="button"
                        @click.stop="open = !open"
                        class="ml-2 text-xs text-gray-600 underline align-middle"
                        x-text="(open ? 'Hide' : 'Show') + ' subcategories'">
                    </button>

                    <div x-cloak
                         x-show="open"
                         x-transition
                         @click.outside="open = false"
                         @keydown.escape.window="open = false"
                         class="mt-2 w-auto min-w-max whitespace-nowrap bg-white rounded-lg shadow-lg py-2 px-1">

                        @include('front.partials.category-menu', [
                            'categories' => $category->childrenRecursive,
                            'level' => $level + 1,
                            'orientation' => 'vertical',
                        ])
                    </div>
                </div>
            @endif

        </li>

    @empty
        <li class="py-2 px-4">No Categories!</li>
    @endforelse

</ul>
