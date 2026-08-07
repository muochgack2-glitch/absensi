<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="description" content="Sistem Absensi QR Code - Modern & Real-time">
    <meta name="theme-color" content="#1e3a8a">

    <title><?php echo e(config('app.name', 'Absensi QR')); ?> - <?php echo e($title ?? 'Dashboard'); ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Bootstrap CSS (Required for Tooltips) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Assets via Vite -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js', 'resources/js/sidebar.js', 'resources/js/navbar.js']); ?>
    
    <!-- Additional Styles -->
    <?php echo $__env->yieldPushContent('styles'); ?>
    
    <!-- Initialize Dark Mode & Sidebar State Early -->
    <script>
        // Apply dark mode class before page renders to prevent flash
        (function() {
            const darkMode = localStorage.getItem('darkMode') === 'true';
            if (darkMode) {
                document.documentElement.classList.add('dark');
            }
            
            // NO LONGER forcing sidebar width here - let Alpine handle it
        })();
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        }
        
        // Dark mode toggle function (Pure JavaScript - no Alpine needed)
        function toggleDarkMode() {
            const html = document.documentElement;
            const isDark = html.classList.contains('dark');
            
            if (isDark) {
                html.classList.remove('dark');
                localStorage.setItem('darkMode', 'false');
                updateDarkModeIcon(false);
            } else {
                html.classList.add('dark');
                localStorage.setItem('darkMode', 'true');
                updateDarkModeIcon(true);
            }
            
            console.log('Dark mode toggled:', !isDark ? 'Dark' : 'Light');
        }
        
        // Update icon display
        function updateDarkModeIcon(isDark) {
            const moonIcon = document.getElementById('dark-icon-moon');
            const sunIcon = document.getElementById('dark-icon-sun');
            
            if (moonIcon && sunIcon) {
                if (isDark) {
                    moonIcon.classList.add('hidden');
                    sunIcon.classList.remove('hidden');
                } else {
                    moonIcon.classList.remove('hidden');
                    sunIcon.classList.add('hidden');
                }
            }
        }
        
        // Initialize icon on page load
        document.addEventListener('DOMContentLoaded', function() {
            const isDark = localStorage.getItem('darkMode') === 'true';
            updateDarkModeIcon(isDark);
        });
    </script>
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <div class="min-h-screen" x-data>
        
        <!-- Sidebar -->
        <?php echo $__env->make('layouts.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        
        <!-- Main Content Area -->
        <div id="mainContent" class="main-content transition-all duration-300">
            
            <!-- Mobile Menu Button (SPMB Style) - Only visible on mobile -->
            <button 
                onclick="toggleMobileMenu()" 
                class="mobile-menu-btn"
                aria-label="Toggle menu"
            >
                <i class="fas fa-bars"></i>
            </button>
            
            <!-- Modern Floating Navbar (Dynamic) -->
            <nav id="dynamicNavbar" class="dynamic-navbar">
                <div class="navbar-container">
                    <!-- Left: Breadcrumb / Page Title -->
                    <div class="navbar-left">
                        <div class="breadcrumb">
                            <i class="fas fa-home text-primary-500"></i>
                            <span class="separator">/</span>
                            <span class="page-title"><?php echo e($pageTitle ?? 'Dashboard'); ?></span>
                        </div>
                    </div>
                    
                    <!-- Center: Search Bar (Optional) -->
                    <div class="navbar-center hidden lg:block">
                        <div class="search-container">
                            <i class="fas fa-search search-icon"></i>
                            <input 
                                type="text" 
                                placeholder="Cari siswa, kelas, atau laporan..." 
                                class="search-input"
                            >
                            <kbd class="search-shortcut">Ctrl+K</kbd>
                        </div>
                    </div>
                    
                    <!-- Right: Quick Actions -->
                    <div class="navbar-right">
                        <!-- Notifications -->
                        <button class="navbar-icon-btn" title="Notifikasi">
                            <i class="fas fa-bell"></i>
                            <span class="notification-badge">3</span>
                        </button>
                        
                        <!-- Dark Mode Toggle -->
                        <button 
                            id="navbarDarkModeToggle"
                            onclick="toggleDarkMode()" 
                            class="navbar-icon-btn" 
                            title="Toggle Dark Mode"
                        >
                            <i class="fas fa-moon" id="navbar-icon-moon"></i>
                            <i class="fas fa-sun hidden" id="navbar-icon-sun"></i>
                        </button>
                        
                        <!-- User Profile -->
                        <div class="user-profile">
                            <img 
                                src="https://ui-avatars.com/api/?name=<?php echo e(urlencode(auth()->user()->name ?? 'User')); ?>&background=3b82f6&color=fff" 
                                alt="User" 
                                class="user-avatar"
                            >
                            <div class="user-info hidden lg:block">
                                <p class="user-name"><?php echo e(auth()->user()->name ?? 'User'); ?></p>
                                <p class="user-role">Admin</p>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
            
            <!-- Page Content -->
            <main class="p-6 animate-fade-in" style="padding-top: 6rem;">
                <!-- Alerts/Flash Messages -->
                <?php if(session('success')): ?>
                    <div x-data="{ show: true }" x-show="show" x-transition class="mb-6">
                        <?php if (isset($component)) { $__componentOriginal5194778a3a7b899dcee5619d0610f5cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5194778a3a7b899dcee5619d0610f5cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.alert','data' => ['type' => 'success','message' => session('success'),'dismissible' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'success','message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(session('success')),'dismissible' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5194778a3a7b899dcee5619d0610f5cf)): ?>
