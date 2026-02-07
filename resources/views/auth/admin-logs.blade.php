<x-app-layout>
    {{-- Header --}}
    <div class="flex items-center justify-between mt-8 mb-4 px-4">
        <h1 class="flex items-center gap-2 text-2xl font-semibold text-gray-800">
            <span class="material-symbols-outlined text-2xl">history</span>
            {{ $admin->name }}'s Logs
        </h1>

        <a href="{{ route('admin.created-admins') }}"
           class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">
            Back
        </a>
    </div>

    {{-- Logs Table --}}
    <div class="bg-white rounded-lg shadow mx-4 overflow-hidden">

        {{-- Table Header --}}
        <div class="bg-blue-100 px-4 py-3">
            <div class="flex text-sm font-semibold text-gray-700">
                <div class="w-1/6 text-center">Request No.</div>
                <div class="w-2/6 text-center">Event</div>
                <div class="w-1/6 text-center">Action</div>
                <div class="w-2/6 text-center">Date</div>
            </div>
        </div>

        {{-- Table Body --}}
        <div class="divide-y divide-gray-200">
            @forelse($logs as $log)
                <div class="px-4 py-3 hover:bg-blue-50 transition">
                    <div class="flex items-center text-sm text-gray-600">
                        <div class="w-1/6 text-center font-medium">#{{ $log->id }}</div>

                        <div class="w-2/6 text-center">
                            {{ $log->event_name ?? '-' }}
                        </div>

                        <div class="w-1/6 text-center font-medium">
                            @if($log->handled_by === $admin->id)
                                @if($log->status === 'Active')
                                    <span class="text-green-600">Accepted</span>
                                @elseif($log->status === 'Declined')
                                    <span class="text-red-600">Declined</span>
                                @else
                                    -
                                @endif
                            @else
                                -
                            @endif
                        </div>

                        <div class="w-2/6 text-center">
                            {{ \Carbon\Carbon::parse($log->updated_at)->format('M d, Y H:i') }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-6 text-center text-gray-500">
                    No logs available.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>

