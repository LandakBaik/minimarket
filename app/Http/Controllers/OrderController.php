<?php

namespace App\Http\Controllers;

use App\Events\OrderPlaced;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Tampilkan halaman form order / transaksi baru
     */
    public function create()
    {
        return view('orders.create');
    }

    /**
     * Memproses pesanan dan memanggil (trigger) event OrderPlaced
     */
    public function store(Request $request)
    {
        // Data transaksi / order
        $order = (object) [
            'id' => rand(1001, 9999),
            'total' => $request->input('total', 75000),
        ];

        // ... setelah proses simpan order berhasil ...
        event(new OrderPlaced($order, auth()->user()));

        return back()->with('status', "Order #{$order->id} berhasil dibuat dengan total Rp " . number_format($order->total, 0, ',', '.'));
    }
}

