<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sistem Absensi QR Code - Modern & Real-time">
    <meta name="theme-color" content="#1e3a8a">

    <title>{{ config('app.name', 'Absensi QR') }} - {{ $title ?? 'Dashboard' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Bootstrap CSS (Required for Tooltips) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Assets via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/sidebar.js'])
    
    <!-- Additional Styles -->
    @stack('styles')
    
    <!-- Initialize Dark Mode Early -->
    <script>
        // Apply dark mode class before page renders to prevent flash
        (function() {
            if (localStorage.getItem('darkMode') === 'true') {
                document.documentElement.classList.add('dark');
            }
        })();
        
        // Dark mode toggle function (called from sidebar)
        function toggleDarkMode() {
            const html = document.documentElement;
            const isDark = html.classList.contains('dark');
            
            if (isDark) {
                html.classList.remove('dark');
                localStorage.setItem('darkMode', 'false');
            } else {
                html.classList.add('dark');
                localStorage.setItem('darkMode', 'true');
            }
            
            // Update sidebar icons (handled by sidebar.js)
            if (typeof updateDarkModeIcons === 'function') {
                updateDarkModeIcons(!isDark);
            }
        }

        // ─── Mobile Avatar Dropdown ─────────────────────────
        function toggleAvatarDropdown() {
            const dropdown = document.getElementById('mobileAvatarDropdown');
            if (!dropdown) return;
            dropdown.classList.toggle('open');
            syncDropdownDarkState();
        }

        function closeAvatarDropdown() {
            const dropdown = document.getElementById('mobileAvatarDropdown');
            if (dropdown) dropdown.classList.remove('open');
        }

        function toggleDarkModeFromDropdown() {
            // Reuse sidebar's toggleDarkMode
            toggleDarkMode();
            syncDropdownDarkState();
        }

        function syncDropdownDarkState() {
            const isDark = document.documentElement.classList.contains('dark');
            const icon  = document.getElementById('madDarkIcon');
            const label = document.getElementById('madDarkLabel');
            if (icon)  { icon.className  = isDark ? 'fas fa-sun mad-item-icon'  : 'fas fa-moon mad-item-icon'; }
            if (label) { label.textContent = isDark ? 'Light Mode' : 'Dark Mode'; }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const wrapper  = document.getElementById('mobileAvatarWrapper');
            const dropdown = document.getElementById('mobileAvatarDropdown');
            if (wrapper && dropdown && dropdown.classList.contains('open')) {
                if (!wrapper.contains(e.target)) closeAvatarDropdown();
            }
        });
    </script>
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <div class="min-h-screen" x-data>
        
        <!-- Sidebar -->
        @include('layouts.sidebar')
        
        <!-- Main Content Area -->
        <div id="mainContent" class="main-content transition-all duration-300">
            
            <!-- Mobile Top App Bar — only on mobile (<1024px) -->
            <header class="mobile-topbar" id="mobileTopbar">
                <!-- Hamburger toggle -->
                <button 
                    id="mobileMenuBtn"
                    onclick="toggleMobileMenu()" 
                    class="mobile-topbar-btn"
                    aria-label="Toggle menu"
                >
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>

                <!-- Brand -->
                <div class="mobile-topbar-brand">
                    @if(!empty($appLogoUrl))
                        <img src="{{ $appLogoUrl }}"
                             alt="Logo"
                             class="mobile-topbar-logo">
                    @else
                        <div style="width:28px;height:28px;background:rgba(255,255,255,0.2);border-radius:6px;display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-qrcode" style="color:white;font-size:14px;"></i>
                        </div>
                    @endif
                    <span class="mobile-topbar-title">{{ config('app.name', 'Absensi QR') }}</span>
                </div>

                <!-- User Avatar + Dropdown -->
                <div class="mobile-avatar-wrapper" id="mobileAvatarWrapper">
                    <button 
                        class="mobile-topbar-avatar" 
                        id="mobileAvatarBtn"
                        onclick="toggleAvatarDropdown()"
                        aria-label="User menu"
                    >
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </button>

                    <!-- Dropdown Menu -->
                    <div class="mobile-avatar-dropdown" id="mobileAvatarDropdown">
                        {{-- User info --}}
                        <div class="mad-header">
                            <div class="mad-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
                            <div class="mad-info">
                                <div class="mad-name">{{ auth()->user()->name }}</div>
                                <div class="mad-email">{{ auth()->user()->email }}</div>
                            </div>
                        </div>
                        <div class="mad-divider"></div>
                        {{-- Dark mode toggle --}}
                        <button class="mad-item" onclick="toggleDarkModeFromDropdown()">
                            <i class="fas fa-moon mad-item-icon" id="madDarkIcon"></i>
                            <span id="madDarkLabel">Dark Mode</span>
                            <span class="mad-toggle" id="madToggle"></span>
                        </button>
                        <div class="mad-divider"></div>
                        {{-- Profile --}}
                        <a href="{{ route('profile.edit') }}" class="mad-item">
                            <i class="fas fa-user mad-item-icon"></i>
                            <span>Profile</span>
                        </a>
                        {{-- Logout --}}
                        <form method="POST" action="{{ route('logout') }}" id="mobileLogoutForm">
                            @csrf
                            <button type="submit" class="mad-item mad-logout">
                                <i class="fas fa-sign-out-alt mad-item-icon"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </header>
            
            
            <!-- Page Content -->
            <main class="p-3 sm:p-4 md:p-6 animate-fade-in">
                <!-- Alerts/Flash Messages -->
                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show" x-transition class="mb-6">
                        <x-alert type="success" :message="session('success')" dismissible />
                    </div>
                @endif
                
                @if (session('error'))
                    <div x-data="{ show: true }" x-show="show" x-transition class="mb-6">
                        <x-alert type="danger" :message="session('error')" dismissible />
                    </div>
                @endif
                
                @if (session('warning'))
                    <div x-data="{ show: true }" x-show="show" x-transition class="mb-6">
                        <x-alert type="warning" :message="session('warning')" dismissible />
                    </div>
                @endif
                
                @if (session('info'))
                    <div x-data="{ show: true }" x-show="show" x-transition class="mb-6">
                        <x-alert type="info" :message="session('info')" dismissible />
                    </div>
                @endif

                <!-- Page Content Slot -->
                {{ $slot }}
            </main>
            
            <!-- Footer -->
            @include('layouts.footer')
        </div>
    </div>

    <!-- Toast Container (Fixed bottom-right) -->
    <div id="toast-container" class="fixed bottom-4 right-4 z-50 space-y-2"></div>

    <!-- Bootstrap JS (Required for Tooltips) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Additional Scripts -->
    @stack('scripts')
</body>
</html>
