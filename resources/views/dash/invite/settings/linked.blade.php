@include('dash.invite.layouts.partials.head')
<div class="p-5">

    <div class="border rounded-md">

        <div class="p-4 flex border-b justify-between">
            <div class="flex flex-col">

                <h2 class="text-[25px] font-bold">Integrations</h2>
                <span class="font-light text-lg">Manage Integrations in review BOD</span>

            </div>

            
        </div>

        <div id="personalInfoForm" class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach (DB::table('linked')->where('userid', $id)->get() as $linked)
                    @if(DB::table("platform_users")->where(["email"=>Auth("linear_user")->user()->email,"source"=>$linked->type])->exists())
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
 
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>

    </div>

</div>