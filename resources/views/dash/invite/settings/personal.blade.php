     @include('dash.invite.layouts.partials.head')
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
                     <input readonly type="text" name="name" value="{{ auth('linear_user')->user()->name }}"
                         class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Your full name">
                 </div>

                 <!-- Email -->
                 <div>
                     <label class="block text-sm font-medium text-gray-500 mb-1">Email Address</label>
                     <input readonly type="email" name="email" value="{{ auth('linear_user')->user()->email }}"
                         class="w-full px-3 py-2 border border-gray-300 rounded-md"
                         placeholder="your.email@example.com">
                 </div>

    

                 <!-- Company Name -->
                 <div>
                     <label class="block text-sm font-medium text-gray-500 mb-1">WorkSpace</label>
                     <input type="text" readonly value="{{ auth('linear_user')->user()->space }}"
                         class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Your company name">
                 </div>
             </div>

          
         </form>
 

       
        </div>


     </div>
     