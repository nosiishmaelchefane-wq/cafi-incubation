<div class="sidebar-custom d-flex flex-column flex-shrink-0 p-3" id="sidebar">

    <!-- Toggle Button -->
    <button class="btn btn-sm border-0 shadow-sm mb-3 align-self-end" id="sidebarToggle" title="Toggle Sidebar" style="background-color: #f0f4f9; color: #142552;">
        <i class="bi bi-chevron-left fs-5" id="toggleIcon" style="color: #142552;"></i>
    </button>

    <!-- Header -->
    <a href="#" class="d-flex align-items-center mb-3 text-decoration-none sidebar-brand">
        <img src="{{ asset('/images/logo.png') }}" 
             alt="LEHSFF" 
             class="img-fluid" 
             style="max-height: 45px; width: auto; object-fit: contain;">
    </a>

    <hr class="my-2" style="border-color: #dee2e6; opacity: 0.5;">

    <!-- Navigation Menu - Scrollable Area -->
    <div class="nav-scrollable-container" style="flex: 1 1 auto; min-height: 0; position: relative;">
        <ul class="nav nav-pills flex-column gap-1" id="sidebarNav" style="margin: 0; padding: 0; list-style: none;">

            <!-- Dashboard - Visible to all authenticated users -->
            @can('view Dashboard')
            <li class="nav-item">
                <a href="{{ route('dashboard.index') }}" 
                class="nav-link d-flex align-items-center py-2 px-3 rounded-4 text-dark hover-nav {{ request()->routeIs('dashboard.*') ? 'active-nav' : '' }}" 
                data-tooltip="Dashboard"
                style="color: #333; {{ request()->routeIs('dashboard.*') ? 'background-color: #05923b; color: white;' : '' }}">
                    
                    <i class="bi bi-speedometer2 fs-5 nav-icon flex-shrink-0"
                    style="color: {{ request()->routeIs('dashboard.*') ? 'white' : '#142552' }};"></i>
                    
                    <span class="fw-medium sidebar-text ms-3"
                        style="color: {{ request()->routeIs('dashboard.*') ? 'white' : '#142552' }};">
                        Dashboard
                    </span>
                </a>
            </li>
            @endcan

            <!-- PRE-INCUBATION -->
            @canany(['view Calls for Applications', 'view Applications', 'view Screening & Eligibility', 'view Evaluation & Scoring'])
            <li class="nav-item mt-2">
                <small class="section-label px-3 sidebar-text" style="color: #142552; font-weight: 700;">Pre-Incubation</small>
                <hr class="section-divider my-1" style="display:none;">
            </li>
            @endcanany

            <!-- Calls for Applications - Visible to Entrepreneur and Super Admin -->
            @can('view Calls for Applications')
            <li class="nav-item">
                <a href="{{ route('calls.index') }}" 
                class="nav-link d-flex align-items-center py-2 px-3 rounded-4 text-dark hover-nav {{ request()->routeIs('calls.*') ? 'active-nav' : '' }}" 
                data-tooltip="Calls for Applications"
                style="color: #333; {{ request()->routeIs('calls.*') ? 'background-color: #05923b; color: white;' : '' }}">
                    
                    <i class="bi bi-megaphone-fill fs-5 nav-icon flex-shrink-0"
                    style="color: {{ request()->routeIs('calls.*') ? 'white' : '#142552' }};"></i>
                    
                    <span class="fw-medium sidebar-text ms-3"
                        style="color: {{ request()->routeIs('calls.*') ? 'white' : '#142552' }};">
                        Calls for Applications
                    </span>
                </a>
            </li>
            @endcan
          

            <!-- Screening & Eligibility - Super Admin and Procurement Officer only -->
            @can('view Screening & Eligibility')
            @if(auth()->user()->hasAnyRole(['Super Administrator', 'Procurement Officer']))
            <li class="nav-item">
                <a href="{{ route('screening.index') }}" 
                class="nav-link d-flex align-items-center py-2 px-3 rounded-4 text-dark hover-nav {{ request()->routeIs('screening.*') ? 'active-nav' : '' }}" 
                data-tooltip="Screening &amp; Eligibility"
                style="color: #333; {{ request()->routeIs('screening.*') ? 'background-color: #05923b; color: white;' : '' }}">
                    
                    <i class="bi bi-funnel-fill fs-5 nav-icon flex-shrink-0"
                    style="color: {{ request()->routeIs('screening.*') ? 'white' : '#142552' }};"></i>
                    
                    <span class="fw-medium sidebar-text ms-3"
                        style="color: {{ request()->routeIs('screening.*') ? 'white' : '#142552' }};">
                        Screening &amp; Eligibility
                    </span>
                    
                    <span class="badge ms-2 sidebar-badge flex-shrink-0" 
                        style="background-color: {{ request()->routeIs('screening.*') ? 'white' : '#142552' }}; 
                                color: {{ request()->routeIs('screening.*') ? '#05923b' : 'white' }};">
                        Restricted
                    </span>
                </a>
            </li>
            @endif
            @endcan

            <!-- Evaluation & Scoring - Admin only -->
            @can('view Evaluation & Scoring')
            <li class="nav-item">
                <a href="{{ route('evaluation.index') }}" 
                class="nav-link d-flex align-items-center py-2 px-3 rounded-4 text-dark hover-nav {{ request()->routeIs('evaluation.*') ? 'active-nav' : '' }}" 
                data-tooltip="Evaluation &amp; Scoring"
                style="color: #333; {{ request()->routeIs('evaluation.*') ? 'background-color: #05923b; color: white;' : '' }}">
                    
                    <i class="bi bi-clipboard2-data-fill fs-5 nav-icon flex-shrink-0"
                    style="color: {{ request()->routeIs('evaluation.*') ? 'white' : '#142552' }};"></i>
                    
                    <span class="fw-medium sidebar-text ms-3"
                        style="color: {{ request()->routeIs('evaluation.*') ? 'white' : '#142552' }};">
                        Evaluation &amp; Scoring
                    </span>
                </a>
            </li>
            @endcan

            <!-- Shortlisting & Pitches - Placeholder (no route yet) -->
            @can('view Cohort Management')
            <li class="nav-item">
                <a href="#" class="nav-link d-flex align-items-center py-2 px-3 rounded-4 text-dark hover-nav" data-tooltip="Shortlisting &amp; Pitches" style="color: #333;">
                    <i class="bi bi-list-check fs-5 nav-icon flex-shrink-0" style="color: #142552;"></i>
                    <span class="fw-medium sidebar-text ms-3" style="color: #142552;">Shortlisting &amp; Pitches</span>
                </a>
            </li>
            @endcan

            <!-- ACTIVE INCUBATION - Admin only (placeholders) -->
            @canany(['view Enterprise Reports', 'view ESO Reports'])
            <li class="nav-item mt-2">
                <small class="section-label px-3 sidebar-text" style="color: #142552; font-weight: 700;">Active Incubation</small>
                <hr class="section-divider my-1" style="display:none;">
            </li>
            @endcanany

            @can('view Enterprise Reports')
            <li class="nav-item">
                <a href="#" class="nav-link d-flex align-items-center py-2 px-3 rounded-4 text-dark hover-nav" data-tooltip="Enterprises" style="color: #333;">
                    <i class="bi bi-shop-window fs-5 nav-icon flex-shrink-0" style="color: #142552;"></i>
                    <span class="fw-medium sidebar-text ms-3" style="color: #142552;">Enterprises</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="#" class="nav-link d-flex align-items-center py-2 px-3 rounded-4 text-dark hover-nav" data-tooltip="Engagement Logs" style="color: #333;">
                    <i class="bi bi-journal-text fs-5 nav-icon flex-shrink-0" style="color: #142552;"></i>
                    <span class="fw-medium sidebar-text ms-3" style="color: #142552;">Engagement Logs</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="#" class="nav-link d-flex align-items-center py-2 px-3 rounded-4 text-dark hover-nav" data-tooltip="Enterprise Reports" style="color: #333;">
                    <i class="bi bi-bar-chart-fill fs-5 nav-icon flex-shrink-0" style="color: #142552;"></i>
                    <span class="fw-medium sidebar-text ms-3" style="color: #142552;">Enterprise Reports</span>
                </a>
            </li>
            @endcan

            @can('view ESO Reports')
            <li class="nav-item">
                <a href="#" class="nav-link d-flex align-items-center py-2 px-3 rounded-4 text-dark hover-nav" data-tooltip="ESO Reports" style="color: #333;">
                    <i class="bi bi-building fs-5 nav-icon flex-shrink-0" style="color: #142552;"></i>
                    <span class="fw-medium sidebar-text ms-3" style="color: #142552;">ESO Reports</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="#" class="nav-link d-flex align-items-center py-2 px-3 rounded-4 text-dark hover-nav" data-tooltip="Report Review" style="color: #333;">
                    <i class="bi bi-patch-check-fill fs-5 nav-icon flex-shrink-0" style="color: #142552;"></i>
                    <span class="fw-medium sidebar-text ms-3 me-auto" style="color: #142552;">Report Review</span>
                    <span class="badge ms-2 sidebar-badge flex-shrink-0" style="background-color: #142552; color: white;">Admin</span>
                </a>
            </li>
            @endcan

            <!-- POST INCUBATION - Admin only -->
            @can('view Cohort Management')
            <li class="nav-item mt-2">
                <small class="section-label px-3 sidebar-text" style="color: #142552; font-weight: 700;">Post-Incubation</small>
                <hr class="section-divider my-1" style="display:none;">
            </li>

            <!-- Cohort Management -->
            <a href="{{ route('cohorts.index') }}" 
                class="nav-link d-flex align-items-center py-2 px-3 rounded-4 text-dark hover-nav {{ request()->routeIs('cohorts.*') ? 'active-nav' : '' }}" 
                data-tooltip="Cohort Management"
                style="color: #333; {{ request()->routeIs('cohorts.*') ? 'background-color: #05923b; color: white;' : '' }}">
                    
                    <i class="bi bi-people-fill fs-5 nav-icon flex-shrink-0"
                    style="color: {{ request()->routeIs('cohorts.*') ? 'white' : '#142552' }};"></i>
                    
                    <span class="fw-medium sidebar-text ms-3 me-auto"
                        style="color: {{ request()->routeIs('cohorts.*') ? 'white' : '#142552' }};">
                        Manage Cohort
                    </span>
                    
                    <span class="badge ms-2 sidebar-badge flex-shrink-0" 
                        style="background-color: {{ request()->routeIs('cohorts.*') ? 'white' : '#142552' }}; 
                                color: {{ request()->routeIs('cohorts.*') ? '#05923b' : 'white' }};">
                        Admin
                    </span>
            </a>

            <!-- Graduation & Outcomes -->
            <li class="nav-item">
                <a href="#" class="nav-link d-flex align-items-center py-2 px-3 rounded-4 text-dark hover-nav" data-tooltip="Graduation &amp; Outcomes" style="color: #333;">
                    <i class="bi bi-mortarboard-fill fs-5 nav-icon flex-shrink-0" style="color: #142552;"></i>
                    <span class="fw-medium sidebar-text ms-3" style="color: #142552;">Graduation &amp; Outcomes</span>
                </a>
            </li>

            <!-- Analytics & Reporting -->
            <li class="nav-item">
                <a href="#" class="nav-link d-flex align-items-center py-2 px-3 rounded-4 text-dark hover-nav" data-tooltip="Analytics &amp; Reporting" style="color: #333;">
                    <i class="bi bi-graph-up-arrow fs-5 nav-icon flex-shrink-0" style="color: #142552;"></i>
                    <span class="fw-medium sidebar-text ms-3 me-auto" style="color: #142552;">Analytics &amp; Reporting</span>
                    <span class="badge ms-2 sidebar-badge flex-shrink-0" style="background-color: #142552; color: white;">Admin</span>
                </a>
            </li>
            @endcan

            <!-- USER MANAGEMENT - Admin only -->
            @can('view User Management')
            <li class="nav-item mt-2">
                <small class="section-label px-3 sidebar-text" style="color: #142552; font-weight: 700;">User Management</small>
                <hr class="section-divider my-1" style="display:none;">
            </li>

            <a href="{{ route('users.index') }}"
            class="nav-link d-flex align-items-center py-2 px-3 rounded-4 {{ request()->routeIs('users.*') ? 'active-nav' : 'hover-nav' }}"
            data-tooltip="All Users">

                <i class="bi bi-person-lines-fill fs-5 nav-icon flex-shrink-0"
                style="color: {{ request()->routeIs('users.*') ? 'white' : '#142552' }}"></i>

                <span class="fw-medium sidebar-text ms-3">All Users</span>
            </a>

            <li class="nav-item">
                <a href="{{ route('roles.index') }}" 
                class="nav-link d-flex align-items-center py-2 px-3 rounded-4 text-dark hover-nav {{ request()->routeIs('roles.*') ? 'active-nav' : '' }}" 
                data-tooltip="Roles &amp; Permissions" 
                style="color: #333; {{ request()->routeIs('roles.*') ? 'background-color: #05923b; color: white;' : '' }}">
                    
                    <i class="bi bi-shield-lock-fill fs-5 nav-icon flex-shrink-0" 
                    style="color: {{ request()->routeIs('roles.*') ? 'white' : '#142552' }};"></i>
                    
                    <span class="fw-medium sidebar-text ms-3 me-auto" 
                        style="color: {{ request()->routeIs('roles.*') ? 'white' : '#142552' }}">
                        Roles &amp; Permissions
                    </span>
                    
                    <span class="badge ms-2 sidebar-badge flex-shrink-0" 
                        style="background-color: {{ request()->routeIs('roles.*') ? 'white' : '#142552' }}; 
                                color: {{ request()->routeIs('roles.*') ? '#05923b' : 'white' }}">
                        Admin
                    </span>
                </a>
            </li>
            @endcan

        </ul>
    </div>

    <!-- Bottom section - fixed at the bottom -->
    <div class="mt-auto pt-2" style="flex-shrink: 0;">
        <hr class="my-2" style="border-color: #dee2e6; opacity: 0.5;">

        <!-- User Profile Section -->
        <div class="d-flex align-items-center p-2 rounded-3 user-profile-toggle" style="background-color: #f0f4f9;">
            <div class="position-relative flex-shrink-0">
                <div class="rounded-circle border border-3 d-flex align-items-center justify-content-center"
                    style="width:40px;height:40px; border-color: #142552 !important;">
                    
                    <i class="bi bi-person-fill" style="font-size:20px; color:#142552;"></i>
                </div>

                <span class="position-absolute bottom-0 end-0 rounded-circle"
                    style="width:12px;height:12px; background-color: #05923b; border: 2px solid white;"></span>
            </div>
            <div class="d-flex flex-column ms-3 sidebar-text overflow-hidden">
                <strong class="text-truncate" style="color: #142552;">{{ Auth::user()->username ?? 'User' }}</strong>
                <small class="text-muted text-capitalize text-truncate">{{ Auth::user()->roles->first()->name ?? 'User' }}</small>
            </div>
        </div>

        <!-- Logout -->
        <div class="mt-2">
            <a href="#"
               class="btn btn-sm w-100 d-flex align-items-center justify-content-center"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               style="background-color: transparent; color: #142552; border: 1px solid #142552;">
                <i class="bi bi-box-arrow-right fs-5 flex-shrink-0" style="color: #142552;"></i>
                <span class="ms-2 sidebar-text">Logout</span>
            </a>
        </div>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>

