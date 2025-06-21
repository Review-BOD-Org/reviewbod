<!-- partials/sidebar.blade.php - Sidebar navigation component -->
<div class=" border-r border-[#F1F2F7] flex flex-col h-screen">

    <!-- App title -->
    {{-- <div class="p-3">
        <div class="px-3 py-3 justify-between flex items-center cursor-pointer relative bg-white rounded-md"
            onclick="toggleDropdown('appMenu')">
            <div class="bg-gradient-to-r   w-6 h-6 rounded-md flex items-center justify-center mr-2 shadow-sm">
                @if (Auth::user()->service)
                    <img src="/images/{{ Auth::user()->service }}.webp" />
                @endif
            </div>

            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 text-gray-500" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>

            <!-- App dropdown menu -->
            <div id="appMenu"
                class="dropdown-content absolute top-full left-0 mt-1 w-48 bg-white rounded-md shadow-lg py-1 z-10 border border-gray-100">
                @foreach (DB::table('linked')->where(['userid' => Auth::id()])->get() as $d)
                    @if ($d->type != 'slack')
                        <div class="flex items-center px-3 py-2 hover:bg-blue-50 transition-colors">
                            <img src="/images/{{ $d->type }}.webp" width="20" />
                            <a href="/dashboard?type={{ $d->type }}"
                                class="ml-3 text-sm text-gray-700 hover:text-blue-600 transition-colors block w-full">{{ $d->type }}</a>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div> --}}

    <!-- Menu sections -->
    <div class="mt-6 px-3 flex-grow">
        @php
            $linked = DB::table('linked')
                ->where('userid', Auth::user()->id)
                ->first();
        @endphp
        <!-- Menu items -->
        <nav class="space-y-[50px]" style="@if (!$linked) pointer-events:none; @endif">
            <a href="/dashboard"
                class="flex items-center px-3 py-2 text-sm font-medium text-gray-900 rounded-md {{ request()->routeIs('dashboard') ? 'bg-gray-100' : 'hover:bg-gray-50 hover:text-gray-900' }}">

                <svg width="42" height="38" viewBox="0 0 42 38" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M41.875 22.1376C41.8742 18.7234 40.7764 15.4011 38.7456 12.6673C36.7148 9.93346 33.8601 7.93493 30.6081 6.97039C27.3561 6.00585 23.8816 6.1271 20.7037 7.31601C17.5258 8.50493 14.8152 10.6977 12.9772 13.5665C11.1392 16.4353 10.2723 19.8262 10.5062 23.2322C10.7401 26.6383 12.0621 29.8767 14.2748 32.4634C16.4875 35.0501 19.4719 36.8462 22.7821 37.5832C26.0922 38.3202 29.5503 37.9586 32.6395 36.5524C35.2734 37.1639 38.1104 37.6911 39.4148 37.9272C39.7482 37.9872 40.091 37.9646 40.4137 37.8612C40.7365 37.7578 41.0293 37.5768 41.267 37.3339C41.5047 37.091 41.6801 36.7935 41.778 36.467C41.8759 36.1405 41.8934 35.7949 41.8289 35.4601C41.4056 33.2399 40.9302 31.0301 40.4031 28.8326C41.374 26.7362 41.8764 24.4508 41.875 22.1376Z"
                        fill="url(#paint0_radial_33_2999)" />
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M3.08198e-06 15.8114C0.000757488 12.3972 1.09857 9.07492 3.12938 6.3411C5.16018 3.60729 8.0149 1.60876 11.2669 0.644221C14.5189 -0.320319 17.9934 -0.199072 21.1713 0.989842C24.3492 2.17876 27.0598 4.37149 28.8978 7.24031C30.7359 10.1091 31.6027 13.5 31.3688 16.9061C31.1349 20.3121 29.8129 23.5505 27.6002 26.1372C25.3875 28.7239 22.4031 30.52 19.0929 31.257C15.7828 31.994 12.3247 31.6324 9.23553 30.2262C6.60159 30.8377 3.76456 31.3649 2.46016 31.6011C2.1268 31.6611 1.78396 31.6384 1.46125 31.535C1.13855 31.4316 0.845694 31.2507 0.608001 31.0078C0.370309 30.7649 0.194931 30.4673 0.0970234 30.1408C-0.000883736 29.8143 -0.0183735 29.4688 0.0460657 29.1339C0.469403 26.9138 0.944817 24.704 1.47191 22.5064C0.501008 20.41 -0.00143586 18.1246 3.08198e-06 15.8114Z"
                        fill="url(#paint1_linear_33_2999)" />
                    <defs>
                        <radialGradient id="paint0_radial_33_2999" cx="0" cy="0" r="1"
                            gradientUnits="userSpaceOnUse"
                            gradientTransform="translate(18.9003 14.7678) rotate(49.445) scale(22.2288 22.2231)">
                            <stop offset="0.63" stop-color="#3D35B1" />
                            <stop offset="0.85" stop-color="#6553C9" />
                            <stop offset="1" stop-color="#7660D3" />
                        </radialGradient>
                        <linearGradient id="paint1_linear_33_2999" x1="3.48071e-06" y1="-0.00351821" x2="31.6354"
                            y2="31.4019" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#0FAFFF" />
                            <stop offset="1" stop-color="#2764E7" />
                        </linearGradient>
                    </defs>
                </svg>



            </a>

            <div class="relative">
                <div onclick="toggleDropdown('membersMenu')"
                    class="flex items-center px-3 py-2 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900 cursor-pointer">

                    <svg width="40" height="41" viewBox="0 0 40 41" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M15.2667 18.5699C15.1 18.5528 14.9 18.5528 14.7167 18.5699C10.75 18.4332 7.60001 15.102 7.60001 11.002C7.60001 6.81658 10.9 3.41699 15 3.41699C19.0833 3.41699 22.4 6.81658 22.4 11.002C22.3833 15.102 19.2333 18.4332 15.2667 18.5699Z"
                            stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path opacity="0.4"
                            d="M27.35 6.83301C30.5833 6.83301 33.1833 9.51509 33.1833 12.8122C33.1833 16.0409 30.6833 18.6718 27.5667 18.7913C27.4333 18.7743 27.2833 18.7743 27.1333 18.7913"
                            stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path
                            d="M6.93334 24.873C2.90001 27.6405 2.90001 32.1505 6.93334 34.9009C11.5167 38.0443 19.0333 38.0443 23.6167 34.9009C27.65 32.1334 27.65 27.6234 23.6167 24.873C19.05 21.7468 11.5333 21.7468 6.93334 24.873Z"
                            stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path opacity="0.4"
                            d="M30.5667 34.167C31.7667 33.9107 32.9 33.4153 33.8333 32.6807C36.4333 30.682 36.4333 27.3849 33.8333 25.3862C32.9167 24.6687 31.8 24.1903 30.6167 23.917"
                            stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>

                </div>

            </div>

            <div class="relative">
                <div onclick="location.href='{{ route('user.settings') }}'"
                    class="flex items-center px-3 py-2 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900 cursor-pointer">


                    <svg width="47" height="50" viewBox="0 0 47 50" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M3.91666 26.8336V23.167C3.91666 21.0003 5.58125 19.2086 7.6375 19.2086C11.1821 19.2086 12.6312 16.542 10.8492 13.2711C9.83083 11.3961 10.4379 8.95865 12.22 7.87531L15.6079 5.81282C17.155 4.83365 19.1525 5.41698 20.0729 7.06282L20.2883 7.45865C22.0508 10.7295 24.9492 10.7295 26.7312 7.45865L26.9467 7.06282C27.8671 5.41698 29.8646 4.83365 31.4117 5.81282L34.7996 7.87531C36.5817 8.95865 37.1887 11.3961 36.1704 13.2711C34.3883 16.542 35.8375 19.2086 39.3821 19.2086C41.4187 19.2086 43.1029 20.9795 43.1029 23.167V26.8336C43.1029 29.0003 41.4383 30.792 39.3821 30.792C35.8375 30.792 34.3883 33.4586 36.1704 36.7295C37.1887 38.6253 36.5817 41.042 34.7996 42.1253L31.4117 44.1878C29.8646 45.167 27.8671 44.5836 26.9467 42.9378L26.7312 42.542C24.9687 39.2711 22.0704 39.2711 20.2883 42.542L20.0729 42.9378C19.1525 44.5836 17.155 45.167 15.6079 44.1878L12.22 42.1253C10.4379 41.042 9.83083 38.6045 10.8492 36.7295C12.6312 33.4586 11.1821 30.792 7.6375 30.792C5.58125 30.792 3.91666 29.0003 3.91666 26.8336Z"
                            fill="#332089" />
                        <path
                            d="M23.5 31.7712C27.0151 31.7712 29.8646 28.7398 29.8646 25.0003C29.8646 21.2609 27.0151 18.2295 23.5 18.2295C19.9849 18.2295 17.1354 21.2609 17.1354 25.0003C17.1354 28.7398 19.9849 31.7712 23.5 31.7712Z"
                            fill="#C6C0FE" />
                    </svg>



                </div>

            </div>

            <a href="{{ route('user.reports') }}"
                class="flex items-center px-3 py-2 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900">


                <svg width="50" height="54" viewBox="0 0 50 54" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.4"
                        d="M33.7292 4.5H16.2917C8.70834 4.5 4.18748 9.38249 4.18748 17.5725V36.405C4.18748 44.595 8.70834 49.4775 16.2917 49.4775H33.7292C41.3125 49.4775 45.8333 44.595 45.8333 36.405V17.5725C45.8333 9.38249 41.3125 4.5 33.7292 4.5Z"
                        fill="#FEB8DD" />
                    <path
                        d="M19.1042 31.7028C19.5 31.7028 19.8958 31.5453 20.2083 31.2078L29.3333 21.3528V26.8203C29.3333 27.7428 30.0417 28.5078 30.8958 28.5078C31.75 28.5078 32.4583 27.7428 32.4583 26.8203V17.2803C32.4583 17.0553 32.4167 16.8528 32.3334 16.6278C32.1667 16.2228 31.875 15.8853 31.4792 15.7053C31.2917 15.6153 31.0834 15.5703 30.875 15.5703H22.0417C21.1875 15.5703 20.4792 16.3353 20.4792 17.2578C20.4792 18.1803 21.1875 18.9453 22.0417 18.9453H27.1042L17.9792 28.8003C17.375 29.4528 17.375 30.5328 17.9792 31.1853C18.3125 31.5453 18.7083 31.7028 19.1042 31.7028Z"
                        fill="#BF0637" />
                    <path
                        d="M38.9792 36.6304C38.7083 35.7529 37.8333 35.2804 37 35.5729C29.25 38.3629 20.7292 38.3629 12.9792 35.5729C12.1667 35.2804 11.2708 35.7529 11 36.6304C10.7292 37.5079 11.1667 38.4754 11.9792 38.7679C16.1667 40.2754 20.5625 41.0404 24.9792 41.0404C29.3958 41.0404 33.7917 40.2754 37.9792 38.7679C38.8125 38.4529 39.25 37.5079 38.9792 36.6304Z"
                        fill="#BF0637" />
                </svg>


            </a>


        </nav>
    </div>

    <!-- Profile section -->

</div>
