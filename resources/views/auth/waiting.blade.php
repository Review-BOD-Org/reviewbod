<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviewbod</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
      <link rel="icon" type="image/x-icon" href="/rb.svg">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        
        .linking {
            background: linear-gradient(90deg, #CB964F 0%, #1E3A8A 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        @keyframes colorTransition {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
        
        .animate-gradient {
            background: linear-gradient(90deg, #CB964F 0%, #1E3A8A 50%, #CB964F 100%);
            background-size: 200% 100%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: colorTransition 3s ease-in-out infinite;
            line-height: 1.2;
            padding-bottom: 0.1em;
        }
        
        .fade-out {
            animation: fadeOut 1s ease-in-out forwards;
        }
        
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
    </style>
</head>
<body class="bg-white">
    <div class="flex w-full h-full">
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
            <div class="text-center">
                <h1 class="text-6xl font-bold text-gray-800 animate-gradient" id="mainText">Linking your Data</h1>
            </div>
        </div>
    </div>

    <script>
        // Set a timer to add fade-out animation before redirecting
        setTimeout(() => {
            document.getElementById('mainText').classList.add('fade-out');
            
            // Redirect after fade animation is complete
            setTimeout(() => {
                location.href = "/dashboard";
            }, 1000);
        }, 3000);
    </script>
</body>
</html>