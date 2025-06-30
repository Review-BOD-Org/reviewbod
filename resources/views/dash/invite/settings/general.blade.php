<div class="container mx-auto flex justify-center items-center mt-5">
    <div class="flex flex-col gap-6 w-full max-w-md mt-5">
        <!-- Header Section -->
        <div class="flex flex-col gap-2 justify-center items-center text-center">
            <div class="bg-[#1E3A8A] rounded-full w-16 h-16 flex items-center justify-center">
                <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="64" height="64" rx="32" fill="#1E3A8A" />
                    <path
                        d="M41 27.52V33.4C41 33.74 40.67 33.98 40.35 33.88L36.42 32.66C35.34 32.33 34.18 32.61 33.39 33.4C32.59 34.2 32.3 35.37 32.64 36.45L33.85 40.35C33.95 40.67 33.71 41 33.37 41H27.52C24.07 41 22 38.94 22 35.48V27.52C22 24.06 24.07 22 27.52 22H35.48C38.93 22 41 24.06 41 27.52Z"
                        fill="white" />
                    <path
                        d="M41.96 38.84L40.33 39.39C39.88 39.54 39.52 39.89 39.37 40.35L38.82 41.98C38.35 43.39 36.37 43.36 35.93 41.95L34.08 36C33.72 34.82 34.81 33.72 35.98 34.09L41.94 35.94C43.34 36.38 43.36 38.37 41.96 38.84Z"
                        fill="white" />
                </svg>

            </div>
            <h2 class="text-2xl font-medium text-gray-800">Connected Apps</h2>
            <p class="text-gray-500">Select from our list of available apps to connect</p>
        </div>

        <!-- App Selection Section -->
        @php
        $linkedValues = DB::table('linked')
            ->where('userid', auth()->user()->id)
            ->pluck('type')
            ->toArray();
    
        $hasLinear = !in_array('linear', $linkedValues);
        $hasSlack = !in_array('slack', $linkedValues);
        $hasTrello = !in_array('trello', $linkedValues);
    @endphp
    
    @if($hasLinear || $hasSlack || $hasTrello)
        <div class="flex gap-3">
            <div class="form-group w-full">
                <label class="text-gray-600 mb-1 block">Select App</label>
                <div class="relative">
                    <select
                    id="stype"
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none">
                        @if($hasLinear)
                            <option value="linear">Linear</option>
                        @endif
    
                        @if($hasSlack)
                            <option value="slack">Slack</option>
                        @endif

                        @if($hasTrello)
                        <option value="trello">Trello</option>
                    @endif
                    </select>
    
                    <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>
            <button type="button" onclick="window.location.href=`/${$('#stype').val()}/auth`" class="bg-[#1E3A8A] text-white px-6 py-3 rounded-lg hover:bg-blue-700 self-end">
                Proceed
            </button>
        </div>
    @endif
    

        <!-- Connected Apps List -->
        <div class="flex flex-col gap-3">

            @foreach (DB::table('linked')->where('userid', auth()->user()->id)->get() as $linked)
                <!-- Linear App -->
                <div class="border rounded-lg p-4 flex justify-between items-center" id="linked_{{ $linked->id }}">
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-2">
                            <h4 class="font-medium">{{ $linked->type }}</h4>
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <circle cx="8" cy="8" r="7" fill="white" stroke="#39B588" />
                                <path d="M5 8L7 10L11 6" stroke="#39B588" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <span>Connected</span>
                            <span class="mx-2">•</span>
                            <span>{{ date('Y - M - D', strtotime($linked->created_at)) }}</span>
                        </div>
                    </div>
                    <button class="text-red-500" onclick="delete_linked({{ $linked->id }})">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            @endforeach


        </div>
    </div>
</div>
