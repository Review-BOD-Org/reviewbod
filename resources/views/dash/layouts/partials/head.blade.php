 <div class="bg-white px-6 py-3 border-b border-black-200 flex items-center justify-between flex-shrink-0">
                <h1 class="text-lg font-medium text-gray-900"></h1>
                <div class="flex items-center">
                    <div class="relative">
                        <button onclick="toggleDropdown('userMenu')"
                            class="flex items-center border rounded-full p-1 text-gray-700 text-sm font-medium hover:text-gray-900">
                            <div
                                class="h-8 w-8 rounded-full bg-yellow-500 flex items-center justify-center text-white text-xs font-bold">
                                {{ Auth::user()->name[0] }}
                            </div>
                            <span class="ml-2">{{ Auth::user()->name }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 text-gray-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="userMenu"
                            class="dropdown-content absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 border border-gray-200">
                            <a href="{{ route('user.settings') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Account Settings</a>
                        
                            <div class="border-t border-gray-100 my-1"></div>
                            <a href="{{ route('user.logout') }}"
                                class="block px-4 py-2 text-sm text-pink-600 hover:bg-gray-100">Logout</a>
                        </div>
                    </div>
                </div>
            </div>