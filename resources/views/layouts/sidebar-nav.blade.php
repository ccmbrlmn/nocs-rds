<nav x-data="{ open: false }">
    <aside class="sidebar">
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
                        <a href="{{ route('admin.dashboard') }}" class="{{ Route::is('admin.dashboard') ? 'active' : '' }}">
                            <span class="material-symbols-outlined">dashboard</span> Dashboard
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.requests') }}" class="{{ request()->routeIs('admin.requests', 'admin.request-details') ? 'active' : '' }}">
                            <span class="material-symbols-outlined">description</span> Requests
                        </a>
                    </li>

                    @php
                        $firstAdminId = \App\Models\User::where('role', 'admin')->orderBy('id')->first()->id ?? null;
                    @endphp

                    @if(Auth::id() === $firstAdminId)
                        <li>
                            <a href="{{ route('admin.created-admins') }}" class="{{ Route::is('admin.created-admins') ? 'active' : '' }}">
                                <span class="material-symbols-outlined">group</span> My Admins
                            </a>
                        </li>
                        
                        <li>
                            <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users', 'admin.users.logs') ? 'active' : '' }}">
                                <span class="material-symbols-outlined">people</span> Users
                            </a>
                        </li>

                    @endif

                @else
                    <li>
                        <a href="{{ route('dashboard') }}" class="{{ Route::is('dashboard') ? 'active' : '' }}">
                            <span class="material-symbols-outlined">dashboard</span> Dashboard
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('user.requests') }}" class="{{ request()->routeIs('requests', 'request-details') ? 'active' : '' }}">
                            <span class="material-symbols-outlined">description</span> Requests
                        </a>
                    </li>
                @endif
            @else
                <li>
                    <a href="{{ route('dashboard') }}">
                        <span class="material-symbols-outlined">dashboard</span> Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('login') }}">
                        <span class="material-symbols-outlined">description</span> Requests
                    </a>
                </li>
            @endauth
        </ul>

        <div class="relative mt-6" x-data="{ open: false }">
            <div class="user-account cursor-pointer" @click="open = !open">
                <div class="user-profile flex items-center gap-3 p-2 hover:bg-transparent rounded-lg transition-colors">
                    <img src="{{ asset('assets/images/user-pic.png') }}" alt="profile-img" class="w-12 h-12 rounded-full border-2 border-white">
                    <div class="user-detail flex flex-col overflow-hidden">
                        <h3 class="text-white text-lg font-semibold truncate">{{ Auth::check() ? Auth::user()->name : 'Guest' }}</h3>
                        <span class="text-white text-sm truncate">{{ Auth::check() ? Auth::user()->email : 'Not Logged In' }}</span>
                    </div>
                    <span class="material-symbols-outlined text-white ml-auto">
                        expand_more
                    </span>
                </div>
            </div>

            <div x-show="open" x-transition
                 @click.away="open = false"
                 class="dropup-menu absolute bottom-full left-0 w-full bg-white shadow-lg rounded-lg p-2 border border-gray-200 mt-2 z-50">
                <ul class="flex flex-col gap-2">
                    @guest
                        <li>
                            <a href="{{ route('login') }}" class="flex items-center gap-2 text-gray-800 hover:bg-gray-100 p-2 rounded">
                                <span class="material-symbols-outlined text-gray-600">login</span> Log In
                            </a>
                        </li>
                    @endguest
                    @auth
                        <li>
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 text-gray-800 hover:bg-gray-100 p-2 rounded">
                                <span class="material-symbols-outlined text-gray-600">person</span> Profile
                            </a>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center gap-2 w-full text-gray-800 hover:bg-gray-100 p-2 rounded">
                                    <span class="material-symbols-outlined text-gray-600">logout</span> Log Out
                                </button>
                            </form>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </aside>
</nav>

