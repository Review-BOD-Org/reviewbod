<!-- views/dash/members.blade.php -->
@extends('dash.layouts.app')

@section('page-title', 'Settings')

@section('content')
    @include('dash.layouts.partials.head')
    <style>
        .font-schibsted {
            font-family: 'Schibsted Grotesk', sans-serif;
        }

        /* Add some custom styles for better chart integration */
        #taskChart {
            border-radius: 8px;
        }
    </style>
    <div class="p-9 flex flex-col">
        <div class="bg-[#1E2875] w-full h-[300px] rounded-lg p-5 relative">
            <h3 class="text-white text-xl font-semibold">User Details</h3>
        </div>

        <div class="flex gap-6 self-center relative -mt-32">
            <!-- Left Card - User Details -->
            <div class="bg-white rounded-lg shadow-lg p-6 w-[400px] border border-gray-100">
                <!-- Profile Section -->
                <div class="flex items-center gap-4 mb-6 justify-between">
                    <div class="relative">
                        <div class="w-[72px] h-[72px] rounded-full overflow-hidden"
                            style="background: linear-gradient(135deg, #FFA78D 0%, #FF8A6B 100%);">
                            <img src="https://static.vecteezy.com/system/resources/thumbnails/036/594/092/small_2x/man-empty-avatar-photo-placeholder-for-social-networks-resumes-forums-and-dating-sites-male-and-female-no-photo-images-for-unfilled-user-profile-free-vector.jpg"
                                alt="Profile Avatar" class="w-full h-full object-cover">
                        </div>
                    </div>
                    <button
                        class="bg-[#F0EFFA] flex gap-3 items-center hover:bg-[#E8E7F5] px-4 py-2 rounded-full text-xs font-medium text-gray-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="2"
                                d="M15 19c1.2-3.678 2.526-5.005 6-6c-3.474-.995-4.8-2.322-6-6c-1.2 3.678-2.526 5.005-6 6c3.474.995 4.8 2.322 6 6Zm-8-9c.6-1.84 1.263-2.503 3-3c-1.737-.497-2.4-1.16-3-3c-.6 1.84-1.263 2.503-3 3c1.737.497 2.4 1.16 3 3Zm1.5 10c.3-.92.631-1.251 1.5-1.5c-.869-.249-1.2-.58-1.5-1.5c-.3.92-.631 1.251-1.5 1.5c.869.249 1.2.58 1.5 1.5Z" />
                        </svg>
                        Analyze
                    </button>
                </div>

                <!-- Personal Information Card -->
                <div class="bg-white border border-black/15 rounded shadow-sm p-4 mb-6">
                    <!-- Full Name -->
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <label class="text-xs font-medium text-gray-600 opacity-70">Full Name</label>
                            <p class="text-sm font-medium text-gray-900 opacity-90">{{ $data->name }}</p>
                        </div>
                        <button class="px-3 py-1 rounded-full text-xs font-medium text-gray-700 transition-colors">
                            <i class="fa fa-user text-[18px]"></i>
                        </button>
                    </div>

                    <!-- Email -->
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <label class="text-xs font-medium text-gray-600 opacity-70">Email</label>
                            <p class="text-sm font-medium text-gray-900 opacity-90">{{ $data->email }}</p>
                        </div>
                        <button class=" px-3 py-1 rounded-full text-xs font-medium text-gray-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 20 20">
                                <path fill="currentColor"
                                    d="M14.608 12.172c0 .84.239 1.175.864 1.175c1.393 0 2.28-1.775 2.28-4.727c0-4.512-3.288-6.672-7.393-6.672c-4.223 0-8.064 2.832-8.064 8.184c0 5.112 3.36 7.896 8.52 7.896c1.752 0 2.928-.192 4.727-.792l.386 1.607c-1.776.577-3.674.744-5.137.744c-6.768 0-10.393-3.72-10.393-9.456c0-5.784 4.201-9.72 9.985-9.72c6.024 0 9.215 3.6 9.215 8.016c0 3.744-1.175 6.6-4.871 6.6c-1.681 0-2.784-.672-2.928-2.161c-.432 1.656-1.584 2.161-3.145 2.161c-2.088 0-3.84-1.609-3.84-4.848c0-3.264 1.537-5.28 4.297-5.28c1.464 0 2.376.576 2.782 1.488l.697-1.272h2.016v7.057zm-2.951-3.168c0-1.319-.985-1.872-1.801-1.872c-.888 0-1.871.719-1.871 2.832c0 1.68.744 2.616 1.871 2.616c.792 0 1.801-.504 1.801-1.896z" />
                            </svg>
                        </button>
                    </div>

                    <!-- Phone Number -->
                    <div class="flex justify-between items-center">
                        <div>
                            <label class="text-xs font-medium text-gray-600 opacity-70">Date Joined</label>
                            <p class="text-sm font-medium text-gray-900 opacity-90">{{ $data->created_at }}</p>
                        </div>
                        <button class="  px-3 py-1 rounded-full text-xs font-medium text-gray-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 20 20">
                                <path fill="currentColor"
                                    d="M5.673 0a.7.7 0 0 1 .7.7v1.309h7.517v-1.3a.7.7 0 0 1 1.4 0v1.3H18a2 2 0 0 1 2 1.999v13.993A2 2 0 0 1 18 20H2a2 2 0 0 1-2-1.999V4.008a2 2 0 0 1 2-1.999h2.973V.699a.7.7 0 0 1 .7-.699M1.4 7.742v10.259a.6.6 0 0 0 .6.6h16a.6.6 0 0 0 .6-.6V7.756zm5.267 6.877v1.666H5v-1.666zm4.166 0v1.666H9.167v-1.666zm4.167 0v1.666h-1.667v-1.666zm-8.333-3.977v1.666H5v-1.666zm4.166 0v1.666H9.167v-1.666zm4.167 0v1.666h-1.667v-1.666zM4.973 3.408H2a.6.6 0 0 0-.6.6v2.335l17.2.014V4.008a.6.6 0 0 0-.6-.6h-2.71v.929a.7.7 0 0 1-1.4 0v-.929H6.373v.92a.7.7 0 0 1-1.4 0z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- About Section Card -->
                <div class="bg-white border border-black/15 rounded shadow-sm p-4 mb-6">
                    <div class="flex justify-between items-start mb-3">
                        <h4 class="text-sm font-medium text-gray-900">User status</h4>
                        <button
                            class="bg-green-400 hover:bg-[#E8E7F5] px-3 py-1 rounded-full text-xs font-medium text-gray-700 transition-colors">
                            Active
                        </button>
                    </div>
                    <p class="text-xs text-gray-700 opacity-80 leading-relaxed">
                        This user was last updated <b>{{ \Carbon\Carbon::parse($data->updated_at)->diffForHumans() }}</b>
                    </p>
                </div>

                <!-- Linked Apps Section Card -->
                <div class="bg-white border border-black/15 rounded shadow-sm p-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-4">Linked Apps</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-900 opacity-90">Linear</span>
                            <span
                                class="bg-[#99FDD2] text-gray-700 px-3 py-1 rounded-full text-xs font-medium opacity-80">Linked</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-900 opacity-90">Trello</span>
                            <span
                                class="bg-[#99FDD2] text-gray-700 px-3 py-1 rounded-full text-xs font-medium opacity-80">Linked</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Card - Professional Details -->
            <div class="bg-white rounded-lg shadow-lg border border-gray-100 p-4 w-[400px] h-fit">

                <!-- Professional Details Header Section -->
                <div
                    class="bg-white border border-black/15 rounded-lg shadow-sm p-4 mb-4 flex items-center justify-between">
                    <div class="flex items-center justify-between w-full gap-3">

                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Professional Details</h4>
                            <!-- Description -->
                            <p class="text-sm text-gray-600 mt-2">
                                This are the professional details shown to users in the app.
                            </p>
                        </div>

                        <!-- Stars Icon -->
                        <div class="w-10 h-10 flex items-center justify-center">
                            <svg width="40" height="40" viewBox="0 0 22 25" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.5"
                                    d="M8.72753 3.15878C10.4381 1.11204 11.2934 0.0886686 12.2766 0.246294C13.2598 0.40392 13.7198 1.63815 14.6397 4.10662L14.8777 4.74524C15.1391 5.4467 15.2698 5.79743 15.5241 6.05579C15.7784 6.31415 16.1276 6.4509 16.8258 6.72438L17.4616 6.97337C19.9188 7.93577 21.1474 8.41698 21.2876 9.40053C21.4277 10.3841 20.3865 11.2186 18.3042 12.8877L17.7654 13.3195C17.1737 13.7938 16.8778 14.0309 16.7066 14.3556C16.5353 14.6802 16.508 15.0555 16.4535 15.8061L16.4038 16.4895C16.2119 19.1311 16.1159 20.4518 15.2193 20.9021C14.3228 21.3523 13.2193 20.6339 11.0124 19.197L10.4415 18.8252C9.81435 18.4169 9.50078 18.2127 9.14062 18.155C8.78045 18.0972 8.41446 18.1924 7.68249 18.3829L7.01608 18.5563C4.44022 19.2264 3.15229 19.5615 2.45804 18.8562C1.76379 18.1509 2.123 16.8723 2.84142 14.3152L3.02728 13.6536C3.23143 12.927 3.33351 12.5637 3.28218 12.2034C3.23085 11.8431 3.03192 11.5266 2.63408 10.8936L2.27187 10.3174C0.871824 8.09004 0.171803 6.97636 0.639328 6.09022C1.10685 5.20408 2.4323 5.13234 5.08319 4.98887L5.76901 4.95175C6.52231 4.91098 6.89896 4.8906 7.2274 4.72566C7.55584 4.56073 7.79889 4.26992 8.28498 3.6883L8.72753 3.15878Z"
                                    fill="#2684FC" />
                                <path
                                    d="M15.1257 11.5283C13.8427 9.99321 13.2013 9.22568 12.4639 9.3439C11.7265 9.46212 11.3815 10.3878 10.6916 12.2391L10.5131 12.7181C10.317 13.2442 10.219 13.5073 10.0282 13.701C9.83749 13.8948 9.57564 13.9974 9.05194 14.2025L8.57515 14.3892C6.73222 15.111 5.81075 15.4719 5.70566 16.2096C5.60057 16.9472 6.38145 17.5731 7.94321 18.825L8.34726 19.1488C8.79106 19.5045 9.01296 19.6824 9.14141 19.9258C9.26985 20.1693 9.29031 20.4508 9.33121 21.0138L9.36845 21.5263C9.5124 23.5075 9.58437 24.4981 10.2568 24.8357C10.9293 25.1734 11.7568 24.6346 13.412 23.5569L13.8402 23.2781C14.3106 22.9718 14.5457 22.8187 14.8159 22.7754C15.086 22.7321 15.3605 22.8035 15.9095 22.9463L16.4093 23.0764C18.3412 23.579 19.3071 23.8303 19.8278 23.3013C20.3485 22.7724 20.0791 21.8134 19.5403 19.8956L19.4009 19.3994C19.2478 18.8544 19.1712 18.5819 19.2097 18.3117C19.2482 18.0415 19.3974 17.8041 19.6958 17.3294L19.9674 16.8972C21.0175 15.2267 21.5425 14.3914 21.1918 13.7268C20.8412 13.0622 19.8471 13.0084 17.8589 12.9008L17.3446 12.873C16.7796 12.8424 16.4971 12.8271 16.2508 12.7034C16.0044 12.5797 15.8222 12.3616 15.4576 11.9254L15.1257 11.5283Z"
                                    fill="#413B89" />
                            </svg>

                        </div>
                    </div>

                </div>



                <!-- Expertise In Section -->
                {{-- <div class="mb-4">
                    <h5 class="text-sm font-medium text-gray-900 mb-3">Expertise In</h5>
                    <div class="flex flex-wrap gap-2">
                        <!-- Career Tag -->
                        <div class="bg-white border border-gray-300 rounded-full px-3 py-1.5 flex items-center gap-2">
                            <div class="w-4 h-4 flex items-center justify-center">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path d="M3 4H13V12H3V4Z" fill="#0ead69"/>
                                    <path d="M4 5H12V6H4V5Z" fill="#ffedde"/>
                                    <path d="M4 7H8V8H4V7Z" fill="#ffedde"/>
                                </svg>
                            </div>
                            <span class="text-sm text-gray-700">Total</span>
                        </div>

                        <!-- Money Tag -->
                        <div class="bg-white border border-gray-300 rounded-full px-3 py-1.5 flex items-center gap-2">
                            <div class="w-4 h-4 flex items-center justify-center">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path d="M3 5C3 4.5 3.5 4 4 4H12C12.5 4 13 4.5 13 5V11C13 11.5 12.5 12 12 12H4C3.5 12 3 11.5 3 11V5Z" fill="#0ead69"/>
                                    <path d="M5 7H11V8H5V7Z" fill="#ffedde"/>
                                    <path d="M6 8.5H10V9H6V8.5Z" fill="#ffedde"/>
                                </svg>
                            </div>
                            <span class="text-sm text-gray-700">Money</span>
                        </div>

                        <!-- Mortgage Tag -->
                        <div class="bg-white border border-gray-300 rounded-full px-3 py-1.5 flex items-center gap-2">
                            <div class="w-4 h-4 flex items-center justify-center">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path d="M4 5L8 3L12 5V12H4V5Z" stroke="#413b89" stroke-width="0.8" fill="#ffedde"/>
                                    <path d="M4 5L8 3L12 5" stroke="#413b89" stroke-width="0.8" fill="none"/>
                                </svg>
                            </div>
                            <span class="text-sm text-gray-700">Mortgage</span>
                        </div>

                        <!-- Stock Tag -->
                        <div class="bg-white border border-gray-300 rounded-full px-3 py-1.5 flex items-center gap-2">
                            <div class="w-4 h-4 flex items-center justify-center">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path d="M3 11L5 9L8 10L13 5" stroke="#413b89" stroke-width="0.8" fill="none"/>
                                    <path d="M11 5H13V7" stroke="#413b89" stroke-width="0.8" fill="none"/>
                                </svg>
                            </div>
                            <span class="text-sm text-gray-700">Stock</span>
                        </div>
                    </div>
                </div> --}}

                <!-- Total Experience Section -->
                <div class="mb-4">
                    <h5 class="text-sm font-medium text-gray-900 mb-3">Total Tasks Assigned</h5>
                    <div class="bg-white border border-black/15 rounded-lg shadow-sm p-4 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">
                                {{ DB::table('tasks')->where(['user_id' => $data->user_id])->count() }}</p>
                            <p class="text-sm text-gray-600">Tasks Assgined</p>
                        </div>
                        <div class="w-12 h-12 bg-[#ffa78d] rounded-lg flex items-center justify-center">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <!-- Medal/Award Icon -->
                                <path d="M12 3L14 9H20L15.5 12.5L17.5 19L12 15L6.5 19L8.5 12.5L4 9H10L12 3Z"
                                    fill="white" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Ratings Section -->
                <div class="mb-6">
                    <h5 class="text-sm font-medium text-gray-900 mb-3">Total Project Assigned</h5>
                    <div class="bg-white border border-black/15 rounded-lg shadow-sm p-4 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">
                                {{ DB::table('projects')->join('tasks', 'tasks.project_id', 'projects.project_key')->where(['tasks.user_id' => $data->user_id])->count() }}
                            </p>
                            <p class="text-sm text-gray-600">Projects Assigned</p>
                        </div>
                        <div class="w-12 h-12 bg-[#ffcb00] bg-opacity-70 rounded-lg flex items-center justify-center">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <!-- Star Icon -->
                                <path
                                    d="M12 3L14.5 8.5H20.5L16.25 12.25L18.25 17.75L12 14L5.75 17.75L7.75 12.25L3.5 8.5H9.5L12 3Z"
                                    fill="white" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- User Analytics CTA Button -->
                <div>
                    <button onclick="openUserAnalyticsModal()"
                        class="w-full bg-[#1e3a8a] text-white py-3 px-4 rounded-lg text-sm font-medium hover:bg-[#1e3a8a]/90 transition-colors">
                        User Analytics
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection


