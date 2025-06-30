<!-- views/dash/members.blade.php -->
@extends('dash.invite.layouts.app')

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
                    <li><a href="/invited/settings"
                            class="p-2 rounded-md hover:bg-gray-100 flex gap-2 items-center text-[17px]"><i
                                class=""><svg width="16" height="18" viewBox="0 0 16 18" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M0.608656 16.3074C0.502269 16.7077 0.740548 17.1185 1.14087 17.2248C1.54118 17.3312 1.95195 17.0929 2.05834 16.6926L0.608656 16.3074ZM13.942 16.6926C14.0484 17.0929 14.4591 17.3312 14.8595 17.2248C15.2598 17.1185 15.4981 16.7077 15.3917 16.3074L13.942 16.6926ZM1.3335 16.5L2.05834 16.6926C2.73354 14.1519 5.12642 12.25 8.00016 12.25V11.5V10.75C4.45901 10.75 1.46175 13.0973 0.608656 16.3074L1.3335 16.5ZM8.00016 11.5V12.25C10.8739 12.25 13.2668 14.1519 13.942 16.6926L14.6668 16.5L15.3917 16.3074C14.5386 13.0973 11.5413 10.75 8.00016 10.75V11.5ZM11.3335 4.83333H10.5835C10.5835 6.26007 9.4269 7.41667 8.00016 7.41667V8.16667V8.91667C10.2553 8.91667 12.0835 7.0885 12.0835 4.83333H11.3335ZM8.00016 8.16667V7.41667C6.57343 7.41667 5.41683 6.26007 5.41683 4.83333H4.66683H3.91683C3.91683 7.0885 5.745 8.91667 8.00016 8.91667V8.16667ZM4.66683 4.83333H5.41683C5.41683 3.4066 6.57343 2.25 8.00016 2.25V1.5V0.75C5.745 0.75 3.91683 2.57817 3.91683 4.83333H4.66683ZM8.00016 1.5V2.25C9.4269 2.25 10.5835 3.4066 10.5835 4.83333H11.3335H12.0835C12.0835 2.57817 10.2553 0.75 8.00016 0.75V1.5Z"
                                        fill="#4B5563" />
                                </svg>
                            </i> Personal Information</a></li>
                     
                    <li><a href="/invited/settings?type=security"
                            class=" p-2 rounded-md hover:bg-gray-100 flex gap-2 items-center text-[17px]"><i
                                class=""><svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M5.83333 9.16602V5.83268C5.83333 4.72761 6.27232 3.66781 7.05372 2.8864C7.83512 2.105 8.89493 1.66602 10 1.66602C11.1051 1.66602 12.1649 2.105 12.9463 2.8864C13.7277 3.66781 14.1667 4.72761 14.1667 5.83268V9.16602M4.16667 9.16602H15.8333C16.7538 9.16602 17.5 9.91221 17.5 10.8327V16.666C17.5 17.5865 16.7538 18.3327 15.8333 18.3327H4.16667C3.24619 18.3327 2.5 17.5865 2.5 16.666V10.8327C2.5 9.91221 3.24619 9.16602 4.16667 9.16602Z"
                                        stroke="#4B5563" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </i>Security</a></li>
                    <li><a href="/invited/settings?type=apps"
                            class=" p-2 rounded-md hover:bg-gray-100 flex gap-2 items-center text-[17px]"><i class="">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M1.6665 17.5L18.3332 17.5M2.49984 3.66667C2.49984 3.35725 2.61822 3.0605 2.82893 2.84171C3.03965 2.62292 3.32544 2.5 3.62343 2.5H16.3762C16.6742 2.5 16.96 2.62292 17.1707 2.84171C17.3815 3.0605 17.4998 3.35725 17.4998 3.66667V13C17.4998 13.3094 17.3815 13.6062 17.1707 13.825C16.96 14.0438 16.6742 14.1667 16.3762 14.1667H3.62343C3.32544 14.1667 3.03965 14.0438 2.82893 13.825C2.61822 13.6062 2.49984 13.3094 2.49984 13V3.66667Z"
                                        stroke="#4B5563" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>

                            </i>Integrations</a></li>
                 
                

                </ul>
            </div>

            <div class="w-full">
                @if (request()->type == '')
                    @include('dash.invite.settings.personal')
              
                @elseif(request()->type == 'security')
                    @include('dash.invite.settings.security')
                   @elseif(request()->type == 'apps')
                    @include('dash.invite.settings.linked')
                      
                @endif
            </div>

        </div>
    </div>
@endsection
 