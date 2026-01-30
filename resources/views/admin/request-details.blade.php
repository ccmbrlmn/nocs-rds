<x-app-layout>
    <div class="header-container flex items-center gap-5 text-white font-medium p-2 mt-8 mb-3">
        <div class="header">
            <h1 class="flex items-center gap-2 text-3xl">
                <span class="material-symbols-outlined text-2xl">
                    description
                </span> 
                Request Application
            </h1>
        </div>
    </div>
    <div class="request-details p-5 rounded-lg bg-white">
    {{-- Notifications --}}
        @if(session('success'))
            <div 
                x-data="{ show: true }"
                x-show="show"
                x-transition
                class="mb-4 p-3 bg-green-100 text-green-700 rounded flex items-center justify-between"
            >
                <span>{{ session('success') }}</span>

                <button 
                    @click="show = false"
                    class="text-green-700 font-bold ml-4 hover:text-green-900"
                >
                    ✕
                </button>
            </div>
        @endif

        @if(session('error'))
            <div 
                x-data="{ show: true }"
                x-show="show"
                x-transition
                class="mb-4 p-3 bg-red-100 text-red-700 rounded flex items-center justify-between"
            >
                <span>{{ session('error') }}</span>

                <button 
                    @click="show = false"
                    class="text-red-700 font-bold ml-4 hover:text-red-900"
                >
                    ✕
                </button>
            </div>
        @endif


        <div class="request-header flex items-center justify-between w-full mb-3">
            <div class="mt-4">
                <h2 class="text-3xl font-semibold mb-2">{{ $request->user->name}}</h2> 

                @php
                    $status = $request->status;
                    $colors = config('status')[$status];
                @endphp

                <span class="px-4 py-1 rounded-full text-sm font-semibold
                {{ $colors['text'] }} {{ $colors['bg'] }}">
                    {{ $status }}
                </span>

            </div>
            
        <div class="flex gap-4 items-center">
            @auth
                @if($request->status === 'Open')
                    {{-- Cancel Button (only show if status is Open) --}}
                    @if($request->status === 'Open')
                        <div x-data="{ openCancelForm: false }">
                            <x-primary-button type="button" @click="openCancelForm = true" style="background-color: #D7070B; color: white; width: 100px; height: 40px;">
                                {{ __('Cancel') }}
                            </x-primary-button>
                            @include('form.cancel-form')
                        </div>
                    @endif

                    {{-- Edit Button --}}
                    <div x-data="{ openEdit: false }">
                        <x-primary-button @click="openEdit = true" style="background-color:#0575E6; color:white; width:100px; height:40px;">
                            {{ __('Edit') }}
                        </x-primary-button>
                        @include('form.edit-request-form', ['request' => $request])
                    </div>
                @endif
            @endauth
        </div>



            


            
        </div>
       
        <hr class="mb-4">

        <div class="request-information flex justify-between pt-3">
            <div class="left-col-info">
                <div class="flex items-center gap-3 mb-6">
                    <span class="material-symbols-outlined text-gray-600">event</span>
                    <div>
                        <p class="header-text font-semibold mb-1">Name of Event:</p>
                        <p class="detail-text">{{ $request->event_name }}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 mb-6">
                    <span class="material-symbols-outlined text-gray-600">event_available</span>
                    <div>
                        <p class="header-text font-semibold mb-1">Purpose of the Event:</p>
                        <p class="detail-text">{{ $request->purpose }}</p>
                    </div>
                </div>
                
            <div class="flex items-start gap-3 mb-6">
                <span class="material-symbols-outlined text-gray-600">inventory_2</span>
                <div>
                    <p class="header-text font-semibold mb-1">Requested Items:</p>
                    <ul class="detail-text list-disc list-inside">
                        @if(!empty($request->items))
                            @foreach(json_decode($request->items, true) as $item)
                                <li>{{ $item['name'] }} — Quantity: {{ $item['quantity'] }}</li>
                            @endforeach
                        @else
                            <li>No items requested</li>
                        @endif
                    </ul>
                </div>
            </div>

            </div>

            <div class="right-col-info">
                <div class="flex items-center gap-3 mb-6">
                    <span class="material-symbols-outlined text-gray-600">location_on</span>
                    <div>
                        <p class="header-text font-semibold mb-1">Location:</p>
                        <p class="detail-text">{{ $request->location }}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 mb-6">
                    <span class="material-symbols-outlined text-gray-600">calendar_clock</span>
                    <div>
                        <p class="header-text font-semibold mb-1">Date of Event:</p>
                        <p class="detail-text">{{ \Carbon\Carbon::parse($request->start_date)->format('M d') }} - {{ \Carbon\Carbon::parse($request->created_at)->format('d, Y') }}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 mb-6">
                    <span class="material-symbols-outlined text-gray-600">calendar_clock</span>
                    <div>
                        <p class="header-text font-semibold mb-1">Request Set-up Date:</p>
                        <p class="detail-text">{{ $request->setup_date }} | {{ $request->setup_time }}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 mb-6">
                    <span class="material-symbols-outlined text-gray-600">group</span>
                    <div>
                        <p class="header-text font-semibold mb-1">No. of Users:</p>
                        <p class="detail-text">{{ $request->users }} users</p>
                    </div>
                </div>
            </div>
        </div>

        <hr class="mb-4 mt-4">

        <p class="header-text font-semibold mb-1">Deployment Information</p>

        <div class="request-information flex justify-between pt-3">
            @php
                $userIsNocs = auth()->check() && auth()->user()->email === 'nocs_services@gbox.adnu.edu.ph';
            @endphp

            {{-- If status is In Progress --}}
            @if($request->status === 'In Progress')
                @if($userIsNocs || !$userIsNocs)
                    <div class="left-col-info">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="material-symbols-outlined text-gray-600">manage_accounts</span>
                            <div>
                                <p class="header-text font-semibold mb-1">Name of Personnel:</p>
                                <p class="detail-text">{{ $request->personnel_name }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="right-col-info">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="material-symbols-outlined text-gray-600">home_repair_service</span>
                            <div>
                                <p class="header-text font-semibold mb-1">Additional Equipments:</p>
                                <p class="detail-text">{{ $request->other_equipments }}</p>
                            </div>
                        </div>
                    </div>
                @endif

            {{-- If status is Cancelled --}}
            @elseif($request->status === 'Declined' && $userIsNocs)
                <div class="left-col-info w-full">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="material-symbols-outlined text-gray-600">cancel</span>
                        <div>
                            <p class="header-text font-semibold mb-1">Cancellation Reason:</p>
                            <p class="detail-text">{{ $request->cancel_reason }}</p>
                        </div>
                    </div>
                </div>

            {{-- If status is Declined --}}
            @elseif($request->status === 'Declined' && !$userIsNocs)
                <div class="left-col-info w-full">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="material-symbols-outlined text-gray-600">block</span>
                        <div>
                            <p class="header-text font-semibold mb-1">Decline Reason:</p>
                            <p class="detail-text">{{ $request->decline_reason }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

    </div>
</x-app-layout>

<style>
    .material-symbols-outlined {
        font-size: 28px;
        vertical-align: middle;
    }

    .request-details {
        margin-left: 4.5rem;
        margin-right: 5rem;
        padding: 2rem;
        max-height: calc(100vh - 160px);
        overflow-y: auto;
    }

    h1 {
        font-size: 1.7rem;
    }

    .header-container {
        margin-left: 5rem;
        margin-right: 5rem;
    }

    .request-information {
        margin-top: 10px;
    }

    .left-col-info,
    .right-col-info {
        text-align: left;
        width: 48%;
        min-width: 250px;
    }

    .request-information {
        display: flex;
        flex-wrap: wrap;
        gap: 2%;
    }


    .header-text{
        font-size: 1.5rem;
    }

    .detail-text{
        font-size: 1.5rem;
        color:#404040;
    }
    
    [x-cloak] { display: none !important; }

</style>

