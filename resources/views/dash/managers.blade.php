<!-- views/dash/members.blade.php -->
@extends('dash.layouts.app')

@section('page-title', 'Members')

@section('content')
    <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet">
    <!--Responsive Extension Datatables CSS-->
    <link href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css" rel="stylesheet">
    <!-- Add these styles to your existing CSS -->
    <!-- Custom CSS for toggle switch -->
    <style>
        .toggle-checkbox:checked {
            right: 0;
            border-color: #CB964F;
            background-color: #CB964F;
        }

        .toggle-checkbox:checked+.toggle-label {
            background-color: #CB964F;
        }

        .toggle-label {
            transition: background-color 0.2s;
        }

        .toggle-checkbox {
            transition: all 0.2s;
            right: 1rem;
        }

        /* Modal animation classes */
        .modal-panel {
            transform: translateX(100%);
        }

        .modal-panel.show {
            transform: translateX(0);
        }

        .modal-backdrop {
            opacity: 0;
        }

        .modal-backdrop.show {
            opacity: 1;
        }

        /* Better dropdown styles */
        .dropdown-container {
            position: relative;
            display: inline-block;
        }

        .dropdown-button {
            position: relative;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: transparent;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            color: #6b7280;
        }

        .dropdown-button:hover {
            background-color: #f3f4f6;
            color: #374151;
            transform: scale(1.05);
        }

        .dropdown-button:active {
            transform: scale(0.95);
            background-color: #e5e7eb;
        }

        .dropdown-button svg {
            pointer-events: none;
            transition: transform 0.2s ease;
        }

        .dropdown-button:hover svg {
            transform: scale(1.1);
        }

        .dropdown-menu {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            z-index: 99999;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            min-width: 180px;
            overflow: hidden;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px) scale(0.95);
            transition: all 0.2s ease;
            transform-origin: top right;
        }

        .dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
        }

        .dropdown-menu::before {
            content: '';
            position: absolute;
            top: -8px;
            right: 12px;
            width: 16px;
            height: 16px;
            background: white;
            border: 1px solid #e5e7eb;
            border-bottom: none;
            border-right: none;
            transform: rotate(45deg);
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            text-decoration: none;
            color: #374151;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.15s ease;
            border: none;
            background: none;
            cursor: pointer;
            width: 100%;
            text-align: left;
        }

        .dropdown-item:hover {
            background-color: #f8fafc;
            color: #1f2937;
            padding-left: 20px;
        }

        .dropdown-item.danger {
            color: #dc2626;
        }

        .dropdown-item.danger:hover {
            background-color: #fef2f2;
            color: #b91c1c;
        }

        .dropdown-item.primary {
            color: #2563eb;
        }

        .dropdown-item.primary:hover {
            background-color: #eff6ff;
            color: #1d4ed8;
        }

        .dropdown-item svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }

        .dropdown-divider {
            height: 1px;
            background-color: #f3f4f6;
            margin: 8px 0;
        }


        /* Special handling for dropdowns in last few rows */
        tbody tr:nth-last-child(-n+3) .dropdown-menu {
            bottom: calc(100% + 4px);
            top: auto;
        }

        /* Ensure dropdown doesn't get cut off by table borders */
        table {
            overflow: visible !important;
        }

        /* Fix for DataTables wrapper */
        .dataTables_wrapper {
            overflow: visible !important;
        }

        /* Additional z-index fixes for potential conflicts */
        .dataTables_wrapper .dataTables_paginate,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_length {
            z-index: 1;
        }

        /* Ensure Alpine.js dropdown animations work smoothly */
        [x-cloak] {
            display: none !important;
        }

        /* Fix for potential scrollbar interference */
        .table-container::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .table-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .table-container::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Existing styles... */
        .custom-checkbox:checked {
            background-color: #3b82f6;
            background-image: url("data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIiIGhlaWdodD0iOSIgdmlld0JveD0iMCAwIDEyIDkiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxwYXRoIGQ9Ik0xIDQuNUw0LjUgOEwxMSAxIiBzdHJva2U9IndoaXRlIiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIvPgo8L3N2Zz4K");
            background-repeat: no-repeat;
            background-position: center;
        }

        /* Bulk actions bar */
        .bulk-actions-bar {
            transform: translateY(-100%);
            transition: transform 0.3s ease-in-out;
        }

        .bulk-actions-bar.show {
            transform: translateY(0);
        }

        /* Selected row highlighting */
        tr.selected {
            background-color: #eff6ff !important;
            border-left: 3px solid #3b82f6;
        }
    </style>
    @include('dash.layouts.partials.head')

    <!-- Add Manager Sidebar Modal -->
    <div id="addManagerModal" class="fixed inset-0 z-50 overflow-hidden hidden" aria-labelledby="slide-over-title"
        role="dialog" aria-modal="true">
        <!-- Background backdrop -->
        <div class="absolute inset-0 overflow-hidden">
            <!-- Background overlay -->
            <div class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity modal-backdrop"></div>

            <!-- Sidebar panel -->
            <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
                <div class="w-screen max-w-md transform transition ease-in-out duration-300 modal-panel translate-x-full">
                    <div class="h-full flex flex-col bg-white shadow-xl">
                        <!-- Header -->
                        <div class="px-6 py-6 bg-[#F4F7FC] border-b">
                            <div class="flex items-center justify-between">
                                <h2 class="text-xl font-semibold text-gray-900" id="slide-over-title">
                                    Add New Manager
                                </h2>
                                <button type="button"
                                    class="text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-full p-2"
                                    onclick="closeAddManagerModal()">
                                    <span class="sr-only">Close panel</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <p class="mt-1 text-sm text-gray-600">
                                Fill in the information below to add a new manager to your workspace.
                            </p>
                        </div>

                        <!-- Form Content -->
                        <div class="flex-1 overflow-y-auto">
                            <form id="addManagerForm" class="h-full">
                                <div class="px-6 py-6 space-y-6">

                                    <!-- Profile Picture Upload -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Profile Picture
                                        </label>
                                        <div class="flex items-center space-x-4">
                                            <div
                                                class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center overflow-hidden">
                                                <img id="profilePreview" src="" alt="Profile preview"
                                                    class="w-full h-full object-cover hidden">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <input type="file" id="profilePicture" name="profile_picture"
                                                    accept="image/*" class="hidden">
                                                <button type="button"
                                                    onclick="document.getElementById('profilePicture').click()"
                                                    class="bg-white border border-gray-300 rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    Upload Photo
                                                </button>
                                            </div>
                                        </div>
                                    </div>



                                    <!-- Full Name -->
                                    <div>
                                        <label for="fullName" class="block text-sm font-medium text-gray-700 mb-2">
                                            Full Name <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" id="fullName" name="name" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Enter full name">
                                    </div>

                                    <!-- Email Address -->
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                            Email Address <span class="text-red-500">*</span>
                                        </label>
                                        <input type="email" id="email" name="email" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="manager@example.com">
                                    </div>

                                    <!-- Phone Number -->
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                            Phone Number (Optional)
                                        </label>

                                        <input type="tel" id="phone" name="phone"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="+234 8012345678">
                                    </div>

                                    <!-- Department/Role -->
                                    <div>
                                        <label for="department" class="block text-sm font-medium text-gray-700 mb-2">
                                            Department/Role
                                        </label>
                                        <select id="department" name="department"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Select Department</option>
                                            <option value="sales">Sales Manager</option>
                                            <option value="marketing">Marketing Manager</option>
                                            <option value="operations">Operations Manager</option>
                                            <option value="hr">HR Manager</option>
                                            <option value="finance">Finance Manager</option>
                                            <option value="it">IT Manager</option>
                                            <option value="customer_service">Customer Service Manager</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>

                                    <!-- Notes/Comments -->
                                    <div>
                                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                            Notes (Optional)
                                        </label>
                                        <textarea id="notes" name="note" rows="3"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Add any additional notes about this manager..."></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Footer Actions -->
                        <div class="flex-shrink-0 px-6 py-4 border-t border-gray-200 bg-gray-50">
                            <div class="flex justify-end space-x-3">
                                <button type="button" onclick="closeAddManagerModal()"
                                    class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Cancel
                                </button>
                                <button type="submit" form="addManagerForm"
                                    class="bg-[#CB964F] py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-[#B8854A] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#CB964F] flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Add Manager
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Manager Sidebar Modal -->
    <div id="editManagerModal" class="fixed inset-0 z-50 overflow-hidden hidden" aria-labelledby="slide-over-title"
        role="dialog" aria-modal="true">
        <!-- Background backdrop -->
        <div class="absolute inset-0 overflow-hidden">
            <!-- Background overlay -->
            <div class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity modal-backdrop"></div>

            <!-- Sidebar panel -->
            <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
                <div class="w-screen max-w-md transform transition ease-in-out duration-300 modal-panel translate-x-full">
                    <div class="h-full flex flex-col bg-white shadow-xl">
                        <!-- Header -->
                        <div class="px-6 py-6 bg-[#F4F7FC] border-b">
                            <div class="flex items-center justify-between">
                                <h2 class="text-xl font-semibold text-gray-900" id="slide-over-title">
                                    Edit Manager
                                </h2>
                                <button type="button"
                                    class="text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-full p-2"
                                    onclick="closeEditManagerModal()">
                                    <span class="sr-only">Close panel</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <p class="mt-1 text-sm text-gray-600">
                                Update the information below to modify manager details.
                            </p>
                        </div>

                        <!-- Form Content -->
                        <div class="flex-1 overflow-y-auto">
                            <form id="editManagerForm" class="h-full">
                                <input type="hidden" id="editManagerId" name="manager_id">
                                <div class="px-6 py-6 space-y-6">

                                    <!-- Profile Picture Upload -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Profile Picture
                                        </label>
                                        <div class="flex items-center space-x-4">
                                            <div
                                                class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center overflow-hidden">
                                                <img id="editProfilePreview" src="" alt="Profile preview"
                                                    class="w-full h-full object-cover">
                                                <svg id="editProfilePlaceholder" class="w-8 h-8 text-gray-400 hidden"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <input type="file" id="editProfilePicture" name="profile_picture"
                                                    accept="image/*" class="hidden">
                                                <button type="button"
                                                    onclick="document.getElementById('editProfilePicture').click()"
                                                    class="bg-white border border-gray-300 rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    Change Photo
                                                </button>
                                            </div>
                                        </div>
                                    </div>
