<!-- Sidebar Component with SPMB Technology (Vanilla JS + Bootstrap Tooltips + Hover Expand) -->

<!-- Mobile Overlay (SPMB Style) - BEFORE sidebar in DOM -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside 
    id="adminSidebar"
    class="sidebar fixed top-0 left-0 h-screen bg-gradient-to-b from-primary-900 via-primary-800 to-primary-900 shadow-2xl z-50 overflow-hidden"
    style="width: 16rem; transition: width 0.3s ease;"
>

<style>
    /* ============================================ */
    /* SPMB SIDEBAR TECHNOLOGY - CSS */
    /* ============================================ */
    
    /* Base Sidebar Styles */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        background: linear-gradient(to bottom, #1e3a8a, #1e40af, #1e3a8a);
        transition: none; /* Disabled on load to prevent flash */
        overflow: hidden;
        z-index: 50;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    }
    
    /* Enable transitions after page load (added by JS after 50ms) */
    .sidebar.transitions-enabled {
        transition: width 0.3s ease !important;
    }
    
    /* Collapsed State */
    .sidebar.collapsed {
        width: 5rem !important;
    }
    
    /* ============================================ */
    /* HOVER EXPAND: Collapsed sidebar expands on hover */
    /* ============================================ */
    @media (min-width: 1024px) {
        .sidebar.collapsed:hover {
            width: 16rem !important;
        }
    }
    
    /* Nav text: width+opacity approach (SPMB - both animatable) */
    .sidebar .nav-text {
        display: inline-block;
        opacity: 1;
        width: auto;
        overflow: hidden;
        white-space: nowrap;
        transition: opacity 0.3s ease, width 0.3s ease;
    }
    
    .sidebar.collapsed .nav-text {
        opacity: 0 !important;
        width: 0 !important;
        overflow: hidden !important;
    }
    
    /* Hover: show nav text again */
    .sidebar.collapsed:hover .nav-text {
        opacity: 1 !important;
        width: auto !important;
    }
    

    
    /* Section labels */
    .sidebar-section-label {
        transition: opacity 0.3s ease;
    }
    
    .sidebar.collapsed .sidebar-section-label {
        opacity: 0;
        height: 0;
        padding: 0 !important;
        margin: 0 !important;
        overflow: hidden;
    }
    
    .sidebar.collapsed:hover .sidebar-section-label {
        opacity: 1;
        height: auto;
        padding: revert !important;
        margin: revert !important;
    }
    

    
    /* Badges */
    .sidebar-badge {
        transition: opacity 0.3s ease;
    }
    
    .sidebar.collapsed .sidebar-badge {
        opacity: 0;
    }
    
    .sidebar.collapsed:hover .sidebar-badge {
        opacity: 1;
    }
    

    
    /* ============================================ */
    /* SIDEBAR BRAND (SPMB Approach) */
    /* ============================================ */
    .sidebar-brand {
        position: relative;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 20px 1rem;
        min-height: 80px;
        transition: padding 0.3s ease;
    }
    
    .sidebar-brand-logo {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .sidebar-brand-text {
        color: white;
        font-weight: 700;
        line-height: 1.2;
        overflow: hidden;
        white-space: nowrap;
        transition: opacity 0.3s ease, width 0.3s ease;
    }
    
    /* Collapsed brand: center logo, shrink text to 0 */
    .sidebar.collapsed .sidebar-brand {
        padding: 20px 10px;
        justify-content: center;
    }
    
    .sidebar.collapsed .sidebar-brand-text {
        opacity: 0;
        width: 0;
        overflow: hidden;
    }
    
    /* Hover: show brand text again */
    .sidebar.collapsed:hover .sidebar-brand {
        padding: 20px 1rem;
        justify-content: flex-start;
    }
    
    .sidebar.collapsed:hover .sidebar-brand-text {
        opacity: 1;
        width: auto;
    }

    
    /* ============================================ */
    /* TOGGLE BUTTON (SPMB Style) */
    /* ============================================ */
    .sidebar-toggle-btn {
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: transparent;
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: rgba(255, 255, 255, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.3s ease, border-color 0.3s ease, color 0.3s ease, opacity 0.3s ease;
        z-index: 10;
        font-size: 10px;
        padding: 0;
        opacity: 1;
    }
    
    .sidebar-toggle-btn:hover {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.6);
        color: white;
    }
    
    /* Show toggle button only on desktop */
    @media (min-width: 1024px) {
        .sidebar-toggle-btn {
            display: flex !important;
        }
    }
    
    /* When collapsed, HIDE toggle button */
    .sidebar.collapsed .sidebar-toggle-btn {
        opacity: 0;
        pointer-events: none;
    }
    
    /* Show toggle button again on hover */
    .sidebar.collapsed:hover .sidebar-toggle-btn {
        opacity: 1;
        pointer-events: auto;
    }
    
    /* ============================================ */
    /* MENU ITEMS (SPMB Approach) */
    /* ============================================ */
    .sidebar-menu-item {
        position: relative;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 0.75rem 1rem;
        color: rgba(191, 219, 254, 0.9);
        text-decoration: none;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
        cursor: pointer;
        white-space: nowrap;
    }
    
    /* Collapsed: center icons */
    .sidebar.collapsed .sidebar-menu-item {
        justify-content: center !important;
        padding: 0.75rem 10px !important;
    }
    
    /* Hover expand: back to left-aligned */
    .sidebar.collapsed:hover .sidebar-menu-item {
        justify-content: flex-start !important;
        padding: 0.75rem 1rem !important;
    }
    
    .sidebar-menu-item:hover {
        background: rgba(30, 64, 175, 0.3);
        color: white;
        transform: translateX(4px);
    }
    
    .sidebar-menu-item.active {
        background: rgba(255, 255, 255, 0.1) !important;
        color: white;
        border-left: 4px solid white;
        padding-left: calc(1rem - 4px);
    }
    
    .sidebar-menu-item.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 4px;
        background: linear-gradient(180deg, rgba(255,255,255,0.9), rgba(255,255,255,0.5));
        box-shadow: 0 0 10px rgba(255,255,255,0.5);
    }
    
    .sidebar-menu-item i {
        width: 1.25rem;
        text-align: center;
        flex-shrink: 0;
    }
    
    /* ============================================ */
    /* SECTION LABELS */
    /* ============================================ */
    .sidebar-section-label {
        font-size: 0.625rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: rgba(255, 255, 255, 0.4);
        padding: 0.5rem 1rem;
        margin-top: 0.5rem;
        max-height: 2rem;
        overflow: hidden;
        transition: max-height 0.3s ease, opacity 0.2s ease, margin 0.3s ease, padding 0.3s ease;
    }
    
    /* Collapsed: height to 0 */
    .sidebar.collapsed .sidebar-section-label {
        max-height: 0;
        opacity: 0;
        margin-top: 0;
        margin-bottom: 0;
        padding-top: 0;
        padding-bottom: 0;
    }
    
    /* Show on hover */
    .sidebar.collapsed:hover .sidebar-section-label {
        max-height: 2rem;
        opacity: 1;
        margin-top: 0.5rem;
        padding: 0.5rem 1rem;
        transition-delay: 0.15s;
    }
    
    /* ============================================ */
    /* BADGE NOTIFICATION */
    /* ============================================ */
    .sidebar-badge {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        min-width: 1.25rem;
        height: 1.25rem;
        padding: 0 0.375rem;
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        font-size: 0.625rem;
        font-weight: 700;
        border-radius: 9999px;
        display: none;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.4);
        animation: pulse-badge 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        transition: opacity 0.3s ease;
    }
    
    @keyframes pulse-badge {
        0%, 100% { opacity: 1; }
        50% { opacity: .8; }
    }
    
    /* ============================================ */
    /* DIVIDER */
    /* ============================================ */
    .sidebar-divider {
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
        margin: 0.75rem 0;
    }
    
    /* ============================================ */
    /* MOBILE OVERLAY (SPMB Style) */
    /* ============================================ */
    .sidebar-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        z-index: 1040;
        transition: opacity 0.3s ease;
    }
    
    .sidebar-overlay.show {
        display: block;
    }
    
    /* ============================================ */
    /* MOBILE CLOSE BUTTON */
    /* ============================================ */
    .sidebar-close-btn {
        display: none;
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        width: 2rem;
        height: 2rem;
        border: none;
        background: rgba(255, 255, 255, 0.15);
        color: white;
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.2s ease;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        z-index: 100;
    }
    
    .sidebar-close-btn:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-50%) scale(1.1);
    }
    
    .sidebar-close-btn:active {
        transform: translateY(-50%) scale(0.95);
    }
    
    /* Only show close button on mobile */
    @media (max-width: 1023px) {
        .sidebar-close-btn {
            display: flex !important;
        }
        .sidebar-toggle-btn {
            display: none !important;
        }
    }
    
    /* ============================================ */
    /* MOBILE MENU STATE (SPMB Approach - Clean) */
    /* ============================================ */
    @media (max-width: 1023px) {
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            width: 16rem !important;
            z-index: 1050 !important;
        }
        
        .sidebar.mobile-show {
            transform: translateX(0);
        }
        
        /* Disable desktop toggle on mobile */
        .sidebar-toggle-btn {
            display: none !important;
        }
    }
    
    /* ============================================ */
    /* CUSTOM SCROLLBAR */
    /* ============================================ */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 2px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.3);
    }
    
    /* ============================================ */
    /* BOTTOM ICON BUTTONS (COMPACT) */
    /* ============================================ */
    .bottom-section {
        padding: 0.75rem;
        border-top: 1px solid rgba(59, 130, 246, 0.5);
    }
    
    .sidebar.collapsed .bottom-section {
        padding: 0.5rem 0.75rem;
    }
    
    .sidebar.collapsed:hover .bottom-section {
        padding: 0.75rem;
    }
    
    .bottom-icons-container {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-evenly;
        gap: 0.5rem;
        width: 100%;
    }
    
    /* Collapsed: vertical stack like nav */
    .sidebar.collapsed .bottom-icons-container {
        flex-direction: column;
        gap: 0.125rem;
    }
    
    /* Bottom section form - ensure it participates in flex layout properly */
    .bottom-icons-container > form {
        display: contents; /* Makes form transparent to flexbox */
    }
    
    .bottom-icon-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        padding: 0.5rem;
        width: auto;
        border-radius: 0.5rem;
        color: rgba(191, 219, 254, 0.9);
        background: transparent;
        border: none;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        white-space: nowrap;
    }
    
    .bottom-icon-btn:hover {
        background: rgba(30, 64, 175, 0.3);
        color: white;
        transform: translateX(4px);
    }
    
    .bottom-icon-btn:active {
        transform: translateX(0);
    }
    
    /* Icon styling - match menu items */
    .bottom-icon-btn i,
    .bottom-icon-btn .icon-wrapper {
        width: 1.25rem;
        text-align: center;
        flex-shrink: 0;
        font-size: 1.125rem;
    }
    
    /* Avatar specific sizing - normal state */
    .bottom-icon-btn .avatar-icon {
        width: 1.5rem;
        height: 1.5rem;
        font-size: 0.75rem;
        transition: all 0.2s ease;
    }
    
    /* Avatar when collapsed - smaller to match icons */
    .sidebar.collapsed .bottom-icon-btn .avatar-icon {
        width: 1.25rem;
        height: 1.25rem;
        font-size: 0.625rem;
    }
    
    /* Avatar when collapsed + hover - back to normal */
    .sidebar.collapsed:hover .bottom-icon-btn .avatar-icon {
        width: 1.5rem;
        height: 1.5rem;
        font-size: 0.75rem;
    }
    
    /* Text labels - match menu items */
    .bottom-icon-btn .btn-text {
        display: none; /* Hide completely by default (expanded sidebar) */
        flex: 1;
        overflow: hidden;
        font-size: 1rem;
        font-weight: 500;
        line-height: 1.5;
        text-align: left;
    }
    
    /* Collapsed state: still hidden */
    .sidebar.collapsed .bottom-icon-btn {
        justify-content: center;
        padding: 0.75rem 10px;
        width: 100%;
    }
    
    .sidebar.collapsed .bottom-icon-btn .btn-text {
        display: none;
    }
    
    /* Collapsed + hover: show text with smooth transition */
    .sidebar.collapsed:hover .bottom-icon-btn {
        justify-content: flex-start;
        padding: 0.75rem 1rem;
    }
    
    .sidebar.collapsed:hover .bottom-icon-btn .btn-text {
        display: block;
        animation: fadeInText 0.2s ease 0.15s forwards;
        opacity: 0;
    }
    
    @keyframes fadeInText {
        to {
            opacity: 1;
        }
    }
    
    /* Special styling for logout button */
    .bottom-icon-btn.logout-btn {
        color: #fca5a5;
    }
    
    .bottom-icon-btn.logout-btn:hover {
        background: rgba(220, 38, 38, 0.2);
        color: #fef2f2;
    }
    /* ============================================ */
    /* COLLAPSIBLE SUBMENU */
    /* ============================================ */
    .sidebar-submenu-group {
        margin-top: 0.25rem;
    }
    
    .sidebar-submenu-toggle {
        text-align: left;
        border: none;
        background: none;
    }
    
    .sidebar-submenu-toggle.submenu-active {
        color: white;
    }
    
    .sidebar-submenu-toggle .submenu-arrow {
        margin-left: auto;
    }
    
    .sidebar-submenu {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease, opacity 0.3s ease;
        opacity: 0;
        padding-left: 0.5rem;
    }
    
    .sidebar-submenu.submenu-open {
        max-height: 400px;
        opacity: 1;
    }
    
    .sidebar-submenu-item {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.5rem 0.75rem 0.5rem 1.75rem;
        color: rgba(191, 219, 254, 0.7);
        text-decoration: none;
        border-radius: 0.375rem;
        transition: all 0.2s ease;
        font-size: 0.8125rem;
        position: relative;
    }
    
    .sidebar-submenu-item::before {
        content: '';
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background: rgba(191, 219, 254, 0.3);
        transition: all 0.2s ease;
    }
    
    .sidebar-submenu-item:hover {
        background: rgba(30, 64, 175, 0.25);
        color: white;
        padding-left: 2rem;
    }
    
    .sidebar-submenu-item:hover::before {
        background: white;
        box-shadow: 0 0 6px rgba(255,255,255,0.4);
    }
    
    .sidebar-submenu-item.active {
        background: rgba(255, 255, 255, 0.08);
        color: white;
    }
    
    .sidebar-submenu-item.active::before {
        background: #60a5fa;
        width: 6px;
        height: 6px;
        box-shadow: 0 0 8px rgba(96, 165, 250, 0.6);
    }
    
    .sidebar-submenu-item i {
        width: 1rem;
        text-align: center;
        font-size: 0.75rem;
        flex-shrink: 0;
    }
    
    /* Collapsed: hide submenu completely */
    .sidebar.collapsed .sidebar-submenu {
        max-height: 0 !important;
        opacity: 0 !important;
    }
    
    /* Collapsed hover: show submenu */
    .sidebar.collapsed:hover .sidebar-submenu.submenu-open {
        max-height: 400px !important;
        opacity: 1 !important;
    }
    
    /* Collapsed: hide arrow */
    .sidebar.collapsed .submenu-arrow {
        opacity: 0;
        visibility: hidden;
    }
    
    .sidebar.collapsed:hover .submenu-arrow {
        opacity: 1;
        visibility: visible;
        transition-delay: 0.15s;
    }

