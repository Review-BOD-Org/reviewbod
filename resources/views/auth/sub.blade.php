<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing Page - Reviewbod</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link href="https://api.fontshare.com/v2/css?f[]=switzer@100,200,300,400,500,600,700,800,900&display=swap"
        rel="stylesheet">
          <link rel="icon" type="image/x-icon" href="/rb.svg">

    <style>
        body {
            font-family: 'Switzer', sans-serif;
        }

        .main-sub {
            background: linear-gradient(180deg, #CB964F 0%, #333333 100%);
            color: white;
        }

        .sub{
            height:400px;
                justify-content: space-between;
    display: flex;
    flex-direction: column;
        }
    </style>
</head>

<body class="bg-white">

    <div class="flex w-full h-screen">
        <!-- Left side with logo and image (reduced width from 45% to 40%) -->
        <div class="w-[40%] bg-[#EBEBEB] h-full flex flex-col p-8">
            <!-- Logo at top left -->
            <div class="mb-8">
                <img src="/rb.svg" width="70" alt="Logo">
            </div>

            <!-- Centered image -->
            <div class="flex-grow flex items-center justify-center">
                <img src="/images/card-img.svg" alt="Login Image">
            </div>
        </div>

        <!-- Right side with pricing plans (increased width from 55% to 60%) -->
        <div class="w-[60%] h-full flex items-center justify-center">
            <div class="w-[90%] max-w-5xl">
                <h1 class="text-5xl font-bold mb-4 text-gray-800 text-center">Plans for Your Need</h1>
                <div class="flex flex-col">
                    <p class="text-gray-600 text-center   text-[20px]">
                        Select from best plan, ensuring a perfect match. Need more or
                    </p>
                    <p class="text-gray-600 text-center text-[20px]">
                        less? Contact us for custom solutions.
                    </p>
                </div>
                <!-- Increased spacing between heading and cards -->
                <div class="flex space-x-6 mt-[100px]">
                    <!-- Basic Plan (made slightly larger) -->
                    <div class="sub flex-1 border border-gray-200 rounded-3xl p-7 bg-white shadow">
                        <div class="flex mb-4">
                            <div class="">
                                <svg width="40" height="40" viewBox="0 0 40 40" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <rect x="0.5" y="0.5" width="39" height="39" rx="19.5"
                                        fill="url(#paint0_linear_31_3125)" />
                                    <rect x="0.5" y="0.5" width="39" height="39" rx="19.5"
                                        stroke="url(#paint1_linear_31_3125)" />
                                    <path opacity="0.6"
                                        d="M21.6666 23.3328C21.6666 24.8078 21.025 26.1411 20 27.0495C19.1166 27.8495 17.95 28.3328 16.6666 28.3328C13.9083 28.3328 11.6666 26.0911 11.6666 23.3328C11.6666 21.0328 13.2333 19.0828 15.35 18.5078C15.925 19.9578 17.1583 21.0745 18.6833 21.4911C19.1 21.6078 19.5416 21.6661 20 21.6661C20.4583 21.6661 20.9 21.6078 21.3166 21.4911C21.5416 22.0578 21.6666 22.6828 21.6666 23.3328Z"
                                        fill="white" />
                                    <path
                                        d="M25 16.666C25 17.316 24.875 17.941 24.65 18.5077C24.075 19.9577 22.8417 21.0743 21.3167 21.491C20.9 21.6077 20.4583 21.666 20 21.666C19.5417 21.666 19.1 21.6077 18.6833 21.491C17.1583 21.0743 15.925 19.9577 15.35 18.5077C15.125 17.941 15 17.316 15 16.666C15 13.9077 17.2417 11.666 20 11.666C22.7583 11.666 25 13.9077 25 16.666Z"
                                        fill="white" />
                                    <path opacity="0.4"
                                        d="M28.3333 23.3328C28.3333 26.0911 26.0917 28.3328 23.3333 28.3328C22.05 28.3328 20.8833 27.8495 20 27.0495C21.025 26.1411 21.6667 24.8078 21.6667 23.3328C21.6667 22.6828 21.5417 22.0578 21.3167 21.4911C22.8417 21.0745 24.075 19.9578 24.65 18.5078C26.7667 19.0828 28.3333 21.0328 28.3333 23.3328Z"
                                        fill="white" />
                                    <defs>
                                        <linearGradient id="paint0_linear_31_3125" x1="20" y1="0"
                                            x2="20" y2="40" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#1E3A8A" />
                                            <stop offset="1" stop-color="#3D3D3D" />
                                        </linearGradient>
                                        <linearGradient id="paint1_linear_31_3125" x1="20" y1="0"
                                            x2="20" y2="40" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="white" stop-opacity="0" />
                                            <stop offset="1" stop-color="white" stop-opacity="0.24" />
                                        </linearGradient>
                                    </defs>
                                </svg>

                            </div>
                        </div>
                       <div class="flex flex-col">
 <h2 class="text-2xl font-bold text-left mb-1">Basic</h2>
                        <p class="text-gray-600 text-left mb-6">Best for personal use.</p>
                       </div>
                        <div class="text-4xl font-bold text-left mb-8">
                            $20 <span class="text-lg font-normal text-gray-600">/ per month</span>
                        </div>
                        <button
                            class="w-full border border-gray-300 rounded-lg py-3 font-medium hover:bg-gray-50 transition">Get
                            Started</button>
                    </div>

                    <!-- Enterprise Plan (Highlighted and made slightly larger) -->
                    <div class="sub flex-1 border border-gray-200 bottom-[20px] relative rounded-3xl p-7 bg-white shadow main-sub">
                        <div class="flex mb-4">
                            <div class="">
                                <svg width="40" height="40" viewBox="0 0 40 40" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <rect x="0.5" y="0.5" width="39" height="39" rx="19.5"
                                        fill="url(#paint0_linear_31_3160)" />
                                    <rect x="0.5" y="0.5" width="39" height="39" rx="19.5"
                                        stroke="url(#paint1_linear_31_3160)" />
                                    <path opacity="0.6"
                                        d="M21.6667 23.3328C21.6667 24.8078 21.0251 26.1411 20.0001 27.0495C19.1167 27.8495 17.9501 28.3328 16.6667 28.3328C13.9084 28.3328 11.6667 26.0911 11.6667 23.3328C11.6667 21.0328 13.2334 19.0828 15.3501 18.5078C15.9251 19.9578 17.1584 21.0745 18.6834 21.4911C19.1001 21.6078 19.5417 21.6661 20.0001 21.6661C20.4584 21.6661 20.9001 21.6078 21.3167 21.4911C21.5417 22.0578 21.6667 22.6828 21.6667 23.3328Z"
                                        fill="black" />
                                    <path
                                        d="M25 16.666C25 17.316 24.875 17.941 24.65 18.5077C24.075 19.9577 22.8417 21.0743 21.3167 21.491C20.9 21.6077 20.4583 21.666 20 21.666C19.5417 21.666 19.1 21.6077 18.6833 21.491C17.1583 21.0743 15.925 19.9577 15.35 18.5077C15.125 17.941 15 17.316 15 16.666C15 13.9077 17.2417 11.666 20 11.666C22.7583 11.666 25 13.9077 25 16.666Z"
                                        fill="black" />
                                    <path opacity="0.4"
                                        d="M28.3333 23.3328C28.3333 26.0911 26.0917 28.3328 23.3333 28.3328C22.05 28.3328 20.8833 27.8495 20 27.0495C21.025 26.1411 21.6667 24.8078 21.6667 23.3328C21.6667 22.6828 21.5417 22.0578 21.3167 21.4911C22.8417 21.0745 24.075 19.9578 24.65 18.5078C26.7667 19.0828 28.3333 21.0328 28.3333 23.3328Z"
                                        fill="black" />
                                    <defs>
                                        <linearGradient id="paint0_linear_31_3160" x1="20" y1="0"
                                            x2="20" y2="40" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="white" />
                                            <stop offset="1" stop-color="#CDCDCD" />
                                        </linearGradient>
                                        <linearGradient id="paint1_linear_31_3160" x1="20" y1="0"
                                            x2="20" y2="40" gradientUnits="userSpaceOnUse">
                                            <stop stop-opacity="0" />
                                            <stop offset="1" stop-opacity="0.24" />
                                        </linearGradient>
                                    </defs>
                                </svg>


                            </div>
                        </div>
                        <div class="flex flex-col">
 <h2 class="text-2xl font-bold text-left mb-1">Basic</h2>
                        <p class="text-white text-left mb-6">Best for personal use.</p>
                       </div>
                        <div class="text-4xl font-bold text-left mb-8">
                            $20 <span class="text-lg font-normal text-white">/ per month</span>
                        </div>
                        <button
                            class="w-full border bg-white text-black border-gray-300 rounded-lg py-3 font-medium hover:bg-gray-50 transition">Get
                            Started</button>
                    </div>

                    <!-- Business Plan (made slightly larger) -->
                    <div class="sub flex-1 border border-gray-200 rounded-3xl p-7 bg-white shadow">
                        <div class="flex mb-4">
                            <div class="">
                                <svg width="40" height="40" viewBox="0 0 40 40" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <rect x="0.5" y="0.5" width="39" height="39" rx="19.5"
                                        fill="url(#paint0_linear_31_3125)" />
                                    <rect x="0.5" y="0.5" width="39" height="39" rx="19.5"
                                        stroke="url(#paint1_linear_31_3125)" />
                                    <path opacity="0.6"
                                        d="M21.6666 23.3328C21.6666 24.8078 21.025 26.1411 20 27.0495C19.1166 27.8495 17.95 28.3328 16.6666 28.3328C13.9083 28.3328 11.6666 26.0911 11.6666 23.3328C11.6666 21.0328 13.2333 19.0828 15.35 18.5078C15.925 19.9578 17.1583 21.0745 18.6833 21.4911C19.1 21.6078 19.5416 21.6661 20 21.6661C20.4583 21.6661 20.9 21.6078 21.3166 21.4911C21.5416 22.0578 21.6666 22.6828 21.6666 23.3328Z"
                                        fill="white" />
                                    <path
                                        d="M25 16.666C25 17.316 24.875 17.941 24.65 18.5077C24.075 19.9577 22.8417 21.0743 21.3167 21.491C20.9 21.6077 20.4583 21.666 20 21.666C19.5417 21.666 19.1 21.6077 18.6833 21.491C17.1583 21.0743 15.925 19.9577 15.35 18.5077C15.125 17.941 15 17.316 15 16.666C15 13.9077 17.2417 11.666 20 11.666C22.7583 11.666 25 13.9077 25 16.666Z"
                                        fill="white" />
                                    <path opacity="0.4"
                                        d="M28.3333 23.3328C28.3333 26.0911 26.0917 28.3328 23.3333 28.3328C22.05 28.3328 20.8833 27.8495 20 27.0495C21.025 26.1411 21.6667 24.8078 21.6667 23.3328C21.6667 22.6828 21.5417 22.0578 21.3167 21.4911C22.8417 21.0745 24.075 19.9578 24.65 18.5078C26.7667 19.0828 28.3333 21.0328 28.3333 23.3328Z"
                                        fill="white" />
                                    <defs>
                                        <linearGradient id="paint0_linear_31_3125" x1="20" y1="0"
                                            x2="20" y2="40" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#1E3A8A" />
                                            <stop offset="1" stop-color="#3D3D3D" />
                                        </linearGradient>
                                        <linearGradient id="paint1_linear_31_3125" x1="20" y1="0"
                                            x2="20" y2="40" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="white" stop-opacity="0" />
                                            <stop offset="1" stop-color="white" stop-opacity="0.24" />
                                        </linearGradient>
                                    </defs>
                                </svg>

                            </div>
                        </div>
  <div class="flex flex-col">
 <h2 class="text-2xl font-bold text-left mb-1">Basic</h2>
                        <p class="text-gray-600 text-left mb-6">Best for personal use.</p>
                       </div>
                        <div class="text-4xl font-bold text-left mb-8">
                            $20 <span class="text-lg font-normal text-gray-600">/ per month</span>
                        </div>
                        <button
                            class="w-full border border-gray-300 rounded-lg py-3 font-medium hover:bg-gray-50 transition">Get
                            Started</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
<script>
$("button").click(()=>{
    toastr.success('You have successfully subscribed to the plan');
    setTimeout(() => {
        window.location.href = "/auth/linking";
    }, 2000);
})
    </script>
</html>