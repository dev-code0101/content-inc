<x-admin-layout>
    <div class="w-full min-h-screen bg-gray-50 flex flex-col">
        <main class="flex-grow p-8">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-semibold text-gray-800">Roles</h1>
                    <p class="text-gray-500">View system roles.</p>
                </div>
            </div>

            <!-- Roles Table -->
            <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm text-gray-700">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs font-semibold tracking-wider">
                            <tr>
                                <th class="py-4 px-6 w-16">#</th>
                                <th class="py-4 px-6">Name</th>
                                <th class="py-4 px-6">Users Count</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse ($roles as $role)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-3 px-6 font-medium text-gray-800">
                                        {{ $role->id }}
                                    </td>
                                    <td class="py-3 px-6">
                                        <span class="font-medium text-gray-800">
                                            {{ $role->name }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-6">
                                        {{ $role->users_count }} Users
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="py-10 text-center">
                                        <div class="flex flex-col items-center gap-2 text-gray-500">
                                            <i class="fas fa-users-cog text-2xl"></i>
                                            <div class="font-medium">No roles found</div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</x-admin-layout>
