<div class="font-sans max-w-full p-4 bg-white">
    <!-- Header and Time Range Selector -->
    <div class="flex justify-between mb-6 items-center">
        <h1 class="text-xl font-bold text-gray-900">Linear Analytics Dashboard</h1>
        <div class="relative inline-block w-60">
            <select id="timeRangeSelect" class="block w-full px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded appearance-none focus:outline-none">
                {{-- <option value="">All time</option> --}}
                <option value="last_week">Last week</option>
                <option value="last_month">Last month</option>
                <option value="last_quarter">Last quarter</option>
            </select>
            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Error Message -->
    <div id="errorMessage" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 hidden" role="alert">
        <p id="errorText"></p>
    </div>

    <!-- Dashboard Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Tasks Completed Card -->
        <div class="border border-gray-200 rounded-lg overflow-hidden">
            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                <h2 class="font-medium text-gray-900">Tasks Completed</h2>
            </div>
            <div class="p-4" id="tasksCompletedContent">
                <div class="flex items-center mb-4">
                    <div class="mr-4">
                        <span class="font-medium">Tasks:</span> <span id="totalTasks">0</span>
                    </div>
                    <div class="mr-4">
                        <span class="font-medium">Subtasks:</span> <span id="totalSubtasks">0</span>
                    </div>
                </div>
                <div class="flex items-center mb-4">
                    <div class="mr-4">
                        <span class="font-medium">Avg. Task Duration:</span> <span id="avgTaskDuration">0</span> hours
                    </div>
                    <div>
                        <span class="font-medium">Avg. Subtask Duration:</span> <span id="avgSubtaskDuration">0</span> hours
                    </div>
                </div>
                <div class="flex mb-4">
                    <div class="flex items-center mr-6">
                        <div class="w-4 h-4 bg-indigo-600 mr-1"></div>
                        <span>Tasks</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-green-400 mr-1"></div>
                        <span>Subtasks</span>
                    </div>
                </div>
                <div id="tasksCompletedEmpty" class="bg-gray-100 rounded p-8 flex flex-col items-center justify-center hidden">
                    <div class="w-16 h-16 bg-gray-300 rounded-full mb-3 flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-500 text-center">No completed tasks or subtasks in this period.</p>
                    <p class="text-gray-500 text-sm text-center">Start completing some tasks to see data here.</p>
                </div>
               <div>
                <canvas id="tasksCompletedChart" class="w-full h-64 hidden"></canvas>
               </div>
            </div>
        </div>

        <!-- Daily Activity Chart Section -->
        <div class="border border-gray-200 rounded-lg overflow-hidden">
            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                <h2 class="font-medium text-gray-900">Daily Activity</h2>
            </div>
            <div class="p-4" id="dailyActivityContent">
                <div class="flex mb-4">
                    <div class="flex items-center mr-6">
                        <div class="w-4 h-4 bg-indigo-600 mr-1"></div>
                        <span>Tasks Completed</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-green-400 mr-1"></div>
                        <span>Subtasks Completed</span>
                    </div>
                </div>
                <div id="dailyActivityEmpty" class="bg-gray-100 rounded p-8 flex flex-col items-center justify-center hidden">
                    <div class="w-16 h-16 bg-gray-300 rounded-full mb-3 flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-500 text-center">No daily activity data available.</p>
                </div>
               <div>

                <canvas id="dailyActivityChart" class="h-80 w-full min-h-64 hidden"></canvas>

               </div>
            </div>
        </div>
    </div>

    <!-- User Retention Section -->
    <div id="userRetentionSection" class="mb-8">
        <h2 class="font-medium text-gray-900 mb-4">User Retention</h2>
        <div class="border border-gray-200 rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Cohort
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            User Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Avg. Sessions
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody id="userRetentionBody" class="bg-white divide-y divide-gray-200">
                    <!-- Populated via JavaScript -->
                </tbody>
            </table>
            <div id="userRetentionEmpty" class="bg-gray-100 rounded p-8 flex flex-col items-center justify-center hidden">
                <div class="w-16 h-16 bg-gray-300 rounded-full mb-3 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <p class="text-gray-500 text-center">No retention data available.</p>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    let tasksChart = null;
    let dailyActivityChart = null;

    const timeRangeSelect = document.getElementById('timeRangeSelect');
    const errorMessage = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');
    const tasksCompletedContent = document.getElementById('tasksCompletedContent');
    const totalTasks = document.getElementById('totalTasks');
    const totalSubtasks = document.getElementById('totalSubtasks');
    const avgTaskDuration = document.getElementById('avgTaskDuration');
    const avgSubtaskDuration = document.getElementById('avgSubtaskDuration');
    const tasksCompletedEmpty = document.getElementById('tasksCompletedEmpty');
    const tasksCompletedChartCanvas = document.getElementById('tasksCompletedChart');
    const dailyActivityContent = document.getElementById('dailyActivityContent');
    const dailyActivityEmpty = document.getElementById('dailyActivityEmpty');
    const dailyActivityChartCanvas = document.getElementById('dailyActivityChart');
    const userRetentionSection = document.getElementById('userRetentionSection');
    const userRetentionBody = document.getElementById('userRetentionBody');
    const userRetentionEmpty = document.getElementById('userRetentionEmpty');

    // Hide content until data is loaded
    tasksCompletedContent.classList.add('hidden');
    dailyActivityContent.classList.add('hidden');
    userRetentionSection.classList.add('hidden');

    // Optimized chart rendering for large datasets
    function optimizeDataForChart(data, maxDataPoints = 100) {
        if (data.length <= maxDataPoints) {
            return data;
        }
        
        const factor = Math.ceil(data.length / maxDataPoints);
        const optimized = [];
        
        for (let i = 0; i < data.length; i += factor) {
            let windowSum = 0;
            let count = 0;
            const endIndex = Math.min(i + factor, data.length);
            let startDate = data[i].date;
            let endDate = data[endIndex - 1]?.date || startDate;
            
            for (let j = i; j < endIndex; j++) {
                windowSum += data[j].tasks || 0;
                count++;
            }
            
            let displayDate = startDate;
            if (startDate !== endDate && endDate) {
                displayDate = `${startDate} - ${endDate}`;
            }
            
            optimized.push({
                date: displayDate,
                tasks: windowSum,
                subtasks: 0,
                originalIndices: [i, endIndex - 1]
            });
        }
        
        return optimized;
    }

    function fetchDashboardData(timeRange) {
        $('#preloader').show()
        fetch(`/dashboard/dashboard-data?id={{ $data['user']['id'] }}&time_range=${timeRange}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            $('#preloader').hide()
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            
            return response.json();
        })
        .then(data => {
            $('#preloader').hide()
            if (!data.success) {
                errorMessage.classList.remove('hidden');
                errorText.textContent = data.message || 'Failed to load dashboard data.';
                tasksCompletedContent.classList.add('hidden');
                dailyActivityContent.classList.add('hidden');
                userRetentionSection.classList.add('hidden');
                return;
            }

            errorMessage.classList.add('hidden');
            tasksCompletedContent.classList.remove('hidden');
            dailyActivityContent.classList.remove('hidden');
            userRetentionSection.classList.remove('hidden');

            totalTasks.textContent = data.tasksCompleted.totalTasks;
            totalSubtasks.textContent = data.tasksCompleted.totalSubtasks;
            avgTaskDuration.textContent = data.tasksCompleted.avgTaskDuration;
            avgSubtaskDuration.textContent = data.tasksCompleted.avgSubtaskDuration;

            // Check if there's any non-zero data for the chart
            const hasChartData = data.tasksCompleted.byDay.some(item => item.tasks > 0 || item.subtasks > 0);
            
            if (hasChartData && (data.tasksCompleted.totalTasks > 0 || data.tasksCompleted.totalSubtasks > 0)) {
                tasksCompletedEmpty.classList.add('hidden');
                tasksCompletedChartCanvas.classList.remove('hidden');

                const optimizedChartData = optimizeDataForChart(data.tasksCompleted.byDay);

                if (tasksChart) {
                    tasksChart.destroy();
                }

                tasksChart = new Chart(tasksCompletedChartCanvas.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: optimizedChartData.map(item => item.date),
                        datasets: [
                            {
                                label: 'Tasks Completed',
                                data: optimizedChartData.map(item => item.tasks),
                                backgroundColor: '#4f46e5',
                                borderColor: '#4338ca',
                                borderWidth: 1
                            },
                            {
                                label: 'Subtasks Completed',
                                data: optimizedChartData.map(item => item.subtasks),
                                backgroundColor: '#4ade80',
                                borderColor: '#16a34a',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                },
                                title: {
                                    display: true,
                                    text: 'Count'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Date'
                                },
                                ticks: {
                                    maxTicksLimit: 20,
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    title: function(tooltipItems) {
                                        return tooltipItems[0].label;
                                    },
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += context.parsed.y;
                                        return label;
                                    }
                                }
                            },
                            decimation: {
                                enabled: true,
                                algorithm: 'lttb',
                                samples: 50,
                                threshold: 100
                            }
                        }
                    }
                });
            } else {
                tasksCompletedEmpty.classList.remove('hidden');
                tasksCompletedChartCanvas.classList.add('hidden');
            }

            if (data.dailyStats && data.dailyStats.some(stat => stat.tasks > 0 || stat.subtasks > 0)) {
                dailyActivityEmpty.classList.add('hidden');
                dailyActivityChartCanvas.classList.remove('hidden');

                const optimizedDailyStats = optimizeDataForChart(data.dailyStats);

                if (dailyActivityChart) {
                    dailyActivityChart.destroy();
                }

                dailyActivityChart = new Chart(dailyActivityChartCanvas.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: optimizedDailyStats.map(stat => stat.date),
                        datasets: [
                            {
                                label: 'Tasks Completed',
                                data: optimizedDailyStats.map(stat => stat.tasks),
                                backgroundColor: '#4f46e5',
                                borderColor: '#4338ca',
                                borderWidth: 1
                            },
                            {
                                label: 'Subtasks Completed',
                                data: optimizedDailyStats.map(stat => stat.subtasks),
                                backgroundColor: '#4ade80',
                                borderColor: '#16a34a',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                stacked: true,
                                title: {
                                    display: true,
                                    text: 'Date'
                                },
                                ticks: {
                                    maxTicksLimit: 20,
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            },
                            y: {
                                beginAtZero: true,
                                stacked: true,
                                ticks: {
                                    precision: 0
                                },
                                title: {
                                    display: true,
                                    text: 'Count'
                                }
                            }
                        },
                        plugins: {
                            decimation: {
                                enabled: true,
                                algorithm: 'lttb',
                                samples: 50,
                                threshold: 100
                            }
                        }
                    }
                });
            } else {
                dailyActivityEmpty.classList.remove('hidden');
                dailyActivityChartCanvas.classList.add('hidden');
            }

            if (data.userRetention && data.userRetention.length > 0 && data.userRetention[0].cohort !== 'Unknown') {
                userRetentionEmpty.classList.add('hidden');
                userRetentionBody.innerHTML = '';
                data.userRetention.forEach(retention => {
                    const statusClass = retention.status === 'Active' ? 'bg-green-100 text-green-800' :
                                       retention.status === 'Occasional' ? 'bg-yellow-100 text-yellow-800' :
                                       'bg-gray-100 text-gray-800';
                    userRetentionBody.innerHTML += `
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">${retention.cohort}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">${retention.userType}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">${retention.avgSessions}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full ${statusClass}">
                                    ${retention.status}
                                </span>
                            </td>
                        </tr>
                    `;
                });
            } else {
                userRetentionEmpty.classList.remove('hidden');
                userRetentionBody.innerHTML = '';
            }
        })
        .catch(error => {
            errorMessage.classList.remove('hidden');
            errorText.textContent = 'Error loading dashboard data: ' + error.message;
            tasksCompletedContent.classList.add('hidden');
            dailyActivityContent.classList.add('hidden');
            userRetentionSection.classList.add('hidden');
            console.error('Fetch error:', error);
        });
    }

    fetchDashboardData(timeRangeSelect.value);

    let timeoutId;
    timeRangeSelect.addEventListener('change', function () {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => {
            fetchDashboardData(this.value);
        }, 300);
    });
});
</script>