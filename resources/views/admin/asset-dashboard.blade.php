<div 
    x-data="Object.assign(assetDashboard(), {
        openEdit: false,
        selectedAsset: null,
        openEditAsset(asset) {
            this.selectedAsset = asset;
            this.openEdit = true;
        }
    })"

x-init="
    darkMode = document.documentElement.classList.contains('dark');
    initChart();

    new MutationObserver(() => {
        darkMode = document.documentElement.classList.contains('dark');
        renderChart();
    }).observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });
"
    class="p-6"
>




<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-200">
            Asset Dashboard
        </h2>
        <p class="text-sm text-gray-500 dark:text-gray-300">
            Visual insights of asset inventory
        </p>
    </div>

<div class="flex items-center gap-2">

    <!-- FILTER BUTTON -->
    <div class="relative">
        <button 
            @click="openFilter = !openFilter"
            class="border px-4 py-2 rounded-lg shadow transition
                   bg-white dark:bg-gray-800
                   text-gray-800 dark:text-gray-200
                   border-gray-300 dark:border-gray-600
                   hover:bg-gray-50 dark:hover:bg-gray-700"
            :class="selectedCategory 
                ? 'bg-indigo-600 text-white border-indigo-600 dark:bg-indigo-600 dark:border-indigo-600' 
                : ''">
            Filter
        </button>

        <!-- DROPDOWN -->
        <div x-show="openFilter"
             x-transition
             @click.outside="openFilter = false"
             x-cloak
             class="absolute right-0 mt-2 w-72
                    bg-white dark:bg-gray-800
                    border border-gray-200 dark:border-gray-700
                    rounded-2xl shadow-xl p-3 z-50">

            <!-- SEARCH -->
            <input type="text"
                   x-model="searchCategory"
                   placeholder="Search category..."
                   class="w-full mb-3 px-3 py-2 border rounded-lg text-sm
                          bg-white dark:bg-gray-700
                          text-gray-800 dark:text-gray-200
                          border-gray-300 dark:border-gray-600
                          focus:ring-2 focus:ring-indigo-500 focus:outline-none">

            <!-- ALL OPTION -->
            <button
                @click="setCategory(null); openFilter=false"
                class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition
                       text-gray-700 dark:text-gray-200
                       hover:bg-gray-100 dark:hover:bg-gray-700"
                :class="selectedCategory === null 
                    ? 'bg-indigo-500 text-white' 
                    : ''">
                All Categories
            </button>

            <!-- DIVIDER -->
            <div class="my-2 border-t border-gray-200 dark:border-gray-700"></div>

            <!-- CATEGORY LIST -->
            <div class="max-h-52 overflow-y-auto space-y-1 pr-1 scroll-smooth">
                <template x-for="cat in categories.filter(c => c.toLowerCase().includes(searchCategory.toLowerCase()))" :key="cat">
                    <button
                        @click="setCategory(cat); openFilter=false"
                        class="w-full text-left px-3 py-2 rounded-lg text-sm transition
                               text-gray-700 dark:text-gray-200
                               hover:bg-gray-100 dark:hover:bg-gray-700"
                        :class="selectedCategory === cat 
                            ? 'bg-indigo-500 text-white' 
                            : ''"
                        x-text="cat">
                    </button>
                </template>
            </div>

        </div>
    </div>

    <!-- ADD BUTTON -->
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

<!-- OVERVIEW -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

    <!-- TOTAL -->
    <div class="p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
        <p class="text-xs text-gray-500 dark:text-gray-300">Total (Filtered)</p>
        <p class="text-2xl font-semibold text-gray-900 dark:text-white" x-text="filteredAssets.length"></p>
    </div>

    <!-- AVAILABLE -->
    <div class="p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
        <p class="text-xs text-gray-500 dark:text-gray-300">Available</p>
        <p class="text-2xl font-semibold text-green-600 dark:text-green-400"
           x-text="countStatus('Available')"></p>
    </div>

    <!-- IN USE -->
    <div class="p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
        <p class="text-xs text-gray-500 dark:text-gray-300">In Use</p>
        <p class="text-2xl font-semibold text-yellow-600 dark:text-yellow-400"
           x-text="countStatus('In Use')"></p>
    </div>

    <!-- MAINTENANCE -->
    <div class="p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
        <p class="text-xs text-gray-500 dark:text-gray-300">Maintenance</p>
        <p class="text-2xl font-semibold text-red-600 dark:text-red-400"
           x-text="countStatus('Maintenance')"></p>
    </div>

</div>

    <!-- MAIN -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

<!-- CHART -->
<div class="lg:col-span-1 bg-white dark:bg-gray-800 p-4 rounded-2xl border dark:border-gray-700 shadow">
    <h3 class="text-sm font-semibold mb-3 text-gray-800 dark:text-gray-200">
        Asset Status by Category
    </h3>
    <div class="h-80">
        <canvas id="categoryStatusChart"></canvas>
    </div>
</div>

