<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create WorkSpace - Reviewbod</title>
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
                <img src="/images/login-img.svg" alt="Workspace Image">
            </div>
        </div>

        <!-- Right side with create workspace form -->
        <div class="w-1/2 h-full flex items-center justify-center">
            <div class="w-4/5 max-w-md">
                <h1 class="text-4xl font-bold mb-4 text-gray-800">Create Your Workspace</h1>
                <p class="text-gray-600 mb-8 text-lg">Let's get started by setting up your new workspace</p>

                <form id="createWorkspaceForm" class="space-y-6">
                    <div class="mb-8">
                        <label for="workspaceName" class="block text-lg font-medium text-gray-700 mb-2">Workspace Name</label>
                        <input type="text" id="workspaceName"
                            class="w-full px-4 py-4 bg-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3A8A] focus:bg-white transition duration-200"
                            placeholder="Enter your workspace name">
                        <p class="text-sm text-gray-500 mt-2">Choose a name that represents your team or project</p>
                    </div>

                    <button type="submit"
                        class="w-full bg-[#1E3A8A] text-white py-4 rounded-lg hover:bg-indigo-900 transition duration-200 text-lg font-medium">Create Workspace</button>

                 
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#createWorkspaceForm').on('submit', function(e) {
                e.preventDefault();

                const workspaceName = $('#workspaceName').val().trim();

                // Basic validation
                if (!workspaceName) {
                    toastr.error('Please enter a workspace name');
                    return;
                }

                if (workspaceName.length < 3) {
                    toastr.error('Workspace name must be at least 3 characters long');
                    return;
                }

                $("button[type='submit']").attr("disabled", true);
                $("button[type='submit']").text("Creating Workspace...");

                // Simulated AJAX request to create workspace
                $.ajax({
                    url: '/auth/create',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        name: workspaceName,
                        "_token": "{{ csrf_token() }}"
                    }),
                    success: function(response) {
                        toastr.success('Workspace created successfully!');
                        // Handle successful creation (e.g., redirect to workspace)
                        setTimeout(() => {
                            location.href = response.redirect || '/auth/choose';
                        }, 1500);
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Failed to create workspace');
                    },
                    complete: function() {
                        $("button[type='submit']").attr("disabled", false);
                        $("button[type='submit']").text("Create Workspace");
                    }
                });
            });

            // Real-time validation feedback
            $('#workspaceName').on('input', function() {
                const value = $(this).val().trim();
                const input = $(this);
                
                if (value.length > 0 && value.length < 3) {
                    input.removeClass('focus:ring-[#1E3A8A]').addClass('focus:ring-red-500 border-red-300');
                } else {
                    input.removeClass('focus:ring-red-500 border-red-300').addClass('focus:ring-[#1E3A8A]');
                }
            });
        });
    </script>
</body>

</html>