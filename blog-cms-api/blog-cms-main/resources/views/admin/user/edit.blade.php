<x-admin-layout>
    <div class="w-full min-h-screen bg-gray-50 flex flex-col">
        <main class="flex-grow p-8">

            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-semibold text-gray-800">Edit User Role</h1>
                    <p class="text-gray-500">Assign or change the user’s role.</p>
                </div>

                <a href="{{ route('admin.user.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium
                   rounded-lg border border-gray-300 shadow-sm hover:bg-gray-200 transition">
                    <i class="fas fa-arrow-left mr-2"></i> Back to List
                </a>
            </div>

            <!-- Card -->
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

                    <form method="POST" action="{{ route('admin.user.update', $user->id) }}" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <div class="grid gap-6 md:grid-cols-2">
                            {{-- Role --}}
                            <x-admin.select name="role_id" label="Role Name" :options="$roles" :value="old('role_id', $user->role_id)"
                                placeholder="— Select role —" icon="fas fa-user-shield" />
                        </div>

                        <!-- Sticky Actions -->
                        <div
                            class="sticky bottom-0 bg-white/80 backdrop-blur-sm border-t border-gray-100
                            -mx-6 md:-mx-8 px-6 md:px-8 py-4">
                            <div class="flex items-center justify-end gap-3">

                                <a href="{{ route('admin.user.index') }}"
                                    class="inline-flex items-center px-4 py-2 bg-white text-gray-700 text-sm font-medium
                                   rounded-lg border border-gray-300 hover:bg-gray-50 transition">
                                    Cancel
                                </a>

                                <button type="submit"
                                    class="inline-flex items-center px-5 py-2.5 bg-green-600 text-white text-sm
                                        font-medium rounded-lg shadow hover:bg-green-700 transition">
                                    <i class="fas fa-save mr-2"></i>
                                    Update Role
                                </button>

                            </div>
                        </div>

                    </form>

                </div>
            </div>

        </main>
    </div>
</x-admin-layout>
