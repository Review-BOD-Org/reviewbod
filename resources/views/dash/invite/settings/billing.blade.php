@include('dash.layouts.partials.head')
<div class="p-5">
    <div class="border rounded-md">
        <div class="p-4 flex border-b justify-between">
            <div class="flex flex-col">
                <h2 class="text-[25px] font-bold">Billing & Subscription</h2>
                <span class="font-light text-lg">Manage your subscriptions</span>
            </div>
        </div>
        <div id="personalInfoForm" class="p-5">
            <!-- Current Plan Summary and Payment Method Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Current Plan Summary -->
                <div class="space-y-4 border flex flex-col gap-4">
                    <div class="flex justify-between items-center  bg-gray-100 p-3">
                        <h3 class="text-xl font-semibold text-gray-900">Current Plan Summary</h3>
                        <button class="bg-[#5B89FF] text-white px-4 py-2 rounded-md font-medium hover:bg-[#4A7AFF] transition-colors">
                            Upgrade
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-4 p-4 justify-center">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">PLAN NAME</p>
                            <p class="text-lg font-semibold text-gray-900">Basic Plan</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">BILLING CYCLE</p>
                            <p class="text-lg font-semibold text-gray-900">Monthly</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">PLAN COST</p>
                            <p class="text-lg font-semibold text-gray-900">$5698</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="space-y-4 border">
                    <div class="flex justify-between items-center  bg-gray-100 p-3">
                        <h3 class="text-xl font-semibold text-gray-900">Payment Method</h3>
                     
                    </div>
                    
                   <div class="p-4">
 <div class="flex items-center space-x-3 p-3 border rounded-md">
                        <!-- Mastercard Icon -->
                        <div class="flex items-center self-start">
                            <div class="w-8 h-8 bg-red-500 rounded-full mr-[-4px] z-10"></div>
                            <div class="w-8 h-8 bg-orange-400 rounded-full opacity-80"></div>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">Master Card</p>
                            <p class="text-gray-600">•••• •••• •••• 4002</p>
                            <p class="text-sm text-gray-500">Expiry on 20/2024</p>
                            <p class="text-sm text-gray-500 flex items-center mt-1">
                             

                                <svg class="w-4 h-4 mr-1" width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M9 10.25H4C2.5 10.25 1.5 9.5 1.5 7.75V4.25C1.5 2.5 2.5 1.75 4 1.75H9C10.5 1.75 11.5 2.5 11.5 4.25V7.75C11.5 9.5 10.5 10.25 9 10.25Z" stroke="#696969" stroke-width="0.8" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M9 4.5L7.435 5.75C6.92 6.16 6.075 6.16 5.56 5.75L4 4.5" stroke="#696969" stroke-width="0.8" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
</svg>

                                billing@acme.corp
                            </p>
                        </div>
                           <button class="border p-2 rounded-md self-start text-gray-600 hover:text-gray-800 font-medium">
                            Change
                        </button>
                    </div>
                   </div>
                </div>
            </div>

            <!-- Invoice Section -->
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-1">Invoice</h3>
                        <p class="text-gray-600">Effortlessly handle your billing and invoices right here.</p>
                    </div>
                    <button class="bg-[#5B89FF] text-white px-4 py-2 rounded-md font-medium hover:bg-[#4A7AFF] transition-colors">
                        Download
                    </button>
                </div>

                <!-- Invoice Table -->
                <div class="mt-6">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-3 px-0 text-sm font-medium text-gray-500 uppercase tracking-wide">
                                        Invoice ID
                                       
                                        <svg class="w-3 h-3 mb-1 inline ml-1" width="7" height="6" viewBox="0 0 7 6" fill="none" xmlns="http://www.w3.org/2000/svg">
<g opacity="0.7">
<path d="M2.9345 5.31539C2.9055 5.28714 2.7815 5.18047 2.6795 5.0811C2.038 4.49854 0.988 2.97881 0.6675 2.18339C0.616 2.06259 0.507 1.75718 0.5 1.59401C0.5 1.43765 0.536 1.2886 0.609 1.14637C0.711 0.96907 0.8715 0.826839 1.061 0.748904C1.1925 0.698734 1.586 0.620799 1.593 0.620799C2.0235 0.542864 2.723 0.5 3.496 0.5C4.2325 0.5 4.9035 0.542864 5.3405 0.606673C5.3475 0.61398 5.8365 0.691914 6.004 0.777155C6.31 0.933512 6.5 1.23892 6.5 1.56576V1.59401C6.4925 1.80687 6.3025 2.25451 6.2955 2.25451C5.9745 3.00706 4.976 4.49172 4.3125 5.08841C4.3125 5.08841 4.142 5.25645 4.0355 5.32952C3.8825 5.4435 3.693 5.5 3.5035 5.5C3.292 5.5 3.095 5.43619 2.9345 5.31539Z" fill="#030229"/>
</g>
</svg>

                                    </th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500 uppercase tracking-wide">
                                        Billing Date
                                            <svg class="w-3 h-3 mb-1 inline ml-1" width="7" height="6" viewBox="0 0 7 6" fill="none" xmlns="http://www.w3.org/2000/svg">
