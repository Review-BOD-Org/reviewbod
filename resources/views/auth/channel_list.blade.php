<!-- Slack Channel Selector View (slack.channel_list.blade.php) -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Select Slack Channel</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>

</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Select a Slack Channel</h1>
            
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-200">
                    <div class="flex items-center">
                        <svg class="h-6 w-6 text-gray-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                        </svg>
                        <span class="text-gray-700 font-medium">Available Channels ({{ count($channels) }})</span>
                    </div>
                </div>
                
                <div class="overflow-y-auto max-h-96">
                    @if(count($channels) > 0)
                        <ul class="divide-y divide-gray-200">
                            @foreach($channels as $channel)
                                <li class="channel-item p-4 hover:bg-gray-50 cursor-pointer transition duration-150" data-channel-id="{{ $channel['id'] }}">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <span class="text-gray-900 font-medium">#{{ $channel['name'] }}</span>
                                            <span class="ml-2 px-2 py-1 text-xs bg-gray-200 text-gray-700 rounded-full">{{ $channel['member_count'] ?? 0 }} members</span>
                                        </div>
                                        <button class="select-channel px-3 py-1 bg-blue-500 text-white text-sm rounded hover:bg-blue-600 transition" data-channel-id="{{ $channel['id'] }}" data-channel-name="{{ $channel['name'] }}">
                                            Select
                                        </button>
                                    </div>
                                    @if(!empty($channel['topic']))
                                        <p class="mt-1 text-sm text-gray-600">{{ $channel['topic'] }}</p>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="p-6 text-center text-gray-500">
                            No channels available. Please check your Slack permissions.
                        </div>
                    @endif
                </div>
            </div>
            
            <div id="status-message" class="mt-4 p-4 rounded-md hidden"></div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $('.select-channel').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const channelId = $(this).data('channel-id');
                const channelName = $(this).data('channel-name');
                const $statusMessage = $('#status-message');
                
                // Show loading state
                $(this).html('<span class="inline-block animate-spin mr-1">↻</span> Linking');
                $(this).prop('disabled', true);
                
                $.ajax({
                    url: '{{ route("slack.save_channel") }}',
                    type: 'POST',
                    data: {
                        channel: channelId
                    },
                    success: function(response) {
                        $statusMessage.removeClass('hidden bg-red-100 text-red-700').addClass('bg-green-100 text-green-700');
                        $statusMessage.html(`<strong>Success!</strong> Channel #${channelName} has been linked to your account.`);
                        
                        // Reset all buttons
                        $('.select-channel').html('Select').prop('disabled', false);
                        
                        // Highlight the selected channel
                        $(`.select-channel[data-channel-id="${channelId}"]`).html('Linked ✓').removeClass('bg-blue-500 hover:bg-blue-600').addClass('bg-green-500');
                        location.href = "/dashboard"
                    },
                    error: function(xhr) {
                        $statusMessage.removeClass('hidden bg-green-100 text-green-700').addClass('bg-red-100 text-red-700');
                        $statusMessage.html('<strong>Error!</strong> Failed to link channel. Please try again.');
                        
                        // Reset button
                        $(`.select-channel[data-channel-id="${channelId}"]`).html('Select').prop('disabled', false);
                    }
                });
            });
            
            // Make the whole row clickable (optional)
            $('.channel-item').on('click', function(e) {
                if (!$(e.target).hasClass('select-channel')) {
                    const channelId = $(this).data('channel-id');
                    $(`.select-channel[data-channel-id="${channelId}"]`).click();
                }
            });
        });
    </script>
</body>
</html>