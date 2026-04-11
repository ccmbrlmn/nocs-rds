@php
$firstAdmin = \App\Models\User::where('role', 'admin')->orderBy('created_at')->first();
@endphp

<script>
const userRole = '{{ auth()->user()->role ?? 'user' }}';
const isFirstAdmin = {{ auth()->user() && $firstAdmin ? (auth()->user()->id === $firstAdmin->id ? 'true' : 'false') : 'false' }};
</script>

<div class="flex items-center gap-4"
     x-data="notificationsModal()"
     x-init="openModal = false; fetchNotifications(); setInterval(fetchNotifications, 30000)">

    <button @click="
        document.documentElement.classList.toggle('dark');
        localStorage.theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
    "
    class="w-12 h-12 flex items-center justify-center rounded-full hover:bg-blue-100 dark:hover:bg-gray-600 transition shadow-sm">
        <span class="material-symbols-outlined text-gray-600 dark:text-gray-200 text-2xl">
            dark_mode
        </span>
    </button>

    <div class="relative">
        <button @click="openNotificationsModal()"
                class="w-12 h-12 flex items-center justify-center rounded-full hover:bg-blue-100 dark:hover:bg-gray-600 transition shadow-sm relative">

            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-6 h-6 text-gray-600 dark:text-gray-200"
                 fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>

            <span x-show="hasNotifications" x-transition.opacity
                  class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full"></span>
        </button>

        <div x-show="openModal" x-cloak
             class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center px-4">

            <div @click.away="openModal = false"
                 class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-lg w-full p-6 overflow-y-auto max-h-[80vh]">

                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                        Notifications
                    </h2>
                    <button @click="openModal = false"
                            class="text-gray-500 dark:text-gray-200 hover:text-gray-700">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <template x-if="notifications.length === 0">
                    <div class="text-center text-gray-600 dark:text-gray-200 py-8">
                        No new notifications
                    </div>
                </template>

                <div class="overflow-y-auto max-h-[60vh]">
                    <template x-for="(notif, index) in notifications.slice(0, visibleCount)" :key="notif.id">
                        <div @click="
                            const data = notif.data;

                            // User/Admin deletion or registration
                            if(['user_deletion_request', 'user_registration', 'profile_updated'].includes(data?.type)){
                                const userId = data?.user_id;
                                if(userId){
                                    openModal = false;
                                    setTimeout(() => {
                                        if(data?.is_admin){
                                            window.location.href = `{{ url('/created-admins') }}?highlight=${userId}`;
                                        } else {
                                            window.location.href = `{{ route('admin.users') }}?highlight=${userId}`;
                                        }
                                    }, 150);
                                }
                                return;
                            }

                            // Request notifications
                            const requestId = data?.request_id;
                            if(requestId){
                                openModal = false;
                                setTimeout(() => {
                                    window.location.href = userRole === 'admin'
                                        ? `{{ url('/admin/requests') }}/${requestId}`
                                        : `{{ url('/request-details') }}/${requestId}`;
                                }, 150);
                                return;
                            }
                        "
                             :class="notif.is_read
                                ? 'block p-3 mb-3 border rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300 cursor-pointer'
                                : 'block p-3 mb-3 border rounded-lg font-semibold cursor-pointer'">

                            <div class="flex justify-between items-start gap-4">
                                <p class="text-sm leading-relaxed break-words max-w-[70%] line-clamp-2"
                                    x-text="(() => {
                                        const d = notif.data ?? {};

                                        // Helper to get the actor name
                                        const actor = d?.user_name || d?.requester_name || 'User';

                                           switch(d?.type || d?.action){
                                                case 'user_management':
                                                    const actionLabel = d.action || 'performed an action';
                                                    return `[User ${actionLabel.charAt(0).toUpperCase() + actionLabel.slice(1)}] ${d.actor_name} ${actionLabel} ${d.user_name}.`;

                                                case 'user_deletion_request':
                                                    return `[${d.is_admin ? 'Admin' : 'User'} Deletion Request] ${actor} requested account deletion.`;

                                                case 'created':
                                                case 'edited':
                                                    return `[${d.type_label || 'Request'}] ${actor} ${d.type === 'created' ? 'submitted' : 'updated'} ${d.request_name || d.eventName}.`;

                                                case 'request_accepted':
                                                    return `[Accepted Request] ${actor} had their request accepted.`;

                                                case 'request_declined':
                                                    return `[Declined Request] ${actor} had their request declined.`;

                                                case 'user_registration':
                                                    return `[User Registration] ${actor} recently created an account.`;

                                                case 'user_approved':
                                                    return `[Account Approved] ${d.message || `${actor}'s account was approved.`}`;

                                                case 'profile_updated':
                                                    return `[Profile Updated] ${actor} updated their profile.`;

                                                default:
                                                    return `[Notification] ${d.message || 'No details available.'}`;
                                        
                                        }
                                    })()"
                                ></p>
                                <span class="text-xs text-gray-400 whitespace-nowrap text-right"
                                      x-text="notif.created_at"></span>
                            </div>
                        </div>
                    </template>

                    <button x-show="visibleCount < notifications.length"
                            @click="visibleCount += 5"
                            class="w-full py-2 mt-2 text-sm font-medium text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-gray-700 rounded">
                        See more
                    </button>
                </div>
            </div>
        </div>
    </div>

    <a href="{{ url('/profile') }}"
       class="w-12 h-12 flex items-center justify-center rounded-full hover:bg-blue-100 dark:hover:bg-gray-600 transition shadow-sm">
        <img src="{{ asset('assets/images/user-pic.png') }}"
             class="w-10 h-10 rounded-full border-2 border-gray-300 dark:border-gray-600">
    </a>
</div>

<script>
window.notificationsModal = function () {
    return {
        openModal: false,
        notifications: [],
        hasNotifications: false,
        visibleCount: 3,

        fetchNotifications() {
            fetch(userRole === 'admin' ? '/admin/notifications' : '/notifications')
                .then(res => res.json())
                .then(data => {
                    this.notifications = data.map(n => {
                        if (typeof n.data === 'string') {
                            try {
                                n.data = JSON.parse(n.data);
                            } catch (e) {
                                n.data = {};
                            }
                        }
                        n.data.type = n.data.type || n.type; 
                        return n;
                    }).sort((a,b) => new Date(b.created_at) - new Date(a.created_at));

                    this.hasNotifications = this.notifications.some(n => !n.is_read);
                });
        },

        markNotificationsRead() {
    fetch(userRole === 'admin' ? '/admin/notifications/read' : '/notifications/read', {
        method: 'POST',
        headers: { 
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.ok ? res.json() : Promise.reject())
    .then(() => {
        this.notifications.forEach(n => n.is_read = true);
        this.hasNotifications = false;
    })
    .catch(() => {
        console.error('Failed to mark notifications as read');
    });
},

        openNotificationsModal() {
            this.openModal = true;

            if (this.notifications.length > 0) {
                this.markNotificationsRead();
            }
        }
    }
}
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
