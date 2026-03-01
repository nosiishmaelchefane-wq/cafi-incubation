<?php

use Livewire\Component;

new class extends Component
{
    public $show = false;
    public $type = 'success';
    public $message = '';
    
    protected $listeners = ['notify' => 'showNotification'];

    public function showNotification($data)
    {
        $this->type = $data['type'] ?? 'success';
        $this->message = $data['message'] ?? '';
        $this->show = true;
        
        $this->dispatch('hideNotification')->delay(3000);
    }

    public function hide()
    {
        $this->show = false;
    }
};
?>
{{-- resources/views/livewire/notification.blade.php --}}
<div>
    @if($show)
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
            <div class="toast align-items-center text-bg-{{ $type }} border-0 show" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        @if($type == 'success')
                            <i class="bi bi-check-circle-fill me-2"></i>
                        @elseif($type == 'error')
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        @elseif($type == 'warning')
                            <i class="bi bi-exclamation-circle-fill me-2"></i>
                        @else
                            <i class="bi bi-info-circle-fill me-2"></i>
                        @endif
                        {{ $message }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" wire:click="hide"></button>
                </div>
            </div>
        </div>
    @endif
</div>