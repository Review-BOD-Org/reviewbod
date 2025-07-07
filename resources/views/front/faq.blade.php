@include("front.layout.header")


    <main class="pt-24 mb-[200px]" style="margin-top:50px">
     
     <div
            class="w-[1220px] h-[425px] rounded-[25px] bg-[#bd9555] flex items-center justify-center relative mx-auto mt-[60px]">
            <div class="absolute inset-0">
                <div class="absolute bottom-0 left-0 w-[415.97px] h-[414.17px] rotate-[-174.08deg] opacity-10">
                    <img src="/rb-f1.svg" class="w-full h-full object-cover"
                        style="transform: rotate(170deg);filter: brightness(10.5);">
                </div>
                <div class="absolute bottom-0 right-0 w-[416.01px] h-[413.97px] rotate-[5.79deg] opacity-10">
                    <img src="/rb-f2.svg" class="w-full h-full object-cover" style="filter: brightness(10.5);">
                </div>
            </div>
            <div class="text-center z-10">
                <h1 class="text-5xl font-bold text-white mb-4">FAQ</h1>
                <p class="text-white text-lg mb-6 w-[80%] mx-auto mt-3">Unlock seamless performance management, AI-driven insights, and team
                    automation—all in one platform.</p>
                <button
                    class="bg-white mt-2 text-[#1e3a8a] font-bold text-lg px-8 py-3 rounded-full shadow-[0_4px_4px_rgba(66,133,244,1)]">
                    Try 7-Days Free Trial
                </button>
            </div>
        </div>


        <div class="flex flex-col gap-5">

