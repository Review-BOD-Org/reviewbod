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
            <div class="bg-white rounded-lg shadow-lg p-6 w-[400px] border border-gray-100 h-[600px]"
                style="overflow-y: scroll">
                <h3 class="mb-3 h3 text-bold">User Teams <i class="fa fa-users"></i></h3>
                @foreach ($teams as $t)
                    <!-- Personal Information Card -->
                    <div class="bg-white border border-black/15 rounded shadow-sm p-4 mb-6">

                        <!-- Full Name -->
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-gray-900 opacity-90">{{ $t->name }}</p>
                            </div>

                        </div>


                    </div>
                @endforeach





            </div>

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
                    @if($manager)
 <button onclick="openManagers()"
                        class="bg-[#cb964f] text-white flex gap-3 items-center  px-4 py-2 rounded-full text-xs font-medium">
                        <i class="fa fa-eye"></i>
                        View Manager
                    </button>
                    @else
                    <button onclick="openManagers()"
                        class="bg-[#F0EFFA] flex gap-3 items-center hover:bg-[#E8E7F5] px-4 py-2 rounded-full text-xs font-medium text-gray-700 transition-colors">
                        <i class="fa fa-plus"></i>
                        Add Manager
                    </button>
                    @endif
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
                    <h4 class="text-sm font-medium text-gray-900 mb-4">Linked App</h4>
                    <div class="space-y-3">

                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-900 opacity-90 capitalize">{{ $data->source }}</span>
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
    <div class="bg-white rounded-[20px] w-[740px] max-w-[90vw] max-h-[90vh] overflow-y-auto"
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
                <button
                    class="filter-btn bg-[#e1e1e1] text-[#717171] text-xs font-medium px-3 py-1 rounded-full transition-all duration-200 hover:bg-[#d1d1d1]"
                    data-days="7">7 Days</button>
                <button
                    class="filter-btn bg-[#e1e1e1] text-[#717171] text-xs font-medium px-3 py-1 rounded-full transition-all duration-200 hover:bg-[#d1d1d1]"
                    data-days="30">30 Days</button>
                <button
                    class="filter-btn bg-[#e1e1e1] text-[#717171] text-xs font-medium px-3 py-1 rounded-full transition-all duration-200 hover:bg-[#d1d1d1]"
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
                <h3 class="text-xl font-semibold text-black mb-4">Insights for this user</h3>


                <div class="w-full bg-[#F1F1F1] p-5 rounded-lg">
                    <div class="flex justify-between">

                        <div class="bg-white flex items-center p-2 gap-3 w-[170px]  justify-center rounded-md">
                            <svg width="20" height="20" viewBox="0 0 12 11" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7.58342 1.4043C8.31261 1.4043 8.90373 0.813171 8.90373 0.0839844H9.5131C9.5131 0.813171 10.1042 1.4043 10.8334 1.4043V2.01367C10.1042 2.01367 9.5131 2.6048 9.5131 3.33398H8.90373C8.90373 2.6048 8.31261 2.01367 7.58342 2.01367V1.4043ZM0.541748 4.95898C2.33667 4.95898 3.79175 3.50391 3.79175 1.70898H4.87508C4.87508 3.50391 6.33016 4.95898 8.12508 4.95898V6.04232C6.33016 6.04232 4.87508 7.4974 4.87508 9.29232H3.79175C3.79175 7.4974 2.33667 6.04232 0.541748 6.04232V4.95898ZM9.34383 6.58398C9.34383 7.55622 8.55565 8.3444 7.58342 8.3444V9.1569C8.55565 9.1569 9.34383 9.94508 9.34383 10.9173H10.1563C10.1563 9.94508 10.9445 9.1569 11.9167 9.1569V8.3444C10.9445 8.3444 10.1563 7.55622 10.1563 6.58398H9.34383Z"
                                    fill="#535754" />
                            </svg>

                            <span>User Summary</span>
                        </div>

                        <a href="javascript:;" class="" onclick="$('#content-ai').toggle()">
                            <svg width="20" height="20" viewBox="0 0 12 8" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M6.00004 4.97633L10.1249 0.851562L11.3034 2.03007L6.00004 7.33341L0.696777 2.03007L1.87529 0.851562L6.00004 4.97633Z"
                                    fill="#585858" />
                            </svg>

                        </a>
                    </div>


                    <div class="mt-5 flex flex-col gap-7" id="content-ai">

                        <p class="text-center">Loading...</p>

                    </div>
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

