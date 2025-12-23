<x-blog-layout title="{{ $tag }}">
    <!-- Posts Section -->
    <section class="w-full md:w-2/3 flex flex-col items-center px-3">


        <!-- Article -->
       <x-post-list :posts="$posts" />

    </section>
</x-blog-layout>