<div class="flex  mt-[90px] w-full justify-center">


            <div class="w-[68%]">
                        <h3 class="mb-8 font-bold text-3xl text-[#CB964F]">Customization</h3>

                <div class="space-y-5">
                    <!-- FAQ Item 1 - Expanded by default -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <button
                            class="faq-toggle w-full px-5 py-6 text-left flex items-center justify-between bg-blue-800 text-white">
                            <span class="font-bold text-lg pr-4">What is Reviewbod, and how does it work?</span>
                            <svg class="w-5 h-5 flex-shrink-0 faq-icon" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4">
                                </path>
                            </svg>
                        </button>
                        <div class="faq-content px-5 py-6 bg-blue-800 text-white border-t border-blue-700">
                            <p class="text-base leading-relaxed">ReviewBod is an AI-powered performance management
                                platform that automates employee reviews, tracks team performance, and integrates with
                                tools like Linear, Trello, and Notion to provide data-driven insights.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 2 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <button
                            class="faq-toggle w-full px-5 py-6 text-left flex items-center justify-between bg-white text-gray-800 hover:bg-gray-50">
                            <span class="font-bold text-lg pr-4">Can I try Reviewbod for free?</span>
                            <svg class="w-5 h-5 flex-shrink-0 faq-icon" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </button>
                        <div class="faq-content hidden px-5 py-6 bg-blue-800 text-white border-t border-blue-700">
                            <p class="text-base leading-relaxed">Yes, ReviewBod offers a free trial period where you
                                can explore all the features and see how it fits your team's needs before committing to
                                a paid plan.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 3 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <button
                            class="faq-toggle w-full px-5 py-6 text-left flex items-center justify-between bg-white text-gray-800 hover:bg-gray-50">
                            <span class="font-bold text-lg pr-4">What platforms does ReviewBod support?</span>
                            <svg class="w-5 h-5 flex-shrink-0 faq-icon" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </button>
                        <div class="faq-content hidden px-5 py-6 bg-blue-800 text-white border-t border-blue-700">
                            <p class="text-base leading-relaxed">ReviewBod integrates with popular productivity tools
                                including Linear, Trello, Notion, Slack, and many other platforms to streamline your
                                workflow.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 4 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <button
                            class="faq-toggle w-full px-5 py-6 text-left flex items-center justify-between bg-white text-gray-800 hover:bg-gray-50">
                            <span class="font-bold text-lg pr-4">Can I customize ReviewBod to match my brand?</span>
                            <svg class="w-5 h-5 flex-shrink-0 faq-icon" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </button>
                        <div class="faq-content hidden px-5 py-6 bg-blue-800 text-white border-t border-blue-700">
                            <p class="text-base leading-relaxed">Absolutely! ReviewBod offers extensive customization
                                options including custom branding, logos, colors, and templates to match your company's
                                identity.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 5 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <button
                            class="faq-toggle w-full px-5 py-6 text-left flex items-center justify-between bg-white text-gray-800 hover:bg-gray-50">
                            <span class="font-bold text-lg pr-4">How secure is my data with Reviewbod</span>
                            <svg class="w-5 h-5 flex-shrink-0 faq-icon" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </button>
                        <div class="faq-content hidden px-5 py-6 bg-blue-800 text-white border-t border-blue-700">
                            <p class="text-base leading-relaxed">Your data security is our top priority. ReviewBod uses
                                enterprise-grade encryption, secure cloud storage, and follows industry-standard
                                security practices to protect your information.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex  mt-[50px] w-full justify-center">


            <div class="w-[68%]">
                        <h3 class="mb-8 font-bold text-3xl text-[#CB964F]">Ai Analysis</h3>

                <div class="space-y-5">
                   
                    <!-- FAQ Item 2 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <button
                            class="faq-toggle w-full px-5 py-6 text-left flex items-center justify-between bg-white text-gray-800 hover:bg-gray-50">
                            <span class="font-bold text-lg pr-4">Can I try Reviewbod for free?</span>
                            <svg class="w-5 h-5 flex-shrink-0 faq-icon" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </button>
                        <div class="faq-content hidden px-5 py-6 bg-blue-800 text-white border-t border-blue-700">
                            <p class="text-base leading-relaxed">Yes, ReviewBod offers a free trial period where you
                                can explore all the features and see how it fits your team's needs before committing to
                                a paid plan.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 3 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <button
                            class="faq-toggle w-full px-5 py-6 text-left flex items-center justify-between bg-white text-gray-800 hover:bg-gray-50">
                            <span class="font-bold text-lg pr-4">What platforms does ReviewBod support?</span>
                            <svg class="w-5 h-5 flex-shrink-0 faq-icon" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </button>
                        <div class="faq-content hidden px-5 py-6 bg-blue-800 text-white border-t border-blue-700">
                            <p class="text-base leading-relaxed">ReviewBod integrates with popular productivity tools
                                including Linear, Trello, Notion, Slack, and many other platforms to streamline your
                                workflow.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 4 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <button
                            class="faq-toggle w-full px-5 py-6 text-left flex items-center justify-between bg-white text-gray-800 hover:bg-gray-50">
                            <span class="font-bold text-lg pr-4">Can I customize ReviewBod to match my brand?</span>
                            <svg class="w-5 h-5 flex-shrink-0 faq-icon" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </button>
                        <div class="faq-content hidden px-5 py-6 bg-blue-800 text-white border-t border-blue-700">
                            <p class="text-base leading-relaxed">Absolutely! ReviewBod offers extensive customization
                                options including custom branding, logos, colors, and templates to match your company's
                                identity.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 5 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <button
                            class="faq-toggle w-full px-5 py-6 text-left flex items-center justify-between bg-white text-gray-800 hover:bg-gray-50">
                            <span class="font-bold text-lg pr-4">How secure is my data with Reviewbod</span>
                            <svg class="w-5 h-5 flex-shrink-0 faq-icon" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </button>
                        <div class="faq-content hidden px-5 py-6 bg-blue-800 text-white border-t border-blue-700">
                            <p class="text-base leading-relaxed">Your data security is our top priority. ReviewBod uses
                                enterprise-grade encryption, secure cloud storage, and follows industry-standard
                                security practices to protect your information.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>

      

 
              
        </div>
        
    </main>
@include("front.layout.footer")