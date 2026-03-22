<x-app-layout>
<div class="screening-page p-4" x-data="screeningApp()">

    {{-- ═══════════════════════════════════════
         PAGE HEADER
    ═══════════════════════════════════════ --}}
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Incubation</a></li>
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Applications</a></li>
                    <li class="breadcrumb-item active">Screening &amp; Eligibility</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-funnel-fill text-primary me-2"></i>Screening &amp; Eligibility
            </h4>
            <p class="text-muted small mb-0">Review submitted applications · Mark eligible or rejected · LEHSFF Cohort 3</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1"
                    @click="exportList()">
                <i class="bi bi-download"></i> Export List
            </button>
            <button class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1"
                    @click="openBulk()" :disabled="selected.length === 0"
                    :class="selected.length === 0 ? 'opacity-50' : ''">
                <i class="bi bi-check2-square"></i>
                Bulk Action
                <span class="badge bg-primary ms-1" x-show="selected.length > 0" x-text="selected.length"></span>
            </button>
        </div>
    </div>

    {{-- ═══════════════════════════════════════
         KPI STRIP
    ═══════════════════════════════════════ --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-sm-4 col-md-2">
            <div class="kpi-mini card border-0 shadow-sm" @click="setFilter('all')"
                 :class="activeKpi==='all' ? 'kpi-active' : ''">
                <div class="card-body p-3 text-center">
                    <div class="fw-bold fs-4 lh-1 text-dark" x-text="counts.total"></div>
                    <small class="text-muted">Total</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-4 col-md-2">
            <div class="kpi-mini card border-0 shadow-sm" @click="setFilter('Pending')"
                 :class="activeKpi==='Pending' ? 'kpi-active border-warning' : ''">
                <div class="card-body p-3 text-center">
                    <div class="fw-bold fs-4 lh-1 text-warning" x-text="counts.pending"></div>
                    <small class="text-muted">Pending</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-4 col-md-2">
            <div class="kpi-mini card border-0 shadow-sm" @click="setFilter('In Review')"
                 :class="activeKpi==='In Review' ? 'kpi-active border-info' : ''">
                <div class="card-body p-3 text-center">
                    <div class="fw-bold fs-4 lh-1 text-info" x-text="counts.inReview"></div>
                    <small class="text-muted">In Review</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-4 col-md-2">
            <div class="kpi-mini card border-0 shadow-sm" @click="setFilter('Eligible')"
                 :class="activeKpi==='Eligible' ? 'kpi-active border-success' : ''">
                <div class="card-body p-3 text-center">
                    <div class="fw-bold fs-4 lh-1 text-success" x-text="counts.eligible"></div>
                    <small class="text-muted">Eligible</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-4 col-md-2">
            <div class="kpi-mini card border-0 shadow-sm" @click="setFilter('Rejected')"
                 :class="activeKpi==='Rejected' ? 'kpi-active border-danger' : ''">
                <div class="card-body p-3 text-center">
                    <div class="fw-bold fs-4 lh-1 text-danger" x-text="counts.rejected"></div>
                    <small class="text-muted">Rejected</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-4 col-md-2">
            <div class="kpi-mini card border-0 shadow-sm">
                <div class="card-body p-3 text-center">
                    <div class="fw-bold fs-4 lh-1 text-primary" x-text="counts.completionPct + '%'"></div>
                    <small class="text-muted">Screened</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Progress Bar --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between small mb-2">
                <span class="fw-medium">Screening Progress</span>
                <span class="text-muted">
                    <span x-text="counts.eligible + counts.rejected"></span> of
                    <span x-text="counts.total"></span> applications screened
                </span>
            </div>
            <div class="progress" style="height:10px; border-radius:8px;">
                <div class="progress-bar bg-success" style="border-radius:8px 0 0 8px;"
                     :style="'width:' + (counts.total ? Math.round(counts.eligible/counts.total*100) : 0) + '%'"></div>
                <div class="progress-bar bg-danger"
                     :style="'width:' + (counts.total ? Math.round(counts.rejected/counts.total*100) : 0) + '%'"></div>
                <div class="progress-bar bg-info"
                     :style="'width:' + (counts.total ? Math.round(counts.inReview/counts.total*100) : 0) + '%'"></div>
            </div>
            <div class="d-flex gap-3 mt-2 small text-muted flex-wrap">
                <span><span class="badge bg-success me-1">&nbsp;</span>Eligible</span>
                <span><span class="badge bg-danger me-1">&nbsp;</span>Rejected</span>
                <span><span class="badge bg-info me-1">&nbsp;</span>In Review</span>
                <span><span class="badge bg-warning me-1">&nbsp;</span>Pending</span>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════
         FILTERS + SEARCH
    ═══════════════════════════════════════ --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-medium mb-1">Search</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0"
                               placeholder="Enterprise name, ID, owner…"
                               x-model="search" @input="applyFilters()">
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-medium mb-1">Status</label>
                    <select class="form-select form-select-sm" x-model="filterStatus" @change="applyFilters()">
                        <option value="">All</option>
                        <option value="Pending">Pending</option>
                        <option value="In Review">In Review</option>
                        <option value="Eligible">Eligible</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-medium mb-1">Sector</label>
                    <select class="form-select form-select-sm" x-model="filterSector" @change="applyFilters()">
                        <option value="">All Sectors</option>
                        <option>Agriculture</option>
                        <option>Technology</option>
                        <option>Manufacturing</option>
                        <option>Retail</option>
                        <option>Textile</option>
                        <option>Food & Beverage</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-medium mb-1">District</label>
                    <select class="form-select form-select-sm" x-model="filterDistrict" @change="applyFilters()">
                        <option value="">All Districts</option>
                        <option>Maseru</option>
                        <option>Leribe</option>
                        <option>Berea</option>
                        <option>Mafeteng</option>
                        <option>Mohale's Hoek</option>
                    </select>
                </div>
                <div class="col-6 col-md-2 d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary w-100" @click="resetFilters()">
                        <i class="bi bi-x-circle me-1"></i>Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════
         APPLICATIONS TABLE
    ═══════════════════════════════════════ --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-3">
                <div class="form-check mb-0">
                    <input class="form-check-input" type="checkbox" id="selectAll"
                           @change="toggleSelectAll($event.target.checked)"
                           :checked="selected.length === filtered.length && filtered.length > 0">
                    <label class="form-check-label small fw-medium" for="selectAll">
                        Select All
                    </label>
                </div>
                <h6 class="fw-bold mb-0">Screening Queue</h6>
                <span class="badge bg-primary rounded-pill" x-text="filtered.length + ' applications'"></span>
            </div>
            <div class="d-flex align-items-center gap-2 small text-muted">
                <span>Sort by:</span>
                <select class="form-select form-select-sm" style="width:auto;" x-model="sortBy" @change="applyFilters()">
                    <option value="submittedAt">Submission Date</option>
                    <option value="enterprise">Enterprise Name</option>
                    <option value="score">Doc Score</option>
                </select>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3 py-3" style="width:40px;"></th>
                            <th class="py-3">Application</th>
                            <th class="py-3">Sector</th>
                            <th class="py-3">District</th>
                            <th class="py-3">Stage</th>
                            <th class="py-3 text-center">Documents</th>
                            <th class="py-3">Submitted</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="app in paginated" :key="app.id">
                            <tr :class="selected.includes(app.id) ? 'table-primary' : ''">
                                <td class="px-3">
                                    <input class="form-check-input" type="checkbox"
                                           :value="app.id" x-model="selected">
                                </td>
                                <td class="py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="app-avatar"
                                             :class="avatarColor(app.sector)">
                                            <span x-text="initials(app.enterprise)"></span>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark" x-text="app.enterprise"></div>
                                            <div class="text-muted" style="font-size:0.72rem;">
                                                <span x-text="app.id"></span> ·
                                                <span x-text="app.owner"></span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border" x-text="app.sector"></span>
                                </td>
                                <td x-text="app.district"></td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary"
                                          x-text="app.stage"></span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <div class="doc-dot"
                                             :class="app.docs.registration ? 'bg-success' : 'bg-danger'"
                                             title="Business Registration"></div>
                                        <div class="doc-dot"
                                             :class="app.docs.tax ? 'bg-success' : 'bg-danger'"
                                             title="Tax Clearance"></div>
                                        <div class="doc-dot"
                                             :class="app.docs.id ? 'bg-success' : 'bg-warning'"
                                             title="Owner ID"></div>
                                        <div class="doc-dot"
                                             :class="app.docs.plan ? 'bg-success' : 'bg-secondary'"
                                             title="Business Plan"></div>
                                    </div>
                                    <div class="text-muted mt-1" style="font-size:0.7rem;"
                                         x-text="docScore(app.docs) + '/4 docs'"></div>
                                </td>
                                <td x-text="app.submittedAt"></td>
                                <td>
                                    <span class="badge rounded-pill" :class="statusBadge(app.status)"
                                          x-text="app.status"></span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button class="btn btn-sm btn-outline-primary py-1 px-2"
                                                @click="openReview(app)" title="Review">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success py-1 px-2"
                                                @click="quickMark(app, 'Eligible')" title="Mark Eligible"
                                                x-show="app.status !== 'Eligible'">
                                            <i class="bi bi-check2"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger py-1 px-2"
                                                @click="openReject(app)" title="Reject"
                                                x-show="app.status !== 'Rejected'">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="filtered.length === 0">
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                                No applications match your filters.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex flex-wrap align-items-center justify-content-between px-4 py-3 border-top gap-2">
                <small class="text-muted">
                    Showing <span x-text="(currentPage-1)*perPage+1"></span>–<span x-text="Math.min(currentPage*perPage, filtered.length)"></span>
                    of <span x-text="filtered.length"></span> applications
                </small>
                <div class="d-flex align-items-center gap-2">
                    <select class="form-select form-select-sm" style="width:auto;" x-model="perPage" @change="currentPage=1">
                        <option value="10">10 / page</option>
                        <option value="20">20 / page</option>
                        <option value="50">50 / page</option>
                    </select>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item" :class="currentPage===1 ? 'disabled' : ''">
                                <button class="page-link" @click="currentPage--">
                                    <i class="bi bi-chevron-left"></i>
                                </button>
                            </li>
                            <template x-for="p in totalPages" :key="p">
                                <li class="page-item" :class="currentPage===p ? 'active' : ''">
                                    <button class="page-link" @click="currentPage=p" x-text="p"></button>
                                </li>
                            </template>
                            <li class="page-item" :class="currentPage===totalPages ? 'disabled' : ''">
                                <button class="page-link" @click="currentPage++">
                                    <i class="bi bi-chevron-right"></i>
                                </button>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>


    {{-- ═══════════════════════════════════════
         FULL REVIEW PANEL (right drawer)
    ═══════════════════════════════════════ --}}
    <div class="panel-backdrop" x-show="showReview" x-transition.opacity></div>
    <div class="review-panel" x-show="showReview" x-transition.opacity>
        <div class="review-panel-inner" x-if="activeApp">

            {{-- Panel Header --}}
            <div class="d-flex align-items-start justify-content-between p-4 border-bottom">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge rounded-pill"
                              :class="statusBadge(activeApp?.status)"
                              x-text="activeApp?.status"></span>
                        <span class="text-muted small" x-text="activeApp?.id"></span>
                    </div>
                    <h5 class="fw-bold mb-0" x-text="activeApp?.enterprise"></h5>
                    <small class="text-muted" x-text="activeApp?.owner + ' · ' + activeApp?.sector + ' · ' + activeApp?.district"></small>
                </div>
                <button class="btn btn-sm btn-light rounded-circle p-2 lh-1" @click="showReview=false">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            {{-- Tabs --}}
            <div class="px-4 pt-3 border-bottom">
                <ul class="nav nav-tabs border-0 gap-1">
                    <template x-for="tab in ['Overview','Documents','Eligibility','History']">
                        <li class="nav-item">
                            <button class="nav-link px-3 py-2 small fw-medium"
                                    :class="activeTab === tab ? 'active text-primary border-bottom border-primary border-2' : 'text-muted border-0'"
                                    @click="activeTab = tab"
                                    x-text="tab"></button>
                        </li>
                    </template>
                </ul>
            </div>

            {{-- Tab Content --}}
            <div class="p-4 overflow-auto flex-grow-1">

                {{-- OVERVIEW TAB --}}
                <div x-show="activeTab === 'Overview'">
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="info-block">
                                <div class="info-label">Business Stage</div>
                                <div class="info-val" x-text="activeApp?.stage"></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-block">
                                <div class="info-label">Years Operating</div>
                                <div class="info-val" x-text="activeApp?.yearsOp + ' years'"></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-block">
                                <div class="info-label">No. of Employees</div>
                                <div class="info-val" x-text="activeApp?.employees"></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-block">
                                <div class="info-label">Annual Revenue (Est.)</div>
                                <div class="info-val" x-text="'M ' + activeApp?.revenue"></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-block">
                                <div class="info-label">Owner Gender</div>
                                <div class="info-val" x-text="activeApp?.gender"></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-block">
                                <div class="info-label">Owner Age</div>
                                <div class="info-val" x-text="activeApp?.age + ' yrs'"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-block">
                                <div class="info-label">Business Description</div>
                                <div class="info-val" x-text="activeApp?.bizDesc"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-block">
                                <div class="info-label">Why LEHSFF?</div>
                                <div class="info-val" x-text="activeApp?.whyLehsff"></div>
                            </div>
                        </div>
                    </div>

                    {{-- PDO Flags --}}
                    <div class="mb-4">
                        <div class="small fw-semibold text-muted mb-2">PDO Target Group Flags</div>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge border px-3 py-2"
                                  :class="activeApp?.isWoman ? 'bg-pink text-white' : 'bg-light text-muted'">
                                <i class="bi bi-gender-female me-1"></i>Women-owned
                            </span>
                            <span class="badge border px-3 py-2"
                                  :class="activeApp?.isYouth ? 'bg-success bg-opacity-20 text-white' : 'bg-light text-muted'">
                                <i class="bi bi-person-fill me-1"></i>Youth-owned
                            </span>
                            <span class="badge border px-3 py-2"
                                  :class="activeApp?.isRural ? 'bg-warning bg-opacity-20 text-warning' : 'bg-light text-muted'">
                                <i class="bi bi-tree me-1"></i>Rural-based
                            </span>
                        </div>
                    </div>
                </div>

                {{-- DOCUMENTS TAB --}}
                <div x-show="activeTab === 'Documents'">
                    <div class="d-flex flex-column gap-3">
                        <template x-for="doc in docList">
                            <div class="d-flex align-items-center justify-content-between p-3 rounded-3 border"
                                 :class="doc.present ? 'border-success bg-success bg-opacity-10' : 'border-danger bg-danger bg-opacity-10'">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="bi fs-4" :class="doc.present ? 'bi-file-earmark-check-fill text-success' : 'bi-file-earmark-x-fill text-danger'"></i>
                                    <div>
                                        <div class="fw-medium small text-white" x-text="doc.name"></div>
                                        <div class="text-muted text-white" style="font-size:0.72rem;"
                                             x-text="doc.present ? 'Uploaded · ' + doc.size : 'Not submitted'"></div>
                                    </div>
                                </div>
                                <div class="d-flex gap-2" x-show="doc.present">
                                    <button class="btn btn-sm btn-primary py-1 px-2">
                                        <i class="bi bi-eye me-1"></i>View
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary py-1 px-2">
                                        <i class="bi bi-download"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div class="alert alert-warning small mt-3 d-flex gap-2 align-items-start">
                        <i class="bi bi-exclamation-triangle-fill mt-1 flex-shrink-0"></i>
                        <span>Missing documents may result in automatic rejection at screening. Ensure all required documents are uploaded before marking eligible.</span>
                    </div>
                </div>

                {{-- ELIGIBILITY TAB --}}
                <div x-show="activeTab === 'Eligibility'">
                    <div class="mb-4">
                        <div class="small fw-semibold text-muted mb-3">Eligibility Checklist</div>
                        <div class="d-flex flex-column gap-2">
                            <template x-for="(criterion, idx) in eligibilityChecklist">
                                <div class="d-flex align-items-start gap-3 p-3 rounded-3 border"
                                     :class="criterion.passed === true ? 'bg-success bg-opacity-10 border-success' :
                                             criterion.passed === false ? 'bg-danger bg-opacity-10 border-danger' : 'bg-light'">
                                    <div class="pt-1">
                                        <input class="form-check-input" type="checkbox"
                                               :id="'crit'+idx"
                                               :checked="criterion.passed === true"
                                               @change="criterion.passed = $event.target.checked ? true : false">
                                    </div>
                                    <div class="flex-grow-1">
                                        <label class="form-check-label small fw-medium" :for="'crit'+idx"
                                               x-text="criterion.label"></label>
                                        <div class="text-muted" style="font-size:0.72rem;" x-text="criterion.hint"></div>
                                    </div>
                                    <i class="bi fs-5 flex-shrink-0"
                                       :class="criterion.passed === true ? 'bi-check-circle-fill text-success' :
                                               criterion.passed === false ? 'bi-x-circle-fill text-danger' :
                                               'bi-circle text-muted'"></i>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium small">Screening Notes</label>
                        <textarea class="form-control small" rows="3"
                                  placeholder="Add notes about this application's eligibility…"
                                  x-model="screeningNotes"></textarea>
                    </div>
                    <div x-show="showRejectionReason" class="mb-3">
                        <label class="form-label fw-medium small text-danger">
                            Rejection Reason <span class="text-danger">*</span>
                        </label>
                        <select class="form-select form-select-sm mb-2" x-model="rejectionCategory">
                            <option value="">— Select primary reason —</option>
                            <option value="incomplete_docs">Incomplete / missing documents</option>
                            <option value="not_registered">Business not formally registered</option>
                            <option value="outside_sector">Outside target sectors</option>
                            <option value="outside_geography">Outside geographic focus</option>
                            <option value="revenue_too_high">Revenue exceeds programme threshold</option>
                            <option value="duplicate">Duplicate application</option>
                            <option value="other">Other (specify below)</option>
                        </select>
                        <textarea class="form-control small" rows="2"
                                  placeholder="Provide a specific rejection reason that will be communicated to the applicant…"
                                  x-model="rejectionDetail"></textarea>
                    </div>
                </div>

                {{-- HISTORY TAB --}}
                <div x-show="activeTab === 'History'">
                    <div class="timeline">
                        <template x-for="(event, idx) in activeApp?.history || []">
                            <div class="timeline-item d-flex gap-3">
                                <div class="timeline-dot" :class="event.color"></div>
                                <div class="flex-grow-1 pb-3">
                                    <div class="fw-semibold small" x-text="event.action"></div>
                                    <div class="text-muted" style="font-size:0.72rem;">
                                        <span x-text="event.by"></span> ·
                                        <span x-text="event.at"></span>
                                    </div>
                                    <div class="text-dark small mt-1" x-show="event.note" x-text="event.note"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

            </div>{{-- end tab content --}}

            {{-- Panel Footer --}}
            <div class="p-4 border-top bg-light d-flex flex-wrap gap-2 justify-content-between align-items-center">
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary"
                            @click="navigate(-1)"
                            :disabled="appIndex <= 0">
                        <i class="bi bi-chevron-left me-1"></i>Prev
                    </button>
                    <button class="btn btn-sm btn-outline-secondary"
                            @click="navigate(1)"
                            :disabled="appIndex >= filtered.length - 1">
                        Next<i class="bi bi-chevron-right ms-1"></i>
                    </button>
                    <small class="text-muted align-self-center"
                           x-text="(appIndex+1) + ' of ' + filtered.length"></small>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-danger px-3"
                            @click="showRejectionReason=true; activeTab='Eligibility'"
                            x-show="activeApp?.status !== 'Rejected'">
                        <i class="bi bi-x-circle me-1"></i>Reject
                    </button>
                    <button class="btn btn-sm btn-success px-3"
                            @click="markEligible()"
                            x-show="activeApp?.status !== 'Eligible'">
                        <i class="bi bi-check-circle me-1"></i>Mark Eligible
                    </button>
                    <button class="btn btn-sm btn-danger px-3"
                            @click="submitRejection()"
                            x-show="showRejectionReason">
                        <i class="bi bi-send me-1"></i>Confirm Rejection
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════
     STYLES
═══════════════════════════════════════ --}}
<style>
.screening-page .card { border-radius: 14px !important; }
.screening-page .card-header { border-radius: 14px 14px 0 0 !important; }

