<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Error Fetching Data</title>
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
  <div class="max-w-sm w-full bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="bg-blue-600 h-2"></div>
    <div class="p-6">
      <div class="flex items-center justify-center mb-4">
        <div class="rounded-full bg-blue-100 p-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
        </div>
      </div>
      
      <h1 class="text-center text-xl font-bold text-gray-800 mb-2">Error Fetching Data</h1>
      
      <div class="mt-6 flex justify-center">
        <button onclick="window.location.reload()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-red-700 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
          Try Again
        </button>
      </div>
    </div>
  </div>
</body>