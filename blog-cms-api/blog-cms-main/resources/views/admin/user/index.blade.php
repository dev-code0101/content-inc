<x-admin-layout>
    <div class="w-full min-h-screen bg-gray-50 flex flex-col">
        <main class="flex-grow p-8">
            <!-- Page Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-semibold text-gray-800">Users</h1>
                    <p class="text-gray-500">Manage users and assign roles easily.</p>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm text-gray-700">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs font-semibold tracking-wider">
                            <tr>
                                <th class="py-4 px-6">#</th>
                                <th class="py-4 px-6">Name</th>
                                <th class="py-4 px-6">Role</th>
                                <th class="py-4 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($users as $user)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-3 px-6 font-medium text-gray-800">{{ $user->id }}</td>
                                    <td class="py-3 px-6">{{ $user->name }}</td>
                                    <td class="py-3 px-6">
                                        <span class="inline-flex items-center text-xs font-medium px-2 py-1 bg-blue-50 text-blue-700 rounded-full">
                                            {{ $user->role->name ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-6 text-right space-x-2">
                                        <a href="{{ route('admin.user.edit', $user->id) }}"
                                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-700 bg-green-100 rounded-md hover:bg-green-200 transition">
                                            <i class="fas fa-user-shield mr-1"></i> Assign Role
                                        </a>

                                        <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete this user?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 rounded-md hover:bg-red-200 transition">
                                                <i class="fas fa-trash mr-1"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-gray-500">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                {!! $users->links() !!}
            </div>
        </main>
    </div>
</x-admin-layout>
