<x-app-layout>

    <div class="calls-page p-4" x-data="callsApp()">

    {{-- ═══════════════════════════════════════
         PAGE HEADER
    ═══════════════════════════════════════ --}}
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-megaphone-fill text-primary me-2"></i>Calls for Applications
            </h4>
            <p class="text-muted small mb-0">Create and manage incubation programme calls · LEHSFF</p>
        </div>
        <button class="btn btn-primary d-flex align-items-center gap-2 px-4"
                @click="openCreate()">
            <i class="bi bi-plus-circle-fill"></i>
            <span>New Call</span>
        </button>
    </div>

    {{-- ═══════════════════════════════════════
         KPI STRIP
    ═══════════════════════════════════════ --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card kpi-card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="kpi-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-megaphone-fill"></i></div>
                    <div><div class="fw-bold fs-4 lh-1">8</div><small class="text-muted">Total Calls</small></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card kpi-card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="kpi-icon bg-success bg-opacity-10 text-white"><i class="bi bi-broadcast"></i></div>
                    <div><div class="fw-bold fs-4 lh-1">2</div><small class="text-muted">Open Now</small></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card kpi-card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="kpi-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-hourglass-split"></i></div>
                    <div><div class="fw-bold fs-4 lh-1">1</div><small class="text-muted">Draft</small></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card kpi-card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="kpi-icon bg-info bg-opacity-10 text-info"><i class="bi bi-file-earmark-text-fill"></i></div>
                    <div><div class="fw-bold fs-4 lh-1">210</div><small class="text-muted">Total Applications</small></div>
                </div>
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
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0" placeholder="Search by title, cohort…"
                               x-model="search" @input="filterCalls()">
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-medium mb-1">Status</label>
                    <select class="form-select form-select-sm" x-model="filterStatus" @change="filterCalls()">
                        <option value="">All Statuses</option>
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="open">Open</option>
                        <option value="closed">Closed</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-medium mb-1">Cohort</label>
                    <select class="form-select form-select-sm" x-model="filterCohort" @change="filterCalls()">
                        <option value="">All Cohorts</option>
                        <option value="1">Cohort 1</option>
                        <option value="2">Cohort 2</option>
                        <option value="3">Cohort 3</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-medium mb-1">Year</label>
                    <select class="form-select form-select-sm" x-model="filterYear" @change="filterCalls()">
                        <option value="">All Years</option>
                        <option value="2023">2023</option>
                        <option value="2024">2024</option>
                        <option value="2025">2025</option>
                    </select>
                </div>
                <div class="col-6 col-md-2 d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary w-100" @click="resetFilters()">
                        <i class="bi bi-x-circle me-1"></i>Reset
                    </button>
                    <button class="btn btn-sm btn-outline-primary w-100">
                        <i class="bi bi-download me-1"></i>Export
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════
         CALLS TABLE
    ═══════════════════════════════════════ --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
            <h6 class="fw-bold mb-0">All Calls</h6>
            <div class="d-flex align-items-center gap-2">
                <small class="text-muted" x-text="`Showing ${filtered.length} of ${calls.length} calls`"></small>
                <div class="btn-group btn-group-sm ms-2" role="group">
                    <button class="btn btn-outline-secondary" :class="view==='table' ? 'active' : ''"
                            @click="view='table'" title="Table view">
                        <i class="bi bi-table"></i>
                    </button>
                    <button class="btn btn-outline-secondary" :class="view==='grid' ? 'active' : ''"
                            @click="view='grid'" title="Grid view">
                        <i class="bi bi-grid-3x3-gap"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- TABLE VIEW --}}
        <div x-show="view === 'table'" class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">Call Title</th>
                            <th class="py-3">Cohort</th>
                            <th class="py-3">Open Date</th>
                            <th class="py-3">Close Date</th>
                            <th class="py-3 text-center">Applications</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="call in filtered" :key="call.id">
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="fw-semibold text-dark" x-text="call.title"></div>
                                    <div class="text-muted" style="font-size:0.75rem;" x-text="call.description"></div>
                                </td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary"
                                          x-text="'Cohort ' + call.cohort"></span>
                                </td>
                                <td x-text="call.openDate"></td>
                                <td x-text="call.closeDate"></td>
                                <td class="text-center">
                                    <span class="fw-semibold" x-text="call.applications"></span>
                                    <span class="text-muted"> / </span>
                                    <span class="text-muted" x-text="call.target"></span>
                                </td>
                                <td>
                                    <span class="badge rounded-pill"
                                          :class="statusClass(call.status)"
                                          x-text="call.status"></span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button class="btn btn-sm btn-outline-primary py-1 px-2"
                                                @click="openView(call)" title="View details">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary py-1 px-2"
                                                @click="openEdit(call)" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm py-1 px-2"
                                                :class="call.status === 'Draft' ? 'btn-outline-success' : 'btn-outline-warning'"
                                                @click="togglePublish(call)"
                                                :title="call.status === 'Draft' ? 'Publish' : 'Unpublish'">
                                            <i :class="call.status === 'Draft' ? 'bi bi-broadcast' : 'bi bi-pause-circle'"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger py-1 px-2"
                                                @click="confirmDelete(call)" title="Delete">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="filtered.length === 0">
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>
                                No calls found matching your filters.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- GRID VIEW --}}
        <div x-show="view === 'grid'" class="card-body p-4">
            <div class="row g-3">
                <template x-for="call in filtered" :key="call.id">
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="card call-grid-card border shadow-sm h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <span class="badge rounded-pill" :class="statusClass(call.status)" x-text="call.status"></span>
                                    <span class="badge bg-primary bg-opacity-10 text-primary" x-text="'Cohort ' + call.cohort"></span>
                                </div>
                                <h6 class="fw-bold mb-1" x-text="call.title"></h6>
                                <p class="text-muted small mb-3" x-text="call.description"></p>
                                <div class="d-flex flex-column gap-1 mb-3 small text-muted">
                                    <div><i class="bi bi-calendar-event me-2"></i>Opens: <span class="text-dark" x-text="call.openDate"></span></div>
                                    <div><i class="bi bi-calendar-x me-2"></i>Closes: <span class="text-dark" x-text="call.closeDate"></span></div>
                                    <div><i class="bi bi-file-earmark-text me-2"></i>
                                        <span x-text="call.applications"></span> / <span x-text="call.target"></span> applications
                                    </div>
                                </div>
                                {{-- Progress bar --}}
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span class="text-muted">Applications</span>
                                        <span class="fw-medium" x-text="Math.round(call.applications/call.target*100)+'%'"></span>
                                    </div>
                                    <div class="progress" style="height:6px;">
                                        <div class="progress-bar bg-primary"
                                             :style="'width:'+Math.min(100,Math.round(call.applications/call.target*100))+'%'"></div>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-primary flex-fill" @click="openView(call)">
                                        <i class="bi bi-eye me-1"></i>View
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary flex-fill" @click="openEdit(call)">
                                        <i class="bi bi-pencil me-1"></i>Edit
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                <div class="col-12 text-center py-4 text-muted" x-show="filtered.length === 0">
                    <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>No calls found.
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════
         CREATE / EDIT MODAL
    ═══════════════════════════════════════ --}}
    <div class="modal-backdrop-custom" x-show="showForm" x-transition.opacity></div>
    <div class="modal-panel" x-show="showForm" x-transition.opacity>
        <div class="modal-panel-inner">
            {{-- Header --}}
            <div class="d-flex align-items-center justify-content-between p-4 border-bottom">
                <div>
                    <h5 class="fw-bold mb-0" x-text="editMode ? 'Edit Call for Applications' : 'Create New Call'">
                    </h5>
                    <small class="text-muted" x-text="editMode ? 'Update call details and settings' : 'Fill in the details to launch a new incubation call'"></small>
                </div>
                <button class="btn btn-sm btn-light rounded-circle p-2 lh-1" @click="closeForm()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            {{-- Stepper --}}
            <div class="px-4 pt-3 pb-0">
                <div class="d-flex align-items-center gap-0">
                    <template x-for="(step, idx) in steps" :key="idx">
                        <div class="d-flex align-items-center flex-grow-1" :class="idx < steps.length-1 ? '' : 'flex-grow-0'">
                            <div class="step-circle" :class="currentStep > idx ? 'done' : currentStep === idx ? 'active' : 'idle'">
                                <span x-show="currentStep <= idx" x-text="idx+1"></span>
                                <i class="bi bi-check2" x-show="currentStep > idx"></i>
                            </div>
                            <div class="step-label small" :class="currentStep === idx ? 'fw-semibold text-primary' : 'text-muted'" x-text="step"></div>
                            <div class="step-line flex-grow-1" x-show="idx < steps.length-1" :class="currentStep > idx ? 'done' : ''"></div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Form Body --}}
            <div class="p-4 overflow-auto" style="max-height:55vh;">

                {{-- STEP 0: Basic Info --}}
                <div x-show="currentStep === 0">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-medium small">Call Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="e.g. LEHSFF Cohort 3 – Incubation Call 2025"
                                   x-model="form.title">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-medium small">Cohort Reference <span class="text-danger">*</span></label>
                            <select class="form-select" x-model="form.cohort">
                                <option value="">— Select Cohort —</option>
                                <option value="1">Cohort 1</option>
                                <option value="2">Cohort 2</option>
                                <option value="3">Cohort 3</option>
                                <option value="4">Cohort 4</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-medium small">Target No. of Applications</label>
                            <input type="number" class="form-control" placeholder="e.g. 200" x-model="form.target">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium small">Short Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" rows="2"
                                      placeholder="Brief public-facing summary of this call…"
                                      x-model="form.description"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium small">Full Programme Details / Guidelines</label>
                            <textarea class="form-control" rows="4"
                                      placeholder="Detailed overview, objectives, programme structure, and what applicants can expect…"
                                      x-model="form.details"></textarea>
                        </div>
                    </div>
                </div>

                {{-- STEP 1: Eligibility --}}
                <div x-show="currentStep === 1">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-medium small">Eligibility Criteria <span class="text-danger">*</span></label>
                            <textarea class="form-control" rows="3"
                                      placeholder="e.g. Must be a formally registered business, operating for at least 6 months…"
                                      x-model="form.eligibility"></textarea>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-medium small">Target Sectors (multi-select)</label>
                            <select class="form-select" multiple x-model="form.sectors" style="height:140px;">
                                <option value="Agriculture">Agriculture</option>
                                <option value="Technology">Technology</option>
                                <option value="Manufacturing">Manufacturing</option>
                                <option value="Retail">Retail &amp; Trade</option>
                                <option value="Textile">Textile &amp; Garments</option>
                                <option value="Food">Food &amp; Beverage</option>
                                <option value="Health">Health &amp; Wellness</option>
                                <option value="Education">Education</option>
                                <option value="Finance">Finance &amp; Fintech</option>
                            </select>
                            <small class="text-muted">Hold Ctrl / Cmd to select multiple</small>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-medium small">Target Stage of Business</label>
                            <div class="d-flex flex-column gap-2 pt-1">
                                <template x-for="stage in ['Idea','Non-Registered Startup','Formally Registered Startup','Fully Operational']">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" :id="'stage-'+stage"
                                               :value="stage" x-model="form.stages">
                                        <label class="form-check-label small" :for="'stage-'+stage" x-text="stage"></label>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-medium small">Geographic Focus</label>
                            <select class="form-select" x-model="form.geography">
                                <option value="">All Districts</option>
                                <option value="Maseru">Maseru</option>
                                <option value="Leribe">Leribe</option>
                                <option value="Berea">Berea</option>
                                <option value="Mafeteng">Mafeteng</option>
                                <option value="Mohales Hoek">Mohale's Hoek</option>
                                <option value="Quthing">Quthing</option>
                                <option value="Qacha">Qacha's Nek</option>
                                <option value="Mokhotlong">Mokhotlong</option>
                                <option value="Butha Buthe">Butha-Buthe</option>
                                <option value="Thaba Tseka">Thaba-Tseka</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-medium small">Priority Groups</label>
                            <div class="d-flex flex-column gap-2 pt-1">
                                <template x-for="grp in ['Women-owned','Youth-owned','Disability-owned','Rural-based']">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" :id="'grp-'+grp"
                                               :value="grp" x-model="form.priorityGroups">
                                        <label class="form-check-label small" :for="'grp-'+grp" x-text="grp"></label>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- STEP 2: Dates & Schedule --}}
                <div x-show="currentStep === 2">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-medium small">Publication Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" x-model="form.publishDate">
                            <small class="text-muted">Call becomes visible to public on this date</small>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-medium small">Application Window Opens <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" x-model="form.openDate">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-medium small">Application Window Closes <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" x-model="form.closeDate">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-medium small">Expected Incubation Start Date</label>
                            <input type="date" class="form-control" x-model="form.incubationStart">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-medium small">Incubation Duration (months)</label>
                            <select class="form-select" x-model="form.duration">
                                <option value="6">6 months</option>
                                <option value="9">9 months</option>
                                <option value="12">12 months</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-medium small">Allow Late Submissions?</label>
                            <div class="d-flex gap-3 pt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="lateSubmit"
                                           id="lateYes" value="yes" x-model="form.allowLate">
                                    <label class="form-check-label small" for="lateYes">Yes</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="lateSubmit"
                                           id="lateNo" value="no" x-model="form.allowLate">
                                    <label class="form-check-label small" for="lateNo">No</label>
                                </div>
                            </div>
                        </div>
                        {{-- Timeline Preview --}}
                        <div class="col-12 mt-2" x-show="form.publishDate && form.openDate && form.closeDate">
                            <div class="alert alert-info d-flex flex-wrap gap-3 align-items-center small mb-0 p-3">
                                <i class="bi bi-info-circle-fill"></i>
                                <span>
                                    <strong>Timeline:</strong>
                                    Published <span class="fw-semibold" x-text="form.publishDate"></span>
                                    → Opens <span class="fw-semibold" x-text="form.openDate"></span>
                                    → Closes <span class="fw-semibold" x-text="form.closeDate"></span>
                                    <template x-if="form.incubationStart">
                                        → Incubation starts <span class="fw-semibold" x-text="form.incubationStart"></span>
                                    </template>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- STEP 3: Required Documents --}}
                <div x-show="currentStep === 3">
                    <div class="mb-3">
                        <label class="form-label fw-medium small">Required Documents from Applicants</label>
                        <small class="text-muted d-block mb-2">Add documents that enterprises must upload when applying.</small>
                        <div class="d-flex flex-column gap-2" id="docList">
                            <template x-for="(doc, idx) in form.requiredDocs" :key="idx">
                                <div class="d-flex align-items-center gap-2 p-2 rounded-3 bg-light border">
                                    <select class="form-select form-select-sm" style="width:140px;flex-shrink:0;" x-model="doc.type">
                                        <option value="PDF">PDF</option>
                                        <option value="PDF/DOCX">PDF/DOCX</option>
                                        <option value="Image">Image</option>
                                        <option value="Any">Any</option>
                                    </select>
                                    <input type="text" class="form-control form-control-sm flex-grow-1"
                                           placeholder="Document name e.g. Business Registration Certificate"
                                           x-model="doc.name">
                                    <div class="form-check form-switch mb-0 flex-shrink-0">
                                        <input class="form-check-input" type="checkbox" x-model="doc.required" :id="'docReq'+idx">
                                        <label class="form-check-label small" :for="'docReq'+idx">Required</label>
                                    </div>
                                    <button class="btn btn-sm btn-outline-danger py-1 px-2 flex-shrink-0"
                                            @click="form.requiredDocs.splice(idx,1)">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <button class="btn btn-sm btn-outline-primary mt-2"
                                @click="form.requiredDocs.push({name:'',type:'PDF',required:true})">
                            <i class="bi bi-plus-circle me-1"></i>Add Document
                        </button>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label class="form-label fw-medium small">Application Form Sections</label>
                        <small class="text-muted d-block mb-2">Toggle which sections appear on the application form.</small>
                        <div class="row g-2">
                            <template x-for="section in formSections">
                                <div class="col-12 col-md-6">
                                    <div class="form-check form-switch p-3 rounded-3 bg-light border">
                                        <input class="form-check-input" type="checkbox" :id="'sec-'+section.key"
                                               x-model="section.enabled">
                                        <label class="form-check-label small fw-medium" :for="'sec-'+section.key"
                                               x-text="section.label"></label>
                                        <div class="text-muted" style="font-size:0.7rem;" x-text="section.hint"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- STEP 4: Review & Publish --}}
                <div x-show="currentStep === 4">
                    <div class="alert alert-light border mb-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-clipboard-check text-primary me-2"></i>Review Before Publishing</h6>
                        <div class="row g-2 small">
                            <div class="col-12 col-md-6">
                                <div class="text-muted">Title</div>
                                <div class="fw-medium" x-text="form.title || '—'"></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="text-muted">Cohort</div>
                                <div class="fw-medium" x-text="form.cohort ? 'Cohort ' + form.cohort : '—'"></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="text-muted">Open Date</div>
                                <div class="fw-medium" x-text="form.openDate || '—'"></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="text-muted">Close Date</div>
                                <div class="fw-medium" x-text="form.closeDate || '—'"></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="text-muted">Duration</div>
                                <div class="fw-medium" x-text="form.duration + ' months'"></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="text-muted">Required Documents</div>
                                <div class="fw-medium" x-text="form.requiredDocs.length + ' document(s)'"></div>
                            </div>
                            <div class="col-12">
                                <div class="text-muted">Sectors</div>
                                <div class="d-flex flex-wrap gap-1 mt-1">
                                    <template x-for="s in form.sectors">
                                        <span class="badge bg-primary bg-opacity-10 text-primary" x-text="s"></span>
                                    </template>
                                    <span x-show="form.sectors.length === 0" class="text-muted">None selected</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium small">Publish Action</label>
                        <div class="d-flex flex-column gap-2">
                            <div class="form-check p-3 rounded-3 border" :class="form.publishAction==='draft' ? 'border-warning bg-warning bg-opacity-10' : ''">
                                <input class="form-check-input" type="radio" name="publishAction"
                                       id="paDraft" value="draft" x-model="form.publishAction">
                                <label class="form-check-label small fw-semibold" for="paDraft">
                                    <i class="bi bi-file-earmark me-1 text-warning"></i>Save as Draft
                                    <div class="text-muted fw-normal">Save now, publish later manually.</div>
                                </label>
                            </div>
                            <div class="form-check p-3 rounded-3 border" :class="form.publishAction==='publish' ? 'border-success bg-success bg-opacity-10' : ''">
                                <input class="form-check-input" type="radio" name="publishAction"
                                       id="paPublish" value="publish" x-model="form.publishAction">
                                <label class="form-check-label small fw-semibold" for="paPublish">
                                    <i class="bi bi-broadcast me-1 text-success"></i>Publish Immediately
                                    <div class="text-muted fw-normal">Make this call visible to applicants now.</div>
                                </label>
                            </div>
                            <div class="form-check p-3 rounded-3 border" :class="form.publishAction==='schedule' ? 'border-primary bg-primary bg-opacity-10' : ''">
                                <input class="form-check-input" type="radio" name="publishAction"
                                       id="paSchedule" value="schedule" x-model="form.publishAction">
                                <label class="form-check-label small fw-semibold" for="paSchedule">
                                    <i class="bi bi-calendar-check me-1 text-primary"></i>Schedule (auto-publish on publication date)
                                    <div class="text-muted fw-normal">Will publish on <span x-text="form.publishDate || 'the date set in Step 3'"></span>.</div>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="notifyApplicants" x-model="form.notifyApplicants">
                        <label class="form-check-label small" for="notifyApplicants">
                            Notify registered entrepreneurs via email when this call is published
                        </label>
                    </div>
                </div>

            </div>{{-- end form body --}}

            {{-- Footer --}}
            <div class="d-flex align-items-center justify-content-between p-4 border-top bg-light rounded-bottom">
                <button class="btn btn-outline-secondary px-4"
                        @click="currentStep > 0 ? currentStep-- : closeForm()"
                        x-text="currentStep === 0 ? 'Cancel' : '← Back'">
                </button>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary px-3" @click="saveDraft()">
                        <i class="bi bi-floppy me-1"></i>Save Draft
                    </button>
                    <button class="btn btn-primary px-4"
                            @click="currentStep < steps.length-1 ? currentStep++ : submitForm()"
                            x-text="currentStep < steps.length-1 ? 'Next →' : (editMode ? 'Update Call' : 'Create Call')">
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
.calls-page .kpi-card { border-radius: 14px !important; transition: transform 0.2s; }
.calls-page .kpi-card:hover { transform: translateY(-2px); }
.calls-page .kpi-icon { width:44px; height:44px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.1rem; flex-shrink:0; }
.calls-page .card { border-radius: 14px !important; }
.calls-page .card-header { border-radius: 14px 14px 0 0 !important; }
.calls-page .call-grid-card { border-radius: 12px !important; transition: box-shadow 0.2s, transform 0.2s; }
.calls-page .call-grid-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.10) !important; transform: translateY(-2px); }
.calls-page .pipeline-pill { background: #f8f9fa; min-width: 70px; }

/* Slide-in panel */
.modal-backdrop-custom {
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.45);
    z-index: 1040;
}
.modal-panel {
    position: fixed; top: 0; right: 0; bottom: 0;
    width: 100%; max-width: 700px;
    z-index: 1050;
    display: flex;
    flex-direction: column;
    background: #fff;
    box-shadow: -4px 0 32px rgba(0,0,0,0.15);
}
.modal-panel-inner {
    display: flex; flex-direction: column; height: 100%;
}
@media (max-width: 576px) {
    .modal-panel { max-width: 100%; }
}

