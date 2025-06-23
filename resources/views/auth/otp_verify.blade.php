<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification - Reviewbod</title>
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
                <img src="/images/verify-img.svg" alt="Login Image">
            </div>
        </div>

        <!-- Right side with OTP verification form -->
        <div class="w-1/2 h-full flex items-center justify-center">
            <div class="w-4/5 max-w-md">
                <h1 class="text-4xl font-bold mb-2 text-gray-800">Verify Account</h1>
                <p class="text-gray-500 mb-8">We sent an otp to {{Auth::user()->email}} enter OTP below</p>

                <form id="otpForm" class="space-y-6">
                    <div class="mb-6">
                        <input type="text" id="otpInput"
                            class="w-full px-4 py-4 bg-gray-100 rounded-lg focus:outline-none"
                            placeholder="Enter OTP">
                    </div>

                    <button type="submit"
                        class="w-full bg-[#1E3A8A] text-white py-4 rounded-lg hover:bg-indigo-900 transition duration-200 text-lg font-medium">
                        Verify
                    </button>

                    <div class="text-center mt-8 pt-6">
                        <span class="text-gray-600">NO OTP YET?</span>
                        <a href="#" id="resendOtp" class="font-medium text-[#1E3A8A] hover:text-[#1E3A8A] ml-1">Resend OTP</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#otpForm').on('submit', function(e) {
                e.preventDefault();

                const otp = $('#otpInput').val();

                // Basic validation
                if (!otp || otp.length !== 6 || !/^\d+$/.test(otp)) {
                    toastr.error('Please enter a valid 6-digit OTP');
                    return;
                }

                $("button[type='submit']").attr("disabled", true);
                $("button[type='submit']").text("Verifying...");
                
                // Simulated AJAX OTP verification
                $.ajax({
                    url: '/auth/verification',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ 
                        otp,
                        "_token":"{{csrf_token()}}"
                    }),
                    success: function(response) {
                        toastr.success('OTP Verified Successfully!');
                        // Handle successful verification (e.g., redirect)
                        location.href = '/auth/choose'
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'OTP Verification Failed');
                    },
                    complete: function() {
                        $("button[type='submit']").attr("disabled", false);
                        $("button[type='submit']").text("Verify");
                    }
                });
            });

            // Resend OTP functionality
            $('#resendOtp').on('click', function(e) {
                e.preventDefault();
                
                $(this).text("Sending...");
                
                // Simulated AJAX resend OTP
                $.ajax({
                    url: '/auth/resend-otp',
                    method: 'GET',
                    success: function(response) {
                        toastr.success('New OTP sent to your email');
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Failed to resend OTP');
                    },
                    complete: function() {
                        $('#resendOtp').text("Resend OTP");
                    }
                });
            });
        });
    </script>
</body>

</html>