<!-- User Analytics Modal -->
<div id="managersmodal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50"
    onclick="closemanagersmodal()">
    <div class="bg-white rounded-[20px] w-[740px] max-w-[90vw] max-h-[90vh] overflow-y-auto"
        onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-8 py-4 border-b border-[#e5e5e5] rounded-t-[20px]">
            <h2 class="text-base font-bold text-[#292d32] font-schibsted">Add Manager</h2>
            <button onclick="closemanagersmodal()"
                class="w-6 h-6 bg-white border border-[#8e8e8e] rounded-full flex items-center justify-center hover:bg-gray-50">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                    <path d="M9 3L3 9M3 3L9 9" stroke="#8e8e8e" stroke-width="1.2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </button>
        </div>

        <!-- Modal Content -->
        <div class="p-8 space-y-6">
            <!-- Search Input -->
            <div class="relative">
                <input type="text" placeholder="Search managers..." id="search"
                    class="w-full h-14 px-6 text-lg bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-gray-500">
                <svg class="absolute right-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <!-- Managers List -->
            <div class="space-y-4" id="managers">

                @foreach (DB::table('managers')->where(['userid' => Auth::user()->id])->get() as $m)
                    <!-- Manager 1 -->
                    <div
                        class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                        <div class="flex items-center space-x-4">
                            <img src="{{ $m->image }}" alt="John Smith"
                                class="w-12 h-12 rounded-full object-cover">
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $m->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $m->email }}</p>
                            </div>
                        </div>
                        <button id="assign-{{$m->id}}" onclick="assign_manager({{ $m->id }})"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            Assign Manager
                        </button>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<script>

    
    @php
        $key = hex2bin(env('SODIUM_KEY')); // 32-byte key from env
        $nonceUser = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $nonceChat = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

        $encryptedUser = sodium_crypto_secretbox(Auth::id(), $nonceUser, $key);

        // combine nonce + ciphertext then base64 encode for safe JS embedding
        $user_id_encrypted = base64_encode($nonceUser . $encryptedUser);
    @endphp

    let taskChart = null;

    function getRandomId() {
        return Math.floor(100000000 + Math.random() * 900000000);
    }
    var id = getRandomId();
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
            const response = await fetch('{{ route('user.getTaskPerformance') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                        'content') || ''
                },
                body: JSON.stringify({
                    days: days,
                    _token: "{{ csrf_token() }}",
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

        $(document).ready(function() {
    // Manager search functionality
    $('#search').on('keyup', function() {
        var searchText = $(this).val().toLowerCase().trim();
        
        // Get all manager cards
        $('#managers > div').each(function() {
            var $managerCard = $(this);
            var managerName = $managerCard.find('h3').text().toLowerCase();
            var managerEmail = $managerCard.find('p').text().toLowerCase();
            
            // Check if search text matches name or email
            if (managerName.includes(searchText) || managerEmail.includes(searchText)) {
                $managerCard.show();
            } else {
                $managerCard.hide();
            }
        });
        
        // Show "No results found" message if no managers are visible
        var visibleManagers = $('#managers > div:visible').length;
        
        // Remove existing no results message
        $('#no-results-message').remove();
        
        if (visibleManagers === 0 && searchText !== '') {
            $('#managers').append(
                '<div id="no-results-message" class="text-center py-8 text-gray-500">' +
                    '<svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">' +
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />' +
                    '</svg>' +
                    '<h3 class="text-sm font-medium text-gray-900 mb-1">No managers found</h3>' +
                    '<p class="text-sm text-gray-500">Try adjusting your search terms.</p>' +
                '</div>'
            );
        }
    });
    
    // Clear search when modal is opened
    $('#managersmodal').on('shown', function() {
        $('#search').val('').trigger('keyup');
    });
    
    // Alternative: If you want real-time search with minimal delay
    var searchTimeout;
    $('#search').on('input', function() {
        clearTimeout(searchTimeout);
        var $input = $(this);
        
        searchTimeout = setTimeout(function() {
            $input.trigger('keyup');
        }, 150); // 150ms delay for better performance
    });
});
    });

    // Modal functions (you'll need to implement these)
    function closeUserAnalyticsModal() {
        document.getElementById('userAnalyticsModal').style.display = 'none';
    }

    function openUserAnalyticsModal() {
        document.getElementById('userAnalyticsModal').style.display = 'flex';
    }

    function openManagers() {
        document.getElementById('managersmodal').style.display = 'flex';
    }

    function closemanagersmodal() {
      $("#managersmodal").hide()
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
        // document.getElementById('tasksLoadingIndicator').classList.remove('hidden');

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

    function stopStream() {
        if (ws.readyState === WebSocket.OPEN && isStreaming) {
            // Close the WebSocket connection to stop streaming
            ws.close();

            // Reset streaming state
            isStreaming = false;
            currentStreamId = null;
            updateSendButton('ready');

            // Clear any accumulated data from the stopped stream
            accumulatedMarkdown = '';
            processedTemplates.clear();
            templatePlaceholders.clear();

            // Reconnect after a short delay
            setTimeout(() => {
                var id = getRandomId()
                initstream();
            }, 100);
        }
    }


    accumulatedMarkdown = '';

    function initstream() {
        // Add variables to track streaming state

        id = getRandomId()
        ws = new WebSocket('wss://api.reviewbod.com/ws');

        ws.onopen = () => {
            console.log('Connected to WebSocket');
            const message = {
                query: "Give analysis base on this user, and avoid starting with hey or hello, just give insight on this user",
                user_id: @json($user_id_encrypted),
                staff_id: "{{ $data->email ? $data->email : $data->user_id }}",
            };

            ws.send(JSON.stringify(message));
        };

        ws.onmessage = (event) => {
            const data = JSON.parse(event.data);
            console.log('Received:', data);

            const responseDiv = document.getElementById(`content-ai`);

            switch (data.type) {
                case 'stream_token':


                    accumulatedMarkdown += data.token;
                    let displayContent = accumulatedMarkdown;
                    responseDiv.innerHTML = marked.parse(displayContent);
                    // autoScroll();

                    break;

                case 'error':
                    //responseDiv.innerHTML += `<p><b>ERROR</b>: ${data.message}</p>`;
                    isStreaming = false;
                    currentStreamId = null;
                    break;
                case 'new_chat_created':



                    chatListContainer.prepend(chatItem);


                    // Start sidebar update interval after first message


                    break;

                case 'sql_query':
                    responseDiv.innerHTML += `
            <div class="flex gap-3 items-center">
                <svg width="30" height="30" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8.81896 13.3258V12.0318C8.87496 11.5718 9.28496 11.2638 9.74796 11.2638H10.303C10.7008 11.2638 11.0823 11.1058 11.3636 10.8245C11.6449 10.5432 11.803 10.1617 11.803 9.76383V8.28783C12.39 8.31083 12.69 8.26183 13.042 8.04083C13.374 7.83083 13.486 7.40583 13.372 7.02883C12.34 3.61183 10.73 0.173828 6.13296 0.173828C2.61296 0.173828 0.584961 1.97983 0.584961 5.58783C0.584961 8.07583 1.23996 8.89583 2.02896 10.1758C2.44196 10.8468 2.61796 11.6298 2.61596 12.4188L2.61396 13.3258C2.61396 13.4584 2.66664 13.5856 2.76041 13.6794C2.85418 13.7731 2.98135 13.8258 3.11396 13.8258H8.31896C8.45157 13.8258 8.57875 13.7731 8.67251 13.6794C8.76628 13.5856 8.81896 13.4584 8.81896 13.3258Z" fill="#8FBFFA"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M6.26557 2.63672H6.67157C6.82356 2.63731 6.97188 2.68345 7.09738 2.7692C7.22287 2.85494 7.31978 2.97634 7.37557 3.11772L7.56157 3.59072L8.12657 3.91672L8.62757 3.84072C8.77822 3.81812 8.93218 3.84132 9.06948 3.90729C9.20678 3.97327 9.32109 4.07898 9.39757 4.21072L9.60057 4.56172C9.67653 4.69379 9.71104 4.84564 9.69963 4.99756C9.68822 5.14949 9.63141 5.29448 9.53657 5.41372L9.22057 5.80972V6.46372L9.53457 6.85972C9.6292 6.97886 9.68588 7.12367 9.69729 7.27539C9.7087 7.42711 9.67431 7.57876 9.59857 7.71072L9.39557 8.06072C9.31937 8.19282 9.20518 8.29895 9.06786 8.36529C8.93053 8.43163 8.77643 8.45513 8.62557 8.43272L8.12557 8.35672L7.55957 8.68272L7.37457 9.15472C7.31893 9.29628 7.2221 9.4179 7.09659 9.50383C6.97108 9.58976 6.82268 9.63605 6.67057 9.63672H6.26457C6.1123 9.63625 5.96367 9.59005 5.83797 9.50411C5.71226 9.41817 5.61527 9.29644 5.55957 9.15472L5.37457 8.68272L4.80857 8.35672L4.30857 8.43272C4.15793 8.45532 4.00397 8.43212 3.86667 8.36615C3.72936 8.30017 3.61506 8.19446 3.53857 8.06272L3.33557 7.71072C3.25983 7.57876 3.22544 7.42711 3.23685 7.27539C3.24826 7.12367 3.30495 6.97886 3.39957 6.85972L3.71557 6.46372V5.80972L3.39957 5.41372C3.30473 5.29448 3.24792 5.14949 3.23651 4.99756C3.2251 4.84564 3.25961 4.69379 3.33557 4.56172L3.53857 4.21172C3.61485 4.07997 3.72894 3.97417 3.86605 3.90802C4.00316 3.84187 4.15698 3.81843 4.30757 3.84072L4.80757 3.91672L5.37457 3.58772L5.56057 3.11772C5.61643 2.97618 5.71349 2.85467 5.83918 2.76892C5.96487 2.68316 6.11341 2.63711 6.26557 2.63672ZM6.46857 4.94672C6.75257 4.94672 7.05157 5.02972 7.27157 5.21272C7.52157 5.42172 7.63957 5.74872 7.65557 6.05972C7.67157 6.37172 7.58857 6.70672 7.38557 6.94572C7.16057 7.20972 6.80257 7.32572 6.46857 7.32572C6.13557 7.32572 5.77657 7.20972 5.55157 6.94572C5.34857 6.70672 5.26557 6.37172 5.28157 6.05972C5.29857 5.74972 5.41657 5.42172 5.66657 5.21272C5.88557 5.02972 6.18457 4.94672 6.46857 4.94672Z" fill="#2859C5"/>
                </svg>
                <span>Thinking..</span>
            </div>`;
                    // autoScroll();

                    break;

                case 'db_results':
                    responseDiv.innerHTML += `
            <div class="flex gap-3 items-center">
                <svg width="30" height="30" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8.81896 13.3258V12.0318C8.87496 11.5718 9.28496 11.2638 9.74796 11.2638H10.303C10.7008 11.2638 11.0823 11.1058 11.3636 10.8245C11.6449 10.5432 11.803 10.1617 11.803 9.76383V8.28783C12.39 8.31083 12.69 8.26183 13.042 8.04083C13.374 7.83083 13.486 7.40583 13.372 7.02883C12.34 3.61183 10.73 0.173828 6.13296 0.173828C2.61296 0.173828 0.584961 1.97983 0.584961 5.58783C0.584961 8.07583 1.23996 8.89583 2.02896 10.1758C2.44196 10.8468 2.61796 11.6298 2.61596 12.4188L2.61396 13.3258C2.61396 13.4584 2.66664 13.5856 2.76041 13.6794C2.85418 13.7731 2.98135 13.8258 3.11396 13.8258H8.31896C8.45157 13.8258 8.57875 13.7731 8.67251 13.6794C8.76628 13.5856 8.81896 13.4584 8.81896 13.3258Z" fill="#8FBFFA"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M6.26557 2.63672H6.67157C6.82356 2.63731 6.97188 2.68345 7.09738 2.7692C7.22287 2.85494 7.31978 2.97634 7.37557 3.11772L7.56157 3.59072L8.12657 3.91672L8.62757 3.84072C8.77822 3.81812 8.93218 3.84132 9.06948 3.90729C9.20678 3.97327 9.32109 4.07898 9.39757 4.21072L9.60057 4.56172C9.67653 4.69379 9.71104 4.84564 9.69963 4.99756C9.68822 5.14949 9.63141 5.29448 9.53657 5.41372L9.22057 5.80972V6.46372L9.53457 6.85972C9.6292 6.97886 9.68588 7.12367 9.69729 7.27539C9.7087 7.42711 9.67431 7.57876 9.59857 7.71072L9.39557 8.06072C9.31937 8.19282 9.20518 8.29895 9.06786 8.36529C8.93053 8.43163 8.77643 8.45513 8.62557 8.43272L8.12557 8.35672L7.55957 8.68272L7.37457 9.15472C7.31893 9.29628 7.2221 9.4179 7.09659 9.50383C6.97108 9.58976 6.82268 9.63605 6.67057 9.63672H6.26457C6.1123 9.63625 5.96367 9.59005 5.83797 9.50411C5.71226 9.41817 5.61527 9.29644 5.55957 9.15472L5.37457 8.68272L4.80857 8.35672L4.30857 8.43272C4.15793 8.45532 4.00397 8.43212 3.86667 8.36615C3.72936 8.30017 3.61506 8.19446 3.53857 8.06272L3.33557 7.71072C3.25983 7.57876 3.22544 7.42711 3.23685 7.27539C3.24826 7.12367 3.30495 6.97886 3.39957 6.85972L3.71557 6.46372V5.80972L3.39957 5.41372C3.30473 5.29448 3.24792 5.14949 3.23651 4.99756C3.2251 4.84564 3.25961 4.69379 3.33557 4.56172L3.53857 4.21172C3.61485 4.07997 3.72894 3.97417 3.86605 3.90802C4.00316 3.84187 4.15698 3.81843 4.30757 3.84072L4.80757 3.91672L5.37457 3.58772L5.56057 3.11772C5.61643 2.97618 5.71349 2.85467 5.83918 2.76892C5.96487 2.68316 6.11341 2.63711 6.26557 2.63672ZM6.46857 4.94672C6.75257 4.94672 7.05157 5.02972 7.27157 5.21272C7.52157 5.42172 7.63957 5.74872 7.65557 6.05972C7.67157 6.37172 7.58857 6.70672 7.38557 6.94572C7.16057 7.20972 6.80257 7.32572 6.46857 7.32572C6.13557 7.32572 5.77657 7.20972 5.55157 6.94572C5.34857 6.70672 5.26557 6.37172 5.28157 6.05972C5.29857 5.74972 5.41657 5.42172 5.66657 5.21272C5.88557 5.02972 6.18457 4.94672 6.46857 4.94672Z" fill="#2859C5"/>
                </svg>
                <span>Thinking..</span>
            </div>`;
                    // autoScroll();

                    break;
                    // Replace your existing 'visualizations' case with this improved version:


                case 'classification':
                    // responseDiv.innerHTML += `<p><b>CLASSIFICATION</b>: ${data.data}</p>`;
                    break;

                case 'stream_end':


                    break;
                default:
                    responseDiv.innerHTML += `<p>${JSON.stringify(data, null, 2)}</p>`;
            }
        };




        ws.onclose = (data) => {
            console.log('Disconnected:', data);
            toastr.error("Network Error")

        };

        ws.onerror = (error) => {
            console.error('WebSocket error:', error);
            toastr.error("Connection Error")
        };
    }

    function getCurrentTimestamp() {
        const now = new Date();
        const day = now.getDate();
        const month = now.toLocaleDateString('en-US', {
            month: 'short'
        });
        const time = now.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
        return `${day} ${month} ▪ ${time}`;
    }


    document.addEventListener('DOMContentLoaded', function() {

        initstream();




    })

    async function assign_manager(id) {
        if (confirm("Are you sure?")) {
                $(`#assign-${id}`).html("Loading...")
            $("button").attr("disabled", true)
            try {
                const response = await fetch('{{ route('user.user_manager') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                            'content') || ''
                    },
                    body: JSON.stringify({
                        id: "{{ $data->id }}",
                        manager_id: id,
                        _token: "{{ csrf_token() }}"
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    toastr.success(data.message)
                    location.reload()
                } else {
                    toastr.error(data.message)
                }
            } catch (error) {
                console.error('Error fetching chart data:', error);
                showError('Failed to load chart data. Please try again.');
            } finally {
                // hideLoading();
                $(`#assign-${id}`).html("Assign Manager")
                $("button").attr("disabled", false)
            }
        }
    }

    
</script>
