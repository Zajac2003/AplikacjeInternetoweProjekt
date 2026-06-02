<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panel Główny') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-12 text-center text-gray-900">
                    <h3 class="text-3xl font-bold mb-4">Witaj ponownie, {{ auth()->user()->name }}! 👋</h3>
                    <p class="mb-10 text-lg text-gray-600">Twój system rozliczeń 5.0 jest gotowy do pracy.</p>

                    <a href="{{ route('groups.index') }}" class="inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-black py-5 px-10 rounded-2xl shadow-2xl transition-all hover:scale-110 text-xl">
                        📂 ZARZĄDZAJ MOIMI GRUPAMI
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
