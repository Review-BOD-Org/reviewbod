<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Page - Reviewbod</title>
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
                <img src="/images/register-img.svg" alt="Login Image" class="shadow">
            </div>
        </div>

        <!-- Right side with registration form -->
        <div class="w-1/2 h-full flex items-center justify-center">
            <div class="w-4/5 max-w-md">
                <h1 class="text-4xl font-bold mb-8 text-gray-800">Create An Account</h1>
                
                <form id="signupForm" class="space-y-6">
                    <div class="mb-4">
                        <label for="fullname" class="block text-lg font-medium text-gray-700 mb-2">Full Name</label>
                        <input type="text" id="fullname" class="w-full px-4 py-4 bg-gray-100 rounded-lg focus:outline-none" placeholder="Enter full name">
                    </div>
                    
                    <div class="mb-4">
                        <label for="email" class="block text-lg font-medium text-gray-700 mb-2">Email address</label>
                        <input type="email" id="email" class="w-full px-4 py-4 bg-gray-100 rounded-lg focus:outline-none" placeholder="Enter email">
                    </div>
                    
                    <div class="mb-4">
                        <label for="phone" class="block text-lg font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" id="phone" class="w-full px-4 py-4 bg-gray-100 rounded-lg focus:outline-none" placeholder="Enter Phone Number">
                    </div>
                    
                    <div class="mb-6">
                        <label for="password" class="block text-lg font-medium text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <input type="password" id="password" class="w-full px-4 py-4 bg-gray-100 rounded-lg focus:outline-none" placeholder="Enter password">
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500" id="togglePassword">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full bg-[#1E3A8A] text-white py-4 rounded-lg hover:bg-indigo-900 transition duration-200 text-lg font-medium">Create account</button>
                    
                    <div class="text-center mt-8 pt-6 border-t border-gray-200">
                        <span class="text-gray-600">Already have an account?</span>
                        <a href="/auth/login" class="font-medium text-[#1E3A8A] hover:text-[#1E3A8A] ml-1">Sign In</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Toggle password visibility
            $("#togglePassword").click(function() {
                const passwordField = $("#password");
                const fieldType = passwordField.attr("type");
                passwordField.attr("type", fieldType === "password" ? "text" : "password");
            });
            
            $('#signupForm').on('submit', function(e) {
                e.preventDefault();
                
                const email = $('#email').val();
                const fullname = $('#fullname').val();
                const phone = $('#phone').val();
                const password = $('#password').val();

                // Basic validation
                if (!email || !phone || !password || !fullname) {
                    toastr.error('Please fill in all fields');
                    return;
                }

                $("button[type='submit']").attr("disabled", true);
                $("button[type='submit']").text("Creating account...");

                // Simulated AJAX signup
                $.ajax({
                    url: '/auth/register',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ 
                        email, 
                        fullname,
                        phone, 
                        password,
                        "_token":"{{csrf_token()}}"
                    }),
                    success: function(response) {
                        toastr.success('Registration Successful!');
                        // Handle successful signup (e.g., redirect to verification)
                        location.href = "/auth/verification"
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Registration failed');
                    },
                    complete: function() {
                        $("button[type='submit']").attr("disabled", false);
                        $("button[type='submit']").text("Create account");
                    }
                });
            });
        });
    </script>
</body>
</html>