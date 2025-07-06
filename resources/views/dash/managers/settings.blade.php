<!-- views/dash.managers/members.blade.php -->
@extends('dash.managers.layouts.app')

@section('page-title', 'Settings')

@section('content')
    <style>
        body {
              font-family: "Plus Jakarta Sans", sans-serif !important;
        }
    </style>
    <div class="    bg-white h-screen">
        <div class="flex">
            <div class="w-1/4    border-r h-screen">
                <div class="p-5 border-b border-gray-200 mb-4">
                    <h2 class="text-[30px] font-semibold mb-4">Settings</h2>
                </div>

                <ul class="space-y-2 p-5">
                    <li><a href="/manager/settings"
                            class="p-2 rounded-md hover:bg-gray-100 flex gap-2 items-center text-[17px]"><i
                                class=""><svg width="16" height="18" viewBox="0 0 16 18" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M0.608656 16.3074C0.502269 16.7077 0.740548 17.1185 1.14087 17.2248C1.54118 17.3312 1.95195 17.0929 2.05834 16.6926L0.608656 16.3074ZM13.942 16.6926C14.0484 17.0929 14.4591 17.3312 14.8595 17.2248C15.2598 17.1185 15.4981 16.7077 15.3917 16.3074L13.942 16.6926ZM1.3335 16.5L2.05834 16.6926C2.73354 14.1519 5.12642 12.25 8.00016 12.25V11.5V10.75C4.45901 10.75 1.46175 13.0973 0.608656 16.3074L1.3335 16.5ZM8.00016 11.5V12.25C10.8739 12.25 13.2668 14.1519 13.942 16.6926L14.6668 16.5L15.3917 16.3074C14.5386 13.0973 11.5413 10.75 8.00016 10.75V11.5ZM11.3335 4.83333H10.5835C10.5835 6.26007 9.4269 7.41667 8.00016 7.41667V8.16667V8.91667C10.2553 8.91667 12.0835 7.0885 12.0835 4.83333H11.3335ZM8.00016 8.16667V7.41667C6.57343 7.41667 5.41683 6.26007 5.41683 4.83333H4.66683H3.91683C3.91683 7.0885 5.745 8.91667 8.00016 8.91667V8.16667ZM4.66683 4.83333H5.41683C5.41683 3.4066 6.57343 2.25 8.00016 2.25V1.5V0.75C5.745 0.75 3.91683 2.57817 3.91683 4.83333H4.66683ZM8.00016 1.5V2.25C9.4269 2.25 10.5835 3.4066 10.5835 4.83333H11.3335H12.0835C12.0835 2.57817 10.2553 0.75 8.00016 0.75V1.5Z"
                                        fill="#4B5563" />
                                </svg>
                            </i> Personal Information</a></li>
              
                    <li><a href="/manager/settings?type=security"
                            class=" p-2 rounded-md hover:bg-gray-100 flex gap-2 items-center text-[17px]"><i
                                class=""><svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M5.83333 9.16602V5.83268C5.83333 4.72761 6.27232 3.66781 7.05372 2.8864C7.83512 2.105 8.89493 1.66602 10 1.66602C11.1051 1.66602 12.1649 2.105 12.9463 2.8864C13.7277 3.66781 14.1667 4.72761 14.1667 5.83268V9.16602M4.16667 9.16602H15.8333C16.7538 9.16602 17.5 9.91221 17.5 10.8327V16.666C17.5 17.5865 16.7538 18.3327 15.8333 18.3327H4.16667C3.24619 18.3327 2.5 17.5865 2.5 16.666V10.8327C2.5 9.91221 3.24619 9.16602 4.16667 9.16602Z"
                                        stroke="#4B5563" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </i>Security</a></li>
                
                    <li><a href="/manager/settings?type=notification"
                            class=" p-2 rounded-md hover:bg-gray-100 flex gap-2 items-center text-[17px]"><i
                                class=""><svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M2.5 14.2493C2.08579 14.2493 1.75 14.5851 1.75 14.9993C1.75 15.4136 2.08579 15.7493 2.5 15.7493V14.2493ZM17.5 15.7493C17.9142 15.7493 18.25 15.4136 18.25 14.9993C18.25 14.5851 17.9142 14.2493 17.5 14.2493V15.7493ZM8.33333 17.5827C7.91912 17.5827 7.58333 17.9185 7.58333 18.3327C7.58333 18.7469 7.91912 19.0827 8.33333 19.0827V17.5827ZM11.6667 19.0827C12.0809 19.0827 12.4167 18.7469 12.4167 18.3327C12.4167 17.9185 12.0809 17.5827 11.6667 17.5827V19.0827ZM2.5 14.9993V15.7493H17.5V14.9993V14.2493H2.5V14.9993ZM8.33333 18.3327V19.0827H11.6667V18.3327V17.5827H8.33333V18.3327ZM4.16667 7.49935H4.91667C4.91667 4.6919 7.19255 2.41602 10 2.41602V1.66602V0.916016C6.36413 0.916016 3.41667 3.86347 3.41667 7.49935H4.16667ZM10 1.66602V2.41602C12.8074 2.41602 15.0833 4.6919 15.0833 7.49935H15.8333H16.5833C16.5833 3.86347 13.6359 0.916016 10 0.916016V1.66602ZM4.16667 7.49935H3.41667V14.9993H4.16667H4.91667V7.49935H4.16667ZM15.8333 7.49935H15.0833V14.9993H15.8333H16.5833V7.49935H15.8333Z"
                                        fill="#4B5563" />
                                </svg>
                            </i> Notifications</a></li>
                 
                    {{-- <li><a href="{{ route('user.settings',['type'=>'delete']) }}"
                            class=" p-2 rounded-md hover:bg-gray-100 flex gap-2 items-center text-[17px]"><i
                                class=""><svg width="18" height="20" viewBox="0 0 18 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M1.5 4.99935H3.16667M3.16667 4.99935H16.5M3.16667 4.99935V16.666C3.16667 17.108 3.34226 17.532 3.65482 17.8445C3.96738 18.1571 4.39131 18.3327 4.83333 18.3327H13.1667C13.6087 18.3327 14.0326 18.1571 14.3452 17.8445C14.6577 17.532 14.8333 17.108 14.8333 16.666V4.99935M5.66667 4.99935V3.33268C5.66667 2.89065 5.84226 2.46673 6.15482 2.15417C6.46738 1.84161 6.89131 1.66602 7.33333 1.66602H10.6667C11.1087 1.66602 11.5326 1.84161 11.8452 2.15417C12.1577 2.46673 12.3333 2.89065 12.3333 3.33268V4.99935"
                                        stroke="#4B5563" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </i> Delete Account</a></li> --}}

                </ul>
            </div>

            <div class="w-full">
                @if (request()->type == '')
                    @include('dash.managers.settings.personal')
                
                @elseif(request()->type == 'security')
                    @include('dash.managers.settings.security')
                
                       @elseif(request()->type == 'notification')
                    @include('dash.managers.settings.notification')
                  
                    
                @endif
            </div>

        </div>
    </div>
@endsection

<script>

     function delete_linked(id) {
        if (confirm("Are you sure you want to delete this linked app?")) {
            $.post('{{ route('customers.delete_linked') }}', {
                id: id,
                _token: '{{ csrf_token() }}'
            }, function(response) {
     
                    toastr.success("Linked app deleted successfully.");
                     $("#linked_" + id).remove();
                
            }).fail(function(xhr) {
                toastr.error(xhr.responseJSON.message);
            });
        }
    }

    </script>
