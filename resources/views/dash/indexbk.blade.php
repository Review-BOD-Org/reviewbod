<!-- views/dashboard.blade.php -->
@extends('dash.layouts.app')

@section('page-title', 'Staff Performance Dashboard')

@section('content')
    <style>
        #chat_data>div {
            padding: 0px !important;
        }

        .flowchart{
            transform:unset !important;
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
                    <div class="flex justify-between bg-[#CB964F] p-3 rounded-lg shadow-lg max-w-full" id="chatbox">
                        <div class="flex gap-3 w-full min-w-0">
                            <div class="flex-shrink-0">
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
                            <input type="text" id="messageInput"
                                class="text-white placeholder-white focus:outline-none bg-[#CB964F] border-0 focus:ring-0 focus:border-0 w-full min-w-0"
                                placeholder="Ask me anything about your data...">
                        </div>
                        <button id="sendButton" onclick="sendMessage()" class="flex-shrink-0 ml-3">
                            <svg width="30" height="32" viewBox="0 0 30 32" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <rect x="0.5" y="0.5" width="29" height="31" rx="7.5" stroke="white" />
                                <path
                                    d="M23.2562 16.5168L10.6167 16.5168M9.56359 9.11278L22.8189 15.5396C23.6352 15.9354 23.6352 17.0983 22.8189 17.494L9.56359 23.9209C8.65552 24.3612 7.69032 23.4329 8.09482 22.5084L10.5257 16.9521C10.6471 16.6746 10.6471 16.359 10.5257 16.0815L8.09482 10.5253C7.69032 9.60073 8.65551 8.6725 9.56359 9.11278Z"
                                    stroke="white" stroke-linecap="round" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
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
    </style>
@endsection

{{-- <script src="https://cdn.jsdelivr.net/npm/mermaid@11.2.1/dist/mermaid.min.js"></script> --}}
<script src="https://unpkg.com/@panzoom/panzoom@4.5.1/dist/panzoom.min.js"></script>
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
    
        if (typeof mermaid === 'undefined') {
            try {
                const script = document.createElement('script');
                script.src = 'https://unpkg.com/mermaid@11.2.1/dist/mermaid.min.js';
                script.onload = () => {
                    mermaid.initialize({
                        startOnLoad: false,
                        securityLevel: 'loose',
                        theme: 'default',
                        flowchart: {
                            useMaxWidth: true,
                            htmlLabels: true,
                            curve: 'basis'
                        },
                        sequence: {
                            useMaxWidth: true
                        },
                        gantt: {
                            useMaxWidth: true
                        },
                        pie: {
                            useMaxWidth: true,
                            textPosition: 0.75,
                            showData: true
                        },
                        journey: {
                            useMaxWidth: true
                        }
                    });
                };
                script.onerror = () => {
                    console.error('Failed to load Mermaid library');
                    document.getElementById('chatContainer').innerHTML +=
                        '<p class="text-sm text-red-500 text-center">Failed to load Mermaid library. Please refresh the page.</p>';
                };
                document.head.appendChild(script);
            } catch (error) {
                console.error('Error loading Mermaid:', error);
            }
        } else {
            mermaid.initialize({
                startOnLoad: false,
                securityLevel: 'loose',
                theme: 'default',
                flowchart: {
                    useMaxWidth: true,
                    htmlLabels: true,
                    curve: 'basis'
                },
                sequence: {
                    useMaxWidth: true
                },
                gantt: {
                    useMaxWidth: true
                },
                pie: {
                    useMaxWidth: true,
                    textPosition: 0.75,
                    showData: true
                },
                journey: {
                    useMaxWidth: true
                }
            });
        }

        const messageInput = document.getElementById('messageInput');
        if (messageInput) {
            messageInput.focus();
            messageInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    sendMessage();
                }
            });
        }

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

        if (chatIdFromPath && !isNaN(chatIdFromPath) && chatIdFromPath !== '') {
            currentChatId = chatIdFromPath;
            loadChats(); // Load chats and messages
            // startSidebarUpdateInterval(); // Start interval for existing chat
        } else {
            //  startSidebarUpdateInterval(); 
            // No chat ID in URL, load sidebar and show placeholder
            loadSidebarChatsOnly();
            // Don't start interval yet - will start after first message
        }

        document.querySelector('button#newchat').addEventListener('click', createNewChat);
    });

    window.addEventListener('beforeunload', () => {
        if (sidebarUpdateInterval) {
            clearInterval(sidebarUpdateInterval);
        }
    });

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
                    chatItem.className = 'flex px-2 w-full gap-3 cursor-pointer';
                    chatItem.dataset.chatId = chat.id;
                    chatItem.innerHTML = `
                    <div class="flex w-full flex-col cursor-pointer px-4 py-4">
                        <div class="flex justify-between">
                            <h3 class="font-bold">${chat.title}</h3>
                            <div class="flex gap-2 items-center">
                                <span class="text-[#7D7272] text-[12px]">${new Date(chat.updated_at).toLocaleString('en-US', { day: 'numeric', month: 'short', hour: 'numeric', minute: '2-digit', hour12: true })}</span>
                                <a href="javascript:;" onclick="deleteChat(${chat.id}, event)" class="text-red-500 hover:text-red-700 transition-colors ml-2" title="Delete chat">
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
                            selectChatWithUrlUpdate(chat.id);
                        }
                    });

                    chatListContainer.appendChild(chatItem);
                });
            } else {
                // chatListContainer.innerHTML = '<p class="px-2 text-sm text-gray-500">No chats found.</p>';
            }

            // Show placeholder in chat container
            const chatContainer = document.getElementById('chatContainer');
            placeholder()

        } catch (error) {
            console.error('Error loading sidebar chats:', error);
            const chatListContainer = document.querySelector('#chat_data');
            chatListContainer.innerHTML = '<p class="px-2 text-sm text-red-500">Error loading chats.</p>';
        }
    }



    function selectChatWithUrlUpdate(chatId) {
        currentChatId = chatId;
        updateUrlChatId(chatId);
        selectChat(chatId);
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

    function addUserMessage(message) {
        const chatContainer = document.getElementById('chatContainer');
        const timestamp = getCurrentTimestamp();
        const userMessageHTML = `
         <div class="flex items-start justify-end space-x-3">
    <div class="max-w-[95%] min-w-[200px] flex flex-col items-end">
        <div class="w-8 h-8 relative top-9 right-3 self-start rounded-full bg-yellow-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
            U
        </div>
        <span class="text-xs text-gray-500 relative self-start left-5">
            <b class="text-[13px] text-[#c4c3c7] text-medium">You</b> ${timestamp}
        </span>
        <div class="w-[-webkit-fill-available] bg-[#1E3A8A] text-white px-4 py-3 rounded-2xl rounded-tl-sm shadow-sm flex justify-between gap-5 items-center">
            <p class="text-sm">${message}</p>
        </div>
    </div>
