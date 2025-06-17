<!-- views/dash/members.blade.php -->
@extends('dash.layouts.app')

@section('page-title', 'Settings')

@section('content')
    <div class="container mx-auto px-[50px] py-6 bg-white h-screen">
        <!-- Tab Navigation -->
        <div class="border-b border-gray-200 mb-6">
            <ul class="flex mb-px" id="tabNav">
                <li class="mr-1">
                    <a href="?type="
                        class="inline-block py-4 px-4 @if (request()->type == '') border-b-2 border-[#1E3A8A] @endif font-medium text-sm text-[#1E3A8A] "
                        data-tab="personal">Profile</a>
                </li>
                <li class="mr-1">
                    <a href="?type=Calendar"
                        class="inline-block @if (request()->type == 'Calendar') border-b-2 border-[#1E3A8A] @endif  py-4 px-4 text-gray-500 hover:text-gray-700 font-medium text-sm "
                        data-tab="general">Calendar</a>
                </li>
                <li class="mr-1">
                    <a href="?type=Analytics"
                        class="inline-block  @if (request()->type == 'Analytics') border-b-2 border-[#1E3A8A] @endif  py-4 px-4 text-gray-500 hover:text-gray-700 font-medium text-sm "
                        data-tab="company">Analytics</a>
                </li>
                <li class="mr-1">
                    <a href="?type=Chat"
                        class="inline-block  @if (request()->type == 'Chat') border-b-2 border-[#1E3A8A] @endif  py-4 px-4 text-gray-500 hover:text-gray-700 font-medium text-sm "
                        data-tab="company">Review Bot</a>
                </li>
                <li class="mr-1">
                    <a href="?type=Manage"
                        class="inline-block  @if (request()->type == 'Manage') border-b-2 border-[#1E3A8A] @endif  py-4 px-4 text-gray-500 hover:text-gray-700 font-medium text-sm "
                        data-tab="company">Manage user</a>
                </li>
            </ul>
        </div>

        <!-- Tab Content Areas -->
        <div class="tab-content-container">

            @if (request()->type == '')
                <!-- Personal Tab Content -->
                <div id="personal" class="tab-content block">
                    <h2 class="text-lg font-medium mb-4">Personal Content</h2>

                    <div class="flex flex-col md:flex-row gap-6">
                        <!-- Personal Details Section -->
                        <div class="w-full md:w-[50%] border rounded-md">
                            <div class="bg-[#FAFAFA] w-full p-3 flex items-center gap-3 rounded-t-lg">
                                <img src="/image.png" width="40">
                                <h3 class="text-bold text-[20px]">Personal Details</h3>
                            </div>

                            <div class="flex flex-col px-4 gap-3">
                                <div class="flex justify-between p-4 border-b">
                                    <span class="text-bold">Full Name</span>
                                    <span class="text-[#1E3A8A] text-medium" >{{ $data['user']['displayName'] }}</span>
                                </div>

                            @if( $data['user']['email'])
                                <div class="flex justify-between p-4 border-b">
                                    <span class="text-bold">Email</span>
                                    <span class="text-[#1E3A8A] text-medium">{{ $data['user']['email'] }}</span>
                                </div>
                                @endif

                                <div class="flex justify-between p-4 border-b">
                                    <span class="text-bold">User ID</span>
                                    <span class="text-[#1E3A8A] text-medium">{{ $data['user']['id'] }}</span>
                                </div>

                                <div class="flex justify-between p-4 border-b">
                                    <span class="text-bold">Last Seen</span>
                                    <span
                                        class="text-[#1E3A8A] text-medium">{{ \Carbon\Carbon::parse($data['user']['lastSeen'])->format('Y M D H:i:s') }}</span>
                                </div>

                                @if( $data['user']['timezone'])
                                <div class="flex justify-between p-4 border-b">
                                    <span class="text-bold">Timezone</span>
                                    <span class="text-[#1E3A8A] text-medium">{{ $data['user']['timezone'] }}</span>
                                </div>
                                @endif

                                <div class="flex justify-between p-4 border-b">
                                    <span class="text-bold">UpComing Schedule</span>
                                    <span class="text-[#1E3A8A] text-medium flex flex-col gap-1">
                                        @foreach ($upcomingTasks as $task)
                                            <span><b>Task Title:</b> {{ $task['title'] }}</span>
                                            <span><b>Due Date:</b>
                                                {{ \Carbon\Carbon::parse($task['dueDate'])->format('Y M D h:i:s') }}</span>

                                            @php
                                                // Format the priority
                                                $priorityLabel = '';
                                                switch ($task['priority']) {
                                                    case 1:
                                                        $priorityLabel = 'High';
                                                        break;
                                                    case 2:
                                                        $priorityLabel = 'Medium';
                                                        break;
                                                    case 3:
                                                        $priorityLabel = 'Low';
                                                        break;
                                                    default:
                                                        $priorityLabel = 'Unknown';
                                                        break;
                                                }
                                            @endphp

                                            <span><b>Priority:</b> {{ $priorityLabel }}</span>
                                            <br>
                                        @endforeach
                                    </span>

                                </div>
                            </div>
                        </div>

                        <!-- Linked Accounts Section -->
                        <div class="w-full md:w-[50%] border rounded-md">
                            <div class="bg-[#FAFAFA] w-full p-4 flex items-center gap-3 rounded-t-lg">

                                <h3 class="text-bold text-[20px]">Linked Accounts</h3>
                            </div>

                            <div class="p-4">
                                <h4 class="text-lg font-medium mb-4">Workspaces Integration</h4>

                                <div class="mb-6">
                                    <div class="flex items-center justify-between p-4 border-b">
                                        <div class="flex items-center gap-3">
                                            <div class="w-12 h-12 rounded-full flex items-center justify-center">
                                               @if(Auth::user()->service == "trello")
                                               <img src="/images/trello.webp">
                                               @elseif(Auth::user()->service == "linear")
                                               <img src="/images/linear.webp">
                                               @endif
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900 capitalize">{{Auth::user()->service}}</p>
                                                {{-- <p class="text-sm text-gray-500">Review-Bot</p> --}}
                                            </div>
                                        </div>
                                        <span class="text-[#1E3A8A] text-medium">{{ $data['user']['email'] }}</span>
                                    </div>

                                    {{-- <div class="flex items-center justify-between p-4 border-b">
                                        <div class="flex items-center gap-3">
                                            <div class="w-12 h-12   flex items-center justify-center">
                                                <img src="/images/slack.webp">
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">Slack</p>
                                                <p class="text-sm text-gray-500">Review-Bot</p>
                                            </div>
                                        </div>
                                        <div>
                                            <button
                                                class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-md">
                                                Link Account
                                            </button>
                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (request()->type == 'Calendar')
                <!-- General Tab Content -->
                <div id="general" class="tab-content">
                    @include('dash.user.calendar')
                </div>
            @endif

            @if (request()->type == 'Analytics')
                <!-- Company Tab Content -->
                <div id="company" class="tab-content">
                    @include('dash.user.analytics')
                </div>
            @endif

            @if (request()->type == 'Chat')
            <!-- Company Tab Content -->
            <div id="company" class="tab-content">
                @include('dash.user.chat')
            </div>
        @endif


            @if (request()->type == 'Manage')
                <!-- Company Tab Content -->
                <div id="company" class="tab-content">
                    @include('dash.user.edit')
                </div>
            @endif

        </div>
    </div>


    <script>
        // This JavaScript will handle calendar rendering and Linear data integration

        class CalendarApp {
            constructor() {
                this.currentYear = {{date('Y')}};
                this.linearData = null;
                this.issuesByDate = {};

                // Initialize the calendar
                this.initCalendar();

                // Add event listeners
                document.querySelector('#left-nav').addEventListener('click', () => this.navigateYear(-1));
                document.querySelector('#right-nav').addEventListener('click', () => this.navigateYear(1));
            }

            initCalendar() {
                console.log(this.currentYear)
                // Update the year title
                document.querySelector('.year-title').textContent = this.currentYear;

                // Generate all months
                const monthsGrid = document.querySelector('.months-grid');
                monthsGrid.innerHTML = ''; // Clear existing months

                const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

                // Create 12 months (4 rows of 3)
                for (let i = 0; i < 12; i++) {
                    const monthCard = this.generateMonthCard(i, monthNames[i], this.currentYear);
                    monthsGrid.appendChild(monthCard);
                }

                // After generating the calendar, apply the Linear data
                this.applyLinearDataToCalendar();
            }

            generateMonthCard(monthIndex, monthName, year) {
                const monthCard = document.createElement('div');
                monthCard.className = 'month-card';

                // Get first day of month and total days
                const firstDay = new Date(year, monthIndex, 1);
                const lastDay = new Date(year, monthIndex + 1, 0);
                const daysInMonth = lastDay.getDate();

                // Get day of week of first day (0 = Sunday, 1 = Monday, etc.)
                // Adjust for Monday as first day of week
                let firstDayOfWeek = firstDay.getDay() - 1;
                if (firstDayOfWeek < 0) firstDayOfWeek = 6; // Sunday becomes last day

                // Create month header
                const monthHeader = document.createElement('div');
                monthHeader.className = 'month-header';
                monthHeader.textContent = monthName;
                monthCard.appendChild(monthHeader);

                // Create table
                const table = document.createElement('table');

                // Create table header
                const thead = document.createElement('thead');
                const headerRow = document.createElement('tr');
                const weekdays = ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'];

                weekdays.forEach(day => {
                    const th = document.createElement('th');
                    th.textContent = day;
                    headerRow.appendChild(th);
                });

                thead.appendChild(headerRow);
                table.appendChild(thead);

                // Create table body
                const tbody = document.createElement('tbody');

                // Create calendar days
                let date = 1;

                // Up to 6 rows might be needed for a month
                for (let i = 0; i < 6; i++) {
                    // Stop if we've used all days in the month
                    if (date > daysInMonth) break;

                    const row = document.createElement('tr');

                    // Create 7 cells for each day of the week
                    for (let j = 0; j < 7; j++) {
                        const cell = document.createElement('td');

                        // Fill in previous month days
                        if (i === 0 && j < firstDayOfWeek) {
                            const prevMonthLastDay = new Date(year, monthIndex, 0).getDate();
                            cell.textContent = prevMonthLastDay - (firstDayOfWeek - j - 1);
                            cell.className = 'other-month';
                        }
                        // Fill in current month days
                        else if (date <= daysInMonth) {
                            cell.textContent = date;

                            // Add data attribute for issue display
                            const fullDate =
                                `${year}-${(monthIndex + 1).toString().padStart(2, '0')}-${date.toString().padStart(2, '0')}`;
                            cell.dataset.date = fullDate;

                            // Add click event for showing issues
                            cell.addEventListener('click', () => this.showIssuesForDate(fullDate));

                            date++;
                        }
                        // Fill in next month days
                        else {
                            cell.textContent = date - daysInMonth;
                            cell.className = 'other-month';
                            date++;
                        }

                        row.appendChild(cell);
                    }

                    tbody.appendChild(row);
                }

                table.appendChild(tbody);
                monthCard.appendChild(table);

                return monthCard;
            }

            navigateYear(increment) {
                this.currentYear += increment;
                this.initCalendar();
            }

            setLinearData(data) {
                this.linearData = data;
                this.processLinearData();
                this.applyLinearDataToCalendar();
            }

            processLinearData() {
            // Reset issues by date
            this.issuesByDate = {};

            if (!this.linearData || !this.linearData.user || !this.linearData.user.assignedIssues) {
                return;
            }

            // Process assigned issues
            const assignedIssues = this.linearData.user.assignedIssues.nodes;

            assignedIssues.forEach(issue => {
                // Process parent issue
                let dateToUse;

                if (issue.dueDate) {
                    dateToUse = issue.dueDate;
                } else if (issue.createdAt) {
                    const createdDate = new Date(issue.createdAt);
                    dateToUse =
                        `${createdDate.getFullYear()}-${(createdDate.getMonth() + 1).toString().padStart(2, '0')}-${createdDate.getDate().toString().padStart(2, '0')}`;
                } else {
                    return; // Skip if no date available
                }

                if (!this.issuesByDate[dateToUse]) {
                    this.issuesByDate[dateToUse] = [];
                }

                this.issuesByDate[dateToUse].push({
                    id: issue.id,
                    title: issue.title,
                    identifier: issue.identifier,
                    priority: issue.priority,
                    state: issue.state.name,
                    url: issue.url,
                    isCreatedDate: !issue.dueDate,
                    isSubissue: false, // Flag to indicate this is a parent issue
                    parentId: null // No parent for top-level issues
                });

                // Process subissues
                if (issue.children && issue.children.nodes && issue.children.nodes.length > 0) {
                    issue.children.nodes.forEach(subissue => {
                        let subissueDate;

                        if (subissue.dueDate) {
                            subissueDate = subissue.dueDate;
                        } else if (subissue.createdAt) {
                            const subCreatedDate = new Date(subissue.createdAt);
                            subissueDate =
                                `${subCreatedDate.getFullYear()}-${(subCreatedDate.getMonth() + 1).toString().padStart(2, '0')}-${subCreatedDate.getDate().toString().padStart(2, '0')}`;
                        } else {
                            return; // Skip subissue if no date available
                        }

                        if (!this.issuesByDate[subissueDate]) {
                            this.issuesByDate[subissueDate] = [];
                        }

                        this.issuesByDate[subissueDate].push({
                            id: subissue.id,
                            title: subissue.title,
                            identifier: subissue.identifier,
                            priority: subissue.priority,
                            state: subissue.state.name,
                            url: subissue.url,
                            isCreatedDate: !subissue.dueDate,
                            isSubissue: true, // Flag to indicate this is a subissue
                            parentId: issue.id, // Link to parent issue
                            parentIdentifier: issue.identifier // For display purposes
                        });
                    });
                }
            });
        }

        applyLinearDataToCalendar() {
            document.querySelectorAll('.has-issues').forEach(el => {
                el.classList.remove('has-issues');
            });

            for (const date in this.issuesByDate) {
                const cells = document.querySelectorAll(`td[data-date="${date}"]`);
                cells.forEach(cell => {
                    cell.classList.add('has-issues');

                    const count = this.issuesByDate[date].length;
                    if (count > 0) {
                        let counter = cell.querySelector('.issue-count');
                        if (!counter) {
                            counter = document.createElement('span');
                            counter.className = 'issue-count';
                            cell.appendChild(counter);
                        }
                        counter.textContent = count;
                    }
                });
            }
        }

        showIssuesForDate(date) {
            let issuesPanel = document.getElementById('issues-panel');

            if (!issuesPanel) {
                issuesPanel = document.createElement('div');
                issuesPanel.id = 'issues-panel';
                issuesPanel.className = 'issues-panel';
                document.querySelector('.calendar-container').appendChild(issuesPanel);
            }

            const displayDate = new Date(date);
            const formattedDate = displayDate.toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            const issues = this.issuesByDate[date] || [];

            let content = `<div class="panel-header">
                <h3>${formattedDate}</h3>
                <button class="close-panel">×</button>
            </div>`;

            if (issues.length === 0) {
                content += `<p>No issues or subissues on this date.</p>`;
            } else {
                content += `<ul class="issues-list">`;
                issues.forEach(issue => {
                    const priorityLabels = {
                        0: 'No Priority',
                        1: 'Urgent',
                        2: 'High',
                        3: 'Medium',
                        4: 'Low'
                    };

                    const priorityLabel = priorityLabels[issue.priority] || 'Unknown';

                    const dateTypeLabel = issue.isCreatedDate
                        ? '<span class="created-date-label">Created On</span>'
                        : '<span class="due-date-label">Due Date</span>';

                    // Add subissue-specific display
                    let subissueLabel = '';
                    if (issue.isSubissue) {
                        subissueLabel = `<span class="subissue-label">Subissue of ${issue.parentIdentifier}</span>`;
                    }

                    content += `
                        <li class="issue-item priority-${issue.priority} ${issue.isCreatedDate ? 'created-date' : 'due-date'} ${issue.isSubissue ? 'subissue' : ''}">
                            <div class="issue-header">
                                <span class="issue-id">${issue.identifier}</span>
                                <span class="issue-priority">${priorityLabel}</span>
                                ${dateTypeLabel}
                                ${subissueLabel}
                            </div>
                            <div class="issue-title">${issue.isSubissue ? '↳ ' : ''}${issue.title}</div>
                            <div class="issue-state">Status: ${issue.state}</div>
                            <a href="${issue.url}" target="_blank" class="issue-link">View in {{Auth::user()->service}}</a>
                        </li>`;
                });
                content += `</ul>`;
            }

            issuesPanel.innerHTML = content;
            issuesPanel.classList.add('visible');

            issuesPanel.querySelector('.close-panel').addEventListener('click', () => {
                issuesPanel.classList.remove('visible');
            });
        }
    }

    // Initialize the calendar application
    document.addEventListener('DOMContentLoaded', () => {
        const calendarApp = new CalendarApp();

        function loadLinearData() {
            fetch('{{ route("user.get_data", ["linear_user_id" => $data["user"]["id"]]) }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        calendarApp.setLinearData(data);
                    } else {
                        console.error('Failed to load Linear data:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error loading Linear data:', error);
                });
        }

        loadLinearData();
    });
    </script>
@endsection
