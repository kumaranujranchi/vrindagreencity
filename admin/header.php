<header class="header">
    <div class="header-content">
        <div class="header-left">
            <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>
            <a href="index.php" class="logo">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    style="color: var(--primary);">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                <h1>Vrinda Green City</h1>
            </a>
        </div>

        <div class="header-actions">
            <div class="user-profile">
                <div class="user-avatar">
                   <?php echo strtoupper(substr($_SESSION['admin_username'], 0, 1)); ?>
                </div>
                <span class="user-name"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    style="color: var(--text-secondary);">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </div>
        </div>
    </div>
</header>

<div class="overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <nav class="nav" role="navigation" aria-label="Admin navigation">
        <a href="index.php"
            class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" title="Dashboard" aria-label="Dashboard">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
            </svg>
            <span class="nav-label">Dashboard</span>
        </a>
        <a href="contact-leads.php"
            class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contact-leads.php' ? 'active' : ''; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                <polyline points="22,6 12,13 2,6"></polyline>
            </svg>
            <span class="nav-label">Leads</span>
        </a>
        <a href="property-inquiries.php"
            class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'property-inquiries.php' ? 'active' : ''; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span class="nav-label">Properties</span>
        </a>
        <a href="subscribers.php"
            class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'subscribers.php' ? 'active' : ''; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                <polyline points="22,6 12,13 2,6"></polyline>
            </svg>
            <span class="nav-label">Subscribers</span>
        </a>
        <a href="push-notifications.php"
            class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'push-notifications.php' || basename($_SERVER['PHP_SELF']) == 'push-subscribers.php' ? 'active' : ''; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
            <span class="nav-label">Push Notifications</span>
        </a>
        <a href="view-chatbot-logs.php"
            class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'view-chatbot-logs.php' ? 'active' : ''; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
            </svg>
            <span class="nav-label">Chatbot Logs</span>
        </a>
        <a href="logout.php" class="nav-link logout">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
            <span class="nav-label">Logout</span>
        </a>
    </nav>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebarToggle');
    const overlay = document.getElementById('sidebarOverlay');
    const body = document.body;

    // Sidebar state persistence
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isCollapsed && window.innerWidth > 1024) {
        sidebar.classList.add('collapsed');
        body.classList.add('sidebar-collapsed');
    }

    function toggleSidebar() {
        if (window.innerWidth <= 1024) {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
        } else {
            sidebar.classList.toggle('collapsed');
            body.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        }
    }

    toggle.addEventListener('click', toggleSidebar);
    overlay.addEventListener('click', toggleSidebar);

    // Standardized Skeleton Loader Initialization
    window.showSkeletons = function(tableId, columns) {
        try {
            const table = document.getElementById(tableId);
            if (!table) return;
            const tbody = table.querySelector('tbody');
            if (!tbody) return;
            
            // Don't show skeleton if there's no data or it's empty state
            if (tbody.querySelector('.text-center')) return;

            const originalContent = tbody.innerHTML;
            let skeletons = '';
            for(let i = 0; i < 5; i++) {
                skeletons += '<tr>';
                for(let j = 0; j < columns; j++) {
                    skeletons += `<td><span class="skeleton" style="height: 14px; width: ${Math.floor(Math.random() * 40) + 60}%; margin: 8px 0;"></span></td>`;
                }
                skeletons += '</tr>';
            }
            tbody.innerHTML = skeletons;

            setTimeout(() => {
                tbody.innerHTML = originalContent;
                // Add fade-in effect to the new rows
                tbody.querySelectorAll('tr').forEach(tr => {
                    tr.style.opacity = '0';
                    tr.style.transition = 'opacity 0.3s ease';
                    requestAnimationFrame(() => tr.style.opacity = '1');
                });
            }, 500);
        } catch (e) { console.error('Skeleton initialization failed:', e); }
    };
});
</script>