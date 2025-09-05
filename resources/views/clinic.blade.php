@extends('layouts.app')

@section('content')
@livewire('appointment-list')


<script>
    // Sidebar Toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const contentArea = document.getElementById('contentArea');
    const mobileMenuButton = document.getElementById('mobileMenuButton');

    sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('sidebar-collapsed');
        contentArea.classList.toggle('content-expanded');
    });

    mobileMenuButton.addEventListener('click', () => {
        sidebar.classList.toggle('sidebar-open');
    });

    // Responsive adjustments
    function handleResize() {
        if (window.innerWidth < 768) {
            sidebar.classList.add('sidebar-collapsed');
            contentArea.classList.add('content-expanded');
        } else {
            sidebar.classList.remove('sidebar-collapsed');
            contentArea.classList.remove('content-expanded');
        }
    }

    window.addEventListener('resize', handleResize);
    handleResize(); // Initial check
</script>
@endsection