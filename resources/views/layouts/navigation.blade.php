<nav x-data="{ open: false }" class="fixed top-0 left-0 right-0 z-50 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 shadow-lg border-b-4 border-purple-400">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 group">
                        <div class="bg-white rounded-lg p-1.5 shadow-md group-hover:shadow-lg transition-shadow flex items-center justify-center w-10 h-10">
                            <img src="{{ asset('images/iconjobdel.png') }}" alt="Job Delegation Icon" class="w-full h-full object-contain">
                        </div>
                        <span class="text-white font-bold text-lg hidden sm:block">Job Delegation</span>
                    </a>
                </div>

                <!-- Grouped Navigation Links -->
                <div class="hidden sm:-my-px sm:ms-10 sm:flex sm:items-center sm:space-x-1">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center h-10 px-4 rounded-lg text-sm font-medium transition-all duration-200 whitespace-nowrap {{ request()->routeIs('dashboard') ? 'bg-white text-purple-600 shadow-lg' : 'text-white hover:bg-white/20 hover:text-white' }}">
                        <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <!-- Tasks (direct link) -->
                    <a href="{{ route('tasks.index') }}" class="inline-flex items-center justify-center h-10 px-4 rounded-lg text-sm font-medium transition-all duration-200 whitespace-nowrap {{ request()->routeIs('tasks.*') ? 'bg-white text-purple-600 shadow-lg' : 'text-white hover:bg-white/20 hover:text-white' }}">
                        <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"></path>
                        </svg>
                        <span>Tasks</span>
                    </a>

                    <!-- Delegations (direct link) -->
                    <a href="{{ route('delegations.index') }}" class="inline-flex items-center justify-center h-10 px-4 rounded-lg text-sm font-medium transition-all duration-200 whitespace-nowrap {{ request()->routeIs('delegations.*') ? 'bg-white text-purple-600 shadow-lg' : 'text-white hover:bg-white/20 hover:text-white' }}">
                        <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.124-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.653.124-1.283.356-1.857m0 0a3.001 3.001 0 015.644 0M12 12a3 3 0 100-6 3 3 0 000 6z"></path>
                        </svg>
                        <span>Delegations</span>
                    </a>

                    <!-- Resources dropdown (Rooms, STO, Subordinates, User) -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="inline-flex items-center justify-center h-10 px-4 rounded-lg text-sm font-medium text-white hover:bg-white/20 transition-all duration-200 whitespace-nowrap focus:outline-none {{ request()->routeIs('rooms.*', 'sto.*', 'leader.subordinates.*', 'admin.users.*') ? 'bg-white text-purple-600 shadow-lg' : '' }}">
                            <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                            </svg>
                            <span>Resources</span>
                            <svg class="w-3 h-3 ml-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition class="absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-50 border border-gray-200" style="display:none;">
                            <a href="{{ route('rooms.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 first:rounded-t-lg">Rooms</a>
                            <a href="{{ route('sto.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">STO</a>
                            @if(Auth::user()->position && Auth::user()->position->name === 'Superuser')
                                <a href="{{ route('admin.users.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 last:rounded-b-lg">User</a>
                            @else
                                <a href="{{ route('leader.subordinates.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 last:rounded-b-lg">Subordinates</a>
                            @endif
                        </div>
                    </div>

                    <!-- Reports dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="inline-flex items-center justify-center h-10 px-4 rounded-lg text-sm font-medium text-white hover:bg-white/20 transition-all duration-200 whitespace-nowrap focus:outline-none">
                            <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span>Reports</span>
                            <svg class="w-3 h-3 ml-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition class="absolute left-0 mt-2 w-56 bg-white rounded-lg shadow-lg z-50 border border-gray-200" style="display:none;">
                            <a href="{{ route('reports.timeline') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 first:rounded-t-lg">Timeline</a>
                            @if(Auth::user()->subordinates()->exists())
                            <a href="{{ route('leader.reports.overview') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Team Report</a>
                            <a href="{{ route('leader.reports.user-reports') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Laporan Per User</a>
                            <a href="{{ route('leader.reports.work-time') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Work Time Report</a>
                            <a href="{{ route('leader.reports.work-time-history') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 last:rounded-b-lg">Efektivitas Waktu Kerja</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pojok kanan: Dropdown nama user (Profile & Log out) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-4 py-2 bg-white/20 backdrop-blur-sm rounded-lg text-sm leading-4 font-medium text-white hover:bg-white/30 focus:outline-none transition ease-in-out duration-150 border border-white/30">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-yellow-400 to-pink-500 flex items-center justify-center text-white font-bold mr-2">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <span>{{ Auth::user()->name }}</span>
                            </div>
                            <div class="ms-2">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        
                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger (mobile) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center min-h-[44px] min-w-[44px] p-2 rounded-lg text-white hover:text-purple-200 hover:bg-white/20 focus:outline-none focus:bg-white/20 focus:text-purple-200 transition duration-150 ease-in-out touch-target" aria-label="Toggle menu">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white/95 backdrop-blur-sm shadow-lg max-h-[calc(100vh-4rem)] overflow-y-auto">
        <div class="pt-2 pb-3 space-y-0 px-2">
            <a href="{{ route('dashboard') }}" class="flex items-center min-h-[44px] px-4 py-3 text-base font-medium rounded-lg {{ request()->routeIs('dashboard') ? 'bg-purple-100 text-purple-600 border-l-4 border-purple-600' : 'text-gray-700 hover:bg-gray-50' }}">Dashboard</a>

            <a href="{{ route('tasks.index') }}" class="flex items-center min-h-[44px] px-4 py-3 text-base font-medium rounded-lg {{ request()->routeIs('tasks.*') ? 'bg-purple-100 text-purple-600 border-l-4 border-purple-600' : 'text-gray-700 hover:bg-gray-50' }}">Tasks</a>

            <a href="{{ route('delegations.index') }}" class="flex items-center min-h-[44px] px-4 py-3 text-base font-medium rounded-lg {{ request()->routeIs('delegations.*') ? 'bg-purple-100 text-purple-600 border-l-4 border-purple-600' : 'text-gray-700 hover:bg-gray-50' }}">Delegations</a>

            <div x-data="{ openResources: false }" class="border-t border-gray-200 pt-1">
                <button @click="openResources = !openResources" class="w-full text-left flex items-center min-h-[44px] px-4 py-3 text-base font-medium text-gray-700 rounded-lg hover:bg-gray-50 touch-target">Resources</button>
                <div x-show="openResources" class="pl-2">
                    <a href="{{ route('rooms.index') }}" class="flex items-center min-h-[44px] px-4 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 rounded-lg">Rooms</a>
                    <a href="{{ route('sto.index') }}" class="flex items-center min-h-[44px] px-4 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 rounded-lg {{ request()->routeIs('sto.*') ? 'bg-purple-100 text-purple-600 border-l-4 border-purple-600' : '' }}">STO</a>
                    @if(Auth::user()->position && Auth::user()->position->name === 'Superuser')
                        <a href="{{ route('admin.users.index') }}" class="flex items-center min-h-[44px] px-4 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 rounded-lg">User</a>
                    @else
                        <a href="{{ route('leader.subordinates.index') }}" class="flex items-center min-h-[44px] px-4 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 rounded-lg">Subordinates</a>
                    @endif
                </div>
            </div>

            <div x-data="{ openReports: false }" class="border-t border-gray-200 pt-1">
                <button @click="openReports = !openReports" class="w-full text-left flex items-center min-h-[44px] px-4 py-3 text-base font-medium text-gray-700 rounded-lg hover:bg-gray-50 touch-target">Reports</button>
                <div x-show="openReports" class="pl-2">
                    <a href="{{ route('reports.timeline') }}" class="flex items-center min-h-[44px] px-4 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 rounded-lg">Timeline</a>
                    @if(Auth::user()->subordinates()->exists())
                    <a href="{{ route('leader.reports.overview') }}" class="flex items-center min-h-[44px] px-4 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 rounded-lg">Team Report</a>
                    <a href="{{ route('leader.reports.user-reports') }}" class="flex items-center min-h-[44px] px-4 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 rounded-lg">Laporan Per User</a>
                    <a href="{{ route('leader.reports.work-time') }}" class="flex items-center min-h-[44px] px-4 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 rounded-lg">Work Time Report</a>
                    <a href="{{ route('leader.reports.work-time-history') }}" class="flex items-center min-h-[44px] px-4 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 rounded-lg">Efektivitas Waktu Kerja</a>
                    @endif
                </div>
            </div>

            <a href="{{ route('profile.edit') }}" class="flex items-center min-h-[44px] px-4 py-3 text-base font-medium rounded-lg border-t border-gray-200 {{ request()->routeIs('profile.*') ? 'bg-purple-100 text-purple-600 border-l-4 border-purple-600' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Profil
            </a>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-yellow-400 to-pink-500 flex items-center justify-center text-white font-bold mr-3">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>
            </div>

            <div class="mt-3 space-y-0">
                <a href="{{ route('profile.edit') }}" class="flex items-center min-h-[44px] px-4 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 rounded-lg">
                    Profile
                </a>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}" class="block">
                    @csrf
                    <button type="submit" class="w-full text-left flex items-center min-h-[44px] px-4 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 rounded-lg touch-target">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
