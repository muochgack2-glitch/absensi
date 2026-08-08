/**
 * Sidebar Management - Vanilla JS (SPMB Technology)
 * 
 * Features:
 * - No Alpine.js dependency
 * - Bootstrap tooltips
 * - Hover expand (CSS-based)
 * - No flash on page load
 * - localStorage persistence
 */

(function() {
    'use strict';
    
    // ============================================
    // PREVENT FLASH ON LOAD
    // ============================================
    // Read saved state immediately (before DOM ready)
    const sidebarOpen = localStorage.getItem('sidebarOpen') !== 'false';
    const sidebar = document.getElementById('adminSidebar');
    
    // Apply width before page render to prevent flash (desktop only - SPMB approach)
    if (sidebar && window.innerWidth >= 1024) {
        sidebar.style.width = sidebarOpen ? '16rem' : '5rem';
        if (!sidebarOpen) {
            sidebar.classList.add('collapsed');
            document.body.classList.add('sidebar-collapsed');
        }
    } else if (sidebar) {
        // Mobile: always full-width, use transform for show/hide
        sidebar.style.width = '16rem';
    }
    
    // ============================================
    // INITIALIZE AFTER DOM READY
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        initializeSidebar();
        initializeTooltips();
        initializeMobileMenu();
        initializeDarkMode();
        
        // Enable transitions after initial state is set (SPMB approach)
        const sb = document.getElementById('adminSidebar');
        const mainContent = document.getElementById('mainContent');
        setTimeout(function() {
            if (sb) sb.classList.add('transitions-enabled');
            if (mainContent) mainContent.classList.add('transitions-enabled');
        }, 50);
    });
    
    // ============================================
    // SIDEBAR TOGGLE FUNCTIONALITY
    // ============================================
    function initializeSidebar() {
        const sidebar = document.getElementById('adminSidebar');
        const toggleBtn = document.getElementById('sidebarToggle');
        
        if (!sidebar || !toggleBtn) {
            console.warn('Sidebar or toggle button not found');
            return;
        }
        
        // Apply initial state to body class
        const initialState = sidebar.classList.contains('collapsed');
        if (initialState) {
            document.body.classList.add('sidebar-collapsed');
        }
        
        // Toggle button click handler
        toggleBtn.addEventListener('click', function() {
            const isOpen = !sidebar.classList.contains('collapsed');
            
            if (isOpen) {
                // Collapse sidebar
                sidebar.classList.add('collapsed');
                sidebar.style.width = '5rem';
                document.body.classList.add('sidebar-collapsed');
                localStorage.setItem('sidebarOpen', 'false');
            } else {
                // Expand sidebar
                sidebar.classList.remove('collapsed');
                sidebar.style.width = '16rem';
                document.body.classList.remove('sidebar-collapsed');
                localStorage.setItem('sidebarOpen', 'true');
            }
            
            // Dispatch event for other components
            window.dispatchEvent(new CustomEvent('sidebar-toggled', { 
                detail: { isOpen: !isOpen } 
            }));
            
            // Reinitialize tooltips after animation completes
            setTimeout(initializeTooltips, 350);
        });
    }

    // ============================================
    // BOOTSTRAP TOOLTIP INITIALIZATION
    // ============================================
    function initializeTooltips() {
        const sidebar = document.getElementById('adminSidebar');
        if (!sidebar) return;
        
        // Check if Bootstrap is available
        if (typeof bootstrap === 'undefined') {
            console.warn('Bootstrap is not loaded. Tooltips will not work.');
            return;
        }
        
        const isCollapsed = sidebar.classList.contains('collapsed');
        
        // Destroy existing tooltips first
        const existingTooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        existingTooltips.forEach(el => {
            const tooltip = bootstrap.Tooltip.getInstance(el);
            if (tooltip) {
                tooltip.dispose();
            }
        });
        
        // Initialize tooltips ONLY when sidebar is collapsed
        if (isCollapsed) {
            const tooltipTriggerList = [].slice.call(
                document.querySelectorAll('[data-bs-toggle="tooltip"]')
            );
            
            tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl, {
                    placement: 'right',
                    trigger: 'hover focus',
                    delay: { show: 300, hide: 100 }
                });
            });
        }
    }

    
    // ============================================
    // MOBILE MENU FUNCTIONALITY (SPMB Style)
    // ============================================
    function initializeMobileMenu() {
        const overlay = document.getElementById('sidebarOverlay');
        const sidebar = document.getElementById('adminSidebar');
        const menuLinks = sidebar?.querySelectorAll('.sidebar-menu-item');
        
        if (!sidebar) return;
        
        // Overlay click to close
        if (overlay) {
            overlay.addEventListener('click', closeMobileMenu);
        }
        
        // SPMB approach: Do NOT close sidebar on menu link clicks.
        // In multi-page apps, page reloads naturally.
        // Closing the sidebar immediately cancels navigation on mobile WebKit.
        
        // Close mobile menu on window resize to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                closeMobileMenu();
            }
        });
    }
    
    function openMobileMenu() {
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        
        if (sidebar) sidebar.classList.add('mobile-show');
        if (overlay) overlay.classList.add('show');
        
        // Hide hamburger button when menu is open
        document.body.classList.add('sidebar-menu-open');
    }
    
    function closeMobileMenu() {
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        
        if (sidebar) sidebar.classList.remove('mobile-show');
        if (overlay) overlay.classList.remove('show');
        
        // Show hamburger button when menu is closed
        document.body.classList.remove('sidebar-menu-open');
    }
    
    // Expose to window for navbar button access
    window.toggleMobileMenu = function() {
        const sidebar = document.getElementById('adminSidebar');
        const isOpen = sidebar?.classList.contains('mobile-show');
        
        if (isOpen) {
            closeMobileMenu();
        } else {
            openMobileMenu();
        }
    };
    
    // Expose closeMobileMenu globally (for close button in sidebar)
    window.closeMobileMenu = closeMobileMenu;

    // ============================================
    // SWIPE GESTURE (Mobile - Swipe Left to Close)
    // ============================================
    (function initSwipeGesture() {
        let touchStartX = 0;
        let touchStartY = 0;
        let touchEndX = 0;
        let isSwiping = false;

        const sidebar = document.getElementById('adminSidebar');
        if (!sidebar) return;

        sidebar.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
            touchStartY = e.changedTouches[0].screenY;
            isSwiping = true;
        }, { passive: true });

        sidebar.addEventListener('touchmove', function(e) {
            if (!isSwiping) return;
            touchEndX = e.changedTouches[0].screenX;
        }, { passive: true });

        sidebar.addEventListener('touchend', function(e) {
            if (!isSwiping) return;
            isSwiping = false;
            
            const deltaX = touchEndX - touchStartX;
            const deltaY = Math.abs(e.changedTouches[0].screenY - touchStartY);
            
            // Swipe left (negative deltaX) with enough distance and mostly horizontal
            if (deltaX < -80 && deltaY < 100 && window.innerWidth < 1024) {
                closeMobileMenu();
            }
        }, { passive: true });

        // Swipe right from left edge to OPEN sidebar
        document.addEventListener('touchstart', function(e) {
            if (window.innerWidth >= 1024) return;
            const touch = e.changedTouches[0];
            if (touch.screenX < 25) { // Within 25px of left edge
                touchStartX = touch.screenX;
                isSwiping = true;
            }
        }, { passive: true });

        document.addEventListener('touchend', function(e) {
            if (!isSwiping || window.innerWidth >= 1024) return;
            isSwiping = false;
            const deltaX = e.changedTouches[0].screenX - touchStartX;
            
            // Swipe right from edge
            if (deltaX > 80) {
                const sidebar = document.getElementById('adminSidebar');
                if (sidebar && !sidebar.classList.contains('mobile-show')) {
                    openMobileMenu();
                }
            }
        }, { passive: true });
    })();

    // ============================================
    // ESCAPE KEY TO CLOSE (Mobile)
    // ============================================
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const sidebar = document.getElementById('adminSidebar');
            if (sidebar && sidebar.classList.contains('mobile-show')) {
                closeMobileMenu();
            }
        }
    });

    // ============================================
    // SUBMENU TOGGLE (Collapsible Accordion)
    // ============================================
    window.toggleSubmenu = function(name) {
        const submenu = document.getElementById('submenu-' + name);
        const group = document.querySelector('[data-submenu="' + name + '"]');
        const arrow = group?.querySelector('.submenu-arrow');
        
        if (!submenu) return;
        
        const isOpen = submenu.classList.contains('submenu-open');
        
        if (isOpen) {
            submenu.classList.remove('submenu-open');
            arrow?.classList.remove('rotate-180');
            group?.querySelector('.sidebar-submenu-toggle')?.classList.remove('submenu-active');
        } else {
            submenu.classList.add('submenu-open');
            arrow?.classList.add('rotate-180');
            group?.querySelector('.sidebar-submenu-toggle')?.classList.add('submenu-active');
        }
    };

    
    // ============================================
    // DARK MODE FUNCTIONALITY
    // ============================================
    function initializeDarkMode() {
        const darkModeBtn = document.getElementById('darkModeToggle');
        if (!darkModeBtn) return;
        
        // Read saved dark mode state
        const isDark = localStorage.getItem('darkMode') === 'true';
        
        // Apply dark mode class
        if (isDark) {
            document.documentElement.classList.add('dark');
        }
        
        // Update all dark mode icons on init
        updateDarkModeIcons(isDark);
        
        // Toggle button click (sidebar)
        darkModeBtn.addEventListener('click', function() {
            const isCurrentlyDark = document.documentElement.classList.contains('dark');
            
            if (isCurrentlyDark) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('darkMode', 'false');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('darkMode', 'true');
            }
            
            updateDarkModeIcons(!isCurrentlyDark);
        });
    }
    
    // Update dark mode icons across sidebar + navbar
    function updateDarkModeIcons(isDark) {
        // Sidebar icon
        const sidebarIcon = document.querySelector('#darkModeToggle i');
        if (sidebarIcon) {
            sidebarIcon.className = isDark 
                ? 'fas fa-sun text-amber-300' 
                : 'fas fa-moon text-primary-200';
        }
        
        // Sidebar text
        const sidebarText = document.querySelector('#darkModeToggle .btn-text');
        if (sidebarText) {
            sidebarText.textContent = isDark ? 'Light Mode' : 'Dark Mode';
        }
        
        // Navbar icons (separate moon/sun elements)
        const navMoon = document.getElementById('dark-icon-moon');
        const navSun = document.getElementById('dark-icon-sun');
        if (navMoon && navSun) {
            navMoon.classList.toggle('hidden', isDark);
            navSun.classList.toggle('hidden', !isDark);
        }
    }
    
    // Expose for navbar dark mode button
    window.toggleDarkMode = function() {
        const isCurrentlyDark = document.documentElement.classList.contains('dark');
        
        if (isCurrentlyDark) {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('darkMode', 'false');
        } else {
            document.documentElement.classList.add('dark');
            localStorage.setItem('darkMode', 'true');
        }
        
        updateDarkModeIcons(!isCurrentlyDark);
    };
    
    // ============================================
    // BADGE COUNT LOADING
    // ============================================
    function loadBadgeCounts() {
        fetch('/api/attendance/today-stats')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const badge = document.querySelector('.sidebar-badge');
                    if (badge && data.absent > 0) {
                        badge.textContent = data.absent;
                        badge.style.display = 'flex';
                    }
                }
            })
            .catch(err => {
                console.log('Badge count fetch error:', err);
            });
    }
    
    // Load badge counts on init
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadBadgeCounts);
    } else {
        loadBadgeCounts();
    }

    // ============================================
    // NOTIFICATION BADGE (Nav Menu Item)
    // ============================================
    function loadNotificationBadge() {
        fetch('/api/attendance/notifications')
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (!data.success) return;
                var badge = document.getElementById('notifBadgeNav');
                if (badge) {
                    if (data.total > 0) {
                        badge.textContent = data.total;
                        badge.style.display = 'flex';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            })
            .catch(function() {});
    }

    // Load on init + auto-refresh every 60s
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            loadNotificationBadge();
            setInterval(loadNotificationBadge, 60000);
        });
    } else {
        loadNotificationBadge();
        setInterval(loadNotificationBadge, 60000);
    }
    
})();
