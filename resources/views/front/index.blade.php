@include('front.layout.header')

<style>
/* FAQ Section Animations */
.faq-left {
    opacity: 0;
    transform: translateX(-50px);
    animation: slideInLeft 1s ease-out 0.3s forwards;
}

.faq-right {
    opacity: 0;
    transform: translateX(50px);
    animation: slideInRight 1s ease-out 0.6s forwards;
}

.faq-item {
    opacity: 0;
    transform: translateY(30px);
    animation: slideInUp 0.6s ease-out forwards;
}

/* Stagger the FAQ items */
.faq-item:nth-child(1) { animation-delay: 0.8s; }
.faq-item:nth-child(2) { animation-delay: 1.0s; }
.faq-item:nth-child(3) { animation-delay: 1.2s; }
.faq-item:nth-child(4) { animation-delay: 1.4s; }
.faq-item:nth-child(5) { animation-delay: 1.6s; }
.faq-item:nth-child(6) { animation-delay: 1.8s; }

/* Keyframe animations */
@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Optional: Add hover animations for FAQ items */
.faq-item {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.faq-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

/* FAQ button hover effect */
.faq-toggle {
    transition: all 0.3s ease;
}

.faq-toggle:hover {
    background-color: #1e40af !important;
}
 
/* Pricing Section Animations */
.pricing-header {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.8s ease-out;
}

.pricing-header.animate {
    opacity: 1;
    transform: translateY(0);
}

.pricing-card {
    opacity: 0;
    transform: translateY(50px);
    transition: all 0.8s ease-out;
}

.pricing-card.animate {
    opacity: 1;
    transform: translateY(0);
}

/* Stagger the animations */
.pricing-card:nth-child(1) {
    transition-delay: 0.2s;
}

.pricing-card:nth-child(2) {
    transition-delay: 0.4s;
}

.pricing-card:nth-child(3) {
    transition-delay: 0.6s;
}

/* Optional hover effect */
.pricing-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}
 
    .hero-zoom {
        animation: zoomOut 1.5s ease-out forwards;
        transform: scale(1.1);
    }

    .hero-title {
        animation: zoomOutFade 1.2s ease-out 0.3s forwards;
        opacity: 0;
        transform: scale(1.2);
    }

    .hero-title-2 {
        animation: zoomOutFade 1.2s ease-out 0.6s forwards;
        opacity: 0;
        transform: scale(1.2);
    }

    .hero-subtitle {
        animation: zoomOutFade 1.2s ease-out 0.9s forwards;
        opacity: 0;
        transform: scale(1.1);
    }

    .hero-video {
        animation: zoomOutFade 1.5s ease-out 1.2s forwards;
        opacity: 0;
        transform: scale(1.2);
    }

    @keyframes zoomOut {
        from {
            transform: scale(1.1);
        }

        to {
            transform: scale(1);
        }
    }

    @keyframes zoomOutFade {
        from {
            opacity: 0;
            transform: scale(1.2);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }
</style>

<main class="pt-24" style="margin-top:50px">
    <div class="flex flex-col mx-auto gap-4 rb-header hero-zoom">
        <h2 class="text-black text-5xl font-bold text-center hero-title">
            Transform Your Performance
        </h2>
        <h2 class="text-black text-5xl font-bold text-center hero-title-2">
            Management with <b class="text-[#1E3A8A]">Reviewbod!</b>
        </h2>

        <p class="text-center text-[19px] w-[67%] self-center mt-5 hero-subtitle">
            Discover the power of ReviewBod — your AI-driven platform for streamlined performance reviews, team
            management, and data-driven insights.
        </p>

        <div class="self-center mt-7 shadow shadow-md hero-video">
            <video autoplay muted loop playsinline style="width: 1300px; height: auto;">
                <source src="/rb.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
    </div>

    <div class="flex flex-col gap-6 mt-[70px]">

        <h3 class="text-5xl font-bold text-center" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">What
            Can I <b class="text-[#1E3A8A]">Benefit?</b></h3>
        <div class="grid grid-cols-3 gap-0 gap-y-[80px] mt-[70px] w-[70%] self-center">
            <div class="flex flex-col items-center gap-2" data-aos="fade-up" data-aos-duration="1000"
                data-aos-delay="100">
                <div class="p-4 bg-[#EBEEFF] rounded-md w-[50px] h-[50px] items-center flex justify-center">
                    <i class="fas fa-brain text-[#1E3A8A] text-xl"></i>
                </div>
                <h3 class="font-bold text-[20px]">AI-Driven Performance Insights</h3>
                <div class="flex flex-col gap-1 items-center text-[#545C66]">
                    <span>Automate reviews with data from</span>
                    <span>Linear, Jira & Trello integrations.</span>
                </div>
            </div>

            <div class="flex flex-col items-center gap-2" data-aos="fade-up" data-aos-duration="1000"
                data-aos-delay="200">
                <div class="p-4 bg-[#EBEEFF] rounded-md w-[50px] h-[50px] items-center flex justify-center">
                    <i class="fas fa-users-cog text-[#1E3A8A] text-xl"></i>
                </div>
                <h3 class="font-bold text-[20px]">Smart Team Management</h3>
                <div class="flex flex-col gap-1 items-center text-[#545C66]">
                    <span>Identify high performers and</span>
                    <span>growth opportunities automatically.</span>
                </div>
            </div>

            <div class="flex flex-col items-center gap-2" data-aos="fade-up" data-aos-duration="1000"
                data-aos-delay="400">
                <div class="p-4 bg-[#EBEEFF] rounded-md w-[50px] h-[50px] items-center flex justify-center">
                    <i class="fas fa-plug text-[#1E3A8A] text-xl"></i>
                </div>
                <h3 class="font-bold text-[20px]">Seamless Integrations</h3>
                <div class="flex flex-col gap-1 items-center text-[#545C66]">
                    <span>Connect Linear, Jira, Trello</span>
                    <span>& Notion without workflow disruption.</span>
                </div>
            </div>

            <div class="flex flex-col items-center gap-2" data-aos="fade-up" data-aos-duration="1000"
                data-aos-delay="500">
                <div class="p-4 bg-[#EBEEFF] rounded-md w-[50px] h-[50px] items-center flex justify-center">
                    <i class="fas fa-chart-line text-[#1E3A8A] text-xl"></i>
                </div>
                <h3 class="font-bold text-[20px]">Data-Driven Decisions</h3>
                <div class="flex flex-col gap-1 items-center text-[#545C66]">
                    <span>Make promotion & salary decisions</span>
                    <span>based on real performance data.</span>
                </div>
            </div>

            <div class="flex flex-col items-center gap-2" data-aos="fade-up" data-aos-duration="1000"
                data-aos-delay="600">
                <div class="p-4 bg-[#EBEEFF] rounded-md w-[50px] h-[50px] items-center flex justify-center">
                    <i class="fas fa-sliders-h text-[#1E3A8A] text-xl"></i>
                </div>
                <h3 class="font-bold text-[20px]">Custom Performance Metrics</h3>
                <div class="flex flex-col gap-1 items-center text-[#545C66]">
                    <span>Set evaluation criteria that match</span>
                    <span>your company culture & roles.</span>
                </div>
            </div>

            <div class="flex flex-col items-center gap-2" data-aos="fade-up" data-aos-duration="1000"
                data-aos-delay="700">
                <div class="p-4 bg-[#EBEEFF] rounded-md w-[50px] h-[50px] items-center flex justify-center">
                    <i class="fas fa-clock text-[#1E3A8A] text-xl"></i>
                </div>
                <h3 class="font-bold text-[20px]">Save 80% Review Time</h3>
                <div class="flex flex-col gap-1 items-center text-[#545C66]">
                    <span>AI chatbot handles scheduling,</span>
                    <span>data collection & report generation.</span>
                </div>
            </div>
        </div>
    </div>

<div class="mt-[150px] flex items-center flex-col gap-5 pricing-header">
    <h3 class="text-5xl font-bold text-center">Flexible <b class="text-[#1E3A8A]">Pricing</b> for Every Need!</h3>
    <span class="text-[#545C66] text-1xl text-center">Choose the perfect plan for your needs—no hidden fees, just powerful AI at your fingertips!</span>
</div>

<div class="flex gap-7 items-center justify-center mt-[70px] pricing-container">
    <!-- Starter Plan -->
    <div class="w-[352px] h-[494px] rounded-[16px] bg-[#E5E7EB] shadow-[0_4px_6px_rgba(0,0,0,0.03),0_10px_15px_rgba(0,0,0,0.03)] pricing-card">
        <div class="p-6">
            <h3 class="text-sm font-bold text-black underline">Starter</h3>
            <h1 class="text-4xl font-bold text-black mt-2">$12<span class="text-lg">/user/month</span></h1>
            <div class="mt-4 mb-4 border-b border-[#545c661a]"></div>
            <p class="text-[#545c66] text-sm mt-2">Perfect for small teams getting started.</p>
            <ul class="mt-4 space-y-2">
                <li class="flex items-center text-[#545c66] text-sm">
                    <i class="fas fa-check text-[#10b981] mr-2"></i>
                    Up to 5 team members
                </li>
                <li class="flex items-center text-[#545c66] text-sm">
                    <i class="fas fa-check text-[#10b981] mr-2"></i>
                    Basic AI performance reviews
                </li>
                <li class="flex items-center text-[#545c66] text-sm">
                    <i class="fas fa-check text-[#10b981] mr-2"></i>
                    1 integration (Linear or Jira)
                </li>
                <li class="flex items-center text-[#545c66] text-sm">
                    <i class="fas fa-check text-[#10b981] mr-2"></i>
                    Basic performance analytics
                </li>
                <li class="flex items-center text-[#545c66] text-sm">
                    <i class="fas fa-check text-[#10b981] mr-2"></i>
                    Monthly team reports
                </li>
                <li class="flex items-center text-[#545c66] text-sm">
                    <i class="fas fa-check text-[#10b981] mr-2"></i>
                    Email support
                </li>
            </ul>
            <div class="mt-6">
                <button class="w-[312px] h-[40px] rounded-[20px] bg-[#bd9555] text-white text-sm font-bold">
                    Start Free Trial
                </button>
            </div>
        </div>
    </div>

    <!-- Professional Plan (Recommended) -->
    <div style="background:linear-gradient(45deg, #111827 0%, #374151 100%)" class="w-[352px] h-[494px] rounded-[16px] shadow-[0_4px_6px_rgba(0,0,0,0.03),0_10px_15px_rgba(0,0,0,0.03)] relative pricing-card">
        <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-[#10b981] text-white px-4 py-1 rounded-full text-xs font-bold">
            MOST POPULAR
        </div>
        <div class="p-6">
            <h3 class="text-sm font-bold text-white underline">Professional</h3>
            <h1 class="text-4xl font-bold text-white mt-2">$25<span class="text-lg">/user/month</span></h1>
            <div class="mt-4 mb-4 border-b border-[#545c661a]"></div>
            <p class="text-[#D1D5DB] text-sm mt-2">Perfect for growing teams and managers.</p>
            <ul class="mt-4 space-y-2 text-white">
                <li class="flex items-center text-[#9CA3AF] text-sm">
                    <i class="fas fa-check text-[#10b981] mr-2"></i>
                    Up to 25 team members
                </li>
                <li class="flex items-center text-[#9CA3AF] text-sm">
                    <i class="fas fa-check text-[#10b981] mr-2"></i>
                    Advanced AI insights & recommendations
                </li>
                <li class="flex items-center text-[#9CA3AF] text-sm">
                    <i class="fas fa-check text-[#10b981] mr-2"></i>
                    All integrations (Linear, Jira, Trello)
                </li>
                <li class="flex items-center text-[#9CA3AF] text-sm">
                    <i class="fas fa-check text-[#10b981] mr-2"></i>
                    Custom performance metrics
                </li>
                <li class="flex items-center text-[#9CA3AF] text-sm">
                    <i class="fas fa-check text-[#10b981] mr-2"></i>
                    Real-time performance tracking
                </li>
                <li class="flex items-center text-[#9CA3AF] text-sm">
                    <i class="fas fa-check text-[#10b981] mr-2"></i>
                    Team comparison analytics
                </li>
                <li class="flex items-center text-[#9CA3AF] text-sm">
                    <i class="fas fa-check text-[#10b981] mr-2"></i>
                    Priority support & AI chat
                </li>
            </ul>
            <div class="mt-6">
                <button class="w-[312px] h-[40px] rounded-[20px] bg-[#10b981] text-white text-sm font-bold">
                    Start Free Trial
                </button>
            </div>
        </div>
    </div>

    <!-- Enterprise Plan -->
    <div class="w-[352px] h-[494px] rounded-[16px] bg-[#E5E7EB] shadow-[0_4px_6px_rgba(0,0,0,0.03),0_10px_15px_rgba(0,0,0,0.03)] pricing-card">
        <div class="p-6">
            <h3 class="text-sm font-bold text-black underline">Enterprise</h3>
            <h1 class="text-4xl font-bold text-black mt-2">$45<span class="text-lg">/user/month</span></h1>
            <div class="mt-4 mb-4 border-b border-[#545c661a]"></div>
            <p class="text-[#545c66] text-sm mt-2">For large teams needing advanced features.</p>
            <ul class="mt-4 space-y-2">
                <li class="flex items-center text-[#545c66] text-sm">
                    <i class="fas fa-check text-[#10b981] mr-2"></i>
                    Unlimited team members
                </li>
                <li class="flex items-center text-[#545c66] text-sm">
                    <i class="fas fa-check text-[#10b981] mr-2"></i>
                    Custom AI model training
                </li>
                <li class="flex items-center text-[#545c66] text-sm">
                    <i class="fas fa-check text-[#10b981] mr-2"></i>
                    Custom integrations & API access
                </li>
                <li class="flex items-center text-[#545c66] text-sm">
                    <i class="fas fa-check text-[#10b981] mr-2"></i>
                    Multi-department analytics
                </li>
                <li class="flex items-center text-[#545c66] text-sm">
                    <i class="fas fa-check text-[#10b981] mr-2"></i>
                    Advanced security & compliance
                </li>
                <li class="flex items-center text-[#545c66] text-sm">
                    <i class="fas fa-check text-[#10b981] mr-2"></i>
                    Dedicated account manager
                </li>
                <li class="flex items-center text-[#545c66] text-sm">
                    <i class="fas fa-check text-[#10b981] mr-2"></i>
                    24/7 phone & chat support
                </li>
            </ul>
            <div class="mt-6">
                <button class="w-[312px] h-[40px] rounded-[20px] bg-[#bd9555] text-white text-sm font-bold">
                    Contact Sales
                </button>
            </div>
        </div>
    </div>
</div>

<div class="flex justify-between mt-[150px] px-[90px]">

    <div class="flex flex-col gap-2" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="200">
        <h3 class="font-bold text-5xl">Frequently <b class="text-[#1E3A8A]">asked</b></h3>
        <h3 class="font-bold text-5xl text-[#1E3A8A] mt-2">questions</h3>
        
        <div class="flex flex-col gap-2 mt-7">
            <span class="text-[#545C66]">For any unanswered questions, reach out to our support team via</span>
            <span class="text-[#545C66]">contact us page or email. We'll respond with-in a day to assist you.</span>
        </div>
    </div>

    <div class="w-[45%]" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="400">
        <div class="space-y-5">
            <!-- FAQ Item 1 - Expanded by default -->
            <div class="border border-gray-200 rounded-lg overflow-hidden" data-aos="fade-up" data-aos-duration="600" data-aos-delay="600">
                <button class="faq-toggle w-full px-5 py-6 text-left flex items-center justify-between bg-blue-800 text-white">
                    <span class="font-bold text-lg pr-4">How does ReviewBod's AI analyze my team's performance?</span>
                    <i class="fas fa-minus text-white"></i>
                </button>
                <div class="faq-content px-5 py-6 bg-blue-800 text-white border-t border-blue-700">
                    <p class="text-base leading-relaxed">ReviewBod connects to your Linear, Jira, and Trello accounts to analyze real work data like task completion rates, code review quality, collaboration patterns, and deadline adherence. Our AI then generates objective performance insights and actionable recommendations for each team member.</p>
                </div>
            </div>

            <!-- FAQ Item 2 -->
            <div class="border border-gray-200 rounded-lg overflow-hidden" data-aos="fade-up" data-aos-duration="600" data-aos-delay="700">
                <button class="faq-toggle w-full px-5 py-6 text-left flex items-center justify-between bg-white text-gray-800 hover:bg-gray-50">
                    <span class="font-bold text-lg pr-4">Can I try ReviewBod for free before purchasing?</span>
                    <i class="fas fa-plus text-gray-800"></i>
                </button>
                <div class="faq-content hidden px-5 py-6 bg-blue-800 text-white border-t border-blue-700">
                    <p class="text-base leading-relaxed">Yes! ReviewBod offers a 7-day free trial with full access to all features. Connect your tools, generate AI reviews for your team, and see the value before committing to any paid plan. No credit card required to start.</p>
                </div>
            </div>

            <!-- FAQ Item 3 -->
            <div class="border border-gray-200 rounded-lg overflow-hidden" data-aos="fade-up" data-aos-duration="600" data-aos-delay="800">
                <button class="faq-toggle w-full px-5 py-6 text-left flex items-center justify-between bg-white text-gray-800 hover:bg-gray-50">
                    <span class="font-bold text-lg pr-4">Which project management tools does ReviewBod integrate with?</span>
                    <i class="fas fa-plus text-gray-800"></i>
                </button>
                <div class="faq-content hidden px-5 py-6 bg-blue-800 text-white border-t border-blue-700">
                    <p class="text-base leading-relaxed">ReviewBod currently integrates with Linear, Jira, Trello, and Notion. We're constantly adding new integrations based on user requests. Our API also allows custom integrations for Enterprise customers with unique tool requirements.</p>
                </div>
            </div>

            <!-- FAQ Item 4 -->
            <div class="border border-gray-200 rounded-lg overflow-hidden" data-aos="fade-up" data-aos-duration="600" data-aos-delay="900">
                <button class="faq-toggle w-full px-5 py-6 text-left flex items-center justify-between bg-white text-gray-800 hover:bg-gray-50">
                    <span class="font-bold text-lg pr-4">How accurate are the AI-generated performance reviews?</span>
                    <i class="fas fa-plus text-gray-800"></i>
                </button>
                <div class="faq-content hidden px-5 py-6 bg-blue-800 text-white border-t border-blue-700">
                    <p class="text-base leading-relaxed">Our AI analyzes objective data from your actual work tools, making reviews 85% more accurate than traditional subjective methods. You can customize performance metrics, edit AI suggestions, and add personal observations to ensure reviews reflect your team culture and expectations.</p>
                </div>
            </div>

            <!-- FAQ Item 5 -->
            <div class="border border-gray-200 rounded-lg overflow-hidden" data-aos="fade-up" data-aos-duration="600" data-aos-delay="1000">
                <button class="faq-toggle w-full px-5 py-6 text-left flex items-center justify-between bg-white text-gray-800 hover:bg-gray-50">
                    <span class="font-bold text-lg pr-4">Is my team's performance data secure with ReviewBod?</span>
                    <i class="fas fa-plus text-gray-800"></i>
                </button>
                <div class="faq-content hidden px-5 py-6 bg-blue-800 text-white border-t border-blue-700">
                    <p class="text-base leading-relaxed">Absolutely. ReviewBod uses enterprise-grade encryption, SOC 2 compliance, and follows GDPR guidelines. We only access project data (tasks, timelines, comments) - never code, sensitive documents, or personal information. Your data is encrypted in transit and at rest.</p>
                </div>
            </div>

            <!-- FAQ Item 6 -->
            <div class="border border-gray-200 rounded-lg overflow-hidden" data-aos="fade-up" data-aos-duration="600" data-aos-delay="1100">
                <button class="faq-toggle w-full px-5 py-6 text-left flex items-center justify-between bg-white text-gray-800 hover:bg-gray-50">
                    <span class="font-bold text-lg pr-4">How long does it take to set up ReviewBod for my team?</span>
                    <i class="fas fa-plus text-gray-800"></i>
                </button>
                <div class="faq-content hidden px-5 py-6 bg-blue-800 text-white border-t border-blue-700">
                    <p class="text-base leading-relaxed">Setup takes just 5 minutes! Connect your Linear or Jira account, select team members to include, and our AI will start analyzing historical data immediately. You can generate your first performance review within 10 minutes of signup.</p>
                </div>
            </div>
        </div>
    </div>

</div>


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
        <h1 class="text-5xl font-bold text-white mb-4" data-aos="fade-up" data-aos-duration="800" data-aos-delay="400">
            Get Started with Reviewbod Today!
        </h1>
        <p class="text-white text-lg mb-6" data-aos="fade-up" data-aos-duration="800" data-aos-delay="600">
            Unlock seamless performance management, AI-driven insights, and team automation—all in one platform.
        </p>
        <button class="bg-white text-[#1e3a8a] font-bold text-lg px-8 py-3 rounded-full shadow-[0_4px_4px_rgba(66,133,244,1)]" data-aos="zoom-in" data-aos-duration="600" data-aos-delay="800">
            Try 7-Days Free Trial
        </button>
    </div>
</div>
</main>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Create intersection observer
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                if (entry.target.classList.contains('pricing-header')) {
                    entry.target.classList.add('animate');
                }
                
                if (entry.target.classList.contains('pricing-container')) {
                    const cards = entry.target.querySelectorAll('.pricing-card');
                    cards.forEach(card => {
                        card.classList.add('animate');
                    });
                }
                
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    // Observe elements
    const pricingHeader = document.querySelector('.pricing-header');
    const pricingContainer = document.querySelector('.pricing-container');
    
    if (pricingHeader) observer.observe(pricingHeader);
    if (pricingContainer) observer.observe(pricingContainer);
});
</script>
@include('front.layout.footer')
