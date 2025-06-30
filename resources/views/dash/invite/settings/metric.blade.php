@include('dash.layouts.partials.head')
<div class="p-5">
    <div class="border rounded-md bg-white">
        <div class="p-4 flex items-center border-b">
            <h2 class="text-lg font-medium">Metric Selection</h2>
            <p class="text-sm text-gray-500 ml-2">Select Metrics for Grading</p>
        </div>

        <div id="personalInfoForm" class="space-y-6 p-5">

            <!-- Step 1: Select Metrics -->
            <div id="step-1" class="step-container">
                <div class="w-full">
                    <!-- Tab Navigation -->
                    <div class="mb-6">
                        <h3 class="text-base font-medium mb-3">Select Metrics</h3>
                        <ul class="flex flex-wrap text-sm font-medium text-center text-gray-500">
                            <li class="me-2">
                                <button onclick="activateTab(1)" id="tab-btn-1"
                                    class="tab-btn inline-block px-4 py-2 text-white bg-[#1E3A8A] rounded active">All</button>
                            </li>
                            @foreach ($metrics_category as $m)
                                <li class="me-2">
                                    <button onclick="activateTab({{ $m->id + 1 }})"
                                        id="tab-btn-{{ $m->id + 1 }}"
                                        class="tab-btn inline-block px-4 py-2 text-gray-600 bg-[#E5E7EB] rounded">{{ $m->title }}</button>
                                </li>
                            @endforeach
                            <li class="me-2">
                                <button onclick="activateTab(999)" id="tab-btn-999"
                                    class="tab-btn inline-block px-4 py-2 text-gray-600 bg-[#E5E7EB] rounded">Custom</button>
                            </li>
                        </ul>

                        <!-- Search Box and Add Custom Button -->
                        <div class="mt-4 flex justify-between">
                            <button type="button" onclick="showAddCustomModal()" 
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none">
                                + Add Custom Metric
                            </button>
                            <input type="text" placeholder="Search Metrics..."
                                class="px-3 py-2 border border-gray-300 rounded-md text-sm w-64 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <!-- All Metrics Tab Content -->
                    <div id="tab-content-1" class="tab-content">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b text-left text-sm font-medium text-gray-600">
                                        <th class="pb-3 w-16">Select</th>
                                        <th class="pb-3">Metric</th>
                                        <th class="pb-3">Description</th>
                                        <th class="pb-3">Category</th> 
                                    </tr>
                                </thead>
                                <tbody class="text-sm">
                                    @php
                                        $n = DB::table('metrics_type')->get();
                                    @endphp
                                    @foreach ($n as $metric)
                                        @php
                                            $checkuser = DB::table('user_metrics')
                                                ->where('userid', auth()->user()->id)
                                                ->where('metric_id', $metric->id)
                                                ->first();
                                        @endphp
                                        <tr class="border-b">
                                            <td class="py-3">
                                                <input type="checkbox" onclick="save_config({{ $metric->id }})"
                                                    @if ($checkuser) checked @endif
                                                    class="metric-checkbox w-4 h-4 text-blue-600 rounded focus:ring-blue-500"
                                                    data-metric="{{ $metric->title }}"
                                                    data-weight="{{$checkuser ? $checkuser->weight : ''}}"
                                                    data-percentage="{{$checkuser ? $checkuser->percentage : ''}}"
                                                    data-id="{{ $metric->id }}" data-category="Performance">
                                            </td>
                                            <td class="py-3 font-medium">{{ $metric->title }}</td>
                                            <td class="py-3 text-gray-600">{{ $metric->description }}</td>
                                            <td class="py-3 text-gray-600">{{ $metric->category }}</td>
                                        </tr>
                                    @endforeach
                                    
                                    <!-- Custom Metrics in All Tab -->
                                    @php
                                        $customMetrics = DB::table('user_metrics')
                                            ->where('userid', auth()->user()->id)
                                            ->whereNull('metric_id')
                                            ->get();
                                    @endphp
                                    @foreach ($customMetrics as $custom)
                                        <tr class="border-b bg-green-50">
                                            <td class="py-3">
                                                <input type="checkbox" checked
                                                    class="metric-checkbox w-4 h-4 text-blue-600 rounded focus:ring-blue-500"
                                                    data-metric="{{ $custom->custom_name }}"
                                                    data-weight="{{ $custom->weight }}"
                                                    data-percentage="{{ $custom->percentage }}"
                                                    data-id="custom-{{ $custom->id }}" 
                                                    data-category="Custom"
                                                    data-custom="true">
                                            </td>
                                            <td class="py-3 font-medium text-green-700">{{ $custom->custom_name }} <span class="text-xs">(Custom)</span></td>
                                            <td class="py-3 text-gray-600">{{ $custom->custom_description }}</td>
                                            <td class="py-3 text-gray-600">Custom</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Category-specific Tab Contents -->
                    @foreach ($metrics_category as $m)
                        <div id="tab-content-{{ $m->id + 1 }}" class="tab-content hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b text-left text-sm font-medium text-gray-600">
                                            <th class="pb-3 w-16">Select</th>
                                            <th class="pb-3">Metric</th>
                                            <th class="pb-3">Description</th>
                                            <th class="pb-3">Category</th>
                                            <th class="pb-3">App</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-sm">
                                        @php
                                            $n = DB::table('metrics_type')
                                                ->where(['category' => $m->id])
                                                ->get();
                                        @endphp
                                        @foreach ($n as $metric)
                                            @php
                                                $checkuser = DB::table('user_metrics')
                                                    ->where('userid', auth()->user()->id)
                                                    ->where('metric_id', $metric->id)
                                                    ->exists();
                                            @endphp
                                            <tr class="border-b">
                                                <td class="py-3">
                                                    <input type="checkbox"
                                                        @if ($checkuser) checked @endif
                                                        onclick="save_config({{ $metric->id }})"
                                                        class="metric-checkbox w-4 h-4 text-blue-600 rounded focus:ring-blue-500"
                                                        data-metric="{{ $metric->title }}" data-app="Linear"
                                                        data-category="Performance">
                                                </td>
                                                <td class="py-3 font-medium">{{ $metric->title }}</td>
                                                <td class="py-3 text-gray-600">{{ $metric->description }}</td>
                                                <td class="py-3 text-gray-600">{{ $metric->category }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach

                    <!-- Custom Metrics Tab Content -->
                    <div id="tab-content-999" class="tab-content hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b text-left text-sm font-medium text-gray-600">
                                        <th class="pb-3 w-16">Select</th>
                                        <th class="pb-3">Metric</th>
                                        <th class="pb-3">Description</th>
                                        <th class="pb-3">Category</th>
                                        <th class="pb-3">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm" id="custom-metrics-table">
                                    @foreach ($customMetrics as $custom)
                                        <tr class="border-b" id="custom-row-{{ $custom->id }}">
                                            <td class="py-3">
                                                <input type="checkbox" checked
                                                    class="metric-checkbox w-4 h-4 text-blue-600 rounded focus:ring-blue-500"
                                                    data-metric="{{ $custom->custom_name }}"
                                                    data-weight="{{ $custom->weight }}"
                                                    data-percentage="{{ $custom->percentage }}"
                                                    data-id="custom-{{ $custom->id }}" 
                                                    data-category="Custom"
                                                    data-custom="true">
                                            </td>
                                            <td class="py-3 font-medium">{{ $custom->custom_name }}</td>
                                            <td class="py-3 text-gray-600">{{ $custom->custom_description }}</td>
                                            <td class="py-3 text-gray-600">Custom</td>
                                            <td class="py-3">
                                                <button onclick="deleteCustomMetric({{ $custom->id }})" 
                                                    class="text-red-600 hover:text-red-800 text-sm">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2: Metrics Weighting -->
            <div id="step-2" class="step-container hidden">
                <div class="w-full">
                    <h3 class="text-base font-medium mb-4">Metrics Weighting</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b text-left text-sm font-medium text-gray-600">
                                    <th class="pb-3">Metric</th>
                                    <th class="pb-3 w-32">Weight</th>
                                    <th class="pb-3 w-32">Percentage</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm" id="metrics-weight-table">
                                <!-- Dynamic content will be inserted here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Step 3: Preview Configuration -->
            <div id="step-3" class="step-container hidden">
                <div class="w-full">
                    <h3 class="text-base font-medium mb-4">Preview Configuration</h3>
                    <div class="space-y-2 text-sm" id="preview-config">
                        <!-- Dynamic content will be inserted here -->
                    </div>
                </div>
            </div>
            
            <!-- Navigation Buttons -->
            <div class="mt-8 flex justify-between items-center">
                <div class="flex space-x-3">
                    <button type="button" id="prev-btn" onclick="previousStep()"
                        class="px-4 py-2 text-gray-600 rounded-md hover:bg-gray-100 focus:outline-none hidden">
                        ← Previous
                    </button>
                    <button type="button" id="next-btn" onclick="nextStep()"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none">
                        Next →
                    </button>
                </div>

                <button id="save-config-btn" type="submit" onclick="saveConfiguration()"
                    class="px-6 py-2 bg-[#4F46E5] text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 hidden">
                    Save Configuration
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Custom Metric Modal -->
<div id="customMetricModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-96">
        <h3 class="text-lg font-medium mb-4">Add Custom Metric</h3>
        <form id="customMetricForm">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Metric Name</label>
                <input type="text" id="customMetricName" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea id="customMetricDescription" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="hideAddCustomModal()"
                    class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Add Metric
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Fixed JavaScript functions - replace your existing script section with this

let currentStep = 1;
const totalSteps = 3;

function activateTab(index) {
    // Only allow tab switching in step 1
    if (currentStep !== 1) return;

    // Hide all tab content
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));

    // Reset all tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('text-white', 'bg-[#1E3A8A]');
        btn.classList.add('text-gray-600', 'bg-[#E5E7EB]');
    });

    // Show active tab content
    const tabContent = document.getElementById('tab-content-' + index);
    if (tabContent) {
        tabContent.classList.remove('hidden');
    }

    // Style active tab button
    const activeBtn = document.getElementById('tab-btn-' + index);
    if (activeBtn) {
        activeBtn.classList.add('text-white', 'bg-[#1E3A8A]');
        activeBtn.classList.remove('text-gray-600', 'bg-[#E5E7EB]');
    }
}

