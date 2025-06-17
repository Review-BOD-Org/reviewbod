<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-2xl font-bold">Manage User</h1>
                <p class="text-gray-500">Add and update custom user details</p>
            </div>
        </div>

        <form id="customerForm">
            <input type="hidden" name="id" value="{{ request()->id }}">

            <!-- Custom Fields -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4">Custom Fields</h2>
                <div id="custom-fields-container">
                    @if (!empty($customFields))
                        @foreach ($customFields as $index => $field)
                            <div class="custom-field grid grid-cols-1 md:grid-cols-2 gap-4 mb-4" data-index="{{ $index }}">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Field Name</label>
                                    <input type="text" name="custom_fields[{{ $index }}][name]" value="{{ $field->field_name }}" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="e.g., Department">
                                </div>
                                <div class="flex gap-2">
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-500 mb-1">Field Value</label>
                                        <input type="text" name="custom_fields[{{ $index }}][value]" value="{{ $field->field_value }}" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="e.g., Engineering">
                                    </div>
                                    <button type="button" class="remove-custom-field mt-7 px-3 py-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200">Remove</button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="custom-field grid grid-cols-1 md:grid-cols-2 gap-4 mb-4" data-index="0">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Field Name</label>
                                <input type="text" name="custom_fields[0][name]" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="e.g., Department">
                            </div>
                            <div class="flex gap-2">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Field Value</label>
                                    <input type="text" name="custom_fields[0][value]" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="e.g., Engineering">
                                </div>
                                <button type="button" class="remove-custom-field mt-7 px-3 py-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200">Remove</button>
                            </div>
                        </div>
                    @endif
                </div>
                <button type="button" id="add-custom-field" class="px-4 py-2 bg-blue-100 text-blue-600 rounded-md hover:bg-blue-200">
                    Add Custom Field
                </button>
            </div>

            <!-- Managers -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4">Managers</h2>
                <div id="managers-container">
                    @if (!empty($managers))
                        @foreach ($managers as $index => $manager)
                            <div class="manager-form grid grid-cols-1 md:grid-cols-3 gap-4 mb-4" data-index="{{ $index }}">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Name</label>
                                    <input type="text" name="managers[{{ $index }}][name]" value="{{ $manager->name }}" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                                    <input type="email" name="managers[{{ $index }}][email]" value="{{ $manager->email }}" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                </div>
                                <div class="flex gap-2">
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-500 mb-1">Phone</label>
                                        <input type="text" name="managers[{{ $index }}][phone]" value="{{ $manager->phone ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                    </div>
                                    <button type="button" class="remove-manager mt-7 px-3 py-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200">Remove</button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="manager-form grid grid-cols-1 md:grid-cols-3 gap-4 mb-4" data-index="0">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Name</label>
                                <input type="text" name="managers[0][name]" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                                <input type="email" name="managers[0][email]" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>
                            <div class="flex gap-2">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Phone</label>
                                    <input type="text" name="managers[0][phone]" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                </div>
                                <button type="button" class="remove-manager mt-7 px-3 py-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200">Remove</button>
                            </div>
                        </div>
                    @endif
                </div>
                <button type="button" id="add-manager" class="px-4 py-2 bg-blue-100 text-blue-600 rounded-md hover:bg-blue-200">
                    Add Manager
                </button>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" class="px-8 py-4 bg-[#1E3A8A] text-white font-medium rounded-md hover:bg-blue-800">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    let customFieldCount = {{ count($customFields) ?: 1 }};
    let managerCount = {{ count($managers) ?: 1 }};

    // Add custom field
    $('#add-custom-field').click(function() {
        const newField = `
            <div class="custom-field grid grid-cols-1 md:grid-cols-2 gap-4 mb-4" data-index="${customFieldCount}">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Field Name</label>
                    <input type="text" name="custom_fields[${customFieldCount}][name]" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="e.g., Department" required>
                </div>
                <div class="flex gap-2">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Field Value</label>
                        <input type="text" name="custom_fields[${customFieldCount}][value]" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="e.g., Engineering" required>
                    </div>
                    <button type="button" class="remove-custom-field mt-7 px-3 py-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200">Remove</button>
                </div>
            </div>`;
        $('#custom-fields-container').append(newField);
        customFieldCount++;
    });

    // Add manager
    $('#add-manager').click(function() {
        const newManager = `
            <div class="manager-form grid grid-cols-1 md:grid-cols-3 gap-4 mb-4" data-index="${managerCount}">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Name</label>
                    <input required type="text" name="managers[${managerCount}][name]" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                    <input required type="email" name="managers[${managerCount}][email]" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div class="flex gap-2">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Phone</label>
                        <input required type="text" name="managers[${managerCount}][phone]" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <button type="button" class="remove-manager mt-7 px-3 py-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200">Remove</button>
                </div>
            </div>`;
        $('#managers-container').append(newManager);
        managerCount++;
    });

    // Remove custom field
    $(document).on('click', '.remove-custom-field', function() {
        $(this).closest('.custom-field').remove();
    });

    // Remove manager
    $(document).on('click', '.remove-manager', function() {
        $(this).closest('.manager-form').remove();
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
            beforeSend: function() {
                $("button[type=submit]").attr("disabled", true).text("Saving...");
            },
            success: function(response) {
                toastr.success(response.message || 'User updated successfully!');
                // Optionally redirect or refresh data
                window.location.reload(); // Adjust as needed
            },
            error: function(xhr) {
                let errorMsg = xhr.responseJSON?.error || 'Failed to save data';
                toastr.error(errorMsg);
            },
            complete: function() {
                $("button[type=submit]").attr("disabled", false).text("Save");
            }
        });
    });
});
</script>