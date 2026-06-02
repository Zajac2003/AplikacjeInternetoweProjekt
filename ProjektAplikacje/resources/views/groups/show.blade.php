<x-app-layout>
    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white p-6 shadow-sm rounded-xl flex justify-between items-center border-l-8 border-indigo-500">
                <div>
                    <p class="text-sm text-gray-500 uppercase font-bold tracking-widest">Szczegóły wycieczki</p>
                    <h2 class="text-3xl font-black text-gray-900">{{ $group->name }}</h2>
                </div>
                <a href="{{ route('groups.index') }}" class="text-indigo-600 font-bold hover:text-indigo-800 transition">&larr; Powrót do listy</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div class="space-y-6">
                    <div class="bg-white p-6 shadow-sm rounded-xl border border-gray-200">
                        <h3 class="font-black text-lg mb-4 text-gray-800 flex items-center">
                            <span class="mr-2">👥</span> Członkowie grupy
                        </h3>
                        <div class="space-y-3 mb-6">
                            @foreach($group->users as $user)
                                <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                                    <span class="font-medium text-gray-700">{{ $user->name }}</span>
                                    @if($user->id === $group->owner_id)
                                        <span class="text-[10px] bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-bold uppercase">Lider</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-100">
                            <p class="text-xs font-bold text-gray-400 uppercase mb-3">Zaproś znajomego</p>
                            <form action="{{ route('groups.add-user', $group) }}" method="POST">
                                @csrf
                                <input type="email" name="email" placeholder="Wpisz e-mail znajomego..." class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 mb-2" required>
                                <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-2 rounded-lg hover:bg-indigo-700 transition shadow-md">Dodaj do grupy</button>
                            </form>
                        </div>
                    </div>

                    <div class="bg-indigo-900 p-6 shadow-lg rounded-xl text-white">
                        <h3 class="font-black text-lg mb-4 flex items-center italic">
                            <span class="mr-2">📊</span> Panel Rozliczeń
                        </h3>
                        <div class="space-y-4">
                            @foreach($group->getBalances() as $data)
                                <div class="border-b border-indigo-800 pb-2">
                                    <div class="flex justify-between items-center">
                                        <span class="font-bold text-sm">{{ $data['user']->name }}</span>
                                        @if($data['balance'] > 0)
                                            <span class="text-green-400 font-black text-xs">+{{ number_format($data['balance'], 2) }} PLN</span>
                                        @else
                                            <span class="text-red-400 font-black text-xs">{{ number_format($data['balance'], 2) }} PLN</span>
                                        @endif
                                    </div>
                                    <p class="text-[10px] text-indigo-300">Wydał łącznie: {{ number_format($data['paid'], 2) }} zł</p>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 pt-4 border-t border-indigo-800 text-center">
                            <p class="text-[10px] uppercase tracking-widest text-indigo-400">Suma wydatków (Baza/Trigger)</p>
                            <p class="text-2xl font-black text-white">{{ number_format($group->total_amount, 2) }} PLN</p>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2 space-y-6">

                    <div class="bg-white p-6 shadow-sm rounded-xl border border-gray-200">
                        <h3 class="font-black text-lg mb-4 text-gray-800 flex items-center">
                            <span class="mr-2">💸</span> Dodaj nowy wydatek
                        </h3>
                        <form action="{{ route('bills.store', $group) }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @csrf
                            <div class="md:col-span-1">
                                <input type="text" name="description" placeholder="Za co? (np. Pizza)" class="w-full border-gray-300 rounded-lg focus:ring-green-500" required>
                            </div>
                            <div>
                                <input type="number" step="0.01" name="amount" placeholder="Kwota (PLN)" class="w-full border-gray-300 rounded-lg focus:ring-green-500" required>
                            </div>
                            <button type="submit" class="bg-green-600 text-white font-black py-2 rounded-lg hover:bg-green-700 transition shadow-md uppercase tracking-tighter">
                                Dodaj rachunek
                            </button>
                        </form>
                    </div>

                    <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
                        <div class="p-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="font-bold text-gray-700 uppercase text-sm tracking-widest">Historia rozliczeń</h3>
                        </div>
                        <div class="divide-y divide-gray-100">
                            @forelse($group->bills as $bill)
                                <div class="p-4 flex justify-between items-center hover:bg-gray-50 transition">
                                    <div>
                                        <p class="font-black text-gray-900 text-lg">{{ $bill->description }}</p>
                                        <p class="text-xs text-gray-500">Zapłacone przez: <span class="font-bold text-indigo-600">{{ $bill->payer->name }}</span></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xl font-black text-green-600">{{ number_format($bill->amount, 2) }} PLN</p>
                                        <p class="text-[10px] text-gray-400">{{ $bill->created_at->format('d.m.Y H:i') }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="p-10 text-center">
                                    <p class="text-gray-400 italic">Brak wydatków w tej grupie. Czas coś kupić!</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