function showAddCustomModal() {
    document.getElementById('customMetricModal').classList.remove('hidden');
    document.getElementById('customMetricModal').classList.add('flex');
}

function hideAddCustomModal() {
    document.getElementById('customMetricModal').classList.add('hidden');
    document.getElementById('customMetricModal').classList.remove('flex');
    document.getElementById('customMetricForm').reset();
}

// Handle custom metric form submission
document.getElementById('customMetricForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const name = document.getElementById('customMetricName').value;
    const description = document.getElementById('customMetricDescription').value;
    
    if (!name.trim()) {
        toastr.error('Please enter a metric name.');
        return;
    }

    $("button").attr("disabled",true)
    
    // AJAX call to save custom metric
    $.ajax({
        url: '{{ route("metrics.save-custom") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            name: name,
            description: description
        },
        success: function(response) {
            toastr.success('Custom metric added successfully!');
            hideAddCustomModal();
            location.reload(); // Reload page to show new metric
        },
        error: function(xhr) {
                $("button").attr("disabled",false)
            const errorMessage = xhr.responseJSON?.message || 'Error adding custom metric.';
            toastr.error(errorMessage);
        }
    });
});

function deleteCustomMetric(id) {
    if (!confirm('Are you sure you want to delete this custom metric?')) {
        return;
    }
    
    $.ajax({
        url: '{{ url("/dashboard/metrics/delete-custom") }}/' + id,
        type: 'DELETE',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            toastr.success('Custom metric deleted successfully!');
            location.reload(); // Reload page to remove deleted metric
        },
        error: function(xhr) {
            const errorMessage = xhr.responseJSON?.message || 'Error deleting custom metric.';
            toastr.error(errorMessage);
        }
    });
}

