<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class CartCounter extends Component
{
    public $count = 0;

    public function mount()
    {
        $this->updateCount();
    }

    #[On('cart-updated')]
    public function updateCount()
    {
        $cart = session()->get('cart', []);
        $this->count = array_sum(array_column($cart, 'quantity'));
    }

    #[On('add-to-cart')]
    public function addToCart($productId, $type = 'unit')
    {
        $cart = session()->get('cart', []);
        $key = $type . '-' . $productId;
        
        if (isset($cart[$key])) {
            $cart[$key]['quantity']++;
        } else {
            $cart[$key] = [
                'id' => $productId,
                'type' => $type,
                'quantity' => 1,
            ];
        }
        
        session()->put('cart', $cart);
        $this->updateCount();
        
        // Dispatch event for other components if needed
        $this->dispatch('cart-updated'); 
    }

    public function render()
    {
        return view('livewire.cart-counter');
    }
}
