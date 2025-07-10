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
            <h1 class="text-5xl font-bold text-white mb-4" data-aos="fade-up" data-aos-duration="800" data-aos-delay="400">HOW IT WORKS</h1>
            <p class="text-white text-lg mb-6 w-[80%] mx-auto mt-3" data-aos="fade-up" data-aos-duration="800" data-aos-delay="600">Transform your team's performance management in minutes. See how ReviewBod's AI analyzes your work data to generate meaningful performance insights.</p>
            <button class="bg-white mt-2 text-[#1e3a8a] font-bold text-lg px-8 py-3 rounded-full shadow-[0_4px_4px_rgba(66,133,244,1)]" data-aos="zoom-in" data-aos-duration="600" data-aos-delay="800">
                Try 7-Days Free Trial
            </button>
        </div>
    </div>

    <div class="mt-[150px] flex items-center flex-col gap-5" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
        <h3 class="text-5xl font-bold text-center">Launch Your <b class="text-[#1E3A8A]">AI Performance Reviews</b> in Just 3 Simple Steps</h3>
    </div>

    <!-- Step 1 & 2 -->
    <div class="flex gap-9 mt-5 w-full px-[80px] items-center mt-[80px]">
        <div class="flex flex-col rounded-[30px] p-[50px] gap-4 justify-center w-[75%] h-[850px]" style="background:rgba(84, 92, 102, 0.04)" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="300">
            <div class="flex items-center justify-center p-3 w-[100px] bg-[#1E3A8A] rounded-full text-white">
                <span>Step 1</span>
            </div>

            <h3 class="text-black font-bold text-[20px] mt-5">Connect Your Tools</h3>

            <span class="text-[19px] mt-5">Connect ReviewBod to your existing project management tools like Linear, Jira, or Trello. Our secure integration takes just 2 minutes and starts analyzing your team's work patterns immediately.</span>

            <img src="/linkapp.svg" class="self-center mt-5" width="600">
        </div>

        <div class="flex flex-col rounded-[30px] p-[50px] gap-4 justify-center w-[75%] h-[850px]" style="background:rgba(84, 92, 102, 0.04)" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="500">
            <div class="flex items-center justify-center p-3 w-[100px] bg-[#1E3A8A] rounded-full text-white">
                <span>Step 2</span>
            </div>

            <h3 class="text-black font-bold text-[20px] mt-5">Customize Performance Metrics</h3>

            <span class="text-[19px] mt-5">Set up custom performance criteria that match your team culture. Choose what matters most: code quality, collaboration, innovation, or delivery speed. Our AI adapts to your company's unique values.</span>

            <img src="/workspace.svg" class="mt-[50px] self-center" width="600">
        </div>
    </div>

    <!-- Step 3 -->
    <div class="flex gap-9 mt-5 w-full px-[80px] items-center mt-[80px]">
        <div class="flex justify-between rounded-[30px] p-[50px] gap-4 justify-center w-full" style="background:rgba(84, 92, 102, 0.04)" data-aos="zoom-in" data-aos-duration="1000" data-aos-delay="400">
            <div class="flex flex-col gap-8">
                <div class="flex items-center justify-center p-3 w-[100px] bg-[#1E3A8A] rounded-full text-white">
                    <span>Step 3</span>
                </div>

                <h3 class="text-black font-bold text-[20px] mt-5">Generate AI-Powered Reviews</h3>

                <span class="text-[19px] mt-5 w-[700px]">Watch as ReviewBod's AI analyzes months of work data to generate objective, actionable performance reviews. Save 80% of your review time while providing better feedback to your team.</span>
                
                <button type="button" class="rb-shadow text-white bg-[#1E3A8A] hover:bg-blue-800 font-medium rounded-full px-8 py-5 w-[300px] text-xl text-center">
                    Try 7-Days Free Trial
                </button>
            </div>
            <div>
                <img src="/dash.svg" class="self-center mt-5" width="700">
            </div>
        </div>
    </div>

    <!-- Additional Benefits Section -->
    <div class="mt-[120px] px-[80px]" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
        <div class="text-center mb-[80px]">
            <h3 class="text-4xl font-bold text-center mb-4">Why Teams Love <b class="text-[#1E3A8A]">ReviewBod</b></h3>
            <p class="text-[#545C66] text-lg">See what makes our AI performance management different</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center p-8 rounded-2xl bg-gradient-to-br from-blue-50 to-indigo-100" data-aos="fade-up" data-aos-duration="600" data-aos-delay="300">
                <div class="w-16 h-16 bg-[#1E3A8A] rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-clock text-white text-2xl"></i>
                </div>
                <h4 class="text-xl font-bold mb-3">Save 80% Time</h4>
                <p class="text-[#545C66]">Automate the entire review process from data collection to final reports. Spend time coaching, not paperwork.</p>
            </div>

            <div class="text-center p-8 rounded-2xl bg-gradient-to-br from-green-50 to-emerald-100" data-aos="fade-up" data-aos-duration="600" data-aos-delay="400">
                <div class="w-16 h-16 bg-[#10b981] rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-chart-line text-white text-2xl"></i>
                </div>
                <h4 class="text-xl font-bold mb-3">85% More Accurate</h4>
                <p class="text-[#545C66]">Data-driven reviews eliminate bias and provide objective insights based on actual work performance.</p>
            </div>

            <div class="text-center p-8 rounded-2xl bg-gradient-to-br from-purple-50 to-violet-100" data-aos="fade-up" data-aos-duration="600" data-aos-delay="500">
                <div class="w-16 h-16 bg-[#8b5cf6] rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-users text-white text-2xl"></i>
                </div>
                <h4 class="text-xl font-bold mb-3">Better Team Engagement</h4>
                <p class="text-[#545C66]">Transparent, fair reviews build trust and help team members understand their growth path.</p>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
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
@include("front.layout.footer")