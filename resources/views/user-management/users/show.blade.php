<x-app-layout>

       
 
<div class="container-fluid py-4 px-3 px-md-4" style="max-width: 1300px;">

    <!-- ── Breadcrumb ── -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">User Management</a></li>
            <li class="breadcrumb-item active text-dark fw-semibold">Amara Mokoena</li>
        </ol>
    </nav>

    <div class="row g-4">

        <!-- ═══════════════════════════════
             LEFT COLUMN – Profile Card
        ════════════════════════════════ -->
        <div class="col-12 col-lg-4 col-xl-3">

            <!-- Profile Hero Card -->
            <div class="card border-0 shadow-sm overflow-hidden mb-4">
                <!-- Avatar -->
                <div class="position-relative mb-3" style="height:50px;">
                    <div class="profile-avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                        AM
                    </div>
                    <!-- Edit avatar btn -->
                    <button class="btn btn-light btn-sm rounded-circle position-absolute d-flex align-items-center justify-content-center p-1 border shadow-sm"
                        style="width:26px;height:26px;bottom:-34px;left:90px;">
                        <i class="bi bi-pencil-fill" style="font-size:.6rem;"></i>
                    </button>
                </div>

                <div class="card-body pt-4 mt-2 px-4 pb-3 mt-3">
                    <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
                        <div>
                            <h5 class="fw-bold mb-0">Amara Mokoena</h5>
                            <p class="text-muted small mb-0">amara.mokoena@email.com</p>
                        </div>
                        <span class="badge badge-active rounded-pill px-2 py-1 small mt-1">
                            <i class="bi bi-circle-fill me-1" style="font-size:.4rem;"></i>Active
                        </span>
                    </div>

                    <div class="mt-3 d-flex flex-wrap gap-2">
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2 py-1 small fw-semibold">
                            <i class="bi bi-shield-fill me-1"></i>Super Administrator
                        </span>
                    </div>

                    <hr class="my-3">

                    <!-- Quick Info -->
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex align-items-center gap-2 text-muted small">
                            <i class="bi bi-building text-primary" style="width:16px;"></i>
                            Government Agency
                        </div>
                        <div class="d-flex align-items-center gap-2 text-muted small">
                            <i class="bi bi-telephone-fill text-primary" style="width:16px;"></i>
                            +27 82 345 6789
                        </div>
                        <div class="d-flex align-items-center gap-2 text-muted small">
                            <i class="bi bi-geo-alt-fill text-primary" style="width:16px;"></i>
                            Johannesburg, South Africa
                        </div>
                        <div class="d-flex align-items-center gap-2 text-muted small">
                            <i class="bi bi-calendar3 text-primary" style="width:16px;"></i>
                            Joined Jan 12, 2024
                        </div>
                        <div class="d-flex align-items-center gap-2 text-muted small">
                            <i class="bi bi-clock-fill text-primary" style="width:16px;"></i>
                            Last active: Today, 9:42 AM
                        </div>
                    </div>

                    <hr class="my-3">

                    <!-- Actions -->
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary btn-sm action-btn d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-pencil-fill"></i> Edit Profile
                        </button>
                        <button class="btn btn-outline-secondary btn-sm action-btn d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-shield-fill"></i> Change Role
                        </button>
                        <button class="btn btn-outline-warning btn-sm action-btn d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-lock-fill"></i> Reset Password
                        </button>
                        <button class="btn btn-outline-danger btn-sm action-btn d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-slash-circle-fill"></i> Suspend User
                        </button>
                    </div>
                </div>
            </div>

            <!-- Assigned Role Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <h6 class="fw-bold mb-0 small"><i class="bi bi-shield-lock-fill text-primary me-2"></i>Assigned Role</h6>
                </div>
                <div class="card-body px-4 py-3">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded-3">
                            <i class="bi bi-shield-fill fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-semibold small">Super Administrator</div>
                            <div class="text-muted" style="font-size:.72rem;">Full system access</div>
                        </div>
                    </div>
                    <p class="text-muted" style="font-size:.78rem;">
                        This role grants unrestricted access to all modules, settings, and user data across the system.
                    </p>
                    <div class="section-label mb-2">Key Permissions</div>
                    <div class="d-flex flex-wrap gap-1">
                        <span class="perm-chip">view Dashboard</span>
                        <span class="perm-chip">manage Users</span>
                        <span class="perm-chip">approve Applications</span>
                        <span class="perm-chip">delete Records</span>
                        <span class="perm-chip">view Analytics</span>
                        <span class="perm-chip">+38 more</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- ═══════════════════════════════
             RIGHT COLUMN – Tabs Content
        ════════════════════════════════ -->
        <div class="col-12 col-lg-8 col-xl-9">

            <!-- ── Summary Stats ── -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm h-100 stat-card">
                        <div class="card-body d-flex align-items-center gap-3 p-3">
                            <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded-3">
                                <i class="bi bi-file-earmark-text-fill fs-5"></i>
                            </div>
                            <div>
                                <div class="fw-bold fs-5 lh-1">34</div>
                                <small class="text-muted">Applications</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm h-100 stat-card">
                        <div class="card-body d-flex align-items-center gap-3 p-3">
                            <div class="stat-icon bg-success bg-opacity-10 text-white rounded-3">
                                <i class="bi bi-check-circle-fill fs-5"></i>
                            </div>
                            <div>
                                <div class="fw-bold fs-5 lh-1">21</div>
                                <small class="text-muted">Approvals</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm h-100 stat-card">
                        <div class="card-body d-flex align-items-center gap-3 p-3">
                            <div class="stat-icon bg-warning bg-opacity-10 text-warning rounded-3">
                                <i class="bi bi-bar-chart-fill fs-5"></i>
                            </div>
                            <div>
                                <div class="fw-bold fs-5 lh-1">9</div>
                                <small class="text-muted">Reports</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm h-100 stat-card">
                        <div class="card-body d-flex align-items-center gap-3 p-3">
                            <div class="stat-icon bg-info bg-opacity-10 text-info rounded-3">
                                <i class="bi bi-people-fill fs-5"></i>
                            </div>
                            <div>
                                <div class="fw-bold fs-5 lh-1">418</div>
                                <small class="text-muted">Login Days</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Tabbed Card ── -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom px-4 pt-3 pb-0">
                    <ul class="nav nav-tabs">
                        <li class="nav-item"><a class="nav-link active" href="#">Profile Details</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Activity Log</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Applications</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Permissions</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Notes</a></li>
                    </ul>
                </div>

                <div class="card-body px-4 py-4">

                    <!-- ══ TAB: Profile Details ══ -->
                    <div class="row g-4">

                        <!-- Personal Information -->
                        <div class="col-12 col-md-6">
                            <p class="section-label mb-3">Personal Information</p>
                            <div class="info-row d-flex align-items-start">
                                <span class="info-label text-muted">Full Name</span>
                                <span class="fw-semibold small">Amara Mokoena</span>
                            </div>
                            <div class="info-row d-flex align-items-start">
                                <span class="info-label text-muted">Email</span>
                                <span class="small">amara.mokoena@email.com</span>
                            </div>
                            <div class="info-row d-flex align-items-start">
                                <span class="info-label text-muted">Phone</span>
                                <span class="small">+27 82 345 6789</span>
                            </div>
                            <div class="info-row d-flex align-items-start">
                                <span class="info-label text-muted">ID / Passport</span>
                                <span class="small">8902145098083</span>
                            </div>
                            <div class="info-row d-flex align-items-start">
                                <span class="info-label text-muted">Gender</span>
                                <span class="small">Female</span>
                            </div>
                            <div class="info-row d-flex align-items-start">
                                <span class="info-label text-muted">Date of Birth</span>
                                <span class="small">14 February 1989</span>
                            </div>
                            <div class="info-row d-flex align-items-start">
                                <span class="info-label text-muted">Nationality</span>
                                <span class="small">South African</span>
                            </div>
                        </div>

                        <!-- Organisation & System Info -->
                        <div class="col-12 col-md-6">
                            <p class="section-label mb-3">Organisation & System</p>
                            <div class="info-row d-flex align-items-start">
                                <span class="info-label text-muted">Organisation</span>
                                <span class="small">Government Agency</span>
                            </div>
                            <div class="info-row d-flex align-items-start">
                                <span class="info-label text-muted">Department</span>
                                <span class="small">Digital Transformation</span>
                            </div>
                            <div class="info-row d-flex align-items-start">
                                <span class="info-label text-muted">Job Title</span>
                                <span class="small">Systems Administrator</span>
                            </div>
                            <div class="info-row d-flex align-items-start">
                                <span class="info-label text-muted">Location</span>
                                <span class="small">Johannesburg, Gauteng</span>
                            </div>
                            <div class="info-row d-flex align-items-start">
                                <span class="info-label text-muted">User ID</span>
                                <span class="small text-muted">#USR-00124</span>
                            </div>
                            <div class="info-row d-flex align-items-start">
                                <span class="info-label text-muted">Account Created</span>
                                <span class="small">Jan 12, 2024</span>
                            </div>
                            <div class="info-row d-flex align-items-start">
                                <span class="info-label text-muted">Approved By</span>
                                <span class="small">System · Auto-approved</span>
                            </div>
                        </div>

                        <div class="col-12">
                            <hr class="my-1">
                            <p class="section-label mb-3 mt-3">Address</p>
                            <div class="row g-3">
                                <div class="col-12 col-sm-6">
                                    <div class="info-row d-flex align-items-start">
                                        <span class="info-label text-muted">Street</span>
                                        <span class="small">14 Mandela Ave, Sandton</span>
                                    </div>
                                    <div class="info-row d-flex align-items-start">
                                        <span class="info-label text-muted">City</span>
                                        <span class="small">Johannesburg</span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="info-row d-flex align-items-start">
                                        <span class="info-label text-muted">Province</span>
                                        <span class="small">Gauteng</span>
                                    </div>
                                    <div class="info-row d-flex align-items-start">
                                        <span class="info-label text-muted">Postal Code</span>
                                        <span class="small">2196</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div><!-- /row profile details -->

                    <hr class="my-4">

                    <!-- ── Recent Activity (mini preview) ── -->
                    <p class="section-label mb-3">Recent Activity</p>
                    <div class="timeline">

                        <div class="timeline-item">
                            <div class="timeline-dot bg-success"></div>
                            <div class="d-flex align-items-start justify-content-between flex-wrap gap-1">
                                <div>
                                    <span class="small fw-semibold">Approved application</span>
                                    <span class="text-muted small"> — APP-2025-0341 (Thabo Startup)</span>
                                </div>
                                <small class="text-muted">Today, 9:40 AM</small>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-dot bg-primary"></div>
                            <div class="d-flex align-items-start justify-content-between flex-wrap gap-1">
                                <div>
                                    <span class="small fw-semibold">Updated role permissions</span>
                                    <span class="text-muted small"> — Reviewer role modified</span>
                                </div>
                                <small class="text-muted">Yesterday, 3:15 PM</small>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-dot bg-warning"></div>
                            <div class="d-flex align-items-start justify-content-between flex-wrap gap-1">
                                <div>
                                    <span class="small fw-semibold">User suspended</span>
                                    <span class="text-muted small"> — Nandi Sithole</span>
                                </div>
                                <small class="text-muted">Feb 18, 2025</small>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-dot bg-info"></div>
                            <div class="d-flex align-items-start justify-content-between flex-wrap gap-1">
                                <div>
                                    <span class="small fw-semibold">Logged in</span>
                                    <span class="text-muted small"> — Chrome on Windows · 41.102.44.200</span>
                                </div>
                                <small class="text-muted">Feb 17, 2025</small>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-dot bg-danger"></div>
                            <div class="d-flex align-items-start justify-content-between flex-wrap gap-1">
                                <div>
                                    <span class="small fw-semibold">Deleted cohort</span>
                                    <span class="text-muted small"> — Cohort 2024-Q3</span>
                                </div>
                                <small class="text-muted">Feb 10, 2025</small>
                            </div>
                        </div>

                    </div>

                    <div class="text-center mt-3">
                        <a href="#" class="btn btn-sm btn-outline-secondary px-4" style="font-size:.8rem;">
                            View Full Activity Log <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>

                    <hr class="my-4">

                    <!-- ── Danger Zone ── -->
                    <p class="section-label mb-3 text-danger">Danger Zone</p>
                    <div class="danger-zone p-4">
                        <div class="row g-3 align-items-center">
                            <div class="col-12 col-md-8">
                                <div class="fw-semibold small mb-1">Suspend Account</div>
                                <p class="text-muted mb-0" style="font-size:.78rem;">
                                    Suspending this account will immediately revoke all active sessions and block login access. The user's data will be retained.
                                </p>
                            </div>
                            <div class="col-12 col-md-4 d-flex justify-content-md-end">
                                <button class="btn btn-outline-danger btn-sm action-btn">
                                    <i class="bi bi-slash-circle-fill me-1"></i> Suspend User
                                </button>
                            </div>

                            <div class="col-12"><hr class="my-1"></div>

                            <div class="col-12 col-md-8">
                                <div class="fw-semibold small mb-1">Delete Account</div>
                                <p class="text-muted mb-0" style="font-size:.78rem;">
                                    Permanently removes this user and all associated data. This action <strong>cannot be undone</strong>.
                                </p>
                            </div>
                            <div class="col-12 col-md-4 d-flex justify-content-md-end">
                                <button class="btn btn-danger btn-sm action-btn">
                                    <i class="bi bi-trash-fill me-1"></i> Delete Account
                                </button>
                            </div>
                        </div>
                    </div>

                </div><!-- /card-body -->
            </div><!-- /card -->
        </div><!-- /right col -->

    </div><!-- /row -->
</div>



</x-app-layout>