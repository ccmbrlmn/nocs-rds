<x-app-layout>
<div 
    class="page-wrapper flex flex-col h-screen bg-gray-100 dark:bg-gray-900"
    x-data="{
        openEdit: false,
        openAssetForm: false,
        openBlockedModal: false,
        selectedAsset: null,
        darkMode: false,

        openEditAsset(asset) {
            this.selectedAsset = asset;
            this.openEdit = true;
        }
    }"
>

    <!-- HEADER -->
    <div class="header-container rounded-2xl mb-3 mx-3 mt-3">
        <div class="p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">

            <div>
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">
                    Asset Inventory
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-300">
                    Manage all system assets
                </p>
            </div>

            @include('layouts.header')
        </div>
    </div>

    <!-- FILTER + ACTION BAR -->
    <div class="px-6 mb-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">

            <!-- FILTER -->
            <div>
                @include('layouts.filter', [
                    'routeName' => 'admin.assets',

                    'statuses' => [
                        'All' => null,
                        'Available' => 'Available',
                        'In Use' => 'In Use',
                        'Maintenance' => 'Maintenance'
                    ],

                    'dateFilters' => [
                        null => 'All Time',
                        'newest' => 'Newest',
                        'oldest' => 'Oldest',
                        '7_days' => '7 Days',
                        '30_days' => '30 Days'
                    ],

                    'exportPdf' => 'admin.assets.pdf',
                    'exportCsv' => 'admin.assets.csv'
                ])
            </div>

            <!-- ADD BUTTON -->
            <div class="flex justify-end">
                <button
                    @click="openAssetForm = true"
                    class="px-4 py-2 rounded-lg shadow transition
                           bg-indigo-600 text-white
                           hover:bg-indigo-700
                           dark:bg-indigo-500 dark:hover:bg-indigo-600">
                    + Add Asset
                </button>
            </div>

        </div>
    </div>

    <!-- TABLE -->
    <div class="px-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-200 dark:border-gray-700">

            <div class="overflow-x-auto max-h-[650px] overflow-y-auto">

                <table class="w-full text-sm text-left text-gray-700 dark:text-gray-200">

                    <thead class="sticky top-0 bg-gray-50 dark:bg-gray-700 text-xs uppercase text-gray-700 dark:text-gray-200">
                        <tr>
                            <th class="px-5 py-3">Asset Name</th>
                            <th class="px-5 py-3">Tag</th>
                            <th class="px-5 py-3">Serial</th>
                            <th class="px-5 py-3">Model</th>
                            <th class="px-5 py-3">Category</th>
                            <th class="px-5 py-3 text-center">Status</th>
                            <th class="px-5 py-3 text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">

                        @forelse($assets as $asset)
                        <tr 
                            class="hover:bg-gray-50 dark:hover:bg-gray-700 transition cursor-pointer"
                            onclick="window.location='{{ route('admin.assets.show', $asset->id) }}'"
                        >

                            <td class="px-5 py-3 font-medium">{{ $asset->asset_name }}</td>
                            <td class="px-5 py-3">{{ $asset->asset_tag ?? '-' }}</td>
                            <td class="px-5 py-3">{{ $asset->asset_serial ?? '-' }}</td>
                            <td class="px-5 py-3">{{ $asset->asset_model ?? '-' }}</td>
                            <td class="px-5 py-3">{{ $asset->asset_category ?? '-' }}</td>

                            <!-- STATUS -->
                            <td class="px-5 py-3 text-center">
                                @php $status = $asset->asset_status; @endphp

                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $status === 'Available'
                                        ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300'
                                        : ($status === 'In Use'
                                            ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300'
                                            : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200') }}">
                                    {{ $status }}
                                </span>
                            </td>

                            <!-- ACTION -->
                            <td class="px-5 py-3 text-center" onclick="event.stopPropagation();">
                                <button
                                    @click="openEditAsset(@js($asset))"
                                    :class="{
                                        'bg-gray-400 cursor-not-allowed': {{ $asset->asset_status === 'In Use' ? 'true' : 'false' }},
                                        'bg-blue-500 hover:bg-blue-600': {{ $asset->asset_status !== 'In Use' ? 'true' : 'false' }}
                                    }"
                                    class="text-white px-3 py-1 rounded-lg text-xs">
                                    Edit
                                </button>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-gray-500 dark:text-gray-400">
                                No assets found.
                            </td>
                        </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div x-show="openEdit" x-cloak
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 w-full max-w-2xl text-gray-800 dark:text-gray-200">

            <div class="flex justify-between mb-4">
                <h2 class="text-lg font-semibold">Edit Asset</h2>
                <button @click="openEdit = false">✕</button>
            </div>

            <form :action="`/admin/assets/${selectedAsset?.id}`" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-4">

                    <input type="text" name="asset_name"
                        x-model="selectedAsset.asset_name"
                        class="rounded border p-2 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-600">

                    <input type="text" name="asset_tag"
                        x-model="selectedAsset.asset_tag"
                        class="rounded border p-2 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-600">

                    <input type="text" name="asset_serial"
                        x-model="selectedAsset.asset_serial"
                        class="rounded border p-2 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-600">

                    <input type="text" name="asset_model"
                        x-model="selectedAsset.asset_model"
                        class="rounded border p-2 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-600">

                    <input type="text" name="asset_category"
                        x-model="selectedAsset.asset_category"
                        class="rounded border p-2 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-600">

                    <select name="asset_status"
                        x-model="selectedAsset.asset_status"
                        :disabled="selectedAsset?.asset_status === 'In Use'"
                        class="rounded border p-2 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-600">

                        <option value="Available">Available</option>
                        <option value="In Use">In Use</option>
                        <option value="Maintenance">Maintenance</option>
                    </select>

                </div>

                <button class="mt-4 w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                    Save Changes
                </button>
            </form>

        </div>
    </div>

    <!-- ADD MODAL -->
    <div x-show="openAssetForm" x-cloak
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 w-full max-w-2xl text-gray-800 dark:text-gray-200">

            <div class="flex justify-between mb-4">
                <h2 class="text-lg font-semibold text-indigo-600 dark:text-blue-400">Add Asset</h2>
                <button @click="openAssetForm = false">✕</button>
            </div>

            <form action="{{ route('assets.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-2 gap-4">

                    <input type="text" name="asset_name" placeholder="Asset Name" class="input-dark">
                    <input type="text" name="asset_tag" placeholder="Asset Tag" class="input-dark">
                    <input type="text" name="asset_serial" placeholder="Serial" class="input-dark">
                    <input type="text" name="asset_model" placeholder="Model" class="input-dark">
                    <input type="text" name="asset_category" placeholder="Category" class="input-dark">

                    <select name="asset_status" class="input-dark">
                        <option value="Available">Available</option>
                        <option value="In Use">In Use</option>
                        <option value="Maintenance">Maintenance</option>
                    </select>

                </div>

                <button class="mt-4 w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                    Save Asset
                </button>

            </form>

        </div>
    </div>

</div>
</x-app-layout>
