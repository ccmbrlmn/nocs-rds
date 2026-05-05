<nav x-data="{ open: false }">
<aside class="sidebar flex flex-col h-screen relative
              bg-white dark:bg-gray-900
              border-r border-gray-800 dark:border-gray-800">
              
        <div class="sidebar-header">
            <div class="row">
                <div class="column">
                    <img src="{{ asset('assets/images/nocs-logo.png') }}" alt="logo" class="logo-icon">
                </div>
                <div class="column logotext">
                    <img src="{{ asset('assets/images/logo-text.png') }}" alt="logo" class="logo-text">
                </div>
            </div>
        </div>

        <ul class="sidebar-links">

            @auth
                @if(Auth::user()->role === 'admin')

                    <li>
                        <a href="{{ route('admin.dashboard') }}"
                           class="flex items-center gap-2
                                  text-gray-700 dark:text-gray-300
                                  {{ Route::is('admin.dashboard') ? 'active text-blue-600 dark:text-blue-400' : '' }}">
                            <span class="material-symbols-outlined">dashboard</span> Dashboard
                        </a>
                    </li>
                    
                    <li>
<a href="{{ route('admin.assets') }}"
   class="flex items-center gap-2
          text-gray-700 dark:text-gray-300
          {{ request()->routeIs('admin.assets') ? 'active text-blue-600 dark:text-blue-400' : '' }}">

                            <span class="material-symbols-outlined">inventory_2</span>

                            Assets
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.requests') }}"
                           class="flex items-center gap-2
                                  text-gray-700 dark:text-gray-300
                                  {{ request()->routeIs('admin.requests','admin.request-details') ? 'active text-blue-600 dark:text-blue-400' : '' }}">
                            <span class="material-symbols-outlined">description</span> Requests
                        </a>
                    </li>

                    @php
                        $firstAdminId = \App\Models\User::where('role','admin')->orderBy('id')->first()->id ?? null;
                    @endphp

                    @if(Auth::id() === $firstAdminId)
                    <li>
                        <a href="{{ route('admin.created-admins') }}"
                           class="flex items-center gap-2
                                  text-gray-700 dark:text-gray-300
                                  {{ Route::is('admin.created-admins') ? 'active text-blue-600 dark:text-blue-400' : '' }}">
                            <span class="material-symbols-outlined">admin_panel_settings</span> Admins
                        </a>
                    </li>
                    @endif

                    <li>
                        <a href="{{ route('admin.users') }}"
                           class="flex items-center gap-2
                                  text-gray-700 dark:text-gray-300
                                  {{ request()->routeIs('admin.users','admin.users.logs') ? 'active text-blue-600 dark:text-blue-400' : '' }}">
                            <span class="material-symbols-outlined">person</span> Users
                        </a>
                    </li>

                @else

                    <li>
                        <a href="{{ route('dashboard') }}"
                           class="flex items-center gap-2
                                  text-gray-700 dark:text-gray-300
                                  {{ Route::is('dashboard') ? 'active text-blue-600 dark:text-blue-400' : '' }}">
                            <span class="material-symbols-outlined">dashboard</span> Dashboard
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('user.requests') }}"
                           class="flex items-center gap-2
                                  text-gray-700 dark:text-gray-300
                                  {{ request()->routeIs('user.requests','request-details.show') ? 'active text-blue-600 dark:text-blue-400' : '' }}">
                            <span class="material-symbols-outlined">description</span> Requests
                        </a>
                    </li>

                @endif

            @else

                <li>
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-2 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400">
                        <span class="material-symbols-outlined">dashboard</span> Dashboard
                    </a>
                </li>

                <li>
                    <a href="{{ route('login') }}"
                       class="flex items-center gap-2 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400">
                        <span class="material-symbols-outlined">description</span> Requests
                    </a>
                </li>

            @endauth

        </ul>

@auth
<div class="absolute bottom-6 left-0 w-full px-4 pt-4">
    <ul class="sidebar-links">
        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <a href="#"
                   onclick="event.preventDefault(); this.closest('form').submit();"
                   class="flex items-center gap-2
                          text-gray-700 dark:text-gray-300
                        hover:text-red-600 dark:hover:text-red-400">
                    <span class="material-symbols-outlined">logout</span> Log Out
                </a>
            </form>
        </li>
    </ul>
</div>
@endauth

</aside>
</nav>
