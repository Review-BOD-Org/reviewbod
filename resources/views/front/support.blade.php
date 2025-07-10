@include("front.layout.header")

<main class="pt-24 mb-[200px]" style="margin-top:50px">
 
    <!-- Hero Section -->
    <div class="w-[1220px] h-[425px] rounded-[25px] bg-[#bd9555] flex items-center justify-center relative mx-auto mt-[60px]" data-aos="zoom-in" data-aos-duration="1000" data-aos-delay="200">
        <div class="absolute inset-0">
            <div class="absolute bottom-0 left-0 w-[415.97px] h-[414.17px] rotate-[-174.08deg] opacity-10" data-aos="fade-right" data-aos-duration="1200" data-aos-delay="600">
                <img src="/rb-f1.svg" class="w-full h-full object-cover"
                    style="transform: rotate(360deg);filter: brightness(10.5);">
            </div>
            <div class="absolute bottom-0 right-0 w-[416.01px] h-[413.97px] rotate-[5.79deg] opacity-10" data-aos="fade-left" data-aos-duration="1200" data-aos-delay="800">
                <img src="/rb-f2.svg" class="w-full h-full object-cover" style="filter: brightness(10.5);">
            </div>
        </div>
        <div class="text-center z-10">
            <h1 class="text-5xl font-bold text-white mb-4" data-aos="fade-up" data-aos-duration="800" data-aos-delay="400">HELP & SUPPORT</h1>
            <p class="text-white text-lg mb-6 w-[80%] mx-auto mt-3" data-aos="fade-up" data-aos-duration="800" data-aos-delay="600">We're here to help you get the most out of ReviewBod. Find answers, guides, and get in touch with our team.</p>
        </div>
    </div>

    <!-- Quick Help Options -->
    <div class="mt-[100px] px-[80px]" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
        <div class="text-center mb-[60px]">
            <h2 class="text-4xl font-bold mb-4">How Can We <b class="text-[#1E3A8A]">Help You?</b></h2>
            <p class="text-[#545C66] text-lg">Choose the option that works best for you</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
            <!-- FAQ -->
            <div class="text-center p-8 rounded-2xl border-2 border-gray-200 hover:border-[#1E3A8A] transition-colors cursor-pointer" data-aos="fade-up" data-aos-duration="600" data-aos-delay="300">
                <div class="w-20 h-20 bg-[#1E3A8A] rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-question-circle text-white text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold mb-4">FAQ</h3>
                <p class="text-[#545C66] mb-6">Find quick answers to common questions about ReviewBod</p>
                <a href="/faq" class="bg-[#1E3A8A] text-white px-6 py-3 rounded-full font-semibold hover:bg-blue-800 transition-colors">
                    View FAQ
                </a>
            </div>

            <!-- Email Support -->
            <div class="text-center p-8 rounded-2xl border-2 border-gray-200 hover:border-[#10b981] transition-colors cursor-pointer" data-aos="fade-up" data-aos-duration="600" data-aos-delay="400">
                <div class="w-20 h-20 bg-[#10b981] rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-envelope text-white text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold mb-4">Email Support</h3>
                <p class="text-[#545C66] mb-6">Get detailed help via email. We respond within 24 hours</p>
                <a href="mailto:support@reviewbod.com" class="bg-[#10b981] text-white px-6 py-3 rounded-full font-semibold hover:bg-green-600 transition-colors">
                    Email Us
                </a>
            </div>

            <!-- Live Chat -->
            <div class="text-center p-8 rounded-2xl border-2 border-gray-200 hover:border-[#f59e0b] transition-colors cursor-pointer" data-aos="fade-up" data-aos-duration="600" data-aos-delay="500">
                <div class="w-20 h-20 bg-[#f59e0b] rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-comments text-white text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold mb-4">Live Chat</h3>
                <p class="text-[#545C66] mb-6">Chat with our team instantly during business hours</p>
                <button class="bg-[#f59e0b] text-white px-6 py-3 rounded-full font-semibold hover:bg-yellow-600 transition-colors">
                    Start Chat
                </button>
            </div>
        </div>
    </div>

    <!-- Common Topics -->
    <div class="mt-[100px] px-[80px]" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
        <div class="text-center mb-[60px]">
            <h2 class="text-4xl font-bold mb-4">Popular <b class="text-[#1E3A8A]">Help Topics</b></h2>
        </div>

        <div class="max-w-4xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-6 rounded-lg border border-gray-200 hover:shadow-lg transition-shadow" data-aos="fade-up" data-aos-duration="600" data-aos-delay="300">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-plug text-[#1E3A8A] text-xl mr-3"></i>
                        <h3 class="text-lg font-semibold">Setting Up Integrations</h3>
                    </div>
                    <p class="text-[#545C66] mb-4">Learn how to connect Linear, Jira, and Trello to ReviewBod</p>
                    <a href="#" class="text-[#1E3A8A] font-medium hover:underline">View Guide →</a>
                </div>

                <div class="p-6 rounded-lg border border-gray-200 hover:shadow-lg transition-shadow" data-aos="fade-up" data-aos-duration="600" data-aos-delay="400">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-sliders-h text-[#1E3A8A] text-xl mr-3"></i>
                        <h3 class="text-lg font-semibold">Custom Performance Metrics</h3>
                    </div>
                    <p class="text-[#545C66] mb-4">Set up metrics that match your team's goals and culture</p>
                    <a href="#" class="text-[#1E3A8A] font-medium hover:underline">View Guide →</a>
                </div>

                <div class="p-6 rounded-lg border border-gray-200 hover:shadow-lg transition-shadow" data-aos="fade-up" data-aos-duration="600" data-aos-delay="500">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-robot text-[#1E3A8A] text-xl mr-3"></i>
                        <h3 class="text-lg font-semibold">Understanding AI Reviews</h3>
                    </div>
                    <p class="text-[#545C66] mb-4">How our AI generates insights and recommendations</p>
                    <a href="#" class="text-[#1E3A8A] font-medium hover:underline">View Guide →</a>
                </div>

                <div class="p-6 rounded-lg border border-gray-200 hover:shadow-lg transition-shadow" data-aos="fade-up" data-aos-duration="600" data-aos-delay="600">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-shield-alt text-[#1E3A8A] text-xl mr-3"></i>
                        <h3 class="text-lg font-semibold">Data Security & Privacy</h3>
                    </div>
                    <p class="text-[#545C66] mb-4">Learn about our security practices and data protection</p>
                    <a href="#" class="text-[#1E3A8A] font-medium hover:underline">View Guide →</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Info -->
    <div class="mt-[100px] px-[80px]" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
        <div class="max-w-4xl mx-auto bg-gray-50 rounded-2xl p-8">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold mb-4">Still Need Help?</h2>
                <p class="text-[#545C66]">Our support team is here to help you succeed with ReviewBod</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div>
                    <i class="fas fa-envelope text-[#1E3A8A] text-2xl mb-3"></i>
                    <h3 class="font-semibold mb-2">Email</h3>
                    <p class="text-[#545C66] text-sm">support@reviewbod.com</p>
                </div>
                <div>
                    <i class="fas fa-clock text-[#1E3A8A] text-2xl mb-3"></i>
                    <h3 class="font-semibold mb-2">Response Time</h3>
                    <p class="text-[#545C66] text-sm">Within 24 hours</p>
                </div>
                <div>
                    <i class="fas fa-globe text-[#1E3A8A] text-2xl mb-3"></i>
                    <h3 class="font-semibold mb-2">Coverage</h3>
                    <p class="text-[#545C66] text-sm">Monday - Friday, 9 AM - 6 PM PST</p>
                </div>
            </div>
        </div>
    </div>
    
</main>
@include("front.layout.footer")