</div>
    `;
        chatContainer.insertAdjacentHTML('beforeend', userMessageHTML);
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    function addLoadingMessage() {
        const chatContainer = document.getElementById('chatContainer');
        const timestamp = getCurrentTimestamp();
        const loadingHTML = `
        <div class="space-x-3" id="loading-message">
            <div class="w-8 h-8 relative top-10 rounded-full bg-green-600 flex items-center justify-center flex-shrink-0">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="3" fill="white" />
                    <path d="M12 2v4M12 18v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M2 12h4M18 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83" stroke="white" stroke-width="1.5" />
                </svg>
            </div>
            <span class="text-xs text-gray-500 relative left-[26px]">
                <b class="text-[13px] text-[#c4c3c7] text-medium">Response</b> ${timestamp}
            </span>
            <div class="max-w-[75%]">
                <div class="bg-[#CB964F] text-white px-4 py-3 rounded-2xl rounded-tl-sm shadow-sm">
                    <p class="text-sm leading-relaxed">
                        <span class="loading-dots">Thinking</span>
                    </p>
                </div>
            </div>
        </div>
    `;
        chatContainer.insertAdjacentHTML('beforeend', loadingHTML);
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    function removeLoadingMessage() {
        const loadingMessage = document.getElementById('loading-message');
        if (loadingMessage) {
            loadingMessage.remove();
        }
    }

    function createMermaidResponse(response, timestamp) {
        const diagramId = `mermaid-diagram-${messageCount}`;
        let mermaidCode = response.data && typeof response.data === 'string' ? response.data :
            response.data && response.data.code ? response.data.code :
            response.content || 'graph TD\n    A[No diagram data] --> B[Please provide valid Mermaid syntax]';
        return `
        <div class="space-x-3">
            <div class="w-8 h-8 relative top-10 rounded-full bg-green-600 flex items-center justify-center flex-shrink-0">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="3" fill="white" />
                    <path d="M12 2v4M12 18v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M2 12h4M18 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83" stroke="white" stroke-width="1.5" />
                </svg>
            </div>
            <span class="text-xs text-gray-500 relative left-[26px]">
                <b class="text-[13px] text-[#c4c3c7] text-medium">Response</b> ${timestamp}
            </span>
            <div class="max-w-[75%]">
                <div class="text-black rounded-2xl rounded-tl-sm shadow-sm p-[40px] bg-white border">
                    <h2 class="text-xl font-semibold text-gray-800 mb-2">${response.title || 'Diagram'}</h2>
                    <p class="mb-2">${response.content || 'Generated diagram'}</p>
                    <div class="mermaid-container bg-white p-4 rounded-lg relative" style="min-height: 300px;">
                        <div class="zoom-controls absolute top-4 right-4 flex space-x-2" style="z-index:99999999">
                            <button onclick="zoomIn('${diagramId}')" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-1 px-2 rounded text-sm" aria-label="Zoom in on diagram">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </button>
                            <button onclick="zoomOut('${diagramId}')" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-1 px-2 rounded text-sm" aria-label="Zoom out on diagram">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </button>
                            <button onclick="resetZoom('${diagramId}')" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-1 px-2 rounded text-sm" aria-label="Reset diagram view">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4 4v5h5M20 20v-5h-5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M9 4a9 9 0 010 16M15 20a9 9 0 010-16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </button>
                        </div>
                        <div class="mermaid-diagram" id="${diagramId}" style="cursor: grab;"></div>
                    </div>
                </div>
            </div>
        </div>
    `;
    }

  function initializeMermaidDiagram(diagramId, mermaidCode) {
    if (!mermaidCode) {
        console.error(`No Mermaid code provided for diagram: ${diagramId}`);
        const element = document.getElementById(diagramId);
        if (element) {
            element.innerHTML = '<p class="text-red-500">No diagram code provided</p>';
        }
        return;
    }

    const element = document.getElementById(diagramId);
    if (!element) {
        console.error(`Mermaid element not found: ${diagramId}`);
        return;
    }

    if (element.dataset.initialized === 'true') {
        console.log(`Diagram ${diagramId} already initialized, skipping.`);
        return;
    }

    element.dataset.initialized = 'true';
    try {
        let cleanCode = String(mermaidCode).trim().replace(/\\n/g, '\n').replace(/\\"/g, '"');

        // Validate if the code starts with a known Mermaid diagram type
        const validDiagramTypes = ['graph', 'sequenceDiagram', 'classDiagram', 'stateDiagram', 'pie', 'gantt', 'erDiagram', 'journey'];
        const isValidMermaid = validDiagramTypes.some(type => cleanCode.startsWith(type) || cleanCode.includes(`${type} `));

        if (!isValidMermaid) {
            console.warn(`Invalid Mermaid syntax for ${diagramId}:`, cleanCode);
            element.innerHTML = `
                <div class="text-red-500 p-4 border border-red-300 rounded">
                    <p class="font-semibold">Invalid diagram syntax</p>
                    <p class="text-sm mt-2">The provided code is not a valid Mermaid diagram.</p>
                    <details class="mt-2">
                        <summary class="cursor-pointer text-xs">Show provided code</summary>
                        <pre class="text-xs mt-2 bg-gray-100 p-2 rounded overflow-auto">${cleanCode}</pre>
                    </details>
                </div>
            `;
            return;
        }

        cleanCode = cleanCode.replace(/^pie\s*showData\s*$/m, 'pie showData');
        console.log(`Rendering Mermaid diagram ${diagramId}:`, cleanCode);

        if (typeof mermaid === 'undefined') {
            element.innerHTML = '<p class="text-red-500">Mermaid library not loaded. Please refresh the page.</p>';
            return;
        }

        const svgId = diagramId + '-svg';
        mermaid.render(svgId, cleanCode).then((result) => {
            element.innerHTML = result.svg;
            const svgElement = element.querySelector('svg');
            if (svgElement) {
                svgElement.style.maxWidth = '100%';
                svgElement.style.height = 'auto';
                svgElement.style.overflow = 'visible';
                svgElement.removeAttribute('width');
                svgElement.removeAttribute('height');

                if (typeof Panzoom !== 'undefined') {
                    const panzoomInstance = Panzoom(svgElement, {
                        maxZoom: 5,
                        minZoom: 0.5,
                        zoomSpeed: 0.2,
                        contain: 'outside',
                        cursor: 'grab',
                        smoothScroll: true,
                        passive: true,
                        startScale: 0.30
                    });
                    window.panzoomInstances = window.panzoomInstances || {};
                    window.panzoomInstances[diagramId] = panzoomInstance;
                    svgElement.parentElement.addEventListener('wheel', (e) => {
                        panzoomInstance.zoomWithWheel(e);
                    }, { passive: true });
                    svgElement.parentElement.addEventListener('mousedown', (e) => {
                        if (e.button === 0) panzoomInstance.pan(e.clientX, e.clientY, { animate: false });
                    }, { passive: true });
                    svgElement.parentElement.addEventListener('touchstart', (e) => {
                        if (e.touches.length === 2) {
                            e.preventDefault();
                            panzoomInstance.zoomWithWheel(e);
                        }
                    }, { passive: true });
                } else {
                    console.warn('Panzoom library not loaded; zoom controls disabled.');
                }
            }
        }).catch((error) => {
            console.error(`Mermaid rendering error for ${diagramId}:`, error, `Code:`, cleanCode);
            element.innerHTML = `
                <div class="text-red-500 p-4 border border-red-300 rounded">
                    <p class="font-semibold">Error rendering diagram</p>
                    <p class="text-sm mt-2">Error: ${error.message || 'Unknown error'}</p>
                    <details class="mt-2">
                        <summary class="cursor-pointer text-xs">Show diagram code</summary>
                        <pre class="text-xs mt-2 bg-gray-100 p-2 rounded overflow-auto">${cleanCode}</pre>
                    </details>
                </div>
            `;
        });
    } catch (error) {
        console.error(`Error initializing Mermaid diagram ${diagramId}:`, error);
        element.innerHTML = `
            <div class="text-red-500 p-4 border border-red-300 rounded">
                <p class="font-semibold">Error initializing diagram</p>
                <p class="text-sm mt-2">Error: ${error.message}</p>
            </div>
        `;
    }
}
    function zoomIn(diagramId) {
        if (window.panzoomInstances && window.panzoomInstances[diagramId]) {
            window.panzoomInstances[diagramId].zoomIn();
        }
    }

    function zoomOut(diagramId) {
        if (window.panzoomInstances && window.panzoomInstances[diagramId]) {
            window.panzoomInstances[diagramId].zoomOut();
        }
    }

    function resetZoom(diagramId) {
        if (window.panzoomInstances && window.panzoomInstances[diagramId]) {
            window.panzoomInstances[diagramId].reset();
        }
    }

    function createTextResponse(response, timestamp) {
        const content = response.content || 'No response content available';
        return `
        <div class="space-x-3">
            <div class="w-8 h-8 relative top-10 rounded-full bg-green-600 flex items-center justify-center flex-shrink-0">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="3" fill="white" />
                    <path d="M12 2v4M12 18v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M2 12h4M18 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83" stroke="white" stroke-width="1.5" />
                </svg>
            </div>
            <span class="text-xs text-gray-500 relative left-[26px]">
                <b class="text-[13px] text-[#c4c3c7] text-medium">Response</b> ${timestamp}
            </span>
            <div class="max-w-[75%]">
                <div class="bg-[#CB964F] text-white px-4 py-3 rounded-2xl rounded-tl-sm shadow-sm">
                    <p class="text-sm leading-relaxed">${content}</p>
                    <div class="flex items-center justify-end mt-2 px-1">
                        <div class="flex space-x-2">
                            <button class="bg-[#7F5721] hover:bg-orange-700 text-white px-3 py-[8px] rounded-md text-xs flex items-center space-x-1 transition-colors" onclick="regenerateResponse()">
                                <span><i class="bi bi-arrow-clockwise"></i> Regenerate Response</span>
                            </button>
                            <button class="bg-[#7F5721] hover:bg-gray-600 text-white px-3 py-[8px] rounded-md text-xs flex items-center space-x-1 transition-colors" onclick="copyResponse('${content.replace(/'/g, "\\'")}')">
                                <span><i class="bi bi-copy"></i> Copy</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    }

    function createTableResponse(response, timestamp) {
        const data = response.data || {};
        const headers = data.headers || [];
        const rows = data.rows || [];
        const filters = data.filters || ['All'];
        const tableId = `table-${messageCount}`;
        tableData[tableId] = {
            headers,
            rows,
            filters,
            originalRows: [...rows]
        };
        let filtersHTML = filters.length > 1 ? `
        <div class="flex space-x-2 mb-4 mt-2">
            ${filters.map((filter, index) => `
                <button class="filter-btn px-4 py-[4px] rounded-md text-xs transition-colors ${index === 0 ? 'active' : ''}" onclick="filterTable('${filter}', this, '${tableId}')">
                    <span>${filter}</span>
                </button>
            `).join('')}
        </div>
    ` : '';
        let tableHTML = headers.length > 0 ? `
        <div class="relative overflow-x-auto sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500" id="${tableId}">
                <thead class="text-xs text-[#4B5563] font-light uppercase bg-[#F9FAFB] h-[50px]">
                    <tr>${headers.map(header => `<th scope="col" class="px-6 py-3">${header}</th>`).join('')}</tr>
                </thead>
                <tbody>
                    ${rows.map(row => `
                        <tr class="bg-white border-b">
                            ${row.map((cell, index) => index === 0 ? `<th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">${cell}</th>` : `<td class="px-6 py-4">${cell}</td>`).join('')}
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    ` : '';
        return `
        <div class="space-x-3">
            <div class="w-8 h-8 relative top-10 rounded-full bg-green-600 flex items-center justify-center flex-shrink-0">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="3" fill="white" />
                    <path d="M12 2v4M12 18v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M2 12h4M18 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83" stroke="white" stroke-width="1.5" />
                </svg>
            </div>
            <span class="text-xs text-gray-500 relative left-[26px]">
                <b class="text-[13px] text-[#c4c3c7] text-medium">Response</b> ${timestamp}
            </span>
            <div class="max-w-[75%]">
                <div class="border-[#CB964F] border-2 text-black px-5 py-3 rounded-2xl rounded-tl-sm shadow-sm">
                    <div class="mt-1">
                        <div class="flex flex-col">
                            <p class="text-md font-bold leading-relaxed">${response.title || 'Data Table'}</p>
                            <p class="mb-2">${response.content}</p>
                            ${filtersHTML}
                        </div>
                        ${tableHTML}
                    </div>
                </div>
            </div>
        </div>
    `;
    }

    function createTimelineResponse(response, timestamp) {
        const data = response.data || {};
        const events = data.events || [];
        const timelineHTML = events.length > 0 ? events.map(event => `
        <li class="mb-10 ms-6 flex" style="margin-left:50px">
            <div class="w-full">
                <span class="absolute flex items-center justify-center w-12 h-12 rounded-full -start-6">
                    <svg width="35" height="35" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="17.5" cy="17.5" r="17.5" fill="#1E3A8A" />
                        <circle cx="17.5" cy="17.5" r="8.75" fill="white" />
                    </svg>
                </span>
                <h3 class="flex items-center mb-1 text-lg font-semibold text-black">${event.title || 'Event'}</h3>
                <time class="block mb-2 text-sm font-normal leading-none text-gray-400">Date: ${event.date || 'Unknown'}</time>
                <p class="mb-4 text-base font-normal text-gray-500">Source: ${event.source || 'System'}</p>
            </div>
            <span class="bg-[#14AE5C] text-white rounded-lg text-xs font-medium me-2 px-2.5 py-0.5 self-center">${event.status || 'Completed'}</span>
        </li>
    `).join('') : '<li class="mb-10 ms-6"><p class="text-sm text-gray-500">No timeline events available.</p></li>';
        return `
        <div class="space-x-3">
            <div class="w-8 h-8 relative top-10 rounded-full bg-green-600 flex items-center justify-center flex-shrink-0">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="3" fill="white" />
                    <path d="M12 2v4M12 18v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M2 12h4M18 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83" stroke="white" stroke-width="1.5" />
                </svg>
            </div>
            <span class="text-xs text-gray-500 relative left-[26px]">
                <b class="text-[13px] text-[#c4c3c7] text-medium">Response</b> ${timestamp}
            </span>
            <div class="max-w-[75%]">
                <div class="text-black rounded-2xl rounded-tl-sm shadow-sm p-[40px] bg-white border">
                    <h2 class="text-xl font-semibold text-gray-800 mb-2">${response.title || 'Timeline'}</h2>
                    <p class="mb-2">${response.content || 'Timeline of events'}</p>
                    <ol class="relative border-s border-gray-200">${timelineHTML}</ol>
                </div>
            </div>
        </div>
    `;
    }

    function addAIResponse(response) {
        removeLoadingMessage();
        const chatContainer = document.getElementById('chatContainer');
        const timestamp = getCurrentTimestamp();
        messageCount++;
        let responseHTML = '';

        if (!response || typeof response !== 'object' || !response.type) {
            responseHTML = createTextResponse({
                content: typeof response === 'string' ? response : JSON.stringify(response),
                title: 'Invalid Response'
            }, timestamp);
        } else if (response.type === 'TEXT') {
            responseHTML = createTextResponse(response, timestamp);
        } else if (response.type === 'TABLE') {
            responseHTML = createTableResponse(response, timestamp);
        } else if (response.type === 'TIMELINE') {
            responseHTML = createTimelineResponse(response, timestamp);
        } else if (response.type === 'MERMAID') {
            let mermaidCode = response.data && typeof response.data === 'string' ? response.data :
                response.data && response.data.code ? response.data.code : response.content;
            if (!mermaidCode) {
                responseHTML = createTextResponse({
                    content: 'Error: Invalid or missing diagram code',
                    title: 'Diagram Error'
                }, timestamp);
            } else {
                responseHTML = createMermaidResponse(response, timestamp);
                chatContainer.insertAdjacentHTML('beforeend', responseHTML);
                chatContainer.scrollTop = chatContainer.scrollHeight;
                initializeMermaidDiagram(`mermaid-diagram-${messageCount}`, mermaidCode);
                return;
            }
        } else {
            responseHTML = createTextResponse({
                content: response.content || JSON.stringify(response),
                title: response.title || 'Response'
            }, timestamp);
        }

        chatContainer.insertAdjacentHTML('beforeend', responseHTML);
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    async function sendMessage() {
        const input = document.getElementById('messageInput');
        const sendButton = document.getElementById('sendButton');
        const message = input.value.trim();
        if (!message) return;

        input.disabled = true;
        sendButton.disabled = true;
        addUserMessage(message);
        input.value = '';
        addLoadingMessage();
        $("#placeholder").remove()
        try {
            const response = await fetch('{{ route('user.chatt') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    query: message,
                    chat_id: currentChatId, // Can be null for new conversation
                    "_token": "{{ csrf_token() }}"
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success && data.response) {
                // If no currentChatId, set it from response and update URL
                startSidebarUpdateInterval();
                if (!currentChatId && data.chat_id) {
                    currentChatId = data.chat_id;
                    updateUrlChatId(currentChatId);

                    // Start sidebar update interval after first message

                }

                addAIResponse(data.response);
                totalMessages++;
            } else {
                addAIResponse({
                    type: 'TEXT',
                    content: data.error || 'Sorry, I encountered an error processing your request.',
                    title: 'Error'
                });
            }
        } catch (error) {
            console.error('Error:', error);
            addAIResponse({
                type: 'TEXT',
                content: 'Something broke. Talk to me later?',
                title: 'Connection Error'
            });
        } finally {
            input.disabled = false;
            sendButton.disabled = false;
            if (input) input.focus();
        }
    }

    function startSidebarUpdateInterval() {
        updateSidebarOnly();
    }

    async function updateSidebarOnly() {
        try {
            const response = await fetch('{{ route('user.getSidebarChatsLast') }}', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) return; // Silently fail to avoid disrupting user

            const data = await response.json();
            if (data.chats && data.chats.length > 0) {
                updateSidebarChats(data.chats);
                // currentChatId = data.chats[0].id;
                //     updateUrlChatId(currentChatId);
            }
        } catch (error) {
            console.error('Error updating sidebar:', error);
            // Don't show error to user, just log it
        }
    }

    function updateSidebarChats(chats) {
        const chatListContainer = document.querySelector('#chat_data');
        if (!chatListContainer) return;

        // Store currently active chat to check user selection
        const currentlyActive = chatListContainer.querySelector('.active-chat');
        const activeChatId = currentlyActive ? currentlyActive.closest('[data-chat-id]')?.dataset.chatId : null;

        chatListContainer.innerHTML = '';

        if (chats.length > 0) {
            // Select the first chat if no user interaction or if the active chat is not in the list
            const shouldSelectFirstChat = !isUserInteracting && (!activeChatId || !chats.some(chat => chat.id ==
                activeChatId));

            chats.forEach((chat, index) => {
                const chatItem = document.createElement('div');
                chatItem.className = 'flex px-2 w-full gap-3 cursor-pointer';
                chatItem.dataset.chatId = chat.id;

                // Mark the first chat as active if shouldSelectFirstChat is true, otherwise use currentChatId
                const isActive = shouldSelectFirstChat && index === 0 ? true : currentChatId == chat.id && !
                    isUserInteracting;

                chatItem.innerHTML = `
                <div class="flex w-full flex-col cursor-pointer px-4 py-4 ${isActive ? 'active-chat' : ''}">
                    <div class="flex justify-between">
                        <h3 class="font-bold">${chat.title}</h3>
                        <div class="flex gap-2 items-center">
                            <span class="text-[#7D7272] text-[12px]">${new Date(chat.updated_at).toLocaleString('en-US', { day: 'numeric', month: 'short', hour: 'numeric', minute: '2-digit', hour12: true })}</span>
                            <a href="javascript:;" onclick="deleteChat(${chat.id}, event)" class="text-red-500 hover:text-red-700 transition-colors ml-2" title="Delete chat">
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
                        isUserInteracting = true;
                        selectChatWithUrlUpdate(chat.id);
                        setTimeout(() => {
                            isUserInteracting = false;
                        }, 1000); // Reset after 1 second
                    }
                });

                chatListContainer.appendChild(chatItem);
            });

            // If shouldSelectFirstChat is true, select the first chat
            if (shouldSelectFirstChat) {
                const firstChat = chats[0];
                currentChatId = firstChat.id;
                updateUrlChatId(currentChatId);
                selectChat(currentChatId); // Load the chat content
            }
        } else {
            // If no chats are present and no currentChatId, show placeholder
            if (!currentChatId) {
                placeholder();
            }
        }
    }

    function regenerateResponse() {
        console.log('Regenerate response clicked');
    }

    function copyResponse(text) {
        navigator.clipboard.writeText(text).then(() => {
            console.log('Response copied to clipboard');
        });
    }

    function filterTable(filter, button, tableId) {
        const tableInfo = tableData[tableId];
        if (!tableInfo) return;

        document.querySelectorAll(`[onclick*="${tableId}"]`).forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');

        const filteredRows = filter === 'All' ? [...tableInfo.originalRows] :
            tableInfo.originalRows.filter(row => row.some(cell => String(cell).toLowerCase().includes(filter
                .toLowerCase())));

        const table = document.getElementById(tableId);
        if (!table) return;
        const tbody = table.querySelector('tbody');
        if (!tbody) return;

        tbody.innerHTML = filteredRows.length === 0 ? `
        <tr class="bg-white border-b">
            <td colspan="${tableInfo.headers.length}" class="px-6 py-4 text-center text-gray-500">
                No data found for "${filter}"
            </td>
        </tr>
    ` : filteredRows.map(row => `
        <tr class="bg-white border-b">
            ${row.map((cell, index) => index === 0 ? `<th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">${cell}</th>` : `<td class="px-6 py-4">${cell}</td>`).join('')}
        </tr>
    `).join('');
    }

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


    async function loadChats() {
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
                    chatItem.className = 'flex px-2 w-full gap-3 cursor-pointer';
                    chatItem.dataset.chatId = chat.id;
                    chatItem.innerHTML = `
                    <div class="flex w-full flex-col cursor-pointer px-4 py-4 ${currentChatId == chat.id ? 'active-chat' : ''}">
                        <div class="flex justify-between">
                            <h3 class="font-bold">${chat.title}</h3>
                            <div class="flex gap-2 items-center">
                                <span class="text-[#7D7272] text-[12px]">${new Date(chat.updated_at).toLocaleString('en-US', { day: 'numeric', month: 'short', hour: 'numeric', minute: '2-digit', hour12: true })}</span>
                                <a href="javascript:;" onclick="deleteChat(${chat.id}, event)" class="text-red-500 hover:text-red-700 transition-colors ml-2" title="Delete chat">
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
                            selectChatWithUrlUpdate(chat.id);
                        }
                    });

                    chatListContainer.appendChild(chatItem);
                });

                // If currentChatId is set, make sure it's selected
                if (currentChatId) {
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
    async function createNewChat() {
        // Reset chat state
        currentChatId = null;
        const chatContainer = document.getElementById('chatContainer');
        chatContainer.innerHTML = '';
        placeholder();

        // Reset URL to /dashboard
        const newUrl = `${window.location.origin}/dashboard`;
        window.history.pushState({}, '', newUrl);

        // Update sidebar to reflect current chats without selecting any
        await updateSidebarOnly();
    }
    async function selectChat(chatId) {
        currentChatId = chatId;
        currentPage = 1;
        loadedMessageIds.clear();
        totalMessages = 0;
        hasMoreMessages = false;

        const chatItems = document.querySelectorAll('.flex.px-2.w-full.gap-3.cursor-pointer');
        chatItems.forEach(item => {
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
        }
    }

  function displayMessages(messages, prepend = false) {
    const chatContainer = document.getElementById('chatContainer');
    const fragment = document.createDocumentFragment();

    const newMessages = messages.filter(message => !loadedMessageIds.has(message.id));
    if (!newMessages.length && prepend) return;

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
        let mermaidCode = null;
        let diagramId = null;

        if (isUser) {
            messageHTML = `
                <div class="flex items-start justify-end space-x-3">
                    <div class="max-w-[95%] min-w-[200px] flex flex-col items-end">
                        <div class="w-8 h-8 relative top-9 right-3 self-start rounded-full bg-yellow-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                            U
                        </div>
                        <span class="text-xs text-gray-500 relative self-start left-5">
                            <b class="text-[13px] text-[#c4c3c7] text-medium">You</b> ${timestamp}
                        </span>
                        <div class="w-[-webkit-fill-available] bg-[#1E3A8A] text-white px-4 py-3 rounded-2xl rounded-tl-sm shadow-sm flex justify-between gap-5 items-center">
                            <p class="text-sm">${message.message}</p>
                        </div>
                    </div>
                </div>
            `;
        } else {
            try {
                const parsed = JSON.parse(message.message);
                if (parsed.type === 'TEXT') {
                    messageHTML = createTextResponse(parsed, timestamp);
                } else if (parsed.type === 'TABLE') {
                    messageCount++;
                    messageHTML = createTableResponse(parsed, timestamp);
                } else if (parsed.type === 'TIMELINE') {
                    messageCount++;
                    messageHTML = createTimelineResponse(parsed, timestamp);
                } else if (parsed.type === 'MERMAID') {
                    messageCount++;
                    mermaidCode = parsed.data && typeof parsed.data === 'string' ? parsed.data :
                        parsed.data && parsed.data.code ? parsed.data.code : parsed.content;
                    if (!mermaidCode) {
                        messageHTML = createTextResponse({
                            content: 'Error: Invalid or missing diagram code',
                            title: 'Diagram Error'
                        }, timestamp);
                    } else {
                        diagramId = `mermaid-diagram-${messageCount}`;
                        messageHTML = createMermaidResponse(parsed, timestamp);
                    }
                } else {
                    messageHTML = createTextResponse({
                        content: parsed.content || JSON.stringify(parsed),
                        title: parsed.title || 'Response'
                    }, timestamp);
                }
            } catch (e) {
                console.warn(`Failed to parse bot message as JSON for message ID ${message.id}:`, message.message, e);
                messageHTML = createTextResponse({
                    content: message.message,
                    title: 'Response'
                }, timestamp);
            }
        }

        const div = document.createElement('div');
        div.innerHTML = messageHTML;
        fragment.appendChild(div);

        // Defer Mermaid initialization until after DOM insertion
        if (mermaidCode && diagramId) {
            setTimeout(() => {
                const element = document.getElementById(diagramId);
                if (element) {
                    initializeMermaidDiagram(diagramId, mermaidCode);
                } else {
                    console.error(`Deferred initialization failed: Mermaid element not found: ${diagramId}`);
                }
            }, 0);
        }
    });

    if (prepend) {
        chatContainer.insertBefore(fragment, chatContainer.firstChild);
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
