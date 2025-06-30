<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spaces - Reviewbod</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
      <link rel="icon" type="image/x-icon" href="/rb.svg">
</head>

<body class="bg-white">

    <style>
.app-option{
 
box-shadow: 0px 0.5px 4px 2px rgba(0, 0, 0, 0.25);
 

}
        </style>
    <div class="flex w-full h-screen">
        <!-- Left side with logo and image -->
        <div class="w-1/2 bg-[#EBEBEB] h-full flex flex-col p-8">
            <!-- Logo at top left -->
            <div class="mb-8">
                <img src="/rb.svg" width="70" alt="Logo">
            </div>

            <!-- Centered image -->
            <div class="flex-grow flex items-center justify-center">
                <img src="/images/login-img.svg" alt="Login Image">
            </div>
        </div>

        <!-- Right side with app linking options -->
        <div class="w-1/2 h-full flex  justify-center">
            <div class="w-4/5 max-w-md mt-[10%]">
                <h1 class="text-3xl font-bold mb-8 text-gray-800">Workspace</h1>
                <p class="text-lg text-gray-700 mb-8">Select work space to authenticate with reviewbod</p>

                <div class="grid grid-cols-3 gap-4 mt-8">


                    @foreach($spaces as $s)
                    <!-- Trello Option -->
                    <div class="cursor-pointer">
                        <div onclick="$('#space').val('{{$s->workspace}}')" id="{{$s->workspace}}"
                            class="app-option bg-gray-100 rounded-lg p-4 flex flex-col items-center justify-center h-24 border border-gray-200 hover:shadow-md transition duration-200">
                            <div class="text-blue-500 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24"><path fill="currentColor" d="M6 13c-2.2 0-4 1.8-4 4s1.8 4 4 4s4-1.8 4-4s-1.8-4-4-4m6-10C9.8 3 8 4.8 8 7s1.8 4 4 4s4-1.8 4-4s-1.8-4-4-4m6 10c-2.2 0-4 1.8-4 4s1.8 4 4 4s4-1.8 4-4s-1.8-4-4-4"/></svg>
                            </div>
                            <span class="font-medium">{{$s->workspace}}</span>
                        </div>
    
                    </div>
                    @endforeach
                     
                </div>

                
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Handle app selection
            $('.app-option').on('click', function() {
                // Remove selected class from all options
                $('.app-option').removeClass('ring-2 ring-blue-500').addClass('bg-gray-100');

                // Add selected class to clicked option
                $(this).removeClass('bg-gray-100').addClass('ring-2 ring-blue-500 bg-blue-50');

                const service = $(this).attr('id');

                // Simulate AJAX request for authentication
                $.ajax({
                    url: '/invited/choose',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        space:$("#space").val(),
                        "_token": "{{ csrf_token() }}"
                    }),
                    success: function(response) {
                        toastr.success(response.message || 'Connecting to ' + service + '...');
                        location.href = "/invited/dash";
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Connection failed');
                    }
                });
            });
        });
    </script>
</body>
<input type="hidden" id="space"/>
</html>
