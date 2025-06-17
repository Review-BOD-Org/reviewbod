<!-- views/dash/reports.blade.php -->
@extends('dash.layouts.app')

@section('page-title', 'Reports')

@section('content')
<div class="container mx-auto px-4 py-6 bg-white min-h-screen">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Analytics Reports</h1>
        <div class="flex space-x-3">
            <button id="exportBtn" class="bg-[#1E3A8A] text-white px-4 py-2 rounded-md text-sm hover:bg-blue-800 transition">
                <i class="fa fa-download mr-2"></i> Export Report
            </button>
            <div class="relative" id="dateFilterDropdown">
                <button class="bg-white border border-gray-300 px-4 py-2 rounded-md text-sm flex items-center">
                    <i class="fa fa-calendar mr-2"></i> Filter
                    <i class="fa fa-chevron-down ml-2"></i>
                </button>
                <div class="absolute right-0 mt-2 bg-white border border-gray-200 rounded-md shadow-lg w-64 z-10 hidden" id="filterOptions">
                    <div class="p-3">
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                            <select id="dateRange" class="w-full border border-gray-300 rounded-md p-2 text-sm">
                                <option value="7days">Last 7 Days</option>
                                <option value="30days">Last 30 Days</option>
                                <option value="90days">Last 90 Days</option>
                                <option value="custom">Custom Range</option>
                            </select>
                        </div>
                        <div id="customDateRange" class="hidden">
                            <div class="mb-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">From</label>
                                <input type="date" id="startDate" class="w-full border border-gray-300 rounded-md p-2 text-sm">
                            </div>
                            <div class="mb-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">To</label>
                                <input type="date" id="endDate" class="w-full border border-gray-300 rounded-md p-2 text-sm">
                            </div>
                        </div>
                        <button id="applyFilter" class="bg-[#1E3A8A] text-white px-4 py-2 rounded-md text-sm hover:bg-blue-800 transition w-full">Apply</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Layout -->
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Charts and Graphs Section (Left 2/3) -->
        <div class="lg:w-2/3">
            <!-- Top Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                    <h3 class="text-gray-500 text-sm mb-1">Total Users</h3>
                    <div class="flex justify-between items-center">
                        <span class="text-2xl font-bold" id="totalUsers">0</span>
                        {{-- <span class="text-green-500 text-sm font-medium" id="userGrowth">+0%</span> --}}
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                    <h3 class="text-gray-500 text-sm mb-1">Active Projects</h3>
                    <div class="flex justify-between items-center">
                        <span class="text-2xl font-bold" id="activeProjects">0</span>
                        {{-- <span class="text-green-500 text-sm font-medium" id="projectGrowth">+0%</span> --}}
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                    <h3 class="text-gray-500 text-sm mb-1">Teams</h3>
                    <div class="flex justify-between items-center">
                        <span class="text-2xl font-bold" id="totalTeams">0</span>
                        {{-- <span class="text-green-500 text-sm font-medium" id="teamGrowth">+0%</span> --}}
                    </div>
                </div>
            </div>

            <!-- Tab Navigation for Charts -->
            <div class="border-b border-gray-200 mb-6">
                <ul class="flex -mb-px" id="chartTabNav">
                    <li class="mr-1">
                        <a href="#" class="inline-block py-4 px-4 border-b-2 border-[#1E3A8A] font-medium text-sm text-[#1E3A8A] chart-tab-item" data-tab="activity">Team Activity</a>
                    </li>
                    <li class="mr-1">
                        <a href="#" class="inline-block py-4 px-4 text-gray-500 hover:text-gray-700 font-medium text-sm chart-tab-item" data-tab="projects">Project Progress</a>
                    </li>
                    <li class="mr-1">
                        <a href="#" class="inline-block py-4 px-4 text-gray-500 hover:text-gray-700 font-medium text-sm chart-tab-item" data-tab="users">User Performance</a>
                    </li>
                </ul>
            </div>

            <!-- Chart Content Areas -->
            <div class="chart-content-container bg-white p-6 rounded-lg border border-gray-200 shadow-sm mb-6">
                <!-- Team Activity Tab Content -->
                <div id="activity" class="chart-content block">
                    <h2 class="text-lg font-medium mb-4">Team Activity Overview</h2>
                    <div class="h-80">
                        <canvas id="teamActivityChart"></canvas>
                    </div>
                </div>

                <!-- Project Progress Tab Content -->
                <div id="projects" class="chart-content hidden">
                    <h2 class="text-lg font-medium mb-4">Project Progress</h2>
                    <div class="h-80">
                        <canvas id="projectProgressChart"></canvas>
                    </div>
                </div>

                <!-- User Performance Tab Content -->
                <div id="users" class="chart-content hidden">
                    <h2 class="text-lg font-medium mb-4">User Performance Metrics</h2>
                    <div class="h-80">
                        <canvas id="userPerformanceChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Additional Data Table -->
            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-medium">Recent Activity</h2>
                    <select id="activityFilter" class="border border-gray-300 rounded-md p-2 text-sm">
                        <option value="all">All Activities</option>
                        <option value="users">User Activities</option>
                        <option value="projects">Project Updates</option>
                        {{-- <option value="teams">Team Changes</option> --}}
                    </select>
                </div>
                <div class="overflow-y-auto" style="height: 300px;">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Related To</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="activityTableBody" style="overflow-y:auto; max-height:300px;">
                            <!-- Activity data will be loaded here -->
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Loading data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
            </div>
        </div>

        <!-- AI Analysis Sidebar (Right 1/3) -->
        <div class="lg:w-1/3">
            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-medium">AI Insights</h2>
                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">Powered by ReviewBOD</span>
                </div>
                <div id="aiAnalysisLoader" class="flex justify-center items-center py-12">
                    <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-[#1E3A8A]"></div>
                </div>
                <div id="aiAnalysisContent" class="text-sm text-gray-600 space-y-4 hidden">
                    <!-- AI analysis will be loaded here -->
                </div>
                <div class="mt-4">
                    <button id="refreshAnalysis" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md text-sm hover:bg-gray-200 transition w-full flex items-center justify-center">
                        <i class="fas fa-sync-alt mr-2"></i> Refresh Analysis
                    </button>
                </div>
            </div>

            <!-- Key Performance Indicators -->
            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm mb-6">
                <h2 class="text-lg font-medium mb-4">Key Performance Indicators</h2>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">Team Efficiency</span>
                            <span class="text-sm font-medium text-gray-700" id="teamEfficiencyValue">0%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-green-500 h-2.5 rounded-full" id="teamEfficiencyBar" style="width: 0%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">Project Completion Rate</span>
                            <span class="text-sm font-medium text-gray-700" id="projectCompletionValue">0%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-blue-500 h-2.5 rounded-full" id="projectCompletionBar" style="width: 0%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">User Engagement</span>
                            <span class="text-sm font-medium text-gray-700" id="userEngagementValue">0%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-purple-500 h-2.5 rounded-full" id="userEngagementBar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recommendations Based on AI Analysis -->
            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                <h2 class="text-lg font-medium mb-4">Recommendations</h2>
                <div id="recommendationsLoader" class="flex justify-center items-center py-6">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#1E3A8A]"></div>
                </div>
                <ul id="recommendationsList" class="text-sm text-gray-600 space-y-4 list-disc pl-5 hidden">
                    <!-- Recommendations will be loaded here -->
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Reports Functionality -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart Tab Functionality
    const chartTabItems = document.querySelectorAll('.chart-tab-item');
    const chartContents = document.querySelectorAll('.chart-content');
    
    // Function to activate a chart tab
    function activateChartTab(tabElement) {
        const tabToActivate = tabElement.getAttribute('data-tab');
        
        // Reset all tabs
        chartTabItems.forEach(tab => {
            tab.classList.remove('border-b-2', 'border-[#1E3A8A]', 'text-[#1E3A8A]');
            tab.classList.add('text-gray-500');
        });
        
        // Activate selected tab
        tabElement.classList.remove('text-gray-500');
        tabElement.classList.add('border-b-2', 'border-[#1E3A8A]', 'text-[#1E3A8A]');
        
        // Hide all content
        chartContents.forEach(content => {
            content.classList.add('hidden');
            content.classList.remove('block');
        });
        
        // Show relevant content
        document.getElementById(tabToActivate).classList.remove('hidden');
        document.getElementById(tabToActivate).classList.add('block');
    }
    
    // Set up click events for all chart tabs
    chartTabItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            activateChartTab(this);
        });
    });

    // Date Filter Dropdown Toggle
    const filterButton = document.querySelector('#dateFilterDropdown button');
    const filterOptions = document.getElementById('filterOptions');
    
    filterButton.addEventListener('click', function() {
        filterOptions.classList.toggle('hidden');
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!document.getElementById('dateFilterDropdown').contains(event.target)) {
            filterOptions.classList.add('hidden');
        }
    });
    
    // Custom Date Range Toggle
    const dateRangeSelect = document.getElementById('dateRange');
    const customDateRange = document.getElementById('customDateRange');
    
    dateRangeSelect.addEventListener('change', function() {
        if (this.value === 'custom') {
            customDateRange.classList.remove('hidden');
        } else {
            customDateRange.classList.add('hidden');
        }
    });
    
    // Apply Filter Button
    document.getElementById('applyFilter').addEventListener('click', function() {
        filterOptions.classList.add('hidden');
        fetchReportData();
    });
    
    // Export Button
    document.getElementById('exportBtn').addEventListener('click', function() {
        exportReport();
    });
    
    // Refresh Analysis Button
    document.getElementById('refreshAnalysis').addEventListener('click', function() {
        fetchAIAnalysis();
    });
    
    // Activity Filter Change
    document.getElementById('activityFilter').addEventListener('change', function() {
        filterActivityTable(this.value);
    });
    
    // Initialize Charts
    initCharts();
    
    // Fetch Data
    fetchReportData();
    fetchAIAnalysis();
});

