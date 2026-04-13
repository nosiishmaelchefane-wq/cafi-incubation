<x-app-layout>

{{-- resources/views/incubation/cohorts/show.blade.php --}}
{{-- Route: /incubation/cohorts/{cohort} --}}

<div class="cohort-show p-4" x-data="cohortShowApp()">

    {{-- ══════════════════════════════════════
         BREADCRUMB
    ══════════════════════════════════════ --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item">
                <a href="#" class="text-decoration-none text-muted"><i class="bi bi-house-fill" style="font-size:.7rem"></i> Home</a>
            </li>
            <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Incubation</a></li>
            <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Cohorts</a></li>
            <li class="breadcrumb-item active fw-semibold" x-text="cohort.name"></li>
        </ol>
    </nav>

    {{-- ══════════════════════════════════════
         HERO HEADER
    ══════════════════════════════════════ --}}
    <div class="cs-hero mb-4">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">

            {{-- Left: badges + identity --}}
            <div class="d-flex align-items-start gap-4">
                <div class="cs-cohort-number flex-shrink-0" :class="cohortBadgeColor">
                    C<span x-text="cohort.cohortNumber"></span>
                </div>
                <div>
                    <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                        <span class="cs-pill" :class="statusClass(cohort.status)" x-text="cohort.status"></span>
                        <span class="cs-meta-tag"><i class="bi bi-calendar3 me-1"></i><span x-text="cohort.year"></span></span>
                        <span class="cs-meta-tag"><i class="bi bi-hourglass-split me-1"></i><span x-text="cohort.durationMonths"></span> months</span>
                        <span class="cs-meta-tag"><i class="bi bi-geo-alt me-1"></i><span x-text="cohort.geography"></span></span>
                    </div>
                    <h2 class="cs-hero-title mb-1" x-text="cohort.name"></h2>
                    <p class="cs-hero-desc mb-0" x-text="cohort.description"></p>
                </div>
            </div>

            {{-- Right: actions --}}
            <div class="d-flex gap-2 flex-wrap flex-shrink-0">
                <button class="btn cs-btn-ghost btn-sm" @click="exportCohort()">
                    <i class="bi bi-download me-1"></i>Export
                </button>
                <button class="btn cs-btn-ghost btn-sm" @click="showAssign = true">
                    <i class="bi bi-person-plus me-1"></i>Assign ESO
                </button>
                <button class="btn cs-btn-primary btn-sm" @click="showEdit = true">
                    <i class="bi bi-pencil-fill me-1"></i>Edit Cohort
                </button>
            </div>
        </div>

        {{-- KPI Strip --}}
        <div class="cs-hero-divider mb-4"></div>
        <div class="row g-3">
            <div class="col-6 col-md-4 col-xl-2">
                <div class="cs-kpi">
                    <div class="cs-kpi-icon" style="background:rgba(59,130,246,.1);color:#3b82f6;"><i class="bi bi-building"></i></div>
                    <div>
                        <div class="cs-kpi-val text-primary" x-text="cohort.enterprises"></div>
                        <div class="cs-kpi-label">Enterprises</div>
                        <div class="cs-kpi-sub">of <span x-text="cohort.targetEnterprises"></span> target</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="cs-kpi">
                    <div class="cs-kpi-icon" style="background:rgba(6,182,212,.1);color:#06b6d4;"><i class="bi bi-people-fill"></i></div>
                    <div>
                        <div class="cs-kpi-val" style="color:#06b6d4;" x-text="cohort.esoCount"></div>
                        <div class="cs-kpi-label">ESOs Active</div>
                        <div class="cs-kpi-sub">support organisations</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="cs-kpi">
                    <div class="cs-kpi-icon" style="background:rgba(16,185,129,.1);color:#10b981;"><i class="bi bi-file-check-fill"></i></div>
                    <div>
                        <div class="cs-kpi-val text-success" x-text="cohort.reporting.submitted + '/' + cohort.reporting.total"></div>
                        <div class="cs-kpi-label">Reports</div>
                        <div class="cs-kpi-sub">submitted this period</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="cs-kpi">
                    <div class="cs-kpi-icon" style="background:rgba(239,68,68,.1);color:#ef4444;"><i class="bi bi-exclamation-triangle-fill"></i></div>
                    <div>
                        <div class="cs-kpi-val text-danger" x-text="cohort.reporting.overdue"></div>
                        <div class="cs-kpi-label">Overdue</div>
                        <div class="cs-kpi-sub">require follow-up</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="cs-kpi">
                    <div class="cs-kpi-icon" style="background:rgba(245,158,11,.1);color:#f59e0b;"><i class="bi bi-chat-dots-fill"></i></div>
                    <div>
                        <div class="cs-kpi-val text-warning" x-text="cohort.totalEngagements"></div>
                        <div class="cs-kpi-label">Engagements</div>
                        <div class="cs-kpi-sub">sessions logged</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="cs-kpi">
                    <div class="cs-kpi-icon" style="background:rgba(139,92,246,.1);color:#8b5cf6;"><i class="bi bi-graph-up-arrow"></i></div>
                    <div>
                        <div class="cs-kpi-val" style="color:#8b5cf6;" x-text="cohort.graduatedCount"></div>
                        <div class="cs-kpi-label">Graduated</div>
                        <div class="cs-kpi-sub">completed programme</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         MAIN TABS
    ══════════════════════════════════════ --}}
    <div class="cs-tab-bar mb-4">
        <template x-for="t in mainTabs">
            <button class="cs-tab" :class="activeTab===t.key ? 'cs-tab-active' : ''"
                    @click="activeTab=t.key">
                <i class="bi me-1" :class="t.icon"></i>
                <span x-text="t.label"></span>
                <span class="cs-tab-badge" x-show="t.badge" x-text="t.badge"></span>
            </button>
        </template>
    </div>

    {{-- ══════════════════════════════════════
         TAB: OVERVIEW
    ══════════════════════════════════════ --}}
    <div x-show="activeTab === 'overview'">
        <div class="row g-4">

            {{-- LEFT --}}
            <div class="col-12 col-xl-8">

                {{-- ── Linked Call Card ── --}}
                <div class="cs-card mb-4">
                    <div class="cs-card-header">
                        <div class="cs-section-icon" style="background:rgba(234,179,8,.1);color:#ca8a04;">
                            <i class="bi bi-megaphone-fill"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">Linked Call for Applications</div>
                            <div class="cs-section-sub">The published call that generated this cohort's applicant pool</div>
                        </div>
                        <a href="#" class="btn cs-btn-ghost btn-sm ms-auto">
                            <i class="bi bi-box-arrow-up-right me-1"></i>View Call
                        </a>
                    </div>
                    <div class="cs-card-body p-0">
                        <div class="cs-call-banner px-4 py-3">
                            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                                <div class="flex-grow-1">
                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                        <span class="cs-pill" :class="statusClass(cohort.call.status)" x-text="cohort.call.status"></span>
                                        <span class="cs-meta-tag text-muted" style="font-size:.7rem;">
                                            <i class="bi bi-hash me-1"></i>Call ID: <span x-text="cohort.call.id"></span>
                                        </span>
                                    </div>
                                    <h6 class="fw-bold mb-1 text-dark" x-text="cohort.call.title"></h6>
                                    <p class="small text-muted mb-0" x-text="cohort.call.description"></p>
                                </div>
                            </div>
                        </div>

                        {{-- Call meta grid --}}
                        <div class="row g-0 border-top">
                            <div class="col-6 col-md-3 cs-call-stat border-end">
                                <div class="cs-call-stat-label">Published</div>
                                <div class="cs-call-stat-val" x-text="cohort.call.publishDate"></div>
                            </div>
                            <div class="col-6 col-md-3 cs-call-stat border-end">
                                <div class="cs-call-stat-label">Window Opened</div>
                                <div class="cs-call-stat-val text-success" x-text="cohort.call.openDate"></div>
                            </div>
                            <div class="col-6 col-md-3 cs-call-stat border-end">
                                <div class="cs-call-stat-label">Window Closed</div>
                                <div class="cs-call-stat-val text-danger" x-text="cohort.call.closeDate"></div>
                            </div>
                            <div class="col-6 col-md-3 cs-call-stat">
                                <div class="cs-call-stat-label">Total Applications</div>
                                <div class="cs-call-stat-val text-primary fw-bold" x-text="cohort.call.totalApplications"></div>
                            </div>
                        </div>

                        {{-- Application pipeline from call --}}
                        <div class="px-4 py-3 border-top">
                            <div class="cs-section-label mb-3">Application Pipeline (from this call)</div>
                            <div class="d-flex align-items-center gap-1 overflow-auto pb-1">
                                <template x-for="(stage, idx) in cohort.call.pipeline" :key="idx">
                                    <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                        <div class="cs-pipe-step" :class="stage.count > 0 ? 'cs-pipe-on' : 'cs-pipe-off'">
                                            <div class="cs-pipe-num" x-text="stage.count"></div>
                                            <div class="cs-pipe-name" x-text="stage.label"></div>
                                        </div>
                                        <i class="bi bi-chevron-right cs-pipe-arrow"
                                           x-show="idx < cohort.call.pipeline.length - 1"></i>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Target sectors --}}
                        <div class="px-4 py-3 border-top">
                            <div class="cs-section-label mb-2">Target Sectors</div>
                            <div class="d-flex flex-wrap gap-2">
                                <template x-for="sector in cohort.call.sectors">
                                    <span class="cs-sector-tag">
                                        <i class="bi bi-tag-fill me-1" style="font-size:.6rem;"></i>
                                        <span x-text="sector"></span>
                                    </span>
                                </template>
                            </div>
                        </div>

                        {{-- Eligibility snippet --}}
                        <div class="px-4 py-3 border-top">
                            <div class="cs-section-label mb-2">Eligibility Criteria (Summary)</div>
                            <p class="small text-muted mb-0" x-text="cohort.call.eligibilitySummary"></p>
                        </div>
                    </div>
                </div>

                {{-- ── Reporting Compliance ── --}}
                <div class="cs-card mb-4">
                    <div class="cs-card-header">
                        <div class="cs-section-icon" style="background:rgba(16,185,129,.1);color:#10b981;">
                            <i class="bi bi-bar-chart-fill"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">Reporting Compliance</div>
                            <div class="cs-section-sub">Period: <span x-text="cohort.currentPeriod"></span></div>
                        </div>
                        <div class="ms-auto">
                            <span class="fw-bold"
                                  :class="overallCompliance >= 80 ? 'text-success' : overallCompliance >= 50 ? 'text-warning' : 'text-danger'"
                                  x-text="overallCompliance + '%'"></span>
                            <span class="text-muted small ms-1">overall</span>
                        </div>
                    </div>
                    <div class="cs-card-body p-0">
                        <div class="cs-compliance-header">
                            <span>ESO</span>
                            <span>Enterprise Reports</span>
                            <span>ESO Reports</span>
                            <span>Overdue</span>
                            <span>Compliance</span>
                        </div>
                        <template x-for="eso in cohort.esos" :key="eso.id">
                            <div class="cs-compliance-row">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="cs-eso-av" x-text="eso.initials"></div>
                                    <div>
                                        <div class="small fw-semibold text-dark" x-text="eso.name"></div>
                                        <div class="cs-sub-text" x-text="eso.enterprises + ' enterprises'"></div>
                                    </div>
                                </div>
                                <div class="small fw-medium text-center">
                                    <span class="text-success" x-text="eso.enterpriseReports.submitted"></span>
                                    <span class="text-muted">/</span>
                                    <span x-text="eso.enterpriseReports.total"></span>
                                </div>
                                <div class="small fw-medium text-center">
                                    <span class="text-success" x-text="eso.esoReports.submitted"></span>
                                    <span class="text-muted">/</span>
                                    <span x-text="eso.esoReports.total"></span>
                                </div>
                                <div class="text-center">
                                    <span class="small fw-semibold"
                                          :class="eso.overdue > 0 ? 'text-danger' : 'text-muted'"
                                          x-text="eso.overdue"></span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="cs-prog-track flex-grow-1">
                                        <div class="cs-prog-fill"
                                             :style="'width:'+esoCompliance(eso)+'%; background:'+(esoCompliance(eso)>=80?'#10b981':esoCompliance(eso)>=50?'#f59e0b':'#ef4444')">
                                        </div>
                                    </div>
                                    <span class="small fw-bold flex-shrink-0"
                                          :class="esoCompliance(eso)>=80?'text-success':esoCompliance(eso)>=50?'text-warning':'text-danger'"
                                          x-text="esoCompliance(eso)+'%'"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- ── PDO Breakdown ── --}}
                <div class="cs-card mb-4">
                    <div class="cs-card-header">
                        <div class="cs-section-icon" style="background:rgba(236,72,153,.1);color:#ec4899;">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">PDO Indicators</div>
                            <div class="cs-section-sub">Programme Development Objective target group breakdown</div>
                        </div>
                    </div>
                    <div class="cs-card-body p-4">
                        <div class="row g-4">
                            <div class="col-12 col-md-4">
                                <div class="text-center">
                                    <div class="cs-pdo-ring" style="--pct:{{ 58 }}; --color:#ec4899;">
                                        <span x-text="cohort.pdo.women + '%'"></span>
                                    </div>
                                    <div class="fw-semibold small mt-2">Women-owned</div>
                                    <div class="cs-sub-text" x-text="cohort.pdo.womenCount + ' enterprises'"></div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="text-center">
                                    <div class="cs-pdo-ring" style="--pct:{{ 43 }}; --color:#10b981;">
                                        <span x-text="cohort.pdo.youth + '%'"></span>
                                    </div>
                                    <div class="fw-semibold small mt-2">Youth-owned</div>
                                    <div class="cs-sub-text" x-text="cohort.pdo.youthCount + ' enterprises'"></div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="text-center">
                                    <div class="cs-pdo-ring" style="--pct:{{ 31 }}; --color:#f59e0b;">
                                        <span x-text="cohort.pdo.rural + '%'"></span>
                                    </div>
                                    <div class="fw-semibold small mt-2">Rural-based</div>
                                    <div class="cs-sub-text" x-text="cohort.pdo.ruralCount + ' enterprises'"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>{{-- end left col --}}

            {{-- RIGHT SIDEBAR --}}
            <div class="col-12 col-xl-4">

                {{-- Cohort Details DL --}}
                <div class="cs-card mb-4">
                    <div class="cs-card-header">
                        <div class="cs-section-icon"><i class="bi bi-info-circle-fill"></i></div>
                        <div class="fw-semibold">Cohort Details</div>
                    </div>
                    <div class="cs-card-body p-0">
                        <dl class="cs-dl">
                            <div class="cs-dl-row">
                                <dt>Cohort ID</dt>
                                <dd class="fw-bold text-primary" x-text="'COH-00' + cohort.id"></dd>
                            </div>
                            <div class="cs-dl-row">
                                <dt>Cycle</dt>
                                <dd x-text="'Cohort ' + cohort.cohortNumber + ' · ' + cohort.year"></dd>
                            </div>
                            <div class="cs-dl-row">
                                <dt>Status</dt>
                                <dd>
                                    <span class="cs-pill cs-pill-sm" :class="statusClass(cohort.status)"
                                          x-text="cohort.status"></span>
                                </dd>
                            </div>
                            <div class="cs-dl-row">
                                <dt>Start Date</dt>
                                <dd class="fw-semibold text-success" x-text="cohort.startDate"></dd>
                            </div>
                            <div class="cs-dl-row">
                                <dt>End Date</dt>
                                <dd class="fw-semibold text-danger" x-text="cohort.endDate"></dd>
                            </div>
                            <div class="cs-dl-row">
                                <dt>Duration</dt>
                                <dd x-text="cohort.durationMonths + ' months'"></dd>
                            </div>
                            <div class="cs-dl-row">
                                <dt>Target Enterprises</dt>
                                <dd x-text="cohort.targetEnterprises"></dd>
                            </div>
                            <div class="cs-dl-row">
                                <dt>Reporting Frequency</dt>
                                <dd x-text="cohort.reportingFrequency"></dd>
                            </div>
                            <div class="cs-dl-row">
                                <dt>Geography</dt>
                                <dd x-text="cohort.geography"></dd>
                            </div>
                            <div class="cs-dl-row">
                                <dt>Created By</dt>
                                <dd x-text="cohort.createdBy"></dd>
                            </div>
                            <div class="cs-dl-row">
                                <dt>Created On</dt>
                                <dd x-text="cohort.createdAt"></dd>
                            </div>
                            <div class="cs-dl-row border-0">
                                <dt>Last Updated</dt>
                                <dd x-text="cohort.updatedAt"></dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- Linked Call Summary Card --}}
                <div class="cs-card mb-4 cs-linked-call-card">
                    <div class="cs-card-header" style="background:rgba(234,179,8,.06); border-bottom-color:#fef08a;">
                        <div class="cs-section-icon" style="background:rgba(234,179,8,.15);color:#ca8a04;">
                            <i class="bi bi-megaphone-fill"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">Linked Call</div>
                            <div class="cs-section-sub">Call for Applications</div>
                        </div>
                        <span class="cs-pill cs-pill-sm ms-auto"
                              :class="statusClass(cohort.call.status)"
                              x-text="cohort.call.status"></span>
                    </div>
                    <div class="cs-card-body p-4">
                        <div class="fw-bold small text-dark mb-1" x-text="cohort.call.title"></div>
                        <div class="cs-sub-text mb-3" x-text="'ID: ' + cohort.call.id"></div>
                        <div class="d-flex flex-column gap-2 small">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Published</span>
                                <span class="fw-medium" x-text="cohort.call.publishDate"></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Opened</span>
                                <span class="fw-medium text-success" x-text="cohort.call.openDate"></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Closed</span>
                                <span class="fw-medium text-danger" x-text="cohort.call.closeDate"></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Applications Received</span>
                                <span class="fw-bold text-primary" x-text="cohort.call.totalApplications"></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Published By</span>
                                <span x-text="cohort.call.publishedBy"></span>
                            </div>
                        </div>
                        <a href="#" class="btn cs-btn-ghost btn-sm w-100 mt-3">
                            <i class="bi bi-arrow-up-right-circle me-1"></i>Open Full Call
                        </a>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="cs-card mb-4">
                    <div class="cs-card-header">
                        <div class="cs-section-icon"><i class="bi bi-lightning-charge-fill"></i></div>
                        <div class="fw-semibold">Quick Actions</div>
                    </div>
                    <div class="cs-card-body p-3">
                        <div class="d-flex flex-column gap-2">
                            <button class="btn cs-action-link text-start" @click="activeTab='enterprises'">
                                <i class="bi bi-building me-2 text-primary"></i>View Enterprises
                                <i class="bi bi-chevron-right ms-auto text-muted"></i>
                            </button>
                            <button class="btn cs-action-link text-start" @click="activeTab='reporting'">
                                <i class="bi bi-file-earmark-bar-graph-fill me-2 text-success"></i>Reporting Dashboard
                                <i class="bi bi-chevron-right ms-auto text-muted"></i>
                            </button>
                            <button class="btn cs-action-link text-start" @click="activeTab='engagements'">
                                <i class="bi bi-chat-dots-fill me-2 text-warning"></i>Engagement Logs
                                <i class="bi bi-chevron-right ms-auto text-muted"></i>
                            </button>
                            <button class="btn cs-action-link text-start" @click="showAssign=true">
                                <i class="bi bi-person-plus-fill me-2 text-info"></i>Assign ESO / Enterprise
                                <i class="bi bi-chevron-right ms-auto text-muted"></i>
                            </button>
                            <button class="btn cs-action-link text-start" @click="exportCohort()">
                                <i class="bi bi-download me-2 text-secondary"></i>Export Cohort List
                                <i class="bi bi-chevron-right ms-auto text-muted"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Activity Feed --}}
                <div class="cs-card">
                    <div class="cs-card-header">
                        <div class="cs-section-icon"><i class="bi bi-clock-history"></i></div>
                        <div class="fw-semibold">Recent Activity</div>
                    </div>
                    <div class="cs-card-body p-4">
                        <div class="d-flex flex-column gap-0">
                            <template x-for="(ev, idx) in cohort.activity" :key="idx">
                                <div class="cs-activity-item"
                                     :class="idx < cohort.activity.length - 1 ? 'cs-activity-line' : ''">
                                    <div class="cs-activity-dot"
                                         :class="ev.type==='success'?'act-green':ev.type==='warning'?'act-amber':'act-blue'">
                                    </div>
                                    <div class="flex-grow-1 pb-3">
                                        <div class="small fw-medium text-dark" x-text="ev.action"></div>
                                        <div class="cs-sub-text">
                                            <span x-text="ev.by"></span> · <span x-text="ev.at"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>{{-- end overview tab --}}


    {{-- ══════════════════════════════════════
         TAB: ESO & ENTERPRISES
    ══════════════════════════════════════ --}}
    <div x-show="activeTab === 'enterprises'">
        <div class="d-flex align-items-center justify-content-between mb-4 gap-3 flex-wrap">
            <div class="d-flex gap-2">
                <div class="cs-search-wrap">
                    <i class="bi bi-search cs-search-icon"></i>
                    <input type="text" class="cs-search-input" placeholder="Search enterprise…"
                           x-model="entSearch" @input="filterEnterprises()">
                </div>
                <select class="cs-select" style="width:auto;" x-model="entEsoFilter" @change="filterEnterprises()">
                    <option value="">All ESOs</option>
                    <template x-for="eso in cohort.esos">
                        <option :value="eso.id" x-text="eso.name"></option>
                    </template>
                </select>
            </div>
            <button class="btn cs-btn-primary btn-sm" @click="showAssign=true">
                <i class="bi bi-plus-circle-fill me-1"></i>Add Enterprise
            </button>
        </div>

        <div class="row g-4">
            <template x-for="eso in filteredEsos" :key="eso.id">
                <div class="col-12">
                    <div class="cs-card">
                        {{-- ESO Header --}}
                        <div class="cs-card-header">
                            <div class="cs-eso-av-lg"><span x-text="eso.initials"></span></div>
                            <div class="flex-grow-1">
                                <div class="fw-bold" x-text="eso.name"></div>
                                <div class="cs-sub-text">
                                    <span x-text="eso.enterprises"></span> enterprises ·
                                    <span x-text="eso.areaOfFocus"></span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3 text-center">
                                <div>
                                    <div class="fw-bold small text-success"
                                         x-text="eso.enterpriseReports.submitted + '/' + eso.enterpriseReports.total"></div>
                                    <div class="cs-sub-text">Reports</div>
                                </div>
                                <div>
                                    <div class="fw-bold small text-warning" x-text="eso.engagements"></div>
                                    <div class="cs-sub-text">Sessions</div>
                                </div>
                                <span class="cs-pill cs-pill-sm"
                                      :class="eso.active ? 'pill-active' : 'pill-inactive'"
                                      x-text="eso.active ? 'Active' : 'Inactive'"></span>
                            </div>
                        </div>

                        {{-- Enterprise table under this ESO --}}
                        <div class="table-responsive">
                            <table class="table cs-table align-middle mb-0 small">
                                <thead>
                                    <tr>
                                        <th class="px-4" style="min-width:200px;">Enterprise</th>
                                        <th>Owner</th>
                                        <th>Sector</th>
                                        <th>District</th>
                                        <th>Stage</th>
                                        <th class="text-center">PDO</th>
                                        <th class="text-center">Reports</th>
                                        <th>Status</th>
                                        <th class="text-center pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="ent in eso.enterpriseList" :key="ent.id">
                                        <tr>
                                            <td class="px-4 py-3">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="cs-ent-av" :class="sectorColor(ent.sector)">
                                                        <span x-text="initials(ent.name)"></span>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold text-dark" x-text="ent.name"></div>
                                                        <div class="cs-sub-text" x-text="ent.appNumber"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div x-text="ent.owner"></div>
                                                <div class="cs-sub-text" x-text="ent.phone"></div>
                                            </td>
                                            <td>
                                                <span class="cs-sector-tag cs-sector-tag-sm" x-text="ent.sector"></span>
                                            </td>
                                            <td x-text="ent.district"></td>
                                            <td>
                                                <span class="cs-stage-tag" x-text="ent.stage"></span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex gap-1 justify-content-center">
                                                    <span class="cs-pdo" :class="ent.isWoman?'pdo-w':'pdo-off'" title="Women">W</span>
                                                    <span class="cs-pdo" :class="ent.isYouth?'pdo-y':'pdo-off'" title="Youth">Y</span>
                                                    <span class="cs-pdo" :class="ent.isRural?'pdo-r':'pdo-off'" title="Rural">R</span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="fw-semibold"
                                                      :class="ent.reportsSubmitted >= ent.reportsTotal ? 'text-success' : ent.reportsSubmitted > 0 ? 'text-warning' : 'text-danger'"
                                                      x-text="ent.reportsSubmitted + '/' + ent.reportsTotal"></span>
                                            </td>
                                            <td>
                                                <span class="cs-ent-status"
                                                      :class="entStatusClass(ent.incubationStatus)"
                                                      x-text="ent.incubationStatus"></span>
                                            </td>
                                            <td class="text-center pe-4">
                                                <div class="d-flex gap-1 justify-content-center">
                                                    <button class="cs-action-btn" title="View Enterprise">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button class="cs-action-btn" title="View Reports">
                                                        <i class="bi bi-file-earmark-text"></i>
                                                    </button>
                                                    <button class="cs-action-btn" title="Log Engagement">
                                                        <i class="bi bi-chat-dots"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="eso.enterpriseList.length === 0">
                                        <td colspan="9" class="text-center py-4 text-muted small">
                                            No enterprises assigned to this ESO yet.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>{{-- end enterprises tab --}}


    {{-- ══════════════════════════════════════
         TAB: REPORTING
    ══════════════════════════════════════ --}}
    <div x-show="activeTab === 'reporting'">
        <div class="row g-4 mb-4">
            <div class="col-12 col-md-4">
                <div class="cs-card h-100">
                    <div class="cs-card-header">
                        <div class="cs-section-icon" style="background:rgba(16,185,129,.1);color:#10b981;"><i class="bi bi-check-circle-fill"></i></div>
                        <div class="fw-semibold">Enterprise Reports</div>
                    </div>
                    <div class="cs-card-body p-4 text-center">
                        <div class="fw-bold" style="font-size:2.5rem;letter-spacing:-.04em;color:#10b981;"
                             x-text="cohort.reporting.submitted"></div>
                        <div class="text-muted small">of <span x-text="cohort.reporting.total"></span> submitted</div>
                        <div class="cs-prog-track mt-3" style="height:8px;">
                            <div class="cs-prog-fill bg-success"
                                 :style="'width:'+Math.round(cohort.reporting.submitted/cohort.reporting.total*100)+'%'">
                            </div>
                        </div>
                        <div class="row g-2 mt-3 text-center">
                            <div class="col-4">
                                <div class="fw-bold text-warning" x-text="cohort.reporting.pending"></div>
                                <div class="cs-sub-text">Pending</div>
                            </div>
                            <div class="col-4">
                                <div class="fw-bold text-danger" x-text="cohort.reporting.overdue"></div>
                                <div class="cs-sub-text">Overdue</div>
                            </div>
                            <div class="col-4">
                                <div class="fw-bold text-secondary" x-text="cohort.reporting.returned"></div>
                                <div class="cs-sub-text">Returned</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-8">
                <div class="cs-card h-100">
                    <div class="cs-card-header">
                        <div class="cs-section-icon"><i class="bi bi-calendar-week-fill"></i></div>
                        <div class="fw-semibold">Reporting Periods</div>
                        <button class="btn cs-btn-primary btn-sm ms-auto" @click="toast('Configuring new period…')">
                            <i class="bi bi-plus-circle me-1"></i>Add Period
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table cs-table align-middle mb-0 small">
                            <thead>
                                <tr>
                                    <th class="px-4">Period</th>
                                    <th>Type</th>
                                    <th>Opens</th>
                                    <th>Closes</th>
                                    <th class="text-center">Submitted</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="period in cohort.reportingPeriods" :key="period.id">
                                    <tr>
                                        <td class="px-4 fw-semibold" x-text="period.label"></td>
                                        <td>
                                            <span class="cs-stage-tag" x-text="period.type"></span>
                                        </td>
                                        <td x-text="period.opens"></td>
                                        <td x-text="period.closes"></td>
                                        <td class="text-center fw-semibold"
                                            :class="period.submitted >= period.total ? 'text-success' : 'text-warning'"
                                            x-text="period.submitted + '/' + period.total"></td>
                                        <td>
                                            <span class="cs-pill cs-pill-sm"
                                                  :class="period.status==='Open'?'pill-active':period.status==='Closed'?'pill-completed':'pill-draft'"
                                                  x-text="period.status"></span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>{{-- end reporting tab --}}


    {{-- ══════════════════════════════════════
         TAB: ENGAGEMENTS
    ══════════════════════════════════════ --}}
    <div x-show="activeTab === 'engagements'">
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
            <div class="d-flex gap-2">
                <select class="cs-select" style="width:auto;" x-model="engEsoFilter">
                    <option value="">All ESOs</option>
                    <template x-for="eso in cohort.esos">
                        <option :value="eso.id" x-text="eso.name"></option>
                    </template>
                </select>
                <select class="cs-select" style="width:auto;" x-model="engTypeFilter">
                    <option value="">All Types</option>
                    <option>Training Session</option>
                    <option>Mentorship</option>
                    <option>Site Visit</option>
                    <option>Financial Review</option>
                    <option>Marketing Support</option>
                </select>
            </div>
            <button class="btn cs-btn-primary btn-sm" @click="toast('Opening engagement log form…')">
                <i class="bi bi-plus-circle-fill me-1"></i>Log Engagement
            </button>
        </div>
        <div class="cs-card">
            <div class="table-responsive">
                <table class="table cs-table align-middle mb-0 small">
                    <thead>
                        <tr>
                            <th class="px-4">Date</th>
                            <th>ESO</th>
                            <th>Enterprise</th>
                            <th>Session Type</th>
                            <th>Duration</th>
                            <th>Topics</th>
                            <th class="text-center">Evidence</th>
                            <th class="text-center pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="eng in cohort.engagements" :key="eng.id">
                            <tr>
                                <td class="px-4 py-3 fw-medium" x-text="eng.date"></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="cs-eso-av" x-text="eng.esoInitials"></div>
                                        <span x-text="eng.esoName"></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium" x-text="eng.enterprise"></div>
                                </td>
                                <td>
                                    <span class="cs-session-tag" x-text="eng.type"></span>
                                </td>
                                <td x-text="eng.duration"></td>
                                <td class="text-muted" style="max-width:200px;">
                                    <span class="text-truncate d-block" x-text="eng.topics"></span>
                                </td>
                                <td class="text-center">
                                    <i class="bi" :class="eng.hasEvidence ? 'bi-paperclip text-primary' : 'bi-dash text-muted'"></i>
                                </td>
                                <td class="text-center pe-4">
                                    <button class="cs-action-btn" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>{{-- end engagements tab --}}


    {{-- Toast --}}
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index:99999;">
        <div class="cs-toast" :class="toastType==='success'?'cs-toast-ok':'cs-toast-err'"
             x-show="showToast" x-transition.opacity>
            <i :class="toastType==='success'?'bi bi-check-circle-fill':'bi bi-x-circle-fill'"></i>
            <span x-text="toastMsg"></span>
            <button class="ms-auto btn-close btn-close-white btn-close-sm" @click="showToast=false"></button>
        </div>
    </div>

