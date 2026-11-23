<header class="sticky top-0 z-50 h-16 bg-white/90 backdrop-blur border-b border-brand-100 shadow-sm">
    <div class="h-full flex items-center justify-between px-4 lg:px-6">

        <!-- Burger & Logo -->
        <div class="flex items-center gap-3">
            <button id="sidebarToggle" type="button" aria-label="Buka atau tutup sidebar"
                class="w-10 h-10 grid place-items-center rounded-lg text-brand-700 hover:bg-brand-100 focus:outline-none focus:ring-2 focus:ring-brand-300 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white rounded-full grid place-items-center ring-2 ring-brand-300 overflow-hidden">
                    <img src="https://cdn.aceimg.com/a9zDsOTNu.png" alt="Logo" class="w-7 h-7 object-contain">
                </div>
                <div class="hidden sm:block leading-tight">
                    <h1 class="text-brand-800 font-bold text-lg tracking-wide">E-LEARNING</h1>
                    <p class="text-brand-500 text-xs font-medium">Guru Dashboard</p>
                </div>
            </div>
        </div>

        <!-- Profile Dropdown -->
        <div class="flex items-center gap-2 sm:gap-4">
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" type="button"
                    class="flex items-center gap-2 hover:bg-brand-50 p-2 rounded-lg transition">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=3a9d5d&color=fff"
                        alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full ring-2 ring-brand-300">
                    <span class="hidden sm:block text-brand-900 font-medium text-sm">{{ Auth::user()->name }}</span>
                    <svg class="w-4 h-4 text-brand-600 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="open" @click.away="open = false"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    x-cloak
                    class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-brand-100 py-2 z-50">
                    <a href="#" class="block px-4 py-2 text-sm text-slate-700 hover:bg-brand-50 hover:text-brand-700 transition-colors">
                        <i class="fas fa-user mr-2 text-brand-500"></i> Profile
                    </a>
                    <a href="#" class="block px-4 py-2 text-sm text-slate-700 hover:bg-brand-50 hover:text-brand-700 transition-colors">
                        <i class="fas fa-cog mr-2 text-brand-500"></i> Pengaturan
                    </a>
                    <hr class="my-2 border-brand-100">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>