</div>

<style>
/* ── Base with official colors ── */
.sidebar-custom {
    width: 280px;
    height: 100vh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-right: 1px solid #dee2e6;
    box-shadow: 2px 0 10px rgba(20, 37, 82, 0.08);
    transition: width 0.3s ease;
}

/* Scrollable container */
.nav-scrollable-container {
    overflow-y: auto;
    overflow-x: hidden;
    scrollbar-width: thin;
    scrollbar-color: #142552 #f0f4f9;
    margin-right: -4px;
    padding-right: 4px;
    width: calc(100% + 4px);
}

/* Webkit scrollbar styles */
.nav-scrollable-container::-webkit-scrollbar {
    width: 4px;
}

.nav-scrollable-container::-webkit-scrollbar-track {
    background: #f0f4f9;
}

.nav-scrollable-container::-webkit-scrollbar-thumb {
    background: #142552;
    border-radius: 4px;
}

.nav-scrollable-container::-webkit-scrollbar-thumb:hover {
    background: #05923b;
}

/* Nav link hover effects - using light green */
.hover-nav {
    transition: all 0.3s ease;
    background-color: transparent;
}

.hover-nav:hover {
    background-color: #e6f0e9 !important;
    transform: translateX(4px);
}

.hover-nav:hover i {
    color: #05923b !important;
}