</div>{{-- end page --}}

{{-- ══════════════════════════════════════ STYLES ══════════════════════════════════════ --}}
<style>
.cohort-show {
    --r: 14px; --r-sm: 8px; --r-xs: 6px;
    --border: #e8ecf0; --bg: #f8fafc;
    --text: #0f172a; --muted: #64748b;
    font-family: 'Inter', system-ui, sans-serif;
}

/* ── Hero ── */
.cs-hero {
    background:#fff; border:1px solid var(--border);
    border-radius:var(--r); padding:2rem;
    box-shadow:0 1px 3px rgba(0,0,0,.05), 0 4px 16px rgba(0,0,0,.04);
}
.cs-hero-divider { border-top:1px solid var(--border); }
.cs-cohort-number {
    width:56px; height:56px; border-radius:14px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-size:1rem; font-weight:900; letter-spacing:-.02em;
}
.badge-c1{background:#dbeafe;color:#1d4ed8;} .badge-c2{background:#dcfce7;color:#15803d;}
.badge-c3{background:#ede9fe;color:#7c3aed;} .badge-c4{background:#fce7f3;color:#be185d;}
.badge-c5{background:#fef9c3;color:#a16207;}
.cs-hero-title { font-size:1.4rem; font-weight:800; color:var(--text); letter-spacing:-.02em; line-height:1.2; }
.cs-hero-desc  { font-size:.88rem; color:var(--muted); max-width:640px; line-height:1.6; }

/* ── KPI ── */
.cs-kpi { display:flex; align-items:flex-start; gap:12px; }
.cs-kpi-icon { width:40px; height:40px; border-radius:10px; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:1rem; }
.cs-kpi-val   { font-size:1.55rem; font-weight:800; letter-spacing:-.03em; line-height:1; }
.cs-kpi-label { font-size:.74rem; font-weight:600; color:var(--text); margin-top:2px; }
.cs-kpi-sub   { font-size:.68rem; color:var(--muted); }

/* ── Tab bar ── */
.cs-tab-bar { display:flex; gap:2px; border-bottom:1px solid var(--border); overflow-x:auto; background:#fff; border-radius:var(--r) var(--r) 0 0; padding:0 4px; }
.cs-tab {
    display:inline-flex; align-items:center; gap:6px;
    padding:12px 18px; border:none; background:none; white-space:nowrap;
    font-size:.82rem; font-weight:500; color:var(--muted);
    border-bottom:2px solid transparent; cursor:pointer; transition:all .12s;
}
.cs-tab:hover { color:var(--text); }
.cs-tab-active { color:#1d4ed8 !important; border-bottom-color:#1d4ed8 !important; font-weight:600; }
.cs-tab-badge {
    background:#fee2e2; color:#dc2626; border-radius:99px;
    padding:1px 7px; font-size:.65rem; font-weight:700;
}

/* ── Cards ── */
.cs-card { background:#fff; border:1px solid var(--border); border-radius:var(--r); overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); }
.cs-card-header { display:flex; align-items:center; gap:12px; padding:14px 20px; border-bottom:1px solid var(--border); background:#fafbfc; flex-wrap:wrap; }
.cs-card-body {}
.cs-section-icon { width:30px; height:30px; border-radius:8px; background:rgba(59,130,246,.1); color:#3b82f6; display:flex; align-items:center; justify-content:center; font-size:.82rem; flex-shrink:0; }
.cs-section-sub { font-size:.72rem; color:var(--muted); margin-top:1px; }

/* ── Call banner inside card ── */
.cs-call-banner { background:linear-gradient(135deg,#fffbeb 0%,#fff 60%); border-bottom:1px solid var(--border); }
.cs-call-stat { padding:12px 16px; }
.cs-call-stat-label { font-size:.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--muted); margin-bottom:3px; }
.cs-call-stat-val { font-size:.88rem; font-weight:600; color:var(--text); }

/* ── Linked call card accent ── */
.cs-linked-call-card { border-color:#fde68a; }

/* ── Pipeline ── */
.cs-pipe-step { text-align:center; padding:10px 12px; border-radius:var(--r-sm); border:1.5px solid var(--border); min-width:80px; transition:all .15s; }
.cs-pipe-on   { border-color:#3b82f6; background:#eff6ff; }
.cs-pipe-off  { opacity:.4; }
.cs-pipe-num  { font-size:1.1rem; font-weight:800; color:var(--text); line-height:1; }
.cs-pipe-on .cs-pipe-num { color:#3b82f6; }
.cs-pipe-name { font-size:.65rem; color:var(--muted); font-weight:500; margin-top:2px; }
.cs-pipe-arrow { font-size:.6rem; color:#cbd5e1; }

/* ── Sector/Stage tags ── */
.cs-sector-tag { display:inline-flex; align-items:center; font-size:.76rem; font-weight:500; color:#1d4ed8; background:#eff6ff; border:1px solid #bfdbfe; border-radius:var(--r-xs); padding:3px 10px; }
.cs-sector-tag-sm { font-size:.68rem; padding:2px 8px; }
.cs-stage-tag { font-size:.68rem; background:#f1f5f9; color:var(--muted); border-radius:4px; padding:2px 8px; font-weight:500; }
.cs-session-tag { font-size:.72rem; background:#ede9fe; color:#7c3aed; border-radius:4px; padding:2px 8px; font-weight:500; }

/* ── Pills ── */
.cs-pill { display:inline-flex; align-items:center; font-size:.68rem; font-weight:700; letter-spacing:.04em; text-transform:uppercase; padding:3px 10px; border-radius:99px; white-space:nowrap; }
.cs-pill-sm { font-size:.62rem; padding:2px 8px; }
.cs-meta-tag { display:inline-flex; align-items:center; font-size:.74rem; font-weight:500; color:var(--muted); background:var(--bg); border:1px solid var(--border); border-radius:99px; padding:3px 10px; }
.pill-draft{background:#fef9c3;color:#a16207;} .pill-active{background:#dcfce7;color:#15803d;}
.pill-completed{background:#ede9fe;color:#7c3aed;} .pill-archived{background:#f1f5f9;color:#64748b;}
.pill-open{background:#dcfce7;color:#15803d;} .pill-closed{background:#f1f5f9;color:#64748b;}
.pill-inactive{background:#fee2e2;color:#dc2626;}

/* ── Compliance table ── */
.cs-compliance-header {
    display:grid; grid-template-columns:2fr 1fr 1fr 1fr 2fr;
    padding:8px 20px; background:#f8fafc; border-bottom:1px solid var(--border);
    font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--muted);
    gap:12px;
}
.cs-compliance-row {
    display:grid; grid-template-columns:2fr 1fr 1fr 1fr 2fr;
    padding:12px 20px; border-bottom:1px solid #f8fafc; gap:12px; align-items:center;
}
.cs-compliance-row:hover { background:#fafbff; }

/* ── PDO ring (CSS-only donut) ── */
.cs-pdo-ring {
    width:90px; height:90px; border-radius:50%; margin:0 auto;
    display:flex; align-items:center; justify-content:center;
    font-size:1.2rem; font-weight:800;
    background: conic-gradient(var(--color) 0% calc(var(--pct) * 1%), #f1f5f9 calc(var(--pct) * 1%) 100%);
    position:relative; color:var(--color);
}
.cs-pdo-ring::before {
    content:''; width:62px; height:62px; background:#fff;
    border-radius:50%; position:absolute; top:50%; left:50%;
    transform:translate(-50%,-50%);
}
.cs-pdo-ring span { position:relative; z-index:1; }

/* ── DL ── */
.cs-dl { margin:0; }
.cs-dl-row { display:flex; align-items:center; justify-content:space-between; padding:10px 20px; border-bottom:1px solid #f1f5f9; font-size:.82rem; gap:12px; }
.cs-dl-row dt { color:var(--muted); font-weight:500; white-space:nowrap; }
.cs-dl-row dd { margin:0; text-align:right; color:var(--text); font-size:.82rem; }

/* ── Progress bars ── */
.cs-prog-track { height:6px; background:#f1f5f9; border-radius:99px; overflow:hidden; }
.cs-prog-fill  { height:100%; border-radius:99px; min-width:2px; transition:width .5s ease; }

/* ── ESO avatars ── */
.cs-eso-av { width:28px; height:28px; border-radius:50%; background:#dbeafe; color:#1d4ed8; display:flex; align-items:center; justify-content:center; font-size:.6rem; font-weight:800; flex-shrink:0; }
.cs-eso-av-lg { width:38px; height:38px; border-radius:50%; background:#dbeafe; color:#1d4ed8; display:flex; align-items:center; justify-content:center; font-size:.75rem; font-weight:800; flex-shrink:0; }

/* ── Enterprise avatar ── */
.cs-ent-av { width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:.68rem; font-weight:800; flex-shrink:0; }
.av-blue{background:#dbeafe;color:#1d4ed8;} .av-green{background:#dcfce7;color:#15803d;}
.av-orange{background:#ffedd5;color:#c2410c;} .av-purple{background:#ede9fe;color:#7c3aed;}
.av-pink{background:#fce7f3;color:#be185d;} .av-teal{background:#ccfbf1;color:#0f766e;}

/* ── PDO dots ── */
.cs-pdo { width:20px; height:20px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.58rem; font-weight:800; flex-shrink:0; }
.pdo-w{background:#fce7f3;color:#be185d;} .pdo-y{background:#dcfce7;color:#15803d;}
.pdo-r{background:#fef9c3;color:#a16207;} .pdo-off{background:#f1f5f9;color:#d1d5db;}

/* ── Enterprise status ── */
.cs-ent-status { display:inline-block; font-size:.65rem; font-weight:700; letter-spacing:.03em; text-transform:uppercase; border-radius:99px; padding:2px 8px; }
.est-active{background:#dcfce7;color:#15803d;} .est-at-risk{background:#fef9c3;color:#a16207;}
.est-completed{background:#ede9fe;color:#7c3aed;} .est-withdrawn{background:#f1f5f9;color:#64748b;}

/* ── Table ── */
.cs-table { font-size:.82rem; }
.cs-table thead tr { background:#f8fafc; }
.cs-table thead th { font-weight:600; font-size:.68rem; text-transform:uppercase; letter-spacing:.05em; color:var(--muted); padding:10px 12px; border-bottom:1px solid var(--border); border-top:none; }
.cs-table tbody td { padding:10px 12px; border-bottom:1px solid #f8fafc; }
.cs-table tbody tr:hover { background:#fafbff; }

/* ── Buttons ── */
.cs-btn-primary { background:#1d4ed8; color:#fff; border:none; border-radius:var(--r-sm); font-weight:600; font-size:.82rem; padding:8px 16px; transition:all .15s; }
.cs-btn-primary:hover { background:#1e40af; color:#fff; transform:translateY(-1px); }
.cs-btn-ghost { background:transparent; border:1px solid var(--border); color:var(--text); border-radius:var(--r-sm); font-weight:500; font-size:.82rem; padding:8px 14px; transition:all .12s; }
.cs-btn-ghost:hover { background:var(--bg); }
.cs-action-btn { width:30px; height:30px; border-radius:7px; border:1px solid var(--border); background:#fff; color:var(--muted); cursor:pointer; font-size:.8rem; display:inline-flex; align-items:center; justify-content:center; transition:all .12s; }
.cs-action-btn:hover { background:#eff6ff; border-color:#93c5fd; color:#1d4ed8; }
.cs-action-link { display:flex; align-items:center; gap:8px; padding:10px 14px; border-radius:var(--r-sm); border:1px solid var(--border); background:#fff; font-size:.82rem; font-weight:500; color:var(--text); text-decoration:none; transition:all .12s; }
.cs-action-link:hover { background:#eff6ff; border-color:#93c5fd; color:#1d4ed8; }

/* ── Activity timeline ── */
.cs-activity-item { display:flex; align-items:flex-start; gap:10px; position:relative; }
.cs-activity-line::before { content:''; position:absolute; left:4px; top:16px; bottom:0; width:2px; background:#f1f5f9; }
.cs-activity-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; margin-top:4px; position:relative; z-index:1; }
.act-green{background:#10b981;} .act-amber{background:#f59e0b;} .act-blue{background:#3b82f6;}

/* ── Search / select ── */
.cs-search-wrap { position:relative; }
.cs-search-icon { position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:.78rem; }
.cs-search-input { padding:8px 10px 8px 30px; border:1px solid var(--border); border-radius:var(--r-sm); font-size:.82rem; background:#fff; width:220px; }
.cs-search-input:focus { outline:none; border-color:#3b82f6; }
.cs-select { padding:8px 10px; border:1px solid var(--border); border-radius:var(--r-sm); font-size:.82rem; background:#fff; color:var(--text); }
.cs-select:focus { outline:none; border-color:#3b82f6; }

/* ── Sub text ── */
.cs-sub-text { font-size:.68rem; color:var(--muted); }
.cs-section-label { font-size:.67rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); }

/* ── Toast ── */
.cs-toast { display:flex; align-items:center; gap:10px; padding:12px 16px; border-radius:var(--r-sm); font-size:.85rem; font-weight:500; min-width:280px; box-shadow:0 4px 20px rgba(0,0,0,.15); }
.cs-toast-ok  { background:#064e3b; color:#ecfdf5; }
.cs-toast-err { background:#7f1d1d; color:#fef2f2; }
</style>

{{-- ══════════════════════════════════════ ALPINE JS ══════════════════════════════════════ --}}
<script>
function cohortShowApp() {
    return {
        activeTab: 'overview',
        panelTab: 'Overview',
        showAssign: false, showEdit: false,
        showToast: false, toastMsg: '', toastType: 'success',
        entSearch: '', entEsoFilter: '',
        engEsoFilter: '', engTypeFilter: '',

        mainTabs: [
            { key:'overview',    label:'Overview',            icon:'bi-grid-1x2-fill',           badge:null },
            { key:'enterprises', label:'ESO & Enterprises',   icon:'bi-building',                badge:null },
            { key:'reporting',   label:'Reporting',           icon:'bi-file-earmark-bar-graph',   badge:'7'  },
            { key:'engagements', label:'Engagement Logs',     icon:'bi-chat-dots-fill',           badge:null },
        ],

        cohort: {
            id: 3, cohortNumber: 3, year: '2025',
            name: 'LEHSFF Cohort 3 – Incubation Programme 2025',
            description: 'Third incubation cohort supporting high-potential MSMEs across all NSDP II priority sectors, with a focus on women and youth-owned enterprises.',
            startDate: '03 Feb 2025', endDate: '01 Aug 2025', durationMonths: 6,
            targetEnterprises: 50, reportingFrequency: 'Monthly',
            geography: 'All Districts', status: 'Active',
            createdBy: 'John Procure', createdAt: '10 Jan 2025', updatedAt: '15 Feb 2025',
            enterprises: 50, esoCount: 5, totalEngagements: 139, graduatedCount: 0,
            currentPeriod: 'March 2025',
            reporting: { submitted: 28, total: 50, pending: 15, overdue: 7, returned: 2 },
            pdo: { women: 58, womenCount: 29, youth: 43, youthCount: 22, rural: 31, ruralCount: 16 },

            call: {
                id: 'CFA-2025-003',
                title: 'LEHSFF Cohort 3 – Call for Applications 2025',
                description: 'Open call for high-potential MSMEs across all NSDP II priority sectors. Applications from women and youth-owned enterprises are strongly encouraged.',
                status: 'Closed',
                publishDate: '15 Jan 2025', openDate: '01 Feb 2025', closeDate: '28 Feb 2025',
                totalApplications: 210, publishedBy: 'John Procure',
                eligibilitySummary: 'Formally registered businesses operating in Lesotho for at least 6 months, annual revenue not exceeding M 2,000,000, owner must be a Lesotho citizen.',
                sectors: ['Agriculture', 'Technology', 'Manufacturing', 'Retail & Trade', 'Textile & Garments', 'Food & Beverage', 'Health & Wellness'],
                pipeline: [
                    { label:'Submitted',  count:210 },
                    { label:'Screened',   count:185 },
                    { label:'Eligible',   count:140 },
                    { label:'Evaluated',  count:100 },
                    { label:'Top 20',     count:20  },
                    { label:'Top 10',     count:10  },
                    { label:'Confirmed',  count:50  },
                ],
            },

            esos: [
                {
                    id:1, name:'Technoserve Lesotho', initials:'TL', active:true, enterprises:10,
                    areaOfFocus:'Agriculture & Technology', email:'info@technoserve.ls', engagements:32,
                    enterpriseReports:{ submitted:8, total:10 }, esoReports:{ submitted:2, total:3 }, overdue:1,
                    enterpriseList:[
                        { id:1,  name:'MoroAgri Basotho',     appNumber:'APP-001', owner:'Limpho Mokoena',    phone:'+266 5823 1234', sector:'Agriculture',    district:'Maseru',       stage:'Registered',  isWoman:true,  isYouth:true,  isRural:false, incubationStatus:'Active',    reportsSubmitted:3, reportsTotal:3 },
                        { id:2,  name:'Lesotho Stitch Co.',   appNumber:'APP-002', owner:'Thabo Ramokhele',   phone:'+266 5861 4321', sector:'Textile',        district:'Leribe',       stage:'Registered',  isWoman:false, isYouth:false, isRural:false, incubationStatus:'Active',    reportsSubmitted:3, reportsTotal:3 },
                        { id:3,  name:'TechForward LS',       appNumber:'APP-003', owner:'Palesa Nthako',     phone:'+266 5834 9876', sector:'Technology',     district:'Maseru',       stage:'Startup',     isWoman:true,  isYouth:true,  isRural:false, incubationStatus:'At Risk',   reportsSubmitted:1, reportsTotal:3 },
                    ]
                },
                {
                    id:2, name:'BEDCO', initials:'BD', active:true, enterprises:10,
                    areaOfFocus:'Manufacturing & Retail', email:'bedco@bedco.ls', engagements:28,
                    enterpriseReports:{ submitted:7, total:10 }, esoReports:{ submitted:2, total:3 }, overdue:2,
                    enterpriseList:[
                        { id:4,  name:'Naledi Weave Studio',  appNumber:'APP-005', owner:'Naledi Sello',      phone:'+266 5812 5566', sector:'Textile',        district:'Mafeteng',     stage:'Registered',  isWoman:true,  isYouth:false, isRural:true,  incubationStatus:'Active',    reportsSubmitted:3, reportsTotal:3 },
                        { id:5,  name:'Selemo Organics',      appNumber:'APP-007', owner:'Mamello Tsita',     phone:'+266 5867 3344', sector:'Agriculture',    district:"Mohale's Hoek",stage:'Registered',  isWoman:true,  isYouth:false, isRural:true,  incubationStatus:'Active',    reportsSubmitted:2, reportsTotal:3 },
                        { id:6,  name:'Lerato Crafts & Arts', appNumber:'APP-008', owner:'Lerato Nkosi',      phone:'+266 5823 9900', sector:'Manufacturing',  district:'Berea',        stage:'Startup',     isWoman:true,  isYouth:true,  isRural:false, incubationStatus:'Active',    reportsSubmitted:3, reportsTotal:3 },
                    ]
                },
                {
                    id:3, name:'Afri-Impact Hub', initials:'AH', active:true, enterprises:10,
                    areaOfFocus:'Social Enterprise & Fintech', email:'hub@afriimpact.ls', engagements:22,
                    enterpriseReports:{ submitted:5, total:10 }, esoReports:{ submitted:1, total:3 }, overdue:3,
                    enterpriseList:[
                        { id:7,  name:'Basotho Digital Hub',  appNumber:'APP-010', owner:'Teboho Phafane',    phone:'+266 5834 5678', sector:'Technology',     district:'Maseru',       stage:'Startup',     isWoman:false, isYouth:true,  isRural:false, incubationStatus:'Active',    reportsSubmitted:2, reportsTotal:3 },
                        { id:8,  name:'Ha Seoli Poultry',     appNumber:'APP-009', owner:'Nthabiseng Seoli',  phone:'+266 5890 1234', sector:'Agriculture',    district:'Leribe',       stage:'Registered',  isWoman:true,  isYouth:false, isRural:true,  incubationStatus:'At Risk',   reportsSubmitted:0, reportsTotal:3 },
                    ]
                },
                {
                    id:4, name:'Grow Lesotho', initials:'GL', active:true, enterprises:10,
                    areaOfFocus:'Food & Beverage / Textile', email:'grow@growls.ls', engagements:30,
                    enterpriseReports:{ submitted:9, total:10 }, esoReports:{ submitted:3, total:3 }, overdue:0,
                    enterpriseList:[
                        { id:9,  name:'Molapo Tech Solutions',appNumber:'APP-006', owner:'Tšepiso Molapo',   phone:'+266 5845 7788', sector:'Technology',     district:'Maseru',       stage:'Startup',     isWoman:false, isYouth:true,  isRural:false, incubationStatus:'Active',    reportsSubmitted:3, reportsTotal:3 },
                        { id:10, name:'Khomo Foods Ltd',      appNumber:'APP-004', owner:'Retseli Mofolo',   phone:'+266 5878 1122', sector:'Food & Beverage',district:'Berea',        stage:'Registered',  isWoman:false, isYouth:false, isRural:false, incubationStatus:'Completed', reportsSubmitted:3, reportsTotal:3 },
                    ]
                },
                {
                    id:5, name:'SMME Hub', initials:'SH', active:true, enterprises:10,
                    areaOfFocus:'Cross-sector MSME Support', email:'smme@hub.ls', engagements:27,
                    enterpriseReports:{ submitted:8, total:10 }, esoReports:{ submitted:2, total:3 }, overdue:1,
                    enterpriseList:[]
                },
            ],

            reportingPeriods: [
                { id:1, label:'February 2025',  type:'Monthly', opens:'01 Feb 2025', closes:'10 Mar 2025', submitted:40, total:50, status:'Closed' },
                { id:2, label:'March 2025',     type:'Monthly', opens:'01 Mar 2025', closes:'10 Apr 2025', submitted:28, total:50, status:'Open'   },
                { id:3, label:'April 2025',     type:'Monthly', opens:'01 Apr 2025', closes:'10 May 2025', submitted:0,  total:50, status:'Draft'  },
                { id:4, label:'May 2025',       type:'Monthly', opens:'01 May 2025', closes:'10 Jun 2025', submitted:0,  total:50, status:'Draft'  },
                { id:5, label:'June 2025',      type:'Monthly', opens:'01 Jun 2025', closes:'10 Jul 2025', submitted:0,  total:50, status:'Draft'  },
                { id:6, label:'Final Report',   type:'Adhoc',   opens:'01 Aug 2025', closes:'31 Aug 2025', submitted:0,  total:55, status:'Draft'  },
            ],

            engagements: [
                { id:1, date:'10 Apr 2025', esoName:'Technoserve Lesotho', esoInitials:'TL', enterprise:'MoroAgri Basotho',     type:'Mentorship',       duration:'2hrs', topics:'Business plan review, market expansion',         hasEvidence:true  },
                { id:2, date:'09 Apr 2025', esoName:'BEDCO',               esoInitials:'BD', enterprise:'Naledi Weave Studio',  type:'Training Session', duration:'3hrs', topics:'Export documentation and trade licensing',        hasEvidence:true  },
                { id:3, date:'08 Apr 2025', esoName:'Grow Lesotho',        esoInitials:'GL', enterprise:'Molapo Tech Solutions',type:'Financial Review',  duration:'1.5hrs','topics':'Cash flow, revenue model review',               hasEvidence:false },
                { id:4, date:'07 Apr 2025', esoName:'Afri-Impact Hub',     esoInitials:'AH', enterprise:'Basotho Digital Hub',  type:'Site Visit',       duration:'2hrs', topics:'Tech infrastructure assessment',                 hasEvidence:true  },
                { id:5, date:'05 Apr 2025', esoName:'SMME Hub',            esoInitials:'SH', enterprise:'Ha Seoli Poultry',     type:'Mentorship',       duration:'1hr',  topics:'Financial management, record keeping basics',    hasEvidence:false },
                { id:6, date:'04 Apr 2025', esoName:'BEDCO',               esoInitials:'BD', enterprise:'Lerato Crafts & Arts', type:'Marketing Support',duration:'2hrs', topics:'Social media marketing, brand identity',         hasEvidence:true  },
            ],

            activity: [
                { action:'7 overdue reports flagged – notifications sent',        by:'System',       at:'10 Apr 2025', type:'warning' },
                { action:'March 2025 reporting period opened',                    by:'Admin',        at:'01 Mar 2025', type:'blue'    },
                { action:'February reports reviewed – 40/50 approved',           by:'Admin',        at:'15 Mar 2025', type:'success' },
                { action:'ESO-Enterprise mapping confirmed for all 5 ESOs',       by:'John Procure', at:'03 Feb 2025', type:'success' },
                { action:'Cohort 3 commenced – 50 enterprises activated',        by:'Admin',        at:'03 Feb 2025', type:'success' },
                { action:'Cohort 3 created and linked to Call CFA-2025-003',      by:'John Procure', at:'20 Jan 2025', type:'blue'    },
            ],
        },

        get cohortBadgeColor() {
            return ['badge-c1','badge-c2','badge-c3','badge-c4','badge-c5'][(this.cohort.cohortNumber - 1) % 5];
        },

        get overallCompliance() {
            const { submitted, total } = this.cohort.reporting;
            return total ? Math.round(submitted / total * 100) : 0;
        },

        get filteredEsos() {
            let esos = this.cohort.esos;
            if (this.entEsoFilter) esos = esos.filter(e => e.id == this.entEsoFilter);
            if (this.entSearch) {
                const s = this.entSearch.toLowerCase();
                esos = esos.map(eso => ({
                    ...eso,
                    enterpriseList: eso.enterpriseList.filter(e =>
                        e.name.toLowerCase().includes(s) || e.owner.toLowerCase().includes(s)
                    )
                })).filter(eso => eso.enterpriseList.length > 0);
            }
            return esos;
        },

        filterEnterprises() { /* triggers filteredEsos getter */ },

        esoCompliance(eso) {
            const total = eso.enterpriseReports.total + eso.esoReports.total;
            const submitted = eso.enterpriseReports.submitted + eso.esoReports.submitted;
            return total ? Math.round(submitted / total * 100) : 0;
        },

        statusClass(s) {
            return { 'Draft':'pill-draft', 'Active':'pill-active', 'Open':'pill-open',
                     'Completed':'pill-completed', 'Archived':'pill-archived',
                     'Closed':'pill-closed', 'Inactive':'pill-inactive' }[s] || 'pill-draft';
        },

        entStatusClass(s) {
            return { 'Active':'est-active', 'At Risk':'est-at-risk',
                     'Completed':'est-completed', 'Withdrawn':'est-withdrawn' }[s] || '';
        },

        sectorColor(sector) {
            return { 'Agriculture':'av-green', 'Technology':'av-blue', 'Textile':'av-pink',
                     'Manufacturing':'av-orange', 'Food & Beverage':'av-teal' }[sector] || 'av-purple';
        },

        initials(name) { return name.split(' ').slice(0,2).map(w=>w[0].toUpperCase()).join(''); },

        exportCohort() { this.toast('Exporting cohort data…'); },

        toast(msg, type='success') {
            this.toastMsg = msg; this.toastType = type; this.showToast = true;
            setTimeout(() => this.showToast = false, 3500);
        },
    };
}
</script>
</x-app-layout>