<x-app-layout>
    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-lg p-6">
                <h2 class="text-2xl font-bold mb-6">Panel administratora - uzytkownicy</h2>

                @if(session('success'))
                    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
                @endif

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Imie</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Rola</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Akcje</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($users as $user)
                            <tr>
                                <td class="px-4 py-3">{{ $user->name }}</td>
                                <td class="px-4 py-3">{{ $user->email }}</td>
                                <td class="px-4 py-3">
                                    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="flex gap-2 items-center">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="name" value="{{ $user->name }}">
                                        <select name="role" class="border-gray-300 rounded text-sm" onchange="this.form.submit()">
                                            <option value="user" @selected($user->role === 'user')>user</option>
                                            <option value="admin" @selected($user->role === 'admin')>admin</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Usunac uzytkownika?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 text-sm">Usun</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
