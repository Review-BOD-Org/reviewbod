 <style>
.chat-button {
    display: none !important;
}

.chat-container {
    display: flex;
    flex-direction: column;
    height: 100vh;
    padding: 1.25rem;
    background-color: white;
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    margin-bottom: 1rem;
    padding-right: 0.5rem;
}

.chat-input-container {
    margin-top: auto;
    padding-bottom: 1rem;
}
.content{
    overflow: hidden;
}
</style>

<div class="chat-container">
    <!-- Scrollable chat area -->
    <div class="chat-messages">
        <div id="chat-messages" class="chats chat-messages2 flex flex-col gap-4 w-full">
         
            
     
        </div>
    </div>

    <!-- Fixed input area at bottom with space underneath -->
    <div class="chat-input-container mb-[50px]">
        <form id="send-bot" style="margin-bottom: 100px;" class="shadow shadow-lg rounded-lg border p-2">
            <div class="flex flex-col p-2 bg-white">
                <input type="text" id="chat-input" placeholder="Type a message..." class="rounded-lg p-2 w-full outline-none" />
                <button onclick="sendMessage()" id="send-btn" class="ml-2 bg-[#4F46E5] text-white rounded-full px-4 py-2 flex gap-2 items-center justify-center self-end mt-5">
                    <span>Send</span>
                    <i class="fa ml-2">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18.2701 8.63131L5.14038 1.13913C4.86355 0.984056 4.54607 0.916943 4.23017 0.946713C3.91427 0.976483 3.61491 1.10173 3.37193 1.30578C3.12894 1.50984 2.95384 1.78304 2.86992 2.08904C2.78599 2.39505 2.79722 2.71935 2.9021 3.01881L5.25992 10.0001L2.9021 16.9813C2.81921 17.2168 2.79396 17.4688 2.82848 17.7161C2.863 17.9634 2.95627 18.1988 3.10049 18.4026C3.2447 18.6064 3.43567 18.7727 3.65737 18.8875C3.87908 19.0023 4.12508 19.0624 4.37476 19.0626C4.64326 19.062 4.90713 18.9926 5.14117 18.861L5.1482 18.8563L18.2732 11.3508C18.5138 11.2146 18.714 11.017 18.8533 10.7781C18.9926 10.5392 19.066 10.2676 19.066 9.99108C19.066 9.71455 18.9926 9.44297 18.8533 9.20408C18.714 8.96519 18.5138 8.76754 18.2732 8.63131H18.2701ZM4.93648 16.8173L6.92242 10.9376H11.2498C11.4984 10.9376 11.7369 10.8388 11.9127 10.663C12.0885 10.4872 12.1873 10.2487 12.1873 10.0001C12.1873 9.75142 12.0885 9.51297 11.9127 9.33715C11.7369 9.16134 11.4984 9.06256 11.2498 9.06256H6.92242L4.9357 3.18131L16.8701 9.99147L4.93648 16.8173Z" fill="white"/>
                        </svg>
                    </i>
                </button>
            </div>
        </form>
    </div>
</div>
 



<!-- JavaScript for Chat Functionality -->
<script>



