<x-app-layout>
<div class="calls-page p-4">
    {{-- ═══════════════════════════════════════
         PAGE HEADER
    ═══════════════════════════════════════ --}}
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-megaphone-fill text-primary me-2"></i>Calls for Applications
            </h4>
            <p class="text-muted small mb-0">Create and manage incubation programme calls · LEHSFF</p>
        </div>
       
      @if(auth()->user()->hasRole('Super Administrator'))
         
            <button class="btn btn-primary d-flex align-items-center gap-2 px-4"
                    data-bs-toggle="modal"
                    data-bs-target="#createCallModal">
                <i class="bi bi-plus-circle-fill"></i>
                <span>New Call</span>
            </button>
        @endif
    </div>

    <livewire:applications.calls-index />
    <livewire:applications.modals.calls-for-application />
</x-app-layout> 