@include("front.layout.header")

<main class="pt-24 mb-[200px]" style="margin-top:50px">
 
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
            <h1 class="text-5xl font-bold text-white mb-4" data-aos="fade-up" data-aos-duration="800" data-aos-delay="400">FAQ</h1>
            <p class="text-white text-lg mb-6 w-[80%] mx-auto mt-3" data-aos="fade-up" data-aos-duration="800" data-aos-delay="600">Find answers to common questions about ReviewBod's AI-powered performance management platform.</p>
            <button class="bg-white mt-2 text-[#1e3a8a] font-bold text-lg px-8 py-3 rounded-full shadow-[0_4px_4px_rgba(66,133,244,1)]" data-aos="zoom-in" data-aos-duration="600" data-aos-delay="800">
                Try 7-Days Free Trial
            </button>
        </div>
    </div>

    <div class="flex flex-col gap-5">

        <!-- Getting Started Section -->
        <div class="flex mt-[90px] w-full justify-center" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
            <div class="w-[68%]">
                <h3 class="mb-8 font-bold text-3xl text-[#CB964F]">Getting Started</h3>

                <div class="space-y-5">
                    <!-- FAQ Item 1 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden" data-aos="fade-up" data-aos-duration="600" data-aos-delay="300">
                        <button class="faq-toggle w-full px-5 py-6 text-left flex items-center justify-between bg-blue-800 text-white">
                            <span class="font-bold text-lg pr-4">What is ReviewBod and how does it work?</span>
                            <i class="fas fa-minus text-white"></i>
                        </button>
                        <div class="faq-content px-5 py-6 bg-blue-800 text-white border-t border-blue-700">
                            <p class="text-base leading-relaxed">ReviewBod is an AI-powered performance management platform that connects to your existing tools like Linear, Jira, and Trello to automatically analyze team performance data. Our AI generates objective, data-driven performance reviews and insights to help managers make better decisions about their teams.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 2 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden" data-aos="fade-up" data-aos-duration="600" data-aos-delay="400">
                        <button class="faq-toggle w-full px-5 py-6 text-left flex items-center justify-between bg-white text-gray-800 hover:bg-gray-50">
                            <span class="font-bold text-lg pr-4">How long does it take to set up ReviewBod?</span>
                            <i class="fas fa-plus text-gray-800"></i>
                        </button>
                        <div class="faq-content hidden px-5 py-6 bg-blue-800 text-white border-t border-blue-700">
                            <p class="text-base leading-relaxed">Setup takes just 5 minutes! Simply connect your Linear or Jira account, select team members to include, and our AI will start analyzing historical data immediately. You can generate your first performance review within 10 minutes of signup.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 3 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden" data-aos="fade-up" data-aos-duration="600" data-aos-delay="500">
                        <button class="faq-toggle w-full px-5 py-6 text-left flex items-center justify-between bg-white text-gray-800 hover:bg-gray-50">
                            <span class="font-bold text-lg pr-4">Can I try ReviewBod for free?</span>
                            <i class="fas fa-plus text-gray-800"></i>
                        </button>
                        <div class="faq-content hidden px-5 py-6 bg-blue-800 text-white border-t border-blue-700">
                            <p class="text-base leading-relaxed">Yes! ReviewBod offers a 7-day free trial with full access to all features. Connect your tools, generate AI reviews for your team, and see the value before committing to any paid plan. No credit card required to start.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 4 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden" data-aos="fade-up" data-aos-duration="600" data-aos-delay="600">
                        <button class="faq-toggle w-full px-5 py-6 text-left flex items-center justify-between bg-white text-gray-800 hover:bg-gray-50">
                            <span class="font-bold text-lg pr-4">Which project management tools does ReviewBod integrate with?</span>
                            <i class="fas fa-plus text-gray-800"></i>
                        </button>
                        <div class="faq-content hidden px-5 py-6 bg-blue-800 text-white border-t border-blue-700">
                            <p class="text-base leading-relaxed">ReviewBod currently integrates with Linear, Jira, Trello, and Notion. We're constantly adding new integrations based on user requests. Our API also allows custom integrations for Enterprise customers with unique tool requirements.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 5 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden" data-aos="fade-up" data-aos-duration="600" data-aos-delay="700">
                        <button class="faq-toggle w-full px-5 py-6 text-left flex items-center justify-between bg-white text-gray-800 hover:bg-gray-50">
                            <span class="font-bold text-lg pr-4">What data does ReviewBod need to generate reviews?</span>
                            <i class="fas fa-plus text-gray-800"></i>
                        </button>
                        <div class="faq-content hidden px-5 py-6 bg-blue-800 text-white border-t border-blue-700">
                            <p class="text-base leading-relaxed">ReviewBod analyzes task completion rates, deadline adherence, code review participation, collaboration patterns, and project contributions. We only access project metadata and never read code, sensitive documents, or personal information.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Analysis Section -->
        <div class="flex mt-[50px] w-full justify-center" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
            <div class="w-[68%]">
                <h3 class="mb-8 font-bold text-3xl text-[#CB964F]">AI Analysis & Features</h3>

                <div class="space-y-5">
                    <!-- FAQ Item 1 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden" data-aos="fade-up" data-aos-duration="600" data-aos-delay="300">
                        <button class="faq-toggle w-full px-5 py-6 text-left flex items-center justify-between bg-white text-gray-800 hover:bg-gray-50">
                            <span class="font-bold text-lg pr-4">How accurate are the AI-generated performance reviews?</span>
                            <i class="fas fa-plus text-gray-800"></i>
                        </button>
                        <div class="faq-content hidden px-5 py-6 bg-blue-800 text-white border-t border-blue-700">
                            <p class="text-base leading-relaxed">Our AI analyzes objective data from your actual work tools, making reviews 85% more accurate than traditional subjective methods. You can customize performance metrics, edit AI suggestions, and add personal observations to ensure reviews reflect your team culture and expectations.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 2 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden" data-aos="fade-up" data-aos-duration="600" data-aos-delay="400">
                        <button class="faq-toggle w-full px-5 py-6 text-left flex items-center justify-between bg-white text-gray-800 hover:bg-gray-50">
                            <span class="font-bold text-lg pr-4">Can I customize the performance metrics ReviewBod tracks?</span>
                            <i class="fas fa-plus text-gray-800"></i>
                        </button>
                        <div class="faq-content hidden px-5 py-6 bg-blue-800 text-white border-t border-blue-700">
                            <p class="text-base leading-relaxed">Absolutely! You can set custom weights for different performance areas like code quality, collaboration, innovation, and delivery. Create role-specific metrics for developers, designers, and project managers that match your company's values and expectations.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 3 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden" data-aos="fade-up" data-aos-duration="600" data-aos-delay="500">
                        <button class="faq-toggle w-full px-5 py-6 text-left flex items-center justify-between bg-white text-gray-800 hover:bg-gray-50">
                            <span class="font-bold text-lg pr-4">How does the AI chatbot help with performance management?</span>
                            <i class="fas fa-plus text-gray-800"></i>
                        </button>
                        <div class="faq-content hidden px-5 py-6 bg-blue-800 text-white border-t border-blue-700">
                            <p class="text-base leading-relaxed">Our AI chatbot handles scheduling review cycles, sending reminders, collecting data from integrations, generating draft reviews, and answering questions about team performance. It saves managers 80% of the time typically spent on administrative review tasks.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 4 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden" data-aos="fade-up" data-aos-duration="600" data-aos-delay="600">
                        <button class="faq-toggle w-full px-5 py-6 text-left flex items-center justify-between bg-white text-gray-800 hover:bg-gray-50">
                            <span class="font-bold text-lg pr-4">Can I compare team members' performance against each other?</span>
                            <i class="fas fa-plus text-gray-800"></i>
                        </button>
                        <div class="faq-content hidden px-5 py-6 bg-blue-800 text-white border-t border-blue-700">
                            <p class="text-base leading-relaxed">Yes, ReviewBod provides team comparison analytics while maintaining individual privacy. You can see how team members perform relative to team averages, identify top performers, and spot those who might need additional support or training.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security & Privacy Section -->
        <div class="flex mt-[50px] w-full justify-center" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
            <div class="w-[68%]">
                <h3 class="mb-8 font-bold text-3xl text-[#CB964F]">Security & Privacy</h3>

                <div class="space-y-5">
                    <!-- FAQ Item 1 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden" data-aos="fade-up" data-aos-duration="600" data-aos-delay="300">
                        <button class="faq-toggle w-full px-5 py-6 text-left flex items-center justify-between bg-white text-gray-800 hover:bg-gray-50">
                            <span class="font-bold text-lg pr-4">Is my team's performance data secure with ReviewBod?</span>
                            <i class="fas fa-plus text-gray-800"></i>
                        </button>
                        <div class="faq-content hidden px-5 py-6 bg-blue-800 text-white border-t border-blue-700">
                            <p class="text-base leading-relaxed">Absolutely. ReviewBod uses enterprise-grade encryption, SOC 2 compliance, and follows GDPR guidelines. We only access project data (tasks, timelines, comments) - never code, sensitive documents, or personal information. Your data is encrypted in transit and at rest.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 2 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden" data-aos="fade-up" data-aos-duration="600" data-aos-delay="400">
                        <button class="faq-toggle w-full px-5 py-6 text-left flex items-center justify-between bg-white text-gray-800 hover:bg-gray-50">
                            <span class="font-bold text-lg pr-4">Can employees see their own performance data?</span>
                            <i class="fas fa-plus text-gray-800"></i>
                        </button>
                        <div class="faq-content hidden px-5 py-6 bg-blue-800 text-white border-t border-blue-700">
                            <p class="text-base leading-relaxed">Yes, employees can access their own performance dashboards to track their progress, see their metrics, and understand how they're performing relative to team goals. This transparency helps build trust and encourages self-improvement.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 3 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden" data-aos="fade-up" data-aos-duration="600" data-aos-delay="500">
                        <button class="faq-toggle w-full px-5 py-6 text-left flex items-center justify-between bg-white text-gray-800 hover:bg-gray-50">
                            <span class="font-bold text-lg pr-4">What happens to my data if I cancel my subscription?</span>
                            <i class="fas fa-plus text-gray-800"></i>
                        </button>
                        <div class="faq-content hidden px-5 py-6 bg-blue-800 text-white border-t border-blue-700">
                            <p class="text-base leading-relaxed">You can export all your performance data, reviews, and analytics before canceling. After cancellation, we provide a 30-day grace period to download your data, then all information is permanently deleted from our servers in compliance with data protection regulations.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pricing & Plans Section -->
        <div class="flex mt-[50px] w-full justify-center" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
            <div class="w-[68%]">
                <h3 class="mb-8 font-bold text-3xl text-[#CB964F]">Pricing & Plans</h3>

                <div class="space-y-5">
                    <!-- FAQ Item 1 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden" data-aos="fade-up" data-aos-duration="600" data-aos-delay="300">
                        <button class="faq-toggle w-full px-5 py-6 text-left flex items-center justify-between bg-white text-gray-800 hover:bg-gray-50">
                            <span class="font-bold text-lg pr-4">How does per-user pricing work?</span>
                            <i class="fas fa-plus text-gray-800"></i>
                        </button>
                        <div class="faq-content hidden px-5 py-6 bg-blue-800 text-white border-t border-blue-700">
                            <p class="text-base leading-relaxed">Our pricing is based on the number of team members you want to track and review. You only pay for active users who have performance data being analyzed. Admins and viewers don't count toward your user limit.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 2 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden" data-aos="fade-up" data-aos-duration="600" data-aos-delay="400">
                        <button class="faq-toggle w-full px-5 py-6 text-left flex items-center justify-between bg-white text-gray-800 hover:bg-gray-50">
                            <span class="font-bold text-lg pr-4">Can I upgrade or downgrade my plan anytime?</span>
                            <i class="fas fa-plus text-gray-800"></i>
                        </button>
                        <div class="faq-content hidden px-5 py-6 bg-blue-800 text-white border-t border-blue-700">
                            <p class="text-base leading-relaxed">Yes, you can change your plan at any time. Upgrades take effect immediately, while downgrades take effect at your next billing cycle. We provide prorated credits for any unused time when upgrading.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 3 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden" data-aos="fade-up" data-aos-duration="600" data-aos-delay="500">
                        <button class="faq-toggle w-full px-5 py-6 text-left flex items-center justify-between bg-white text-gray-800 hover:bg-gray-50">
                            <span class="font-bold text-lg pr-4">Is there a setup fee or long-term contract required?</span>
                            <i class="fas fa-plus text-gray-800"></i>
                        </button>
                        <div class="faq-content hidden px-5 py-6 bg-blue-800 text-white border-t border-blue-700">
                            <p class="text-base leading-relaxed">No setup fees and no long-term contracts required. All plans are month-to-month with the flexibility to cancel anytime. Enterprise customers can choose annual billing for additional discounts.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</main>
@include("front.layout.footer")