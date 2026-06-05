<x-app-layout>
    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <h2 class="text-xl font-bold mb-4">Edytuj grupe</h2>
                <form action="{{ route('groups.update', $group) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="text" name="name" value="{{ $group->name }}" class="w-full border-gray-300 rounded-md" required>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md">Zapisz</button>
                    <a href="{{ route('groups.show', $group) }}" class="text-gray-600 ml-2">Anuluj</a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
