@extends('dash.layouts.app')

@section('page-title', 'Add Linear Customer')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-2xl font-bold">Add New Linear Customer</h1>
                <p class="text-gray-500">Create a user in Linear and add custom details</p>
            </div>
        </div>

        <form id="customerForm">
            <!-- Linear User Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="border border-gray-100 rounded-md p-4">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <label for="name" class="text-sm font-medium text-gray-500">Full Name *</label>
                    </div>
                    <input type="text" id="name" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                </div>
                <div class="border border-gray-100 rounded-md p-4">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <label for="email" class="text-sm font-medium text-gray-500">Email *</label>
                    </div>
                    <input type="email" id="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                </div>
                <div class="border border-gray-100 rounded-md p-4">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <label for="phone" class="text-sm font-medium text-gray-500">Phone</label>
                    </div>
                    <input type="text" id="phone" name="phone" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div class="border border-gray-100 rounded-md p-4">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <label for="company" class="text-sm font-medium text-gray-500">Company</label>
                    </div>
                    <input type="text" id="company" name="company" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
            </div>
            <input type="hidden" name="id" value="{{request()->id}}">

            <!-- Custom Fields -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4">Custom Fields</h2>
                <div id="custom-fields-container">
                    <div class="custom-field grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Field Name</label>
                            <input type="text" name="custom_fields[0][name]" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="e.g., Department">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Field Value</label>
                            <input type="text" name="custom_fields[0][value]" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="e.g., Engineering">
                        </div>
                    </div>
                </div>
                <button type="button" id="add-custom-field" class="px-4 py-2 bg-blue-100 text-blue-600 rounded-md hover:bg-blue-200">
                    Add Custom Field
                </button>
            </div>

            <!-- Managers -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4">Managers</h2>
                <div id="managers-container">
                    <div class="manager-form grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Name</label>
                            <input type="text" name="managers[0][name]" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                            <input type="email" name="managers[0][email]" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Phone</label>
                            <input type="text" name="managers[0][phone]" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                    </div>
                </div>
                <button type="button" id="add-manager" class="px-4 py-2 bg-blue-100 text-blue-600 rounded-md hover:bg-blue-200">
                    Add Manager
                </button>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" class="px-8 py-4 bg-[#1E3A8A] text-white font-medium rounded-md hover:bg-blue-800">
                    Save Customer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

 
<script>
    setTimeout(() => {
        $(document).ready(function() {
    let customFieldCount = 1;
    let managerCount = 1;

    // Add custom field
    $('#add-custom-field').click(function() {
        const newField = `
            <div class="custom-field grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Field Name</label>
                    <input type="text" name="custom_fields[${customFieldCount}][name]" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="e.g., Department">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Field Value</label>
                    <input type="text" name="custom_fields[${customFieldCount}][value]" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="e.g., Engineering">
                </div>
            </div>`;
        $('#custom-fields-container').append(newField);
        customFieldCount++;
    });

    // Add manager
    $('#add-manager').click(function() {
        const newManager = `
            <div class="manager-form grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Name</label>
                    <input type="text" name="managers[${managerCount}][name]" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                    <input type="email" name="managers[${managerCount}][email]" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Phone</label>
                    <input type="text" name="managers[${managerCount}][phone]" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
            </div>`;
        $('#managers-container').append(newManager);
        managerCount++;
    });

    // Form submission
    $('#customerForm').submit(function(e) {
        e.preventDefault();

        $.ajax({
            url: "{{ route('customers.store') }}",
            type: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                alert('Customer created successfully!');
                $('#customerForm')[0].reset();
                // Reset custom fields
                $('#custom-fields-container').html(`
                    <div class="custom-field grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Field Name</label>
                            <input type="text" name="custom_fields[0][name]" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="e.g., Department">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Field Value</label>
                            <input type="text" name="custom_fields[0][value]" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="e.g., Engineering">
                        </div>
                    </div>
                `);
                // Reset managers
                $('#managers-container').html(`
                    <div class="manager-form grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Name</label>
                            <input type="text" name="managers[0][name]" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                            <input type="email" name="managers[0][email]" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Phone</label>
                            <input type="text" name="managers[0][phone]" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                    </div>
                `);
                customFieldCount = 1;
                managerCount = 1;
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors;
                let errorMessage = 'Please fix the following errors:\n';
                $.each(errors, function(key, value) {
                    errorMessage += `- ${value[0]}\n`;
                });
                alert(errorMessage);
            }
        });
    });
});
    }, 1000);
</script>
 