.hover-nav:hover span:not(.badge) {
    color: #05923b !important;
}

/* Active state - using light green */
.active-nav {
    background-color: #05923b !important;
    color: white !important;
    box-shadow: 0 4px 8px rgba(5, 146, 59, 0.25) !important;
}

.active-nav i {
    color: white !important;
}

/* Section labels */
.section-label {
    font-size: 0.68rem;
    letter-spacing: 0.09em;
    text-transform: uppercase;
    display: block;
    margin-bottom: 2px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* ── Collapsed state adjustments ── */
.sidebar-custom.collapsed {
    width: 72px;
}

.sidebar-custom.collapsed .sidebar-text,
.sidebar-custom.collapsed .sidebar-badge {
    display: none !important;
}

.sidebar-custom.collapsed .section-divider {
    display: block !important;
}

.sidebar-custom.collapsed .nav-scrollable-container {
    width: 100%;
    margin-right: 0;
    padding-right: 0;
}

.sidebar-custom.collapsed .nav-link {
    justify-content: center;
    padding-left: 0 !important;
    padding-right: 0 !important;
}

.sidebar-custom.collapsed .user-profile-toggle {
    justify-content: center;
}

/* Tooltip for collapsed state */
.sidebar-custom.collapsed .nav-link {
    position: relative;
}

.sidebar-custom.collapsed .nav-link::after {
    content: attr(data-tooltip);
    position: absolute;
    left: calc(100% + 10px);
    top: 50%;
    transform: translateY(-50%);
    background: #142552;
    color: #fff;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 0.78rem;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s ease;
    z-index: 9999;
}

.sidebar-custom.collapsed .nav-link:hover::after {
    opacity: 1;
}

/* Toggle button */
#sidebarToggle {
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background 0.2s ease;
}