<!-- User Analytics Modal -->
<div id="userAnalyticsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50"
    onclick="closeUserAnalyticsModal()">
    <div class="bg-white rounded-[20px] w-[540px] max-w-[90vw] max-h-[90vh] overflow-y-auto"
        onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-8 py-4 border-b border-[#e5e5e5] rounded-t-[20px]">
            <h2 class="text-base font-bold text-[#292d32] font-schibsted">User Details</h2>
            <button onclick="closeUserAnalyticsModal()"
                class="w-6 h-6 bg-white border border-[#8e8e8e] rounded-full flex items-center justify-center hover:bg-gray-50">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                    <path d="M9 3L3 9M3 3L9 9" stroke="#8e8e8e" stroke-width="1.2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </button>
        </div>

        <!-- Modal Content -->
        <div class="p-8 space-y-6">
            <!-- Performance Metrics -->
            <div class="grid grid-cols-3 gap-4">
                <!-- Tasks Completed -->
                <div class="bg-[#f9fafb] rounded-lg p-4 h-32">
                    <div class="h-full flex flex-col justify-between">
                        <div class="flex items-start justify-between">
                            <p class="text-xs font-semibold text-[#757575] font-schibsted">Tasks Completed</p>
                            <div class="w-6 h-6 rounded-full bg-[#70b489] flex items-center justify-center">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path d="M13.5 4.5L6 12L2.5 8.5" stroke="white" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                        </div>
                        <!-- Fixed: Changed from in_progress to completed tasks -->
                        <p class="text-2xl font-bold text-[#f4d632] font-schibsted">
                            {{ $tasks->where('status', 'Done')->count() }}</p>
                    </div>
                </div>

                <!-- Average Completion Time -->
                <div class="bg-[#f9fafb] rounded-lg p-4 h-32">
                    <div class="h-full flex flex-col justify-between">
                        <div class="flex items-start justify-between">
                            <p class="text-xs font-semibold text-[#757575] font-schibsted leading-tight">Avg.
                                Completion<br>Time</p>
                            <div class="w-7 h-7 rounded-full bg-[#70b489] flex items-center justify-center">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <circle cx="10" cy="10" r="8" fill="white" />
                                    <path d="M10 6v4l3 2" stroke="#70b489" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </div>
                        </div>
                        <!-- Fixed: Calculate actual average completion time -->
                        @php
                            $completedTasks = $tasks->where('status', 'Done');
                            $totalTime = 0;
                            $taskCount = 0;

                            foreach ($completedTasks as $task) {
                                if ($task->created_at && $task->updated_at) {
                                    $completionTime = \Carbon\Carbon::parse($task->created_at)->diffInHours(
                                        \Carbon\Carbon::parse($task->updated_at),
                                    );
                                    $totalTime += $completionTime;
                                    $taskCount++;
                                }
                            }

                            $avgTime = $taskCount > 0 ? round($totalTime / $taskCount, 1) : 0;
                        @endphp
                        <p class="text-2xl font-bold text-[#f4d632] font-schibsted">{{ $avgTime }}h</p>
                    </div>
                </div>

                <!-- On-Time Rate -->
                <div class="bg-[#f9fafb] rounded-lg p-4 h-32">
                    <div class="h-full flex flex-col justify-between">
                        <div class="flex items-start justify-between">
                            <p class="text-xs font-semibold text-[#757575] font-schibsted">On - Time Rate</p>
                            <div class="w-6 h-6 rounded-full bg-[#70b489] flex items-center justify-center">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path d="M13.5 4.5L6 12L2.5 8.5" stroke="white" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                        </div>
                        <!-- Fixed: Calculate on-time completion rate as percentage -->
                        @php

                            $completedTasks = $tasks->where('status', 'Done');
                            $onTimeTasks = 0;
                            $totalCompleted = $completedTasks->count();

                            foreach ($completedTasks as $task) {
                                if ($task->due_date && $task->updated_at) {
                                    $dueDate = \Carbon\Carbon::parse($task->due_date);
                                    $completedDate = \Carbon\Carbon::parse($task->updated_at);
                                    if ($completedDate->lte($dueDate)) {
                                        $onTimeTasks++;
                                    }
                                }
                            }

                            $onTimeRate = $totalCompleted > 0 ? round(($onTimeTasks / $totalCompleted) * 100) : 0;
                        @endphp
                        <p class="text-2xl font-bold text-[#1e3a8a] font-schibsted">{{ $onTimeRate }}%</p>
                    </div>
                </div>
            </div>

        <!-- Time Filter Buttons -->
<div class="flex items-center justify-center space-x-4">
    <button class="filter-btn bg-[#e1e1e1] text-[#717171] text-xs font-medium px-3 py-1 rounded-full transition-all duration-200 hover:bg-[#d1d1d1]"
        data-days="7">7 Days</button>
    <button class="filter-btn bg-[#e1e1e1] text-[#717171] text-xs font-medium px-3 py-1 rounded-full transition-all duration-200 hover:bg-[#d1d1d1]"
        data-days="30">30 Days</button>
      <button class="filter-btn bg-[#e1e1e1] text-[#717171] text-xs font-medium px-3 py-1 rounded-full transition-all duration-200 hover:bg-[#d1d1d1]"
        data-days="all">All</button>
</div>

<!-- Task Performance Chart -->
<div>
    <h3 class="text-xl font-semibold text-black mb-6 text-center">Task Performance</h3>
    
    <!-- Loading Indicator -->
    <div id="loadingIndicator" class="hidden flex items-center justify-center mb-4">
        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-[#6fb489]"></div>
        <span class="ml-2 text-sm text-[#8e8e8e]">Loading chart data...</span>
    </div>
    
    <!-- Error Message -->
    <div id="errorMessage" class="hidden text-center text-red-500 text-sm mb-4"></div>
    
    <!-- Chart Container -->
    <div class="relative mb-6">
        <div class="flex items-center justify-center mb-4">
            <div class="w-8"></div>
            <canvas id="taskChart" width="400" height="200"></canvas>
        </div>
        
        <!-- Legend -->
        <div class="flex items-center justify-center space-x-6 mt-4">
            <div class="flex items-center space-x-2">
                <div class="w-10 h-3 bg-[#6fb489] rounded-sm"></div>
                <span class="text-xs text-[#8e8e8e]">Completed</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-10 h-3 bg-[#f4d632] rounded-sm"></div>
                <span class="text-xs text-[#8e8e8e]">Assigned</span>
            </div>
        </div>
    </div>
</div>

           <!-- Task List Section -->
            <div>
                <h3 class="text-xl font-semibold text-black mb-4">Task List</h3>

                <!-- Loading Indicator for Tasks -->
                <div id="tasksLoadingIndicator" class="hidden flex items-center justify-center mb-4">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-[#6fb489]"></div>
                    <span class="ml-2 text-sm text-[#8e8e8e]">Loading tasks...</span>
                </div>

                <!-- Task Items Container -->
                <div id="tasksList" class="space-y-3">
                    <!-- Tasks will be loaded here via AJAX -->
                </div>

                <!-- Load More Button -->
                <div class="text-center mt-6">
                    <button id="loadMoreBtn" onclick="loadMoreTasks()" 
                        class="bg-[#e1e1e1] text-[#717171] text-sm font-medium px-6 py-2 rounded-full hover:bg-[#d1d1d1] transition-all duration-200">
                        Load More
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-8 py-4 border-t border-[#e5e5e580] rounded-b-[20px]">
            <button onclick="closeUserAnalyticsModal()"
                class="w-full bg-[#1e3a8a] text-white py-3 px-4 rounded-lg text-sm font-medium hover:bg-[#1e3a8a]/90 transition-colors">
                Chat for more information
            </button>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<script>
 
let taskChart = null;

// Chart configuration
const chartConfig = {
    type: 'line',
    data: {
        labels: [],
        datasets: [{
            label: 'Completed',
            data: [],
            borderColor: '#6fb489',
            backgroundColor: 'rgba(111, 180, 137, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#6fb489',
            pointBorderColor: '#6fb489',
            pointRadius: 4,
            pointHoverRadius: 6
        }, {
            label: 'Assigned',
            data: [],
            borderColor: '#f4d632',
            backgroundColor: 'rgba(244, 214, 50, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#f4d632',
            pointBorderColor: '#f4d632',
            pointRadius: 4,
            pointHoverRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0,0,0,0.05)'
                },
                ticks: {
                    color: '#8e8e8e',
                    font: {
                        size: 11
                    }
                }
            },
            x: {
                grid: {
                    color: 'rgba(0,0,0,0.05)'
                },
                ticks: {
                    color: '#8e8e8e',
                    font: {
                        size: 11
                    }
                }
            }
        },
        interaction: {
            intersect: false,
            mode: 'index'
        }
    }
};

// Initialize chart
function initChart() {
    const ctx = document.getElementById('taskChart').getContext('2d');
    taskChart = new Chart(ctx, chartConfig);
}

// Show loading state
function showLoading() {
    document.getElementById('loadingIndicator').classList.remove('hidden');
    document.getElementById('errorMessage').classList.add('hidden');
}

// Hide loading state
function hideLoading() {
    document.getElementById('loadingIndicator').classList.add('hidden');
}

// Show error message
function showError(message) {
    document.getElementById('errorMessage').textContent = message;
    document.getElementById('errorMessage').classList.remove('hidden');
    hideLoading();
}

// Fetch chart data via AJAX
async function fetchChartData(days = 7) {
    showLoading();
    
    try {
        const response = await fetch('{{route('user.getTaskPerformance')}}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                days: days,
                _token: "{{csrf_token()}}",
                user_id: window.currentUserId // Assume this is set globally
            })
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        
        if (data.success) {
            updateChart(data.data);
        } else {
            throw new Error(data.message || 'Failed to fetch chart data');
        }
    } catch (error) {
        console.error('Error fetching chart data:', error);
        showError('Failed to load chart data. Please try again.');
    } finally {
        hideLoading();
    }
}

// Update chart with new data
function updateChart(data) {
    if (!taskChart) return;
    
    taskChart.data.labels = data.labels;
    taskChart.data.datasets[0].data = data.completed;
    taskChart.data.datasets[1].data = data.assigned;
    taskChart.update('active');
}

// Handle filter button clicks
function setupFilterButtons() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active state from all buttons
            filterButtons.forEach(btn => {
                btn.classList.remove('bg-[#6fb489]', 'text-white');
                btn.classList.add('bg-[#e1e1e1]', 'text-[#717171]');
            });
            
            // Add active state to clicked button
            this.classList.remove('bg-[#e1e1e1]', 'text-[#717171]');
            this.classList.add('bg-[#6fb489]', 'text-white');
            
            // Fetch new data
            const days = parseInt(this.getAttribute('data-days'));
            fetchChartData(days);
        });
    });
    
    // Set default active button (7 days)
    filterButtons[0].classList.remove('bg-[#e1e1e1]', 'text-[#717171]');
    filterButtons[0].classList.add('bg-[#6fb489]', 'text-white');
}

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initChart();
    setupFilterButtons();
    fetchChartData(7); // Load initial data
});

    // Modal functions (you'll need to implement these)
    function closeUserAnalyticsModal() {
        document.getElementById('userAnalyticsModal').style.display = 'none';
    }

    function openUserAnalyticsModal() {
        document.getElementById('userAnalyticsModal').style.display = 'flex';
    }

    function openUserAnalyticsModal() {
        const modal = document.getElementById('userAnalyticsModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeUserAnalyticsModal() {
        const modal = document.getElementById('userAnalyticsModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    // Close modal on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeUserAnalyticsModal();
        }
    });

    

    // Task loading variables
