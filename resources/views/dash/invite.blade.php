<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Password - Reviewbod</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="icon" type="image/x-icon" href="/rb.svg">
</head>

<body class="bg-white">

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

        @if($user->status == "pending")

        <!-- Right side with password creation form -->
        <div class="w-1/2 h-full flex items-center justify-center">
            <div class="w-4/5 max-w-md">
                <h1 class="text-4xl font-bold mb-4 text-gray-800">Hey {{$user->name}}</h1>
                <p class="text-lg text-gray-500 mb-8"> You have been invited to a {{$user->workspace}} created by {{$invite->name}}, create your password to get started</p>

                <form id="loginForm" class="space-y-6">
                    <div class="mb-6">
                        <label for="password" class="block text-lg font-medium text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <input type="password" id="password"
                                class="w-full px-4 py-4 bg-gray-100 rounded-lg focus:outline-none"
                                placeholder="Enter Password">
                            <button type="button"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500"
                                id="togglePassword">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="cpassword" class="block text-lg font-medium text-gray-700 mb-2">Confirm Password</label>
                        <div class="relative">
                            <input type="password" id="cpassword"
                                class="w-full px-4 py-4 bg-gray-100 rounded-lg focus:outline-none"
                                placeholder="Confirm Password">
                            <button type="button"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500"
                                id="toggleConfirmPassword">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-[#1E3A8A] text-white py-4 rounded-lg hover:bg-indigo-900 transition duration-200 text-lg font-medium">Get Started</button>
                </form>
            </div>
        </div>

        @elseif($user->status == "decline")

        <!-- Right side with decline status -->
        <div class="w-1/2 h-full flex items-center justify-center">
            <div class="w-4/5 max-w-md text-center">
                <!-- Decline Icon -->
                <div class="mb-6 flex justify-center">
                    <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                </div>

                <h1 class="text-4xl font-bold mb-4 text-gray-800">Invitation Declined</h1>
                <p class="text-lg text-gray-500 mb-6">
                    You have declined the invitation to join <strong>{{$user->workspace}}</strong> workspace created by <strong>{{$invite->name}}</strong>.
                </p>
                
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-8">
                    <p class="text-red-700 text-sm">
                        <strong>Note:</strong> If you change your mind, you'll need to contact {{$invite->name}} for a new invitation.
                    </p>
                </div>

                <div class="space-y-4">
                    <button onclick="window.location.href='/'" 
                        class="w-full bg-gray-500 text-white py-4 rounded-lg hover:bg-gray-600 transition duration-200 text-lg font-medium">
                        Go to Homepage
                    </button>
                    
                    <button onclick="window.location.href='/contact'" 
                        class="w-full border border-gray-300 text-gray-700 py-4 rounded-lg hover:bg-gray-50 transition duration-200 text-lg font-medium">
                        Contact Support
                    </button>
                </div>
            </div>
        </div>

        @else

        <!-- Right side with invitation acceptance -->
        <div class="w-1/2 h-full flex items-center justify-center">
            <div class="w-4/5 max-w-md">
                <h1 class="text-4xl font-bold mb-4 text-gray-800">Hey {{$user->name}}</h1>
                <p class="text-lg text-gray-500 mb-8">You have been invited to a <strong>{{$user->workspace}}</strong> created by <strong>{{$invite->name}}</strong>. You can accept or decline the invitation below.</p>

                <div class="space-y-4">
                    <button id="acceptBtn" type="button"
                        class="w-full bg-[#1E3A8A] text-white py-4 rounded-lg hover:bg-indigo-900 transition duration-200 text-lg font-medium">
                        Accept Invitation
                    </button>
                    
                    <button id="cancelBtn" type="button"
                        class="w-full bg-gray-500 text-white py-4 rounded-lg hover:bg-gray-600 transition duration-200 text-lg font-medium">
                        Decline Invitation
                    </button>
                </div>
            </div>
        </div>

        @endif
    </div>

    <script>
        $(document).ready(function() {
            // Toggle password visibility for inactive users
            $("#togglePassword").click(function() {
                const passwordField = $("#password");
                const fieldType = passwordField.attr("type");
                passwordField.attr("type", fieldType === "password" ? "text" : "password");
            });

            // Toggle confirm password visibility for inactive users
            $("#toggleConfirmPassword").click(function() {
                const passwordField = $("#cpassword");
                const fieldType = passwordField.attr("type");
                passwordField.attr("type", fieldType === "password" ? "text" : "password");
            });

            // Handle password creation form for inactive users
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();
                
                const cpassword = $('#cpassword').val();
                const password = $('#password').val();

                // Basic validation
                if (password != cpassword) {
                    toastr.error('Password and Confirm Password did not match');
                    return;
                }

                $("button[type='submit']").attr("disabled", true);
                $("button[type='submit']").text("Please wait...");
                
                // AJAX request for password update
                $.ajax({
                    url: '/auth/update_password',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ id:"{{request()->id}}", password, "_token":"{{csrf_token()}}" }),
                    success: function(response) {
                        toastr.success('Password created successfully!');
                        location.href = "/invited/login";
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Failed to create password');
                    },
                    complete: function() {
                        $("button[type='submit']").attr("disabled", false);
                        $("button[type='submit']").text("Get Started");
                    }
                });
            });

            // Handle Accept Invitation button for active users
            $('#acceptBtn').on('click', function() {
                const $btn = $(this);
                $btn.attr("disabled", true);
                $btn.text("Accepting...");

                // AJAX request to accept invitation
                $.ajax({
                    url: '/invited/update_status',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ 
                        id: "{{request()->id}}", 
                        action: "accept",
                         workspace: "{{request()->workspace}}",
                        "_token": "{{csrf_token()}}" 
                    }),
                    success: function(response) {
                        toastr.success('Invitation accepted successfully!');
                        // Redirect to dashboard or appropriate page
                        setTimeout(function() {
                            location.href = response.redirect_url || "/invited/login";
                        }, 1500);
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Failed to accept invitation');
                    },
                    complete: function() {
                        $btn.attr("disabled", false);
                        $btn.text("Accept Invitation");
                    }
                });
            });

            // Handle Decline Invitation button for active users
            $('#cancelBtn').on('click', function() {
                const $btn = $(this);
                $btn.attr("disabled", true);
                $btn.text("Declining...");

                // AJAX request to decline invitation
                $.ajax({
                    url: '/invited/update_status',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ 
                        id: "{{request()->id}}", 
                        workspace: "{{request()->workspace}}",
                        action: "decline",
                        "_token": "{{csrf_token()}}" 
                    }),
                    success: function(response) {
                        toastr.success('Invitation declined.');
                        // Redirect to home page or login
                        setTimeout(function() {
                            location.href = response.redirect_url || "/";
                        }, 1500);
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Failed to decline invitation');
                    },
                    complete: function() {
                        $btn.attr("disabled", false);
                        $btn.text("Decline Invitation");
                    }
                });
            });
        });
    </script>
</body>

</html>