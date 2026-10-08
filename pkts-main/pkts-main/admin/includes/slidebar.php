<?php
// Admin Sidebar Navigation
?>
<aside class="admin-sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <img src="../logos/PKTS LOGO.png" alt="PKTS Karate" class="sidebar-logo">
        </div>
    </div>
    <nav class="sidebar-nav">
        <ul class="sidebar-menu">
            <li class="sidebar-item">
                <a href="dashboard.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="students.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) === 'students.php' ? 'active' : ''; ?>">
                    <i class="fas fa-user-graduate"></i>
                    <span>Student & Payments</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="enrollments.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) === 'enrollments.php' ? 'active' : ''; ?>">
                    <i class="fas fa-id-card"></i>
                    <span>Online Enrollments</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="progress.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) === 'progress.php' ? 'active' : ''; ?>">
                    <i class="fas fa-medal"></i>
                    <span>Sensei Belt Review</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="announcements.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) === 'announcements.php' ? 'active' : ''; ?>">
                    <i class="fas fa-bullhorn"></i>
                    <span>Announcements</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="messages.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) === 'messages.php' ? 'active' : ''; ?>">
                    <i class="fas fa-comments"></i>
                    <span>Messages</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="site_settings.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) === 'site_settings.php' ? 'active' : ''; ?>">
                    <i class="fas fa-edit"></i>
                    <span>Website Details</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="admins.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) === 'admins.php' ? 'active' : ''; ?>">
                    <i class="fas fa-user-shield"></i>
                    <span>Admin Team</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="security.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) === 'security.php' ? 'active' : ''; ?>">
                    <i class="fas fa-lock"></i>
                    <span>Admin Security</span>
                </a>
            </li>
        </ul>
    </nav>
    <div class="sidebar-footer">
        <a href="login.php" class="sidebar-logout" title="Logout">
            <i class="fas fa-sign-out-alt"></i>
        </a>
    </div>
</aside>

<style>
.sidebar-nav {
    padding-top: 16px;
}

.sidebar-menu {
    list-style: none;
    padding: 16px 0;
    margin: 0;
}

.sidebar-item {
    margin-bottom: 4px;
}

.sidebar-link {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 14px 24px;
    color: #a0a0a0;
    text-decoration: none;
    font-size: 0.95rem;
    font-weight: 500;
    transition: all 0.2s ease;
    border-radius: 0 30px 30px 0;
    position: relative;
}

.sidebar-link:hover {
    color: var(--red);
    background: rgba(255, 255, 255, 0.05);
}

.sidebar-link.active {
    color: var(--red);
    background: rgba(220, 38, 38, 0.15);
}

.sidebar-link.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: var(--red);
    border-radius: 0 4px 4px 0;
}

.sidebar-link i {
    width: 24px;
    text-align: center;
    font-size: 1.1rem;
}

.sidebar-footer {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 16px 20px;
    border-top: 1px solid var(--border);
    background: #1a1a1a;
    text-align: center;
}

.sidebar-footer i {
    font-size: 1rem;
}

.sidebar-logout {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #888;
    text-decoration: none;
    font-size: 1rem;
    cursor: pointer;
    padding: 10px;
    border-radius: 50px;
    transition: background 0.2s, color 0.2s;
}

.sidebar-logout:hover {
    background: rgba(255, 255, 255, 0.1);
    color: var(--red);
}

.admin-sidebar.collapsed .sidebar-link span {
    display: none;
}

.admin-sidebar.collapsed .sidebar-link {
    gap: 16px;
    padding: 14px;
    justify-content: center;
}

.sidebar-logo {
    max-height: 40px;
    max-width: 180px;
    object-fit: contain;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.admin-sidebar');
    const content = document.querySelector('.admin-content');
    
    function updateToggleIcon() {
        if (toggleBtn) {
            if (sidebar.classList.contains('collapsed')) {
                toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
                toggleBtn.classList.remove('sidebar-open');
                toggleBtn.classList.add('sidebar-collapsed');
                document.body.classList.add('admin-collapsed');
            } else {
                toggleBtn.innerHTML = '<i class="fas fa-times"></i>';
                toggleBtn.classList.remove('sidebar-collapsed');
                toggleBtn.classList.add('sidebar-open');
                document.body.classList.remove('admin-collapsed');
            }
        }
    }
    
    function updateContent() {
        if (content) {
            content.classList.toggle('has-sidebar', !sidebar.classList.contains('collapsed'));
            content.classList.toggle('sidebar-collapsed', sidebar.classList.contains('collapsed'));
        }
    }
    
    function init() {
        sidebar.classList.remove('collapsed');
        document.body.classList.remove('admin-collapsed');
        updateContent();
        updateToggleIcon();
    }
    
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            sidebar.classList.toggle('collapsed');
            updateContent();
            updateToggleIcon();
        });
    }
    
    init();
    
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(init, 250);
    });
});
</script>