let currentTaskPage = 1;
let isLoadingTasks = false;
let hasMoreTasks = true;

// Load tasks function
async function loadTasks(page = 1) {
    if (isLoadingTasks) return;
    
    isLoadingTasks = true;
    document.getElementById('tasksLoadingIndicator').classList.remove('hidden');
    
    try {
        const response = await fetch('{{ route('user.loadusertasks') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                user_id: '{{ $data->user_id }}',
                page: page,
                _token: "{{ csrf_token() }}"
            })
        });

        const data = await response.json();
        
        if (data.success) {
            displayTasks(data.tasks, page === 1);
            hasMoreTasks = data.has_more;
            currentTaskPage = data.current_page;
            
            // Show/hide load more button
            document.getElementById('loadMoreBtn').style.display = hasMoreTasks ? 'block' : 'none';
        }
    } catch (error) {
        console.error('Error loading tasks:', error);
    } finally {
        isLoadingTasks = false;
        document.getElementById('tasksLoadingIndicator').classList.add('hidden');
    }
}
// Updated displayTasks function with new card design
function displayTasks(tasks, clearExisting = false) {
    const tasksList = document.getElementById('tasksList');
    
    if (clearExisting) {
        tasksList.innerHTML = '';
    }
    
    tasks.forEach(task => {
        const statusConfig = getStatusConfig(task.status);
        const dueDate = task.due_date ? new Date(task.due_date).toLocaleDateString() : 'No due date';
        
        const taskHTML = `
            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between mb-2">
                    <h4 class="text-sm font-medium text-gray-900">${task.title}</h4>
                    <span class="${statusConfig.class}">${statusConfig.text}</span>
                </div>
                <p class="text-xs text-gray-600 mb-3 leading-relaxed">${task.description || 'No description available'}</p>
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <div class="flex items-center gap-4">
                        <span class="flex items-center gap-1">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                <path d="M6 1v5l3 2" stroke="currentColor" stroke-width="1" stroke-linecap="round"/>
                                <circle cx="6" cy="6" r="5" stroke="currentColor" stroke-width="1" fill="none"/>
                            </svg>
                            ${dueDate}
                        </span>
                        <span class="flex items-center gap-1">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                <rect x="2" y="3" width="8" height="6" rx="1" stroke="currentColor" stroke-width="1" fill="none"/>
                                <path d="M4 1v4M8 1v4" stroke="currentColor" stroke-width="1" stroke-linecap="round"/>
                            </svg>
                            ${task.project_name || 'No Project'}
                        </span>
                    </div>
                    <div class="w-2 h-2 rounded-full ${getStatusDot(task.status)}"></div>
                </div>
            </div>
        `;
        
        tasksList.insertAdjacentHTML('beforeend', taskHTML);
    });
}

