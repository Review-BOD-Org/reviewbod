<!-- views/dash/members.blade.php -->
@extends('dash.layouts.app')

@section('page-title', 'Members')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <form action="{{ route('user.members') }}" method="GET" class="relative w-80">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-md block w-full pl-10 p-2.5"
                        placeholder="Search here...">
                </form>

                {{-- <a href="{{route('user.new')}}" id="add-member" class="flex items-center gap-2 bg-indigo-700 hover:bg-indigo-800 text-white py-2 px-4 rounded-md transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Member
            </a> --}}
            </div>

            <div class="">
                <table class="w-full">
                    <thead class="bg-gray-50 text-left">
                        <tr>
                            <th class="py-4 px-4 text-sm font-medium text-gray-500">Name</th>
                            <th class="py-4 px-4 text-sm font-medium text-gray-500">Email</th>
                            <th class="py-4 px-4 text-sm font-medium text-gray-500">Work Title</th>
                            <th class="py-4 px-4 text-sm font-medium text-gray-500">Role</th>
                            <th class="py-4 px-4 text-sm font-medium text-gray-500">Status</th>
                            <th class="py-4 px-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($members as $index => $member)
                            <tr>
                                <td class="py-4 px-4 text-sm text-gray-700">
                                    @if (isset($member['displayName']))
                                        {{ $member['displayName'] }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-sm text-gray-700">
                                    {{ $member['email'] ?? 'N/A' }}
                                </td>
                                <td class="py-4 px-4 text-sm text-gray-700">
                                    @if (isset($member['teams']['nodes'][0]['name']))
                                        {{ $member['teams']['nodes'][0]['name'] }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-sm text-gray-700">
                                    {{ $index === 1 ? 'Admin' : 'Member' }}
                                </td>
                                <td class="py-4 px-4">
                                    @if (request()->status == 'blocked')
                                        <span class="px-3 py-1 text-sm rounded-full bg-red-100 text-red-600">Blocked</span>
                                    @elseif(request()->status == 'pending')
                                        <span
                                            class="px-3 py-1 text-sm rounded-full bg-yellow-100 text-yellow-600">Pending</span>
                                    @else
                                        <span
                                            class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-600">Active</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-left">
                                    <div class="relative inline-block" x-data="{ open: false }">
                                        <button @click="open = !open" @click.away="open = false"
                                            class="text-gray-400 hover:text-gray-600 border rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-gray-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path
                                                    d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                            </svg>
                                        </button>

                                        <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            class="absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded-md shadow-lg z-10">
                                            <a href="{{ route('user.member', ['id' => $member['id']]) }}"
                                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">View</a>
                                            @if (request()->status != 'blocked')
                                                @if ($member['email'] != "N/A")
                                                    <a href="#"
                                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                                        onclick="send_invite('{{ $member['id'] }}','{{ $member['displayName'] }}','{{ $member['email'] }}')">Send
                                                        Invitation</a>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex items-center justify-between mt-6 px-4">
                <div class="text-sm text-gray-500">
                    {{ $totalMembers }} members
                </div>

                <div class="flex items-center space-x-1">
                    @if ($members->onFirstPage())
                        <span
                            class="flex items-center justify-center w-8 h-8 text-gray-300 border border-gray-200 rounded-md cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                                </path>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $members->previousPageUrl() }}"
                            class="flex items-center justify-center w-8 h-8 text-gray-500 border border-gray-200 rounded-md hover:bg-gray-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                                </path>
                            </svg>
                        </a>
                    @endif

                    @php
                        $start = max($members->currentPage() - 2, 1);
                        $end = min($start + 4, $members->lastPage());
                        $start = max(min($members->lastPage() - 4, $start), 1);
                    @endphp

                    @for ($i = $start; $i <= $end; $i++)
                        <a href="{{ $members->url($i) }}"
                            class="flex items-center justify-center w-8 h-8 {{ $members->currentPage() == $i ? 'text-white bg-indigo-700' : 'text-gray-500 border border-gray-200 hover:bg-gray-100' }} rounded-md">
                            {{ $i }}
                        </a>
                    @endfor

                    @if ($end < $members->lastPage())
                        <span class="flex items-center justify-center w-8 h-8 text-gray-500">...</span>
                        <a href="{{ $members->url($members->lastPage()) }}"
                            class="flex items-center justify-center w-8 h-8 text-gray-500 border border-gray-200 rounded-md hover:bg-gray-100">
                            {{ $members->lastPage() }}
                        </a>
                    @endif

                    @if ($members->hasMorePages())
                        <a href="{{ $members->nextPageUrl() }}"
                            class="flex items-center justify-center w-8 h-8 text-gray-500 border border-gray-200 rounded-md hover:bg-gray-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </a>
                    @else
                        <span
                            class="flex items-center justify-center w-8 h-8 text-gray-300 border border-gray-200 rounded-md cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </span>
                    @endif
                </div>
            </div>

        </div>
    @endsection

    <script>
        // document.addEventListener('DOMContentLoaded', function() {
        //     // Search functionality
        //     const searchInput = document.getElementById('search-members');
        //     searchInput.addEventListener('keyup', function() {
        //         const filter = searchInput.value.toUpperCase();
        //         const table = document.querySelector('table');
        //         const tr = table.getElementsByTagName('tr');

        //         for (let i = 1; i < tr.length; i++) {
        //             let txtValue = '';
        //             const tdName = tr[i].getElementsByTagName("td")[0];
        //             const tdEmail = tr[i].getElementsByTagName("td")[1];

        //             if (tdName && tdEmail) {
        //                 txtValue = tdName.textContent || tdName.innerText;
        //                 txtValue += tdEmail.textContent || tdEmail.innerText;

        //                 if (txtValue.toUpperCase().indexOf(filter) > -1) {
        //                     tr[i].style.display = "";
        //                 } else {
        //                     tr[i].style.display = "none";
        //                 }
        //             }
        //         }
        //     });
        // });
        function send_invite(id, name, email) {
            // Send the invite using AJAX
            $.ajax({
                url: '{{ route('customers.send_invite') }}',
                type: 'POST',
                data: {
                    id: id,
                    name: name,
                    email: email,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {

                    toastr.success('Invitation sent successfully!');

                },
                error: function(xhr, status, error) {
                    console.error(error);
                    toastr.error(xhr.responseJSON?.message || 'Failed to send invitation');

                }
            });
        }
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-submit search form when typing
            const searchInput = document.querySelector('input[name="search"]');
            let debounceTimer;

            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    this.form.submit();
                }, 500);
            });
        });
    </script>
