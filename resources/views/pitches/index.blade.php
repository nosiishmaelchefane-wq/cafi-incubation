<x-app-layout>
    {{-- resources/views/incubation/shortlisting/index.blade.php --}}
{{-- Route: /incubation/shortlisting/{call} --}}

<div class="sl-page p-4" x-data="shortlistApp()">

    {{-- ══════════════════════════════════════
         BREADCRUMB
    ══════════════════════════════════════ --}}
    <nav aria-label="breadcrumb" class="mb-2">
        <ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted"><i class="bi bi-house-fill" style="font-size:.7rem"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Incubation</a></li>
            <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Applications</a></li>
            <li class="breadcrumb-item active fw-semibold">Shortlisting &amp; Pitches</li>
        </ol>
    </nav>

    {{-- ══════════════════════════════════════
         PAGE HEADER
    ══════════════════════════════════════ --}}
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-trophy-fill text-warning me-2"></i>Shortlisting &amp; Pitches
            </h4>
            <p class="text-muted small mb-0">
                LEHSFF Cohort 3 · Call CFA-2025-003 ·
                Manage Top 20 shortlist, pitch schedule, scores and due diligence
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn sl-btn-ghost btn-sm" @click="exportList()">
                <i class="bi bi-download me-1"></i>Export
            </button>
            <button class="btn sl-btn-ghost btn-sm" @click="sendBulkInvitation()"
                    x-show="stage === 'shortlist'">
                <i class="bi bi-envelope me-1"></i>Send Pitch Invitations
            </button>
            <button class="btn sl-btn-primary btn-sm"
                    x-show="stage === 'shortlist'" @click="confirmTop20()">
                <i class="bi bi-check2-all me-1"></i>Confirm Top 20
            </button>
            <button class="btn sl-btn-primary btn-sm"
                    x-show="stage === 'pitch'" @click="confirmTop10()">
                <i class="bi bi-award-fill me-1"></i>Confirm Top 10
            </button>
            <button class="btn sl-btn-primary btn-sm"
                    x-show="stage === 'due-diligence'" @click="finaliseAll()">
                <i class="bi bi-people-fill me-1"></i>Finalise Cohort
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         WORKFLOW PROGRESS STEPPER
    ══════════════════════════════════════ --}}
    <div class="sl-card mb-4 p-0">
        <div class="d-flex align-items-stretch">
            <template x-for="(step, idx) in stages" :key="idx">
                <div class="sl-stepper-step flex-grow-1"
                     :class="stageIndex >= idx ? 'sl-step-done' : 'sl-step-future'"
                     @click="stage = step.key; applyFilters()">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <div class="sl-step-circle"
                             :class="stageIndex > idx ? 'sc-done' : stageIndex === idx ? 'sc-active' : 'sc-future'">
                            <i class="bi" :class="stageIndex > idx ? 'bi-check2' : step.icon"></i>
                        </div>
                        <span class="fw-semibold small" x-text="step.label"></span>
                    </div>
                    <div class="sl-step-desc" x-text="step.desc"></div>
                    <div class="sl-step-count"
                         :class="stageIndex >= idx ? 'text-primary' : 'text-muted'"
                         x-text="stepCount(step.key)"></div>
                </div>
            </template>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         KPI STRIP
    ══════════════════════════════════════ --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3 col-xl">
            <div class="sl-kpi" @click="filterByStatus('all')">
                <div class="sl-kpi-icon" style="background:rgba(59,130,246,.1);color:#3b82f6;"><i class="bi bi-list-check"></i></div>
                <div>
                    <div class="sl-kpi-val text-primary" x-text="counts.totalEvaluated"></div>
                    <div class="sl-kpi-label">Total Evaluated</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl">
            <div class="sl-kpi" @click="filterByStatus('Top 20')">
                <div class="sl-kpi-icon" style="background:rgba(245,158,11,.1);color:#f59e0b;"><i class="bi bi-star-fill"></i></div>
                <div>
                    <div class="sl-kpi-val text-warning" x-text="counts.top20"></div>
                    <div class="sl-kpi-label">Top 20 Shortlisted</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl">
            <div class="sl-kpi" @click="filterByStatus('Pitched')">
                <div class="sl-kpi-icon" style="background:rgba(6,182,212,.1);color:#06b6d4;"><i class="bi bi-mic-fill"></i></div>
                <div>
                    <div class="sl-kpi-val" style="color:#06b6d4;" x-text="counts.pitched"></div>
                    <div class="sl-kpi-label">Pitched</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl">
            <div class="sl-kpi" @click="filterByStatus('Top 10')">
                <div class="sl-kpi-icon" style="background:rgba(139,92,246,.1);color:#8b5cf6;"><i class="bi bi-award-fill"></i></div>
                <div>
                    <div class="sl-kpi-val" style="color:#8b5cf6;" x-text="counts.top10"></div>
                    <div class="sl-kpi-label">Top 10 Confirmed</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl">
            <div class="sl-kpi" @click="filterByStatus('DD Pass')">
                <div class="sl-kpi-icon" style="background:rgba(16,185,129,.1);color:#10b981;"><i class="bi bi-shield-check"></i></div>
                <div>
                    <div class="sl-kpi-val text-success" x-text="counts.ddPass"></div>
                    <div class="sl-kpi-label">DD Passed</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl">
            <div class="sl-kpi" @click="filterByStatus('DD Fail')">
                <div class="sl-kpi-icon" style="background:rgba(239,68,68,.1);color:#ef4444;"><i class="bi bi-shield-x"></i></div>
                <div>
                    <div class="sl-kpi-val text-danger" x-text="counts.ddFail"></div>
                    <div class="sl-kpi-label">DD Failed</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl">
            <div class="sl-kpi" @click="filterByStatus('Final Accepted')">
                <div class="sl-kpi-icon" style="background:rgba(16,185,129,.1);color:#10b981;"><i class="bi bi-check-circle-fill"></i></div>
                <div>
                    <div class="sl-kpi-val text-success" x-text="counts.finalAccepted"></div>
                    <div class="sl-kpi-label">Final Accepted</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         STAGE TABS + FILTERS
    ══════════════════════════════════════ --}}
    <div class="sl-card mb-4">
        <div class="sl-card-header px-4 pt-3 pb-0 flex-column align-items-stretch gap-0">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                <div class="d-flex gap-2 align-items-center">
                    <h6 class="fw-bold mb-0">
                        <span x-text="activeStageLabel"></span>
                    </h6>
                    <span class="sl-count-badge" x-text="filtered.length + ' applications'"></span>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <div class="sl-search-wrap">
                        <i class="bi bi-search sl-search-icon"></i>
                        <input type="text" class="sl-search-input" placeholder="Search enterprise, ID…"
                               x-model="search" @input="applyFilters()">
                    </div>
                    <select class="sl-select" x-model="filterSector" @change="applyFilters()">
                        <option value="">All Sectors</option>
                        <option>Agriculture</option>
                        <option>Technology</option>
                        <option>Textile</option>
                        <option>Manufacturing</option>
                        <option>Food & Beverage</option>
                    </select>
                    <button class="btn sl-btn-ghost btn-sm" @click="resetFilters()">
                        <i class="bi bi-x-circle me-1"></i>Reset
                    </button>
                </div>
            </div>
            <div class="sl-tab-row">
                <template x-for="t in stageTabs" :key="t.key">
                    <button class="sl-tab" :class="activeStatusFilter===t.key?'sl-tab-active':''"
                            @click="filterByStatus(t.key)">
                        <span x-text="t.label"></span>
                        <span class="sl-tab-badge" x-text="tabCount(t.key)"></span>
                    </button>
                </template>
            </div>
        </div>

        {{-- ── RANKED TABLE ── --}}
        <div class="table-responsive">
            <table class="table sl-table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="px-4" style="width:54px;">Rank</th>
                        <th style="min-width:210px;">Enterprise</th>
                        <th>Sector</th>
                        <th class="text-center">Eval Score</th>
                        <th class="text-center">Pitch Score</th>
                        <th class="text-center">Total Score</th>
                        <th>PDO</th>
                        <th>Pitch Slot</th>
                        <th>DD Status</th>
                        <th>Status</th>
                        <th class="text-center pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(app, idx) in paginated" :key="app.id">
                        <tr :class="app.status === 'Final Accepted' ? 'sl-row-accepted' :
                                    app.status === 'DD Fail' ? 'sl-row-failed' :
                                    app.status === 'Replaced' ? 'sl-row-replaced' : ''">

                            {{-- Rank --}}
                            <td class="px-4 py-3">
                                <div class="sl-rank-badge"
                                     :class="app.rank===1?'rank-gold':app.rank===2?'rank-silver':app.rank===3?'rank-bronze':app.rank<=10?'rank-purple':app.rank<=20?'rank-blue':'rank-default'"
                                     x-text="app.rank">
                                </div>
                            </td>

                            {{-- Enterprise --}}
                            <td class="py-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="sl-av" :class="sectorColor(app.sector)">
                                        <span x-text="initials(app.enterprise)"></span>
                                    </div>
                                    <div>
                                        <div class="fw-semibold small text-dark" x-text="app.enterprise"></div>
                                        <div class="sl-sub" x-text="app.id + ' · ' + app.district"></div>
                                    </div>
                                </div>
                            </td>

                            {{-- Sector --}}
                            <td><span class="sl-sector-tag" x-text="app.sector"></span></td>

                            {{-- Eval Score --}}
                            <td class="text-center">
                                <span class="fw-bold" :class="scoreColor(app.evalScore)"
                                      x-text="app.evalScore + '%'"></span>
                            </td>

                            {{-- Pitch Score --}}
                            <td class="text-center">
                                <template x-if="app.pitchScore !== null">
                                    <span class="fw-bold" :class="scoreColor(app.pitchScore)"
                                          x-text="app.pitchScore + '%'"></span>
                                </template>
                                <template x-if="app.pitchScore === null && app.status === 'Top 20'">
                                    <button class="btn sl-score-btn btn-sm"
                                            @click="openPitchScore(app)">
                                        <i class="bi bi-pencil me-1"></i>Score
                                    </button>
                                </template>
                                <template x-if="app.pitchScore === null && app.status !== 'Top 20'">
                                    <span class="text-muted small">—</span>
                                </template>
                            </td>

                            {{-- Total Score --}}
                            <td class="text-center">
                                <div class="sl-total-score"
                                     :class="totalScoreClass(app)"
                                     x-text="totalScore(app) + '%'">
                                </div>
                            </td>

                            {{-- PDO --}}
                            <td>
                                <div class="d-flex gap-1">
                                    <span class="sl-pdo" :class="app.isWoman?'pdo-w':'pdo-off'" title="Women">W</span>
                                    <span class="sl-pdo" :class="app.isYouth?'pdo-y':'pdo-off'" title="Youth">Y</span>
                                    <span class="sl-pdo" :class="app.isRural?'pdo-r':'pdo-off'" title="Rural">R</span>
                                </div>
                            </td>

                            {{-- Pitch Slot --}}
                            <td>
                                <template x-if="app.pitchDate">
                                    <div>
                                        <div class="small fw-medium" x-text="app.pitchDate"></div>
                                        <div class="sl-sub" x-text="app.pitchTime + ' · ' + app.pitchVenue"></div>
                                    </div>
                                </template>
                                <template x-if="!app.pitchDate && app.status === 'Top 20'">
                                    <button class="btn sl-ghost-xs" @click="openSchedule(app)">
                                        <i class="bi bi-calendar-plus me-1"></i>Schedule
                                    </button>
                                </template>
                                <template x-if="!app.pitchDate && app.status !== 'Top 20'">
                                    <span class="text-muted small">—</span>
                                </template>
                            </td>

                            {{-- DD Status --}}
                            <td>
                                <template x-if="app.ddStatus">
                                    <div>
                                        <span class="sl-dd-badge"
                                              :class="app.ddStatus==='Pass'?'dd-pass':app.ddStatus==='Fail'?'dd-fail':'dd-pending'"
                                              x-text="app.ddStatus">
                                        </span>
                                        <div class="sl-sub mt-1" x-text="app.ddDate" x-show="app.ddDate"></div>
                                    </div>
                                </template>
                                <template x-if="!app.ddStatus && ['Top 10','Provisional Top 10'].includes(app.status)">
                                    <button class="btn sl-ghost-xs" @click="openDD(app)">
                                        <i class="bi bi-clipboard-check me-1"></i>Schedule DD
                                    </button>
                                </template>
                                <template x-if="!app.ddStatus && !['Top 10','Provisional Top 10'].includes(app.status)">
                                    <span class="text-muted small">—</span>
                                </template>
                            </td>

                            {{-- Status --}}
                            <td>
                                <span class="sl-status-pill"
                                      :class="statusClass(app.status)"
                                      x-text="app.status">
                                </span>
                                <div class="sl-sub mt-1" x-show="app.status === 'DD Fail'">
                                    <button class="text-primary small border-0 bg-transparent p-0 text-decoration-underline"
                                            @click="openReplacement(app)">
                                        Find replacement
                                    </button>
                                </div>
                            </td>

                            {{-- Actions --}}
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-1">
                                    <button class="sl-action-btn" @click="openView(app)" title="View Application">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="sl-action-btn" @click="openPitchScore(app)"
                                            title="Score Pitch"
                                            x-show="app.status === 'Top 20' || app.status === 'Pitched'">
                                        <i class="bi bi-mic-fill"></i>
                                    </button>
                                    <button class="sl-action-btn" @click="openDD(app)"
                                            title="Due Diligence"
                                            x-show="['Top 10','Provisional Top 10','DD Pass','DD Fail'].includes(app.status)">
                                        <i class="bi bi-clipboard-check"></i>
                                    </button>
                                    <div class="dropdown">
                                        <button class="sl-action-btn" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end sl-dropdown shadow border-0 small">
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2" href="#"
                                                   @click.prevent="openSchedule(app)">
                                                    <i class="bi bi-calendar-event text-primary"></i>Schedule Pitch Slot
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2" href="#"
                                                   @click.prevent="sendInvitation(app)">
                                                    <i class="bi bi-envelope text-info"></i>Send Pitch Invitation
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider my-1"></li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2" href="#"
                                                   @click.prevent="promoteApp(app)"
                                                   x-show="app.rank > 20">
                                                    <i class="bi bi-arrow-up-circle text-success"></i>Promote as Replacement
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2 text-danger" href="#"
                                                   @click.prevent="removeFromList(app)">
                                                    <i class="bi bi-x-circle"></i>Remove from Shortlist
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filtered.length === 0">
                        <td colspan="11" class="text-center py-5">
                            <div class="sl-empty">
                                <i class="bi bi-trophy d-block mb-2"></i>
                                <p class="mb-0">No applications in this view.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="sl-pag-bar">
            <small class="text-muted">
                Showing <span x-text="Math.min((page-1)*perPage+1, filtered.length)"></span>–<span
                    x-text="Math.min(page*parseInt(perPage), filtered.length)"></span>
                of <span x-text="filtered.length"></span>
            </small>
            <div class="d-flex gap-2 align-items-center">
                <select class="sl-select" style="width:auto;" x-model="perPage" @change="page=1">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">All</option>
                </select>
                <div class="d-flex gap-1">
                    <button class="sl-pg-btn" @click="page--" :disabled="page===1"><i class="bi bi-chevron-left"></i></button>
                    <template x-for="p in totalPages" :key="p">
                        <button class="sl-pg-btn" :class="page===p?'sl-pg-active':''" @click="page=p" x-text="p"></button>
                    </template>
                    <button class="sl-pg-btn" @click="page++" :disabled="page===totalPages"><i class="bi bi-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </div>


    {{-- ══════════════════════════════════════
         PITCH SCHEDULE PANEL (bottom card, shown during pitch stage)
    ══════════════════════════════════════ --}}
    <div class="sl-card mb-4" x-show="stage === 'pitch' || stage === 'due-diligence'">
        <div class="sl-card-header">
            <div class="sl-icon-sm" style="background:rgba(6,182,212,.1);color:#06b6d4;">
                <i class="bi bi-calendar-week-fill"></i>
            </div>
            <div>
                <div class="fw-semibold">Pitch Schedule</div>
                <div class="sl-section-sub">All pitch slots for the Top 20 shortlisted enterprises</div>
            </div>
            <button class="btn sl-btn-primary btn-sm ms-auto" @click="openAddPitchSlot()">
                <i class="bi bi-plus-circle me-1"></i>Add Slot
            </button>
        </div>
        <div class="table-responsive">
            <table class="table sl-table align-middle mb-0 small">
                <thead>
                    <tr>
                        <th class="px-4">Date &amp; Time</th>
                        <th>Venue / Mode</th>
                        <th>Enterprise</th>
                        <th class="text-center">Pitch Score</th>
                        <th>Evaluators</th>
                        <th>Invitation</th>
                        <th>Status</th>
                        <th class="text-center pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="slot in pitchSlots" :key="slot.id">
                        <tr>
                            <td class="px-4 py-3">
                                <div class="fw-semibold" x-text="slot.date"></div>
                                <div class="sl-sub" x-text="slot.time"></div>
                            </td>
                            <td>
                                <div class="small fw-medium" x-text="slot.venue"></div>
                                <div class="sl-sub">
                                    <i class="bi" :class="slot.mode==='In-Person'?'bi-building':'bi-camera-video'"></i>
                                    <span x-text="slot.mode" class="ms-1"></span>
                                </div>
                            </td>
                            <td>
                                <div class="fw-medium small" x-text="slot.enterprise"></div>
                                <div class="sl-sub" x-text="slot.appId"></div>
                            </td>
                            <td class="text-center">
                                <template x-if="slot.pitchScore !== null">
                                    <span class="fw-bold" :class="scoreColor(slot.pitchScore)"
                                          x-text="slot.pitchScore + '%'"></span>
                                </template>
                                <template x-if="slot.pitchScore === null && slot.slotStatus !== 'Upcoming'">
                                    <button class="btn sl-score-btn btn-sm" @click="openPitchScoreById(slot.appId)">
                                        <i class="bi bi-pencil me-1"></i>Score
                                    </button>
                                </template>
                                <template x-if="slot.pitchScore === null && slot.slotStatus === 'Upcoming'">
                                    <span class="text-muted">—</span>
                                </template>
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <template x-for="ev in slot.evaluators">
                                        <span class="sl-ev-chip" x-text="ev" :title="ev"></span>
                                    </template>
                                </div>
                            </td>
                            <td>
                                <span class="sl-dd-badge"
                                      :class="slot.invitationSent ? 'dd-pass' : 'dd-pending'"
                                      x-text="slot.invitationSent ? 'Sent' : 'Pending'">
                                </span>
                            </td>
                            <td>
                                <span class="sl-status-pill"
                                      :class="slot.slotStatus==='Completed'?'pill-completed':slot.slotStatus==='Upcoming'?'pill-upcoming':'pill-draft'"
                                      x-text="slot.slotStatus">
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex gap-1 justify-content-center">
                                    <button class="sl-action-btn" title="Edit Slot" @click="editSlot(slot)">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="sl-action-btn" title="Send Invitation"
                                            x-show="!slot.invitationSent"
                                            @click="sendInvitationForSlot(slot)">
                                        <i class="bi bi-envelope"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>


    {{-- ══════════════════════════════════════
         APPLICATION DETAIL PANEL (right drawer)
    ══════════════════════════════════════ --}}
    <div class="sl-backdrop" x-show="showView" x-transition.opacity @click="showView=false"></div>
    <div class="sl-panel" x-show="showView" x-transition.opacity>
        <div class="sl-panel-inner" x-if="activeApp">
            <div class="sl-panel-header">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <div class="sl-rank-badge"
                                 :class="activeApp?.rank<=3?'rank-gold':activeApp?.rank<=10?'rank-purple':'rank-blue'"
                                 x-text="'#' + activeApp?.rank">
                            </div>
                            <span class="sl-status-pill" :class="statusClass(activeApp?.status)"
                                  x-text="activeApp?.status"></span>
                        </div>
                        <h5 class="fw-bold mb-0" x-text="activeApp?.enterprise"></h5>
                        <div class="sl-sub" x-text="activeApp?.id + ' · ' + activeApp?.owner + ' · ' + activeApp?.district"></div>
                    </div>
                    <button class="btn sl-btn-ghost btn-sm" @click="showView=false">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            <div class="sl-panel-body">
                {{-- Score summary --}}
                <div class="row g-3 mb-4">
                    <div class="col-4">
                        <div class="sl-score-card">
                            <div class="sl-score-card-label">Evaluation</div>
                            <div class="sl-score-card-val" :class="scoreColor(activeApp?.evalScore)"
                                 x-text="activeApp?.evalScore + '%'"></div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="sl-score-card">
                            <div class="sl-score-card-label">Pitch</div>
                            <div class="sl-score-card-val"
                                 :class="activeApp?.pitchScore ? scoreColor(activeApp?.pitchScore) : 'text-muted'"
                                 x-text="activeApp?.pitchScore !== null ? activeApp?.pitchScore + '%' : '—'">
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="sl-score-card">
                            <div class="sl-score-card-label">Total</div>
                            <div class="sl-score-card-val" :class="totalScoreClass(activeApp)"
                                 x-text="totalScore(activeApp) + '%'">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Details DL --}}
                <dl class="sl-dl mb-4">
                    <div class="sl-dl-row"><dt>Sector</dt><dd x-text="activeApp?.sector"></dd></div>
                    <div class="sl-dl-row"><dt>District</dt><dd x-text="activeApp?.district"></dd></div>
                    <div class="sl-dl-row"><dt>Stage</dt><dd x-text="activeApp?.stage"></dd></div>
                    <div class="sl-dl-row"><dt>Owner</dt><dd x-text="activeApp?.owner"></dd></div>
                    <div class="sl-dl-row"><dt>Employees</dt><dd x-text="activeApp?.employees"></dd></div>
                    <div class="sl-dl-row"><dt>Monthly Revenue</dt><dd x-text="'M ' + activeApp?.revenue"></dd></div>
                    <div class="sl-dl-row">
                        <dt>PDO Flags</dt>
                        <dd>
                            <div class="d-flex gap-1">
                                <span class="sl-pdo" :class="activeApp?.isWoman?'pdo-w':'pdo-off'">W</span>
                                <span class="sl-pdo" :class="activeApp?.isYouth?'pdo-y':'pdo-off'">Y</span>
                                <span class="sl-pdo" :class="activeApp?.isRural?'pdo-r':'pdo-off'">R</span>
                            </div>
                        </dd>
                    </div>
                </dl>

                {{-- Pitch info --}}
                <div class="mb-4" x-show="activeApp?.pitchDate">
                    <div class="sl-section-label mb-2">Pitch Details</div>
                    <div class="p-3 rounded-3 border" style="background:#f8fafc;">
                        <div class="d-flex gap-4 small">
                            <div><div class="sl-sub">Date</div><div class="fw-semibold" x-text="activeApp?.pitchDate"></div></div>
                            <div><div class="sl-sub">Time</div><div class="fw-semibold" x-text="activeApp?.pitchTime"></div></div>
                            <div><div class="sl-sub">Venue</div><div class="fw-semibold" x-text="activeApp?.pitchVenue"></div></div>
                        </div>
                    </div>
                </div>

                {{-- Due Diligence info --}}
                <div class="mb-4" x-show="activeApp?.ddStatus">
                    <div class="sl-section-label mb-2">Due Diligence</div>
                    <div class="p-3 rounded-3 border"
                         :class="activeApp?.ddStatus==='Pass'?'border-success bg-success bg-opacity-5':activeApp?.ddStatus==='Fail'?'border-danger bg-danger bg-opacity-5':''"
                         style="background:#f8fafc;">
                        <div class="d-flex gap-4 small mb-2">
                            <div><div class="sl-sub">Result</div>
                                <span class="sl-dd-badge"
                                      :class="activeApp?.ddStatus==='Pass'?'dd-pass':activeApp?.ddStatus==='Fail'?'dd-fail':'dd-pending'"
                                      x-text="activeApp?.ddStatus">
                                </span>
                            </div>
                            <div><div class="sl-sub">Date</div><div class="fw-semibold" x-text="activeApp?.ddDate || '—'"></div></div>
                        </div>
                        <div class="sl-sub" x-show="activeApp?.ddNotes">Notes: <span class="text-dark" x-text="activeApp?.ddNotes"></span></div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="sl-section-label mb-2">Actions</div>
                <div class="d-flex flex-column gap-2">
                    <button class="btn sl-action-link text-start"
                            @click="openPitchScore(activeApp)"
                            x-show="activeApp?.status === 'Top 20' || activeApp?.status === 'Pitched'">
                        <i class="bi bi-mic-fill me-2 text-info"></i>Score Pitch
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </button>
                    <button class="btn sl-action-link text-start"
                            @click="openSchedule(activeApp)"
                            x-show="!activeApp?.pitchDate && activeApp?.status === 'Top 20'">
                        <i class="bi bi-calendar-plus me-2 text-primary"></i>Schedule Pitch Slot
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </button>
                    <button class="btn sl-action-link text-start"
                            @click="sendInvitation(activeApp)"
                            x-show="activeApp?.status === 'Top 20'">
                        <i class="bi bi-envelope me-2 text-success"></i>Send Pitch Invitation
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </button>
                    <button class="btn sl-action-link text-start"
                            @click="openDD(activeApp)"
                            x-show="['Top 10','Provisional Top 10'].includes(activeApp?.status)">
                        <i class="bi bi-clipboard-check me-2 text-warning"></i>Record DD Outcome
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </button>
                    <button class="btn sl-action-link text-start"
                            @click="openReplacement(activeApp)"
                            x-show="activeApp?.status === 'DD Fail'">
                        <i class="bi bi-arrow-repeat me-2 text-danger"></i>Find Replacement Candidate
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>


   


  

