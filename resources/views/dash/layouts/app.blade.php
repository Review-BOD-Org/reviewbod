<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Linear App Clone') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.tailwindcss.css">
 <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script> 
 <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<script src="https://cdn.datatables.net/2.3.2/js/dataTables.tailwindcss.js"></script> 
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400..800&family=Schibsted+Grotesk:ital,wght@0,400..900;1,400..900&family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
<!-- Google Charts -->
<script src="https://www.gstatic.com/charts/loader.js"></script> 
<!-- CSRF Token Meta (Required for Laravel AJAX) -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<div id="preloader" class="preloader">
    <div class="logo-container">
        <img src="/rb.svg" alt="Logo" class="logo">
    </div>
</div>

<style>
body {
  font-family: "Urbanist", sans-serif !important;

}

    /* Preloader Styles */
.preloader {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    opacity: 1;
    transition: opacity 0.5s ease;
}

.logo-container {
    text-align: center;
}

.logo {
    width: 100px; /* Adjust the size of the logo */
}

/* Hide preloader after content is loaded */
.preloader.hidden {
    opacity: 0;
    pointer-events: none;
}

 
        body {
            /* font-family: 'Inter', sans-serif; */
            margin: 0;
            padding: 0;
            /* Removed overflow: hidden */
        }
        html, body {
            height: 100%;
        }
        .dropdown-content {
            display: none;
        }
        .dropdown-content.show {
            display: block;
        }
    </style>
</head>
<body class="bg-white h-screen flex">
    <!-- Sidebar -->
    @include('dash.layouts.partials.sidebar')
    
    <!-- Main content -->
    {{-- <div class="flex-1 flex flex-col h-screen"> --}}
        <!-- Top navigation -->
        @include('dash.layouts.partials.topnav')
        
        <!-- Page content - Changed to overflow-y-auto to enable scrolling -->
        <div class="flex-1 overflow-y-auto content">
            @yield('content')
        </div>
    {{-- </div> --}}
    
    <!-- Chat button (bottom right) -->
    @include('dash.layouts.partials.chat-button')
    
    <!-- JavaScript for dropdown functionality -->
    <script>

        // Show preloader when an AJAX request starts
// $(document).ajaxStart(function () {
//     $('#preloader').css({"display":"flex"}); // Show preloader 
// });

// Hide preloader when all AJAX requests complete
// $(document).ajaxStop(function () {
//    setTimeout(() => {
//     $('#preloader').hide(); // Hide preloader 
//    }, 1000);
// });


        // Function to toggle dropdown visibility
        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            
            // Close all other dropdowns first
            const allDropdowns = document.querySelectorAll('.dropdown-content');
            allDropdowns.forEach(menu => {
                if (menu.id !== id && menu.classList.contains('show')) {
                    menu.classList.remove('show');
                }
            });
            
            // Toggle the clicked dropdown
            dropdown.classList.toggle('show');
        }
        
        // Close dropdowns when clicking outside
        window.addEventListener('click', function(event) {
            if (!event.target.closest('.dropdown-content') && 
                !event.target.closest('[onclick*="toggleDropdown"]')) {
                const dropdowns = document.querySelectorAll('.dropdown-content');
                dropdowns.forEach(dropdown => {
                    if (dropdown.classList.contains('show')) {
                        dropdown.classList.remove('show');
                    }
                });
            }
        });
    </script>
    @yield('scripts')
</body>
</html>