function nextStep() {
    if (currentStep < totalSteps) {
        // Hide current step
        const currentStepEl = document.getElementById('step-' + currentStep);
        if (currentStepEl) {
            currentStepEl.classList.add('hidden');
        }

        currentStep++;

        // Show next step
        const nextStepEl = document.getElementById('step-' + currentStep);
        if (nextStepEl) {
            nextStepEl.classList.remove('hidden');
            console.log('Showing step:', currentStep); // Debug log
        } else {
            console.error('Step element not found:', 'step-' + currentStep); // Debug log
        }

        if (currentStep === 2) {
            updateMetricsWeightTable();
        } else if (currentStep === 3) {
            updatePreviewConfiguration();
        }

        updateNavigationButtons();
    }
}

function previousStep() {
    if (currentStep > 1) {
        // Hide current step
        const currentStepEl = document.getElementById('step-' + currentStep);
        if (currentStepEl) {
            currentStepEl.classList.add('hidden');
        }

        currentStep--;

        // Show previous step
        const prevStepEl = document.getElementById('step-' + currentStep);
        if (prevStepEl) {
            prevStepEl.classList.remove('hidden');
        }

        // If going back to step 1, make sure the active tab is visible
        if (currentStep === 1) {
            const activeTabContent = document.querySelector('.tab-content:not(.hidden)');
            if (!activeTabContent) {
                activateTab(1); // Show the "All" tab by default
            }
        }

        updateNavigationButtons();
    }
}

function updateNavigationButtons() {
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const saveBtn = document.getElementById('save-config-btn');

    // Show/hide previous button
    if (currentStep > 1) {
        prevBtn.classList.remove('hidden');
    } else {
        prevBtn.classList.add('hidden');
    }

    // Show/hide next button and save button
    if (currentStep === totalSteps) {
        nextBtn.classList.add('hidden');
        saveBtn.classList.remove('hidden');
    } else {
        nextBtn.classList.remove('hidden');
        saveBtn.classList.add('hidden');
    }
}

