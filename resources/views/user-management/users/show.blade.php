<x-app-layout>


     <livewire:user-management.user-details :id="$user->id" />
    <livewire:user-management.modals.add-users :id="$user->id" />
            
    <livewire:user-management.modals.suspend-user/>
    <livewire:user-management.modals.change-role/>
    <livewire:user-management.modals.edit-registration-details :id="$user->id"/>
    <livewire:notify />

</x-app-layout>