<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Apps - Reviewbod</title>
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
                <h1 class="text-3xl font-bold mb-8 text-gray-800">Link Apps</h1>
                <p class="text-lg text-gray-700 mb-8">Select any task platform to authenticate with reviewbod</p>

                <div class="grid grid-cols-3 gap-4 mt-8">
                    <!-- Trello Option -->
                    <div class="cursor-pointer">
                        <div id="trello"
                            class="app-option bg-gray-100 rounded-lg p-4 flex flex-col items-center justify-center h-24 border border-gray-200 hover:shadow-md transition duration-200">
                            <div class="text-blue-500 mb-2">
                                <svg width="40" height="40" viewBox="0 0 60 60" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M54.1406 0H5.85938C2.62333 0 0 2.62333 0 5.85938V54.1406C0 57.3767 2.62333 60 5.85938 60H54.1406C57.3767 60 60 57.3767 60 54.1406V5.85938C60 2.62333 57.3767 0 54.1406 0Z"
                                        fill="url(#paint0_linear_0_1)" />
                                    <path
                                        d="M49.3874 7.7998H36.7124C35.1591 7.7998 33.8999 9.059 33.8999 10.6123V31.2373C33.8999 32.7906 35.1591 34.0498 36.7124 34.0498H49.3874C50.9407 34.0498 52.1999 32.7906 52.1999 31.2373V10.6123C52.1999 9.059 50.9407 7.7998 49.3874 7.7998Z"
                                        fill="white" />
                                    <path
                                        d="M23.2875 7.7998H10.6125C9.05925 7.7998 7.80005 9.059 7.80005 10.6123V46.2373C7.80005 47.7906 9.05925 49.0498 10.6125 49.0498H23.2875C24.8409 49.0498 26.1 47.7906 26.1 46.2373V10.6123C26.1 9.059 24.8409 7.7998 23.2875 7.7998Z"
                                        fill="white" />
                                    <defs>
                                        <linearGradient id="paint0_linear_0_1" x1="3000" y1="0"
                                            x2="3000" y2="6000" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#0091E6" />
                                            <stop offset="1" stop-color="#0079BF" />
                                        </linearGradient>
                                    </defs>
                                </svg>

                            </div>
                            <span class="font-medium">Trello</span>
                        </div>
                    </div>

                    <!-- Jira Option -->
                    <div class="cursor-pointer">
                        <div id="jira"
                            class="app-option bg-gray-100 rounded-lg p-4 flex flex-col items-center justify-center h-24 border border-gray-200 hover:shadow-md transition duration-200">
                            <div class="text-blue-500 mb-2">
                                <svg width="44" height="44" viewBox="0 0 59 59" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M49.7919 7.375H28.4883C28.4883 12.685 32.7861 16.9846 38.0974 16.9846H42.0154V20.7763C42.0154 26.0863 46.315 30.3859 51.625 30.3859V9.20815C51.6247 8.72208 51.4314 8.25602 51.0877 7.91232C50.744 7.56862 50.278 7.37537 49.7919 7.375Z"
                                        fill="#2684FF" />
                                    <path
                                        d="M39.2355 17.9951H17.9314C17.9314 23.3047 22.2296 27.6043 27.5392 27.6043H31.459V31.396C31.459 36.706 35.7586 41.0056 41.0686 41.0056V19.8278C41.0679 19.3418 40.8745 18.876 40.5309 18.5324C40.1873 18.1888 39.7214 17.9958 39.2355 17.9951Z"
                                        fill="url(#paint0_linear_31_410)" />
                                    <path
                                        d="M28.6791 28.6143H7.375C7.375 33.9261 11.6728 38.2239 16.9846 38.2239H20.9026V42.0174C20.9026 47.3274 25.2022 51.6252 30.5118 51.6252V30.4492C30.5113 29.9632 30.3181 29.4971 29.9745 29.1532C29.631 28.8093 29.1651 28.6152 28.6791 28.6143Z"
                                        fill="url(#paint1_linear_31_410)" />
                                    <defs>
                                        <linearGradient id="paint0_linear_31_410" x1="40.6252" y1="18.0191"
                                            x2="31.5613" y2="27.365" gradientUnits="userSpaceOnUse">
                                            <stop offset="0.176" stop-color="#0052CC" />
                                            <stop offset="1" stop-color="#2684FF" />
                                        </linearGradient>
                                        <linearGradient id="paint1_linear_31_410" x1="30.6818" y1="28.6963"
                                            x2="20.202" y2="38.8922" gradientUnits="userSpaceOnUse">
                                            <stop offset="0.176" stop-color="#0052CC" />
                                            <stop offset="1" stop-color="#2684FF" />
                                        </linearGradient>
                                    </defs>
                                </svg>

                            </div>
                            <span class="font-medium">Jira</span>
                        </div>
                    </div>

                    <!-- Linear Option -->
                    <div class="cursor-pointer">
                        <div id="linear"
                            class="app-option bg-gray-100 rounded-lg p-4 flex flex-col items-center justify-center h-24 border border-gray-200 hover:shadow-md transition duration-200">
                            <div class="text-indigo-600 mb-2">
                                <svg width="40" height="40" viewBox="0 0 49 48" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_31_425)">
                                        <mask id="mask0_31_425" style="mask-type:luminance" maskUnits="userSpaceOnUse"
                                            x="0" y="0" width="49" height="48">
                                            <path d="M49 0H0V48H49V0Z" fill="white" />
                                        </mask>
                                        <g mask="url(#mask0_31_425)">
                                            <path
                                                d="M24.5 48C38.031 48 49 37.2548 49 24C49 10.7452 38.031 0 24.5 0C10.969 0 0 10.7452 0 24C0 37.2548 10.969 48 24.5 48Z"
                                                fill="#5E6AD2" />
                                            <mask id="mask1_31_425" style="mask-type:luminance"
                                                maskUnits="userSpaceOnUse" x="7" y="7" width="35" height="34">
                                                <path d="M41.6501 7.2002H7.3501V40.8002H41.6501V7.2002Z"
                                                    fill="white" />
                                            </mask>
                                            <g mask="url(#mask1_31_425)">
                                                <path
                                                    d="M7.77041 27.8719C7.6941 27.5532 8.08168 27.3524 8.31798 27.5839L20.8418 39.8521C21.078 40.0836 20.8731 40.4632 20.5478 40.3885C14.2277 38.9362 9.25299 34.0629 7.77041 27.8719ZM7.35075 22.9549C7.3447 23.0501 7.38123 23.143 7.45007 23.2105L25.3062 40.7023C25.375 40.7697 25.47 40.8056 25.5671 40.7995C26.3797 40.75 27.1771 40.645 27.9553 40.4885C28.2174 40.4357 28.3086 40.1201 28.1192 39.9347L8.23365 20.4549C8.04436 20.2694 7.72218 20.3587 7.66832 20.6155C7.50852 21.3778 7.40136 22.1589 7.35075 22.9549ZM8.79445 17.1812C8.73734 17.3068 8.76642 17.4536 8.86567 17.5508L31.0838 39.3156C31.1831 39.4128 31.333 39.4412 31.4611 39.3853C32.0737 39.118 32.6675 38.8165 33.2397 38.4835C33.4292 38.3733 33.4584 38.1183 33.3027 37.9658L10.2435 15.3773C10.0877 15.2247 9.82754 15.2533 9.71505 15.4389C9.37503 15.9995 9.06732 16.5811 8.79445 17.1812ZM11.692 13.2731C11.5651 13.1487 11.5572 12.9493 11.6768 12.8181C14.8204 9.37053 19.3933 7.2002 24.4836 7.2002C33.9643 7.2002 41.6501 14.7291 41.6501 24.0163C41.6501 29.0028 39.4345 33.4823 35.9151 36.5617C35.7813 36.6789 35.5776 36.6712 35.4507 36.5468L11.692 13.2731Z"
                                                    fill="white" />
                                            </g>
                                        </g>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_31_425">
                                            <rect width="49" height="48" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>

                            </div>
                            <span class="font-medium">Linear</span>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-8 pt-6 border-t border-gray-200">
                    <a href="#" class="font-medium text-[#1E3A8A] hover:text-[#1E3A8A]">Skip for now</a>
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
                    url: '/auth/choose',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        service,
                        "_token": "{{ csrf_token() }}"
                    }),
                    success: function(response) {
                        toastr.success(response.message || 'Connecting to ' + service + '...');
                        location.href = response.link;
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Connection failed');
                    }
                });
            });
        });
    </script>
</body>

</html>
