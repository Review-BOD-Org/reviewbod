<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviewbod</title>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="icon" type="image/x-icon" href="/rb.svg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&family=Schibsted+Grotesk:ital,wght@0,400..900;1,400..900&display=swap"
        rel="stylesheet">
</head>
<style>
    body {
        font-family: "Plus Jakarta Sans", sans-serif;
    }

    .rb-header {
        background: linear-gradient(179.7deg, #FFFFFF 34.08%, #EEF2FF 56.94%, #323C4C 89.79%)
    }

    .rb-shadow {
        box-shadow: 0px 4px 4px #4285F4;
    }
</style>

<body class="bg-white">


    <nav class="bg-white  fixed w-full z-20 top-0 start-0">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
            <a href="/" class="flex items-center space-x-3 rtl:space-x-reverse">
                <img src="/rb.svg" class="h-14" alt="RB">

                <span class="self-center text-dark text-2xl mt-2 font-bold">Reviewbod</span>
            </a>
            <div class="flex md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
                <button type="button" onclick="location.href='/auth/login'"
                    class="rb-shadow text-white bg-[#1E3A8A] hover:bg-blue-800   font-medium rounded-full text-sm px-8 py-2 text-center">Get
                    started</button>
                <button data-collapse-toggle="navbar-sticky" type="button"
                    class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
                    aria-controls="navbar-sticky" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 17 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 1h15M1 7h15M1 13h15" />
                    </svg>
                </button>
            </div>
           <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-sticky">
    <ul class="flex flex-col p-4 md:p-0 mt-4 font-medium border border-gray-100 rounded-lg md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 text-black">
        
        <li>
            <a href="/how" class="block py-2 px-3 text-black rounded-sm {{ request()->is('how') ? 'border-b-2 border-[##bd9555]' : '' }}">
                How It Works
            </a>
        </li>
        <li>
            <a href="/pricing" class="block py-2 px-3 text-black rounded-sm {{ request()->is('pricing') ? 'border-b-2 border-[##bd9555]' : '' }}">
                Pricing
            </a>
        </li>
        <li>
            <a href="/faq" class="block py-2 px-3 text-black rounded-sm {{ request()->is('faq') ? 'border-b-2 border-[##bd9555]' : '' }}">
                FAQ
            </a>
        </li>
        <li>
            <a href="/support" class="block py-2 px-3 text-black rounded-sm {{ request()->is('support') ? 'border-b-2 border-[##bd9555]' : '' }}">
                Help & Support
            </a>
        </li>
    </ul>
</div>
        </div>
    </nav>