</style>


    <div class="flex flex-col h-full">
        
        <!-- Logo Section with Toggle Button (SPMB Position) -->
        <div class="sidebar-brand border-b border-primary-700/50">
            <div class="sidebar-brand-logo">
                <?php if($appLogoUrl): ?>
                    <img src="<?php echo e($appLogoUrl); ?>" alt="Logo"
                         class="w-10 h-10 rounded-lg object-contain bg-white p-0.5 shadow-lg">
                <?php else: ?>
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-400 to-primary-600 rounded-lg flex items-center justify-center shadow-lg">
                        <i class="fas fa-qrcode text-white text-xl"></i>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="sidebar-brand-text">
                <h1 class="text-white font-bold text-lg leading-tight">Absensi QR</h1>
                <p class="text-primary-300 text-xs truncate max-w-[120px]"><?php echo e($appSchoolName); ?></p>
            </div>
            
            <!-- Toggle Button (SPMB Position - Top Right Corner) -->
            <button class="sidebar-toggle-btn" 
                    type="button" 
                    id="sidebarToggle" 
                    title="Toggle Sidebar">
                <i class="fas fa-circle text-xs"></i>
            </button>

            <!-- Mobile Close Button -->
            <button 
                onclick="closeMobileMenu()"
                class="sidebar-close-btn"
                type="button"
                aria-label="Close menu"
            >
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto custom-scrollbar">
            
            <!-- Dashboard -->
            <a 
                href="<?php echo e(route('attendance.dashboard')); ?>"
                class="sidebar-menu-item <?php echo e((request()->routeIs('attendance.dashboard') || request()->routeIs('dashboard')) ? 'active' : ''); ?>"
                data-bs-toggle="tooltip" 
                data-bs-placement="right" 
                title="Dashboard"
            >
                <i class="fas fa-home text-lg"></i>
                <span class="nav-text font-medium">Dashboard</span>
            </a>

            <!-- QR Scanner -->
            <a 
                href="<?php echo e(route('attendance.scanner')); ?>"
                class="sidebar-menu-item <?php echo e(request()->routeIs('attendance.scanner') ? 'active' : ''); ?>"
                data-bs-toggle="tooltip" 
                data-bs-placement="right" 
                title="QR Scanner"
            >
                <i class="fas fa-camera text-lg"></i>
                <span class="nav-text font-medium">QR Scanner</span>
            </a>

            <!-- Input Absensi Manual -->
            <a 
                href="<?php echo e(route('attendance.manual.index')); ?>"
                class="sidebar-menu-item <?php echo e(request()->routeIs('attendance.manual.*') ? 'active' : ''); ?>"
                data-bs-toggle="tooltip" 
                data-bs-placement="right" 
                title="Input Manual"
            >
                <i class="fas fa-clipboard-check text-lg"></i>
                <span class="nav-text font-medium">Input Manual</span>
            </a>

            <!-- Divider -->
            <div class="sidebar-divider"></div>

            <!-- Data Siswa -->
            <a 
                href="<?php echo e(route('attendance.students.index')); ?>"
                class="sidebar-menu-item <?php echo e(request()->routeIs('attendance.students.*') ? 'active' : ''); ?>"
                data-bs-toggle="tooltip" 
                data-bs-placement="right" 
                title="Data Siswa"
            >
                <i class="fas fa-users text-lg"></i>
                <span class="nav-text font-medium">Data Siswa</span>
            </a>

            <!-- Data Kelas -->
            <a 
                href="<?php echo e(route('attendance.classes.index')); ?>"
                class="sidebar-menu-item <?php echo e(request()->routeIs('attendance.classes.*') ? 'active' : ''); ?>"
                data-bs-toggle="tooltip" 
                data-bs-placement="right" 
                title="Data Kelas"
            >
                <i class="fas fa-school text-lg"></i>
                <span class="nav-text font-medium">Data Kelas</span>
            </a>


            <!-- Laporan -->
            <a 
                href="<?php echo e(route('attendance.reports.index')); ?>"
                class="sidebar-menu-item <?php echo e(request()->routeIs('attendance.reports.*') && !request()->routeIs('attendance.reports.semester*') ? 'active' : ''); ?>"
                data-bs-toggle="tooltip" 
                data-bs-placement="right" 
                title="Laporan"
            >
                <i class="fas fa-chart-bar text-lg"></i>
                <span class="nav-text font-medium">Laporan</span>
                
                
                <?php $todayAlphaCount = \App\Models\AttendanceRecord::today()->where('status','alpha')->count(); ?>
                <?php if($todayAlphaCount > 0): ?>
                    <span class="sidebar-badge"><?php echo e($todayAlphaCount); ?></span>
                <?php endif; ?>
            </a>

            <!-- Rekap Semester -->
            <a 
                href="<?php echo e(route('attendance.reports.semester')); ?>"
                class="sidebar-menu-item <?php echo e(request()->routeIs('attendance.reports.semester*') ? 'active' : ''); ?>"
                data-bs-toggle="tooltip" 
                data-bs-placement="right" 
                title="Rekap Semester"
            >
                <i class="fas fa-graduation-cap text-lg"></i>
                <span class="nav-text font-medium">Rekap Semester</span>
            </a>

            <!-- Izin Online -->
            <?php $pendingIzin = \App\Models\AttendanceIzin::where('status','pending')->count(); ?>
            <a 
                href="<?php echo e(route('attendance.izin.index')); ?>"
                class="sidebar-menu-item <?php echo e(request()->routeIs('attendance.izin*') ? 'active' : ''); ?>"
                data-bs-toggle="tooltip" 
                data-bs-placement="right" 
                title="Izin Online"
            >
                <i class="fas fa-file-medical text-lg"></i>
                <span class="nav-text font-medium">Izin Online</span>
                <?php if($pendingIzin > 0): ?>
                    <span class="sidebar-badge" id="notifBadgeNav"><?php echo e($pendingIzin); ?></span>
                <?php else: ?>
                    <span class="sidebar-badge" id="notifBadgeNav" style="display: none;">0</span>
                <?php endif; ?>
            </a>

            
            <?php if(auth()->user()?->isAdmin()): ?>
            <!-- Pengguna -->
            <a 
                href="<?php echo e(route('attendance.users.index')); ?>"
                class="sidebar-menu-item <?php echo e(request()->routeIs('attendance.users*') ? 'active' : ''); ?>"
                data-bs-toggle="tooltip" 
                data-bs-placement="right" 
                title="Pengguna"
            >
                <i class="fas fa-user-shield text-lg"></i>
                <span class="nav-text font-medium">Pengguna</span>
            </a>

            <!-- Tahun Ajaran -->
            <a 
                href="<?php echo e(route('attendance.tahun-ajaran.index')); ?>"
                class="sidebar-menu-item <?php echo e(request()->routeIs('attendance.tahun-ajaran*') ? 'active' : ''); ?>"
                data-bs-toggle="tooltip" 
                data-bs-placement="right" 
                title="Tahun Ajaran"
            >
                <i class="fas fa-calendar-alt text-lg"></i>
                <span class="nav-text font-medium">Tahun Ajaran</span>
            </a>
            <?php endif; ?>

            <!-- WhatsApp Collapsible Menu -->
            <?php $waOpen = request()->routeIs('whatsapp.*') || request()->routeIs('gateway.*'); ?>
            <div class="sidebar-submenu-group" data-submenu="whatsapp">
                <button 
                    type="button"
                    class="sidebar-menu-item sidebar-submenu-toggle w-full <?php echo e($waOpen ? 'submenu-active' : ''); ?>"
                    onclick="toggleSubmenu('whatsapp')"
                    data-bs-toggle="tooltip" 
                    data-bs-placement="right" 
                    title="WhatsApp"
                >
                    <i class="fab fa-whatsapp text-lg text-green-400"></i>
                    <span class="nav-text font-medium flex-1 text-left">WhatsApp</span>
                    <i class="fas fa-chevron-down nav-text text-xs submenu-arrow transition-transform duration-200 <?php echo e($waOpen ? 'rotate-180' : ''); ?>"></i>
                </button>
                
                <div class="sidebar-submenu <?php echo e($waOpen ? 'submenu-open' : ''); ?>" id="submenu-whatsapp">
                    <a href="<?php echo e(route('whatsapp.index')); ?>"
                       class="sidebar-submenu-item <?php echo e(request()->routeIs('whatsapp.index') ? 'active' : ''); ?>">
                        <i class="fas fa-tachometer-alt"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                    <a href="<?php echo e(route('whatsapp.send')); ?>"
                       class="sidebar-submenu-item <?php echo e(request()->routeIs('whatsapp.send') ? 'active' : ''); ?>">
                        <i class="fas fa-paper-plane"></i>
                        <span class="nav-text">Kirim Pesan</span>
                    </a>
                    <a href="<?php echo e(route('whatsapp.logs')); ?>"
                       class="sidebar-submenu-item <?php echo e(request()->routeIs('whatsapp.logs') ? 'active' : ''); ?>">
                        <i class="fas fa-history"></i>
                        <span class="nav-text">Log Pesan</span>
                    </a>
                    <a href="<?php echo e(route('whatsapp.templates')); ?>"
                       class="sidebar-submenu-item <?php echo e(request()->routeIs('whatsapp.templates*') ? 'active' : ''); ?>">
                        <i class="fas fa-file-alt"></i>
                        <span class="nav-text">Templates</span>
                    </a>
                    <a href="<?php echo e(route('whatsapp.broadcast')); ?>"
                       class="sidebar-submenu-item <?php echo e(request()->routeIs('whatsapp.broadcast') ? 'active' : ''); ?>">
                        <i class="fas fa-bullhorn"></i>
                        <span class="nav-text">Broadcast</span>
                    </a>
                    <a href="<?php echo e(route('gateway.index')); ?>"
                       class="sidebar-submenu-item <?php echo e(request()->routeIs('gateway.*') ? 'active' : ''); ?>">
                        <i class="fas fa-server"></i>
                        <span class="nav-text">Gateway</span>
                    </a>
                </div>
            </div>

            <!-- Divider -->
            <div class="sidebar-divider"></div>

            <!-- Settings Collapsible Menu -->
            <?php $settingsOpen = request()->routeIs('attendance.settings.*') || request()->routeIs('whatsapp.settings'); ?>
            <div class="sidebar-submenu-group" data-submenu="settings">
                <button 
                    type="button"
                    class="sidebar-menu-item sidebar-submenu-toggle w-full <?php echo e($settingsOpen ? 'submenu-active' : ''); ?>"
                    onclick="toggleSubmenu('settings')"
                    data-bs-toggle="tooltip" 
                    data-bs-placement="right" 
                    title="Settings"
                >
                    <i class="fas fa-cog text-lg"></i>
                    <span class="nav-text font-medium flex-1 text-left">Settings</span>
                    <i class="fas fa-chevron-down nav-text text-xs submenu-arrow transition-transform duration-200 <?php echo e($settingsOpen ? 'rotate-180' : ''); ?>"></i>
                </button>
                
                <div class="sidebar-submenu <?php echo e($settingsOpen ? 'submenu-open' : ''); ?>" id="submenu-settings">
                    <a href="<?php echo e(route('attendance.settings.index')); ?>"
                       class="sidebar-submenu-item <?php echo e(request()->routeIs('attendance.settings.*') ? 'active' : ''); ?>">
                        <i class="fas fa-sliders-h"></i>
                        <span class="nav-text">Setting Sistem</span>
                    </a>
                    <a href="<?php echo e(route('whatsapp.settings')); ?>"
                       class="sidebar-submenu-item <?php echo e(request()->routeIs('whatsapp.settings') ? 'active' : ''); ?>">
                        <i class="fab fa-whatsapp"></i>
                        <span class="nav-text">Setting WA</span>
                    </a>
                </div>
            </div>

        </nav>

        <!-- Bottom Section: Compact Icon-Only (Horizontal) -->
        <div class="bottom-section">
            <div class="bottom-icons-container">
                
                <!-- User Profile Icon -->
                <a 
                    href="<?php echo e(route('profile.edit')); ?>"
                    class="bottom-icon-btn"
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="<?php echo e(auth()->user()->name ?? 'User'); ?> - Profile"
                >
                    <div class="avatar-icon bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center text-white font-bold shadow-lg">
                        <?php echo e(strtoupper(substr(auth()->user()->name ?? 'U', 0, 1))); ?>

                    </div>
                    <span class="btn-text">Profile</span>
                </a>

                <!-- Dark Mode Toggle Icon -->
                <button 
                    id="darkModeToggle"
                    type="button"
                    class="bottom-icon-btn"
                    title="Toggle Dark Mode"
                >
                    <i id="darkModeIcon" class="fas fa-moon text-primary-200"></i>
                    <span class="btn-text" id="darkModeText">Dark Mode</span>
                </button>

                <!-- Logout Icon -->
                <form method="POST" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button 
                        type="submit"
                        class="bottom-icon-btn logout-btn"
                        data-bs-toggle="tooltip" 
                        data-bs-placement="top" 
                        title="Logout"
                    >
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="btn-text">Logout</span>
                    </button>
                </form>

            </div>
        </div>

    </div>
</aside>
<?php /**PATH C:\Users\DMCenter\Music\SPMB2\SPMB\absensi\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>