<g opacity="0.7">
<path d="M2.9345 5.31539C2.9055 5.28714 2.7815 5.18047 2.6795 5.0811C2.038 4.49854 0.988 2.97881 0.6675 2.18339C0.616 2.06259 0.507 1.75718 0.5 1.59401C0.5 1.43765 0.536 1.2886 0.609 1.14637C0.711 0.96907 0.8715 0.826839 1.061 0.748904C1.1925 0.698734 1.586 0.620799 1.593 0.620799C2.0235 0.542864 2.723 0.5 3.496 0.5C4.2325 0.5 4.9035 0.542864 5.3405 0.606673C5.3475 0.61398 5.8365 0.691914 6.004 0.777155C6.31 0.933512 6.5 1.23892 6.5 1.56576V1.59401C6.4925 1.80687 6.3025 2.25451 6.2955 2.25451C5.9745 3.00706 4.976 4.49172 4.3125 5.08841C4.3125 5.08841 4.142 5.25645 4.0355 5.32952C3.8825 5.4435 3.693 5.5 3.5035 5.5C3.292 5.5 3.095 5.43619 2.9345 5.31539Z" fill="#030229"/>
</g>
</svg>
                                    </th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500 uppercase tracking-wide">
                                        Plan
                                          <svg class="w-3 h-3 mb-1 inline ml-1" width="7" height="6" viewBox="0 0 7 6" fill="none" xmlns="http://www.w3.org/2000/svg">
<g opacity="0.7">
<path d="M2.9345 5.31539C2.9055 5.28714 2.7815 5.18047 2.6795 5.0811C2.038 4.49854 0.988 2.97881 0.6675 2.18339C0.616 2.06259 0.507 1.75718 0.5 1.59401C0.5 1.43765 0.536 1.2886 0.609 1.14637C0.711 0.96907 0.8715 0.826839 1.061 0.748904C1.1925 0.698734 1.586 0.620799 1.593 0.620799C2.0235 0.542864 2.723 0.5 3.496 0.5C4.2325 0.5 4.9035 0.542864 5.3405 0.606673C5.3475 0.61398 5.8365 0.691914 6.004 0.777155C6.31 0.933512 6.5 1.23892 6.5 1.56576V1.59401C6.4925 1.80687 6.3025 2.25451 6.2955 2.25451C5.9745 3.00706 4.976 4.49172 4.3125 5.08841C4.3125 5.08841 4.142 5.25645 4.0355 5.32952C3.8825 5.4435 3.693 5.5 3.5035 5.5C3.292 5.5 3.095 5.43619 2.9345 5.31539Z" fill="#030229"/>
</g>
</svg>
                                    </th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500 uppercase tracking-wide">
                                        Amount
                                           <svg class="w-3 h-3 mb-1 inline ml-1" width="7" height="6" viewBox="0 0 7 6" fill="none" xmlns="http://www.w3.org/2000/svg">
<g opacity="0.7">
<path d="M2.9345 5.31539C2.9055 5.28714 2.7815 5.18047 2.6795 5.0811C2.038 4.49854 0.988 2.97881 0.6675 2.18339C0.616 2.06259 0.507 1.75718 0.5 1.59401C0.5 1.43765 0.536 1.2886 0.609 1.14637C0.711 0.96907 0.8715 0.826839 1.061 0.748904C1.1925 0.698734 1.586 0.620799 1.593 0.620799C2.0235 0.542864 2.723 0.5 3.496 0.5C4.2325 0.5 4.9035 0.542864 5.3405 0.606673C5.3475 0.61398 5.8365 0.691914 6.004 0.777155C6.31 0.933512 6.5 1.23892 6.5 1.56576V1.59401C6.4925 1.80687 6.3025 2.25451 6.2955 2.25451C5.9745 3.00706 4.976 4.49172 4.3125 5.08841C4.3125 5.08841 4.142 5.25645 4.0355 5.32952C3.8825 5.4435 3.693 5.5 3.5035 5.5C3.292 5.5 3.095 5.43619 2.9345 5.31539Z" fill="#030229"/>
</g>
</svg>
                                    </th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500 uppercase tracking-wide">
                                        Status
                                         <svg class="w-3 h-3 mb-1 inline ml-1" width="7" height="6" viewBox="0 0 7 6" fill="none" xmlns="http://www.w3.org/2000/svg">
