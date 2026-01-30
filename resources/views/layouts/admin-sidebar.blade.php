<nav x-data="{ open: false }">
   <aside class = "sidebar">
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
    <ul class = "sidebar-links">
        <li>
            <a href="{{ route('admin-dashboard') }}"
                class="{{ Route::is('admin-dashboard') ? 'active' : '' }}">
            <span class="material-symbols-outlined">
                dashboard
            </span>Dashboard</a>
        </li>
        <li>
            <a href="{{ route('requests') }}" 
                class="{{ request()->routeIs('requests', 'request-details') ? 'active' : '' }}">
                <span class="material-symbols-outlined">description</span>
                Requests
            </a>
        </li>
       

    </ul>


    <div class="relative" x-data="{ open: false }">
        <div class="user-account cursor-pointer"
            @click="open = !open">
            <div class="user-profile flex items-center gap-1">
                <img src="{{ asset('assets/images/user-pic.png') }}" alt="profile-img" class="w-10 h-10 rounded-full">
                <div class="user-detail">
                    <h3 class="text-lg font-semibold">
                        {{ Auth::user()->name }}
                    </h3>
                    <span class="text-sm text-white">
                        {{ Auth::user()->email }}
                    </span>
                </div>
            </div>
        </div>

        <div x-show="open" x-transition 
            class="dropup-menu absolute bottom-full left-0 bg-white shadow-lg rounded-lg p-2 border border-gray-200"
            x-bind:style="open ? 'height: 7rem;' : ''">
        
            <ul class="text-sm text-gray-700 h-full flex flex-col justify-between">
                <li class="px-4 py-2 hover:bg-gray-100">
                    <a href="{{ route('register') }}" class="flex justify-between items-center w-full rounded-md text-black cursor-pointer ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                        @click.stop>
                        <span>Create Account</span>
                        <span class="material-symbols-outlined">account_circle</span>
                    </a>
                </li>


                <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer">
                    <form method="POST" action="{{ route('logout') }}" class="flex justify-between items-center w-full">
                        @csrf
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                this.closest('form').submit();"
                            class="flex justify-between items-center w-full">
                            <span>{{ __('Log Out') }}</span>
                            <span class="material-symbols-outlined">logout</span>
                        </a>
                    </form>
                </li>
            </ul>
        </div>

    </div>

</nav>



