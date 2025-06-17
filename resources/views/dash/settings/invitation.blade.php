<h2 class="text-lg font-medium mb-4">Invitations Content</h2>

<div class="">
    <table class="w-full">
        <thead class="bg-gray-50 text-left">
            <tr>
                <th class="py-4 px-4 text-sm font-medium text-gray-500">Name</th>
                <th class="py-4 px-4 text-sm font-medium text-gray-500">Email</th>
                <th class="py-4 px-4 text-sm font-medium text-gray-500">Date Invited</th>

                <th class="py-4 px-4 text-sm font-medium text-gray-500">Status</th>
                <th class="py-4 px-4 text-sm font-medium text-gray-500">Action</th>
                <th class="py-4 px-4"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach ($users as $user)
                <tr id="invitation_{{ $user->id }}">
                    <td class="py-4 px-4 text-sm text-gray-700">
                       {{$user->name}}
                    </td>
                    <td class="py-4 px-4 text-sm text-gray-700">
                        {{$user->email}}
                    </td>
                    <td class="py-4 px-4 text-sm text-gray-700">
                        {{$user->created_at}}
                    </td>
                    <td class="py-4 px-4 text-sm text-gray-700">
                       @if($user->verified == 1)
                            <span class="text-green-500">Verified</span>
                        @else
                            <span class="text-red-500">Not Verified</span>
                        @endif
                    </td>
                  
                    <td class="py-4 px-4 text-left">
                        <button class="text-red-500 text-center hover:text-red-700" onclick="delete_invitation({{ $user->id }})">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    function delete_invitation(id) {
        if (confirm('Are you sure you want to delete this invitation?')) {
            $.ajax({
                url:  '{{ route('customers.delete_invitation') }}',
                type: 'POST',
                data: {
                    id: id,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    toastr.success('Invitation deleted successfully');
                    $('#invitation_' + id).remove();
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON.message || 'An error occurred while deleting the invitation.');
                }
            });
        }
    }
</script>