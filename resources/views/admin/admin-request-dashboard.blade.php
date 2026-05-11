<div
    x-data="requestDashboard()"
    x-init="initChart()"
    class="p-6"
>

<!-- HEADER -->
<div class="flex justify-between items-center mb-6">

    <!-- TITLE -->
    <div>
        <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-200">
            Request Dashboard
        </h2>
        <p class="text-sm text-gray-500 dark:text-gray-300">
            Visual insights of user requests
        </p>
    </div>

    <div class="flex items-center gap-2">

        <!-- FILTER -->
        <div class="relative" x-data="{ openFilter: false, search: '' }">

            <button
                @click="openFilter = !openFilter"
                class="border px-4 py-2 rounded-xl text-sm font-medium transition
                       bg-white dark:bg-gray-800
                       text-gray-800 dark:text-gray-200
                       border-gray-300 dark:border-gray-600
                       hover:bg-gray-50 dark:hover:bg-gray-700"
                :class="selectedStatus
                    ? 'bg-indigo-600 text-white border-indigo-600 dark:bg-indigo-600 dark:border-indigo-600'
                    : ''">

                Filter
            </button>

            <!-- DROPDOWN -->
            <div
                x-show="openFilter"
                x-transition
                @click.outside="openFilter = false"
                x-cloak
                class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-xl p-3 z-50">

                <input
                    type="text"
                    x-model="search"
                    placeholder="Search status..."
                    class="w-full mb-3 px-3 py-2 border rounded-lg text-sm
                           bg-white dark:bg-gray-700
                           text-gray-800 dark:text-gray-200
                           border-gray-300 dark:border-gray-600">

                <button
                    @click="setStatus(null); openFilter=false"
                    class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition
                           text-gray-700 dark:text-gray-200
                           hover:bg-gray-100 dark:hover:bg-gray-700"
                    :class="selectedStatus === null ? 'bg-indigo-600 text-white' : ''">
                    All Status
                </button>

                <div class="my-2 border-t border-gray-200 dark:border-gray-700"></div>

                <div class="space-y-1 max-h-52 overflow-y-auto pr-1">

                    <template x-for="status in statuses.filter(s => s.toLowerCase().includes(search.toLowerCase()))" :key="status">
                        <button
                            @click="setStatus(status); openFilter=false"
                            class="w-full text-left px-3 py-2 rounded-lg text-sm transition
                                   text-gray-700 dark:text-gray-200
                                   hover:bg-gray-100 dark:hover:bg-gray-700"
                            :class="selectedStatus === status ? 'bg-indigo-600 text-white' : ''"
                            x-text="status">
                        </button>
                    </template>

                </div>

            </div>
        </div>

        <!-- VIEW REQUEST BUTTON -->
        <button
            @click="window.location.href='/admin/requests'"
            class="px-4 py-2 rounded-xl text-sm font-medium transition
                   bg-indigo-600 text-white
                   hover:bg-indigo-700
                   dark:bg-indigo-500 dark:hover:bg-indigo-600">
            View Requests
        </button>

    </div>

</div>

<div x-data="{ showMore: false }">

    <!-- OVERVIEW -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

        <!-- TOTAL -->
        <div class="p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
            <p class="text-xs text-gray-500 dark:text-gray-300">Total</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white"
               x-text="filteredRequests.length"></p>
        </div>

        <!-- OPEN -->
        <div class="p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
            <p class="text-xs text-gray-500 dark:text-gray-300">Open</p>
            <p class="text-2xl font-semibold text-blue-600 dark:text-blue-400"
               x-text="countStatus('Open')"></p>
        </div>

        <!-- ACTIVE -->
        <div class="p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
            <p class="text-xs text-gray-500 dark:text-gray-300">Active</p>
            <p class="text-2xl font-semibold text-yellow-600 dark:text-yellow-400"
               x-text="countStatus('Active')"></p>
        </div>

        <!-- CLOSED -->
        <div class="p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
            <p class="text-xs text-gray-500 dark:text-gray-300">Closed</p>
            <p class="text-2xl font-semibold text-green-600 dark:text-green-400"
               x-text="countStatus('Closed')"></p>
        </div>

    </div>

    <!-- TOGGLE -->
    <button
        @click="showMore = !showMore"
        class="text-sm mb-4 text-indigo-600 dark:text-indigo-400 hover:underline transition">
        <span x-text="showMore ? 'Hide details' : 'Show more statuses'"></span>
    </button>

    <!-- SECONDARY OVERVIEW -->
    <div x-show="showMore" x-transition
         class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

        <!-- DECLINED -->
        <div class="p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
            <p class="text-xs text-gray-500 dark:text-gray-300">Declined</p>
            <p class="text-2xl font-semibold text-red-600 dark:text-red-400"
               x-text="countStatus('Declined')"></p>
        </div>

        <!-- PENDING RETURN -->
        <div class="p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
            <p class="text-xs text-gray-500 dark:text-gray-300">Pending Return</p>
            <p class="text-2xl font-semibold text-purple-600 dark:text-purple-400"
               x-text="countStatus('Pending Return')"></p>
        </div>

        <!-- PENDING RETRIEVAL -->
        <div class="p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
            <p class="text-xs text-gray-500 dark:text-gray-300">Pending Retrieval</p>
            <p class="text-2xl font-semibold text-orange-600 dark:text-orange-400"
               x-text="countStatus('Pending Retrieval')"></p>
        </div>

    </div>