<?php $attributes = $__attributesOriginal5194778a3a7b899dcee5619d0610f5cf; ?>
<?php unset($__attributesOriginal5194778a3a7b899dcee5619d0610f5cf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5194778a3a7b899dcee5619d0610f5cf)): ?>
<?php $component = $__componentOriginal5194778a3a7b899dcee5619d0610f5cf; ?>
<?php unset($__componentOriginal5194778a3a7b899dcee5619d0610f5cf); ?>
<?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if(session('error')): ?>
                    <div x-data="{ show: true }" x-show="show" x-transition class="mb-6">
                        <?php if (isset($component)) { $__componentOriginal5194778a3a7b899dcee5619d0610f5cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5194778a3a7b899dcee5619d0610f5cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.alert','data' => ['type' => 'danger','message' => session('error'),'dismissible' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'danger','message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(session('error')),'dismissible' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5194778a3a7b899dcee5619d0610f5cf)): ?>
<?php $attributes = $__attributesOriginal5194778a3a7b899dcee5619d0610f5cf; ?>
<?php unset($__attributesOriginal5194778a3a7b899dcee5619d0610f5cf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5194778a3a7b899dcee5619d0610f5cf)): ?>
<?php $component = $__componentOriginal5194778a3a7b899dcee5619d0610f5cf; ?>
<?php unset($__componentOriginal5194778a3a7b899dcee5619d0610f5cf); ?>
<?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if(session('warning')): ?>
                    <div x-data="{ show: true }" x-show="show" x-transition class="mb-6">
                        <?php if (isset($component)) { $__componentOriginal5194778a3a7b899dcee5619d0610f5cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5194778a3a7b899dcee5619d0610f5cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.alert','data' => ['type' => 'warning','message' => session('warning'),'dismissible' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'warning','message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(session('warning')),'dismissible' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5194778a3a7b899dcee5619d0610f5cf)): ?>
<?php $attributes = $__attributesOriginal5194778a3a7b899dcee5619d0610f5cf; ?>
<?php unset($__attributesOriginal5194778a3a7b899dcee5619d0610f5cf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5194778a3a7b899dcee5619d0610f5cf)): ?>
<?php $component = $__componentOriginal5194778a3a7b899dcee5619d0610f5cf; ?>
<?php unset($__componentOriginal5194778a3a7b899dcee5619d0610f5cf); ?>
<?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if(session('info')): ?>
                    <div x-data="{ show: true }" x-show="show" x-transition class="mb-6">
                        <?php if (isset($component)) { $__componentOriginal5194778a3a7b899dcee5619d0610f5cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5194778a3a7b899dcee5619d0610f5cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.alert','data' => ['type' => 'info','message' => session('info'),'dismissible' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'info','message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(session('info')),'dismissible' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5194778a3a7b899dcee5619d0610f5cf)): ?>
<?php $attributes = $__attributesOriginal5194778a3a7b899dcee5619d0610f5cf; ?>
<?php unset($__attributesOriginal5194778a3a7b899dcee5619d0610f5cf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5194778a3a7b899dcee5619d0610f5cf)): ?>
<?php $component = $__componentOriginal5194778a3a7b899dcee5619d0610f5cf; ?>
<?php unset($__componentOriginal5194778a3a7b899dcee5619d0610f5cf); ?>
<?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Page Content Slot -->
                <?php echo e($slot); ?>

            </main>
            
            <!-- Footer -->
            <?php echo $__env->make('layouts.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>

    <!-- Toast Container (Fixed bottom-right) -->
    <div id="toast-container" class="fixed bottom-4 right-4 z-50 space-y-2"></div>

    <!-- Bootstrap JS (Required for Tooltips) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Additional Scripts -->
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\Users\DMCenter\Music\SPMB2\SPMB\absensi\resources\views/layouts/app.blade.php ENDPATH**/ ?>