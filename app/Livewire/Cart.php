<?php

namespace App\Livewire;

use App\Models\Sparepart;
use App\Models\UnitAC;
use Livewire\Attributes\On;
use Livewire\Component;

class Cart extends Component
{
    public $cartItems = [];

    public $total = 0;

    public function mount()
    {
        $this->loadCart();
    }

    #[On('cart-updated')]
    public function loadCart()
    {
        $cart = session()->get('cart', []);
        $this->cartItems = [];
        $this->total = 0;

        foreach ($cart as $key => $item) {
            $product = null;
            if ($item['type'] === 'unit') {
                $product = UnitAC::find($item['id']);
            } elseif ($item['type'] === 'sparepart') {
                $product = Sparepart::find($item['id']);
            }

            if ($product) {
                $price = $product->display_price;
                $subtotal = $price * $item['quantity'];
                $this->total += $subtotal;

                // Handle image
                $image = asset('img/produk/placeholder.png');
                if ($item['type'] === 'unit' && !empty($product->path_foto_produk) && is_array($product->path_foto_produk)) {
                    $image = asset('storage/' . $product->path_foto_produk[0]);
                } elseif ($item['type'] === 'sparepart' && !empty($product->path_foto_sparepart) && is_array($product->path_foto_sparepart)) {
                    $image = asset('storage/' . $product->path_foto_sparepart[0]);
                }

                $this->cartItems[] = [
                    'key' => $key,
                    'id' => $item['id'],
                    'type' => $item['type'],
                    'name' => $item['type'] === 'unit' ? $product->nama_unit : $product->nama_sparepart,
                    'price' => $price,
                    'quantity' => $item['quantity'],
                    'image' => $image,
                    'subtotal' => $subtotal,
                ];
            }
        }
    }

    public function increment($key)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$key])) {
            $cart[$key]['quantity']++;
            session()->put('cart', $cart);
            $this->dispatch('cart-updated');
            $this->loadCart();
        }
    }

    public function decrement($key)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$key])) {
            if ($cart[$key]['quantity'] > 1) {
                $cart[$key]['quantity']--;
            } else {
                unset($cart[$key]);
            }
            session()->put('cart', $cart);
            $this->dispatch('cart-updated');
            $this->loadCart();
        }
    }

    public function remove($key)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$key])) {
            unset($cart[$key]);
            session()->put('cart', $cart);
            $this->dispatch('cart-updated');
            $this->loadCart();
        }
    }

    public function checkout()
    {
        $message = "Halo Global Servis Int., saya ingin memesan produk berikut:\n\n";

        foreach ($this->cartItems as $item) {
            $message .= '- ' . $item['name'] . ' (' . $item['quantity'] . 'x) - Rp ' . number_format($item['subtotal'], 0, ',', '.') . "\n";
        }

        $message .= "\nTotal: Rp " . number_format($this->total, 0, ',', '.') . "\n";
        $message .= "\nMohon info ketersediaan dan cara pembayarannya. Terima kasih.";

        $encodedMessage = urlencode($message);
        $whatsappUrl = "https://wa.me/6285695643257?text={$encodedMessage}"; // Replace with actual number

        return redirect()->away($whatsappUrl);
    }

    public function render()
    {
        return view('pages.cart')->extends('layouts.app');
    }
}