document.addEventListener('DOMContentLoaded', function() {
    $("#send-bot").submit(function(){
        event.preventDefault();
        sendMessage();
    })
    getChats();
}, false);

    // Chat history storage
    let chatHistory = [];
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
      // Set current timestamp
      updateTimestamp();
      
      // Display welcome message
      addBotMessage("Hello, I'm ReviewBOT! 👋 I'm your personal assistant. How can I help you?");
    });
    
 
    
    // Update timestamp
    function updateTimestamp() {
      const now = new Date();
      const options = { weekday: 'short', hour: 'numeric', minute: 'numeric', hour12: true };
      const timestamp = now.toLocaleString('en-US', options);
    //   document.getElementById('chat-timestamp').textContent = timestamp;
    }
    
    // Add a user message to the chat
    function addUserMessage(message) {
      const chatMessages = document.getElementById('chat-messages');
      const messageHTML = `
      
                <!-- First outgoing message -->
            <div class="flex gap-4 justify-end w-full">
                <div class="border border-gray-300 p-2 rounded-lg bg-[#4F46E5] text-white max-w-[70%]">
                    <div class="flex flex-col">
                        <span>${escapeHTML(message)}</span>
                        <div class="flex gap-3 items-center self-end mt-1">
                            <span class="font-normal text-xs" id="chat-timestamp"></span>
                            <i>
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7.14415 4.15129L2.94415 8.27629C2.83896 8.37964 2.69739 8.43755 2.54993 8.43755C2.40246 8.43755 2.2609 8.37964 2.15571 8.27629L0.355708 6.50863C0.302984 6.45686 0.260973 6.39522 0.232074 6.32721C0.203175 6.25921 0.187954 6.18618 0.187279 6.11229C0.185917 5.96306 0.243889 5.81941 0.348442 5.71293C0.400212 5.66021 0.461859 5.6182 0.529864 5.5893C0.59787 5.5604 0.670901 5.54518 0.744789 5.5445C0.894012 5.54314 1.03766 5.60111 1.14415 5.70567L2.5504 7.0866L6.35618 3.34879C6.46259 3.24424 6.60619 3.18624 6.75537 3.18756C6.90455 3.18888 7.04709 3.2494 7.15165 3.35582C7.2562 3.46224 7.31419 3.60583 7.31288 3.75501C7.31156 3.90419 7.25103 4.04674 7.14461 4.15129H7.14415ZM11.6512 3.35441C11.5994 3.30151 11.5377 3.25935 11.4695 3.23036C11.4014 3.20136 11.3282 3.1861 11.2542 3.18545C11.1802 3.18479 11.1067 3.19876 11.0381 3.22655C10.9695 3.25434 10.907 3.2954 10.8543 3.34738L7.04993 7.0866L6.68055 6.72379C6.57413 6.61924 6.43054 6.56124 6.28136 6.56256C6.13218 6.56388 5.98964 6.6244 5.88508 6.73082C5.78053 6.83724 5.72253 6.98083 5.72385 7.13001C5.72517 7.27919 5.7857 7.42174 5.89211 7.52629L6.65571 8.27629C6.7609 8.37964 6.90246 8.43755 7.04993 8.43755C7.19739 8.43755 7.33896 8.37964 7.44414 8.27629L11.6441 4.15129C11.6968 4.09952 11.7388 4.03788 11.7677 3.96989C11.7966 3.90189 11.8118 3.82888 11.8124 3.75501C11.8131 3.68115 11.7992 3.60787 11.7715 3.53938C11.7438 3.47089 11.7029 3.40851 11.6512 3.35582V3.35441Z" fill="#475569"/>
                                </svg>
                            </i>
                        </div>
                    </div>
                </div>
                <div class="h-[40px] w-[40px] bg-gray-200 rounded-full flex items-center justify-center">
                    {{Auth::user()->name[0]}}
                </div>
            </div>
            
      `;
      chatMessages.insertAdjacentHTML('beforeend', messageHTML);
      scrollToBottom();
      
      // Add to history
      chatHistory.push({ role: 'user', content: message });
    }
    
    // Add a bot message to the chat
    function addBotMessage(message) {
      const chatMessages = document.getElementById('chat-messages');
      
      const messageHTML = `
    

           <!-- First incoming message -->
            <div class="flex gap-4 justify-start w-full">
                <div class="h-[40px] w-[40px]  rounded-full flex items-center justify-center">
                   <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
<path fill-rule="evenodd" clip-rule="evenodd" d="M34.5879 25.0886C31.8888 26.1708 28.9753 26.8383 25.9281 27.0112C25.4026 27.041 24.8731 27.0562 24.34 27.0562C23.8069 27.0562 23.2774 27.041 22.7519 27.0112C19.704 26.8382 16.7899 26.1705 14.0903 25.0878C8.42492 22.8158 3.70417 18.7165 0.67334 13.5281C5.39741 5.44098 14.2271 0 24.34 0C34.4529 0 43.2826 5.44098 48.0067 13.5281C44.9755 18.7171 40.254 22.8167 34.5879 25.0886Z" fill="white"/>
<path d="M14.0899 27.1939C13.4372 26.9321 12.7971 26.6461 12.1707 26.337C10.1347 27.7573 8.41365 29.5261 7.11768 31.5442C9.32049 34.9745 12.7515 37.6846 16.8691 39.1867C18.8312 39.9025 20.9492 40.344 23.1644 40.4583H25.4729C27.6876 40.344 31.5803 42.5168 25.9277 47.9999C30.0459 46.4979 39.3165 34.9749 41.5196 31.5442C40.2273 29.5319 38.5123 27.7673 36.484 26.3492C35.8648 26.6539 35.2323 26.9361 34.5875 27.1946C31.8884 28.2768 28.975 28.9443 25.9277 29.1173C25.4022 29.1471 24.8727 29.1622 24.3397 29.1622C23.8066 29.1622 23.2771 29.1471 22.7516 29.1173C19.7036 28.9443 16.7895 28.2765 14.0899 27.1939Z" fill="white"/>
<rect x="12.9277" y="10.0675" width="22.8741" height="8.08988" rx="4.04494" fill="#162550"/>
<ellipse cx="30.673" cy="14.0675" rx="1.49771" ry="1.48315" fill="#04FED1"/>
<ellipse cx="24.3185" cy="34.0224" rx="1.49771" ry="1.48315" fill="#162550"/>
<ellipse cx="18.3288" cy="14.0675" rx="1.49771" ry="1.48315" fill="#04FED1"/>
<ellipse cx="18.3288" cy="34.0224" rx="1.49771" ry="1.48315" fill="#162550"/>
<ellipse cx="30.3102" cy="34.0224" rx="1.49771" ry="1.48315" fill="#162550"/>
</svg>
                </div>
                <div class="border border-gray-300 p-2 rounded-lg max-w-[70%]">
                    <div class="flex flex-col">
                        <span>${formatMessage(message)}</span>
                        <div class="flex gap-3 items-center self-end mt-1">
                            <span class="font-normal text-xs" id="chat-timestamp"></span>
                            <i>
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7.14415 4.15129L2.94415 8.27629C2.83896 8.37964 2.69739 8.43755 2.54993 8.43755C2.40246 8.43755 2.2609 8.37964 2.15571 8.27629L0.355708 6.50863C0.302984 6.45686 0.260973 6.39522 0.232074 6.32721C0.203175 6.25921 0.187954 6.18618 0.187279 6.11229C0.185917 5.96306 0.243889 5.81941 0.348442 5.71293C0.400212 5.66021 0.461859 5.6182 0.529864 5.5893C0.59787 5.5604 0.670901 5.54518 0.744789 5.5445C0.894012 5.54314 1.03766 5.60111 1.14415 5.70567L2.5504 7.0866L6.35618 3.34879C6.46259 3.24424 6.60619 3.18624 6.75537 3.18756C6.90455 3.18888 7.04709 3.2494 7.15165 3.35582C7.2562 3.46224 7.31419 3.60583 7.31288 3.75501C7.31156 3.90419 7.25103 4.04674 7.14461 4.15129H7.14415ZM11.6512 3.35441C11.5994 3.30151 11.5377 3.25935 11.4695 3.23036C11.4014 3.20136 11.3282 3.1861 11.2542 3.18545C11.1802 3.18479 11.1067 3.19876 11.0381 3.22655C10.9695 3.25434 10.907 3.2954 10.8543 3.34738L7.04993 7.0866L6.68055 6.72379C6.57413 6.61924 6.43054 6.56124 6.28136 6.56256C6.13218 6.56388 5.98964 6.6244 5.88508 6.73082C5.78053 6.83724 5.72253 6.98083 5.72385 7.13001C5.72517 7.27919 5.7857 7.42174 5.89211 7.52629L6.65571 8.27629C6.7609 8.37964 6.90246 8.43755 7.04993 8.43755C7.19739 8.43755 7.33896 8.37964 7.44414 8.27629L11.6441 4.15129C11.6968 4.09952 11.7388 4.03788 11.7677 3.96989C11.7966 3.90189 11.8118 3.82888 11.8124 3.75501C11.8131 3.68115 11.7992 3.60787 11.7715 3.53938C11.7438 3.47089 11.7029 3.40851 11.6512 3.35582V3.35441Z" fill="#475569"/>
                                </svg>
                            </i>
                        </div>
                    </div>
                </div>
            </div>
      `;
      chatMessages.insertAdjacentHTML('beforeend', messageHTML);
      scrollToBottom();
      
      // Add to history
      chatHistory.push({ role: 'assistant', content: message });
    }
  
    // Format message (handle line breaks, links, etc.)
    function formatMessage(message) {
    var   msg =  escapeHTML(message)
        .replace(/\n/g, '<br>')
        .replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" class="text-blue-600 underline">$1</a>');
  
        return marked.parse(msg);
  
    }
    
    // Escape HTML to prevent XSS
    function escapeHTML(str) {
      return str.replace(/[&<>'"]/g, 
        tag => ({
          '&': '&amp;',
          '<': '&lt;',
          '>': '&gt;',
          "'": '&#39;',
          '"': '&quot;'
        }[tag]));
    }
   

    function scrollToBottom() {
  // Add a small delay to ensure content is rendered
  setTimeout(() => {
    const chatMessages = document.querySelector('.chat-messages');
    if (chatMessages) {
      chatMessages.scrollTop = chatMessages.scrollHeight;
    }
  }, 100);
}
    
    // Send a message when the user clicks send or presses enter
    function sendMessage() {
     
      const inputField = document.getElementById('chat-input');
      const message = inputField.value.trim();
      
      if (message === '') return;
      
      // Clear input field
      inputField.value = '';
      
      // Add user message to chat
      addUserMessage(message);
      
      // Show loading indicator
    //   document.getElementById('chat-loading').classList.remove('hidden');
      
      // Send to server
      sendToOpenAI(message);
    }
    
    // Send a quick question from the buttons
    function sendQuickQuestion(question) {
      // Add user message to chat
      addUserMessage(question);
      
      // Show loading indicator
      document.getElementById('chat-loading').classList.remove('hidden');
      
      // Send to server
      sendToOpenAI(question);
    }

    function revert(){
        $("#send-btn").html(`
          <span>Send</span>
                    <i class="fa ml-2">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18.2701 8.63131L5.14038 1.13913C4.86355 0.984056 4.54607 0.916943 4.23017 0.946713C3.91427 0.976483 3.61491 1.10173 3.37193 1.30578C3.12894 1.50984 2.95384 1.78304 2.86992 2.08904C2.78599 2.39505 2.79722 2.71935 2.9021 3.01881L5.25992 10.0001L2.9021 16.9813C2.81921 17.2168 2.79396 17.4688 2.82848 17.7161C2.863 17.9634 2.95627 18.1988 3.10049 18.4026C3.2447 18.6064 3.43567 18.7727 3.65737 18.8875C3.87908 19.0023 4.12508 19.0624 4.37476 19.0626C4.64326 19.062 4.90713 18.9926 5.14117 18.861L5.1482 18.8563L18.2732 11.3508C18.5138 11.2146 18.714 11.017 18.8533 10.7781C18.9926 10.5392 19.066 10.2676 19.066 9.99108C19.066 9.71455 18.9926 9.44297 18.8533 9.20408C18.714 8.96519 18.5138 8.76754 18.2732 8.63131H18.2701ZM4.93648 16.8173L6.92242 10.9376H11.2498C11.4984 10.9376 11.7369 10.8388 11.9127 10.663C12.0885 10.4872 12.1873 10.2487 12.1873 10.0001C12.1873 9.75142 12.0885 9.51297 11.9127 9.33715C11.7369 9.16134 11.4984 9.06256 11.2498 9.06256H6.92242L4.9357 3.18131L16.8701 9.99147L4.93648 16.8173Z" fill="white"/>
                        </svg>
                    </i>
        `)
    }
    
    // Send message to OpenAI via Laravel backend
    function sendToOpenAI(message) {
      // Get CSRF token
      $("#send-btn").html("<img src='/loading.gif' alt='loading' class='w-5 h-5 animate-spin' />")
  

      $("button").attr("disabled",true)
      const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      
      fetch('/dashboard/chat', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({
          message: message,
          history: chatHistory,
          user_id: "{{request()->id}}",
          name:"{{$data['user']['displayName']}}"
        })
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        $("button").attr("disabled",false)
        revert()
      
        return response.json();
      })
      .then(data => {
        // Hide loading indicator
        // document.getElementById('chat-loading').classList.add('hidden');
    
        // Add bot response to chat
        addBotMessage(data.response);
        revert()
        $("button").attr("disabled",false)
      })
      .catch(error => {
        console.error('Error:', error);
        
        // Hide loading indicator
        document.getElementById('chat-loading').classList.add('hidden');
        $("button").attr("disabled",false)
        revert()
        // Show error message
        addBotMessage("I'm sorry, I'm having trouble connecting. Please try again later.");
      });
    }

    function getChats() {
    $.ajax({
        url: '{{route('user.chats',['user_id'=>request()->id])}}',
        method: 'GET',
        success: function(response) {
            const msg = response.data;
           msg.map((res)=>{
            if(res.sender_type == "user"){
                addUserMessage(res.message)
            }else{
                addBotMessage(res.message)
            }
           })
        },
        error: function(xhr) {
            console.error("Failed to fetch last chat:", xhr.responseText);
        }
    });
}


  </script>