     @include('dash.layouts.partials.head')
     <div class="p-5">

        <div class="border rounded-md">

     <div class="p-4 flex items-center border-b">
    <h2 class="text-lg font-medium ">Personal Information</h2>
     </div>

         <form id="personalInfoForm" class="space-y-6 p-5">
             @csrf

             <!-- Alert for success/error messages -->
             <div id="personalFormAlert" class="hidden mb-4 p-4 rounded-md"></div>

             <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                 <!-- Name -->
                 <div>
                     <label class="block text-sm font-medium text-gray-500 mb-1">Full Name</label>
                     <input type="text" name="name" value="{{ auth()->user()->name }}"
                         class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Your full name">
                 </div>

                 <!-- Email -->
                 <div>
                     <label class="block text-sm font-medium text-gray-500 mb-1">Email Address</label>
                     <input type="email" name="email" value="{{ auth()->user()->email }}"
                         class="w-full px-3 py-2 border border-gray-300 rounded-md"
                         placeholder="your.email@example.com">
                 </div>

                 <!-- Phone -->
                 <div>
                     <label class="block text-sm font-medium text-gray-500 mb-1">Phone Number</label>
                     <input type="tel" name="phone" value="{{ auth()->user()->phone }}"
                         class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="(123) 456-7890">
                 </div>

                 <!-- Company Name -->
                 <div>
                     <label class="block text-sm font-medium text-gray-500 mb-1">Company Name</label>
                     <input type="text" name="company_name" value="{{ auth()->user()->company_name }}"
                         class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Your company name">
                 </div>
             </div>

             <div class="mt-6">
                 <button id="savePersonalInfo" type="submit"
                     class="px-4 py-2 bg-[#4F46E5] text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                     Update Personal Information
                 </button>
             </div>
         </form>
 

       
        </div>


     </div>
     <script>
         // Wait for the DOM to be fully loaded
         $(document).ready(function() {
             // Personal Information Form AJAX submission
             $('#personalInfoForm').submit(function(e) {
                 e.preventDefault();

                 $.ajax({
                     url: '{{ route('customers.personal.update') }}',
                     type: 'POST',
                     data: $(this).serialize(),
                     beforeSend: function() {
                         // Show loading indicator
                         $('#savePersonalInfo').html('Updating...').prop('disabled', true);
                     },
                     success: function(response) {
                         toastr.success(response.message ||
                             'Personal information updated successfully.');

                     },
                     error: function(xhr) {
                         toastr.error(xhr.responseJSON.message ||
                             'Personal information updated successfully.');

                     },
                     complete: function() {
                         // Reset button state
                         $('#savePersonalInfo').html('Update Personal Information').prop(
                             'disabled', false);
                     }
                 });
             });

             // Password Update Form AJAX submission
             $('#passwordUpdateForm').submit(function(e) {
                 e.preventDefault();

                 $.ajax({
                     url: '{{ route('customers.personal.password') }}',
                     type: 'POST',
                     data: $(this).serialize(),
                     beforeSend: function() {
                         // Show loading indicator
                         $('#updatePassword').html('Updating...').prop('disabled', true);
                     },
                     success: function(response) {
                         toastr.success(response.message ||
                             'Personal information updated successfully.');

                     },
                     error: function(xhr) {
                         toastr.error(xhr.responseJSON.message ||
                             'Personal information updated successfully.');

                     },
                     complete: function() {
                         // Reset button state
                         $('#updatePassword').html('Update Password').prop('disabled', false);
                     }
                 });
             });


         });
     </script>
