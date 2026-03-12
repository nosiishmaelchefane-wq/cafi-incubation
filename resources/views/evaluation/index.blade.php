<x-app-layout>

  <div class="eval-page p-4" x-data="evalApp()">

    {{-- ═══════════════════════════════════════
         PAGE HEADER
    ═══════════════════════════════════════ --}}
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Incubation</a></li>
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Applications</a></li>
                    <li class="breadcrumb-item active">Evaluation &amp; Scoring</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-clipboard2-data-fill text-primary me-2"></i>Evaluation &amp; Scoring
            </h4>
            <p class="text-muted small mb-0">Score eligible applications · Compute rankings · LEHSFF Cohort 3</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1"
                    @click="exportRanking()">
                <i class="bi bi-download"></i> Export Rankings
            </button>
            <button class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1"
                    @click="showAssignModal = true"
                    x-show="isAdmin">
                <i class="bi bi-person-plus"></i> Assign Evaluators
            </button>
            <button class="btn btn-sm d-flex align-items-center gap-1"
                    :class="windowOpen ? 'btn-warning' : 'btn-success'"
                    @click="toggleWindow()"
                    x-show="isAdmin">
                <i :class="windowOpen ? 'bi bi-lock' : 'bi bi-unlock'"></i>
                <span x-text="windowOpen ? 'Lock Window' : 'Open Window'"></span>
            </button>
        </div>
    </div>

    {{-- ═══════════════════════════════════════
         EVALUATION WINDOW BANNER
    ═══════════════════════════════════════ --}}
    <div class="alert d-flex align-items-center gap-3 mb-4 shadow-sm"
         :class="windowOpen ? 'alert-success' : 'alert-danger'">
        <i class="bi fs-4" :class="windowOpen ? 'bi-unlock-fill' : 'bi-lock-fill'"></i>
        <div class="flex-grow-1">
            <div class="fw-semibold" x-text="windowOpen ? 'Evaluation Window is OPEN' : 'Evaluation Window is CLOSED'"></div>
            <small x-text="windowOpen ? 'Evaluators can submit and edit scores until 31 March 2025.' : 'Scoring is locked. No further edits are permitted.'"></small>
        </div>
        <span class="badge" :class="windowOpen ? 'bg-success' : 'bg-danger'"
              x-text="windowOpen ? 'Active' : 'Locked'"></span>
    </div>

    {{-- ═══════════════════════════════════════
         KPI STRIP
    ═══════════════════════════════════════ --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-sm-4 col-md-2">
            <div class="kpi-mini card border-0 shadow-sm" @click="setTab('all')"
                 :class="activeTab==='all' ? 'kpi-active' : ''">
                <div class="card-body p-3 text-center">
                    <div class="fw-bold fs-4 lh-1 text-dark" x-text="counts.total"></div>
                    <small class="text-muted">Eligible Apps</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-4 col-md-2">
            <div class="kpi-mini card border-0 shadow-sm" @click="setTab('pending')"
                 :class="activeTab==='pending' ? 'kpi-active' : ''">
                <div class="card-body p-3 text-center">
                    <div class="fw-bold fs-4 lh-1 text-warning" x-text="counts.pending"></div>
                    <small class="text-muted">Not Scored</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-4 col-md-2">
            <div class="kpi-mini card border-0 shadow-sm" @click="setTab('partial')"
                 :class="activeTab==='partial' ? 'kpi-active' : ''">
                <div class="card-body p-3 text-center">
                    <div class="fw-bold fs-4 lh-1 text-info" x-text="counts.partial"></div>
                    <small class="text-muted">Partial</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-4 col-md-2">
            <div class="kpi-mini card border-0 shadow-sm" @click="setTab('scored')"
                 :class="activeTab==='scored' ? 'kpi-active' : ''">
                <div class="card-body p-3 text-center">
                    <div class="fw-bold fs-4 lh-1 text-success" x-text="counts.scored"></div>
                    <small class="text-muted">Fully Scored</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-4 col-md-2">
            <div class="kpi-mini card border-0 shadow-sm">
                <div class="card-body p-3 text-center">
                    <div class="fw-bold fs-4 lh-1 text-primary" x-text="counts.evaluators"></div>
                    <small class="text-muted">Evaluators</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-4 col-md-2">
            <div class="kpi-mini card border-0 shadow-sm">
                <div class="card-body p-3 text-center">
                    <div class="fw-bold fs-4 lh-1 text-secondary" x-text="counts.avgScore + '%'"></div>
                    <small class="text-muted">Avg Score</small>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════
         VIEW TOGGLE: TABLE / RANKED LIST
    ═══════════════════════════════════════ --}}
    <div class="d-flex align-items-center gap-3 mb-3">
        <div class="btn-group btn-group-sm" role="group">
            <button class="btn btn-outline-secondary" :class="view==='table' ? 'active' : ''"
                    @click="view='table'">
                <i class="bi bi-table me-1"></i>Scoring Queue
            </button>
            <button class="btn btn-outline-secondary" :class="view==='ranking' ? 'active' : ''"
                    @click="view='ranking'">
                <i class="bi bi-trophy me-1"></i>Ranked List
            </button>
        </div>
        <div class="ms-auto d-flex gap-2 align-items-center">
            <label class="small text-muted mb-0">Search:</label>
            <div class="input-group input-group-sm" style="width:220px;">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" class="form-control border-start-0"
                       placeholder="Enterprise, ID…"
                       x-model="search" @input="applyFilters()">
            </div>
            <select class="form-select form-select-sm" style="width:auto;"
                    x-model="filterSector" @change="applyFilters()">
                <option value="">All Sectors</option>
                <option>Agriculture</option>
                <option>Technology</option>
                <option>Textile</option>
                <option>Manufacturing</option>
                <option>Food & Beverage</option>
            </select>
        </div>
    </div>

    {{-- ═══════════════════════════════════════
         SCORING QUEUE TABLE VIEW
    ═══════════════════════════════════════ --}}
    <div class="card border-0 shadow-sm" x-show="view === 'table'">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <h6 class="fw-bold mb-0">Scoring Queue</h6>
            <small class="text-muted" x-text="filtered.length + ' applications'"></small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3" style="width:220px;">Application</th>
                            <th class="py-3">Sector</th>
                            <th class="py-3 text-center">Evaluators Assigned</th>
                            <th class="py-3 text-center" style="width:120px;">Score Progress</th>
                            <th class="py-3 text-center">Final Score</th>
                            <th class="py-3 text-center">My Score</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="app in filtered" :key="app.id">
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="app-avatar" :class="sectorColor(app.sector)">
                                            <span x-text="initials(app.enterprise)"></span>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark" x-text="app.enterprise"></div>
                                            <div class="text-muted" style="font-size:0.72rem;"
                                                 x-text="app.id + ' · ' + app.district"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border" x-text="app.sector"></span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <template x-for="ev in app.evaluators">
                                            <div class="ev-avatar" :title="ev.name"
                                                 :class="ev.scored ? 'bg-success text-white' : 'bg-light text-muted border'"
                                                 x-text="ev.initials"></div>
                                        </template>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height:6px; border-radius:4px;">
                                            <div class="progress-bar bg-primary"
                                                 :style="'width:' + scoreProgress(app) + '%'"></div>
                                        </div>
                                        <span class="text-muted small flex-shrink-0"
                                              x-text="app.evaluators.filter(e=>e.scored).length + '/' + app.evaluators.length"></span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <template x-if="app.finalScore !== null">
                                        <div>
                                            <div class="fw-bold fs-6 lh-1"
                                                 :class="scoreColor(app.finalScore)"
                                                 x-text="app.finalScore + '%'"></div>
                                            <div class="text-muted" style="font-size:0.68rem;">avg of
                                                <span x-text="app.evaluators.filter(e=>e.scored).length"></span>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="app.finalScore === null">
                                        <span class="text-muted small">—</span>
                                    </template>
                                </td>
                                <td class="text-center">
                                    <template x-if="app.myScore !== null">
                                        <span class="badge bg-primary bg-opacity-15 text-primary fw-bold"
                                              x-text="app.myScore + '%'"></span>
                                    </template>
                                    <template x-if="app.myScore === null">
                                        <span class="badge bg-warning bg-opacity-15 text-warning">Pending</span>
                                    </template>
                                </td>
                                <td>
                                    <span class="badge rounded-pill" :class="evalStatusBadge(app.evalStatus)"
                                          x-text="app.evalStatus"></span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-primary py-1 px-3"
                                            @click="openScoring(app)"
                                            :disabled="!windowOpen && app.myScore === null">
                                        <i class="bi me-1" :class="app.myScore !== null ? 'bi-pencil' : 'bi-star-fill'"></i>
                                        <span x-text="app.myScore !== null ? 'Edit' : 'Score'"></span>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="filtered.length === 0">
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                                No applications found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════
         RANKED LIST VIEW
    ═══════════════════════════════════════ --}}
    <div class="card border-0 shadow-sm" x-show="view === 'ranking'">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <h6 class="fw-bold mb-0"><i class="bi bi-trophy-fill text-warning me-2"></i>Ranked Applications</h6>
                <small class="text-muted">Sorted by final average score · Only fully scored applications are ranked</small>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-success"
                        @click="confirmTop20()"
                        x-show="isAdmin">
                    <i class="bi bi-check2-all me-1"></i>Confirm Top 20
                </button>
                <button class="btn btn-sm btn-outline-secondary" @click="exportRanking()">
                    <i class="bi bi-download me-1"></i>Export
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3" style="width:60px;">Rank</th>
                            <th class="py-3">Application</th>
                            <th class="py-3">Sector / District</th>
                            <template x-for="ev in allEvaluators">
                                <th class="py-3 text-center" x-text="ev.initials" :title="ev.name"></th>
                            </template>
                            <th class="py-3 text-center">Final Score</th>
                            <th class="py-3 text-center">PDO</th>
                            <th class="py-3 text-center">Shortlist</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(app, idx) in ranked" :key="app.id">
                            <tr :class="idx < 20 ? 'top20-row' : ''">
                                <td class="px-4 py-3">
                                    <div class="rank-badge"
                                         :class="idx===0 ? 'rank-gold' : idx===1 ? 'rank-silver' : idx===2 ? 'rank-bronze' : 'rank-default'"
                                         x-text="idx + 1"></div>
                                </td>
                                <td class="py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="app-avatar" :class="sectorColor(app.sector)">
                                            <span x-text="initials(app.enterprise)"></span>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark" x-text="app.enterprise"></div>
                                            <div class="text-muted" style="font-size:0.72rem;" x-text="app.id"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div x-text="app.sector"></div>
                                    <div class="text-muted" style="font-size:0.72rem;" x-text="app.district"></div>
                                </td>
                                <template x-for="ev in allEvaluators">
                                    <td class="text-center">
                                        <template x-if="getScore(app, ev.id) !== null">
                                            <span class="fw-medium" x-text="getScore(app, ev.id) + '%'"></span>
                                        </template>
                                        <template x-if="getScore(app, ev.id) === null">
                                            <span class="text-muted">—</span>
                                        </template>
                                    </td>
                                </template>
                                <td class="text-center">
                                    <span class="fw-bold fs-6" :class="scoreColor(app.finalScore)"
                                          x-text="app.finalScore !== null ? app.finalScore + '%' : '—'"></span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <span class="badge bg-pink-soft text-pink" x-show="app.isWoman" title="Women-owned">W</span>
                                        <span class="badge bg-success bg-opacity-15 text-success" x-show="app.isYouth" title="Youth-owned">Y</span>
                                        <span class="badge bg-warning bg-opacity-15 text-warning" x-show="app.isRural" title="Rural-based">R</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="form-check d-flex justify-content-center mb-0" x-show="isAdmin">
                                        <input class="form-check-input" type="checkbox"
                                               x-model="app.shortlisted"
                                               :id="'sl-'+app.id">
                                    </div>
                                    <span x-show="!isAdmin && app.shortlisted" class="badge bg-success">✓</span>
                                    <span x-show="!isAdmin && !app.shortlisted" class="text-muted">—</span>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="ranked.length < applications.length">
                            <td colspan="10" class="px-4 py-3 text-muted small bg-light">
                                <i class="bi bi-info-circle me-2"></i>
                                <span x-text="applications.length - ranked.length"></span>
                                application(s) are not yet fully scored and excluded from ranking.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    {{-- ═══════════════════════════════════════
         SCORING PANEL (right drawer)
    ═══════════════════════════════════════ --}}
    <div class="panel-backdrop" x-show="showScoring" x-transition.opacity></div>
    <div class="score-panel" x-show="showScoring" x-transition.opacity>
        <div class="score-panel-inner" x-if="activeApp">

            {{-- Header --}}
            <div class="d-flex align-items-start justify-content-between p-4 border-bottom">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge rounded-pill" :class="evalStatusBadge(activeApp?.evalStatus)"
                              x-text="activeApp?.evalStatus"></span>
                        <span class="text-muted small" x-text="activeApp?.id"></span>
                    </div>
                    <h5 class="fw-bold mb-0" x-text="activeApp?.enterprise"></h5>
                    <small class="text-muted"
                           x-text="activeApp?.sector + ' · ' + activeApp?.district + ' · ' + activeApp?.stage"></small>
                </div>
                <button class="btn btn-sm btn-light rounded-circle p-2 lh-1" @click="showScoring=false">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            {{-- Score Summary Bar --}}
            <div class="px-4 py-3 bg-light border-bottom">
                <div class="row g-2 text-center small">
                    <div class="col-4">
                        <div class="text-muted">My Score</div>
                        <div class="fw-bold fs-5" :class="scoreColor(myTotalScore)"
                             x-text="myTotalScore !== null ? myTotalScore + '%' : '—'"></div>
                    </div>
                    <div class="col-4">
                        <div class="text-muted">Final Avg</div>
                        <div class="fw-bold fs-5" :class="scoreColor(activeApp?.finalScore)"
                             x-text="activeApp?.finalScore !== null ? activeApp?.finalScore + '%' : '—'"></div>
                    </div>
                    <div class="col-4">
                        <div class="text-muted">Evaluators</div>
                        <div class="fw-bold fs-5"
                             x-text="(activeApp?.evaluators?.filter(e=>e.scored).length || 0) + '/' + (activeApp?.evaluators?.length || 0)"></div>
                    </div>
                </div>
            </div>

            {{-- Scoring Tabs --}}
            <div class="px-4 pt-3 border-bottom">
                <ul class="nav nav-tabs border-0 gap-1">
                    <template x-for="t in ['Score','Application','Evaluators']">
                        <li class="nav-item">
                            <button class="nav-link px-3 py-2 small fw-medium"
                                    :class="scoreTab===t ? 'active text-primary border-bottom border-primary border-2' : 'text-muted border-0'"
                                    @click="scoreTab=t" x-text="t"></button>
                        </li>
                    </template>
                </ul>
            </div>

            <div class="p-4 overflow-auto flex-grow-1">

                {{-- SCORE TAB --}}
                <div x-show="scoreTab === 'Score'">

                    <template x-if="!windowOpen && activeApp?.myScore !== null">
                        <div class="alert alert-warning small d-flex gap-2 align-items-start mb-3">
                            <i class="bi bi-lock-fill mt-1 flex-shrink-0"></i>
                            <span>The evaluation window is closed. Your submitted score is read-only.</span>
                        </div>
                    </template>

                    <div class="d-flex flex-column gap-3">
                        <template x-for="(criterion, idx) in scoringCriteria" :key="idx">
                            <div class="scoring-criterion p-3 rounded-3 border"
                                 :class="criterion.score > 0 ? 'border-primary bg-primary bg-opacity-5' : 'bg-light'">
                                <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold small" x-text="criterion.label"></div>
                                        <div class="text-muted" style="font-size:0.72rem;"
                                             x-text="criterion.hint"></div>
                                    </div>
                                    <div class="text-end flex-shrink-0">
                                        <span class="badge bg-secondary bg-opacity-15 text-secondary small"
                                              x-text="'Max: ' + criterion.max"></span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <input type="range"
                                           class="form-range flex-grow-1"
                                           :min="0" :max="criterion.max" step="1"
                                           x-model.number="criterion.score"
                                           :disabled="!windowOpen"
                                           @input="recalcMyScore()">
                                    <div class="score-input-box text-center"
                                         :class="criterion.score >= criterion.max*0.7 ? 'bg-success text-white' :
                                                 criterion.score >= criterion.max*0.4 ? 'bg-warning text-dark' :
                                                 'bg-light text-muted'">
                                        <span x-text="criterion.score"></span>
                                        <span class="opacity-50" x-text="'/' + criterion.max"></span>
                                    </div>
                                </div>
                                {{-- Quick score buttons --}}
                                <div class="d-flex gap-1 mt-2 flex-wrap" x-show="windowOpen">
                                    <template x-for="pct in [0, 25, 50, 75, 100]">
                                        <button class="btn btn-xs py-0 px-2 border small"
                                                :class="criterion.score === Math.round(criterion.max*(pct/100)) ? 'btn-primary' : 'btn-light'"
                                                @click="criterion.score = Math.round(criterion.max*(pct/100)); recalcMyScore()"
                                                x-text="pct + '%'"></button>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="mt-4 p-3 rounded-3 border-2 border-primary bg-primary bg-opacity-5">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-semibold small">Total Score</span>
                            <span class="fw-bold fs-5" :class="scoreColor(myTotalScore)"
                                  x-text="myTotalScore !== null ? myTotalScore + '%' : '0%'"></span>
                        </div>
                        <div class="progress mb-2" style="height:8px; border-radius:6px;">
                            <div class="progress-bar" :class="scoreColor(myTotalScore).replace('text-','bg-')"
                                 :style="'width:' + (myTotalScore||0) + '%'"></div>
                        </div>
                        <div class="d-flex justify-content-between small text-muted">
                            <span x-text="currentCriterionTotal + ' / ' + maxTotal + ' points'"></span>
                            <span :class="scoreColor(myTotalScore)"
                                  x-text="myTotalScore >= 70 ? 'Strong' : myTotalScore >= 50 ? 'Average' : 'Weak'"></span>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label fw-medium small">Evaluator Comments</label>
                        <textarea class="form-control small" rows="3"
                                  placeholder="Provide qualitative comments about this application's strengths and weaknesses…"
                                  x-model="evalComments"
                                  :disabled="!windowOpen"></textarea>
                    </div>
                </div>

                {{-- APPLICATION TAB --}}
                <div x-show="scoreTab === 'Application'">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="info-block">
                                <div class="info-label">Stage</div>
                                <div class="info-val" x-text="activeApp?.stage"></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-block">
                                <div class="info-label">Years Operating</div>
                                <div class="info-val" x-text="(activeApp?.yearsOp || '—') + ' years'"></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-block">
                                <div class="info-label">Employees</div>
                                <div class="info-val" x-text="activeApp?.employees || '—'"></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-block">
                                <div class="info-label">Revenue (Est.)</div>
                                <div class="info-val" x-text="'M ' + (activeApp?.revenue || '—')"></div>
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
                        <div class="col-12">
                            <div class="small fw-semibold text-muted mb-2">PDO Flags</div>
                            <div class="d-flex gap-2 flex-wrap">
                                <span class="badge border px-3 py-2"
                                      :class="activeApp?.isWoman ? 'bg-pink text-white' : 'bg-light text-muted'">
                                    <i class="bi bi-gender-female me-1"></i>Women-owned
                                </span>
                                <span class="badge border px-3 py-2"
                                      :class="activeApp?.isYouth ? 'bg-success bg-opacity-20 text-success' : 'bg-light text-muted'">
                                    <i class="bi bi-person-fill me-1"></i>Youth-owned
                                </span>
                                <span class="badge border px-3 py-2"
                                      :class="activeApp?.isRural ? 'bg-warning bg-opacity-20 text-warning' : 'bg-light text-muted'">
                                    <i class="bi bi-tree me-1"></i>Rural-based
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- EVALUATORS TAB --}}
                <div x-show="scoreTab === 'Evaluators'">
                    <div class="d-flex flex-column gap-3">
                        <template x-for="ev in activeApp?.evaluators || []">
                            <div class="d-flex align-items-center justify-content-between p-3 rounded-3 border"
                                 :class="ev.scored ? 'border-success bg-success bg-opacity-5' : 'bg-light'">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="ev-lg-avatar"
                                         :class="ev.scored ? 'bg-success text-white' : 'bg-secondary bg-opacity-20 text-muted'"
                                         x-text="ev.initials"></div>
                                    <div>
                                        <div class="fw-medium small" x-text="ev.name"></div>
                                        <div class="text-muted" style="font-size:0.72rem;" x-text="ev.role"></div>
                                        <div class="text-muted" style="font-size:0.72rem;" x-show="ev.scoredAt"
                                             x-text="'Scored: ' + ev.scoredAt"></div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <template x-if="ev.scored">
                                        <div>
                                            <div class="fw-bold fs-5" :class="scoreColor(ev.score)"
                                                 x-text="ev.score + '%'"></div>
                                            <div class="text-muted small">score</div>
                                        </div>
                                    </template>
                                    <template x-if="!ev.scored">
                                        <span class="badge bg-warning bg-opacity-15 text-warning">Pending</span>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="mt-4 p-3 rounded-3 bg-light border" x-show="activeApp?.finalScore !== null">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-semibold small">Computed Average Score</span>
                            <span class="fw-bold fs-4" :class="scoreColor(activeApp?.finalScore)"
                                  x-text="activeApp?.finalScore + '%'"></span>
                        </div>
                        <small class="text-muted">Average of all evaluator scores using equal weighting</small>
                    </div>
                </div>

            </div>{{-- end tab content --}}

            {{-- Panel Footer --}}
            <div class="p-4 border-top bg-light d-flex flex-wrap gap-2 justify-content-between align-items-center">
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary"
                            @click="navigateApp(-1)" :disabled="appIndex <= 0">
                        <i class="bi bi-chevron-left me-1"></i>Prev
                    </button>
                    <button class="btn btn-sm btn-outline-secondary"
                            @click="navigateApp(1)" :disabled="appIndex >= filtered.length-1">
                        Next<i class="bi bi-chevron-right ms-1"></i>
                    </button>
                    <small class="text-muted align-self-center"
                           x-text="(appIndex+1) + ' of ' + filtered.length"></small>
                </div>
                <div class="d-flex gap-2" x-show="windowOpen">
                    <button class="btn btn-sm btn-outline-secondary px-3" @click="saveDraft()">
                        <i class="bi bi-floppy me-1"></i>Save Draft
                    </button>
                    <button class="btn btn-sm btn-primary px-4" @click="submitScore()">
                        <i class="bi bi-send me-1"></i>Submit Score
                    </button>
                </div>
                <div x-show="!windowOpen">
                    <span class="badge bg-secondary py-2 px-3">
                        <i class="bi bi-lock me-1"></i>Window Closed
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════
         ASSIGN EVALUATORS MODAL
    ═══════════════════════════════════════ --}}
    {{-- <div class="modal fade show d-block" tabindex="-1"
         x-show="showAssignModal" x-transition.opacity
         style="background:rgba(0,0,0,0.45);">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-person-plus-fill text-primary me-2"></i>Assign Evaluators
                    </h5>
                    <button class="btn-close" @click="showAssignModal=false"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <p class="small text-muted mb-3">
                        Assign evaluators to this call. Each evaluator will see only eligible applications assigned to them.
                        A minimum of 2 evaluators is recommended.
                    </p>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle small mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-3 py-2" style="width:40px;"></th>
                                    <th class="py-2">Evaluator</th>
                                    <th class="py-2">Role / Department</th>
                                    <th class="py-2 text-center">Assigned Apps</th>
                                    <th class="py-2 text-center">Scored</th>
                                    <th class="py-2 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="ev in allEvaluators">
                                    <tr>
                                        <td class="px-3">
                                            <input class="form-check-input" type="checkbox"
                                                   x-model="ev.assigned">
                                        </td>
                                        <td class="py-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="ev-lg-avatar bg-primary text-white"
                                                     x-text="ev.initials"></div>
                                                <div>
                                                    <div class="fw-medium" x-text="ev.name"></div>
                                                    <div class="text-muted" style="font-size:0.72rem;" x-text="ev.email"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td x-text="ev.role"></td>
                                        <td class="text-center" x-text="ev.assignedApps"></td>
                                        <td class="text-center" x-text="ev.scoredApps"></td>
                                        <td class="text-center">
                                            <span class="badge"
                                                  :class="ev.active ? 'bg-success bg-opacity-15 text-success' : 'bg-secondary bg-opacity-15 text-muted'"
                                                  x-text="ev.active ? 'Active' : 'Inactive'"></span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <label class="form-label small fw-medium">Evaluation Window Deadline</label>
                        <input type="date" class="form-control form-control-sm" style="width:220px;"
                               value="2025-03-31">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button class="btn btn-light px-4" @click="showAssignModal=false">Cancel</button>
                    <button class="btn btn-primary px-4" @click="saveAssignments()">
                        <i class="bi bi-save me-1"></i>Save Assignments
                    </button>
                </div>
            </div>
        </div>
    </div> --}}


