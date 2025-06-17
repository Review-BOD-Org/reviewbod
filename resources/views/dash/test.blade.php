<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Assistant Chat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .message-container {
            max-height: calc(100vh - 160px);
            overflow-y: auto;
        }
        .typing-indicator {
            display: none;
        }
        .typing-indicator.show {
            display: block;
        }
        .animate-pulse-slow {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</head>
<body class="bg-gray-50 h-screen flex flex-col">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 px-6 py-4">
        <h1 class="text-xl font-semibold text-gray-800">AI Assistant</h1>
        <p class="text-sm text-gray-500">Ask me anything about your data</p>
    </div>

    <!-- Chat Messages Container -->
    <div class="flex-1 message-container px-6 py-4 space-y-4" id="messagesContainer">
        <!-- Welcome Message -->
        <div class="space-x-3">
            <div class="w-8 h-8 relative top-10 rounded-full bg-green-600 flex items-center justify-center flex-shrink-0">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="3" fill="white" />
                    <path d="M12 2v4M12 18v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M2 12h4M18 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83" stroke="white" stroke-width="1.5" />
                </svg>
            </div>
            <span class="text-xs text-gray-500 relative left-[26px]">
                <b class="text-[13px] text-[#c4c3c7] text-medium">AI Assistant</b> Now
            </span>
            <div class="max-w-[75%]">
                <div class="bg-[#CB964F] text-white px-4 py-3 rounded-2xl rounded-tl-sm shadow-sm">
                    <p class="text-sm leading-relaxed">Hello! I can help you query your database and present the results in different formats like tables, charts, timelines, or simple text. Try asking me about your tasks, projects, teams, or users!</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Input Area -->
    <div class="bg-white border-t border-gray-200 px-6 py-4">
        <div class="flex space-x-3">
            <input 
                type="text" 
                id="queryInput" 
                placeholder="Ask me about your data..." 
                class="flex-1 px-4 py-3 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
            <button 
                id="sendButton"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-full transition-colors disabled:bg-gray-400"
            >
                <i class="bi bi-send-fill"></i>
            </button>
        </div>
    </div>

    <script>
        class ChatInterface {
            constructor() {
                this.messagesContainer = document.getElementById('messagesContainer');
                this.queryInput = document.getElementById('queryInput');
                this.sendButton = document.getElementById('sendButton');
                this.apiEndpoint = 'https://api.reviewbod.com/api/query';
                
                this.init();
            }

            init() {
                this.sendButton.addEventListener('click', () => this.sendQuery());
                this.queryInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') this.sendQuery();
                });
            }

            async sendQuery() {
                const query = this.queryInput.value.trim();
                if (!query) return;

                // Add user message
                this.addUserMessage(query);
                this.queryInput.value = '';
                this.sendButton.disabled = true;

                // Show typing indicator
                const typingId = this.showTypingIndicator();

                try {
                    const response = await fetch(this.apiEndpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ query })
                    });

                    const data = await response.json();

                    // Remove typing indicator
                    this.removeTypingIndicator(typingId);

                    if (data.success) {
                        this.addAIResponse(data.response);
                    } else {
                        this.addErrorMessage(data.error || 'An error occurred');
                    }
                } catch (error) {
                    this.removeTypingIndicator(typingId);
                    this.addErrorMessage('Failed to connect to AI service');
                } finally {
                    this.sendButton.disabled = false;
                }
            }

            addUserMessage(message) {
                const messageHtml = `
                    <div class="flex items-start justify-end space-x-3">
                        <div class="max-w-[70%] flex flex-col items-end">
                            <div class="w-8 h-8 relative top-9 right-3 self-start rounded-full bg-yellow-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                U
                            </div>
                            <span class="text-xs text-gray-500 relative self-start left-5">
                                <b class="text-[13px] text-[#c4c3c7] text-medium">You</b> ${this.getCurrentTime()}
                            </span>
                            <div class="bg-[#1E3A8A] text-white px-4 py-3 rounded-2xl rounded-tr-sm shadow-sm">
                                <p class="text-sm">${message}</p>
                            </div>
                        </div>
                    </div>
                `;
                this.messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
                this.scrollToBottom();
            }

            showTypingIndicator() {
                const typingId = `typing-${Date.now()}`;
                const typingHtml = `
                    <div class="space-x-3 typing-indicator show" id="${typingId}">
                        <div class="w-8 h-8 relative top-10 rounded-full bg-green-600 flex items-center justify-center flex-shrink-0">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="3" fill="white" />
                                <path d="M12 2v4M12 18v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M2 12h4M18 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83" stroke="white" stroke-width="1.5" />
                            </svg>
                        </div>
                        <span class="text-xs text-gray-500 relative left-[26px]">
                            <b class="text-[13px] text-[#c4c3c7] text-medium">AI Assistant</b> typing...
                        </span>
                        <div class="max-w-[75%]">
                            <div class="bg-[#CB964F] text-white px-4 py-3 rounded-2xl rounded-tl-sm shadow-sm">
                                <div class="flex space-x-1">
                                    <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                                    <div class="w-2 h-2 bg-white rounded-full animate-pulse-slow"></div>
                                    <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                this.messagesContainer.insertAdjacentHTML('beforeend', typingHtml);
                this.scrollToBottom();
                return typingId;
            }

            removeTypingIndicator(typingId) {
                const typingElement = document.getElementById(typingId);
                if (typingElement) {
                    typingElement.remove();
                }
            }

            addAIResponse(response) {
                let contentHtml = '';
                
                switch (response.type) {
                    case 'text':
                        contentHtml = this.renderTextResponse(response.data);
                        break;
                    case 'table':
                        contentHtml = this.renderTableResponse(response.data);
                        break;
                    case 'timeline':
                        contentHtml = this.renderTimelineResponse(response.data);
                        break;
                    case 'chart':
                        contentHtml = this.renderChartResponse(response.data);
                        break;
                    default:
                        contentHtml = this.renderTextResponse({ content: response.original_sql_result || 'No data available' });
                }

                const messageHtml = `
                    <div class="space-x-3">
                        <div class="w-8 h-8 relative top-10 rounded-full bg-green-600 flex items-center justify-center flex-shrink-0">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="3" fill="white" />
                                <path d="M12 2v4M12 18v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M2 12h4M18 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83" stroke="white" stroke-width="1.5" />
                            </svg>
                        </div>
                        <span class="text-xs text-gray-500 relative left-[26px]">
                            <b class="text-[13px] text-[#c4c3c7] text-medium">AI Assistant</b> ${response.timestamp || this.getCurrentTime()}
                        </span>
                        <div class="max-w-[75%]">
                            ${contentHtml}
                        </div>
                    </div>
                `;
                
                this.messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
                this.scrollToBottom();

                // Initialize any charts after adding to DOM
                if (response.type === 'chart') {
                    setTimeout(() => this.initializeChart(response.data), 100);
                }
            }

            renderTextResponse(data) {
                return `
                    <div class="bg-[#CB964F] text-white px-4 py-3 rounded-2xl rounded-tl-sm shadow-sm">
                        <p class="text-sm leading-relaxed">${data.content}</p>
                        <div class="flex items-center justify-end mt-2 px-1">
                            <div class="flex space-x-2">
                                <button class="bg-[#7F5721] hover:bg-orange-700 text-white px-3 py-[8px] rounded-md text-xs flex items-center space-x-1 transition-colors" onclick="this.closest('.bg-\\[\\#CB964F\\]').querySelector('p').select()">
                                    <span><i class="bi bi-copy"></i> Copy</span>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }

            renderTableResponse(data) {
                const headers = data.headers || Object.keys(data.rows?.[0] || {});
                const rows = data.rows || [];

                let tableRows = '';
                rows.forEach(row => {
                    let rowHtml = '<tr class="bg-white border-b">';
                    headers.forEach((header, index) => {
                        const cellData = typeof row === 'object' ? row[header] || row[Object.keys(row)[index]] : row;
                        if (index === 0) {
                            rowHtml += `<th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">${cellData}</th>`;
                        } else {
                            rowHtml += `<td class="px-6 py-4">${cellData}</td>`;
                        }
                    });
                    rowHtml += '</tr>';
                    tableRows += rowHtml;
                });

                return `
                    <div class="border-[#CB964F] border-2 text-black py-3 rounded-2xl rounded-tl-sm shadow-sm">
                        <div class="mt-1">
                            <div class="flex flex-col px-5">
                                <p class="text-md font-medium leading-relaxed">${data.title || 'Data Table'}</p>
                            </div>
                            <div class="relative overflow-x-auto mt-4">
                                <table class="w-full text-sm text-left text-gray-500">
                                    <thead class="text-xs text-[#4B5563] font-light uppercase bg-[#F9FAFB] h-[50px]">
                                        <tr>
                                            ${headers.map(header => `<th scope="col" class="px-6 py-3">${header}</th>`).join('')}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${tableRows}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `;
            }

            renderTimelineResponse(data) {
                const events = data.events || [];
                let timelineHtml = '';

                events.forEach(event => {
                    timelineHtml += `
                        <li class="mb-10 ms-6 flex" style="margin-left:50px">
                            <div class="w-full">
                                <span class="absolute flex items-center justify-center w-12 h-12 rounded-full -start-6">
                                    <svg width="35" height="35" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="17.5" cy="17.5" r="17.5" fill="#1E3A8A" />
                                        <circle cx="17.5" cy="17.5" r="8.75" fill="white" />
                                    </svg>
                                </span>
                                <h3 class="flex items-center mb-1 text-lg font-semibold text-black">
                                    ${event.title}
                                </h3>
                                <time class="block mb-2 text-sm font-normal leading-none text-gray-400">
                                    Date: ${event.date}
                                </time>
                                <p class="mb-4 text-base font-normal text-gray-500">
                                    Source: ${event.source || 'Unknown'}
                                </p>
                            </div>
                            <span class="bg-[#14AE5C] text-white rounded-lg text-xs font-medium px-2.5 py-0.5 self-center">
                                ${event.status || 'Active'}
                            </span>
                        </li>
                    `;
                });

                return `
                    <div class="border-[#CB964F] border-2 text-black rounded-2xl rounded-tl-sm shadow-sm p-[40px]">
                        <h2 class="text-xl font-semibold text-gray-800 mb-8">${data.title || 'Timeline'}</h2>
                        <ol class="relative border-s border-gray-200">
                            ${timelineHtml}
                        </ol>
                    </div>
                `;
            }

            renderChartResponse(data) {
                const chartId = `chart-${Date.now()}`;
                
                if (data.chart_type === 'doughnut') {
                    return `
                        <div class="border-[#CB964F] border-2 text-black rounded-2xl rounded-tl-sm shadow-sm p-[40px]">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h2 class="text-xl font-semibold text-gray-800 mb-8">${data.title || 'Chart'}</h2>
                                    <div class="space-y-6" id="legend-${chartId}">
                                        <!-- Legend will be populated by JavaScript -->
                                    </div>
                                </div>
                                <canvas id="${chartId}" width="300" height="300"></canvas>
                            </div>
                        </div>
                    `;
                } else {
                    return `
                        <div class="border-[#CB964F] border-2 text-black rounded-2xl rounded-tl-sm shadow-sm p-[40px]">
                            <div class="flex items-center justify-between mb-8">
                                <h2 class="text-xl font-semibold text-gray-800">${data.title || 'Chart'}</h2>
                            </div>
                            <div class="h-80 mb-8">
                                <canvas id="${chartId}"></canvas>
                            </div>
                        </div>
                    `;
                }
            }

            initializeChart(data) {
                const chartElements = this.messagesContainer.querySelectorAll('canvas[id^="chart-"]');
                const latestChart = chartElements[chartElements.length - 1];
                
                if (!latestChart) return;

                const ctx = latestChart.getContext('2d');

                if (data.chart_type === 'doughnut') {
                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                data: data.values,
                                backgroundColor: data.colors,
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: false,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            }
                        }
                    });

                    // Create custom legend
                    const legendContainer = document.getElementById(`legend-${latestChart.id}`);
                    if (legendContainer) {
                        let legendHtml = '';
                        data.labels.forEach((label, index) => {
                            legendHtml += `
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 rounded-full mr-3" style="background-color: ${data.colors[index]}"></div>
                                        <span class="text-gray-600">${label}</span>
                                    </div>
                                    <span class="text-gray-400 font-medium">${data.values[index]}</span>
                                </div>
                            `;
                        });
                        legendContainer.innerHTML = legendHtml;
                    }
                } else {
                    // Bar chart
                    const datasets = data.datasets ? Object.keys(data.datasets).map(key => ({
                        label: key,
                        data: data.datasets[key],
                        backgroundColor: '#4F46E5',
                        borderRadius: 4,
                        maxBarThickness: 40
                    })) : [{
                        data: data.values || [],
                        backgroundColor: '#4F46E5',
                        borderRadius: 4,
                        maxBarThickness: 40
                    }];

                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.labels,
                            datasets: datasets
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: datasets.length > 1 }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        color: '#9ca3af',
                                        font: { size: 12 }
                                    },
                                    grid: {
                                        color: '#f3f4f6',
                                        drawBorder: false
                                    }
                                },
                                x: {
                                    ticks: {
                                        color: '#9ca3af',
                                        font: { size: 12 }
                                    },
                                    grid: {
                                        display: false,
                                        drawBorder: false
                                    }
                                }
                            }
                        }
                    });
                }
            }

            addErrorMessage(error) {
                const messageHtml = `
                    <div class="space-x-3">
                        <div class="w-8 h-8 relative top-10 rounded-full bg-red-600 flex items-center justify-center flex-shrink-0">
                            <i class="bi bi-exclamation-triangle text-white text-xs"></i>
                        </div>
                        <span class="text-xs text-gray-500 relative left-[26px]">
                            <b class="text-[13px] text-[#c4c3c7] text-medium">Error</b> ${this.getCurrentTime()}
                        </span>
                        <div class="max-w-[75%]">
                            <div class="bg-red-500 text-white px-4 py-3 rounded-2xl rounded-tl-sm shadow-sm">
                                <p class="text-sm leading-relaxed">${error}</p>
                            </div>
                        </div>
                    </div>
                `;
                this.messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
                this.scrollToBottom();
            }

            getCurrentTime() {
                const now = new Date();
                return now.toLocaleDateString('en-US', { 
                    day: 'numeric', 
                    month: 'short' 
                }) + ' ▪ ' + now.toLocaleTimeString('en-US', { 
                    hour: 'numeric', 
                    minute: '2-digit',
                    hour12: true 
                });
            }

            scrollToBottom() {
                setTimeout(() => {
                    this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
                }, 100);
            }
        }

        // Initialize chat interface when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            new ChatInterface();
        });
    </script>
</body>
</html>