</div>

    <!-- MAIN -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

<div class="lg:col-span-1 bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-200 dark:border-gray-700">

    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">
        Request Status Distribution (by Requester)
    </h3>

    <!-- CHART -->
    <div class="h-80">
        <canvas id="requestStatusChart"></canvas>
    </div>

    <div class="flex flex-wrap gap-2 mt-3 text-xs">

        <template x-for="ds in chart.data.datasets" :key="ds.label">
            <span class="px-2 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">

                <span class="inline-block w-2 h-2 rounded-full mr-1"
                      :style="`background:${ds.backgroundColor}`"></span>

                <span x-text="ds.label"></span>

            </span>
        </template>

    </div>

</div>

<!-- TABLE -->
<div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700">

    <div class="p-4 border-b border-gray-200 dark:border-gray-700 font-semibold text-gray-700 dark:text-gray-200">
        Requests
    </div>

    <div class="overflow-auto max-h-[500px]">

        <table class="w-full text-sm">

            <!-- HEADER -->
            <thead class="bg-gray-100 dark:bg-gray-800 sticky top-0">
                <tr class="text-gray-700 dark:text-gray-300">
                    <th class="p-4 text-left">Requester</th>
                    <th class="p-3 text-left">Event</th>
                    <th class="p-3 text-left">Date</th>
                    <th class="p-3 text-left">Status</th>
                </tr>
            </thead>

            <!-- BODY -->
            <tbody class="text-gray-700 dark:text-gray-200">

            <template x-for="req in filteredRequests" :key="req.id">
                <tr
                    class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer"
                    @click="window.location = `/admin/requests/${req.id}`"
                >

                    <td class="p-3" x-text="req.user?.name || '—'"></td>

                    <td class="p-3" x-text="req.event_name || '-'"></td>

                    <td class="p-3"
                        x-text="new Date(req.created_at).toLocaleDateString()">
                    </td>

                    <td class="p-3">
                        <span
                            class="px-2 py-1 rounded-full text-xs font-medium"
                            :class="{
                                'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300': req.computed_status === 'Open',
                                'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300': req.computed_status === 'Active',
                                'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300': req.computed_status === 'Closed',
                                'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300': req.computed_status === 'Declined',
                                'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300': req.computed_status === 'Pending Return',
                                'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300': req.computed_status === 'Pending Retrieval'
                            }"
                            x-text="req.computed_status">
                        </span>
                    </td>

                </tr>
            </template>

            </tbody>

        </table>

    </div>

</div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
function requestDashboard() {
    return {
        requests: @json($requests),

        statuses: [
            'Open',
            'Active',
            'Closed',
            'Declined',
            'Pending Return',
            'Pending Retrieval'
        ],
        selectedStatus: null,
        chart: null,

        setStatus(status) {
            this.selectedStatus = status;
            this.renderChart();
        },

        get filteredRequests() {
            return this.selectedStatus
                ? this.requests.filter(r => r.computed_status === this.selectedStatus)
                : this.requests;
        },

        countStatus(status) {
            return this.filteredRequests.filter(r => r.computed_status === status).length;
        },


buildChartData() {
    let statuses = this.statuses;

    let requesters = [...new Set(
        this.filteredRequests.map(r => r.user?.name ?? 'Unknown')
    )];

    requesters.sort((a, b) => {
        return this.filteredRequests.filter(r => (r.user?.name ?? 'Unknown') === b).length
             - this.filteredRequests.filter(r => (r.user?.name ?? 'Unknown') === a).length;
    });

    const TOP_N = 5;
    const topRequesters = requesters.slice(0, TOP_N);
    const others = requesters.slice(TOP_N);

    let datasets = topRequesters.map(name => {
        return {
            label: name,
            data: statuses.map(status => {
                return this.filteredRequests.filter(r =>
                    (r.user?.name ?? 'Unknown') === name &&
                    r.computed_status === status
                ).length;
            }),
            backgroundColor: this.getColor(name)
        };
    });

    if (others.length > 0) {
        datasets.push({
            label: `Others (${others.length})`,
            data: statuses.map(status => {
                return this.filteredRequests.filter(r =>
                    others.includes(r.user?.name ?? 'Unknown') &&
                    r.computed_status === status
                ).length;
            }),
            backgroundColor: '#94a3b8'
        });
    }

    return {
        labels: statuses,
        datasets: datasets
    };
},

        getColor(name) {
            let colors = [
                '#6366f1', '#22c55e', '#f59e0b',
                '#ef4444', '#06b6d4', '#a855f7',
                '#10b981', '#f97316'
            ];

            return colors[name.length % colors.length];
        },

        /* ===================== */
        /* INIT */
        /* ===================== */
        initChart() {
            this.renderChart();
        },

        /* ===================== */
        /* RENDER */
        /* ===================== */
        renderChart() {
            const ctx = document.getElementById('requestStatusChart');

            let d = this.buildChartData();

            if (this.chart) {
                this.chart.destroy();
            }

            this.chart = new Chart(ctx, {
                type: 'bar',
                data: d,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: { stacked: true },
                        y: { stacked: true, beginAtZero: true }
                    }
                }
            });
        }
    }
}
</script>
