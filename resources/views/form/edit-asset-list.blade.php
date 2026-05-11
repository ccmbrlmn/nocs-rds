<div class="p-6">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-200">
                Asset Inventory
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-300">
                Manage all system assets
            </p>
        </div>

        <button 
            @click="openAssetForm = true"
            class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700 transition dark:text-gray-200">
            + Add Asset
        </button>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-100 dark:border-gray-700 overflow-hidden">

        <table class="w-full text-sm text-left border-collapse">
            
            <!-- HEADER -->
            <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-200 text-xs uppercase tracking-wide">
                <tr>
                    <th class="px-5 py-3">Asset Name</th>
                    <th class="px-5 py-3">Tag</th>
                    <th class="px-5 py-3">Serial</th>
                    <th class="px-5 py-3">Model</th>
                    <th class="px-5 py-3">Category</th>
                    <th class="px-5 py-3 text-center">Status</th>
                </tr>
            </thead>

            <!-- BODY -->
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($assets as $asset)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">

                        <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-200 whitespace-nowrap">
                            {{ $asset->asset_name }}
                        </td>

                        <td class="px-5 py-3 text-gray-600 dark:text-gray-300">
                            {{ $asset->asset_tag ?? '-' }}
                        </td>

                        <td class="px-5 py-3 text-gray-600 dark:text-gray-300">
                            {{ $asset->asset_serial ?? '-' }}
                        </td>

                        <td class="px-5 py-3 text-gray-600 dark:text-gray-300">
                            {{ $asset->asset_model ?? '-' }}
                        </td>

                        <td class="px-5 py-3 text-gray-600 dark:text-gray-300">
                            {{ $asset->asset_category ?? '-' }}
                        </td>

                        <td class="px-5 py-3 text-center">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold 
                                {{ $asset->asset_status === 'Available' 
                                    ? 'bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300' 
                                    : ($asset->asset_status === 'In Use' 
                                        ? 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-300'
                                        : 'bg-gray-200 text-gray-700 dark:bg-gray-600 dark:text-gray-200') }}">
                                {{ $asset->asset_status }}
                            </span>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-12 text-gray-500 dark:text-gray-300">
                            No assets found.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    setTimeout(() => {

        const statusLabels = @json($statusData->keys());
        const statusValues = @json($statusData->values());

        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusValues,
                    backgroundColor: ['#22c55e', '#facc15', '#94a3b8']
                }]
            }
        });

        const categoryLabels = @json($categoryData->keys());
        const categoryValues = @json($categoryData->values());

        new Chart(document.getElementById('categoryChart'), {
            type: 'bar',
            data: {
                labels: categoryLabels,
                datasets: [{
                    label: 'Assets',
                    data: categoryValues,
                    backgroundColor: '#6366f1'
                }]
            }
        });

    }, 300);
</script>
