<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class CartHandler extends Component
{
    #[On('add-to-cart')]
    public function addToCart($productId, $type = 'unit')
    {
        $cart = session()->get('cart', []);
        $key = $type.'-'.$productId;

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

        // Dispatch event for other components (like CartCounter)
        $this->dispatch('cart-updated');
    }

    public function render()
    {
        return <<<'HTML'
        <div></div>
        HTML;
    }
}