#sidebarToggle:hover {
    background-color: #e6f0e9 !important;
}

#sidebarToggle:hover i {
    color: #05923b !important;
}

#toggleIcon {
    transition: transform 0.3s ease;
}

.sidebar-custom.collapsed #toggleIcon {
    transform: rotate(180deg);
}

/* Badge styles */
.sidebar-badge {
    font-size: 0.65rem;
    padding: 0.25rem 0.5rem;
    border-radius: 20px;
    font-weight: 500;
}

/* Profile section hover */
.user-profile-toggle {
    transition: all 0.3s ease;
}

.user-profile-toggle:hover {
    background-color: #e6f0e9 !important;
}

.user-profile-toggle:hover strong {
    color: #05923b !important;
}

/* Logout button hover */
.btn-outline-dark-blue {
    transition: all 0.3s ease;
}

.btn-outline-dark-blue:hover {
    background-color: #e6f0e9 !important;
    border-color: #05923b !important;
}

.btn-outline-dark-blue:hover i,
.btn-outline-dark-blue:hover span {
    color: #05923b !important;
}

/* Logout button hover state */
.mt-2 a:hover {
    background-color: #e6f0e9 !important;
    border-color: #05923b !important;
    color: #05923b !important;
}

.mt-2 a:hover i {
    color: #05923b !important;
}

/* Nav link styles */
.nav-link {
    border: none !important;
}

.rounded-4 {
    border-radius: 12px !important;
}

/* Ensure proper spacing */
.mt-auto {
    margin-top: auto !important;
}

/* Fix for icons in collapsed state */
.sidebar-custom.collapsed .nav-icon {
    margin: 0 !important;
}

/* Status dot */
.bg-success {
    background-color: #05923b !important;
}
</style>

<script>
(function () {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');

    if (!toggleBtn || !sidebar) return;

    toggleBtn.addEventListener('click', function () {
        sidebar.classList.toggle('collapsed');
        
        // Dispatch event for layout to update
        const event = new Event('sidebarToggle');
        window.dispatchEvent(event);
    });

    // Ensure scroll container works after collapse/expand
    const scrollContainer = document.querySelector('.nav-scrollable-container');
    if (scrollContainer) {
        scrollContainer.style.overflowY = 'auto';
    }
})();
</script>