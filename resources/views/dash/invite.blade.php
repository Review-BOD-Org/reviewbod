<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-[#F4F9FF] px-4 py-6">
    <div class="w-full max-w-md bg-white shadow-sm rounded-lg p-6 sm:p-8">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-gray-200 rounded-full mx-auto mb-4"></div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Hey {{$user->name}}</h2>
            <p class="text-xs sm:text-sm text-gray-500 mt-2">
          Create your password to get started
            </p>
 
        </div>

        <form id="loginForm" class="space-y-4 mt-4">
           
            <div class="relative shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <input 
                    type="password" 
                    id="password" 
                    placeholder="Enter Password" 
                    class="w-full pl-10 pr-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                >
            </div>

            <div class="relative shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <input 
                    type="password" 
                    id="cpassword" 
                    placeholder="Confirm Password" 
                    class="w-full pl-10 pr-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                >
            </div>

            <button 
                type="submit" 
                class="w-full bg-[#1E3A8A] text-white py-3 rounded-lg hover:bg-blue-700 transition duration-300 text-sm sm:text-base"
            >
                Get Started
            </button>
        </form>

       
    </div>

    <script>
    $(document).ready(function() {
        $('#loginForm').on('submit', function(e) {
            e.preventDefault();
            
            const cpassword = $('#cpassword').val();
            const password = $('#password').val();

            // Basic validation
            if (password != cpassword) {
                toastr.error('Password and Confirm Password did not match');
                return;
            }

            $("button").attr("disabled", true);
            $("button").text("Please wait...");
            // Simulated AJAX login
            $.ajax({
                url: '/auth/update_password',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ id:"{{request()->id}}", password,"_token":"{{csrf_token()}}" }),
                success: function(response) {
                    toastr.success('Authenticated!');
                    // Handle successful login (e.g., redirect)
                    location.href = "/user/dash"
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Login failed');
                },
                complete: function() {
                    $("button").attr("disabled", false);
                    $("button").text("Login");
                }
            });
        });
    });
    </script>
</body>
</html>