// Function to initialize charts
function initCharts() {
    // Team Activity Chart
    const teamActivityCtx = document.getElementById('teamActivityChart').getContext('2d');
    const teamActivityChart = new Chart(teamActivityCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: '',
                data: [],
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.4
            }, {
                label: '',
                data: [],
                borderColor: 'rgba(153, 102, 255, 1)',
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
    
    // Project Progress Chart
    const projectProgressCtx = document.getElementById('projectProgressChart').getContext('2d');
    const projectProgressChart = new Chart(projectProgressCtx, {
        type: 'bar',
        data: {
            labels: ['Project A', 'Project B', 'Project C', 'Project D'],
            datasets: [{
                label: 'Completed',
                data: [65, 40, 80, 25],
                backgroundColor: 'rgba(54, 162, 235, 0.7)'
            }, {
                label: 'Remaining',
                data: [35, 60, 20, 75],
                backgroundColor: 'rgba(255, 99, 132, 0.7)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    stacked: true,
                },
                y: {
                    stacked: true,
                    max: 100
                }
            }
        }
    });
    
    // User Performance Chart
    const userPerformanceCtx = document.getElementById('userPerformanceChart').getContext('2d');
    const userPerformanceChart = new Chart(userPerformanceCtx, {
        type: 'radar',
        data: {
            labels: ['Tasks Completed', 'Response Time', 'Communication', 'Team Collaboration', 'Innovation'],
            datasets: [{
                label: 'User 1',
                data: [85, 72, 90, 80, 78],
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)'
            }, {
                label: 'User 2',
                data: [70, 85, 75, 92, 80],
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    min: 0,
                    max: 100
                }
            }
        }
    });
}

