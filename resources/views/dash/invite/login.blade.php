<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Reviewbod</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
      <link rel="icon" type="image/x-icon" href="/rb.svg">
<script src="https://cdn.jsdelivr.net/npm/pwacompat" async></script>


<link rel="manifest" href="/manifest.json" />
<meta name="theme-color" content="#1e3a8a"/>

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

        <!-- Right side with login form - updated to match design -->
        <div class="w-1/2 h-full flex items-center justify-center">
            <div class="w-4/5 max-w-md">
                <h1 class="text-4xl font-bold mb-8 text-gray-800">Nice to see you again</h1>

                <form id="loginForm" class="space-y-6">
                    <div class="mb-6">
                        <label for="email" class="block text-lg font-medium text-gray-700 mb-2">Login</label>
                        <input type="text" id="email"
                            class="w-full px-4 py-4 bg-gray-100 rounded-lg focus:outline-none"
                            placeholder="Email Address">
                    </div>

                    <div class="mb-6">
                        <label for="password" class="block text-lg font-medium text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <input type="password" id="password"
                                class="w-full px-4 py-4 bg-gray-100 rounded-lg focus:outline-none"
                                placeholder="Enter password">
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

                    <div class="flex items-center justify-between mt-4 mb-6">


                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" value="" class="sr-only peer">
                            <div
                                class="relative w-11 h-6 bg-[#E5E5E5] peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#1E3A8A] rounded-full peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-[#E5E5E5] after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#1E3A8A]">
                            </div>
                            <span class="ms-3 text-sm font-medium text-[#000000]">Remember me</span>
                        </label>


                        <a href="#" class="text-[#1E3A8A] hover:text-[#1E3A8A]">Forgot password?</a>
                    </div>

                    <button type="submit"
                        class="w-full bg-[#1E3A8A] text-white py-4 rounded-lg hover:bg-indigo-900 transition duration-200 text-lg font-medium">Sign
                        in</button>

                  
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

            $('#loginForm').on('submit', function(e) {
                e.preventDefault();

                const email = $('#email').val();
                const password = $('#password').val();

                // Basic validation
                if (!email || !password) {
                    toastr.error('Please enter both email and password');
                    return;
                }

                $("button[type='submit']").attr("disabled", true);
                $("button[type='submit']").text("Signing in...");
                // Simulated AJAX login
                $.ajax({
                    url: '/invited/login',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        email,
                        password,
                        "_token": "{{ csrf_token() }}"
                    }),
                    success: function(response) {
                        toastr.success('Login Successful!');
                        // Handle successful login (e.g., redirect)
                        location.href =  "/invited/space"
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Login failed');
                    },
                    complete: function() {
                        $("button[type='submit']").attr("disabled", false);
                        $("button[type='submit']").text("Sign in");
                    }
                });
            });
        });
    </script>

<script>
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js');
  }
</script>

</body>

</html>
