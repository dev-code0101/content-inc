<x-admin-layout>
    <div class="w-full overflow-x-hidden border-t flex flex-col bg-gray-50">
        <main class="w-full flex-grow p-8">
            <div class="flex items-center mb-8">
                <i class="fas fa-chart-bar text-blue-600 text-2xl mr-3"></i>
                <h1 class="text-2xl font-semibold text-gray-800">Statistics</h1>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
                @foreach ($statistics as $stat)
                    <div class="bg-white rounded-2xl shadow-md hover:shadow-lg transition p-6 text-center">
                        <div class="text-lg font-semibold text-gray-700 mb-2">{{ $stat['label'] }}</div>
                        <div class="text-5xl font-bold text-blue-600">{{ $stat['value'] }}</div>
                    </div>
                @endforeach
            </div>
        </main>
    </div>
</x-admin-layout>