<g opacity="0.7">
<path d="M2.9345 5.31539C2.9055 5.28714 2.7815 5.18047 2.6795 5.0811C2.038 4.49854 0.988 2.97881 0.6675 2.18339C0.616 2.06259 0.507 1.75718 0.5 1.59401C0.5 1.43765 0.536 1.2886 0.609 1.14637C0.711 0.96907 0.8715 0.826839 1.061 0.748904C1.1925 0.698734 1.586 0.620799 1.593 0.620799C2.0235 0.542864 2.723 0.5 3.496 0.5C4.2325 0.5 4.9035 0.542864 5.3405 0.606673C5.3475 0.61398 5.8365 0.691914 6.004 0.777155C6.31 0.933512 6.5 1.23892 6.5 1.56576V1.59401C6.4925 1.80687 6.3025 2.25451 6.2955 2.25451C5.9745 3.00706 4.976 4.49172 4.3125 5.08841C4.3125 5.08841 4.142 5.25645 4.0355 5.32952C3.8825 5.4435 3.693 5.5 3.5035 5.5C3.292 5.5 3.095 5.43619 2.9345 5.31539Z" fill="#030229"/>
</g>
</svg>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr class="hover:bg-gray-50">
                                    <td class="py-4 px-0 text-gray-900 font-medium">#23456</td>
                                    <td class="py-4 px-4 text-gray-700">23 Jan 2023</td>
                                    <td class="py-4 px-4 text-gray-700">Basic Plan</td>
                                    <td class="py-4 px-4 text-gray-900 font-semibold">$1200</td>
                                    <td class="py-4 px-4">
                                        <span class="inline-flex items-center p-2 w-[100px] text-center justify-center rounded-full text-xs font-bold bg-green-100 text-[#07A104]">
                                            Paid
                                        </span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-4 px-0 text-gray-900 font-medium">#56489</td>
                                    <td class="py-4 px-4 text-gray-700">23 Feb 2023</td>
                                    <td class="py-4 px-4 text-gray-700">Pro Plan</td>
                                    <td class="py-4 px-4 text-gray-900 font-semibold">$7000</td>
                                    <td class="py-4 px-4">
                                            <span class="inline-flex items-center p-2 w-[100px] text-center justify-center rounded-full text-xs font-bold bg-green-100 text-[#07A104]">
                                            Paid
                                        </span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-4 px-0 text-gray-900 font-medium">#56489</td>
                                    <td class="py-4 px-4 text-gray-700">23 Mar 2023</td>
                                    <td class="py-4 px-4 text-gray-700">Pro Plan</td>
                                    <td class="py-4 px-4 text-gray-900 font-semibold">$7000</td>
                                    <td class="py-4 px-4">
                                            <span class="inline-flex items-center p-2 w-[100px] text-center justify-center rounded-full text-xs font-bold bg-green-100 text-[#07A104]">
                                            Paid
                                        </span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-4 px-0 text-gray-900 font-medium">#98380</td>
                                    <td class="py-4 px-4 text-gray-700">23 Apr 2023</td>
                                    <td class="py-4 px-4 text-gray-700">Growth Plan</td>
                                    <td class="py-4 px-4 text-gray-900 font-semibold">$5698</td>
                                    <td class="py-4 px-4">
                                            <span class="inline-flex items-center p-2 w-[100px] text-center justify-center rounded-full text-xs font-bold bg-green-100 text-[#07A104]">
                                            Paid
                                        </span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-4 px-0 text-gray-900 font-medium">#90394</td>
                                    <td class="py-4 px-4 text-gray-700">23 May 2023</td>
                                    <td class="py-4 px-4 text-gray-700">Basic Plan</td>
                                    <td class="py-4 px-4 text-gray-900 font-semibold">$1200</td>
                                    <td class="py-4 px-4">
                                            <span class="inline-flex items-center p-2 w-[100px] text-center justify-center rounded-full text-xs font-bold bg-green-100 text-[#07A104]">
                                            Paid
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>