function getSelectedMetrics() {
    const selectedMetrics = [];
    document.querySelectorAll('.metric-checkbox:checked').forEach(checkbox => {
        console.log(checkbox)
        if (checkbox.dataset.id != undefined) {
            selectedMetrics.push({
                name: checkbox.dataset.metric,
                app: checkbox.dataset.app || 'N/A',
                category: checkbox.dataset.category,
                id: checkbox.dataset.id,
                weight: checkbox.dataset.weight,
                percentage: checkbox.dataset.percentage,
                isCustom: checkbox.dataset.custom === 'true'
            });
        }
    });
    //avoid duplicates
    return selectedMetrics.filter((metric, index, self) =>
        index === self.findIndex((m) => m.id === metric.id)
    );
}

function updateMetricsWeightTable() {
    const selectedMetrics = getSelectedMetrics();
    const tableBody = document.getElementById('metrics-weight-table');

    if (!tableBody) return;

    tableBody.innerHTML = '';

    selectedMetrics.forEach(metric => {
        const row = document.createElement('tr');
        row.className = 'border-b';
        const metricName = metric.isCustom ? `${metric.name} (Custom)` : metric.name;
        row.innerHTML = `
            <td class="py-3 font-medium">${metricName}</td>
            <td class="py-3">
                <input type="number" value="${metric.weight ? metric.weight : '1'}" min="1" 
                    class="weight-input w-20 px-2 py-1 border border-gray-300 rounded text-center focus:outline-none focus:ring-2 focus:ring-blue-500">
            </td>
            <td class="py-3 text-gray-600 percentage-cell">${metric.percentage ? metric.percentage : '25'}%</td>
        `;
        tableBody.appendChild(row);
    });

    // Add event listeners to weight inputs
    document.querySelectorAll('.weight-input').forEach(input => {
        input.addEventListener('input', updatePercentages);
    });

    updatePercentages();
}

function updatePercentages() {
    const inputs = document.querySelectorAll('.weight-input');
    const total = Array.from(inputs).reduce((sum, input) => sum + parseInt(input.value || 0), 0);

    inputs.forEach((input) => {
        const weight = parseInt(input.value || 0);
        const percentage = total > 0 ? Math.round((weight / total) * 100) : 0;
        const percentageCell = input.closest('tr').querySelector('.percentage-cell');
        if (percentageCell) {
            percentageCell.textContent = percentage + '%';
        }
    });
}

var datas = []

function updatePreviewConfiguration() {
    const selectedMetrics = getSelectedMetrics();
    const previewContainer = document.getElementById('preview-config');

    if (!previewContainer) return;

    previewContainer.innerHTML = '';
    datas = []; // Reset datas array

    const weightInputs = document.querySelectorAll('.weight-input');
    const total = Array.from(weightInputs).reduce((sum, input) => sum + parseInt(input.value || 0), 0);

    selectedMetrics.forEach((metric, index) => {
        const weight = weightInputs[index] ? parseInt(weightInputs[index].value || 0) : 1;
        const percentage = total > 0 ? Math.round((weight / total) * 100) : 0;
        
        // Add percentage to metric for preview
        const item = document.createElement('div');
        item.className = 'flex items-center';
        const metricName = metric.isCustom ? `${metric.name} (Custom)` : metric.name;
        item.innerHTML = `
            <div class="w-2 h-2 bg-black rounded-full mr-3"></div>
            <span>${metricName}, ${metric.category}) - Weight: ${weight} (${percentage}%)</span>
        `;
        previewContainer.appendChild(item);

        datas.push({
            id: metric.id,
            name: metric.name,
            app: metric.app,
            category: metric.category,
            weight: weight,
            percentage: percentage,
            isCustom: metric.isCustom
        });
    });
}

function saveConfiguration() {
    if (datas.length === 0) {
        toastr.error('Please select at least one metric.');
        return;
    }

    $("button").prop("disabled", true); // Disable all buttons to prevent multiple clicks

    // Send AJAX request to save the configuration
    $.ajax({
        url: '{{ route('metrics.save', ['update' => 1]) }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            metrics: JSON.stringify(datas),
        },
        success: function(response) {
            toastr.success(response.message || 'Configuration saved successfully.');
            // Optionally, reset the form or redirect
            $("button").prop("disabled", false); // Re-enable buttons
        },
        error: function(xhr) {
            const errorMessage = xhr.responseJSON?.message || 'Error saving configuration.';
            toastr.error(errorMessage);
            $("button").prop("disabled", false); // Re-enable buttons
        }
    });
}

// Click outside modal to close
document.getElementById('customMetricModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideAddCustomModal();
    }
});
</script>