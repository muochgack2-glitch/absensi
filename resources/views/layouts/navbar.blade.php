<!-- Top Navbar Component — Polished & Upgraded -->
<nav class="sticky top-0 z-30 bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl border-b border-gray-200/60 dark:border-gray-700/60 shadow-sm transition-colors duration-300">
    <div class="px-4 md:px-6 py-3">
        <div class="flex items-center justify-between">
            
            <!-- Left: Page Title & Breadcrumb -->
            <div class="flex items-center space-x-4">
                <!-- Mobile Menu Toggle -->
                <button 
                    @click="window.dispatchEvent(new CustomEvent('toggle-sidebar'))"
                    class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                    title="Toggle Sidebar"
                >
                    <i class="fas fa-bars text-lg"></i>
                </button>

                <!-- Page Title -->
                <div>
                    @php
                        $rawTitle = $pageTitle ?? 'Dashboard';
                        $displayTitle = is_string($rawTitle) ? $rawTitle : (string) $rawTitle;
                        
                        $titleMap = [
                            'Dashboard Absensi' => 'Dashboard',
                            'Manajemen Siswa' => 'Data Siswa',
                            'Manajemen Kelas' => 'Data Kelas',
                            'Laporan Absensi' => 'Laporan',
                            'Pengaturan Sistem' => 'Settings',
                        ];
                        
                        $displayTitle = $titleMap[$displayTitle] ?? $displayTitle;
                        
                        // Auto breadcrumb from title
                        $breadcrumbSection = null;
                        if (request()->routeIs('whatsapp.*')) {
                            $breadcrumbSection = 'WhatsApp';
                        } elseif (request()->routeIs('gateway.*')) {
                            $breadcrumbSection = 'Gateway';
                        } elseif (request()->routeIs('attendance.reports.*')) {
                            $breadcrumbSection = 'Laporan';
                        } elseif (request()->routeIs('attendance.students.*')) {
                            $breadcrumbSection = 'Siswa';
                        } elseif (request()->routeIs('attendance.classes.*')) {
                            $breadcrumbSection = 'Kelas';
                        }
                    @endphp
                    
                    <div class="flex items-center gap-2">
                        <h2 class="text-lg md:text-xl font-bold text-gray-800 dark:text-white">
                            {{ $displayTitle }}
                        </h2>
                    </div>
                    
                    <!-- Auto Breadcrumb -->
                    @if($breadcrumbSection || isset($breadcrumbs))
                        <nav class="flex items-center space-x-1.5 text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            <a href="{{ route('dashboard') }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                                <i class="fas fa-home"></i>
                            </a>
                            @if($breadcrumbSection)
                                <i class="fas fa-chevron-right text-[8px] text-gray-400"></i>
                                <span class="text-gray-600 dark:text-gray-300">{{ $breadcrumbSection }}</span>
                            @endif
                            @isset($breadcrumbs)
                                @foreach($breadcrumbs as $breadcrumb)
                                    <i class="fas fa-chevron-right text-[8px] text-gray-400"></i>
                                    @if(isset($breadcrumb['url']))
                                        <a href="{{ $breadcrumb['url'] }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                                            {{ $breadcrumb['label'] }}
                                        </a>
                                    @else
                                        <span class="text-gray-600 dark:text-gray-300">{{ $breadcrumb['label'] }}</span>
                                    @endif
                                @endforeach
                            @endisset
                        </nav>
                    @endif
                </div>
            </div>

            <!-- Right: Actions -->
            <div class="flex items-center space-x-1 md:space-x-2">
                
                <!-- Global Search -->
                <div class="relative hidden md:block" x-data="{ searchOpen: false, query: '' }">
                    <div class="relative">
                        <input 
                            type="text" 
                            placeholder="Cari siswa, kelas..." 
                            x-model="query"
                            @focus="searchOpen = true"
                            @blur="setTimeout(() => searchOpen = false, 200)"
                            @keydown.escape="searchOpen = false"
                            class="w-48 lg:w-64 pl-9 pr-8 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-700 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 focus:bg-white dark:focus:bg-gray-700 transition-all"
                        >
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <kbd class="absolute right-2 top-1/2 -translate-y-1/2 hidden lg:inline-flex items-center px-1.5 py-0.5 text-[10px] font-mono font-medium text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-600 rounded border border-gray-200 dark:border-gray-500">⌘K</kbd>
                    </div>
                    
                    <!-- Search Dropdown -->
                    <div 
                        x-show="searchOpen && query.length > 0" 
                        x-transition
                        class="absolute top-full mt-2 w-full bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden"
                    >
                        <div class="p-3 text-sm text-gray-500 dark:text-gray-400 text-center">
                            <i class="fas fa-search mr-1.5"></i>Ketik untuk mencari...
                        </div>
                    </div>
                </div>

                <!-- WhatsApp Gateway Status Pill -->
                <div class="hidden md:flex items-center" id="navbarWaStatus">
                    <a href="{{ route('whatsapp.index') }}" 
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full transition-all hover:scale-105"
                       id="waStatusPill"
                       title="WhatsApp Gateway Status">
                        <span class="relative flex h-2 w-2" id="waStatusDot">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-gray-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-gray-400"></span>
                        </span>
                        <i class="fab fa-whatsapp text-sm"></i>
                        <span class="hidden lg:inline" id="waStatusText">WA</span>
                    </a>
                </div>

                <!-- Dark Mode Toggle -->
                <button 
                    onclick="toggleDarkMode()"
                    id="dark-mode-toggle"
                    class="p-2.5 rounded-xl text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-amber-500 dark:hover:text-amber-400 transition-all"
                    title="Toggle Dark Mode"
                >
                    <i class="fas fa-moon text-base" id="dark-icon-moon"></i>
                    <i class="fas fa-sun text-base hidden" id="dark-icon-sun"></i>
                </button>

                <!-- Notifications -->
                <div class="relative" x-data="{ notifOpen: false }">
                    <button 
                        @click="notifOpen = !notifOpen"
                        class="relative p-2.5 rounded-xl text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all"
                        title="Notifikasi"
                    >
                        <i class="fas fa-bell text-base"></i>
                        <!-- Live badge (hidden by default, shown dynamically) -->
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white dark:ring-gray-800" id="notifBadge"></span>
                    </button>

                    <!-- Notifications Dropdown -->
                    <div 
                        x-show="notifOpen" 
                        @click.away="notifOpen = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden"
                    >
                        <!-- Header -->
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50/50 dark:bg-gray-900/30">
                            <h3 class="font-semibold text-gray-800 dark:text-white text-sm">Notifikasi</h3>
                            <span class="text-[10px] bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 px-2 py-0.5 rounded-full font-medium" id="notifCount">0 Baru</span>
                        </div>
                        
                        <!-- Notifications List -->
                        <div class="max-h-80 overflow-y-auto" id="notifList">
                            <!-- Dynamic notifications will be inserted here -->
                            <div class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">
                                <i class="fas fa-bell-slash text-2xl mb-2 opacity-50"></i>
                                <p class="text-xs">Belum ada notifikasi</p>
                            </div>
                        </div>
                        
                        <!-- Footer -->
                        <div class="px-4 py-2.5 bg-gray-50/80 dark:bg-gray-900/30 border-t border-gray-100 dark:border-gray-700 text-center">
                            <a href="{{ route('whatsapp.logs') }}" class="text-xs text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium">
                                Lihat semua log →
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Divider -->
                <div class="hidden md:block h-6 w-px bg-gray-200 dark:bg-gray-700"></div>

                <!-- User Menu Dropdown -->
                <div class="relative" x-data="{ userMenuOpen: false }">
                    <button 
                        @click="userMenuOpen = !userMenuOpen"
                        class="flex items-center space-x-2.5 p-1.5 pr-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-all"
                    >
                        <!-- Avatar -->
                        <div class="w-9 h-9 bg-gradient-to-br from-primary-400 to-primary-600 rounded-xl flex items-center justify-center text-white font-semibold text-sm shadow-md shadow-primary-500/20">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </div>
                        
                        <!-- User Info (hidden on mobile) -->
                        <div class="hidden md:block text-left">
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-200 leading-tight">{{ auth()->user()->name ?? 'User' }}</p>
                            <p class="text-[10px] text-gray-500 dark:text-gray-400 leading-tight">{{ ucfirst(auth()->user()->role ?? 'admin') }}</p>
                        </div>
                        
                        <i class="fas fa-chevron-down text-[10px] text-gray-400 transition-transform duration-200 hidden md:block" :class="{ 'rotate-180': userMenuOpen }"></i>
                    </button>

                    <!-- User Dropdown Menu -->
                    <div 
                        x-show="userMenuOpen" 
                        @click.away="userMenuOpen = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-60 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden"
                    >
                        <!-- User Info Section -->
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-primary-50 to-transparent dark:from-primary-900/20 dark:to-transparent">
                            <p class="text-sm font-bold text-gray-800 dark:text-white">{{ auth()->user()->name ?? 'User' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->email ?? 'user@example.com' }}</p>
                        </div>
                        
                        <!-- Menu Items -->
                        <div class="py-1.5">
                            <a href="{{ route('profile.edit') }}" class="flex items-center space-x-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <i class="fas fa-user-circle w-5 text-gray-400 dark:text-gray-500"></i>
                                <span>Profile Saya</span>
                            </a>
                            
                            <a href="{{ route('attendance.settings.index') }}" class="flex items-center space-x-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <i class="fas fa-cog w-5 text-gray-400 dark:text-gray-500"></i>
                                <span>Settings</span>
                            </a>

                            <a href="{{ route('whatsapp.index') }}" class="flex items-center space-x-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <i class="fab fa-whatsapp w-5 text-green-500"></i>
                                <span>WhatsApp Gateway</span>
                            </a>
                            
                            <div class="border-t border-gray-100 dark:border-gray-700 my-1.5"></div>
                            
                            <!-- Logout -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center space-x-3 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                    <i class="fas fa-sign-out-alt w-5"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</nav>

<!-- Navbar WA Status Script -->
<script>
    // Update WhatsApp status indicator in navbar
    function updateNavbarWaStatus() {
        fetch('{{ route("whatsapp.status") }}')
            .then(r => r.json())
            .then(data => {
                const pill = document.getElementById('waStatusPill');
                const dot = document.getElementById('waStatusDot');
                const text = document.getElementById('waStatusText');
                if (!pill) return;
                
                if (data.success && data.data && data.data.status === 'connected') {
                    pill.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full transition-all hover:scale-105 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800';
                    dot.innerHTML = '<span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-500 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>';
                    if (text) text.textContent = 'Online';
                } else if (data.success) {
                    pill.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full transition-all hover:scale-105 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-400 border border-yellow-200 dark:border-yellow-800';
                    dot.innerHTML = '<span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-500 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-yellow-500"></span>';
                    if (text) text.textContent = 'No Auth';
                } else {
                    pill.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full transition-all hover:scale-105 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800';
                    dot.innerHTML = '<span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>';
                    if (text) text.textContent = 'Offline';
                }
            })
            .catch(() => {
                const pill = document.getElementById('waStatusPill');
                const text = document.getElementById('waStatusText');
                if (pill) pill.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full transition-all hover:scale-105 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600';
                if (text) text.textContent = 'WA';
            });
    }

    // Run on page load and every 60 seconds
    document.addEventListener('DOMContentLoaded', function() {
        updateNavbarWaStatus();
        setInterval(updateNavbarWaStatus, 60000);
    });
</script>
