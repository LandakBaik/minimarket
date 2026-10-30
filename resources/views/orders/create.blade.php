<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Simulasi Transaksi Order (ACARA 32)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if (session('status'))
                    <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <h3 class="text-lg font-medium text-gray-900 mb-2">Buat Order / Transaksi Baru</h3>
                <p class="text-sm text-gray-600 mb-6">
                    Menekan tombol di bawah akan memanggil <code class="bg-gray-100 px-1 py-0.5 rounded text-indigo-600">OrderController::store</code> dan memicu event broadcast <code class="bg-gray-100 px-1 py-0.5 rounded text-indigo-600">OrderPlaced</code> secara real-time melalui Reverb ke WebSocket client.
                </p>

                <form method="POST" action="{{ route('orders.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="total" :value="__('Nominal Transaksi (Rp)')" />
                        <x-text-input id="total" class="block mt-1 w-full" type="number" name="total" value="75000" required />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>
                            {{ __('Kirim Order (Trigger Event OrderPlaced)') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

