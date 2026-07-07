<?php

namespace App\View\Components;

use Illuminate\View\Component;

/**
 * Modal component — renders a modal dialog with Alpine.js show/hide logic.
 *
 * Usage in Blade:
 *   <x-modal name="create-road" maxWidth="2xl">
 *       ... modal content ...
 *   </x-modal>
 *
 * To open:   window.dispatchEvent(new CustomEvent('open-modal', { detail: 'create-road' }))
 * To close:  window.dispatchEvent(new CustomEvent('close-modal', { detail: 'create-road' }))
 *
 * The matching Blade template is at resources/views/components/modal.blade.php.
 */
class Modal extends Component
{
    public function __construct(
        public string $name,
        public bool $show = false,
        public string $maxWidth = '2xl',
    ) {}

    public function render()
    {
        return view('components.modal');
    }
}
