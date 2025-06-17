<!-- views/dash/members.blade.php -->
@extends('dash.layouts.app')

@section('page-title', 'Settings')

@section('content')
<div class="container mx-auto px-4 py-6 bg-white h-screen">
    <!-- Tab Navigation -->
    <div class="border-b border-gray-200 mb-6">
        <ul class="flex -mb-px" id="tabNav">
            <li class="mr-1">
                <a href="#" class="inline-block py-4 px-4 border-b-2 border-[#1E3A8A] font-medium text-sm text-[#1E3A8A] tab-item" data-tab="personal">Personal</a>
            </li>
            <li class="mr-1">
                <a href="#" class="inline-block py-4 px-4 text-gray-500 hover:text-gray-700 font-medium text-sm tab-item" data-tab="general">General</a>
            </li>
            <li class="mr-1">
                <a href="#" class="inline-block py-4 px-4 text-gray-500 hover:text-gray-700 font-medium text-sm tab-item" data-tab="company">Notifications</a>
            </li>
            <li class="mr-1">
                <a href="#" class="inline-block py-4 px-4 text-gray-500 hover:text-gray-700 font-medium text-sm tab-item" data-tab="invitations">Invitations</a>
            </li>
            <li class="mr-1">
                <a href="#" class="inline-block py-4 px-4 text-gray-500 hover:text-gray-700 font-medium text-sm tab-item" data-tab="subscription">Subscription</a>
            </li>
        </ul>
    </div>

    <!-- Tab Content Areas -->
    <div class="tab-content-container">
        <!-- Personal Tab Content -->
        <div id="personal" class="tab-content block">
            @include("dash.settings.personal")
        </div>

        <!-- General Tab Content -->
        <div id="general" class="tab-content hidden">
             @include("dash.settings.general")
        </div>
  <!-- Company Tab Content -->
        <div id="company" class="tab-content hidden">
            <h2 class="text-lg font-medium mb-4">Notification Channel</h2>
            @include("dash.settings.notification")
        </div> 

        <!-- Invitations Tab Content -->
        <div id="invitations" class="tab-content hidden"> 
            @include("dash.settings.invitation")
        </div>

        <!-- Subscription Tab Content -->
        <div id="subscription" class="tab-content hidden"> 
            @include("dash.settings.sub")
        </div>
    </div>
</div>

<!-- JavaScript for Tab Functionality -->
<script>
 document.addEventListener('DOMContentLoaded', function() {
    const tabItems = document.querySelectorAll('.tab-item');
    const tabContents = document.querySelectorAll('.tab-content');
    
    // Function to activate a tab
    function activateTab(tabElement) {
        const tabToActivate = tabElement.getAttribute('data-tab');
        
        // Reset all tabs
        tabItems.forEach(tab => {
            tab.classList.remove('border-b-2', 'border-[#1E3A8A]', 'text-[#1E3A8A]');
            tab.classList.add('text-gray-500');
        });
        
        // Activate selected tab
        tabElement.classList.remove('text-gray-500');
        tabElement.classList.add('border-b-2', 'border-[#1E3A8A]', 'text-[#1E3A8A]');
        
        // Hide all content
        tabContents.forEach(content => {
            content.classList.add('hidden');
            content.classList.remove('block');
        });
        
        // Show relevant content
        document.getElementById(tabToActivate).classList.remove('hidden');
        document.getElementById(tabToActivate).classList.add('block');
    }
    
    // Set up click events for all tabs
    tabItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            activateTab(this);
        });
    });
});

    function delete_linked(id) {
        if (confirm("Are you sure you want to delete this linked app?")) {
            $.post('{{ route('customers.delete_linked') }}', {
                id: id,
                _token: '{{ csrf_token() }}'
            }, function(response) {
     
                    toastr.success("Linked app deleted successfully.");
                     $("#linked_" + id).remove();
                
            }).fail(function(xhr) {
                toastr.error(xhr.responseJSON.message);
            });
        }
    }
</script>
@endsection