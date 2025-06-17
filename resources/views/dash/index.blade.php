<!-- views/dashboard.blade.php -->
@extends('dash.layouts.app')

@section('page-title', 'Staff Performance Dashboard')

@section('content')
    <style>
        #chat_data>div {
            padding: 0px !important;
        }

        .flowchart {
            transform: unset !important;
        }

        .res>ol>li {
            gap: 20px;
            display: FLEX;
            flex-direction: column;
        }

        .res>ol {
            gap: 20px;
            display: flex;
            flex-direction: column;
        }

        .template-skeleton {
            animation: pulse 1.5s ease-in-out infinite alternate;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            border-radius: 8px;
            margin: 16px 0;
        }

        .skeleton-table {
            height: 200px;
            width: 100%;
        }

        .skeleton-chart {
            height: 300px;
            width: 100%;
        }

        .skeleton-diagram {
            height: 250px;
            width: 100%;
        }

        @keyframes pulse {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        .template-placeholder {
            background: linear-gradient(90deg, #CB964F 25%, #B8854A 50%, #CB964F 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
            border-radius: 8px;
            padding: 16px;
            margin: 16px 0;
            border: 1px solid #A67843;
            position: relative;
            overflow: hidden;
        }

        .template-placeholder::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg,
                    transparent,
                    rgba(255, 255, 255, 0.4),
                    transparent);
            animation: shimmer 2s infinite;
        }

        .template-placeholder::after {
            content: '';
            display: block;
            width: 100%;
            height: 200px;
            background:
                linear-gradient(#A67843 12px, transparent 12px),
                linear-gradient(90deg, #A67843 80px, transparent 80px),
                linear-gradient(#A67843 12px, transparent 12px),
                linear-gradient(90deg, #A67843 120px, transparent 120px),
                linear-gradient(#A67843 12px, transparent 12px),
                linear-gradient(90deg, #A67843 60px, transparent 60px);
            background-size:
                100% 30px,
                100% 30px,
                100% 30px,
                100% 30px,
                100% 30px,
                100% 30px;
            background-position:
                0 20px,
                0 20px,
                0 60px,
                0 60px,
                0 100px,
                0 100px;
            background-repeat: no-repeat;
            border-radius: 4px;
            margin-top: 12px;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -100% 0;
            }
        }

        @keyframes shimmer {
            0% {
                left: -100%;
            }

            100% {
                left: 100%;
            }
        }
    </style>
    <div class="flex h-screen overflow-hidden">
        <div style="max-width:25%;min-width:25%" class="flex flex-col w-[25%]   h-full bg-[#E8EAEE] flex-shrink-0">
            <div class="px-2 w-full mt-4">
                <button id="newchat"
                    class="shadow shadow-md flex justify-between w-full p-3 items-center bg-white rounded-lg">
                    <span class="font-bold">NEW CHAT</span>
                    <svg width="18" height="21" viewBox="0 0 18 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M6 2C6 1.46957 6.21071 0.960859 6.58579 0.585786C6.96086 0.210714 7.46957 0 8 0H10C10.5304 0 11.0391 0.210714 11.4142 0.585786C11.7893 0.960859 12 1.46957 12 2V6H16C16.5304 6 17.0391 6.21071 17.4142 6.58579C17.7893 6.96086 18 7.46957 18 8V10C18 10.5304 17.7893 11.0391 17.4142 11.4142C17.0391 11.7893 16.5304 12 16 12H12V16C12 16.5304 11.7893 17.0391 11.4142 17.4142C11.0391 17.7893 10.5304 18 10 18H8C7.46957 18 6.96086 17.7893 6.58579 17.4142C6.21071 17.0391 6 16.5304 6 16V12H2C1.46957 12 0.960859 11.7893 0.585786 11.4142C0.210714 11.0391 0 10.5304 0 10V8C0 7.46957 0.210714 6.96086 0.585786 6.58579C0.960859 6.21071 1.46957 6 2 6H6V2Z"
                            fill="black" />
                    </svg>
                </button>
            </div>
            <div class="flex px-2 w-full gap-3 mt-9">
                <div class="relative mb-6 w-full">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                        <svg width="12" height="11" viewBox="0 0 12 11" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M5.47222 8.625C7.73506 8.625 9.56944 6.87611 9.56944 4.71875C9.56944 2.56139 7.73506 0.8125 5.47222 0.8125C3.20939 0.8125 1.375 2.56139 1.375 4.71875C1.375 6.87611 3.20939 8.625 5.47222 8.625Z"
                                stroke="#575B65" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M11.2083 10.1878L8.36758 7.47949" stroke="#575B65" stroke-width="1.2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <input type="text" id="searchdata"
                        class="bg-[#c6c7c7] border border-[#c6c7c7] text-black text-sm rounded-lg focus:ring-blue-500 focus:border-black block w-full ps-10 p-2.5"
                        placeholder="Search...">
                </div>
                <div class="flex-shrink-0">
                    <svg width="33" height="33" viewBox="0 0 26 25" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M0 8C0 3.58172 3.58172 0 8 0H18C22.4183 0 26 3.58172 26 8V17C26 21.4183 22.4183 25 18 25H8C3.58172 25 0 21.4183 0 17V8Z"
                            fill="#1E3A8A" />
                        <path d="M8.45 13.125H17.225M6.5 9.375H19.175M11.375 16.875H14.3" stroke="white" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
            </div>
            <div class="flex flex-col space-y-2 mt-[10px] overflow-y-auto flex-grow min-h-0" id="chat_data">
            </div>
        </div>
        <div style="max-width:75%;min-width:75%" class="w-[75%] flex flex-col h-full min-w-0">
            <div class="bg-white px-6 py-3 border-b border-black-200 flex items-center justify-between flex-shrink-0">
                <h1 class="text-lg font-medium text-gray-900"></h1>
                <div class="flex items-center">
                    <div class="relative">
                        <button onclick="toggleDropdown('userMenu')"
                            class="flex items-center border rounded-full p-1 text-gray-700 text-sm font-medium hover:text-gray-900">
                            <div
                                class="h-8 w-8 rounded-full bg-yellow-500 flex items-center justify-center text-white text-xs font-bold">
                                {{ Auth::user()->name[0] }}
                            </div>
                            <span class="ml-2">{{ Auth::user()->name }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 text-gray-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="userMenu"
                            class="dropdown-content absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 border border-gray-200">
                            <a href="{{ route('user.settings') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Account Settings</a>
                            <a href="" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Theme
                                Preferences</a>
                            <div class="border-t border-gray-100 my-1"></div>
                            <a href="{{ route('user.logout') }}"
                                class="block px-4 py-2 text-sm text-pink-600 hover:bg-gray-100">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex flex-col flex-grow relative min-h-0 overflow-hidden w-[85%] mx-auto" id="chats">
                <div class="flex-1 overflow-y-auto overflow-x-hidden px-6 py-4 space-y-4"
                    style="max-height: calc(100vh - 160px);" id="chatContainer">
                </div>
                <div class="absolute bottom-6 left-6 right-6">
                    <form id="sendMessage" class="flex justify-between bg-[#CB964F] p-3 rounded-lg shadow-lg max-w-full"
                        id="chatbox">
                        <div class="flex gap-3 w-full min-w-0">
                            <div class="flex-shrink-0 cursor-pointer" onclick="suggest()">
                                <svg width="32" height="32" viewBox="0 0 32 32" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <rect width="32" height="32" rx="6" fill="#7F5721" />
                                    <path
                                        d="M11.4294 9.37547C9.73749 9.71359 8.46312 10.988 8.12499 12.6798C7.78687 10.988 6.51249 9.71359 4.82062 9.37547C6.51249 9.03734 7.78687 7.76297 8.12499 6.07109C8.46312 7.76297 9.73749 9.03734 11.4294 9.37547ZM19.8794 9.12984C18.4419 8.17297 18.0562 8.17297 16.6206 9.12984C17.5775 7.69234 17.5775 7.30672 16.6206 5.87109C18.0581 6.82797 18.4437 6.82797 19.8794 5.87109C18.9225 7.30672 18.9225 7.69422 19.8794 9.12984ZM12.3794 19.1298C10.9419 18.173 10.5562 18.173 9.12062 19.1298C10.0775 17.6923 10.0775 17.3067 9.12062 15.8711C10.5581 16.828 10.9437 16.828 12.3794 15.8711C11.4225 17.3067 11.4225 17.6942 12.3794 19.1298ZM23.0756 14.9511C21.7981 14.0992 21.4506 14.0992 20.175 14.9511C21.0269 13.6736 21.0269 13.3261 20.175 12.0505C21.4525 12.9023 21.8 12.9023 23.0756 12.0505C22.2237 13.328 22.2237 13.673 23.0756 14.9511Z"
                                        fill="#EEEEEE" />
                                    <path
                                        d="M13.6007 13.1585L13.1588 13.6005C12.6706 14.0886 12.6706 14.8801 13.1588 15.3683L22.8815 25.091C23.3697 25.5791 24.1611 25.5791 24.6493 25.091L25.0912 24.649C25.5794 24.1609 25.5794 23.3694 25.0912 22.8813L15.3685 13.1585C14.8803 12.6704 14.0889 12.6704 13.6007 13.1585Z"
                                        fill="#EEEEEE" />
                                    <path
                                        d="M13.1585 13.6005L13.6005 13.1585C13.8349 12.9241 14.1528 12.7924 14.4844 12.7924C14.8159 12.7924 15.1338 12.9241 15.3683 13.1585L16.6941 14.4844L14.4844 16.6941L13.1585 15.3683C12.9241 15.1338 12.7924 14.8159 12.7924 14.4844C12.7924 14.1529 12.9241 13.8349 13.1585 13.6005Z"
                                        fill="#EEEEEE" />
                                </svg>
                            </div>
                            <input autocomplete="off" type="text" id="messageInput"
                                class="text-white placeholder-white focus:outline-none bg-[#CB964F] border-0 focus:ring-0 focus:border-0 w-full min-w-0"
                                placeholder="Ask me anything about your data...">
                        </div>
                        <button id="sendButton" type="submit" class="flex-shrink-0 ml-3">
                            <svg width="30" height="32" viewBox="0 0 30 32" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <rect x="0.5" y="0.5" width="29" height="31" rx="7.5" stroke="white" />
                                <path
                                    d="M23.2562 16.5168L10.6167 16.5168M9.56359 9.11278L22.8189 15.5396C23.6352 15.9354 23.6352 17.0983 22.8189 17.494L9.56359 23.9209C8.65552 24.3612 7.69032 23.4329 8.09482 22.5084L10.5257 16.9521C10.6471 16.6746 10.6471 16.359 10.5257 16.0815L8.09482 10.5253C7.69032 9.60073 8.65551 8.6725 9.56359 9.11278Z"
                                    stroke="white" stroke-linecap="round" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .dt-length>select {
            background: white;
        }

        .dt-search>input {
            background: white;
        }

        .pagination>a {
            background: white;
        }

        html,
        body {
            overflow-x: hidden;
        }

        #input-group-1 {
            height: 33px;
            border-radius: 8px;
            font-size: 13px;
        }

        .active-chat {
            background-color: #CB964F;
            color: white;
        }

        .active-chat>div>span {
            color: white;
        }

        .active-chat>p {
            color: white;
        }

        #chatbox {
            box-shadow: 0px 19px 29px rgba(30, 31, 34, 0.2);
        }

        #chatContainer * {
            max-width: 100%;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .relative.overflow-x-auto {
            overflow-x: auto;
            max-width: 100%;
        }

        .mermaid-diagram {
            max-width: 100% !important;
            height: auto !important;
        }

        .loading-dots {
            display: inline-block;
        }

        .loading-dots::after {
            content: '';
            animation: dots 1.5s infinite;
        }

        @keyframes dots {

            0%,
            20% {
                content: '';
            }

            40% {
                content: '.';
            }

            60% {
                content: '..';
            }

            80%,
            100% {
                content: '...';
            }
        }

        .filter-btn {
            transition: all 0.2s ease;
        }

        .filter-btn.active {
            background-color: #1E3A8A !important;
            color: white !important;
        }

        .filter-btn:not(.active) {
            background-color: #E1E1E1;
            color: #717171;
        }

        /* Template Content Container */
        .template-content {
            border-radius: 3px;
            /* padding: 16px; */
            margin: 16px 0;
            background-color: #ffffff;
            position: relative;
            overflow: hidden;
        }

        /* Skeleton loader for when template is loading */
        .template-content:empty::before {
            content: '';
            display: block;
            width: 50%;
            height: 50px;
            background: linear-gradient(90deg,
                    #f0f0f0 25%,
                    #e0e0e0 50%,
                    #f0f0f0 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s infinite;
            border-radius: 4px;
        }
    </style>
@endsection

{{-- <script src="https://cdn.jsdelivr.net/npm/mermaid@11.2.1/dist/mermaid.min.js"></script> --}}
<script src="https://unpkg.com/@panzoom/panzoom@4.5.1/dist/panzoom.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>


@php
    $key = hex2bin(env('SODIUM_KEY')); // 32-byte key from env
    $nonceUser = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
    $nonceChat = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

    $encryptedUser = sodium_crypto_secretbox(Auth::id(), $nonceUser, $key);
    $encryptedChat = sodium_crypto_secretbox($id, $nonceChat, $key);

    // combine nonce + ciphertext then base64 encode for safe JS embedding
    $user_id_encrypted = base64_encode($nonceUser . $encryptedUser);
    $chat_id_encrypted = base64_encode($nonceChat . $encryptedChat);
@endphp

<script>
    let messageCount = 0;
    let tableData = {};
    let currentChatId = null;
    let isLoadingMore = false;
    let currentPage = 1;
    let loadedMessageIds = new Set(); // Track message IDs to prevent duplication
    let hasMoreMessages = false; // Track if more messages are available
    let totalMessages = 0; // Track total messages in the conversation
    let sidebarUpdateInterval = null;
    let isUserInteracting = false;



    async function loadSidebarChatsOnly() {
        try {
            const response = await fetch('{{ route('user.sidebar_chats') }}', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

            const data = await response.json();
            const chatListContainer = document.querySelector('#chat_data');
            chatListContainer.innerHTML = '';

            if (data.chats && data.chats.length > 0) {
                data.chats.forEach(chat => {
                    const chatItem = document.createElement('div');
                    chatItem.className = 'flex px-2 w-full gap-3 cursor-pointer chat-item';
                    chatItem.dataset.chatId = chat.uuid;
                    chatItem.innerHTML = `
                    <div class="flex w-full flex-col cursor-pointer px-4 py-4">
                        <div class="flex justify-between">
                            <h3 class="font-bold">${chat.title}</h3>
                            <div class="flex gap-2 items-center">
                                <span class="text-[#7D7272] text-[12px]">${new Date(chat.updated_at).toLocaleString('en-US', { day: 'numeric', month: 'short', hour: 'numeric', minute: '2-digit', hour12: true })}</span>
                                <a href="javascript:;" onclick="deleteChat('${chat.uuid}', event)" class="text-red-500 hover:text-red-700 transition-colors ml-2" title="Delete chat">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </div>
                        </div>
                        <p class="text-[#7B7878] mt-3">${chat.description}</p>
                    </div>
                `;

                    // Add click event for chat selection
                    chatItem.addEventListener('click', (e) => {
                        if (!e.target.closest('.fa-trash') && !e.target.closest(
                                '[onclick*="deleteChat"]')) {
                            selectChatWithUrlUpdate(chat.uuid);
                        }
                    });

                    chatListContainer.appendChild(chatItem);
                });
            } else {
                // chatListContainer.innerHTML = '<p class="px-2 text-sm text-gray-500">No chats found.</p>';
            }
            selectChatWithUrlUpdate(currentChatId); // Select chat if ID exists

            // Show placeholder in chat container
            const chatContainer = document.getElementById('chatContainer');
            // placeholder()

        } catch (error) {
            console.error('Error loading sidebar chats:', error);
            const chatListContainer = document.querySelector('#chat_data');
            // chatListContainer.innerHTML = '<p class="px-2 text-sm text-red-500">Error loading chats.</p>';
        }
    }

    function selectChatWithUrlUpdate(chatId) {
        if (chatId == "dashboard") {
            placeholder()
            return false;
        }
        currentChatId = chatId;
        updateUrlChatId(chatId);
        selectChat(chatId);
    }
    document.addEventListener('DOMContentLoaded', async () => {

        $('#searchdata').on('input', function() {
            let searchTerm = $(this).val().trim().toLowerCase();

            // Get all chat items
            $('#chat_data > div').each(function() {
                let chatTitle = $(this).find('h3').text().toLowerCase();
                let chatDescription = $(this).find('p').text().toLowerCase();

                // Check if search term matches title or description
                if (searchTerm === '' ||
                    chatTitle.includes(searchTerm) ||
                    chatDescription.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    })

    function getRandomId() {
        return Math.floor(100000000 + Math.random() * 900000000);
    }
    var id = getRandomId();

    function addUserMessage(message) {
        const chatContainer = document.getElementById('chatContainer');
        const timestamp = getCurrentTimestamp();

        const userMessageHTML = `
         <div class="flex items-start justify-start space-x-3">
    <div class="max-w-[95%] min-w-[200px] flex flex-col items-end space-x-3">
        <div class="w-8 h-8 relative top-9 right-3 self-start rounded-full bg-yellow-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
            U
        </div>
        <span class="text-xs text-gray-500 relative self-start left-5">
            <b class="text-[13px] text-[#c4c3c7] text-medium">You</b> ${timestamp}
        </span>
        <div  class="w-[-webkit-fill-available] text-black px-5 py-3 rounded-2xl rounded-tl-sm  flex justify-between gap-5 items-center">
            <p>${message}</p>
        </div>
    </div>
</div>
    `;
        chatContainer.insertAdjacentHTML('beforeend', userMessageHTML);
        chatContainer.scrollTop = chatContainer.scrollHeight;
        addaimessage()
    }


    // Add these variables at the top level to track template state
    let accumulatedMarkdown = '';
    let currentTemplate = null;
    let templateContent = '';
    let isInsideTemplate = false;
    let contentBeforeTemplate = '';
    let storedTemplates = new Map(); // Store templates by their ID

    const ws = new WebSocket('wss://api.reviewbod.com/ws');

    ws.onopen = () => {
        console.log('Connected to WebSocket');
        document.getElementById('status').textContent = 'Connected';
        document.getElementById('status').style.color = 'green';
    };








    // Function to replace template placeholders in content
    function replaceTemplatePlaceholders(content) {
        // Look for [TEMPLATE:id] patterns
        const templatePattern = /\[TEMPLATE:([a-f0-9-]+)\]/g;

        return content.replace(templatePattern, (match, templateId) => {
            const template = storedTemplates.get(templateId);
            if (template) {
                return renderTemplate(template);
            } else {
                return `<div class="template-error">Template not found: ${templateId}</div>`;
            }
        });
    }
    // Add these new variables at the top
    let templateSkeletons = new Map(); // Track active skeletons
    let templatePlaceholders = new Map(); // Track placeholder positions

    function createSkeletonLoader(templateType, skeletonId) {
        const skeletonClass = `skeleton-${templateType}`;
        return `
        <div id="${skeletonId}" class="template-skeleton ${skeletonClass}">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                <div style="width: 20px; height: 20px; background: #ddd; border-radius: 50%;"></div>
                <span style="color: #666;">Generating ${templateType}...</span>
            </div>
        </div>
    `;
    }
    // Helper function to generate random ID
    function generateRandomId() {
        return 'template_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }

    // Global variables for Google Charts management
    let googleChartsLoaded = false;
    let chartsLoadPromise = null;
    let pendingCharts = [];

    // Check if Google Charts is already loaded (in case it was loaded elsewhere)
    function checkExistingGoogleCharts() {
        if (typeof google !== 'undefined' &&
            google.charts &&
            google.visualization &&
            google.visualization.DataTable) {
            googleChartsLoaded = true;
            return true;
        }
        return false;
    }

    // Initialize Google Charts with multiple fallback strategies
    function initializeGoogleCharts() {
        // If already loaded or loading, return existing promise
        if (googleChartsLoaded || chartsLoadPromise) {
            return chartsLoadPromise || Promise.resolve();
        }

        // Check if already loaded
        if (checkExistingGoogleCharts()) {
            console.log('Google Charts already loaded');
            return Promise.resolve();
        }

        // Check if Google Charts script is available
        if (typeof google === 'undefined' || !google.charts) {
            return Promise.reject(new Error('Google Charts library not found'));
        }

        console.log('Loading Google Charts...');

        chartsLoadPromise = new Promise((resolve, reject) => {
            let resolved = false;

            const onSuccess = () => {
                if (!resolved) {
                    resolved = true;
                    googleChartsLoaded = true;
                    console.log('Google Charts loaded successfully');

                    // Process any pending charts
                    processPendingCharts();
                    resolve();
                }
            };

            const onError = (error) => {
                if (!resolved) {
                    resolved = true;
                    console.error('Google Charts loading failed:', error);
                    reject(error);
                }
            };

            try {
                // Primary loading method
                google.charts.load('current', {
                    'packages': ['corechart', 'bar', 'line', 'scatter', 'area', 'histogram', 'combo'],
                    'callback': onSuccess
                });

                // Backup callback
                google.charts.setOnLoadCallback(onSuccess);

                // Timeout fallback
                setTimeout(() => {
                    if (!resolved) {
                        // Check one more time if it loaded without callback
                        if (checkExistingGoogleCharts()) {
                            onSuccess();
                        } else {
                            onError(new Error('Google Charts loading timeout'));
                        }
                    }
                }, 15000); // 15 second timeout

            } catch (error) {
                onError(error);
            }
        });

        return chartsLoadPromise;
    }

    // Process pending charts after Google Charts loads
    function processPendingCharts() {
        while (pendingCharts.length > 0) {
            const {
                res,
                containerId
            } = pendingCharts.shift();
            renderChart(res, containerId);
        }
    }

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelector('button#newchat').addEventListener('click', createNewChat);

        loadSidebarChatsOnly()
        initializeGoogleCharts().catch(error => {
            console.error('Failed to initialize Google Charts:', error);
        });
    });


    async function createNewChat() {
        // Reset chat state
        currentChatId = null;
        const chatContainer = document.getElementById('chatContainer');
        chatContainer.innerHTML = '';
        placeholder();

        const pathParts = window.location.pathname.split('/');
        const chatIdFromPath = pathParts[pathParts.length - 1];
        // if(!chatIdFromPath){

        // Reset URL to /dashboard
        const newUrl = `${window.location.origin}/dashboard`;
        window.history.pushState({}, '', newUrl);
        // }


        // Update sidebar to reflect current chats without selecting any
        // await updateSidebarOnly();
        $(".active-chat").removeClass("active-chat")
    }

    function renderChart(res, containerId) {
        console.log(`Attempting to render chart for container ${containerId} with template:`, res);

        // Validate inputs
        if (!res || res.template_type !== 'chart' || !res.structure) {
            console.error(`Invalid chart response for ${containerId}:`, res);
            return showChartError(containerId, 'Invalid chart data or template type');
        }

        const container = document.getElementById(containerId);
        if (!container) {
            console.error(`Container with ID ${containerId} not found in DOM`);
            return showChartError(containerId, `Container with ID ${containerId} not found`);
        }

        try {
            const {
                chartType,
                options,
                columns,
                chart_data,
                column_types
            } = res.structure;

            // Validate required fields
            if (!chartType) {
                throw new Error('Chart type not specified');
            }
            if (!chart_data || !Array.isArray(chart_data) || chart_data.length === 0) {
                throw new Error('No chart data available');
            }
            if (!columns || !Array.isArray(columns) || columns.length !== 2) {
                throw new Error(
                    `Invalid columns configuration: exactly 2 columns required, got ${columns ? columns.length : 'none'}`
                );
            }
            if (!column_types || typeof column_types !== 'object') {
                throw new Error('Invalid column_types configuration');
            }

            console.log(`Processing chart data for ${containerId}:`, {
                chartType,
                dataLength: chart_data.length
            });

            // Clear container and reset classes
            container.innerHTML = "";
            container.className = "";

            // Create DataTable
            const data = new google.visualization.DataTable();

            // Add columns
            columns.forEach(column => {
                if (!column.type || !column.label || !column.data) {
                    throw new Error(`Invalid column configuration: ${JSON.stringify(column)}`);
                }
                data.addColumn(column.type, column.label);
            });

            // Process chart_data into rows
            const rows = chart_data.map((row, index) => {
                try {
                    return columns.map(column => {
                        const value = row[column.data];
                        const targetType = column_types[column.data] || column.type;

                        if (value === null || value === undefined || value === '') {
                            return targetType === 'number' ? 0 : '';
                        }

                        if (targetType === 'number') {
                            const numValue = parseFloat(value);
                            return isNaN(numValue) ? 0 : numValue;
                        } else if (targetType === 'date') {
                            const dateValue = new Date(value);
                            return isNaN(dateValue.getTime()) ? null : dateValue;
                        } else {
                            return String(value);
                        }
                    });
                } catch (rowError) {
                    console.warn(`Error processing row ${index} in ${containerId}:`, rowError, row);
                    return null;
                }
            }).filter(row => row !== null);

            if (rows.length === 0) {
                throw new Error('No valid data rows after processing');
            }

            data.addRows(rows);

            // Set default options
            const defaultOptions = {
                title: 'Chart',
                width: '100%',
                height: 400,
                backgroundColor: 'transparent',
                titleTextStyle: {
                    fontSize: 16,
                    bold: true
                },
                legend: {
                    position: 'bottom',
                    alignment: 'center'
                },
                animation: {
                    startup: true,
                    duration: 1000,
                    easing: 'out'
                },
                hAxis: {
                    title: columns[0].label || 'X Axis'
                },
                vAxis: {
                    title: columns[1].label || 'Y Axis'
                }
            };

            // Merge with provided options
            const finalOptions = Object.assign({}, defaultOptions, options || {});

            // Create chart
            const chartTypes = {
                'LineChart': google.visualization.LineChart,
                'BarChart': google.visualization.BarChart,
                'ColumnChart': google.visualization.ColumnChart,
                'PieChart': google.visualization.PieChart,
                'ScatterChart': google.visualization.ScatterChart,
                'AreaChart': google.visualization.AreaChart,
                'ComboChart': google.visualization.ComboChart,
                'Histogram': google.visualization.Histogram
            };
            console.log("chartTypechartTypechartTypechartType", chartType)

            const ChartClass = chartTypes[chartType] || google.visualization.ColumnChart;
            const chart = new ChartClass(container);

            // Draw the chart
            chart.draw(data, finalOptions);

            // Add resize handler
            const resizeHandler = () => {
                try {
                    chart.draw(data, finalOptions);
                } catch (resizeError) {
                    console.warn(`Error during chart resize for ${containerId}:`, resizeError);
                }
            };

            // Remove existing resize listener
            if (container._chartResizeListener) {
                window.removeEventListener('resize', container._chartResizeListener);
            }

            // Add new resize listener
            container._chartResizeListener = resizeHandler;
            window.addEventListener('resize', resizeHandler);

            console.log(`Chart ${chartType} rendered successfully in ${containerId}`);

        } catch (error) {
            console.error(`Error rendering chart for ${containerId}:`, error);
            showChartError(containerId, error.message);
        }
    }
    // Helper function to show chart errors consistently
    function showChartError(containerId, errorMessage) {
        console.log("containerId", containerId)
        const container = document.getElementById(containerId);
        if (container) {
            container.innerHTML = `
            <div class="p-4 text-red-600 border border-red-200 rounded bg-red-50">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="font-semibold">Chart Error</p>
                </div>
                <p class="text-sm">${errorMessage}</p>
            </div>
        `;
            container.className = "";
        }
    }

    function renderTable(res, containerId) {
          console.log("invalid table 1")
        if (!res || res.template_type !== 'table' || !res.structure) {
            return `<code>${res.message}</code>`;
        }

        const container = document.getElementById(containerId);
        if (!container) {
            // alert()
            console.log("invalid table 2")
            return;
        }

        console.log("Rendering table with structure:", res.structure);

        const {
            columns,
            data
        } = res.structure;
        const tableId = `table-${res.id}`;
        console.log(`Rendering table ${tableId} with ${data.length} rows and ${columns.length} columns`);

        const tableHTML = `
        <div class="bg-white">
            <table id="${tableId}" class="min-w-full divide-y divide-gray-200 table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        ${columns.map(col => `
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ${col.title}
                            </th>
                        `).join('')}
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    ${data.map(row => `
                        <tr class="hover:bg-gray-50">
                            ${columns.map(col => `
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ${row[col.data] || 'No Data'}
                                </td>
                            `).join('')}
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;

        container.innerHTML = tableHTML;
        //removce all class
          $(`#${containerId}`).removeClass('template-placeholder'); 
             container.className = "";
        container.className = "overflow-x-auto";
      

        // Initialize DataTable if jQuery and DataTables are available
        if (typeof $ !== 'undefined' && $.fn.DataTable) {
            $(`#${tableId}`).DataTable({
                responsive: true,
            });
        }
    }

    // Safe render function for charts

    function safeRenderChart(res, containerId) {
        console.log(`Initiating safe render for chart ${containerId}`);

        const container = document.getElementById(containerId);
        if (!container) {
            console.error(`Container ${containerId} not found, aborting render`);
            return showChartError(containerId, `Container ${containerId} not found`);
        }

        // Show loading spinner if container is empty or has placeholder
        if (!container.innerHTML || container.innerHTML.includes('template-placeholder')) {
            container.innerHTML = `
            <div class="p-4 text-gray-600 flex items-center justify-center">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mr-3"></div>
                <span>Loading chart...</span>
            </div>
        `;
        }

        // Set a timeout to detect stuck charts
        const renderTimeout = setTimeout(() => {
            console.error(`Chart rendering timed out for ${containerId}`);
            showChartError(containerId, 'Chart rendering timed out. Please try again.');
        }, 10000); // 10-second timeout

        if (googleChartsLoaded) {
            console.log(`Google Charts loaded, rendering chart ${containerId} immediately`);
            clearTimeout(renderTimeout);
            renderChart(res, containerId);
        } else {
            console.log(`Google Charts not loaded, queuing chart ${containerId}`);
            initializeGoogleCharts()
                .then(() => {
                    console.log(`Google Charts loaded, processing chart ${containerId}`);
                    clearTimeout(renderTimeout);
                    renderChart(res, containerId);
                })
                .catch((error) => {
                    console.error(`Failed to load Google Charts for ${containerId}:`, error);
                    clearTimeout(renderTimeout);
                    showChartError(containerId, `Failed to load chart: ${error.message}`);
                });

            // Add to pending queue with timeout check
            pendingCharts.push({
                res,
                containerId
            });
            // Ensure queue doesn't grow indefinitely
            if (pendingCharts.length > 10) {
                const oldChart = pendingCharts.shift();
                console.warn(`Removed old pending chart ${oldChart.containerId} from queue`);
                showChartError(oldChart.containerId, 'Chart rendering queue overflow');
            }
        }

        container.className = "";
    }
    // Legacy function with retry mechanism (kept for backward compatibility)
    function safeRenderChartLegacy(res, containerId, retryCount = 0) {
        if (typeof google !== 'undefined' && google.charts && googleChartsLoaded) {
            renderChart(res, containerId);
        } else if (retryCount < 10) {
            console.log('Google Charts not ready, retrying...', retryCount + 1);
            setTimeout(() => safeRenderChartLegacy(res, containerId, retryCount + 1), 500);
        } else {
            console.error('Google Charts failed to load after multiple attempts');
            const container = document.getElementById(containerId);
            if (container) {
                container.innerHTML = '<div class="p-4 text-red-600">Failed to load Google Charts library</div>';
                container.className = ""
            }
        }
    }

    // Updated main function
    function gettemplate(temp) {
        const pathParts = window.location.pathname.split('/');
        const chatIdFromPath = pathParts[pathParts.length - 1];
        $.post('{{ route('user.get_template') }}', {
            'des': temp.description,
            'id': temp.id,
            'sql': temp.sql,
            'chat_id': chatIdFromPath,
            '_token': '{{ csrf_token() }}'
        }).done((res) => {
            console.log('Template response:', res);

            // Double-check that container still exists before rendering
            const container = document.getElementById(temp.id);
            if (!container) {
                console.warn(`Container ${temp.id} no longer exists, skipping render`);
                return;
            }

            if (res.template_type == 'table') {
                console.log("Rendering table");
                renderTable(res, temp.id);
            } else if (res.template_type == 'chart') {
                console.log("Rendering chart-------");

                forceRenderChart(res, temp.id);

            }
        }).fail((error) => {
            console.error('Error fetching template:', error);
            const container = document.getElementById(temp.id);
            if (container) {
                container.innerHTML = `
                <div class="p-4 text-red-600 border border-red-200 rounded bg-red-50">
                    <p class="font-semibold">Error loading template</p>
                    <p class="text-sm mt-1">Please try again later</p>
                </div>
            `;
            }
        });
    }

    // Utility function to manually trigger chart rendering (useful for debugging)
    function forceRenderChart(res, containerId) {
        console.log('Force rendering chart:', res, containerId);
        if (googleChartsLoaded) {
            renderChart(res, containerId);
        } else {
            console.log('Google Charts not loaded, initializing...');
            initializeGoogleCharts().then(() => {
                renderChart(res, containerId);
            }).catch(error => {
                console.error('Failed to initialize Google Charts:', error);
            });
        }
    }

    // Debug function to check Google Charts status
    function debugGoogleCharts() {
        console.log('=== Google Charts Debug Info ===');
        console.log('typeof google:', typeof google);
        console.log('google.charts available:', typeof google !== 'undefined' && !!google.charts);
        console.log('google.visualization available:', typeof google !== 'undefined' && !!google.visualization);
        console.log('googleChartsLoaded:', googleChartsLoaded);
        console.log('chartsLoadPromise:', chartsLoadPromise);
        console.log('pendingCharts.length:', pendingCharts.length);

        if (typeof google !== 'undefined' && google.charts) {
            try {
                google.charts.load('current', {
                    'packages': ['corechart']
                });
                google.charts.setOnLoadCallback(() => {
                    console.log('Debug: Google Charts test load successful');
                });
            } catch (error) {
                console.error('Debug: Google Charts test load failed:', error);
            }
        }
    }

    // Add a Set to track processed templates


    function processNewTemplates(matches) {
        console.log(matches)
        matches.forEach((match) => {
            try {
                const jsonContent = match[1];
                const templateData = JSON.parse(jsonContent);
                const templateId = templateData.id;
                const randomId = () => crypto.randomUUID().replace(/-/g, '');


                // Only process if this is a truly new template
                if (!processedTemplates.has(templateId)) {
                    console.log("Processing new template:", templateId);

                    // Mark as processed immediately to prevent duplicates
                    processedTemplates.add(templateId);

                    // Create new placeholder with the actual template ID
                    const placeholder =
                        `<div class="template-placeholder simple" id='${templateId}' data-template-id="${templateId}"></div>`;

                    // Store both the placeholder and template data
                    templatePlaceholders.set(templateId, placeholder);
                    storedTemplates.set(templateId, templateData);

                    // Process the template
                    setTimeout(() => gettemplate(templateData), 500);
                }
            } catch (error) {
                console.error('Failed to parse template JSON:', error);
            }
        });
        console.log(matches)
    }

    // Separate function to replace templates with placeholders
    function replaceTemplatesWithPlaceholders(content, regex) {
        return content.replace(regex, (match, jsonContent) => {
            try {
                const templateData = JSON.parse(jsonContent);
                console.log("templateDatatemplateDatatemplateDatatemplateDatatemplateDatatemplateData: ", templateData)
                const templateId = templateData.id;
                return templatePlaceholders.get(templateId) || match;
            } catch (error) {
                                console.log("errrrorororororororor: ", error)

                return '<div class="template-error">❌ Template parsing error</div>';
            }
        });
    }

    // Separate function to handle incomplete templates
    function handleIncompleteTemplates(content) {
        const startTemplateMatches = [...content.matchAll(/\[START TEMPLATE\]/g)];
        const endTemplateMatches = [...content.matchAll(/\[END TEMPLATE\]/g)];

        // If we have more START than END templates, replace the last incomplete one
        if (startTemplateMatches.length > endTemplateMatches.length) {
            const lastStartIndex = content.lastIndexOf('[START TEMPLATE]');
            const incompleteTemplateId = 'incomplete-template';

            // Create incomplete template placeholder if it doesn't exist
            if (!templatePlaceholders.has(incompleteTemplateId)) {
                const incompletePlaceholder = `<div class="template-placeholder simple" id="${incompleteTemplateId}" data-template-id="${incompleteTemplateId}">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                    <div style="width: 20px; height: 20px; background: #ddd; border-radius: 50%;"></div>
                    <span style="color: #666;">Generating template...</span>
                </div>
            </div>`;
                templatePlaceholders.set(incompleteTemplateId, incompletePlaceholder);
            }

            content = content.substring(0, lastStartIndex) + templatePlaceholders.get(incompleteTemplateId);
        }

        return content;
    }

    // Add these variables at the top with your other globals
    const processedTemplates = new Set();
    // Add this variable at the top with your other globals
    let lastProcessedLength = 0;


// Add this helper function for reliable auto-scrolling
function autoScroll() {
     const chatContainer = document.getElementById('chatContainer');
    // Use requestAnimationFrame to ensure DOM updates are complete
    requestAnimationFrame(() => {
        chatContainer.scrollTop = chatContainer.scrollHeight;
    });
}

    ws.onmessage = (event) => {
        const data = JSON.parse(event.data);
        console.log('Received:', data);

        const responseDiv = document.getElementById(`msg-${id}`);

        switch (data.type) {
            case 'stream_token':
                accumulatedMarkdown += data.token;

                // Only process new content that hasn't been processed yet
                let displayContent = accumulatedMarkdown;

                // Check if we have new complete templates to process
                const completeTemplateRegex = /\[START TEMPLATE\](.*?)\[END TEMPLATE\]/gs;

                // Find all matches
                const matches = [...accumulatedMarkdown.matchAll(completeTemplateRegex)];

                // Process only NEW complete templates (moved outside and optimized)
                processNewTemplates(matches);

                // Replace templates with placeholders
                displayContent = replaceTemplatesWithPlaceholders(displayContent, completeTemplateRegex);

                // Handle incomplete templates
                displayContent = handleIncompleteTemplates(displayContent);

                lastProcessedLength = displayContent.length;
                responseDiv.innerHTML = marked.parse(displayContent);
                           autoScroll();

                break;

            case 'error':
                responseDiv.innerHTML += `<p><b>ERROR</b>: ${data.message}</p>`;
                break;
            case 'new_chat_created':

                const chatListContainer = document.querySelector('#chat_data');

                currentChatId = data.chat_uuid;
                updateUrlChatId(currentChatId);


                const chatItem = document.createElement('div');
                chatItem.className = 'flex px-2 w-full gap-3 cursor-pointer chat-item';
                chatItem.dataset.chatId = currentChatId;
                chatItem.innerHTML = `
                    <div class="flex w-full flex-col cursor-pointer px-4 py-4 active-chat">
                        <div class="flex justify-between">
                            <h3 class="font-bold">${data.title}</h3>
                            <div class="flex gap-2 items-center">
                                <span class="text-[#7D7272] text-[12px]">${new Date(data.created_at).toLocaleString('en-US', { day: 'numeric', month: 'short', hour: 'numeric', minute: '2-digit', hour12: true })}</span>
                                <a href="javascript:;" onclick="deleteChat('${data.chat_uuid}', event)" class="text-red-500 hover:text-red-700 transition-colors ml-2" title="Delete chat">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </div>
                        </div>
                        <p class="text-[#7B7878] mt-3">${data.description}</p>
                    </div>
                `;

                // Add click event for chat selection
                chatItem.addEventListener('click', (e) => {
                    if (!e.target.closest('.fa-trash') && !e.target.closest(
                            '[onclick*="deleteChat"]')) {
                        selectChatWithUrlUpdate(data.chat_uuid);
                    }
                });

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
                        autoScroll();

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
                        autoScroll();

                break;
                // Replace your existing 'visualizations' case with this improved version:


            case 'classification':
                // responseDiv.innerHTML += `<p><b>CLASSIFICATION</b>: ${data.data}</p>`;
                break;

            case 'stream_end':
                // Reset the accumulated markdown for the next message
                accumulatedMarkdown = '';
                // Clear processed templates for the next message
                processedTemplates.clear();
                // Clear template placeholders for the next message
                templatePlaceholders.clear();
                // Generate new ID for next message
              
              $(`#msg-${id}`).append(`
    <div class="flex gap-3 justify-end text-xl">
        <i id="copy-${id}" class="bi bi-clipboard cursor-pointer" onclick="performAction('${id}', 'copy')"></i>
        <i id="like-${id}" class="bi bi-hand-thumbs-up cursor-pointer" onclick="performAction('${id}', 'like')"></i>
        <i id="unlike-${id}" class="bi bi-hand-thumbs-down cursor-pointer" onclick="performAction('${id}', 'unlike')"></i>
        <i id="bookmark-${id}" class="bi bi-bookmark cursor-pointer" onclick="performAction('${id}', 'bookmark')"></i>
    </div>
`);
                  id = getRandomId();
                break;
            default:
                responseDiv.innerHTML += `<p>${JSON.stringify(data, null, 2)}</p>`;
        }
    };



    ws.onclose = (data) => {
        console.log('Disconnected:', data);
        document.getElementById('status').textContent = 'Disconnected';
        document.getElementById('status').style.color = 'red';
    };

    ws.onerror = (error) => {
        console.error('WebSocket error:', error);
        document.getElementById('status').textContent = 'Connection Error';
        document.getElementById('status').style.color = 'red';
    };

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


    function addaimessage() {
        const chatContainer = document.getElementById('chatContainer');
        const timestamp = getCurrentTimestamp();

        const loadingHTML = `
        <div class="space-x-3" id="loading-message">
            <div class="w-8 h-8 relative top-9 right-3 self-start rounded-full  flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
               <svg width="32" height="32"  viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
<rect width="32" height="32" rx="8" fill="url(#pattern0_209_4633)"/>
<defs>
<pattern id="pattern0_209_4633" patternContentUnits="objectBoundingBox" width="1" height="1">
<use xlink:href="#image0_209_4633" transform="scale(0.00262467)"/>
</pattern>
<image id="image0_209_4633" width="381" height="381" preserveAspectRatio="none" xlink:href="data:image/png;base64,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"/>
</defs>
</svg>

            </div>
            <span class="text-xs text-gray-500 relative left-[26px]">
                <b class="text-[13px] text-[#c4c3c7] text-medium">Response</b> ${timestamp}
            </span>
            <div class="max-w-[75%]">
                <div id='msg-${id}' class="res flex flex-col gap-2 text-black px-5 py-3 rounded-2xl rounded-tl-sm shadow-sm">
               
            <div class="flex gap-2 items-center mt-4">
                <svg class="animate-spin" width="30" height="30" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 8.014C13.1837 8.12552 11.4708 8.89734 10.1841 10.1841C8.89734 11.4708 8.12552 13.1837 8.014 15H7.986C7.87469 13.1836 7.10294 11.4706 5.81615 10.1839C4.52936 8.89706 2.81639 8.12531 1 8.014V7.986C2.81639 7.87469 4.52936 7.10294 5.81615 5.81615C7.10294 4.52936 7.87469 2.81639 7.986 1H8.014C8.12552 2.81631 8.89734 4.52916 10.1841 5.81591C11.4708 7.10266 13.1837 7.87448 15 7.986V8.014Z" fill="#448AFF"/>
                </svg>
                <span>Getting Response..</span>
            </div>
                </div>
            </div>
        </div>
    `;
        chatContainer.insertAdjacentHTML('beforeend', loadingHTML);
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }



    function placeholder() {
        const chatContainer = document.getElementById('chatContainer');
        chatContainer.innerHTML = `
        <div class="flex flex-col items-center justify-center h-full px-8 py-16" id="placeholder">
            <div class="relative mb-8">
                <div class="w-20 h-20 rounded-full flex items-center justify-center">
                   <img src="/images/{{ Auth::user()->service }}.webp" />
                </div> 
            </div>
            
            <h3 class="text-2xl font-bold text-gray-800 mb-3">Ready to Analyze</h3>
            <p class="text-gray-600 text-center max-w-md leading-relaxed mb-6">
                Your {{ Auth::user()->service }} workspace is connected. Start a conversation to get AI insights on your tasks, priorities, and workflow patterns.
            </p>
            
            <div class="flex space-x-3">
                <div class="w-2 h-2 rounded-full animate-bounce" style="background: #CB964F;"></div>
                <div class="w-2 h-2 rounded-full animate-bounce" style="background: #CB964F; opacity: 0.7; animation-delay: 0.1s"></div>
                <div class="w-2 h-2 rounded-full animate-bounce" style="background: #CB964F; opacity: 0.5; animation-delay: 0.2s"></div>
            </div>
        </div>
    `;
    }



    async function loadChats() {
        if (currentChatId == "dashboard") {
            return false;
        }
        try {
            const response = await fetch('{{ route('user.chat_data') }}' + (currentChatId ?
                `?chat_id=${currentChatId}` : ''), {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

            const data = await response.json();
            const chatListContainer = document.querySelector('#chat_data');

            //   const chatListContainer = document.querySelector('#chat_data');
            if (chatListContainer) {
                chatListContainer.addEventListener('mouseenter', () => {
                    isUserInteracting = true;
                });

                chatListContainer.addEventListener('mouseleave', () => {
                    setTimeout(() => {
                        isUserInteracting = false;
                    }, 500);
                });
            }

            chatListContainer.innerHTML = '';

            if (data.chats && data.chats.length > 0) {
                data.chats.forEach(chat => {
                    const chatItem = document.createElement('div');
                    chatItem.className = 'flex px-2 w-full gap-3 cursor-pointer chat-item';
                    chatItem.dataset.chatId = chat.uuid;
                    chatItem.innerHTML = `
                    <div class="flex w-full flex-col cursor-pointer px-4 py-4 ${currentChatId == chat.uuid ? 'active-chat' : ''}">
                        <div class="flex justify-between">
                            <h3 class="font-bold">${chat.title}</h3>
                            <div class="flex gap-2 items-center">
                                <span class="text-[#7D7272] text-[12px]">${new Date(chat.updated_at).toLocaleString('en-US', { day: 'numeric', month: 'short', hour: 'numeric', minute: '2-digit', hour12: true })}</span>
                                <a href="javascript:;" onclick="deleteChat('${chat.uuid}', event)" class="text-red-500 hover:text-red-700 transition-colors ml-2" title="Delete chat">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </div>
                        </div>
                        <p class="text-[#7B7878] mt-3">${chat.description}</p>
                    </div>
                `;

                    // Add click event for chat selection
                    chatItem.addEventListener('click', (e) => {
                        if (!e.target.closest('.fa-trash') && !e.target.closest(
                                '[onclick*="deleteChat"]')) {
                            selectChatWithUrlUpdate(chat.uuid);
                        }
                    });

                    chatListContainer.appendChild(chatItem);
                });

                // If currentChatId is set, make sure it's selected
                if (currentChatId && currentChatId != "dashboard") {
                    selectChat(currentChatId);
                }
            } else {
                // chatListContainer.innerHTML = '<p class="px-2 text-sm text-gray-500">No chats found.</p>';
            }

            if (data.messages && data.messages.data) {
                currentPage = data.messages.current_page;
                hasMoreMessages = data.hasMore;
                totalMessages = data.messages.total || data.messages.data.length;
                loadedMessageIds.clear();
                displayMessages(data.messages.data);
            }
        } catch (error) {
            console.error('Error loading chats:', error);
            const chatListContainer = document.querySelector('#chat_data');
            chatListContainer.innerHTML = '<p class="px-2 text-sm text-red-500">Error loading chats.</p>';
        }
    }





    document.addEventListener("DOMContentLoaded", function() {
        //  placeholder()



        const chatContainer = document.getElementById('chatContainer');
        let scrollTimeout;
        chatContainer.addEventListener('scroll', () => {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                // Check if scrolled to top (within 50px threshold)
                const isAtTop = chatContainer.scrollTop <= 50;

                console.log('Scroll check:', {
                    scrollTop: chatContainer.scrollTop,
                    isAtTop: isAtTop,
                    isLoadingMore: isLoadingMore,
                    hasMoreMessages: hasMoreMessages,
                    totalMessages: totalMessages,
                    currentChatId: currentChatId
                });

                if (isAtTop && !isLoadingMore && hasMoreMessages && currentChatId &&
                    totalMessages >= 10) {
                    console.log('Loading more messages...');
                    loadMoreMessages();
                }
            }, 100);
        });

        // Get chat_id from URL path instead of query params
        const pathParts = window.location.pathname.split('/');
        const chatIdFromPath = pathParts[pathParts.length - 1];
        console.log('Chat ID from path:', chatIdFromPath);

        if (chatIdFromPath) {
            currentChatId = chatIdFromPath;
            loadChats(); // Load chats and messages
            loadSidebarChatsOnly();
            // updateUrlChatId(chatIdFromPath); // Update URL with chat ID

            // selectChat(chatIdFromPath);
            // selectChatWithUrlUpdate(chatIdFromPath); // Select chat and update URL
            // startSidebarUpdateInterval(); // Start interval for existing chat
        } else {
            //  startSidebarUpdateInterval(); 
            // No chat ID in URL, load sidebar and show placeholder
            loadSidebarChatsOnly();
            // Don't start interval yet - will start after first message
        }

        const path = window.location.pathname;

        // // Check if it matches /dashboard/:id
        // const hasIdAfterDashboard = /^\/dashboard\/\d+\/?$/.test(path);
        // if (!hasIdAfterDashboard) {
        //     placeholder()
        // }
        $("#sendMessage").submit(() => {
            event.preventDefault()
            const pathParts = window.location.pathname.split('/');
            const chatIdFromPath = pathParts[pathParts.length - 1];

            const messageInput = $("#messageInput").val();
            if (ws.readyState === WebSocket.OPEN) {
                addUserMessage(messageInput)
                $("#placeholder").remove()
                const message = {
                    query: messageInput,
                    user_id: @json($user_id_encrypted),
                    chat_id: chatIdFromPath == "dashboard" ? "" : chatIdFromPath,
                };
                ws.send(JSON.stringify(message));
                $("#messageInput").val("");
                console.log('Sent:', message);
            } else {
                console.error('WebSocket not connected');
            }
        })

    })



    function updateUrlChatId(chatId) {
        if (chatId) {
            // Update URL to use slash format: /chat/123
            const newUrl = `${window.location.origin}${window.location.pathname.replace(/\/\d+$/, '')}/${chatId}`;
            window.history.pushState({}, '', newUrl);
        } else {
            // Remove chat ID from URL
            const newUrl = window.location.pathname.replace(/\/\d+$/, '');
            window.history.pushState({}, '', newUrl);
        }
    }

    async function selectChat(chatId) {
        currentChatId = chatId;
        currentPage = 1;
        loadedMessageIds.clear();
        totalMessages = 0;
        hasMoreMessages = false;

        const chatItems = document.querySelectorAll('.chat-item');
        if (currentChatId == "dashboard") {
            currentChatId = "";
        }
        console.log('Selecting chat:', chatId, 'Current chat ID:', currentChatId);
        chatItems.forEach(item => {
            // alert(item.dataset.chatId, chatId)
            item.querySelector('div').classList.toggle('active-chat', item.dataset.chatId == chatId);
        });

        const chatContainer = document.getElementById('chatContainer');
        chatContainer.innerHTML = '';

        try {
            const response = await fetch(`{{ route('user.chat_data') }}?chat_id=${chatId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

            const data = await response.json();
            console.log('Chat data loaded:', data);

            if (data.messages && data.messages.data) {
                currentPage = data.messages.current_page;
                hasMoreMessages = data.hasMore || false;
                totalMessages = data.messages.total || data.messages.data.length;

                console.log('Pagination state:', {
                    currentPage,
                    hasMoreMessages,
                    totalMessages
                });

                displayMessages(data.messages.data);
            } else {
                chatContainer.innerHTML =
                    '<p class="text-sm text-gray-500 text-center">No messages in this chat.</p>';
                hasMoreMessages = false;
                totalMessages = 0;
            }
        } catch (error) {
            console.error('Error loading messages:', error);
            chatContainer.innerHTML = '<p class="text-sm text-red-500 text-center">Error loading messages.</p>';
            hasMoreMessages = false;
            totalMessages = 0;
            // placeholder();
            createNewChat()
        }
    }


    // Initialize processedTemplates as a Map if not already defined
    if (typeof window.processedTemplates === 'undefined') {
        window.processedTemplates = new Map();
    }

 function displayMessages(messages, prepend = false) {
    const chatContainer = document.getElementById('chatContainer');
    const fragment = document.createDocumentFragment();

    const newMessages = messages.filter(message => !loadedMessageIds.has(message.id));
    if (!newMessages.length && prepend) return;

//     if (prepend) {
    newMessages.reverse();
// }

    newMessages.forEach(message => {
        loadedMessageIds.add(message.id);
        const isUser = message.sender_type === 'user';
        const timestamp = new Date(message.created_at).toLocaleString('en-US', {
            day: 'numeric',
            month: 'short',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });

        let messageHTML = '';
        const messageDiv = document.createElement('div');

        if (isUser) {
            const userMessageHTML = `
         <div class="flex items-start justify-start space-x-3">
    <div class="max-w-[95%] min-w-[200px] flex flex-col items-end space-x-3">
        <div class="w-8 h-8 relative top-9 right-3 self-start rounded-full bg-yellow-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
            U
        </div>
        <span class="text-xs text-gray-500 relative self-start left-5">
            <b class="text-[13px] text-[#c4c3c7] text-medium">You</b> ${timestamp}
        </span>
        <div  class="w-[-webkit-fill-available] text-black px-5 py-3 rounded-2xl rounded-tl-sm  flex justify-between gap-5 items-center">
            <p>${message.message}</p>
        </div>
    </div>
</div>
    `;
            messageDiv.innerHTML = userMessageHTML;

        } else {
            // Process bot message with potential template
            let processedMessage = message.message;

            // Also check for multiple templates in the message text itself
            // Find all template markers in the message
            const templateMarkerRegex = /\[START TEMPLATE\](.*?)\[END TEMPLATE\]/gs;
            const templateMatches = [...processedMessage.matchAll(templateMarkerRegex)];

            if (templateMatches.length > 0) {
                console.log(`Found ${templateMatches.length} template markers in message`);
                console.log("templateMatches", templateMatches)

                templateMatches.forEach((match, index) => {
                    const fullMatch = match[0];
                    const templateContent = JSON.parse(match[1]);
                    console.log("templateContent", templateContent)

                    // Extract template ID from the content
                    const templateIdMatch = templateContent.id;

                    if (templateIdMatch) {
                        const templateId = templateContent.id;

                        window.processedTemplates.set(templateId, templateContent);
                        // Check if we already have this template data

                        // Replace the template marker with placeholder
                        const templatePlaceholder =
                            `<div id="${templateId}" class="template-placeholder">Loading template...</div>`;
                        processedMessage = processedMessage.replace(fullMatch, templatePlaceholder);
                    } else {
                        // If no template ID found, generate a unique one
                        const generatedId = `template-${Date.now()}-${index}`;
                        const templatePlaceholder =
                            `<div id="${generatedId}" class="template-placeholder">Loading template...</div>`;
                        processedMessage = processedMessage.replace(fullMatch, templatePlaceholder);
                        console.warn(
                            `No template ID found in template marker, generated: ${generatedId}`
                            );
                    }
                });
            }

     
console.log("my message data", message)
            const botMessageHTML = `
        <div class="space-x-3">
            <div class="w-8 h-8 relative top-9 right-3 self-start rounded-full  flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
      <svg width="32" height="32"  viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
<rect width="32" height="32" rx="8" fill="url(#pattern0_209_4633)"/>
<defs>
<pattern id="pattern0_209_4633" patternContentUnits="objectBoundingBox" width="1" height="1">
<use xlink:href="#image0_209_4633" transform="scale(0.00262467)"/>
</pattern>
<image id="image0_209_4633" width="381" height="381" preserveAspectRatio="none" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAX0AAAF9CAYAAADoebhRAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAr1SURBVHgB7d0xjFVVHsfxw8bYQDRazFg4FmNDsgO1YEGyC8kyjVpJtKHRbjvdaKKVJkuW7Xab1cYN0azNsjayiboJhdiPY0LjFAvNUEAk0NDgO08eAYSZNzMwc+75fT7J5DGhee/l5XvP/N+95+6aObV4swAQ4TcFgBiiDxBE9AGCiD5AENEHCCL6AEFEHyCI6AMEEX2AIKIPEET0AYKIPkAQ0QcIIvoAQUQfIIjoAwQRfYAgog8QRPQBgog+QBDRBwgi+gBBRB8giOgDBBF9gCCiDxBE9AGCiD5AENEHCCL6AEFEHyCI6AMEEX2AIKIPEET0AYKIPkAQ0QcIIvoAQUQfIIjoAwQRfYAgog8QRPQBgog+QBDRBwgi+gBBRB8giOgDBBF9gCCiDxBE9AGCiD5AENEHCCL6AEFEHyCI6AMEEX2AIKIPEET0AYKIPkAQ0QcIIvoAQUQfIIjoAwQRfYAgog8QRPQBgog+QBDRBwgi+gBBRB8giOgDBBF9gCCiDxBE9AGCiD5AENEHCCL6AEFEHyCI6AMEEX2AIKIPEET0AYKIPkAQ0QcIIvoAQUQfIIjoAwQRfYAgog8QRPQBgog+QBDRBwgi+gBBRB8giOgDBBF9gCCiDxBE9Il0+siJAolEn0gHZ/cVSCT6AEFEHyCI6AMEEX2AIKIPEET0AYKIPkAQ0QcIIvoAQUQfIIjoAwQRfYAgog8QRPQBgog+QBDRBwgi+gBBRB8giOh37M29L5eFp+YLTGvh6fnx54Z+iX7Hzq0ulU8OvSf8TKUG/5ND75dzl5YK/RL9ji1fWSnHz34o/KxrEvzjZz8oy5dXCv0S/c4JP+sR/CyiH0D4eRDBzyP6IYSfewl+JtEPIvxMCH4u0Q8j/Ah+NtEPJPy5BB/RDyX8eQSfSvSDCX8OwWdC9MMJf/8EnzuJPsLfMcHnXqLPmPD3R/C5H9HnNuHvh+DzIKLPXYR/+ASftYg+v5IQ/rrtdI8En/Xsmjm1eLPAfdTg1/DXA0A9ENA2wWcaVvo8kFHPcAg+0xJ91iT87RN8NkL0WZfwt0vw2SjRZyrC3x7BZzNEn6kJfzsEn80SfTZE+Hee4LMVos+GCf/OEXy2SvTZFOHffoLPw+DiLLZkHPxdRYS2gfeah0H0AYIY7wAEEX2AIKIPEET0AYKIPkAQ0QcIIvoAQUQfIIjoAwQRfYAgog8QRPQBgog+QBDRBwgi+gBBRB8gyGMFOlbvNnVwdl+Z2zNT5nY/M3588vE9t///wrXV8ePylR9H/75UfriyUr5d/b5Ar0Sfrjzx+O6yOHegvDp/eBz8+vta5nbPjB/rgWHi6o3r4/CfufBdOXPxu/Hv0Au3S6QLc3tmy7FR6N/c+9K6od+IGvwa/pNLn93+qwCGTPQZtBr4t/e/Po79o/b5ytfiz+CJPoP15t6XR8F/7aGu7NdTg1/DXw8AMESiz+DUyP/z0Pt3zeG3m1U/Q+WUTQalzu7/t/j3HQ1+Vb8oPn34z+PnA0Mi+gzGwtPzo+D/bXzaZQtq8L8ZPZ96lhAMhegzCDX4pw+f2Nb5/TSeHD2ffx85IfwMhujTvLqibjH4E5PwG/UwBKJP01oP/kQNf53xt/48QfRpWj0ls5UZ/nrqAaqeVQQtE32aVc+QqT9DUs8qqtcPQKtEnybVVXO90naI3trmC8ZgI0SfJg1prHOvOt//00APWPRP9GlOXeUPbaxzrzf2vuRsHpok+jSnrvJ7cGz+9wVaI/o0pY5Gjj57oPTgjdEXumb7tEb0acofRsHvJZS/HMBeKNAS0acpR5/rK5LHnj9SoCWiT1NenNlfevJbe/LQGNGnGfXCpt5m4HXEs/DU8wVaIfo0o9edKg/OLhRohejTjKFejLWeXl8XwyT6NKPXMcjc7mcKtEL04RF7breVPu0QfZrR67YFLtCiJY8VmnH6yIkdv+H3nc6tLpVXvnq3MCw+R6xF9BvyylfvFNgqnyPWYrxDM366ca306ML11QKtEH2aIY7w6Ik+zbhwrc/oL19ZKdAK0acZF65dKj3q9XUxTKJPM7699H3p0Q9W+jRE9GnGD5dXytUb10tPfhq9nm9X+zyYMUyiT1N6m3+fE3waI/o05V8rX5eenLnwXYGWiD5N+e8okr2MeOpo58xF0actok9Taij/cf4/pQc1+L19R8HwiT7N+Xzlm9KDvy59VqA1ok9z6kVaHw18tV+/m+j1YjOGTfRpUl0lD3U0UmNvlU+rRJ8m1dn+X5Y+LUN0chR8q3xaJfo06+PzXwzuPPc61vm8s9NO6Yvo07TjZz8YzN41xjoMgejTtDrmeeXrd5qf718dP893jXVonujTvBrSejeoVsN/9daBSfAZAtFnEOqePC2GfxL85ct20mQYRJ/BqOH/3Zd/bGbGX1f29fkIPkMi+gzKeNQzWlnv9MZs51aXbh2AjHQYll0zpxZvFhigV+cPl7f3v17m9syU7VLHOSeXPi0fnf+iwBCJPoM2t2e2vLX/tXJsdAB41OrWECcHfKUwVKJPFybxX3z2QHni8d3lYbl6a9fPugmcUQ49EH26UoN/dBT+o3MvlBdn92/qAFBDv3zlx3Hov+xof3+oRJ+uHZzdVxaemi/Pjub++0aPVf2rYKJe/FVX8P+/vlouXrs03vaht1s2wp1EHyCIUzYBgog+QBDRBwgi+gBBRB8giOgDBBF9gCCiDxBE9AGCiD5AENEHCCL6AEFEHyCI6AMEEX2AIKIPEET02ZKFp+fHd6bi0fNe8zCIPptWI/TJofdL2VXYDjfL6P1+T/jZEtFnUybBP372g7J82T1lt0O9d+/xsx8KP1si+myY4O8c4WerRJ8NEfydJ/xshegzNcFvh/CzWaLPVAS/PcLPZog+6xL8dgk/GyX6rEnw2yf8bITo80CCPxzCz7REn/vqPfinj5wovRF+piH6/ErCCv/g7L7SI+FnPaLPXYx0hk/4WYvoc5vg90P4eRDRZ0zw+yP83I/oI/gdE37uJfrhBL9/ws+dRD+Y4OcQfiZEP5Tg5xF+KtEPJPi5hB/RDyP4CH820Q8i+EwIfy7RDyH43Ev4M4l+AMHnQYQ/j+h3TvBZj/BnEf2OCT7TEv4cot+xAzP7BJ+pTcLf67bT/OKxQrc+Pv9FgY2o4a8/9MtKHyCI6AMEEX2AIKIPEET0AYKIPkAQ0QcIIvoAQUQfIIjoAwQRfYAgog8QRPQBgog+QBDRBwgi+gBBRB8giOgDBBF9Ip1bXSqQaNfMqcWbBYAIVvoAQUQfIIjoAwQRfYAgog8QRPQBgog+QBDRBwgi+gBBRB8giOgDBBF9gCCiDxBE9AGCiD5AENEHCCL6AEFEHyCI6AMEEX2AIKIPEET0AYKIPkAQ0QcIIvoAQUQfIIjoAwQRfYAgog8QRPQBgog+QBDRBwgi+gBBRB8giOgDBBF9gCCiDxBE9AGCiD5AENEHCCL6AEFEHyCI6AMEEX2AIKIPEET0AYKIPkAQ0QcIIvoAQUQfIIjoAwQRfYAgog8QRPQBgog+QBDRBwgi+gBBRB8giOgDBBF9gCCiDxBE9AGCiD5AENEHCCL6AEFEHyCI6AMEEX2AIKIPEET0AYKIPkAQ0QcIIvoAQUQfIIjoAwQRfYAgog8QRPQBgog+QBDRBwgi+gBBRB8giOgDBBF9gCCiDxBE9AGCiD5AENEHCCL6AEFEHyCI6AMEEX2AIKIPEET0AYKIPkAQ0QcIIvoAQUQfIMjPhswM2qw1AMQAAAAASUVORK5CYII="/>
</defs>
</svg>


            </div>
            <span class="text-xs text-gray-500 relative left-[26px]">
                <b class="text-[13px] text-[#c4c3c7] text-medium">Response</b> ${timestamp}
            </span>
            <div class="max-w-[75%]">
                <div id='msg-${message.id}' class="res flex flex-col gap-2 text-black px-5 py-3 rounded-2xl rounded-tl-sm shadow-sm">
                    ${marked.parse(processedMessage)}

                    
                      <div class="flex gap-3 justify-end text-xl" id='containerr-${message.chat_id}'>
        <i id="copy-${message.id}" class="bi bi-clipboard cursor-pointer" onclick="performAction('${message.id}', 'copy')"></i>
        <i id="like-${message.id}" class="bi bi-hand-thumbs-up cursor-pointer" onclick="performAction('${message.id}', 'like')"></i>
        <i id="unlike-${message.id}" class="bi bi-hand-thumbs-down cursor-pointer" onclick="performAction('${message.id}', 'unlike')"></i>
        <i id="bookmark-${message.id}" class="bi bi-bookmark cursor-pointer" onclick="performAction('${message.id}', 'bookmark')"></i>
    </div>
                </div>
            </div>
        </div>
    `;

 
        
            messageDiv.innerHTML = botMessageHTML;

            // Process templates after creating the element
            message.template.forEach(msg => {
                if (msg &&
                    msg.text &&
                    msg.text !== 'undefined' &&
                    msg.unique_id_with_template) {

                    setTimeout(() => {
                        try {
                            const templateData = JSON.parse(msg.text);
                            const templateId = msg.template_id;
                            const res = templateData;

                            // Use existing template processing logic if processNewTemplates function exists
                            if (typeof processNewTemplates === 'function') {
                                console.log('template type:', templateData);
                                if (res.template_type == 'table') {
                                    console.log("Rendering table");
                                    renderTable(res, templateId);
                                } else if (res.template_type == 'chart') {
                                    console.log("Rendering chart-------");
                                    forceRenderChart(res, templateId);
                                }
                            } else {
                                console.warn('processNewTemplates function not found');
                            }

                        } catch (error) {
                            console.error('Error processing template after DOM insertion:', error);
                        }
                    }, 100);
                }
            });
        }

        fragment.appendChild(messageDiv);
    });

    // Apply the fragment based on prepend flag
    if (prepend) {
        chatContainer.insertBefore(fragment, chatContainer.firstElementChild);
    } else {
        chatContainer.appendChild(fragment);
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }
}

    async function loadMoreMessages() {
        if (!currentChatId || isLoadingMore || !hasMoreMessages) {
            console.log('Load more blocked:', {
                currentChatId,
                isLoadingMore,
                hasMoreMessages
            });
            return;
        }

        console.log('Starting to load more messages...');
        isLoadingMore = true;

        const chatContainer = document.getElementById('chatContainer');
        const previousScrollHeight = chatContainer.scrollHeight;
        const previousScrollTop = chatContainer.scrollTop;

        // Add a loading indicator at the top
        const loadingIndicator = document.createElement('div');
        loadingIndicator.id = 'loading-more-indicator';
        loadingIndicator.className = 'text-center py-2 text-sm text-gray-500';
        loadingIndicator.innerHTML = '<span class="loading-dots">Loading more messages</span>';
        chatContainer.insertAdjacentElement('afterbegin', loadingIndicator);

        try {
            const response = await fetch(
                `{{ route('user.loadMore') }}?chat_id=${currentChatId}&page=${currentPage + 1}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

            const data = await response.json();
            console.log('Load more response:', data);

            if (data.messages && data.messages.data && data.messages.data.length > 0) {
                currentPage = data.messages.current_page;
                hasMoreMessages = data.hasMore;

                // Remove loading indicator before adding messages
                const indicator = document.getElementById('loading-more-indicator');
                if (indicator) indicator.remove();

                displayMessages(data.messages.data, true);

                // Maintain scroll position
                const newScrollHeight = chatContainer.scrollHeight;
                const scrollHeightDifference = newScrollHeight - previousScrollHeight;
                chatContainer.scrollTop = previousScrollTop + scrollHeightDifference;

                console.log('Messages loaded successfully');
            } else {
                hasMoreMessages = false;
                console.log('No more messages available');
            }
        } catch (error) {
            console.error('Error loading more messages:', error);
            hasMoreMessages = false; // Prevent further attempts

            // Show error message at top
            const errorDiv = document.createElement('div');
            errorDiv.className = 'text-center py-2 text-sm text-red-500';
            errorDiv.textContent = 'Error loading more messages. Please try again.';
            chatContainer.insertAdjacentElement('afterbegin', errorDiv);

            // Remove error message after 3 seconds
            setTimeout(() => errorDiv.remove(), 3000);
        } finally {
            // Remove loading indicator if it still exists
            const indicator = document.getElementById('loading-more-indicator');
            if (indicator) indicator.remove();

            isLoadingMore = false;
            console.log('Load more completed');
        }
    }



    async function deleteChat(chatId, event) {
        event.stopPropagation();

        if (!confirm('Are you sure you want to delete this chat? This action cannot be undone.')) {
            return;
        }

        try {
            const response = await fetch(`{{ route('user.delete_chat') }}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    chat_id: chatId,
                    "_token": "{{ csrf_token() }}"
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                const chatElement = document.querySelector(`[data-chat-id="${chatId}"]`);
                if (chatElement) {
                    chatElement.remove();
                }

                if (currentChatId == chatId) {
                    const chatContainer = document.getElementById('chatContainer');
                    placeholder()
                    const remainingChats = document.querySelectorAll('[data-chat-id]');
                    if (remainingChats.length > 0) {
                        const firstChatId = remainingChats[0].dataset.chatId;
                        selectChatWithUrlUpdate(firstChatId);
                    } else {
                        currentChatId = null;
                        updateUrlChatId('');
                        await createNewChat();
                    }
                }

                console.log('Chat deleted successfully');

            } else {
                throw new Error(data.error || 'Failed to delete chat');
            }

        } catch (error) {
            console.error('Error deleting chat:', error);
            alert('Failed to delete chat. Please try again.');
        }
    }


const questions = [
  // 🔁 Project Performance
  "Which project has the highest number of completed tasks?",
  "What is the average task completion time per project?",
  "Which projects are past their end date but not marked complete?",
  "Which projects have the most overdue tasks?",
  "What’s the average priority level of tasks in each project?",
  "Which projects have the most open tasks?",
  "How many tasks per project are marked high priority?",
  "Which project has the highest estimated workload?",
  "Which project has the highest task reassignment rate?",
  "Which project has the most deleted tasks?",

  // 📊 Cross-Platform Analysis
  "Which platform (Linear, Trello, Jira) has the highest task completion rate?",
  "What’s the average task estimate per platform?",
  "Which platform has the most overdue tasks?",
  "Which platform has the most deleted or cancelled tasks?",
  "Which source has the most completed projects?",
  "How does task volume differ between Linear, Trello, and Jira?",

  // ⏱️ Deadlines and Time Tracking
  "What is the average time between task creation and due date?",
  "Which tasks are overdue and not in 'completed' status?",
  "How many tasks are due in the next 7 days?",
  "What’s the total estimate of all tasks due this week?",
  "Which project has the tightest deadline vs. workload estimate?",

  // 🚦 Task Status + Flow
  "What percentage of tasks are currently in 'in-progress' status?",
  "How many tasks are in each status category across all platforms?",
  "Which team has the highest number of tasks stuck in 'in-progress'?",
  "What’s the ratio of completed to total tasks per project?",
  "How many tasks have no status assigned?",

  // 📌 Priority and Labels
  "What is the distribution of task priorities across all projects?",
  "Which labels are used most across high-priority tasks?",
  "How many high-priority tasks are still incomplete?",
  "Which label appears most frequently in overdue tasks?",
  "Which label is associated with the longest task durations?",

  // 👥 User & Team Performance
  "Which user has the most tasks completed this month?",
  "Which user is assigned to the most overdue tasks?",
  "Which team has the highest task throughput?",
  "Who has the highest number of high-priority tasks assigned?",
  "Which users have the highest task estimate vs. delivery ratio?",

  // 🧹 Maintenance & Data Health
  "Which tasks have not been updated in over 30 days?",
  "How many tasks were synced in the last 24 hours?",
  "How many tasks are missing due dates?",
  "Which projects haven’t been updated since creation?",
  "Are there tasks linked to non-existent or deleted projects?"
];

function suggest() {
  const randomIndex = Math.floor(Math.random() * questions.length);
  var text = questions[randomIndex];
  $("#messageInput").val(text);
  $("#messageInput").focus();
}

function performAction(id = '',messageId, action) {
    console.log(id)
    const messageElement = document.getElementById(`msg-${messageId}`);
    if (!messageElement) return;

    switch (action) {
        case 'copy':
            navigator.clipboard.writeText(messageElement.innerText)
                .then(() => toastr.success('Message copied to clipboard'))
                .catch(err => toastr.error('Failed to copy message'));
            break;
        case 'like':
            // Handle like action
            reaction(id, 'like')
            break;
        case 'unlike':
            // Handle unlike action
            reaction(id, 'dislike')
            break;
        case 'bookmark':
            // Handle bookmark action
            toastr.success('Message bookmarked');
            break;
        default:
            console.warn('Unknown action:', action);
    }
}
 

function reaction(id,type){
    $.ajax({
        url: "{{ route('user.reaction') }}",
        type: "POST",
        data: {
            id: id,
            type: type,
            _token: "{{ csrf_token() }}"
        },
        success: function(response) {
            if (response.success) {
                toastr.success(`Message ${type}d successfully`);
                // Update the UI or perform any additional actions if needed
                $(`#${type}-${id}`).toggleClass('text-blue-500 text-gray-500');
            } else {
                toastr.error('Failed to update reaction');
            }
        },
        error: function(xhr, status, error) {
            toastr.error('An error occurred while updating reaction');
        }
    });
}

</script>



<style>
    .mermaid-diagram {
        display: block;
        margin: 0 auto;
        border-radius: 8px;
        min-height: 200px;
    }

    .mermaid-container {
        overflow-x: auto;
        overflow-y: visible;
    }

    .loading-dots::after {
        content: '';
        animation: dots 1.5s steps(4, end) infinite;
    }

    @keyframes dots {

        0%,
        20% {
            content: '.';
        }

        40% {
            content: '..';
        }

        60% {
            content: '...';
        }

        80%,
        100% {
            content: '';
        }
    }

    .filter-btn {
        background-color: #f3f4f6;
        color: #374151;
    }

    .filter-btn.active {
        background-color: #1d4ed8;
        color: white;
    }

    .filter-btn:hover {
        background-color: #e5e7eb;
    }

    .filter-btn.active:hover {
        background-color: #1e40af;
    }
</style>
