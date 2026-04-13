<x-app-layout>
<div class="cds container py-4">

    <livewire:applications.show-call :id="$id" />
    <livewire:applications.incubation-application.incubation-application-list :id="$id" />

</div>

<livewire:notify />
<livewire:applications.modals.calls-for-application />
<livewire:applications.modals.apply-for-incubation  :id="$id" />
</x-app-layout> 