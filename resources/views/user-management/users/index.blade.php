<x-app-layout>


   <div class="container-fluid py-4 px-3 px-md-4">

    <!-- ── Page Header ── -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-people-fill text-primary me-2"></i>User Management
            </h4>
            <p class="text-muted mb-0 small">Manage users, approvals, and role assignments.</p>
        </div>
        <button class="btn btn-primary d-flex align-items-center gap-2 px-4">
            <i class="bi bi-person-plus-fill"></i>
            <span>Add User</span>
        </button>
    </div>

    <!-- ── Pending Approval Banner ── -->
    <div class="pending-banner rounded-3 p-3 mb-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-hourglass-split text-warning fs-5"></i>
            <div>
                <span class="fw-semibold text-dark small">3 users awaiting approval</span>
                <span class="text-muted small d-block d-sm-inline ms-sm-2">New registrations require admin approval before they can access the system.</span>
            </div>
        </div>
        <a href="#pendingTab" class="btn btn-sm btn-warning d-flex align-items-center gap-1">
            <i class="bi bi-eye-fill"></i>
            <span class="small">Review Now</span>
        </a>
    </div>

    <!-- ── Stat Cards ── -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded-3">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-4 lh-1">124</div>
                        <small class="text-muted">Total Users</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="stat-icon bg-success bg-opacity-10 text-white rounded-3">
                        <i class="bi bi-person-check-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-4 lh-1">108</div>
                        <small class="text-muted">Active</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning rounded-3">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-4 lh-1">3</div>
                        <small class="text-muted">Pending</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="stat-icon bg-danger bg-opacity-10 text-danger rounded-3">
                        <i class="bi bi-person-x-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-4 lh-1">13</div>
                        <small class="text-muted">Suspended</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Main Card ── -->
    <div class="card border-0 shadow-sm">

        <!-- Card Header: Tabs + Filter Bar -->


        <!-- Filter / Search Bar -->
        <div class="card-body border-bottom py-3 px-4 filter-bar">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-sm-5 col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted" style="font-size:.75rem;"></i>
                        </span>
                        <input type="text" class="form-control border-start-0 ps-0" placeholder="Search by name or email…">
                    </div>
                </div>
                <div class="col-6 col-sm-3 col-md-2">
                    <select class="form-select form-select-sm">
                        <option selected>All Roles</option>
                        <option>Super Administrator</option>
                        <option>Manager</option>
                        <option>Reviewer</option>
                        <option>Applicant</option>
                    </select>
                </div>
                <div class="col-6 col-sm-3 col-md-2 hide-xs">
                    <select class="form-select form-select-sm">
                        <option selected>All Status</option>
                        <option>Active</option>
                        <option>Pending</option>
                        <option>Suspended</option>
                    </select>
                </div>
                <div class="col-12 col-md-4 d-flex justify-content-md-end gap-2 mt-1 mt-md-0">
                    <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
                        <i class="bi bi-funnel-fill" style="font-size:.75rem;"></i>
                        <span class="small">Filter</span>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 hide-sm">
                        <i class="bi bi-download" style="font-size:.75rem;"></i>
                        <span class="small">Export</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ── Pending Approval Section (shown when Pending tab active) ── -->
        <div class="px-4 pt-3 pb-0">
            <p class="small fw-semibold text-warning mb-2">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>Awaiting Approval
            </p>
        </div>

        <!-- Pending Users -->
        <div class="px-4 pb-3">
            <div class="row g-3">

                <!-- Pending User Card 1 -->
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card border shadow-sm approval-card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between py-2 px-3">
                            <span class="badge badge-pending rounded-pill px-2 py-1 small">
                                <i class="bi bi-hourglass-split me-1"></i>Pending Approval
                            </span>
                            <small class="text-muted">2 hrs ago</small>
                        </div>
                        <div class="card-body px-3 py-3">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="avatar-circle bg-info text-white rounded-circle d-flex align-items-center justify-content-center fw-bold">
                                    TM
                                </div>
                                <div>
                                    <div class="fw-semibold small">Thabo Molefe</div>
                                    <div class="text-muted" style="font-size:.75rem;">thabo.molefe@email.com</div>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="d-flex align-items-center gap-1 text-muted" style="font-size:.75rem;">
                                    <i class="bi bi-building"></i> ESO Partner
                                </span>
                                <span class="d-flex align-items-center gap-1 text-muted" style="font-size:.75rem;">
                                    <i class="bi bi-calendar3"></i> Registered 12 Feb 2025
                                </span>
                            </div>
                            <!-- Role Assignment -->
                            <div class="mb-3">
                                <label class="form-label small fw-semibold mb-1">Assign Role</label>
                                <select class="form-select form-select-sm">
                                    <option selected disabled>Select a role…</option>
                                    <option>Manager</option>
                                    <option>Reviewer</option>
                                    <option>Applicant</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-top px-3 py-2 d-flex gap-2">
                            <button class="btn btn-sm btn-success flex-fill d-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-check-lg"></i> Approve
                            </button>
                            <button class="btn btn-sm btn-outline-danger flex-fill d-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-x-lg"></i> Reject
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Pending User Card 2 -->
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card border shadow-sm approval-card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between py-2 px-3">
                            <span class="badge badge-pending rounded-pill px-2 py-1 small">
                                <i class="bi bi-hourglass-split me-1"></i>Pending Approval
                            </span>
                            <small class="text-muted">5 hrs ago</small>
                        </div>
                        <div class="card-body px-3 py-3">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="avatar-circle bg-danger text-white rounded-circle d-flex align-items-center justify-content-center fw-bold">
                                    PN
                                </div>
                                <div>
                                    <div class="fw-semibold small">Palesa Nkosi</div>
                                    <div class="text-muted" style="font-size:.75rem;">palesa.nkosi@email.com</div>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="d-flex align-items-center gap-1 text-muted" style="font-size:.75rem;">
                                    <i class="bi bi-building"></i> Startup
                                </span>
                                <span class="d-flex align-items-center gap-1 text-muted" style="font-size:.75rem;">
                                    <i class="bi bi-calendar3"></i> Registered 10 Feb 2025
                                </span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold mb-1">Assign Role</label>
                                <select class="form-select form-select-sm">
                                    <option selected disabled>Select a role…</option>
                                    <option>Manager</option>
                                    <option>Reviewer</option>
                                    <option>Applicant</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-top px-3 py-2 d-flex gap-2">
                            <button class="btn btn-sm btn-success flex-fill d-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-check-lg"></i> Approve
                            </button>
                            <button class="btn btn-sm btn-outline-danger flex-fill d-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-x-lg"></i> Reject
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Pending User Card 3 -->
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card border shadow-sm approval-card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between py-2 px-3">
                            <span class="badge badge-pending rounded-pill px-2 py-1 small">
                                <i class="bi bi-hourglass-split me-1"></i>Pending Approval
                            </span>
                            <small class="text-muted">1 day ago</small>
                        </div>
                        <div class="card-body px-3 py-3">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="avatar-circle bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold">
                                    LZ
                                </div>
                                <div>
                                    <div class="fw-semibold small">Lungelo Zulu</div>
                                    <div class="text-muted" style="font-size:.75rem;">lungelo.zulu@email.com</div>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="d-flex align-items-center gap-1 text-muted" style="font-size:.75rem;">
                                    <i class="bi bi-building"></i> Government
                                </span>
                                <span class="d-flex align-items-center gap-1 text-muted" style="font-size:.75rem;">
                                    <i class="bi bi-calendar3"></i> Registered 9 Feb 2025
                                </span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold mb-1">Assign Role</label>
                                <select class="form-select form-select-sm">
                                    <option selected disabled>Select a role…</option>
                                    <option>Manager</option>
                                    <option>Reviewer</option>
                                    <option>Applicant</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-top px-3 py-2 d-flex gap-2">
                            <button class="btn btn-sm btn-success flex-fill d-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-check-lg"></i> Approve
                            </button>
                            <button class="btn btn-sm btn-outline-danger flex-fill d-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-x-lg"></i> Reject
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ── Divider ── -->
        <div class="px-4 pt-2 pb-0">
            <hr class="my-2">
            <p class="small fw-semibold text-muted mb-2">
                <i class="bi bi-people me-1"></i>All Users
            </p>
        </div>

        <!-- ── Users Table ── -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 users-table">
                <thead class="table-light">
                    <tr>
                        <th class="px-4 py-3" style="width:5%;">
                            <input class="form-check-input" type="checkbox">
                        </th>
                        <th class="py-3">User</th>
                        <th class="py-3 hide-sm">Role</th>
                        <th class="py-3 hide-sm">Status</th>
                        <th class="py-3 hide-xs">Joined</th>
                        <th class="py-3 hide-xs">Last Active</th>
                        <th class="py-3 text-end px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>

                    <!-- Row 1 – Active -->
                    <tr>
                        <td class="px-4"><input class="form-check-input" type="checkbox"></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-circle bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold">
                                    AM
                                </div>
                                <div>
                                    <div class="fw-semibold small">Amara Mokoena</div>
                                    <div class="text-muted" style="font-size:.72rem;">amara.mokoena@email.com</div>
                                </div>
                            </div>
                        </td>
                        <td class="hide-sm">
                            <span class="role-pill bg-primary bg-opacity-10 text-primary">Super Administrator</span>
                        </td>
                        <td class="hide-sm">
                            <span class="badge badge-active rounded-pill px-2 py-1 small">
                                <i class="bi bi-circle-fill me-1" style="font-size:.45rem;"></i>Active
                            </span>
                        </td>
                        <td class="hide-xs text-muted small">Jan 12, 2024</td>
                        <td class="hide-xs text-muted small">Today, 9:42 AM</td>
                        <td class="text-end px-4">
                            <div class="row-actions d-flex justify-content-end gap-1">
                                <button class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil-fill" style="font-size:.7rem;"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" title="Assign Role">
                                    <i class="bi bi-shield-fill" style="font-size:.7rem;"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="Suspend">
                                    <i class="bi bi-slash-circle-fill" style="font-size:.7rem;"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Row 2 – Active -->
                    <tr>
                        <td class="px-4"><input class="form-check-input" type="checkbox"></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-circle bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold">
                                    KD
                                </div>
                                <div>
                                    <div class="fw-semibold small">Kgomotso Dlamini</div>
                                    <div class="text-muted" style="font-size:.72rem;">k.dlamini@email.com</div>
                                </div>
                            </div>
                        </td>
                        <td class="hide-sm">
                            <span class="role-pill bg-success bg-opacity-10 text-white">Manager</span>
                        </td>
                        <td class="hide-sm">
                            <span class="badge badge-active rounded-pill px-2 py-1 small">
                                <i class="bi bi-circle-fill me-1" style="font-size:.45rem;"></i>Active
                            </span>
                        </td>
                        <td class="hide-xs text-muted small">Mar 5, 2024</td>
                        <td class="hide-xs text-muted small">Yesterday</td>
                        <td class="text-end px-4">
                            <div class="row-actions d-flex justify-content-end gap-1">
                                <button class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil-fill" style="font-size:.7rem;"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" title="Assign Role">
                                    <i class="bi bi-shield-fill" style="font-size:.7rem;"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="Suspend">
                                    <i class="bi bi-slash-circle-fill" style="font-size:.7rem;"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Row 3 – Suspended -->
                    <tr class="bg-danger bg-opacity-10">
                        <td class="px-4"><input class="form-check-input" type="checkbox"></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-circle bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold">
                                    NS
                                </div>
                                <div>
                                    <div class="fw-semibold small text-muted">Nandi Sithole</div>
                                    <div class="text-muted" style="font-size:.72rem;">nandi.sithole@email.com</div>
                                </div>
                            </div>
                        </td>
                        <td class="hide-sm">
                            <span class="role-pill bg-secondary bg-opacity-10 text-secondary">Reviewer</span>
                        </td>
                        <td class="hide-sm">
                            <span class="badge badge-suspended rounded-pill px-2 py-1 small">
                                <i class="bi bi-circle-fill me-1" style="font-size:.45rem;"></i>Suspended
                            </span>
                        </td>
                        <td class="hide-xs text-muted small">Jul 19, 2024</td>
                        <td class="hide-xs text-muted small">3 weeks ago</td>
                        <td class="text-end px-4">
                            <div class="row-actions d-flex justify-content-end gap-1">
                                <button class="btn btn-sm btn-outline-success" title="Reinstate">
                                    <i class="bi bi-check-circle-fill" style="font-size:.7rem;"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" title="Assign Role">
                                    <i class="bi bi-shield-fill" style="font-size:.7rem;"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="Delete">
                                    <i class="bi bi-trash-fill" style="font-size:.7rem;"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Row 4 – Active -->
                    <tr>
                        <td class="px-4"><input class="form-check-input" type="checkbox"></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-circle bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold">
                                    BM
                                </div>
                                <div>
                                    <div class="fw-semibold small">Bongani Mthembu</div>
                                    <div class="text-muted" style="font-size:.72rem;">bongani.m@email.com</div>
                                </div>
                            </div>
                        </td>
                        <td class="hide-sm">
                            <span class="role-pill bg-warning bg-opacity-10 text-warning">Applicant</span>
                        </td>
                        <td class="hide-sm">
                            <span class="badge badge-active rounded-pill px-2 py-1 small">
                                <i class="bi bi-circle-fill me-1" style="font-size:.45rem;"></i>Active
                            </span>
                        </td>
                        <td class="hide-xs text-muted small">Sep 2, 2024</td>
                        <td class="hide-xs text-muted small">Today, 11:15 AM</td>
                        <td class="text-end px-4">
                            <div class="row-actions d-flex justify-content-end gap-1">
                                <button class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil-fill" style="font-size:.7rem;"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" title="Assign Role">
                                    <i class="bi bi-shield-fill" style="font-size:.7rem;"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="Suspend">
                                    <i class="bi bi-slash-circle-fill" style="font-size:.7rem;"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Row 5 – Pending (in all-users view) -->
                    <tr class="bg-warning bg-opacity-10">
                        <td class="px-4"><input class="form-check-input" type="checkbox"></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-circle bg-info text-white rounded-circle d-flex align-items-center justify-content-center fw-bold">
                                    TM
                                </div>
                                <div>
                                    <div class="fw-semibold small">Thabo Molefe</div>
                                    <div class="text-muted" style="font-size:.72rem;">thabo.molefe@email.com</div>
                                </div>
                            </div>
                        </td>
                        <td class="hide-sm">
                            <span class="text-muted small fst-italic">— Unassigned —</span>
                        </td>
                        <td class="hide-sm">
                            <span class="badge badge-pending rounded-pill px-2 py-1 small">
                                <i class="bi bi-hourglass-split me-1" style="font-size:.65rem;"></i>Pending
                            </span>
                        </td>
                        <td class="hide-xs text-muted small">Feb 12, 2025</td>
                        <td class="hide-xs text-muted small">—</td>
                        <td class="text-end px-4">
                            <div class="d-flex justify-content-end gap-1">
                                <button class="btn btn-sm btn-success" title="Approve">
                                    <i class="bi bi-check-lg" style="font-size:.7rem;"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="Reject">
                                    <i class="bi bi-x-lg" style="font-size:.7rem;"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

        <!-- ── Pagination ── -->
        <div class="card-footer bg-white border-top px-4 py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <small class="text-muted">Showing 1–10 of 124 users</small>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item disabled"><a class="page-link" href="#">‹</a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#">›</a></li>
                </ul>
            </nav>
        </div>

    </div><!-- /card -->


</div><!-- /container -->
  

</x-app-layout>