// Helper function for status dots
function getStatusDot(status) {
    const dotColors = {
        'Done': 'bg-green-500',
        'In Progress': 'bg-yellow-500', 
        'Pending': 'bg-orange-500',
        'Todo': 'bg-gray-400'
    };
    return dotColors[status] || 'bg-gray-400';
}

// Updated status config with better sizing
function getStatusConfig(status) {
    const configs = {
        'Done': {
            class: 'bg-[#72c364] text-white text-xs px-3 py-1 rounded-full',
            text: 'Completed'
        },
        'In Progress': {
            class: 'bg-[#f4d632] text-black text-xs px-3 py-1 rounded-full',
            text: 'In Progress'
        },
        'Pending': {
            class: 'bg-[#ff8a6b] text-white text-xs px-3 py-1 rounded-full',
            text: 'Pending'
        },
        'Todo': {
            class: 'bg-[#e1e1e1] text-black text-xs px-3 py-1 rounded-full',
            text: 'To Do'
        }
    };
    
    return configs[status] || {
        class: 'bg-[#e1e1e1] text-black text-xs px-3 py-1 rounded-full',
        text: status || 'Unknown'
    };
}

// Load more tasks
function loadMoreTasks() {
    if (!hasMoreTasks || isLoadingTasks) return;
    loadTasks(currentTaskPage + 1);
}

// Load tasks when modal opens
function openUserAnalyticsModal() {
    const modal = document.getElementById('userAnalyticsModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
    
    // Reset and load tasks
    currentTaskPage = 1;
    hasMoreTasks = true;
    loadTasks(1);
}
</script>