</div>

{{-- ═══════════════════════════════════════ STYLES ═══════════════════════════════════════ --}}
<style>
.eval-page .card { border-radius: 14px !important; }
.eval-page .card-header { border-radius: 14px 14px 0 0 !important; }

/* KPI mini */
.eval-page .kpi-mini {
    border-radius: 12px !important; cursor: pointer;
    transition: all 0.2s; border: 2px solid transparent !important;
}
.eval-page .kpi-mini:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.10) !important; }
.eval-page .kpi-mini.kpi-active { border-color: #0d6efd !important; box-shadow: 0 0 0 3px rgba(13,110,253,0.15) !important; }

/* App avatars */
.eval-page .app-avatar {
    width: 36px; height: 36px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.75rem; font-weight: 700; flex-shrink: 0;
}
.av-blue   { background:#dbeafe; color:#1d4ed8; }
.av-green  { background:#dcfce7; color:#15803d; }
.av-orange { background:#ffedd5; color:#c2410c; }
.av-purple { background:#ede9fe; color:#7c3aed; }
.av-pink   { background:#fce7f3; color:#be185d; }
.av-teal   { background:#ccfbf1; color:#0f766e; }

/* Evaluator avatars */
.eval-page .ev-avatar {
    width: 28px; height: 28px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.65rem; font-weight: 700; flex-shrink: 0;
}
.eval-page .ev-lg-avatar {
    width: 38px; height: 38px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.78rem; font-weight: 700; flex-shrink: 0;
}

/* Score input box */
.score-input-box {
    width: 64px; height: 36px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.82rem; font-weight: 700; flex-shrink: 0;
}

/* Rank badges */
.rank-badge {
    width: 32px; height: 32px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.78rem; font-weight: 800;
}
.rank-gold   { background: #fef08a; color: #a16207; border: 2px solid #eab308; }
.rank-silver { background: #e5e7eb; color: #374151; border: 2px solid #9ca3af; }
.rank-bronze { background: #fed7aa; color: #9a3412; border: 2px solid #f97316; }
.rank-default{ background: #f3f4f6; color: #6b7280; border: 2px solid #e5e7eb; }

/* Top 20 highlight */
.top20-row { background-color: #f0fdf4 !important; }

/* Scoring criterion */
.scoring-criterion { transition: all 0.2s; }

/* Panel */
.panel-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 1040; }
.score-panel {
    position: fixed; top: 0; right: 0; bottom: 0;
    width: 100%; max-width: 660px; z-index: 1050;
    background: #fff; box-shadow: -4px 0 32px rgba(0,0,0,0.15);
    display: flex; flex-direction: column;
}
.score-panel-inner { display: flex; flex-direction: column; height: 100%; }
@media (max-width: 576px) { .score-panel { max-width: 100%; } }

/* Info blocks */
.info-block { background: #f8f9fa; border-radius: 10px; padding: 12px 14px; height: 100%; }
.info-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.06em; color: #6c757d; font-weight: 600; margin-bottom: 4px; }
.info-val { font-size: 0.875rem; font-weight: 600; color: #212529; }

/* Tabs */
.score-panel .nav-tabs { border-bottom: none; }
.score-panel .nav-link { border: none !important; border-bottom: 2px solid transparent !important; border-radius: 0 !important; }
.score-panel .nav-link.active { border-bottom-color: #0d6efd !important; }

/* PDO badges */
.bg-pink { background-color: #ec4899; }
.bg-pink-soft { background-color: #fce7f3; }
.text-pink { color: #be185d; }

/* Btn xs */
.btn-xs { font-size: 0.72rem; }
</style>

{{-- ═══════════════════════════════════════ ALPINE JS ═══════════════════════════════════════ --}}
<script>
function evalApp() {
    return {
        view: 'table',
        search: '', filterSector: '', activeTab: 'all',
        isAdmin: true,
        windowOpen: true,
        showScoring: false, showAssignModal: false, showTop20Modal: false,
        showToast: false, toastMsg: '', toastType: 'success',
        activeApp: null, appIndex: 0,
        scoreTab: 'Score',
        evalComments: '',
        myTotalScore: null,
        currentCriterionTotal: 0,
        maxTotal: 0,

        scoringCriteria: [
            { label: 'Business Viability & Model Clarity',     hint: 'Clear value proposition, revenue model, and market understanding',   max: 20, score: 0 },
            { label: 'Market Opportunity & Demand',            hint: 'Evidence of real demand and defined target customers',                max: 15, score: 0 },
            { label: 'Team Capability & Commitment',           hint: 'Skills, experience, and dedication of the founding team',             max: 15, score: 0 },
            { label: 'Innovation & Differentiation',           hint: 'Uniqueness of product/service and competitive advantage',            max: 15, score: 0 },
            { label: 'Financial Management Readiness',         hint: 'Basic financial literacy, record keeping, and funding awareness',    max: 10, score: 0 },
            { label: 'Scalability & Growth Potential',         hint: 'Potential to grow beyond current operations',                        max: 10, score: 0 },
            { label: 'Social Impact & PDO Alignment',          hint: 'Women/youth ownership, job creation, rural reach',                   max: 10, score: 0 },
            { label: 'Engagement & Communication Clarity',     hint: 'Clarity of written application and interview/pitch performance',     max: 5,  score: 0 },
        ],

        allEvaluators: [
            { id:1, name:'Dr. Lerato Mohapi',    initials:'LM', role:'Business Development Officer',  email:'l.mohapi@lehsff.ls',  assigned:true,  active:true, assignedApps:8, scoredApps:6 },
            { id:2, name:'Mr. Tšepiso Ntšekhe', initials:'TN', role:'CAFI Evaluation Officer',        email:'t.ntsekhe@cafi.ls',   assigned:true,  active:true, assignedApps:8, scoredApps:8 },
            { id:3, name:'Ms. Palesa Leseli',   initials:'PL', role:'Entrepreneurship Specialist',    email:'p.leseli@lehsff.ls',  assigned:true,  active:true, assignedApps:8, scoredApps:5 },
            { id:4, name:'Mr. Retšelisitsoe M', initials:'RM', role:'Finance &amp; Investment Analyst', email:'r.m@cafi.ls',       assigned:false, active:true, assignedApps:0, scoredApps:0 },
        ],

        applications: [
            { id:'APP-001', enterprise:'MoroAgri Basotho',      owner:'Limpho Mokoena',       sector:'Agriculture',    district:'Maseru',  stage:'Registered Startup', yearsOp:2, employees:4, revenue:'120,000', isWoman:true,  isYouth:true,  isRural:false, bizDesc:'Hydroponic vegetable farming using recycled water systems targeting Maseru hotels and supermarkets.', whyLehsff:'Need business skills and access to markets.', evalStatus:'Scored', myScore:78, finalScore:76, shortlisted:true,
                evaluators:[{id:1,name:'Dr. Lerato Mohapi',   initials:'LM',scored:true, score:74,scoredAt:'05 Mar 2025'},{id:2,name:'Mr. Tšepiso Ntšekhe',initials:'TN',scored:true,score:80,scoredAt:'06 Mar 2025'},{id:3,name:'Ms. Palesa Leseli',initials:'PL',scored:true,score:74,scoredAt:'07 Mar 2025'}],
                scoresByEv:{1:74,2:80,3:74} },
            { id:'APP-002', enterprise:'Lesotho Stitch Co.',    owner:'Thabo Ramokhele',      sector:'Textile',        district:'Leribe',  stage:'Formally Registered', yearsOp:3, employees:8, revenue:'250,000', isWoman:false, isYouth:false, isRural:false, bizDesc:'Garment manufacturing for export to South Africa and regional markets.', whyLehsff:'Need access to finance and export certifications.', evalStatus:'Scored', myScore:65, finalScore:67, shortlisted:false,
                evaluators:[{id:1,name:'Dr. Lerato Mohapi',   initials:'LM',scored:true, score:65,scoredAt:'05 Mar 2025'},{id:2,name:'Mr. Tšepiso Ntšekhe',initials:'TN',scored:true,score:70,scoredAt:'06 Mar 2025'},{id:3,name:'Ms. Palesa Leseli',initials:'PL',scored:true,score:66,scoredAt:'07 Mar 2025'}],
                scoresByEv:{1:65,2:70,3:66} },
            { id:'APP-005', enterprise:'Naledi Weave Studio',   owner:'Naledi Sello',         sector:'Textile',        district:'Mafeteng',stage:'Formally Registered', yearsOp:2, employees:5, revenue:'95,000',  isWoman:true,  isYouth:false, isRural:true,  bizDesc:'Handwoven Basotho blanket and tapestry products for domestic and diaspora markets.', whyLehsff:'Market development and e-commerce skills.', evalStatus:'Partial', myScore:null, finalScore:null, shortlisted:false,
                evaluators:[{id:1,name:'Dr. Lerato Mohapi',   initials:'LM',scored:true, score:82,scoredAt:'05 Mar 2025'},{id:2,name:'Mr. Tšepiso Ntšekhe',initials:'TN',scored:false,score:null,scoredAt:null},{id:3,name:'Ms. Palesa Leseli',initials:'PL',scored:false,score:null,scoredAt:null}],
                scoresByEv:{1:82,2:null,3:null} },
            { id:'APP-007', enterprise:'Selemo Organics',       owner:'Mamello Tsita',        sector:'Agriculture',    district:"Mohale's Hoek",stage:'Formally Registered',yearsOp:3,employees:6,revenue:'145,000',isWoman:true,isYouth:false,isRural:true, bizDesc:'Organic herb and spice farming, processing and packaging for retail and export.', whyLehsff:'Export market access and certification.', evalStatus:'Not Scored', myScore:null, finalScore:null, shortlisted:false,
                evaluators:[{id:1,name:'Dr. Lerato Mohapi',   initials:'LM',scored:false,score:null,scoredAt:null},{id:2,name:'Mr. Tšepiso Ntšekhe',initials:'TN',scored:false,score:null,scoredAt:null},{id:3,name:'Ms. Palesa Leseli',initials:'PL',scored:false,score:null,scoredAt:null}],
                scoresByEv:{1:null,2:null,3:null} },
            { id:'APP-008', enterprise:'Lerato Crafts & Arts',  owner:'Lerato Nkosi',         sector:'Manufacturing',  district:'Berea',   stage:'Registered Startup',  yearsOp:2, employees:3, revenue:'62,000',  isWoman:true,  isYouth:true,  isRural:false, bizDesc:'Handcrafted leather goods and traditional Basotho jewellery for local and online markets.', whyLehsff:'E-commerce setup and export documentation.', evalStatus:'Scored', myScore:88, finalScore:85, shortlisted:true,
                evaluators:[{id:1,name:'Dr. Lerato Mohapi',   initials:'LM',scored:true, score:84,scoredAt:'05 Mar 2025'},{id:2,name:'Mr. Tšepiso Ntšekhe',initials:'TN',scored:true,score:88,scoredAt:'06 Mar 2025'},{id:3,name:'Ms. Palesa Leseli',initials:'PL',scored:true,score:83,scoredAt:'07 Mar 2025'}],
                scoresByEv:{1:84,2:88,3:83} },
        ],

        filtered: [],

        get counts() {
            const all = this.applications;
            const scored = all.filter(a=>a.evalStatus==='Scored');
            const avgScore = scored.length ? Math.round(scored.reduce((s,a)=>s+(a.finalScore||0),0)/scored.length) : 0;
            return {
                total: all.length,
                pending: all.filter(a=>a.evalStatus==='Not Scored').length,
                partial: all.filter(a=>a.evalStatus==='Partial').length,
                scored:  scored.length,
                evaluators: this.allEvaluators.filter(e=>e.assigned).length,
                avgScore,
            };
        },

        get ranked() {
            return [...this.applications]
                .filter(a => a.finalScore !== null)
                .sort((a,b) => b.finalScore - a.finalScore);
        },

        init() { this.applyFilters(); this.calcMaxTotal(); },

        calcMaxTotal() {
            this.maxTotal = this.scoringCriteria.reduce((s,c) => s+c.max, 0);
        },

        setTab(tab) {
            this.activeTab = tab;
            const map = { pending:'Not Scored', partial:'Partial', scored:'Scored' };
            this.filterStatus = map[tab] || '';
            this.applyFilters();
        },

        applyFilters() {
            const s = this.search.toLowerCase();
            this.filtered = this.applications.filter(a => {
                const ms = !s || a.enterprise.toLowerCase().includes(s) || a.id.toLowerCase().includes(s);
                const mse = !this.filterSector || a.sector === this.filterSector;
                const mst = !this.filterStatus || a.evalStatus === this.filterStatus;
                return ms && mse && mst;
            });
        },

        evalStatusBadge(s) {
            return {'Not Scored':'bg-warning text-dark','Partial':'bg-info text-white','Scored':'bg-success text-white'}[s]||'bg-secondary text-white';
        },

        scoreColor(v) {
            if (v === null || v === undefined) return 'text-muted';
            return v >= 70 ? 'text-success' : v >= 50 ? 'text-warning' : 'text-danger';
        },

        sectorColor(sector) {
            return {'Agriculture':'av-green','Technology':'av-blue','Textile':'av-pink','Manufacturing':'av-orange','Food & Beverage':'av-teal'}[sector]||'av-purple';
        },

        initials(name) { return name.split(' ').slice(0,2).map(w=>w[0].toUpperCase()).join(''); },

        scoreProgress(app) {
            if (!app.evaluators.length) return 0;
            return Math.round(app.evaluators.filter(e=>e.scored).length / app.evaluators.length * 100);
        },

        getScore(app, evId) { return app.scoresByEv[evId] ?? null; },

        openScoring(app) {
            this.activeApp = app;
            this.appIndex = this.filtered.indexOf(app);
            this.scoreTab = 'Score';
            this.evalComments = '';
            this.scoringCriteria.forEach(c => c.score = 0);
            this.recalcMyScore();
            this.showScoring = true;
        },

        recalcMyScore() {
            const total = this.scoringCriteria.reduce((s,c) => s+c.score, 0);
            const max   = this.maxTotal || 100;
            this.currentCriterionTotal = total;
            this.myTotalScore = Math.round(total / max * 100);
        },

        navigateApp(dir) {
            const idx = this.appIndex + dir;
            if (idx >= 0 && idx < this.filtered.length) {
                this.appIndex = idx;
                this.activeApp = this.filtered[idx];
                this.scoringCriteria.forEach(c => c.score = 0);
                this.recalcMyScore();
                this.scoreTab = 'Score';
            }
        },

        saveDraft() { this.toast('Draft score saved.'); },

        submitScore() {
            if (this.myTotalScore === 0) {
                this.toast('Please score at least one criterion before submitting.', 'error');
                return;
            }
            const app = this.activeApp;
            app.myScore = this.myTotalScore;
            const me = app.evaluators.find(e => e.id === 1);
            if (me) { me.scored = true; me.score = this.myTotalScore; me.scoredAt = new Date().toLocaleDateString('en-GB'); }
            app.scoresByEv[1] = this.myTotalScore;
            const scoredEvs = app.evaluators.filter(e => e.scored);
            app.finalScore = scoredEvs.length ? Math.round(scoredEvs.reduce((s,e)=>s+e.score,0)/scoredEvs.length) : null;
            app.evalStatus = scoredEvs.length === app.evaluators.length ? 'Scored' : 'Partial';
            this.toast(`Score submitted for "${app.enterprise}": ${this.myTotalScore}%`);
            this.applyFilters();
        },

        toggleWindow() {
            this.windowOpen = !this.windowOpen;
            this.toast(this.windowOpen ? 'Evaluation window opened.' : 'Evaluation window locked. No further edits permitted.');
        },

        confirmTop20() { this.showTop20Modal = true; },

        doTop20() {
            const r = this.ranked;
            r.forEach((a,i) => { a.shortlisted = i < 20; });
            this.showTop20Modal = false;
            this.toast('Top 20 confirmed. Pitch invitations will be triggered.');
        },

        saveAssignments() {
            this.showAssignModal = false;
            this.toast('Evaluator assignments saved.');
        },

        exportRanking() { this.toast('Export started – file will download shortly.'); },

        toast(msg, type='success') {
            this.toastMsg = msg; this.toastType = type; this.showToast = true;
            setTimeout(() => this.showToast = false, 3500);
        },
    };
}
</script>

</x-app-layout>