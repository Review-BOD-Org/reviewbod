<div class="container mx-auto flex justify-center items-center mt-5">
    <div class="flex flex-col gap-6 w-full max-w-md mt-5">
        <!-- Header Section -->
        <div class="flex flex-col gap-2 justify-center items-center text-center">
            <div class="bg-[#1E3A8A] rounded-full h-16 w-16 flex items-center justify-center">
                <i class="fa fa-link text-white text-xl"></i>
            </div>
            <h2 class="text-2xl font-medium text-gray-800">Notification Channels</h2>
            <p class="text-gray-500">Choose how you'd like to receive notifications</p>
        </div>

        @php
 
            $notifications = DB::table("notification_channel_manager")->where(["userid"=>Auth("managers")->user()->id])->get();
            $hasEmail = $notifications->where('channel', 'email')->count() > 0;
            $hasSlack = $notifications->where('channel', 'slack')->count() > 0;
        @endphp

        <form id="notificationPreferencesForm">
            @csrf
            <!-- Notification Channels List -->
            <div class="flex flex-col gap-3">
                <!-- Email Notification Channel -->
                <div class="border rounded-lg p-4 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="bg-gray-100 rounded-lg p-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17 20.5H7C4 20.5 2 19 2 15.5V8.5C2 5 4 3.5 7 3.5H17C20 3.5 22 5 22 8.5V15.5C22 19 20 20.5 17 20.5Z" stroke="#1E3A8A" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M17 9L13.87 11.5C12.84 12.32 11.15 12.32 10.12 11.5L7 9" stroke="#1E3A8A" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium">Email</h4>
                            <p class="text-sm text-gray-500">Receive notifications via email</p>
                        </div>
                    </div>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="email_notifications" value="1" class="sr-only peer" {{ $hasEmail ? 'checked' : '' }}>
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#1E3A8A]"></div>
                    </label>
                </div>

                <!-- Slack Notification Channel -->
                <div class="border rounded-lg p-4 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="bg-gray-100 rounded-lg">
                            <img src="/images/slack.webp" width="30">
                        </div>
                        <div>
                            <h4 class="font-medium">Slack</h4>
                            <p class="text-sm text-gray-500">Receive notifications in Slack</p>
                        </div>
                    </div>
                    <label class="inline-flex items-center cursor-pointer">
                        @if($slack)
                        <input type="checkbox" name="slack_notifications" value="1" class="sr-only peer" {{ $hasSlack ? 'checked' : '' }}>
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#1E3A8A]"></div>
                        @else
                        <a href="javascript:;" class="bg-red-400 text-white px-3 py-1 rounded-lg  text-sm">Not Linked</a>
                        @endif
                    </label>
                </div>
            </div>
            
            <!-- Save Button -->
            <button type="submit" id="savePreferencesBtn" class="bg-[#1E3A8A] text-white px-6 py-3 rounded-lg hover:bg-blue-700 w-full mt-4">
                Save Preferences
            </button>
        </form>

        <!-- Status message -->
        <div id="statusMessage" class="hidden text-center p-2 rounded-lg mt-2"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('notificationPreferencesForm');
    const statusMessage = document.getElementById('statusMessage');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        const saveBtn = document.getElementById('savePreferencesBtn');
        const originalBtnText = saveBtn.innerHTML;
        saveBtn.innerHTML = 'Saving...';
        saveBtn.disabled = true;
        
        // Get form data
        const formData = new FormData(form);
        
        // Check if checkboxes are checked, if not, add them with value 0
        if (!formData.has('email_notifications')) {
            formData.append('email_notifications', '0');
        }
        
        if (!formData.has('slack_notifications') && document.querySelector('input[name="slack_notifications"]')) {
            formData.append('slack_notifications', '0');
        }
        
        // Send Ajax request
        fetch('/manager/save_notification', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            toastr.success(data.message);
            // Reset button
            saveBtn.innerHTML = originalBtnText;
            saveBtn.disabled = false;
        })
        .catch(error => {
            toastr.error(error.responseJSON ? error.responseJSON.message : 'An error occurred');
            // Reset button
            saveBtn.innerHTML = originalBtnText;
            saveBtn.disabled = false;
        });
    });
});
</script>