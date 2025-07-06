{{-- <!-- partials/topnav.blade.php - Top navigation bar component -->
<div class="bg-white px-6 py-3 border-b border-gray-200 flex items-center justify-between">
    <h1 class="text-lg font-medium text-gray-900">@yield('page-title', 'Dashboard')</h1>
    
    <!-- User profile -->
    <div class="flex items-center">
        <div class="relative">
            <button onclick="toggleDropdown('userMenu')" class="flex items-center text-gray-700 text-sm font-medium hover:text-gray-900">
                <div class="h-8 w-8 rounded-full bg-yellow-500 flex items-center justify-center text-white text-xs font-bold">
                    {{ Auth('managers')->user()->name[0] }}
                </div>
                <span class="ml-2">{{ Auth('managers')->user()->name }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            
            <!-- User dropdown menu -->
            <div id="userMenu" class="dropdown-content absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 border border-gray-200">
                <a href="{{route('user.settings')}}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Account Settings</a>
                <a href="" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Theme Preferences</a>
                <div class="border-t border-gray-100 my-1"></div>
                <a href="{{route('user.logout')}}"  class="block px-4 py-2 text-sm text-pink-600 hover:bg-gray-100">Logout</a>
            </div>
        </div>
    </div>
</div>
@if(Auth('managers')->user()->service)
<!-- partials/chat-button.blade.php - Floating chat button component -->
<div class="fixed bottom-6 right-6 chat-button">
    <button class="bg-blue-800 rounded-full p-4 text-white  hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
        </svg>
    </button>
</div>
@endif --}}