/* Stepper */
.step-circle {
    width: 28px; height: 28px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.78rem; font-weight: 700; flex-shrink: 0;
    transition: all 0.2s;
}
.step-circle.idle   { background: #e9ecef; color: #6c757d; }
.step-circle.active { background: #0d6efd; color: #fff; box-shadow: 0 0 0 3px rgba(13,110,253,0.25); }
.step-circle.done   { background: #198754; color: #fff; }
.step-label { font-size: 0.72rem; white-space: nowrap; margin: 0 6px; }
.step-line { height: 2px; background: #e9ecef; margin: 0 4px; }
.step-line.done { background: #198754; }
</style>

{{-- ═══════════════════════════════════════
     ALPINE JS DATA
═══════════════════════════════════════ --}}
<script>
function callsApp() {
    return {
        view: 'table',
        search: '',
        filterStatus: '',
        filterCohort: '',
        filterYear: '',
        showForm: false,
        showView: false,
        showDelete: false,
        showToast: false,
        toastMsg: '',
        toastType: 'success',
        activeCall: null,
        deleteTarget: null,
        editMode: false,
        currentStep: 0,
        steps: ['Basic Info', 'Eligibility', 'Dates', 'Documents', 'Review'],
        formSections: [
            { key: 'business_profile',  label: 'Business Profile',      hint: 'Name, registration, sector, stage', enabled: true },
            { key: 'owner_profile',     label: 'Owner / Founder Info',   hint: 'Personal info, gender, age',        enabled: true },
            { key: 'financial_info',    label: 'Financial Information',  hint: 'Revenue, employees, funding need',  enabled: true },
            { key: 'business_plan',     label: 'Business Plan Summary',  hint: 'Problem, solution, market',        enabled: true },
            { key: 'impact_statement',  label: 'Impact Statement',       hint: 'Social and economic impact',       enabled: false },
            { key: 'references',        label: 'References / Endorsements', hint: 'Two referees required',         enabled: false },
        ],
        form: {
            title: '', cohort: '', target: '', description: '', details: '',
            eligibility: '', sectors: [], stages: [], geography: '', priorityGroups: [],
            publishDate: '', openDate: '', closeDate: '', incubationStart: '',
            duration: '6', allowLate: 'no',
            requiredDocs: [
                { name: 'Business Registration Certificate', type: 'PDF', required: true },
                { name: 'Tax Clearance Certificate',         type: 'PDF', required: true },
                { name: 'Owner National ID',                 type: 'Image', required: true },
            ],
            publishAction: 'draft',
            notifyApplicants: true,
        },
        calls: [
            { id:1, title:'LEHSFF Cohort 1 – Incubation Call 2023', cohort:'1', description:'First incubation call targeting high-potential MSMEs.', openDate:'01 Nov 2023', closeDate:'30 Nov 2023', applications:87, target:200, status:'Closed', year:'2023', pipeline:{'Submitted':87,'Screened':72,'Evaluated':60,'Top 20':20,'Top 10':10,'Final':10} },
            { id:2, title:'LEHSFF Cohort 2 – Incubation Call 2024', cohort:'2', description:'Second cycle focusing on women and youth-owned enterprises.', openDate:'01 Jun 2024', closeDate:'30 Jun 2024', applications:113, target:200, status:'Closed', year:'2024', pipeline:{'Submitted':113,'Screened':98,'Evaluated':80,'Top 20':20,'Top 10':10,'Final':10} },
            { id:3, title:'LEHSFF Cohort 3 – Incubation Call 2025', cohort:'3', description:'Third incubation cohort. Open to all NSDP II sectors.', openDate:'01 Feb 2025', closeDate:'28 Feb 2025', applications:210, target:250, status:'Open', year:'2025', pipeline:{'Submitted':210,'Screened':145,'Evaluated':80,'Top 20':0,'Top 10':0,'Final':0} },
            { id:4, title:'LEHSFF Diaspora Engagement Call 2025',   cohort:'3', description:'Special call for diaspora-linked enterprises.', openDate:'15 Mar 2025', closeDate:'15 Apr 2025', applications:34, target:50, status:'Open', year:'2025', pipeline:{'Submitted':34,'Screened':20,'Evaluated':0,'Top 20':0,'Top 10':0,'Final':0} },
            { id:5, title:'LEHSFF Cohort 4 – Draft Call 2025',       cohort:'4', description:'Draft call for upcoming fourth cohort.', openDate:'—', closeDate:'—', applications:0, target:200, status:'Draft', year:'2025', pipeline:{} },
        ],
        filtered: [],

        init() { this.filterCalls(); },

        filterCalls() {
            const s = this.search.toLowerCase();
            this.filtered = this.calls.filter(c => {
                const matchSearch  = !s || c.title.toLowerCase().includes(s) || String(c.cohort).includes(s);
                const matchStatus  = !this.filterStatus  || c.status.toLowerCase() === this.filterStatus;
                const matchCohort  = !this.filterCohort  || c.cohort === this.filterCohort;
                const matchYear    = !this.filterYear    || c.year   === this.filterYear;
                return matchSearch && matchStatus && matchCohort && matchYear;
            });
        },

        resetFilters() {
            this.search = ''; this.filterStatus = ''; this.filterCohort = ''; this.filterYear = '';
            this.filterCalls();
        },

        statusClass(s) {
            const m = { 'Open':'bg-success text-white', 'Closed':'bg-secondary text-white',
                        'Draft':'bg-warning text-dark', 'Published':'bg-primary text-white', 'Archived':'bg-dark text-white' };
            return m[s] || 'bg-light text-muted';
        },

        openCreate() {
            this.editMode = false; this.currentStep = 0;
            this.form = { title:'', cohort:'', target:'', description:'', details:'',
                eligibility:'', sectors:[], stages:[], geography:'', priorityGroups:[],
                publishDate:'', openDate:'', closeDate:'', incubationStart:'',
                duration:'6', allowLate:'no',
                requiredDocs:[
                    { name:'Business Registration Certificate', type:'PDF', required:true },
                    { name:'Tax Clearance Certificate', type:'PDF', required:true },
                    { name:'Owner National ID', type:'Image', required:true },
                ],
                publishAction:'draft', notifyApplicants:true };
            this.showForm = true;
        },

        openEdit(call) {
            this.editMode = true; this.currentStep = 0;
            this.form = { ...call, sectors:[], stages:[], priorityGroups:[],
                requiredDocs:[
                    { name:'Business Registration Certificate', type:'PDF', required:true },
                    { name:'Tax Clearance Certificate', type:'PDF', required:true },
                ],
                publishAction:'draft', notifyApplicants:true,
                publishDate:call.openDate, openDate:call.openDate, closeDate:call.closeDate,
                duration:'6', allowLate:'no', geography:'', details:'', eligibility:'' };
            this.showView = false;
            this.showForm = true;
        },

        openView(call) { this.activeCall = call; this.showView = true; },

        closeForm() { this.showForm = false; },

        saveDraft() {
            this.toast('Draft saved successfully.', 'success');
        },

        submitForm() {
            if (this.editMode) {
                const idx = this.calls.findIndex(c => c.id === this.form.id);
                if (idx > -1) this.calls[idx].title = this.form.title;
                this.toast('Call updated successfully.', 'success');
            } else {
                this.calls.push({
                    id: Date.now(), title: this.form.title, cohort: this.form.cohort,
                    description: this.form.description, openDate: this.form.openDate,
                    closeDate: this.form.closeDate, applications: 0, target: parseInt(this.form.target)||200,
                    status: this.form.publishAction === 'publish' ? 'Open' : 'Draft',
                    year: new Date().getFullYear().toString(), pipeline: {}
                });
                this.toast('Call created successfully!', 'success');
            }
            this.showForm = false;
            this.filterCalls();
        },

        togglePublish(call) {
            call.status = call.status === 'Draft' ? 'Open' : 'Draft';
            this.toast(`Call ${call.status === 'Open' ? 'published' : 'unpublished'}.`, 'success');
            this.filterCalls();
        },

        confirmDelete(call) { this.deleteTarget = call; this.showDelete = true; },

        doDelete() {
            this.calls = this.calls.filter(c => c.id !== this.deleteTarget.id);
            this.showDelete = false;
            this.toast(`"${this.deleteTarget.title}" deleted.`, 'success');
            this.filterCalls();
        },

        toast(msg, type='success') {
            this.toastMsg = msg; this.toastType = type; this.showToast = true;
            setTimeout(() => this.showToast = false, 3500);
        },
    }
}
</script>

</x-app-layout> 