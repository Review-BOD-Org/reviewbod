@include('dash.layouts.partials.head')
<div class="p-5">

    <div class="border rounded-md">

        <div class="p-4 flex border-b justify-between">
            <div class="flex flex-col">

                <h2 class="text-[25px] font-bold">Integrations</h2>
                <span class="font-light text-lg">Manage Integrations in review BOD</span>

            </div>

            <button onclick="location.href='/auth/choose?type=save'"
                class="btn border h-[50px] rounded-md text-[#1E3A8A] w-[200px] font-medium">Add
                Integration</button>
        </div>

        <div id="personalInfoForm" class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach (DB::table('linked')->where('userid', auth()->user()->id)->get() as $linked)
                    <div class="border p-4 rounded-lg">
                        <div class="flex justify-between">
                            <div><img src="@if($linked->type == 'jira') /{{ $linked->type }}.svg @else /images/{{ $linked->type }}.webp @endif" width="100"></div>
                            <div
                                class="p-1 w-[150px] self-center rounded-full bg-[#BBF7D0] border-[#22C55E] border items-center flex justify-center text-[#22C55E]">
                                Connected
                            </div>
                        </div>

                        <div class="flex justify-between mt-5">
                            <div class="flex flex-col">
                                <h3 class="text-black text-[25px] font-bold">{{ $linked->type }}</h3>
                                <span class="text-[#8B8B8B] text-lg">Date:
                                    {{ \Carbon\Carbon::parse($linked->created_at)->format('d M Y, h:i A') }}
                                </span>
                            </div>

                            <button class="btn" onclick="delete_linked({{ $linked->id }})"><i
                                    class="fa fa-trash-o text-red-400 text-[30px]"></i></button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>