<x-app-layout>
  

<div class="container roles-page p-4">

    {{-- ── Page Header ── --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-shield-lock-fill text-primary me-2"></i>Roles &amp; Permissions
            </h4>
            <p class="text-muted mb-0 small">Manage system roles and control what each role can access.</p>
        </div>
        <button class="btn btn-primary d-flex align-items-center gap-2 px-4" data-bs-toggle="modal" data-bs-target="#roleModal">
            <i class="bi bi-plus-circle-fill"></i>
            <span>Add New Role</span>
        </button>
    </div>

    {{-- ── Summary Cards ── --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                        <i class="bi bi-shield-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-4 lh-1">6</div>
                        <small class="text-muted">Total Roles</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="stat-icon bg-opacity-10 text-success rounded-3 p-3">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-4 lh-1">48</div>
                        <small class="text-muted">Total Users</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                        <i class="bi bi-key-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-4 lh-1">24</div>
                        <small class="text-muted">Permissions</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="stat-icon bg-danger bg-opacity-10 text-danger rounded-3 p-3">
                        <i class="bi bi-person-x-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-4 lh-1">3</div>
                        <small class="text-muted">Suspended</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- ── Roles List ── --}}
        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0">System Roles</h6>
                        <span class="badge bg-primary rounded-pill">6 Roles</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" id="roleList">

                        <a href="#" class="list-group-item list-group-item-action role-item active-role px-4 py-3" data-role="super-admin" onclick="selectRole(this); return false;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="role-avatar bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;font-size:0.85rem;font-weight:700;flex-shrink:0;">SA</div>
                                    <div>
                                        <div class="fw-semibold text-dark small">Super Administrator</div>
                                        <div class="text-muted" style="font-size:0.75rem;">Full system access</div>
                                    </div>
                                </div>
                                <span class="badge bg-danger bg-opacity-10 text-danger">2 users</span>
                            </div>
                        </a>

                        <a href="#" class="list-group-item list-group-item-action role-item px-4 py-3" data-role="cafi-admin" onclick="selectRole(this); return false;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="role-avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;font-size:0.85rem;font-weight:700;flex-shrink:0;">CA</div>
                                    <div>
                                        <div class="fw-semibold text-dark small">CAFI Administrator</div>
                                        <div class="text-muted" style="font-size:0.75rem;">Programme management</div>
                                    </div>
                                </div>
                                <span class="badge bg-primary bg-opacity-10 text-primary">5 users</span>
                            </div>
                        </a>

                        <a href="#" class="list-group-item list-group-item-action role-item px-4 py-3" data-role="eso" onclick="selectRole(this); return false;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="role-avatar bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;font-size:0.85rem;font-weight:700;flex-shrink:0;">ES</div>
                                    <div>
                                        <div class="fw-semibold text-dark small">Enterprise Support Org.</div>
                                        <div class="text-muted" style="font-size:0.75rem;">Incubation &amp; reporting</div>
                                    </div>
                                </div>
                                <span class="badge bg-opacity-10 text-success">8 users</span>
                            </div>
                        </a>

                        <a href="#" class="list-group-item list-group-item-action role-item px-4 py-3" data-role="entrepreneur" onclick="selectRole(this); return false;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="role-avatar bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;font-size:0.85rem;font-weight:700;flex-shrink:0;">EN</div>
                                    <div>
                                        <div class="fw-semibold text-dark small">Entrepreneur</div>
                                        <div class="text-muted" style="font-size:0.75rem;">Networking &amp; learning</div>
                                    </div>
                                </div>
                                <span class="badge bg-info bg-opacity-10 text-info">20 users</span>
                            </div>
                        </a>

                        <a href="#" class="list-group-item list-group-item-action role-item px-4 py-3" data-role="mentor" onclick="selectRole(this); return false;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="role-avatar bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;font-size:0.85rem;font-weight:700;flex-shrink:0;">ME</div>
                                    <div>
                                        <div class="fw-semibold text-dark small">Mentor</div>
                                        <div class="text-muted" style="font-size:0.75rem;">Advisory &amp; guidance</div>
                                    </div>
                                </div>
                                <span class="badge bg-warning bg-opacity-10 text-warning">9 users</span>
                            </div>
                        </a>

                        <a href="#" class="list-group-item list-group-item-action role-item px-4 py-3" data-role="investor" onclick="selectRole(this); return false;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="role-avatar bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;font-size:0.85rem;font-weight:700;flex-shrink:0;">IN</div>
                                    <div>
                                        <div class="fw-semibold text-dark small">Investor</div>
                                        <div class="text-muted" style="font-size:0.75rem;">Discovery &amp; networking</div>
                                    </div>
                                </div>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">4 users</span>
                            </div>
                        </a>

                    </div>
                </div>
            </div>
        </div>

        {{-- ── Permissions Matrix ── --}}
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h6 class="fw-bold mb-0" id="permTitle">Super Administrator — Permissions</h6>
                            <small class="text-muted" id="permSubtitle">Full system access · 24 of 24 permissions granted</small>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-secondary" onclick="toggleAll(false)">
                                <i class="bi bi-x-circle me-1"></i>Revoke All
                            </button>
                            <button class="btn btn-sm btn-outline-primary" onclick="toggleAll(true)">
                                <i class="bi bi-check-circle me-1"></i>Grant All
                            </button>
                            <button class="btn btn-sm btn-primary" onclick="savePermissions()">
                                <i class="bi bi-floppy-fill me-1"></i>Save
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 perm-table">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3" style="width:35%">Module</th>
                                    <th class="text-center py-3">View</th>
                                    <th class="text-center py-3">Create</th>
                                    <th class="text-center py-3">Edit</th>
                                    <th class="text-center py-3">Delete</th>
                                    <th class="text-center py-3">Approve</th>
                                </tr>
                            </thead>
                            <tbody id="permTable">
                                <!-- Row template: module | view | create | edit | delete | approve -->
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-speedometer2 text-primary"></i>
                                            <span class="fw-medium small">Dashboard</span>
                                        </div>
                                    </td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" disabled></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" disabled></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" disabled></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" disabled></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-megaphone-fill text-primary"></i>
                                            <span class="fw-medium small">Calls for Applications</span>
                                        </div>
                                    </td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-file-earmark-text-fill text-primary"></i>
                                            <span class="fw-medium small">Applications</span>
                                        </div>
                                    </td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-funnel-fill text-primary"></i>
                                            <span class="fw-medium small">Screening &amp; Eligibility</span>
                                        </div>
                                    </td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-clipboard2-data-fill text-primary"></i>
                                            <span class="fw-medium small">Evaluation &amp; Scoring</span>
                                        </div>
                                    </td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-people-fill text-primary"></i>
                                            <span class="fw-medium small">Cohort Management</span>
                                        </div>
                                    </td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-bar-chart-fill text-primary"></i>
                                            <span class="fw-medium small">Enterprise Reports</span>
                                        </div>
                                    </td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-building text-primary"></i>
                                            <span class="fw-medium small">ESO Reports</span>
                                        </div>
                                    </td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-person-lines-fill text-primary"></i>
                                            <span class="fw-medium small">User Management</span>
                                        </div>
                                    </td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-book-fill text-primary"></i>
                                            <span class="fw-medium small">Knowledge Hub</span>
                                        </div>
                                    </td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" disabled></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-graph-up-arrow text-primary"></i>
                                            <span class="fw-medium small">Analytics &amp; Reporting</span>
                                        </div>
                                    </td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" checked></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" disabled></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" disabled></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" disabled></td>
                                    <td class="text-center"><input class="form-check-input perm-check" type="checkbox" disabled></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

{{-- ── Add / Edit Role Modal ── --}}
<div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="roleModalLabel">
                    <i class="bi bi-shield-plus text-primary me-2"></i>Add New Role
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <div class="mb-3">
                    <label class="form-label fw-medium small">Role Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" placeholder="e.g. Evaluation Officer">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium small">Description</label>
                    <textarea class="form-control" rows="2" placeholder="Brief description of this role's responsibilities"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium small">Role Colour</label>
                    <div class="d-flex gap-2 flex-wrap">
                        <input type="radio" class="btn-check" name="roleColor" id="c1" value="primary" checked>
                        <label class="btn btn-sm btn-primary rounded-circle color-pick" for="c1" style="width:32px;height:32px;"></label>

                        <input type="radio" class="btn-check" name="roleColor" id="c2" value="success">
                        <label class="btn btn-sm btn-success rounded-circle color-pick" for="c2" style="width:32px;height:32px;"></label>

                        <input type="radio" class="btn-check" name="roleColor" id="c3" value="danger">
                        <label class="btn btn-sm btn-danger rounded-circle color-pick" for="c3" style="width:32px;height:32px;"></label>

                        <input type="radio" class="btn-check" name="roleColor" id="c4" value="warning">
                        <label class="btn btn-sm btn-warning rounded-circle color-pick" for="c4" style="width:32px;height:32px;"></label>

                        <input type="radio" class="btn-check" name="roleColor" id="c5" value="info">
                        <label class="btn btn-sm btn-info rounded-circle color-pick" for="c5" style="width:32px;height:32px;"></label>

                        <input type="radio" class="btn-check" name="roleColor" id="c6" value="secondary">
                        <label class="btn btn-sm btn-secondary rounded-circle color-pick" for="c6" style="width:32px;height:32px;"></label>
                    </div>
                </div>
                <div class="mb-1">
                    <label class="form-label fw-medium small">Base Permission Template</label>
                    <select class="form-select">
                        <option value="">— No template, configure manually —</option>
                        <option>Copy from: CAFI Administrator</option>
                        <option>Copy from: ESO</option>
                        <option>Copy from: Entrepreneur</option>
                        <option>Copy from: Mentor</option>
                        <option>Copy from: Investor</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal">
                    <i class="bi bi-plus-circle me-1"></i>Create Role
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── Save Toast ── --}}
<div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999">
    <div id="saveToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-check-circle-fill me-2"></i>Permissions saved successfully.
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script>
/* ── Role selection: highlight row + update header ── */
const roleMeta = {
    'super-admin':   { title: 'Super Administrator',       subtitle: 'Full system access · 24 of 24 permissions granted' },
    'cafi-admin':    { title: 'CAFI Administrator',         subtitle: 'Programme management · 18 of 24 permissions granted' },
    'eso':           { title: 'Enterprise Support Org.',    subtitle: 'Incubation & reporting · 12 of 24 permissions granted' },
    'entrepreneur':  { title: 'Entrepreneur',               subtitle: 'Networking & learning · 8 of 24 permissions granted' },
    'mentor':        { title: 'Mentor',                     subtitle: 'Advisory & guidance · 10 of 24 permissions granted' },
    'investor':      { title: 'Investor',                   subtitle: 'Discovery & networking · 7 of 24 permissions granted' },
};

function selectRole(el) {
    document.querySelectorAll('.role-item').forEach(r => r.classList.remove('active-role'));
    el.classList.add('active-role');
    const role = el.dataset.role;
    if (roleMeta[role]) {
        document.getElementById('permTitle').textContent    = roleMeta[role].title + ' — Permissions';
        document.getElementById('permSubtitle').textContent = roleMeta[role].subtitle;
    }
}

/* ── Grant / Revoke all ── */
function toggleAll(state) {
    document.querySelectorAll('.perm-check:not(:disabled)').forEach(cb => cb.checked = state);
}

/* ── Save with toast ── */
function savePermissions() {
    const toast = new bootstrap.Toast(document.getElementById('saveToast'), { delay: 3000 });
    toast.show();
}
</script>

  

</x-app-layout>