<!-- ASSET TABLE -->
<div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl border dark:border-gray-700 shadow">

    <div class="p-4 border-b dark:border-gray-700 font-semibold text-gray-800 dark:text-gray-200">
        Assets Table
    </div>

    <div class="overflow-auto max-h-[500px]">
        <table class="w-full text-sm">

            <thead class="bg-gray-100 dark:bg-gray-700 sticky top-0">
                <tr class="text-gray-700 dark:text-gray-200">
                    <th class="p-3 text-left">Asset Name</th>
                    <th class="p-3 text-left">Tag</th>
                    <th class="p-3 text-left">Serial</th>
                    <th class="p-3 text-left">Model</th>
                    <th class="p-3 text-left">Category</th>
                    <th class="p-3 text-left">Status</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="asset in filteredAssets" :key="asset.id">
                    <tr 
                        class="border-t dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer"
                        @click="window.location = `/admin/assets/${asset.id}`"
                    >

                        <td class="p-3 text-gray-700 dark:text-gray-200" x-text="asset.asset_name"></td>

                        <td class="p-3 text-gray-600 dark:text-gray-300" x-text="asset.asset_tag || '-'"></td>

                        <td class="p-3 text-gray-600 dark:text-gray-300" x-text="asset.asset_serial || '-'"></td>

                        <td class="p-3 text-gray-600 dark:text-gray-300" x-text="asset.asset_model || '-'"></td>

                        <td class="p-3 text-gray-600 dark:text-gray-300" x-text="asset.asset_category"></td>

                        <td class="p-3">
                            <span 
                                class="px-2 py-1 rounded-full text-xs font-medium"
                                :class="{
                                    'bg-green-100 text-green-700': (asset.computed_status ?? asset.asset_status) === 'Available',
                                    'bg-yellow-100 text-yellow-700': (asset.computed_status ?? asset.asset_status) === 'In Use',
                                    'bg-gray-200 text-gray-700 dark:bg-gray-600 dark:text-gray-200': (asset.computed_status ?? asset.asset_status) === 'Maintenance'
                                }"
                                x-text="asset.computed_status ?? asset.asset_status">
                            </span>
                        </td>

                    </tr>
                </template>
            </tbody>

        </table>
    </div>

</div>

</div>

    <!-- EDIT MODAL -->
    <div x-show="openEdit" x-cloak
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 w-full max-w-2xl">

            <div class="flex justify-between mb-4">
                <h2 class="text-lg font-semibold">Edit Asset</h2>
                <button @click="openEdit = false">✕</button>
            </div>

            <form :action="`/admin/assets/${selectedAsset?.id}`" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-4">

                    <input type="text" name="asset_name" x-model="selectedAsset.asset_name" class="rounded border p-2">
                    <input type="text" name="asset_tag" x-model="selectedAsset.asset_tag" class="rounded border p-2">
                    <input type="text" name="asset_serial" x-model="selectedAsset.asset_serial" class="rounded border p-2">
                    <input type="text" name="asset_model" x-model="selectedAsset.asset_model" class="rounded border p-2">
                    <input type="text" name="asset_category" x-model="selectedAsset.asset_category" class="rounded border p-2">

                    <select name="asset_status" x-model="selectedAsset.asset_status" class="rounded border p-2">
                        <option value="Available">Available</option>
                        <option value="In Use">In Use</option>
                        <option value="Maintenance">Maintenance</option>
                    </select>

                </div>

                <button class="mt-4 w-full bg-indigo-600 text-white py-2 rounded-lg">
                    Save Changes
                </button>
            </form>

        </div>
    </div>

</div>

<!-- SCRIPT -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
function assetDashboard() {
    return {
        assets: @json($assets ?? []),
        categories: @json($categories ?? []),

        selectedCategory: null,
        chart: null,

        setCategory(cat) {
            this.selectedCategory = cat;
            this.renderChart();
        },
        
        openFilter: false,
        searchCategory: '',

        get filteredAssets() {
            return this.selectedCategory
                ? this.assets.filter(a => a.asset_category === this.selectedCategory)
                : this.assets;
        },

        countStatus(status) {
            return this.filteredAssets.filter(a =>
                (a.computed_status ?? a.asset_status) === status
            ).length;
        },

        buildChartData() {
            let data = this.filteredAssets;
            let cats = [...new Set(data.map(a => a.asset_category))].filter(Boolean);

            return {
                labels: cats,
                available: cats.map(c =>
                    data.filter(a => a.asset_category === c && (a.computed_status ?? a.asset_status) === 'Available').length
                ),
                inuse: cats.map(c =>
                    data.filter(a => a.asset_category === c && (a.computed_status ?? a.asset_status) === 'In Use').length
                ),
                maintenance: cats.map(c =>
                    data.filter(a => a.asset_category === c && (a.computed_status ?? a.asset_status) === 'Maintenance').length
                ),
            };
        },

        initChart() {
            this.renderChart();
        },

        renderChart() {
            const ctx = document.getElementById('categoryStatusChart');
            if (!ctx) return;

            let d = this.buildChartData();

            if (this.chart) {
                this.chart.destroy();
            }

const isDark = document.documentElement.classList.contains('dark');

const textColor = isDark ? '#f9fafb' : '#374151';
const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
const tooltipBg = isDark ? '#1f2937' : '#ffffff';
const borderColor = isDark ? '#374151' : '#e5e7eb';


this.chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: d.labels,
        datasets: [
            { label: 'Available', data: d.available, backgroundColor: '#22c55e' },
            { label: 'In Use', data: d.inuse, backgroundColor: '#facc15' },
            { label: 'Maintenance', data: d.maintenance, backgroundColor: '#94a3b8' }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,

        plugins: {
            legend: {
                labels: {
                    color: textColor
                }
            },
            tooltip: {
                backgroundColor: tooltipBg,
                titleColor: textColor,
                bodyColor: textColor,
                borderColor: borderColor,
                borderWidth: 1
            }
        },

scales: {
    x: {
        stacked: true,
        ticks: {
            color: textColor,
            font: {
                weight: '500'
            }
        },
        grid: {
            color: gridColor
        }
    },
    y: {
        stacked: true,
        ticks: {
            color: textColor,
            font: {
                weight: '500'
            }
        },
        grid: {
            color: gridColor
        }
    }
}


    }
});
        }
    }
}
</script>
