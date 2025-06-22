@include('dash.layouts.partials.head')
<div class="p-5">
    <div class="border rounded-md">
        <div class="p-4 flex border-b justify-between">
            <div class="flex flex-col">
                <h2 class="text-[25px] font-bold">Delete Account</h2>
                <span class="font-light text-lg">Manage your account data</span>
            </div>
        </div>
        <div id="personalInfoForm" class="p-5">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
                <!-- Left side - Illustration -->
                <div class="flex justify-center  items-center lg:justify-start flex flex-col">
                    <img src="/delete_account.svg" alt="Delete Account" class="w-80 h-80 object-contain">
                      <!-- Delete Button -->
                    <div class="pt-4">
                        <button 
                            id="deleteAccountBtn" 
                            class="bg-red-500 hover:bg-red-600 text-white font-medium px-6 py-3 rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                        >
                            I ACCEPT, DELETE MY ACCOUNT
                        </button>
                    </div>
                </div>
                
                <!-- Right side - Content -->
                <div class="space-y-6">
                    <div class="space-y-4">
                        <p class="text-lg text-gray-800 leading-relaxed">
                            By requesting the deletion of your account on <strong>Reviewbod</strong>, you agree to the following:
                        </p>
                        
                        <div class="space-y-4 text-gray-700">
                            <div class="flex items-start space-x-3">
                                <span class="text-lg font-semibold text-gray-900 mt-0.5">1.</span>
                                <p class="text-base leading-relaxed">
                                    You confirm that you are the rightful owner of the account and are initiating this request voluntarily.
                                </p>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <span class="text-lg font-semibold text-gray-900 mt-0.5">2.</span>
                                <p class="text-base leading-relaxed">
                                    Account deletion is permanent and cannot be reversed.
                                </p>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <span class="text-lg font-semibold text-gray-900 mt-0.5">3.</span>
                                <p class="text-base leading-relaxed">
                                    All personal information associated with your account will be permanently deleted from our active systems. This includes your name, email address, profile data, and account settings.
                                </p>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <span class="text-lg font-semibold text-gray-900 mt-0.5">4.</span>
                                <p class="text-base leading-relaxed">
                                    Any content you have created may also be deleted or anonymized, depending on the platform's retention policy.
                                </p>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <span class="text-lg font-semibold text-gray-900 mt-0.5">5.</span>
                                <p class="text-base leading-relaxed">
                                    Some data may be retained for legal, regulatory, or security purposes, including financial transactions or abuse prevention.
                                </p>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <span class="text-lg font-semibold text-gray-900 mt-0.5">6.</span>
                                <p class="text-base leading-relaxed">
                                    After deletion, you will lose access to the account, and any active subscriptions, stored data, or associated content will be removed.
                                </p>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <span class="text-lg font-semibold text-gray-900 mt-0.5">7.</span>
                                <p class="text-base leading-relaxed">
                                    If your account is linked to third-party services (e.g., Google, Apple, Facebook), deleting your account does not remove data from those services. You must manage that separately.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                  
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L4.316 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-2">Confirm Account Deletion</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Are you absolutely sure you want to delete your account? This action cannot be undone.
                </p>
            </div>
            <div class="items-center px-4 py-3 space-x-3 flex justify-center">
                <button 
                    id="cancelDelete" 
                    class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300"
                >
                    Cancel
                </button>
                <button 
                    id="confirmDelete" 
                    class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                >
                    Delete Account
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Setup CSRF token for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Show confirmation modal when delete button is clicked
    $('#deleteAccountBtn').click(function() {
        $('#confirmationModal').removeClass('hidden');
    });
    
    // Hide modal when cancel is clicked
    $('#cancelDelete').click(function() {
        $('#confirmationModal').addClass('hidden');
    });
    
    // Hide modal when clicking outside
    $('#confirmationModal').click(function(e) {
        if (e.target === this) {
            $(this).addClass('hidden');
        }
    });
    
    // Handle account deletion
    $('#confirmDelete').click(function() {
        var button = $(this);
        var originalText = button.text();
        
        // Show loading state
        button.prop('disabled', true).text('Deleting...');
        
        $.ajax({
            url: '{{route('user.delete_account')}}', // Adjust this URL to match your route
            type: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Hide modal
                $('#confirmationModal').addClass('hidden');
                
                // Show success message
                toastr.success('Your account has been successfully deleted. You will be redirected shortly.', 'Account Deleted');
                
                // Redirect after 3 seconds
                setTimeout(function() {
                    window.location.href = '/auth/login'; // Adjust redirect URL as needed
                }, 3000);
            },
            error: function(xhr, status, error) {
                // Hide modal
                $('#confirmationModal').addClass('hidden');
                
                // Reset button
                button.prop('disabled', false).text(originalText);
                
                // Show error message
                var errorMessage = 'An error occurred while deleting your account. Please try again.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                
                toastr.error(errorMessage, 'Deletion Failed');
            }
        });
    });
});
</script>