<x-app-layout>


   <div class="container-fluid py-4 px-3 px-md-4">

    <!-- ── Page Header ── -->
   <livewire:user-management.add-system-users/>

    <!-- ── Stat Cards ── -->
   <livewire:user-management.users-stats />

    <!-- ── Main Card ── -->
    <livewire:user-management.manage-users />
    <livewire:notify />
    <!-- /card -->


</div><!-- /container -->
  

</x-app-layout>