/* KPI mini cards */
.screening-page .kpi-mini {
    border-radius: 12px !important;
    cursor: pointer;
    transition: all 0.2s;
    border: 2px solid transparent !important;
}
.screening-page .kpi-mini:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.1) !important; }
.screening-page .kpi-mini.kpi-active { border-color: #0d6efd !important; box-shadow: 0 0 0 3px rgba(13,110,253,0.15) !important; }

/* Avatar */
.screening-page .app-avatar {
    width: 36px; height: 36px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.75rem; font-weight: 700; flex-shrink: 0;
}
.av-blue   { background: #dbeafe; color: #1d4ed8; }
.av-green  { background: #dcfce7; color: #15803d; }
.av-orange { background: #ffedd5; color: #c2410c; }
.av-purple { background: #ede9fe; color: #7c3aed; }
.av-pink   { background: #fce7f3; color: #be185d; }
.av-teal   { background: #ccfbf1; color: #0f766e; }

/* Doc dots */
.doc-dot {
    width: 10px; height: 10px; border-radius: 50%;
    flex-shrink: 0;
}

/* Review panel */
.panel-backdrop {
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.45);
    z-index: 1040;
}
.review-panel {
    position: fixed; top: 0; right: 0; bottom: 0;
    width: 100%; max-width: 640px;
    z-index: 1050;
    background: #fff;
    box-shadow: -4px 0 32px rgba(0,0,0,0.15);
    display: flex; flex-direction: column;
}
.review-panel-inner {
    display: flex; flex-direction: column; height: 100%;
}
@media (max-width: 576px) {
    .review-panel { max-width: 100%; }
}

/* Info blocks */
.info-block {
    background: #f8f9fa; border-radius: 10px; padding: 12px 14px;
    height: 100%;
}
.info-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.06em; color: #6c757d; font-weight: 600; margin-bottom: 4px; }
.info-val { font-size: 0.875rem; font-weight: 600; color: #212529; }

/* Timeline */
.timeline-item { position: relative; }
.timeline-dot {
    width: 12px; height: 12px; border-radius: 50%;
    flex-shrink: 0; margin-top: 4px;
    position: relative; z-index: 1;
}
.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: 5px; top: 18px; bottom: 0;
    width: 2px; background: #e9ecef;
}
.bg-pink { background-color: #ec4899; }

/* Nav tabs override */
.review-panel .nav-tabs { border-bottom: none; }
.review-panel .nav-link { border: none !important; border-bottom: 2px solid transparent !important; border-radius: 0 !important; }
.review-panel .nav-link.active { border-bottom-color: #0d6efd !important; }
</style>

{{-- ═══════════════════════════════════════
     ALPINE JS
═══════════════════════════════════════ --}}
<script>
function screeningApp() {
    return {
        // ── State ──
        search: '', filterStatus: '', filterSector: '', filterDistrict: '',
        activeKpi: 'all', sortBy: 'submittedAt',
        showReview: false, showBulk: false, showToast: false,
        toastMsg: '', toastType: 'success',
        activeApp: null, appIndex: 0,
        activeTab: 'Overview',
        screeningNotes: '',
        showRejectionReason: false,
        rejectionCategory: '', rejectionDetail: '',
        selected: [], bulkAction: '', bulkReason: '',
        currentPage: 1, perPage: 10,

        eligibilityChecklist: [
            { label: 'Business is formally registered',              hint: 'Valid registration certificate present',              passed: null },
            { label: 'Valid Tax Clearance Certificate submitted',     hint: 'Not expired at time of submission',                  passed: null },
            { label: 'Owner national ID verified',                   hint: 'Clear legible copy submitted',                       passed: null },
            { label: 'Business operates in a target sector',         hint: 'Aligned with NSDP II priority sectors',              passed: null },
            { label: 'Business is within the geographic focus area', hint: 'Registered or operating in Lesotho',                 passed: null },
            { label: 'Application form fully completed',             hint: 'All mandatory fields filled',                        passed: null },
            { label: 'No duplicate application for this call',       hint: 'One application per enterprise per call',            passed: null },
            { label: 'Business is not in a conflict of interest',    hint: 'Owner not related to LEHSFF/CAFI programme staff',   passed: null },
        ],

        docList: [
            { name: 'Business Registration Certificate', present: true,  size: '245 KB' },
            { name: 'Tax Clearance Certificate',         present: true,  size: '189 KB' },
            { name: 'Owner National ID',                 present: true,  size: '1.2 MB' },
            { name: 'Business Plan / Pitch Deck',        present: false, size: '' },
        ],

        // ── Applications data ──
        applications: [
            { id:'APP-001', enterprise:'MoroAgri Basotho', owner:'Limpho Mokoena', sector:'Agriculture', district:'Maseru', stage:'Registered Startup', status:'Eligible', submittedAt:'01 Mar 2025', gender:'Female', age:28, yearsOp:2, employees:4, revenue:'120,000', isWoman:true, isYouth:true, isRural:false, bizDesc:'Hydroponic vegetable farming using recycled water systems targeting Maseru hotels and supermarkets.', whyLehsff:'Need business skills and access to markets.', docs:{registration:true,tax:true,id:true,plan:true}, history:[{action:'Application Submitted',by:'Limpho Mokoena',at:'01 Mar 2025',note:'',color:'bg-primary'},{action:'Marked Eligible',by:'John Procure',at:'03 Mar 2025',note:'All documents verified.',color:'bg-success'}] },
            { id:'APP-002', enterprise:'Lesotho Stitch Co.', owner:'Thabo Ramokhele', sector:'Textile', district:'Leribe', stage:'Formally Registered', status:'Pending', submittedAt:'02 Mar 2025', gender:'Male', age:35, yearsOp:3, employees:8, revenue:'250,000', isWoman:false, isYouth:false, isRural:false, bizDesc:'Garment manufacturing for export to South Africa and regional markets.', whyLehsff:'Need access to finance and export certifications.', docs:{registration:true,tax:true,id:true,plan:false}, history:[{action:'Application Submitted',by:'Thabo Ramokhele',at:'02 Mar 2025',note:'',color:'bg-primary'}] },
            { id:'APP-003', enterprise:'TechForward LS', owner:'Palesa Nthako', sector:'Technology', district:'Maseru', stage:'Non-Registered Startup', status:'In Review', submittedAt:'03 Mar 2025', gender:'Female', age:24, yearsOp:1, employees:2, revenue:'45,000', isWoman:true, isYouth:true, isRural:false, bizDesc:'Mobile application for rural farmers to access weather data and market prices.', whyLehsff:'Need technical mentorship and incubation support.', docs:{registration:false,tax:false,id:true,plan:true}, history:[{action:'Application Submitted',by:'Palesa Nthako',at:'03 Mar 2025',note:'',color:'bg-primary'},{action:'Moved to In Review',by:'John Procure',at:'04 Mar 2025',note:'Registration docs missing – requested from applicant.',color:'bg-info'}] },
            { id:'APP-004', enterprise:'Khomo Foods Ltd', owner:'Retselisitsoe Mofolo', sector:'Food & Beverage', district:'Berea', stage:'Formally Registered', status:'Rejected', submittedAt:'03 Mar 2025', gender:'Male', age:42, yearsOp:5, employees:12, revenue:'980,000', isWoman:false, isYouth:false, isRural:false, bizDesc:'Processing and packaging of local dairy products for retail distribution.', whyLehsff:'Expand production capacity.', docs:{registration:true,tax:true,id:true,plan:true}, history:[{action:'Application Submitted',by:'Retselisitsoe Mofolo',at:'03 Mar 2025',note:'',color:'bg-primary'},{action:'Rejected at Screening',by:'John Procure',at:'05 Mar 2025',note:'Revenue exceeds programme threshold for early-stage enterprises.',color:'bg-danger'}] },
            { id:'APP-005', enterprise:'Naledi Weave Studio', owner:'Naledi Sello', sector:'Textile', district:'Mafeteng', stage:'Formally Registered', status:'Eligible', submittedAt:'04 Mar 2025', gender:'Female', age:31, yearsOp:2, employees:5, revenue:'95,000', isWoman:true, isYouth:false, isRural:true, bizDesc:'Handwoven Basotho blanket and tapestry products for domestic and diaspora markets.', whyLehsff:'Market development and e-commerce skills.', docs:{registration:true,tax:true,id:true,plan:true}, history:[{action:'Application Submitted',by:'Naledi Sello',at:'04 Mar 2025',note:'',color:'bg-primary'},{action:'Marked Eligible',by:'Jane Screen',at:'06 Mar 2025',note:'All documents verified, meets all criteria.',color:'bg-success'}] },
            { id:'APP-006', enterprise:'Molapo Tech Solutions', owner:'Tšepiso Molapo', sector:'Technology', district:'Maseru', stage:'Non-Registered Startup', status:'Pending', submittedAt:'05 Mar 2025', gender:'Male', age:26, yearsOp:1, employees:1, revenue:'18,000', isWoman:false, isYouth:true, isRural:false, bizDesc:'SaaS platform for small retailers to manage inventory and generate invoices via mobile.', whyLehsff:'Business development support and formalisation.', docs:{registration:false,tax:false,id:true,plan:true}, history:[{action:'Application Submitted',by:'Tšepiso Molapo',at:'05 Mar 2025',note:'',color:'bg-primary'}] },
            { id:'APP-007', enterprise:'Selemo Organics', owner:'Mamello Tsita', sector:'Agriculture', district:"Mohale's Hoek", stage:'Formally Registered', status:'Pending', submittedAt:'06 Mar 2025', gender:'Female', age:38, yearsOp:3, employees:6, revenue:'145,000', isWoman:true, isYouth:false, isRural:true, bizDesc:'Organic herb and spice farming, processing and packaging for retail and export.', whyLehsff:'Export market access and certification.', docs:{registration:true,tax:true,id:true,plan:false}, history:[{action:'Application Submitted',by:'Mamello Tsita',at:'06 Mar 2025',note:'',color:'bg-primary'}] },
            { id:'APP-008', enterprise:'Lerato Crafts &amp; Arts', owner:'Lerato Nkosi', sector:'Manufacturing', district:'Berea', stage:'Registered Startup', status:'In Review', submittedAt:'07 Mar 2025', gender:'Female', age:29, yearsOp:2, employees:3, revenue:'62,000', isWoman:true, isYouth:true, isRural:false, bizDesc:'Handcrafted leather goods and traditional Basotho jewellery for local and online markets.', whyLehsff:'E-commerce setup and export documentation.', docs:{registration:true,tax:false,id:true,plan:true}, history:[{action:'Application Submitted',by:'Lerato Nkosi',at:'07 Mar 2025',note:'',color:'bg-primary'},{action:'Moved to In Review',by:'John Procure',at:'08 Mar 2025',note:'Tax clearance pending. Awaiting updated certificate.',color:'bg-info'}] },
        ],

        filtered: [],

        get counts() {
            const all = this.applications;
            const screened = all.filter(a => a.status==='Eligible'||a.status==='Rejected').length;
            return {
                total: all.length,
                pending:   all.filter(a => a.status==='Pending').length,
                inReview:  all.filter(a => a.status==='In Review').length,
                eligible:  all.filter(a => a.status==='Eligible').length,
                rejected:  all.filter(a => a.status==='Rejected').length,
                completionPct: all.length ? Math.round(screened/all.length*100) : 0,
            };
        },

        get totalPages() {
            return Math.max(1, Math.ceil(this.filtered.length / this.perPage));
        },

        get paginated() {
            const s = (this.currentPage - 1) * parseInt(this.perPage);
            return this.filtered.slice(s, s + parseInt(this.perPage));
        },

        init() { this.applyFilters(); },

        setFilter(status) {
            this.activeKpi = status;
            this.filterStatus = status === 'all' ? '' : status;
            this.applyFilters();
        },

        applyFilters() {
            const s = this.search.toLowerCase();
            let list = this.applications.filter(a => {
                const ms = !s || a.enterprise.toLowerCase().includes(s) ||
                           a.owner.toLowerCase().includes(s) || a.id.toLowerCase().includes(s);
                const mst = !this.filterStatus  || a.status  === this.filterStatus;
                const mse = !this.filterSector  || a.sector  === this.filterSector;
                const mdi = !this.filterDistrict|| a.district=== this.filterDistrict;
                return ms && mst && mse && mdi;
            });
            if (this.sortBy === 'enterprise') list.sort((a,b) => a.enterprise.localeCompare(b.enterprise));
            else if (this.sortBy === 'score')  list.sort((a,b) => this.docScore(b.docs) - this.docScore(a.docs));
            this.filtered = list;
            this.currentPage = 1;
        },

        resetFilters() {
            this.search=''; this.filterStatus=''; this.filterSector='';
            this.filterDistrict=''; this.activeKpi='all'; this.applyFilters();
        },

        statusBadge(s) {
            return { 'Pending':'bg-warning text-dark', 'In Review':'bg-info text-white',
                     'Eligible':'bg-success text-white', 'Rejected':'bg-danger text-white' }[s] || 'bg-secondary text-white';
        },

        avatarColor(sector) {
            return { 'Agriculture':'av-green', 'Technology':'av-blue', 'Textile':'av-pink',
                     'Manufacturing':'av-orange', 'Food & Beverage':'av-teal', 'Retail':'av-purple' }[sector] || 'av-blue';
        },

        initials(name) {
            return name.split(' ').slice(0,2).map(w => w[0].toUpperCase()).join('');
        },

        docScore(docs) { return Object.values(docs).filter(Boolean).length; },

        openReview(app) {
            this.activeApp = app;
            this.appIndex = this.filtered.indexOf(app);
            this.activeTab = 'Overview';
            this.screeningNotes = '';
            this.showRejectionReason = false;
            this.rejectionCategory = ''; this.rejectionDetail = '';
            this.eligibilityChecklist.forEach(c => c.passed = null);
            this.showReview = true;
        },

        navigate(dir) {
            const newIdx = this.appIndex + dir;
            if (newIdx >= 0 && newIdx < this.filtered.length) {
                this.appIndex = newIdx;
                this.activeApp = this.filtered[newIdx];
                this.showRejectionReason = false;
                this.activeTab = 'Overview';
            }
        },

        quickMark(app, status) {
            app.status = status;
            app.history = app.history || [];
            app.history.push({ action: 'Marked ' + status, by: 'Current Officer',
                               at: new Date().toLocaleDateString('en-GB'), note: '', color: 'bg-success' });
            this.toast(`"${app.enterprise}" marked as ${status}.`);
            this.applyFilters();
        },

        markEligible() {
            const passed = this.eligibilityChecklist.filter(c => c.passed === true).length;
            const total  = this.eligibilityChecklist.length;
            if (passed < total * 0.75) {
                this.toast(`Please complete the eligibility checklist (${passed}/${total} criteria met).`, 'error');
                this.activeTab = 'Eligibility';
                return;
            }
            this.activeApp.status = 'Eligible';
            this.activeApp.history.push({ action:'Marked Eligible', by:'Current Officer',
                at:new Date().toLocaleDateString('en-GB'), note: this.screeningNotes || 'Eligibility verified.', color:'bg-success' });
            this.toast(`"${this.activeApp.enterprise}" marked Eligible.`);
            this.applyFilters();
        },

        openReject(app) {
            this.openReview(app);
            this.showRejectionReason = true;
            this.activeTab = 'Eligibility';
        },

        submitRejection() {
            if (!this.rejectionCategory) {
                this.toast('Please select a rejection reason.', 'error'); return;
            }
            this.activeApp.status = 'Rejected';
            this.activeApp.history.push({ action:'Rejected at Screening', by:'Current Officer',
                at:new Date().toLocaleDateString('en-GB'),
                note: this.rejectionDetail || this.rejectionCategory, color:'bg-danger' });
            this.showRejectionReason = false;
            this.toast(`"${this.activeApp.enterprise}" rejected. Applicant will be notified.`);
            this.applyFilters();
        },

        toggleSelectAll(val) {
            this.selected = val ? this.paginated.map(a => a.id) : [];
        },

        openBulk() { this.showBulk = true; this.bulkAction = ''; },

        applyBulk() {
            const statusMap = { eligible:'Eligible', review:'In Review', reject:'Rejected' };
            const newStatus = statusMap[this.bulkAction];
            this.applications.forEach(a => {
                if (this.selected.includes(a.id)) {
                    a.status = newStatus;
                    a.history.push({ action: 'Bulk: ' + newStatus, by:'Current Officer',
                        at:new Date().toLocaleDateString('en-GB'),
                        note: this.bulkReason || '', color:'bg-primary' });
                }
            });
            this.toast(`${this.selected.length} application(s) updated to "${newStatus}".`);
            this.selected = [];
            this.showBulk = false;
            this.applyFilters();
        },

        exportList() { this.toast('Export started — file will download shortly.'); },

        toast(msg, type='success') {
            this.toastMsg = msg; this.toastType = type; this.showToast = true;
            setTimeout(() => this.showToast = false, 3500);
        },
    };
}
</script>
    
</x-app-layout>