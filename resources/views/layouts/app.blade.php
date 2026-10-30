<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Toastr CSS untuk Notifikasi Visual (Acara 32 Langkah 3 Poin 5) -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

        <!-- Scripts Vite -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Pusher & Echo CDN (Sebagai cadangan jika npm build belum dijalankan) -->
        <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- jQuery & Toastr JS (Acara 32 Langkah 3 Poin 5) -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

        <!-- Laravel Echo Setup & Listener (Acara 32 Langkah 1 & 3) -->
        @auth
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Inisialisasi window.Echo jika belum diinisialisasi oleh bundler Vite
                if ((!window.Echo || typeof window.Echo.private !== 'function') && typeof Echo !== 'undefined') {
                    window.Pusher = Pusher;
                    window.Echo = new Echo({
                        broadcaster: 'reverb',
                        key: '{{ env('REVERB_APP_KEY', 'minimarketkey') }}',
                        wsHost: '{{ env('REVERB_HOST', '127.0.0.1') }}',
                        wsPort: {{ env('REVERB_PORT', 8080) }},
                        wssPort: {{ env('REVERB_PORT', 8080) }},
                        forceTLS: false,
                        enabledTransports: ['ws', 'wss'],
                        authorizer: (channel, options) => {
                            return {
                                authorize: (socketId, callback) => {
                                    fetch('/broadcasting/auth', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                        },
                                        body: JSON.stringify({
                                            socket_id: socketId,
                                            channel_name: channel.name
                                        })
                                    })
                                    .then(response => {
                                        if (!response.ok) {
                                            throw new Error(`HTTP error! status: ${response.status}`);
                                        }
                                        return response.json();
                                    })
                                    .then(data => callback(null, data))
                                    .catch(error => callback(error, null));
                                }
                            };
                        }
                    });
                }

                if (window.Echo && typeof window.Echo.private === 'function') {
                    console.log('Menghubungkan ke channel user.{{ auth()->id() }}...');

                    const handleOrderPlaced = (e) => {
                        console.log('Order Placed Event Diterima:', e);

                        // Langkah 3 Poin 5: Notifikasi visual menggunakan Toastr
                        if (typeof toastr !== 'undefined') {
                            toastr.options = {
                                "closeButton": true,
                                "progressBar": true,
                                "positionClass": "toast-top-right",
                                "timeOut": "7000"
                            };
                            toastr.success(
                                `Order #${e.order_id} telah ditempatkan! Total: Rp ${Number(e.total).toLocaleString('id-ID')}`,
                                'Notifikasi Transaksi'
                            );
                        }

                        // Langkah 3 Poin 4: Alert bawaan
                        alert(`Order #${e.order_id} telah ditempatkan! Total: Rp ${Number(e.total).toLocaleString('id-ID')}`);
                    };

                    window.Echo.private('user.{{ auth()->id() }}')
                        .listen('OrderPlaced', handleOrderPlaced)
                        .listen('.order.placed', handleOrderPlaced);
                } else {
                    console.warn('Laravel Echo belum tersedia.');
                }
            });
        </script>
        @endauth
    </body>
</html>