// Function to fetch report data
function fetchReportData() {
    // Show loading states
    document.getElementById('totalUsers').textContent = '...';
    document.getElementById('activeProjects').textContent = '...';
    document.getElementById('totalTeams').textContent = '...';
    
    // Fetch data from backend
    fetch('/dashboard/reports/data', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(getFilterParams())
    })
    .then(response => response.json())
    .then(data => {
        // Update stats cards
        document.getElementById('totalUsers').textContent = data.stats.users.total;
        // document.getElementById('userGrowth').textContent = `${data.stats.users.growth > 0 ? '+' : ''}${data.stats.users.growth}%`;
        // document.getElementById('userGrowth').className = data.stats.users.growth >= 0 ? 'text-green-500 text-sm font-medium' : 'text-red-500 text-sm font-medium';
        
        document.getElementById('activeProjects').textContent = data.stats.projects.total;
        // document.getElementById('projectGrowth').textContent = `${data.stats.projects.growth > 0 ? '+' : ''}${data.stats.projects.growth}%`;
        // document.getElementById('projectGrowth').className = data.stats.projects.growth >= 0 ? 'text-green-500 text-sm font-medium' : 'text-red-500 text-sm font-medium';
        
        document.getElementById('totalTeams').textContent = data.stats.teams.total;
        // document.getElementById('teamGrowth').textContent = `${data.stats.teams.growth > 0 ? '+' : ''}${data.stats.teams.growth}%`;
        // document.getElementById('teamGrowth').className = data.stats.teams.growth >= 0 ? 'text-green-500 text-sm font-medium' : 'text-red-500 text-sm font-medium';
        
        // Update KPIs
        updateKPIs(data.kpi);
        
        // Update activity table
        updateActivityTable(data.activities);
        
        // Update charts with new data
        updateCharts(data.charts);
    })
    .catch(error => {
        console.error('Error fetching report data:', error);
        // Show error message to user
        alert('Failed to load report data. Please try again.');
    });
}

