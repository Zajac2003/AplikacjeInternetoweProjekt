<x-app-layout>
    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
            @endif

            <div class="bg-white p-6 shadow-sm rounded-xl flex justify-between items-center border-l-8 border-indigo-500">
                <div>
                    <p class="text-sm text-gray-500 uppercase font-bold tracking-widest">Szczegoly wycieczki</p>
                    <h2 class="text-3xl font-black text-gray-900">{{ $group->name }}</h2>
                </div>
                <div class="flex gap-3">
                    @if(auth()->user()->isAdmin() || $group->owner_id === auth()->id())
                        <a href="{{ route('groups.edit', $group) }}" class="text-gray-600 font-bold">Edytuj</a>
                    @endif
                    <a href="{{ route('groups.index') }}" class="text-indigo-600 font-bold hover:text-indigo-800 transition">&larr; Powrot</a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div class="space-y-6">
                    <div class="bg-white p-6 shadow-sm rounded-xl border border-gray-200">
                        <h3 class="font-black text-lg mb-4 text-gray-800">Czlonkowie grupy</h3>
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

                        <form action="{{ route('groups.add-user', $group) }}" method="POST">
                            @csrf
                            <input type="email" name="email" placeholder="E-mail znajomego..." class="w-full border-gray-300 rounded-lg text-sm mb-2" required>
                            <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-2 rounded-lg">Dodaj do grupy</button>
                        </form>
                    </div>

                    <div class="bg-indigo-900 p-6 shadow-lg rounded-xl text-white">
                        <h3 class="font-black text-lg mb-4">Panel rozliczen</h3>
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
                                    <p class="text-[10px] text-indigo-300">Zaplacil: {{ number_format($data['paid'], 2) }} zl | Naleznosc: {{ number_format($data['owed'], 2) }} zl</p>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-6 pt-4 border-t border-indigo-800 text-center">
                            <p class="text-[10px] uppercase tracking-widest text-indigo-400">Suma wydatkow (trigger DB)</p>
                            <p class="text-2xl font-black text-white">{{ number_format($group->total_amount, 2) }} PLN</p>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2 space-y-6">

                    <div class="bg-white p-6 shadow-sm rounded-xl border border-gray-200">
                        <h3 class="font-black text-lg mb-4">Dodaj wydatek</h3>
                        <form action="{{ route('bills.store', $group) }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            @csrf
                            <input type="text" name="description" placeholder="Za co?" class="border-gray-300 rounded-lg" required>
                            <input type="number" step="0.01" name="amount" placeholder="Kwota" class="border-gray-300 rounded-lg" required>
                            <select name="payer_id" class="border-gray-300 rounded-lg" required>
                                @foreach($group->users as $user)
                                    <option value="{{ $user->id }}" @selected($user->id === auth()->id())>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="bg-green-600 text-white font-bold py-2 rounded-lg">Dodaj</button>
                        </form>
                    </div>

                    <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
                        <div class="p-4 bg-gray-50 border-b">
                            <h3 class="font-bold text-gray-700 uppercase text-sm">Historia rozliczen</h3>
                        </div>
                        <div class="divide-y divide-gray-100">
                            @forelse($group->bills as $bill)
                                <div class="p-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-black text-lg">{{ $bill->description }}</p>
                                            <p class="text-xs text-gray-500">Platnik: <span class="font-bold text-indigo-600">{{ $bill->payer->name }}</span></p>
                                            <p class="text-xl font-black text-green-600 mt-1">{{ number_format($bill->amount, 2) }} PLN</p>
                                        </div>
                                        <form action="{{ route('bills.destroy', [$group, $bill]) }}" method="POST" onsubmit="return confirm('Usunac rachunek?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 text-sm">Usun</button>
                                        </form>
                                    </div>

                                    @if($bill->splits->isNotEmpty())
                                        <div class="mt-3 text-xs text-gray-600">
                                            <span class="font-bold">Podzial (bill_splits):</span>
                                            @foreach($bill->splits as $split)
                                                {{ $split->user->name }}: {{ number_format($split->amount, 2) }} zl{{ !$loop->last ? ', ' : '' }}
                                            @endforeach
                                        </div>
                                    @endif

                                    @if($bill->items->isNotEmpty())
                                        <div class="mt-3 pl-3 border-l-2 border-indigo-200">
                                            <p class="text-xs font-bold text-gray-500 mb-1">Pozycje z paragonu:</p>
                                            @foreach($bill->items as $item)
                                                <p class="text-sm">{{ $item->name }} ({{ number_format($item->price, 2) }} zl x{{ $item->quantity }})
                                                    - przypisane: {{ $item->users->pluck('name')->join(', ') ?: 'brak' }}</p>
                                            @endforeach
                                        </div>
                                    @endif

                                    <details class="mt-3">
                                        <summary class="text-xs text-indigo-600 cursor-pointer">Dodaj pozycje z paragonu</summary>
                                        <form action="{{ route('bill-items.store', [$group, $bill]) }}" method="POST" class="mt-2 grid grid-cols-2 gap-2">
                                            @csrf
                                            <input type="text" name="name" placeholder="Nazwa pozycji" class="border-gray-300 rounded text-sm" required>
                                            <input type="number" step="0.01" name="price" placeholder="Cena" class="border-gray-300 rounded text-sm" required>
                                            <input type="number" name="quantity" value="1" min="1" class="border-gray-300 rounded text-sm" required>
                                            <div class="col-span-2">
                                                <p class="text-xs mb-1">Przypisz do:</p>
                                                @foreach($group->users as $user)
                                                    <label class="inline-flex items-center mr-3 text-sm">
                                                        <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="rounded">
                                                        <span class="ml-1">{{ $user->name }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                            <button class="col-span-2 bg-indigo-600 text-white text-sm py-1 rounded">Dodaj pozycje</button>
                                        </form>
                                    </details>
                                </div>
                            @empty
                                <div class="p-10 text-center text-gray-400 italic">Brak wydatkow.</div>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