<input type="hidden" id="id" name="id"/>
                                    <!-- Full Name -->
                                    <div>
                                        <label for="editFullName" class="block text-sm font-medium text-gray-700 mb-2">
                                            Full Name <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" id="editFullName" name="name" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Enter full name">
                                    </div>

                                    <!-- Email Address -->
                                    <div>
                                        <label for="editEmail" class="block text-sm font-medium text-gray-700 mb-2">
                                            Email Address <span class="text-red-500">*</span>
                                        </label>
                                        <input type="email" id="editEmail" name="email" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="manager@example.com">
                                    </div>

                                    <!-- Phone Number -->
                                    <div>
                                        <label for="editPhone" class="block text-sm font-medium text-gray-700 mb-2">
                                            Phone Number (Optional)
                                        </label>
                                        <input type="tel" id="editPhone" name="phone"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="+234 8012345678">
                                    </div>

                                    <!-- Status/Role -->
                                    <div>
                                        <label for="editStatus" class="block text-sm font-medium text-gray-700 mb-2">
                                            Department/Role
                                        </label>
                                        <select id="editStatus" name="status"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Select Status</option>
                                        
                                           <option value="pending">Pending</option>
                                            <option value="active">Active</option>
                                            <option value="blocked">Blocked</option>
                                           
                                        </select>
                                    </div>

                                    <!-- Department/Role -->
                                    <div>
                                        <label for="editDepartment" class="block text-sm font-medium text-gray-700 mb-2">
                                            Department/Role
                                        </label>
                                        <select id="editDepartment" name="department"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Select Department</option>
                                            <option value="sales">Sales Manager</option>
                                            <option value="marketing">Marketing Manager</option>
                                            <option value="operations">Operations Manager</option>
                                            <option value="hr">HR Manager</option>
                                            <option value="finance">Finance Manager</option>
                                            <option value="it">IT Manager</option>
                                            <option value="customer_service">Customer Service Manager</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>

                                    <!-- Notes/Comments -->
                                    <div>
                                        <label for="editNotes" class="block text-sm font-medium text-gray-700 mb-2">
                                            Notes (Optional)
                                        </label>
                                        <textarea id="editNotes" name="note" rows="3"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Add any additional notes about this manager..."></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Footer Actions -->
                        <div class="flex-shrink-0 px-6 py-4 border-t border-gray-200 bg-gray-50">
                            <div class="flex justify-end space-x-3">
                                <button type="button" onclick="closeEditManagerModal()"
                                    class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Cancel
                                </button>
                                <button type="submit" form="editManagerForm"
                                    class="bg-[#CB964F] py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-[#B8854A] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#CB964F] flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Update Manager
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="p-2 mx-auto  py-1">
        <div class="p-5">
            <div class="border rounded-md">
                <div class="p-4 flex border-b justify-between">
                    <div class="flex flex-col">
                        <h2 class="text-[25px] font-bold">Managers</h2>
                        <span class="font-light text-lg">See managers and details</span>
                    </div>
                </div>
                <div class="p-2 mx-auto py-1">
                    <div class="p-5">
                        <div class="border rounded-md bg-white">

                            <!-- Updated Header Section with Bulk Actions Bar -->
                            <div class="p-6 flex border-b justify-between items-center bg-[#F4F7FC]">
                                <div class="flex items-center gap-4">
                                    <button class="p-2 border shadow shadow-sm rounded-md bg-white">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                        </svg>
                                    </button>
                                    <div class="relative flex items-center">
                                        <svg class="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                        <input name='search' type="text" placeholder="Search..."
                                            class="pl-10 pr-4 py-2 border shadow shadow-sm rounded-md w-64 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">



                                    </div>


                                </div>

                                <button
                                    class="btn flex items-center gap-2 shadow py-2 px-3 rounded-md bg-[#CB964F] text-white"
                                    onclick="openAddManagerModal()">
                                    <i class="fa fa-plus"></i>
                                    <span>Add Manager</span>
                                </button>
                            </div>

                            <div id="bulkActionsBar"
                                class="bulk-actions-bar bg-blue-50 border-b border-blue-200 px-6 py-3 hidden">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <span class="text-sm font-medium text-blue-900">
                                            <span id="selectedCount">0</span> user(s) selected
                                        </span>
                                        <button id="clearSelection"
                                            class="text-sm text-blue-600 hover:text-blue-800 underline">
                                            Clear selection
                                        </button>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button id="bulkDeleteUsers"
                                            class="bg-red-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center gap-2 transition-colors">
                                             <i class="fa fa-trash"></i>
                                            Delete Users
                                        </button>
                                        <button id="bulkBlockBtn"
                                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center gap-2 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728" />
                                            </svg>
                                            Block Users
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Table Section -->
                            <!-- Add these CSS fixes to your existing styles -->
                            <style>
                                /* Existing styles... */
                                .custom-checkbox:checked {
                                    background-color: #3b82f6;
                                    background-image: url("data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIiIGhlaWdodD0iOSIgdmlld0JveD0iMCAwIDEyIDkiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxwYXRoIGQ9Ik0xIDQuNUw0LjUgOEwxMSAxIiBzdHJva2U9IndoaXRlIiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIvPgo8L3N2Zz4K");
                                    background-repeat: no-repeat;
                                    background-position: center;
                                }

                                table.dataTable.no-footer {
                                    border-bottom: none !important;
                                }

                                th {
                                    border-bottom: 0.5px solid #e5e7eb !important;
                                }

                                #membersTable_filter {
                                    display: none !important;
                                }

                                /* NEW: Fix for dropdown z-index issues */
                                .overflow-x-auto {
                                    overflow: visible !important;
                                    /* Allow dropdowns to show outside table */
                                }

                                /* Ensure table container doesn't create stacking context issues */
                                .table-container {
                                    position: relative;
                                    z-index: 1;
                                }

                                /* Fix for dropdown positioning in table */
                                tbody tr {
                                    position: relative;
                                }


                                /* Fix for last few rows where dropdown might be cut off */
                                tbody tr:nth-last-child(-n+3) .dropdown-menu {
                                    bottom: 100%;
                                    top: auto;
                                    margin-bottom: 0.25rem;
                                    margin-top: 0;
                                }
                            </style>

                            <!-- Updated Table with Checkbox Functionality -->
                            <div class="overflow-x-auto table-container">
                                <table class="w-full" id="membersTable" style="width:100% !important">
                                    <thead>
                                        <tr class="border-b bg-[#F4F7FC]">
                                            <th class="text-left py-4 px-6 font-medium text-gray-600 text-sm w-12">
                                                <!-- UPDATED: Master checkbox with ID -->
                                                <input type="checkbox" id="masterCheckbox"
                                                    class="custom-checkbox w-5 h-5 rounded border-0 appearance-none focus:ring-0 focus:outline-none bg-white"
                                                    style="border: none !important;box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.1), 0px 0px 0px 1px rgba(70, 79, 96, 0.16), 0px 2px 5px rgba(89, 96, 120, 0.1);">
                                            </th>
                                            <th class="text-left py-4 px-6 font-medium text-gray-600 text-sm">
                                                <div class="flex items-center gap-1">
                                                    #
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                                                    </svg>
                                                </div>
                                            </th>
                                            <th class="text-left py-4 px-6 font-medium text-gray-600 text-sm">NAME</th>
                                            <th class="text-left py-4 px-6 font-medium text-gray-600 text-sm">EMAIL</th>
                                            <th class="text-left py-4 px-6 font-medium text-gray-600 text-sm">PHONE</th>
                                            <th class="text-left py-4 px-6 font-medium text-gray-600 text-sm">STATUS</th>
                                            <th class="text-left py-4 px-6 font-medium text-gray-600 text-sm">DATE</th>
                                            <th class="text-left py-4 px-6 font-medium text-gray-600 text-sm">ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach ($managers as $user)
                                            <tr class="hover:bg-gray-50/50" data-user-id="{{ $user->id }}">
                                                <td class="py-4 px-6">
                                                    <!-- UPDATED: Individual checkbox with data attributes -->
                                                    <input type="checkbox"
                                                        class="user-checkbox custom-checkbox w-5 h-5 rounded border-0 appearance-none focus:ring-0 focus:outline-none bg-white"
                                                        data-user-id="{{ $user->id }}"
                                                        data-user-name="{{ $user->name }}"
                                                        data-user-email="{{ $user->email }}"
                                                        data-user-status="{{ $user->status }}"
                                                        style="margin-left: 10%;border: none !important;box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.1), 0px 0px 0px 1px rgba(70, 79, 96, 0.16), 0px 2px 5px rgba(89, 96, 120, 0.1);">
                                                </td>
                                                <td class="py-4 px-6 text-sm font-medium text-gray-900">
                                                    {{ $user->manager_id }}
                                                </td>
                                                <td class="py-4 px-6">
                                                    <div class="flex gap-2 items-center">
                                                        <img src="{{ $user->image ? $user->image : '/profile.png' }}"
                                                             class="rounded-full" width="40" />
                                                        <span
                                                            class="text-sm font-medium text-gray-900">{{ $user->name }}</span>
                                                    </div>
                                                </td>
                                                <td class="py-4 px-6">
                                                    <span
                                                        class="text-sm font-medium text-gray-900">{{ $user->email ? $user->email : 'No email provided' }}</span>
                                                </td>

                                                <td class="py-4 px-6">
                                                    <span
                                                        class="text-sm font-medium text-gray-900">{{ $user->phone ? $user->phone : 'No phone number provided' }}</span>
                                                </td>


                                                <td class="py-4 px-6">
                                                    @if ($user->status == 'blocked')
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Blocked</span>
                                                    @elseif($user->status == 'pending')
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Pending
                                                            Invite</span>
                                                    @elseif($user->status == 'active')
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Not
                                                            Invited</span>
                                                    @endif
                                                </td>
                                                <td class="py-4 px-6 text-sm text-gray-600">
                                                    {{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('Y - D - M') : '--' }}
                                                </td>
                                                <td class="py-4 px-6">

                                                    <div class="dropdown-container">
                                                        <!-- Dropdown Button with your SVG -->
                                                        <button class="dropdown-button" aria-label="More actions">
                                                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg"
                                                                width="96" height="96" viewBox="0 0 512 512">
                                                                <circle cx="256" cy="256" r="48"
                                                                    fill="currentColor" />
                                                                <circle cx="416" cy="256" r="48"
                                                                    fill="currentColor" />
                                                                <circle cx="96" cy="256" r="48"
                                                                    fill="currentColor" />
                                                            </svg>
                                                        </button>

                                                        <!-- Dropdown Menu -->
                                                        <div class="dropdown-menu">



                                                            <a href="javascript:;"
                                                                onclick="edit_user('{{ $user->id }}','{{$user->name}}','{{ $user->status }}','{{ $user->email }}','{{ $user->phone }}','{{ $user->department }}','{{ $user->note }}',`{{$user->image}}`)"
                                                                class="dropdown-item">
                                                                <i class="fa fa-edit"></i>
                                                                </svg>
                                                                Edit Manager
                                                            </a>



                                                            <!-- Show divider only if there are actions above -->
                                                            @if (
                                                                ($user->status != 'blocked' && $user->status != 'active' && $user->email != 'N/A') ||
                                                                    $user->status == 'active' ||
                                                                    $user->status == 'blocked')
                                                                <div class="dropdown-divider"></div>
                                                            @endif

                                                            <button class="dropdown-item danger"
                                                                onclick="delete_user('{{ $user->id }}')">
                                                                <i class="fa fa-trash"></i>
                                                                Delete User
                                                            </button>

                                                            <!-- Show divider only if there are actions above -->
                                                            @if (
                                                                ($user->status != 'blocked' && $user->status != 'active' && $user->email != 'N/A') ||
                                                                    $user->status == 'active' ||
                                                                    $user->status == 'blocked')
                                                                <div class="dropdown-divider"></div>
                                                            @endif

                                                            <!-- Restrict User - Only show if user is active -->
                                                            @if ($user->status == 'active')
                                                                <button class="dropdown-item danger"
                                                                    onclick="userstatus('{{ $user->id }}','blocked')">
                                                                    <svg fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728" />
                                                                    </svg>
                                                                    Restrict User
                                                                </button>
                                                            @endif

                                                            <!-- Unrestrict User - Only show if user is blocked -->
                                                            @if ($user->status == 'blocked')
                                                                <button class="dropdown-item primary"
                                                                    onclick="userstatus('{{ $user->id }}','active')">
                                                                    <svg fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                    </svg>
                                                                    Unrestrict User
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>

                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination Section -->
                            <!-- Pagination Section -->
                            <div class="flex items-center justify-between px-6 py-4 border-t bg-[#F4F7FC]">
                                <div class="text-sm text-gray-600" id="paginationInfo">
                                    <!-- Info will be populated by JavaScript -->
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-600">Rows per page:</span>
                                    <select id="perPageSelect"
                                        class="border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                    </select>
                                    <div class="flex items-center gap-1 ml-4">
                                        <button id="prevBtn"
                                            class="p-2 text-gray-400 hover:text-gray-600 shadow bg-white rounded-md">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 19l-7-7 7-7" />
                                            </svg>
                                        </button>
                                        <span class="text-sm text-gray-600 px-2" id="pageInfo">
                                            <!-- Page info will be populated by JavaScript -->
                                        </span>
                                        <button id="nextBtn"
                                            class="p-2 text-gray-400 hover:text-gray-600 shadow bg-white rounded-md">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <!-- Confirmation Modal -->
                    <div id="confirmationModal"
                        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
                        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                            <div class="mt-3 text-center">
                                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L4.316 15.5c-.77.833.192 2.5 1.732 2.5z">
                                        </path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mt-2">Confirm Account Deletion</h3>
                                <div class="mt-2 px-7 py-3">
                                    <p class="text-sm text-gray-500">
                                        Are you absolutely sure you want to delete your account? This action cannot be
                                        undone.
                                    </p>
                                </div>
                                <div class="items-center px-4 py-3 space-x-3 flex justify-center">
                                    <button id="cancelDelete"
                                        class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                                        Cancel
                                    </button>
                                    <button id="confirmDelete"
                                        class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                        Delete Account
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Confirmation Modal -->
            <div id="confirmationModal"
                class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <div class="mt-3 text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L4.316 15.5c-.77.833.192 2.5 1.732 2.5z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mt-2">Confirm Account Deletion</h3>
                        <div class="mt-2 px-7 py-3">
                            <p class="text-sm text-gray-500">
                                Are you absolutely sure you want to delete your account? This action cannot be undone.
                            </p>
                        </div>
                        <div class="items-center px-4 py-3 space-x-3 flex justify-center">
                            <button id="cancelDelete"
                                class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                                Cancel
                            </button>
                            <button id="confirmDelete"
                                class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                Delete Account
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

            <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
            <script>
                function edit_user(id,name, status, email, phone, department, note,image) {
                    $("#editStatus").val(status)
                    $("#editEmail").val(email)
                    $("#editFullName").val(name)
$("#id").val(id)
                    $("#editPhone").val(status)
                    $("#editDepartment").val(department)
                    $("#editNotes").val(note) 
                    $("#editProfilePreview").attr("src",image)
                    // Show modal
                    const modal = $('#editManagerModal');
                    const backdrop = modal.find('.modal-backdrop');
                    const panel = modal.find('.modal-panel');

                    modal.removeClass('hidden');

                    setTimeout(() => {
                        backdrop.addClass('show');
                        panel.addClass('show');
                    }, 10);

                }
                let platformUsers = [];

                $(document).ready(function() {
                    // Setup CSRF token for AJAX requests
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });




                    // Search platform users
                    $('#searchPlatformUsers').on('input', function() {
                        const searchTerm = $(this).val().toLowerCase();
                        filterPlatformUsers(searchTerm);
                    });

                    // Show confirmation modal when delete button is clicked
                    $('#deleteAccountBtn').click(function() {
                        $('#confirmationModal').removeClass('hidden');
                    });

                    // Hide modal when cancel is clicked
                    $('#cancelDelete').click(function() {
                        $('#confirmationModal').addClass('hidden');
                    });

                    // Hide modal when clicking outside
                    $('#confirmationModal').click(function(e) {
                        if (e.target === this) {
                            $(this).addClass('hidden');
                        }
                    });

                    // Handle account deletion
                    $('#confirmDelete').click(function() {
                        var button = $(this);
                        var originalText = button.text();

                        // Show loading state
                        button.prop('disabled', true).text('Deleting...');

                        $.ajax({
                            url: '{{ route('user.delete_account') }}', // Adjust this URL to match your route
                            type: 'DELETE',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                // Hide modal
                                $('#confirmationModal').addClass('hidden');

                                // Show success message
                                toastr.success(
                                    'Your account has been successfully deleted. You will be redirected shortly.',
                                    'Account Deleted');

                                // Redirect after 3 seconds
                                setTimeout(function() {
                                    window.location.href =
                                        '/auth/login'; // Adjust redirect URL as needed
                                }, 3000);
                            },
                            error: function(xhr, status, error) {
                                // Hide modal
                                $('#confirmationModal').addClass('hidden');

                                // Reset button
                                button.prop('disabled', false).text(originalText);

                                // Show error message
                                var errorMessage =
                                    'An error occurred while deleting your account. Please try again.';

                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                                    errorMessage = xhr.responseJSON.error;
                                }

                                toastr.error(errorMessage, 'Deletion Failed');
                            }
                        });
                    });
                });

                function loadPlatformUsers() {
                    $.ajax({
                        url: '{{ route('user.loadusers') }}', // You'll need to create this route
                        type: 'GET',
                        success: function(response) {
                            platformUsers = response.users;
                            displayPlatformUsers(platformUsers);
                        },
                        error: function(xhr, status, error) {
                            console.error('Error loading platform users:', error);
                            toastr.error('Failed to load platform users');
                        }
                    });
                }

                function displayPlatformUsers(users) {
                    const usersList = $('#platformUsersList');
                    usersList.empty();

                    if (users.length === 0) {
                        usersList.html('<p class="text-gray-500 text-center py-4">No users found</p>');
                        return;
                    }



                    users.forEach(user => {

                        if (user.linked_status == "pending") {
                            var status = `<button disabled class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm invite-user-btn">
                                Pending
                            </button>`
                        } else if (user.linked_status == "active") {
                            var status = `<button disabled class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm invite-user-btn">
                                Invited
                            </button>`
                        } else {
                            var status = `<button  class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm invite-user-btn">
                                Send
                            </button>`
                        }
                        const userItem = $(`
                        <div class="flex justify-between items-center p-3 border-b hover:bg-gray-50 cursor-pointer user-item" 
                             data-name="${user.name}" data-email="${user.email}">
                            <div>
                                <div class="font-medium text-gray-900">${user.name}</div>
                                <div class="text-sm text-gray-500">${user.email}</div>
                                <br>
                                 <div class="text-sm text-gray-500 font-bold">${user.type}</div>
                            </div>
                            ${status}
                            
                        </div>
                    `);

                        userItem.find('.invite-user-btn').click(function(e) {
                            e.stopPropagation();
                            const name = $(this).closest('.user-item').data('name');
                            const email = $(this).closest('.user-item').data('email');
                            send_invite_from_popup(name, email);
                        });

                        usersList.append(userItem);
                    });
                }

                function filterPlatformUsers(searchTerm) {
                    const filteredUsers = platformUsers.filter(user =>
                        user.name.toLowerCase().includes(searchTerm) ||
                        user.email.toLowerCase().includes(searchTerm)
                    );
                    displayPlatformUsers(filteredUsers);
                }
            </script>
        </div>
    @endsection



    <script>

          // Bulk send invites function
        function bulkDeleteUsers_(users) {
            const button = document.getElementById('bulkDeleteUsers');
            const originalText = button.innerHTML;

            // Show loading state
            button.disabled = true;
            button.innerHTML = `
        <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Sending...
    `;

            $.ajax({
                url: '{{ route('user.delete_bulk_managers') }}', // You'll need to create this route
                type: 'POST',
                data: {
                    users: users,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    toastr.success(`managers deleted successfully!`);

                    // Clear selection and reload page or update UI
                    document.getElementById('clearSelection').click();
                    setTimeout(() => location.reload(), 1500);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    toastr.error(xhr.responseJSON?.message || 'Failed to send bulk invitations');
                },
                complete: function() {
                    // Reset button
                    button.disabled = false;
                    button.innerHTML = originalText;
                }
            });
        }

        document.addEventListener("DOMContentLoaded", function() {

            setTimeout(function() {
                try {

                    var table = $('#membersTable').DataTable({
                        responsive: true,
                        searching: true,
                        lengthChange: false,
                        info: false,
                        paging: true, // Enable DataTable pagination
                        pageLength: 10, // Default page size
                        pagingType: 'simple', // Use simple pagination
                        dom: 't', // Only show table (hide default pagination controls)
                        ordering: true,
                        columnDefs: [{
                            orderable: false,
                            targets: [0, 6]
                        }],
                        drawCallback: function(settings) {
                            updateCustomPagination(this.api());
                        }
                    });

                    // Connect custom search input to DataTable
                    document.querySelector('input[name="search"]').addEventListener('keyup', function() {
                        table.search(this.value).draw();
                    });


                } catch (error) {
                    console.error('DataTables initialization error:', error);
                }
            }, 100);
        });

        let dataTable;

        // Function to update custom pagination info
        function updateCustomPagination(api) {
            const info = api.page.info();

            // Update pagination info text
            document.getElementById('paginationInfo').textContent =
                `${info.start + 1}-${info.end} of ${info.recordsDisplay}`;

            // Update page info
            document.getElementById('pageInfo').textContent =
                `${info.page + 1}/${info.pages}`;

            // Update button states
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');

            if (info.page === 0) {
                prevBtn.disabled = true;
                prevBtn.classList.add('opacity-50');
            } else {
                prevBtn.disabled = false;
                prevBtn.classList.remove('opacity-50');
            }

            if (info.page >= info.pages - 1) {
                nextBtn.disabled = true;
                nextBtn.classList.add('opacity-50');
            } else {
                nextBtn.disabled = false;
                nextBtn.classList.remove('opacity-50');
            }
        }

        // Add event listeners after DataTable is initialized
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                dataTable = $('#membersTable').DataTable();

                // Previous page button
                document.getElementById('prevBtn').addEventListener('click', function() {
                    dataTable.page('previous').draw('page');
                });

                // Next page button
                document.getElementById('nextBtn').addEventListener('click', function() {
                    dataTable.page('next').draw('page');
                });

                // Per page select
                document.getElementById('perPageSelect').addEventListener('change', function() {
                    dataTable.page.len(parseInt(this.value)).draw();
                });

            }, 200);
        });

        function userstatus(id, status) {
            if (confirm("Are you sure?")) {
                $.ajax({
                    url: '{{ route('user.manager_setstatus') }}',
                    type: 'POST',
                    data: {
                        id: id,
                        status,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {

                        toastr.success(response.message);
                        location.reload()

                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        toastr.error(xhr.responseJSON?.message || 'Failed to send invitation');

                    }
                });
            }
        }

        // Checkbox and Bulk Actions Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const masterCheckbox = document.getElementById('masterCheckbox');
            const userCheckboxes = document.querySelectorAll('.user-checkbox');
            const bulkActionsBar = document.getElementById('bulkActionsBar');
            const selectedCountSpan = document.getElementById('selectedCount');
            const clearSelectionBtn = document.getElementById('clearSelection');
            const bulkDeleteUsers = document.getElementById('bulkDeleteUsers');
            const bulkBlockBtn = document.getElementById('bulkBlockBtn');

            let selectedUsers = [];

            // Master checkbox functionality
            masterCheckbox.addEventListener('change', function() {
                const isChecked = this.checked;
                userCheckboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                    const row = checkbox.closest('tr');
                    if (isChecked) {
                        row.classList.add('selected');
                    } else {
                        row.classList.remove('selected');
                    }
                });
                updateSelection();
            });

            // Individual checkbox functionality
            userCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const row = this.closest('tr');
                    if (this.checked) {
                        row.classList.add('selected');
                    } else {
                        row.classList.remove('selected');
                    }

                    // Update master checkbox state
                    const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
                    masterCheckbox.checked = checkedBoxes.length === userCheckboxes.length;
                    masterCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length <
                        userCheckboxes.length;

                    updateSelection();
                });
            });

            // Update selection and show/hide bulk actions
            function updateSelection() {
                const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
                selectedUsers = Array.from(checkedBoxes).map(checkbox => ({
                    id: checkbox.dataset.userId,
                    name: checkbox.dataset.userName,
                    email: checkbox.dataset.userEmail,
                    status: checkbox.dataset.userStatus
                }));

                selectedCountSpan.textContent = selectedUsers.length;

                if (selectedUsers.length > 0) {
                    bulkActionsBar.classList.remove('hidden');
                    bulkActionsBar.classList.add('show');
                } else {
                    bulkActionsBar.classList.add('hidden');
                    bulkActionsBar.classList.remove('show');
                }
            }

            // Clear selection
            clearSelectionBtn.addEventListener('click', function() {
                userCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                    checkbox.closest('tr').classList.remove('selected');
                });
                masterCheckbox.checked = false;
                masterCheckbox.indeterminate = false;
                updateSelection();
            });

            // Bulk invite functionality
            bulkDeleteUsers.addEventListener('click', function() {
                if (selectedUsers.length === 0) {
                    toastr.warning('Please select users to invite');
                    return;
                }

                // Filter users that can be invited (not blocked, not already active)
                const invitableUsers = selectedUsers

                if (confirm(`Send invitations to ${invitableUsers.length} user(s)?`)) {
                    bulkDeleteUsers_(invitableUsers);
                }
            });

            // Bulk block functionality
            bulkBlockBtn.addEventListener('click', function() {
                if (selectedUsers.length === 0) {
                    toastr.warning('Please select users to block');
                    return;
                }

                const blockableUsers = selectedUsers.filter(user => user.status !== 'blocked');

                if (blockableUsers.length === 0) {
                    toastr.warning('No users available to block');
                    return;
                }

                if (confirm(`Block ${blockableUsers.length} user(s)?`)) {
                    bulkBlockUsers(blockableUsers);
                }
            });
        });

      
        // Bulk block users function
        function bulkBlockUsers(users) {
            const button = document.getElementById('bulkBlockBtn');
            const originalText = button.innerHTML;

            // Show loading state
            button.disabled = true;
            button.innerHTML = `
        <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Blocking...
    `;

            $.ajax({
                url: '{{ route('user.bulk_block_managers') }}', // You'll need to create this route
                type: 'POST',
                data: {
                    users: users,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    toastr.success(`${users.length} user(s) blocked successfully!`);

                    // Clear selection and reload page or update UI
                    document.getElementById('clearSelection').click();
                    setTimeout(() => location.reload(), 1500);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    toastr.error(xhr.responseJSON?.message || 'Failed to block users');
                },
                complete: function() {
                    // Reset button
                    button.disabled = false;
                    button.innerHTML = originalText;
                }
            });
        }

        function delete_user(id) {
            if (confirm("Are you sure?")) {
                $("button").attr("disabled", true)
                $.ajax({
                    url: '{{ route('user.delete_manager') }}', // You'll need to create this route
                    type: 'POST',
                    data: {
                        id: id,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        toastr.success(response.message)
                        $("button").attr("disabled", false)
                        setTimeout(() => location.reload(), 1500);
                    },
                    error: function(xhr, status, error) {
                        toastr.success(xhr.responseJSON.message)
                        $("button").attr("disabled", false)
                    },
                    complete: function() {

                    }
                });
            }
        }
    </script>


    <script>
        // Fixed Vanilla JavaScript version
        document.addEventListener('DOMContentLoaded', function() {
            const dropdowns = document.querySelectorAll('.dropdown-container');

            dropdowns.forEach(dropdown => {
                const button = dropdown.querySelector('.dropdown-button');
                const menu = dropdown.querySelector('.dropdown-menu');

                button.addEventListener('click', function(e) {
                    e.stopPropagation();

                    const isCurrentlyOpen = menu.classList.contains('show');

                    // Close all other dropdowns first
                    dropdowns.forEach(other => {
                        if (other !== dropdown) {
                            other.querySelector('.dropdown-menu').classList.remove('show');
                        }
                    });

                    // Toggle current dropdown (opposite of its current state)
                    if (isCurrentlyOpen) {
                        menu.classList.remove('show');
                    } else {
                        menu.classList.add('show');
                    }
                });

                // Prevent dropdown from closing when clicking inside the menu
                menu.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            });

            // Close all dropdowns when clicking outside
            document.addEventListener('click', function(e) {
                // Check if click is outside all dropdowns
                const clickedInsideAnyDropdown = Array.from(dropdowns).some(dropdown =>
                    dropdown.contains(e.target)
                );

                if (!clickedInsideAnyDropdown) {
                    dropdowns.forEach(dropdown => {
                        dropdown.querySelector('.dropdown-menu').classList.remove('show');
                    });
                }
            });
        });




        // Function to open the modal
        function openAddManagerModal() {
            const modal = document.getElementById('addManagerModal');
            const panel = modal.querySelector('.modal-panel');
            const backdrop = modal.querySelector('.modal-backdrop');

            modal.classList.remove('hidden');

            // Trigger animation
            setTimeout(() => {
                panel.classList.add('show');
                backdrop.classList.add('show');
            }, 10);

            // Prevent body scroll
            document.body.style.overflow = 'hidden';
        }

        // Function to close the modal
        function closeAddManagerModal() {
            const modal = document.getElementById('addManagerModal');
            const panel = modal.querySelector('.modal-panel');
            const backdrop = modal.querySelector('.modal-backdrop');

            panel.classList.remove('show');
            backdrop.classList.remove('show');

            // Hide modal after animation
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }, 300);
        }

        setTimeout(() => {
            // Profile picture preview
            document.getElementById('profilePicture').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById('profilePreview');
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');

                        // Prepare FormData for AJAX upload
                        const formData = new FormData();
                        formData.append('profilePicture', file);
                        formData.append('_token', "{{ csrf_token() }}");
$("button").attr("disabled",true)
                        // Use jQuery AJAX to upload the file
                        $.ajax({
                            url: '{{ route('user.upload_file') }}', // Replace with your server upload URL
                            method: 'POST',
                            data: formData,
                            processData: false, // Important for file upload
                            contentType: false, // Important for file upload

                            success: function(response) {
                                preview.src = response.url
                                $("button").attr("disabled",false)
                                // console.log('Upload successful:', response);
                                // You can add more UI feedback here
                            },
                            error: function(xhr, status, error) {
                                     $("button").attr("disabled",false)
                                console.error('Upload failed:', error);
                                // Handle error UI here
                            }
                        });
                    };
                    reader.readAsDataURL(file);
                }
            });
        
        


              // Profile picture preview
            document.getElementById('editProfilePicture').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById('editProfilePreview');
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');

                        // Prepare FormData for AJAX upload
                        const formData = new FormData();
                        formData.append('profilePicture', file);
                        formData.append('_token', "{{ csrf_token() }}");
     $("button").attr("disabled",true)
                        // Use jQuery AJAX to upload the file
                        $.ajax({
                            url: '{{ route('user.upload_file') }}', // Replace with your server upload URL
                            method: 'POST',
                            data: formData,
                            processData: false, // Important for file upload
                            contentType: false, // Important for file upload

                            success: function(response) {
                                preview.src = response.url
                                     $("button").attr("disabled",false)
                                // console.log('Upload successful:', response);
                                // You can add more UI feedback here
                            },
                            error: function(xhr, status, error) {
                                console.error('Upload failed:', error);
                                     $("button").attr("disabled",false)
                                // Handle error UI here
                            }
                        });
                    };
                    reader.readAsDataURL(file);
                }
            });
        
        
        
        
        }, 500)


        document.addEventListener('DOMContentLoaded', function() {


$(document).on('submit', '#editManagerForm', function(e) {
    e.preventDefault();
    
    // Get form data
    const formData = new FormData(this);
    
    // Add profile image if available
    const profileSrc = $('#editProfilePreview').attr('src');
    if (profileSrc && profileSrc !== '') {
        formData.append("image", profileSrc);
    }
    
    // Disable buttons
    $("button").attr("disabled", true);
    
    $.ajax({
        url: '{{ route("user.edit_manager") }}', // Replace with your edit manager route
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            toastr.success(response.message);
            closeEditManagerModal();
            location.reload();
            $("button").attr("disabled", false);
        },
        error: function(xhr, status, error) {
            toastr.error(xhr.responseJSON.message);
            console.error('Update failed:', error);
            $("button").attr("disabled", false);
        }
    });
});
                    // Close modal when clicking outside
        document.getElementById('addManagerModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddManagerModal();
            }
        });

            // Form submission
            $("#addManagerForm").submit((e) => {
                e.preventDefault();

                // Get form data
                const formData = new FormData($("#addManagerForm")[0]);
                formData.append("image", $("#profilePreview").attr("src"))
                $("button").attr("disabled", true)

                $.ajax({
                    url: '{{ route('user.add_manager') }}', // Replace with your server upload URL
                    method: 'POST',
                    data: formData,
                    processData: false, // Important for file upload
                    contentType: false, // Important for file upload

                    success: function(response) {
                        toastr.success(response.message)
                        location.reload()
                        // console.log('Upload successful:', response);
                        // You can add more UI feedback here
                        $("button").attr("disabled", false)
                    },
                    error: function(xhr, status, error) {
                        toastr.error(xhr.responseJSON.message)
                        console.error('Upload failed:', error);
                        $("button").attr("disabled", false)
                        // Handle error UI here
                    }
                });

            });
        });



        // Escape key to close modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !document.getElementById('addManagerModal').classList.contains('hidden')) {
                closeAddManagerModal();
            }
        });



        function closeEditManagerModal() {
    const modal = $('#editManagerModal');
    const backdrop = modal.find('.modal-backdrop');
    const panel = modal.find('.modal-panel');
    
    backdrop.removeClass('show');
    panel.removeClass('show');
    
    setTimeout(() => {
        modal.addClass('hidden');
        // Reset form
        $('#editManagerForm')[0].reset();
        $('#editProfilePreview').addClass('hidden').attr('src', '');
        $('#editProfilePlaceholder').removeClass('hidden');
    }, 300);
}


    </script>