// Function to update KPI indicators
function updateKPIs(kpiData) {
    // Team Efficiency
    document.getElementById('teamEfficiencyValue').textContent = `${kpiData.teamEfficiency}%`;
    document.getElementById('teamEfficiencyBar').style.width = `${kpiData.teamEfficiency}%`;
    
    // Project Completion
    document.getElementById('projectCompletionValue').textContent = `${kpiData.projectCompletion}%`;
    document.getElementById('projectCompletionBar').style.width = `${kpiData.projectCompletion}%`;
    
    // User Engagement
    document.getElementById('userEngagementValue').textContent = `${kpiData.userEngagement}%`;
    document.getElementById('userEngagementBar').style.width = `${kpiData.userEngagement}%`;
}

// Function to update activity table
function updateActivityTable(activities) {
    const tableBody = document.getElementById('activityTableBody');
    tableBody.innerHTML = '';
    
    if (activities.length === 0) {
        const row = document.createElement('tr');
        row.innerHTML = `<td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No activities found for the selected period.</td>`;
        tableBody.appendChild(row);
        return;
    }
    
    activities.forEach(activity => {
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50';
        row.dataset.type = activity.type;
        
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-${getTypeColor(activity.type)}-100 text-${getTypeColor(activity.type)}-800">
                    ${activity.type}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${activity.description}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${activity.relatedTo}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${formatDate(activity.date)}</td>
        `;
        
        tableBody.appendChild(row);
    });
}

// Function to filter activity table
function filterActivityTable(type) {
    const rows = document.querySelectorAll('#activityTableBody tr');
    
    rows.forEach(row => {
        if (type === 'all' || row.dataset.type === type) {
            row.classList.remove('hidden');
        } else {
            row.classList.add('hidden');
        }
    });
}

// Function to update chart data
function updateCharts(chartData) {
    // Get chart instances
    const teamActivityChart = Chart.getChart('teamActivityChart');
    const projectProgressChart = Chart.getChart('projectProgressChart');
    const userPerformanceChart = Chart.getChart('userPerformanceChart');
    
    // Update team activity chart
    teamActivityChart.data.labels = chartData.teamActivity.labels;
    teamActivityChart.data.datasets = chartData.teamActivity.datasets;
    teamActivityChart.update();
    
    // Update project progress chart
    projectProgressChart.data.labels = chartData.projectProgress.labels;
    projectProgressChart.data.datasets = chartData.projectProgress.datasets;
    projectProgressChart.update();
    
    // Update user performance chart
    userPerformanceChart.data.labels = chartData.userPerformance.labels;
    userPerformanceChart.data.datasets = chartData.userPerformance.datasets;
    userPerformanceChart.update();
}

// Function to fetch AI analysis
function fetchAIAnalysis() {
    // Show loader
    document.getElementById('aiAnalysisLoader').classList.remove('hidden');
    document.getElementById('aiAnalysisContent').classList.add('hidden');
    document.getElementById('recommendationsLoader').classList.remove('hidden');
    document.getElementById('recommendationsList').classList.add('hidden');
    
    // Fetch analysis from backend
    fetch('/dashboard/reports/analysis', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(getFilterParams())
    })
    .then(response => response.json())
    .then(data => {
        // Update AI analysis content
        const analysisContent = document.getElementById('aiAnalysisContent');
        analysisContent.innerHTML = '';
        
        // Format and display analysis paragraphs
        data.analysis.forEach(paragraph => {
            const p = document.createElement('p');
            p.textContent = paragraph;
            analysisContent.appendChild(p);
        });
        
        // Update recommendations
        const recommendationsList = document.getElementById('recommendationsList');
        recommendationsList.innerHTML = '';
            console.log( data.recommendations)
        // Add recommendation items
        data.recommendations.forEach(recommendation => {
            const li = document.createElement('li');
            li.textContent = recommendation;
            recommendationsList.appendChild(li);
        });
        
        // Hide loaders, show content
        document.getElementById('aiAnalysisLoader').classList.add('hidden');
        document.getElementById('aiAnalysisContent').classList.remove('hidden');
        document.getElementById('recommendationsLoader').classList.add('hidden');
        document.getElementById('recommendationsList').classList.remove('hidden');
    })
    .catch(error => {
        console.error('Error fetching AI analysis:', error);
        // Show error message
        document.getElementById('aiAnalysisLoader').classList.add('hidden');
        document.getElementById('aiAnalysisContent').classList.remove('hidden');
        document.getElementById('aiAnalysisContent').innerHTML = '<p class="text-red-500">Failed to load AI analysis. Please try again.</p>';
        
        document.getElementById('recommendationsLoader').classList.add('hidden');
        document.getElementById('recommendationsList').classList.remove('hidden');
        document.getElementById('recommendationsList').innerHTML = '<li class="text-red-500">Failed to load recommendations.</li>';
    });
}

// Function to export report
function exportReport() {
    // Get current filter parameters
    const params = getFilterParams();
    
    // Create form to trigger download
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/dashboard/reports/export';
    form.style.display = 'none';
    
    // Add CSRF token
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    form.appendChild(csrfToken);
    
    // Add filter parameters
    for (const key in params) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = params[key];
        form.appendChild(input);
    }
    
    // Submit form to trigger download
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

// Helper function to get filter parameters
function getFilterParams() {
    const dateRange = document.getElementById('dateRange').value;
    
    if (dateRange === 'custom') {
        return {
            range: 'custom',
            startDate: document.getElementById('startDate').value,
            endDate: document.getElementById('endDate').value
        };
    }
    
    return {
        range: dateRange
    };
}

// Helper function to format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Helper function to get color for activity type
function getTypeColor(type) {
    switch(type) {
        case 'users': return 'blue';
        case 'projects': return 'green';
        case 'teams': return 'purple';
        default: return 'gray';
    }
}
</script>
@endsection