</div>{{-- end page --}}


{{-- ══════════════════════════════════════ STYLES ══════════════════════════════════════ --}}
<style>
.sl-page {
    --r:14px; --r-sm:8px; --r-xs:6px;
    --border:#e8ecf0; --bg:#f8fafc;
    --text:#0f172a; --muted:#64748b;
    font-family:'Inter',system-ui,sans-serif; color:var(--text);
}

/* ── Buttons ── */
.sl-btn-primary { background:#1d4ed8; color:#fff; border:none; border-radius:var(--r-sm); font-weight:600; font-size:.82rem; padding:8px 16px; transition:all .15s; }
.sl-btn-primary:hover { background:#1e40af; color:#fff; transform:translateY(-1px); }
.sl-btn-ghost { background:transparent; border:1px solid var(--border); color:var(--text); border-radius:var(--r-sm); font-weight:500; font-size:.82rem; padding:8px 14px; transition:all .12s; }
.sl-btn-ghost:hover { background:var(--bg); }
.sl-ghost-xs { font-size:.72rem; padding:2px 8px; border:1px solid var(--border); border-radius:6px; background:#fff; color:var(--muted); cursor:pointer; transition:all .12s; }
.sl-ghost-xs:hover { border-color:#93c5fd; color:#1d4ed8; background:#eff6ff; }
.sl-score-btn { font-size:.72rem; padding:2px 10px; background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; border-radius:6px; cursor:pointer; }

/* ── Stepper ── */
.sl-stepper-step {
    padding:16px 20px; cursor:pointer; transition:all .15s;
    border-right:1px solid var(--border);
    position:relative;
}
.sl-stepper-step:last-child { border-right:none; }
.sl-stepper-step:hover { background:#fafbff; }
.sl-step-done { background:linear-gradient(180deg,#f0fdf4 0%,#fff 100%); }
.sl-step-future {}
.sl-step-circle { width:26px; height:26px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.72rem; flex-shrink:0; }
.sc-done  { background:#10b981; color:#fff; }
.sc-active{ background:#1d4ed8; color:#fff; box-shadow:0 0 0 3px rgba(29,78,216,.2); }
.sc-future{ background:#e9ecef; color:#6c757d; }
.sl-step-desc { font-size:.68rem; color:var(--muted); margin-top:2px; }
.sl-step-count{ font-size:.68rem; font-weight:700; margin-top:4px; }

/* ── KPI ── */
.sl-kpi {
    background:#fff; border:1px solid var(--border); border-radius:var(--r);
    padding:14px; display:flex; align-items:flex-start; gap:12px;
    box-shadow:0 1px 3px rgba(0,0,0,.04); cursor:pointer; transition:all .15s;
}
.sl-kpi:hover { transform:translateY(-2px); box-shadow:0 4px 16px rgba(0,0,0,.08); }
.sl-kpi-icon { width:38px; height:38px; border-radius:10px; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:.9rem; }
.sl-kpi-val  { font-size:1.5rem; font-weight:800; letter-spacing:-.03em; line-height:1; }
.sl-kpi-label{ font-size:.72rem; color:var(--muted); font-weight:500; margin-top:2px; }

/* ── Cards ── */
.sl-card { background:#fff; border:1px solid var(--border); border-radius:var(--r); overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); }
.sl-card-header { display:flex; align-items:center; gap:12px; padding:14px 20px; border-bottom:1px solid var(--border); background:#fafbfc; flex-wrap:wrap; }
.sl-icon-sm { width:28px; height:28px; border-radius:7px; background:rgba(59,130,246,.1); color:#3b82f6; display:flex; align-items:center; justify-content:center; font-size:.78rem; flex-shrink:0; }
.sl-section-sub { font-size:.72rem; color:var(--muted); }

/* ── Tab bar ── */
.sl-tab-row { display:flex; gap:0; overflow-x:auto; }
.sl-tab { padding:10px 16px; border:none; background:none; white-space:nowrap; font-size:.82rem; font-weight:500; color:var(--muted); border-bottom:2px solid transparent; cursor:pointer; transition:all .12s; }
.sl-tab:hover { color:var(--text); }
.sl-tab-active { color:#1d4ed8 !important; border-bottom-color:#1d4ed8 !important; font-weight:600; }
.sl-tab-badge { background:#f1f5f9; color:var(--muted); border-radius:99px; padding:1px 7px; font-size:.65rem; font-weight:700; margin-left:4px; }
.sl-tab-active .sl-tab-badge { background:#dbeafe; color:#1d4ed8; }
.sl-count-badge { background:#eff6ff; color:#1d4ed8; border-radius:99px; padding:2px 10px; font-size:.72rem; font-weight:700; }

/* ── Search / select ── */
.sl-search-wrap { position:relative; }
.sl-search-icon { position:absolute; left:9px; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:.75rem; }
.sl-search-input { padding:7px 9px 7px 28px; border:1px solid var(--border); border-radius:var(--r-sm); font-size:.82rem; background:#fff; width:210px; }
.sl-search-input:focus { outline:none; border-color:#3b82f6; }
.sl-select { padding:7px 10px; border:1px solid var(--border); border-radius:var(--r-sm); font-size:.82rem; background:#fff; color:var(--text); }

/* ── Table ── */
.sl-table { font-size:.82rem; }
.sl-table thead tr { background:#f8fafc; }
.sl-table thead th { font-weight:600; font-size:.67rem; text-transform:uppercase; letter-spacing:.05em; color:var(--muted); padding:10px 12px; border-bottom:1px solid var(--border); border-top:none; white-space:nowrap; }
.sl-table tbody td { padding:10px 12px; border-bottom:1px solid #f8fafc; }
.sl-table tbody tr:hover { background:#fafbff; }
.sl-row-accepted { background:#f0fdf4 !important; }
.sl-row-failed   { background:#fff1f2 !important; opacity:.7; }
.sl-row-replaced { background:#f1f5f9 !important; opacity:.5; }

/* ── Rank badges ── */
.sl-rank-badge { width:30px; height:30px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:.72rem; font-weight:800; flex-shrink:0; }
.rank-gold   { background:#fef08a; color:#a16207; border:2px solid #eab308; }
.rank-silver { background:#e5e7eb; color:#374151; border:2px solid #9ca3af; }
.rank-bronze { background:#fed7aa; color:#9a3412; border:2px solid #f97316; }
.rank-purple { background:#ede9fe; color:#7c3aed; border:2px solid #c4b5fd; }
.rank-blue   { background:#dbeafe; color:#1d4ed8; border:2px solid #93c5fd; }
.rank-default{ background:#f3f4f6; color:#6b7280; border:2px solid #e5e7eb; }

/* ── Avatars ── */
.sl-av { width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:.68rem; font-weight:800; flex-shrink:0; }
.av-blue{background:#dbeafe;color:#1d4ed8;} .av-green{background:#dcfce7;color:#15803d;}
.av-orange{background:#ffedd5;color:#c2410c;} .av-purple{background:#ede9fe;color:#7c3aed;}
.av-pink{background:#fce7f3;color:#be185d;} .av-teal{background:#ccfbf1;color:#0f766e;}

/* ── Tags ── */
.sl-sector-tag { font-size:.72rem; font-weight:500; color:#1d4ed8; background:#eff6ff; border:1px solid #bfdbfe; border-radius:5px; padding:2px 9px; }
.sl-total-score { display:inline-flex; align-items:center; justify-content:center; font-size:.88rem; font-weight:800; width:48px; height:28px; border-radius:7px; background:#f1f5f9; }

/* ── Status pills ── */
.sl-status-pill { display:inline-flex; align-items:center; font-size:.66rem; font-weight:700; letter-spacing:.04em; text-transform:uppercase; padding:3px 9px; border-radius:99px; white-space:nowrap; }
.pill-top20     { background:#fef9c3; color:#a16207; }
.pill-pitched   { background:#e0f2fe; color:#0369a1; }
.pill-top10     { background:#ede9fe; color:#7c3aed; }
.pill-prov-top10{ background:#fce7f3; color:#be185d; }
.pill-ddpass    { background:#dcfce7; color:#15803d; }
.pill-ddfail    { background:#fee2e2; color:#dc2626; }
.pill-accepted  { background:#dcfce7; color:#15803d; }
.pill-replaced  { background:#f1f5f9; color:#64748b; }
.pill-not-shortlisted { background:#f1f5f9; color:#94a3b8; }
.pill-completed { background:#ede9fe; color:#7c3aed; }
.pill-upcoming  { background:#dbeafe; color:#1d4ed8; }
.pill-draft     { background:#f1f5f9; color:#94a3b8; }

/* ── DD badges ── */
.sl-dd-badge { display:inline-block; font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; border-radius:99px; padding:2px 9px; }
.dd-pass    { background:#dcfce7; color:#15803d; }
.dd-fail    { background:#fee2e2; color:#dc2626; }
.dd-pending { background:#fef9c3; color:#a16207; }

/* ── PDO dots ── */
.sl-pdo { width:20px; height:20px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.58rem; font-weight:800; flex-shrink:0; }
.pdo-w{background:#fce7f3;color:#be185d;} .pdo-y{background:#dcfce7;color:#15803d;}
.pdo-r{background:#fef9c3;color:#a16207;} .pdo-off{background:#f1f5f9;color:#d1d5db;}

/* ── Evaluator chips ── */
.sl-ev-chip { font-size:.65rem; font-weight:600; background:#f1f5f9; color:var(--muted); border-radius:4px; padding:1px 6px; }

/* ── Pagination ── */
.sl-pag-bar { display:flex; align-items:center; justify-content:space-between; padding:12px 20px; border-top:1px solid var(--border); flex-wrap:wrap; gap:8px; }
.sl-pg-btn { width:30px; height:30px; border-radius:6px; border:1px solid var(--border); background:#fff; color:var(--muted); cursor:pointer; font-size:.75rem; display:inline-flex; align-items:center; justify-content:center; transition:all .12s; }
.sl-pg-btn:disabled { opacity:.35; cursor:default; }
.sl-pg-btn:not(:disabled):hover { border-color:#93c5fd; color:#1d4ed8; background:#eff6ff; }
.sl-pg-active { background:#1d4ed8 !important; border-color:#1d4ed8 !important; color:#fff !important; font-weight:700; }

/* ── Progress ── */
.sl-prog-track { height:6px; background:#f1f5f9; border-radius:99px; overflow:hidden; }
.sl-prog-fill  { height:100%; border-radius:99px; min-width:2px; transition:width .4s ease; }

/* ── Panel ── */
.sl-backdrop { position:fixed; inset:0; background:rgba(0,0,0,.4); z-index:1040; }
.sl-panel { position:fixed; top:0; right:0; bottom:0; width:100%; max-width:520px; z-index:1050; background:#fff; display:flex; flex-direction:column; box-shadow:-4px 0 32px rgba(0,0,0,.14); }
.sl-panel-inner { display:flex; flex-direction:column; height:100%; }
.sl-panel-header { padding:20px 24px 16px; border-bottom:1px solid var(--border); flex-shrink:0; }
.sl-panel-body { padding:20px 24px; overflow-y:auto; flex-grow:1; }
@media (max-width:576px) { .sl-panel { max-width:100%; } }

/* ── Score cards (panel & modal) ── */
.sl-score-card { padding:14px; border-radius:var(--r-sm); border:1px solid var(--border); background:var(--bg); text-align:center; }
.sl-score-card-label { font-size:.67rem; text-transform:uppercase; letter-spacing:.05em; font-weight:700; color:var(--muted); margin-bottom:4px; }
.sl-score-card-val   { font-size:1.6rem; font-weight:800; letter-spacing:-.03em; line-height:1; }

/* ── Score box (sliders) ── */
.sl-score-box { width:60px; height:32px; border-radius:7px; display:flex; align-items:center; justify-content:center; font-size:.76rem; font-weight:700; flex-shrink:0; }
.score-high { background:#dcfce7; color:#15803d; }
.score-mid  { background:#fef9c3; color:#a16207; }
.score-low  { background:#f1f5f9; color:#94a3b8; }
.sl-max-badge { font-size:.68rem; background:#f1f5f9; color:var(--muted); border-radius:4px; padding:2px 7px; flex-shrink:0; align-self:flex-start; }

/* ── DL ── */
.sl-dl { margin:0; }
.sl-dl-row { display:flex; align-items:center; justify-content:space-between; padding:9px 0; border-bottom:1px solid #f1f5f9; font-size:.82rem; gap:12px; }
.sl-dl-row dt { color:var(--muted); font-weight:500; white-space:nowrap; }
.sl-dl-row dd { margin:0; text-align:right; color:var(--text); }

/* ── Action links ── */
.sl-action-btn { width:28px; height:28px; border-radius:6px; border:1px solid var(--border); background:#fff; color:var(--muted); cursor:pointer; font-size:.78rem; display:inline-flex; align-items:center; justify-content:center; transition:all .12s; }
.sl-action-btn:hover { background:#eff6ff; border-color:#93c5fd; color:#1d4ed8; }
.sl-action-link { display:flex; align-items:center; gap:8px; padding:10px 12px; border-radius:var(--r-sm); border:1px solid var(--border); background:#fff; font-size:.82rem; font-weight:500; color:var(--text); cursor:pointer; transition:all .12s; }
.sl-action-link:hover { background:#eff6ff; border-color:#93c5fd; color:#1d4ed8; }

/* ── Modal / forms ── */
.sl-modal { border-radius:var(--r) !important; box-shadow:0 20px 60px rgba(0,0,0,.18); }
.sl-form-label { display:block; font-size:.72rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.04em; margin-bottom:4px; }
.sl-input { width:100%; padding:8px 12px; border:1px solid var(--border); border-radius:var(--r-sm); font-size:.85rem; color:var(--text); background:#fff; transition:border-color .15s; }
.sl-input:focus { outline:none; border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,.1); }
textarea.sl-input { resize:vertical; min-height:76px; }
.sl-check-card { display:flex; align-items:center; gap:12px; padding:12px 14px; border-radius:var(--r-sm); border:1.5px solid var(--border); cursor:pointer; transition:all .12s; }
.sl-check-card:hover { border-color:#93c5fd; background:#fafcff; }
.sl-check-card-active { border-color:#3b82f6 !important; background:#eff6ff !important; }

/* ── Dropdown ── */
.sl-dropdown { border-radius:var(--r-sm) !important; padding:6px; min-width:190px; }
.sl-dropdown .dropdown-item { border-radius:var(--r-xs); font-size:.8rem; padding:8px 10px; }

/* ── Sub text ── */
.sl-sub { font-size:.68rem; color:var(--muted); }
.sl-section-label { font-size:.67rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); }

/* ── Empty ── */
.sl-empty { color:var(--muted); text-align:center; padding:32px; }
.sl-empty i { font-size:2.2rem; opacity:.3; }
.sl-empty p { font-size:.85rem; margin:0; }

/* ── Toast ── */
.sl-toast { display:flex; align-items:center; gap:10px; padding:12px 16px; border-radius:var(--r-sm); font-size:.85rem; font-weight:500; min-width:280px; box-shadow:0 4px 20px rgba(0,0,0,.15); }
.sl-toast-ok  { background:#064e3b; color:#ecfdf5; }
.sl-toast-err { background:#7f1d1d; color:#fef2f2; }
</style>


{{-- ══════════════════════════════════════ ALPINE JS ══════════════════════════════════════ --}}
<script>
function shortlistApp() {
    return {
        stage: 'shortlist',
        activeStatusFilter: 'all',
        search: '', filterSector: '',
        page: 1, perPage: 20,
        showView: false, showPitchScore: false, showSchedule: false,
        showDD: false, showReplacement: false, showConfirmTop: false,
        showToast: false, toastMsg: '', toastType: 'success',
        activeApp: null, scoringApp: null, schedulingApp: null,
        ddApp: null, replacementApp: null,
        selectedReplacement: null,
        confirmTopTitle: '', confirmTopMessage: '', confirmTopAction: '',
        filtered: [],

        stages: [
            { key:'shortlist',    label:'Top 20 Shortlist',    icon:'bi-star-fill',        desc:'Confirm ranked shortlist',       badge:null },
            { key:'pitch',        label:'Pitch Event',          icon:'bi-mic-fill',         desc:'Schedule & score pitches',       badge:null },
            { key:'due-diligence',label:'Due Diligence',        icon:'bi-shield-check',     desc:'DD checks for Top 10',           badge:null },
            { key:'final',        label:'Final Cohort',         icon:'bi-people-fill',      desc:'Confirm final 10 enterprises',   badge:null },
        ],

        stageTabs: [
            { key:'all',               label:'All'               },
            { key:'Top 20',            label:'Top 20'            },
            { key:'Pitched',           label:'Pitched'           },
            { key:'Top 10',            label:'Top 10'            },
            { key:'Provisional Top 10',label:'Prov. Top 10'      },
            { key:'DD Pass',           label:'DD Pass'           },
            { key:'DD Fail',           label:'DD Fail'           },
            { key:'Final Accepted',    label:'Final Accepted'    },
            { key:'Not Shortlisted',   label:'Not Shortlisted'   },
        ],

        pitchCriteria: [
            { label:'Clarity of Business Idea',       hint:'Clear articulation of problem and solution',            max:20, score:0 },
            { label:'Market Understanding',           hint:'Evidence of market research and customer validation',   max:20, score:0 },
            { label:'Financial Projections',          hint:'Realistic and well-reasoned financial forecasts',       max:20, score:0 },
            { label:'Team Capability',                hint:'Demonstrated capacity to execute the business plan',    max:20, score:0 },
            { label:'Presentation Quality',           hint:'Confidence, clarity, and professional delivery',        max:20, score:0 },
        ],
        pitchScoreForm: { total:0, comments:'' },

        scheduleForm: { date:'', time:'', venue:'', mode:'In-Person', evaluators:[], sendInvitation:true },
        ddForm: { date:'', officer:'', result:'', failReason:'', notes:'', notifyApplicant:true },

        applications: [
            { id:'APP-008', rank:1,  enterprise:'Lerato Crafts & Arts',   owner:'Lerato Nkosi',     district:'Berea',        sector:'Manufacturing',  stage:'Registered', isWoman:true,  isYouth:true,  isRural:false, evalScore:88, pitchScore:85, ddStatus:'Pass', ddDate:'20 Mar 2025', ddNotes:'All documents verified. Fully operational.',        pitchDate:'15 Mar 2025', pitchTime:'09:00',  pitchVenue:'LEHSFF Boardroom', status:'Final Accepted', employees:3, revenue:'62,000' },
            { id:'APP-001', rank:2,  enterprise:'MoroAgri Basotho',        owner:'Limpho Mokoena',   district:'Maseru',       sector:'Agriculture',    stage:'Registered', isWoman:true,  isYouth:true,  isRural:false, evalScore:78, pitchScore:80, ddStatus:'Pass', ddDate:'20 Mar 2025', ddNotes:'Strong team and clear market strategy.',             pitchDate:'15 Mar 2025', pitchTime:'10:00',  pitchVenue:'LEHSFF Boardroom', status:'Final Accepted', employees:4, revenue:'120,000' },
            { id:'APP-005', rank:3,  enterprise:'Naledi Weave Studio',     owner:'Naledi Sello',     district:'Mafeteng',     sector:'Textile',        stage:'Registered', isWoman:true,  isYouth:false, isRural:true,  evalScore:82, pitchScore:78, ddStatus:'Pass', ddDate:'21 Mar 2025', ddNotes:'Rural-based, strong PDO alignment.',                 pitchDate:'15 Mar 2025', pitchTime:'11:00',  pitchVenue:'LEHSFF Boardroom', status:'Final Accepted', employees:5, revenue:'95,000' },
            { id:'APP-011', rank:4,  enterprise:'Basali Bakery Co.',       owner:'Mamello Leseli',   district:'Maseru',       sector:'Food & Beverage',stage:'Registered', isWoman:true,  isYouth:false, isRural:false, evalScore:80, pitchScore:76, ddStatus:null,   ddDate:null,           ddNotes:'',                                                   pitchDate:'16 Mar 2025', pitchTime:'09:00',  pitchVenue:'LEHSFF Boardroom', status:'Top 10',       employees:6, revenue:'88,000' },
            { id:'APP-012', rank:5,  enterprise:'Ha-Ntho Fashion House',   owner:'Nthoana Molefe',   district:'Leribe',       sector:'Textile',        stage:'Registered', isWoman:true,  isYouth:true,  isRural:false, evalScore:79, pitchScore:74, ddStatus:null,   ddDate:null,           ddNotes:'',                                                   pitchDate:'16 Mar 2025', pitchTime:'10:00',  pitchVenue:'LEHSFF Boardroom', status:'Top 10',       employees:4, revenue:'71,000' },
            { id:'APP-013', rank:6,  enterprise:'Khahliso Solar Tech',     owner:'Khahliso Pule',    district:'Maseru',       sector:'Technology',     stage:'Startup',    isWoman:false, isYouth:true,  isRural:false, evalScore:77, pitchScore:73, ddStatus:null,   ddDate:null,           ddNotes:'',                                                   pitchDate:'16 Mar 2025', pitchTime:'11:00',  pitchVenue:'LEHSFF Boardroom', status:'Provisional Top 10', employees:2, revenue:'34,000' },
            { id:'APP-014', rank:7,  enterprise:'Litšoele Poultry Farm',   owner:'Litšoele Sello',   district:'Berea',        sector:'Agriculture',    stage:'Registered', isWoman:false, isYouth:false, isRural:true,  evalScore:76, pitchScore:71, ddStatus:null,   ddDate:null,           ddNotes:'',                                                   pitchDate:'17 Mar 2025', pitchTime:'09:00',  pitchVenue:'LEHSFF Boardroom', status:'Pitched',      employees:5, revenue:'92,000' },
            { id:'APP-015', rank:8,  enterprise:'Selemo Crafts & Tourism', owner:'Selemo Mahase',    district:'Mokhotlong',   sector:'Manufacturing',  stage:'Startup',    isWoman:true,  isYouth:false, isRural:true,  evalScore:75, pitchScore:null,ddStatus:null,   ddDate:null,           ddNotes:'',                                                   pitchDate:'17 Mar 2025', pitchTime:'10:00',  pitchVenue:'LEHSFF Boardroom', status:'Top 20',       employees:3, revenue:'41,000' },
            { id:'APP-016', rank:9,  enterprise:'Bolaile Digital Media',   owner:'Bolaile Mokhele',  district:'Maseru',       sector:'Technology',     stage:'Startup',    isWoman:false, isYouth:true,  isRural:false, evalScore:74, pitchScore:null,ddStatus:null,   ddDate:null,           ddNotes:'',                                                   pitchDate:'17 Mar 2025', pitchTime:'11:00',  pitchVenue:'LEHSFF Boardroom', status:'Top 20',       employees:2, revenue:'22,000' },
            { id:'APP-007', rank:10, enterprise:'Selemo Organics',         owner:'Mamello Tsita',    district:"Mohale's Hoek",sector:'Agriculture',    stage:'Registered', isWoman:true,  isYouth:false, isRural:true,  evalScore:71, pitchScore:null,ddStatus:null,   ddDate:null,           ddNotes:'',                                                   pitchDate:null,          pitchTime:null,     pitchVenue:null,               status:'Top 20',       employees:6, revenue:'145,000' },
            { id:'APP-017', rank:11, enterprise:'Potlako Grain Mill',      owner:'Potlako Ntai',     district:'Leribe',       sector:'Agriculture',    stage:'Registered', isWoman:false, isYouth:false, isRural:true,  evalScore:70, pitchScore:null,ddStatus:null,   ddDate:null,           ddNotes:'',                                                   pitchDate:null,          pitchTime:null,     pitchVenue:null,               status:'Top 20',       employees:7, revenue:'130,000' },
            { id:'APP-018', rank:12, enterprise:'Makholo Textiles',        owner:'Makholo Sello',    district:'Mafeteng',     sector:'Textile',        stage:'Registered', isWoman:true,  isYouth:false, isRural:true,  evalScore:69, pitchScore:null,ddStatus:null,   ddDate:null,           ddNotes:'',                                                   pitchDate:null,          pitchTime:null,     pitchVenue:null,               status:'Top 20',       employees:8, revenue:'110,000' },
            { id:'APP-006', rank:13, enterprise:'Molapo Tech Solutions',   owner:'Tšepiso Molapo',   district:'Maseru',       sector:'Technology',     stage:'Startup',    isWoman:false, isYouth:true,  isRural:false, evalScore:68, pitchScore:null,ddStatus:'Fail',  ddDate:'22 Mar 2025',  ddNotes:'Financial records inconsistent with application.',   pitchDate:'17 Mar 2025', pitchTime:'14:00',  pitchVenue:'LEHSFF Boardroom', status:'DD Fail',      employees:1, revenue:'18,000' },
            { id:'APP-019', rank:14, enterprise:'Sejo sa Lesotho Foods',   owner:'Sejo Mohale',      district:'Maseru',       sector:'Food & Beverage',stage:'Startup',    isWoman:true,  isYouth:true,  isRural:false, evalScore:67, pitchScore:null,ddStatus:null,   ddDate:null,           ddNotes:'',                                                   pitchDate:null,          pitchTime:null,     pitchVenue:null,               status:'Top 20',       employees:2, revenue:'28,000' },
            { id:'APP-020', rank:15, enterprise:'Motse Wa Afrika Crafts',  owner:'Mpho Mokoena',     district:'Maseru',       sector:'Manufacturing',  stage:'Registered', isWoman:true,  isYouth:false, isRural:false, evalScore:66, pitchScore:null,ddStatus:null,   ddDate:null,           ddNotes:'',                                                   pitchDate:null,          pitchTime:null,     pitchVenue:null,               status:'Top 20',       employees:4, revenue:'54,000' },
            { id:'APP-021', rank:16, enterprise:'Litlhare Pharmacy Plus',  owner:'Litlhare Khali',   district:'Berea',        sector:'Health',         stage:'Registered', isWoman:true,  isYouth:false, isRural:false, evalScore:65, pitchScore:null,ddStatus:null,   ddDate:null,           ddNotes:'',                                                   pitchDate:null,          pitchTime:null,     pitchVenue:null,               status:'Top 20',       employees:5, revenue:'167,000' },
            { id:'APP-022', rank:17, enterprise:'Koena Digital Hub',       owner:'Koena Mohapi',     district:'Maseru',       sector:'Technology',     stage:'Startup',    isWoman:false, isYouth:true,  isRural:false, evalScore:64, pitchScore:null,ddStatus:null,   ddDate:null,           ddNotes:'',                                                   pitchDate:null,          pitchTime:null,     pitchVenue:null,               status:'Top 20',       employees:2, revenue:'15,000' },
            { id:'APP-023', rank:18, enterprise:'Nkoe Poultry &amp; Eggs',      owner:'Nkoe Tlali',       district:'Leribe',       sector:'Agriculture',    stage:'Registered', isWoman:false, isYouth:false, isRural:true,  evalScore:63, pitchScore:null,ddStatus:null,   ddDate:null,           ddNotes:'',                                                   pitchDate:null,          pitchTime:null,     pitchVenue:null,               status:'Top 20',       employees:4, revenue:'89,000' },
            { id:'APP-024', rank:19, enterprise:'Qhoali Herbs &amp; Spices',    owner:'Qhoali Matsoso',   district:'Mafeteng',     sector:'Agriculture',    stage:'Registered', isWoman:true,  isYouth:false, isRural:true,  evalScore:62, pitchScore:null,ddStatus:null,   ddDate:null,           ddNotes:'',                                                   pitchDate:null,          pitchTime:null,     pitchVenue:null,               status:'Top 20',       employees:3, revenue:'46,000' },
            { id:'APP-025', rank:20, enterprise:'Rapula Youth Ventures',   owner:'Rapula Letsie',    district:'Maseru',       sector:'Technology',     stage:'Startup',    isWoman:false, isYouth:true,  isRural:false, evalScore:61, pitchScore:null,ddStatus:null,   ddDate:null,           ddNotes:'',                                                   pitchDate:null,          pitchTime:null,     pitchVenue:null,               status:'Top 20',       employees:2, revenue:'12,000' },
            { id:'APP-002', rank:21, enterprise:'Lesotho Stitch Co.',      owner:'Thabo Ramokhele',  district:'Leribe',       sector:'Textile',        stage:'Registered', isWoman:false, isYouth:false, isRural:false, evalScore:65, pitchScore:null,ddStatus:null,   ddDate:null,           ddNotes:'',                                                   pitchDate:null,          pitchTime:null,     pitchVenue:null,               status:'Not Shortlisted', employees:8, revenue:'250,000' },
            { id:'APP-003', rank:22, enterprise:'TechForward LS',          owner:'Palesa Nthako',    district:'Maseru',       sector:'Technology',     stage:'Startup',    isWoman:true,  isYouth:true,  isRural:false, evalScore:60, pitchScore:null,ddStatus:null,   ddDate:null,           ddNotes:'',                                                   pitchDate:null,          pitchTime:null,     pitchVenue:null,               status:'Not Shortlisted', employees:2, revenue:'45,000' },
        ],

        pitchSlots: [
            { id:1,  date:'15 Mar 2025', time:'09:00–09:45', venue:'LEHSFF Boardroom', mode:'In-Person', enterprise:'Lerato Crafts & Arts',   appId:'APP-008', pitchScore:85, evaluators:['Dr. LM','TN','PL'], invitationSent:true,  slotStatus:'Completed' },
            { id:2,  date:'15 Mar 2025', time:'10:00–10:45', venue:'LEHSFF Boardroom', mode:'In-Person', enterprise:'MoroAgri Basotho',        appId:'APP-001', pitchScore:80, evaluators:['Dr. LM','TN'],     invitationSent:true,  slotStatus:'Completed' },
            { id:3,  date:'15 Mar 2025', time:'11:00–11:45', venue:'LEHSFF Boardroom', mode:'In-Person', enterprise:'Naledi Weave Studio',     appId:'APP-005', pitchScore:78, evaluators:['Dr. LM','PL'],     invitationSent:true,  slotStatus:'Completed' },
            { id:4,  date:'16 Mar 2025', time:'09:00–09:45', venue:'LEHSFF Boardroom', mode:'In-Person', enterprise:'Basali Bakery Co.',       appId:'APP-011', pitchScore:76, evaluators:['Dr. LM','TN','PL'], invitationSent:true,  slotStatus:'Completed' },
            { id:5,  date:'16 Mar 2025', time:'10:00–10:45', venue:'LEHSFF Boardroom', mode:'In-Person', enterprise:'Ha-Ntho Fashion House',   appId:'APP-012', pitchScore:74, evaluators:['Dr. LM','TN'],     invitationSent:true,  slotStatus:'Completed' },
            { id:6,  date:'17 Mar 2025', time:'09:00–09:45', venue:'Virtual – Zoom',   mode:'Virtual',   enterprise:'Litšoele Poultry Farm',   appId:'APP-014', pitchScore:71, evaluators:['TN','PL'],         invitationSent:true,  slotStatus:'Completed' },
            { id:7,  date:'17 Mar 2025', time:'10:00–10:45', venue:'LEHSFF Boardroom', mode:'In-Person', enterprise:'Selemo Crafts & Tourism', appId:'APP-015', pitchScore:null, evaluators:['Dr. LM','TN'],   invitationSent:true,  slotStatus:'Upcoming'  },
            { id:8,  date:'17 Mar 2025', time:'11:00–11:45', venue:'LEHSFF Boardroom', mode:'In-Person', enterprise:'Bolaile Digital Media',   appId:'APP-016', pitchScore:null, evaluators:['Dr. LM','PL'],   invitationSent:false, slotStatus:'Upcoming'  },
        ],

        replacementCandidates: [],

        get stageIndex() {
            return this.stages.findIndex(s => s.key === this.stage);
        },

        get activeStageLabel() {
            const labels = { shortlist:'Top 20 Shortlist', pitch:'Pitch Event', 'due-diligence':'Due Diligence', final:'Final Cohort' };
            return labels[this.stage] || 'Shortlisting';
        },

        get counts() {
            return {
                totalEvaluated: this.applications.length,
                top20:          this.applications.filter(a => ['Top 20','Pitched','Top 10','Provisional Top 10','DD Pass','DD Fail','Final Accepted'].includes(a.status)).length,
                pitched:        this.applications.filter(a => ['Pitched','Top 10','Provisional Top 10','DD Pass','DD Fail','Final Accepted'].includes(a.status) && a.pitchScore !== null).length,
                top10:          this.applications.filter(a => ['Top 10','DD Pass','DD Fail','Final Accepted'].includes(a.status)).length,
                ddPass:         this.applications.filter(a => a.ddStatus === 'Pass').length,
                ddFail:         this.applications.filter(a => a.ddStatus === 'Fail').length,
                finalAccepted:  this.applications.filter(a => a.status === 'Final Accepted').length,
            };
        },

        get totalPages() {
            return Math.max(1, Math.ceil(this.filtered.length / parseInt(this.perPage)));
        },

        get paginated() {
            const s = (this.page - 1) * parseInt(this.perPage);
            return this.filtered.slice(s, s + parseInt(this.perPage));
        },

        init() { this.applyFilters(); },

        applyFilters() {
            const s = this.search.toLowerCase();
            this.filtered = this.applications.filter(a => {
                const ms = !s || a.enterprise.toLowerCase().includes(s) || a.id.toLowerCase().includes(s);
                const mse = !this.filterSector || a.sector === this.filterSector;
                const mst = this.activeStatusFilter === 'all' || a.status === this.activeStatusFilter;
                return ms && mse && mst;
            });
            this.page = 1;
        },

        resetFilters() { this.search=''; this.filterSector=''; this.activeStatusFilter='all'; this.applyFilters(); },

        filterByStatus(key) { this.activeStatusFilter = key; this.applyFilters(); },

        stepCount(key) {
            const stageStatusMap = {
                shortlist:     ['Top 20','Pitched','Top 10','Provisional Top 10','DD Pass','DD Fail','Final Accepted'],
                pitch:         ['Pitched','Top 10','Provisional Top 10','DD Pass','DD Fail','Final Accepted'],
                'due-diligence':['Top 10','Provisional Top 10','DD Pass','DD Fail','Final Accepted'],
                final:         ['Final Accepted'],
            };
            return this.applications.filter(a => (stageStatusMap[key]||[]).includes(a.status)).length + ' apps';
        },

        tabCount(key) {
            if (key === 'all') return this.applications.length;
            return this.applications.filter(a => a.status === key).length;
        },

        totalScore(app) {
            if (!app) return 0;
            if (app.pitchScore !== null) return Math.round(app.evalScore * 0.6 + app.pitchScore * 0.4);
            return app.evalScore;
        },

        totalScoreClass(app) {
            const s = this.totalScore(app);
            return s >= 75 ? 'text-success' : s >= 60 ? 'text-warning' : 'text-danger';
        },

        statusClass(s) {
            return { 'Top 20':'pill-top20', 'Pitched':'pill-pitched', 'Top 10':'pill-top10',
                     'Provisional Top 10':'pill-prov-top10', 'DD Pass':'pill-ddpass', 'DD Fail':'pill-ddfail',
                     'Final Accepted':'pill-accepted', 'Replaced':'pill-replaced',
                     'Not Shortlisted':'pill-not-shortlisted', 'Completed':'pill-completed',
                     'Upcoming':'pill-upcoming', 'Draft':'pill-draft',
            }[s] || '';
        },

        scoreColor(v) { return v >= 75 ? 'text-success' : v >= 55 ? 'text-warning' : 'text-danger'; },

        sectorColor(sector) {
            return {'Agriculture':'av-green','Technology':'av-blue','Textile':'av-pink',
                    'Manufacturing':'av-orange','Food & Beverage':'av-teal','Health':'av-purple'}[sector]||'av-purple';
        },

        initials(name) { return name.split(' ').slice(0,2).map(w=>w[0].toUpperCase()).join(''); },

        openView(app) { this.activeApp = app; this.showView = true; },

        openPitchScore(app) {
            this.scoringApp = app;
            this.pitchCriteria.forEach(c => c.score = 0);
            this.pitchScoreForm = { total:0, comments:'' };
            this.recalcPitch();
            this.showPitchScore = true;
        },

        openPitchScoreById(appId) {
            const app = this.applications.find(a => a.id === appId);
            if (app) this.openPitchScore(app);
        },

        recalcPitch() {
            const total = this.pitchCriteria.reduce((s,c) => s+c.score, 0);
            const max   = this.pitchCriteria.reduce((s,c) => s+c.max, 0);
            this.pitchScoreForm.total = Math.round(total / max * 100);
        },

        savePitchDraft() { this.toast('Pitch score draft saved.'); },

        submitPitchScore() {
            if (this.scoringApp) {
                this.scoringApp.pitchScore = this.pitchScoreForm.total;
                this.scoringApp.status = 'Pitched';
                this.applyFilters();
                this.toast(`Pitch score ${this.pitchScoreForm.total}% submitted for "${this.scoringApp.enterprise}".`);
            }
            this.showPitchScore = false;
        },

        openSchedule(app) {
            this.schedulingApp = app;
            this.scheduleForm = { date:'', time:'', venue:'LEHSFF Conference Room, Maseru', mode:'In-Person', evaluators:[], sendInvitation:true };
            this.showSchedule = true;
        },

        openAddPitchSlot() { this.schedulingApp = null; this.showSchedule = true; },

        editSlot(slot) { this.toast('Editing slot: ' + slot.enterprise); },

        saveSchedule() {
            if (this.schedulingApp) {
                this.schedulingApp.pitchDate  = this.scheduleForm.date;
                this.schedulingApp.pitchTime  = this.scheduleForm.time;
                this.schedulingApp.pitchVenue = this.scheduleForm.venue;
            }
            this.showSchedule = false;
            this.toast('Pitch slot saved' + (this.scheduleForm.sendInvitation ? ' and invitation sent.' : '.'));
        },

        sendInvitation(app) { this.toast(`Pitch invitation sent to "${app.enterprise}".`); },
        sendInvitationForSlot(slot) { slot.invitationSent = true; this.toast(`Invitation sent to ${slot.enterprise}.`); },
        sendBulkInvitation() { this.toast('Pitch invitations sent to all Top 20 applicants.'); },

        openDD(app) {
            this.ddApp = app;
            this.ddForm = { date:'', officer:'', result:'', failReason:'', notes:'', notifyApplicant:true };
            this.showDD = true;
        },

        saveDD() {
            if (this.ddApp) {
                this.ddApp.ddStatus = this.ddForm.result;
                this.ddApp.ddDate   = this.ddForm.date;
                this.ddApp.ddNotes  = this.ddForm.notes;
                this.ddApp.status   = this.ddForm.result === 'Pass' ? 'DD Pass' : 'DD Fail';
                this.applyFilters();
                this.toast(`Due Diligence outcome "${this.ddForm.result}" recorded for "${this.ddApp.enterprise}".`);
            }
            this.showDD = false;
        },

        openReplacement(app) {
            this.replacementApp = app;
            this.selectedReplacement = null;
            this.replacementCandidates = this.applications
                .filter(a => a.rank > 20 && a.status === 'Not Shortlisted')
                .slice(0, 4);
            this.showReplacement = true;
        },

        confirmReplacement() {
            const cand = this.applications.find(a => a.id === this.selectedReplacement);
            if (cand && this.replacementApp) {
                cand.status = 'Top 20';
                this.replacementApp.status = 'Replaced';
                this.applyFilters();
                this.toast(`"${cand.enterprise}" promoted as replacement for "${this.replacementApp.enterprise}".`);
            }
            this.showReplacement = false;
        },

        confirmTop20() {
            this.confirmTopTitle   = 'Confirm Top 20 Shortlist?';
            this.confirmTopMessage = 'This will lock the shortlist, update all statuses, and send pitch invitations to the Top 20 applicants.';
            this.confirmTopAction  = 'top20';
            this.showConfirmTop    = true;
        },

        confirmTop10() {
            this.confirmTopTitle   = 'Confirm Top 10?';
            this.confirmTopMessage = 'The highest-scoring 10 pitched applicants will be marked as Provisional Top 10 and due diligence will be triggered.';
            this.confirmTopAction  = 'top10';
            this.showConfirmTop    = true;
        },

        doConfirmTop() {
            if (this.confirmTopAction === 'top20') {
                this.stage = 'pitch';
                this.toast('Top 20 confirmed. Pitch invitations sent.');
            } else {
                const top = [...this.applications]
                    .filter(a => a.pitchScore !== null)
                    .sort((a,b) => this.totalScore(b) - this.totalScore(a))
                    .slice(0, 10);
                top.forEach(a => { a.status = 'Provisional Top 10'; });
                this.stage = 'due-diligence';
                this.toast('Top 10 confirmed. Due diligence phase activated.');
                this.applyFilters();
            }
            this.showConfirmTop = false;
        },

        finaliseAll() {
            this.applications.filter(a => a.ddStatus === 'Pass').forEach(a => { a.status = 'Final Accepted'; });
            this.stage = 'final';
            this.applyFilters();
            this.toast('Final cohort confirmed. ESO assignment step is next.');
        },

        promoteApp(app) { app.status = 'Top 20'; this.applyFilters(); this.toast(`"${app.enterprise}" promoted to Top 20.`); },
        removeFromList(app) { app.status = 'Not Shortlisted'; this.applyFilters(); this.toast(`"${app.enterprise}" removed from shortlist.`); },
        exportList() { this.toast('Exporting shortlist…'); },

        toast(msg, type='success') {
            this.toastMsg = msg; this.toastType = type; this.showToast = true;
            setTimeout(() => this.showToast = false, 3500);
        },
    };
}
</script>

</x-app-layout>