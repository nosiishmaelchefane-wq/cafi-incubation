<x-app-layout>
    {{-- resources/views/incubation/shortlisting/index.blade.php --}}
{{-- Route: /incubation/shortlisting/{call} --}}

<div class="sl-page p-4">

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
            <button class="btn sl-btn-ghost btn-sm">
                <i class="bi bi-download me-1"></i>Export
            </button>
            <button class="btn sl-btn-ghost btn-sm">
                <i class="bi bi-envelope me-1"></i>Send Pitch Invitations
            </button>
            <button class="btn sl-btn-primary btn-sm">
                <i class="bi bi-check2-all me-1"></i>Confirm Top 20
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         WORKFLOW PROGRESS STEPPER
    ══════════════════════════════════════ --}}
    <div class="sl-card mb-4 p-0">
        <div class="d-flex align-items-stretch">
            <div class="sl-stepper-step flex-grow-1 sl-step-done">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div class="sl-step-circle sc-done"><i class="bi bi-check2"></i></div>
                    <span class="fw-semibold small">Top 20 Shortlist</span>
                </div>
                <div class="sl-step-desc">Confirm ranked shortlist</div>
                <div class="sl-step-count text-primary">20 apps</div>
            </div>
            <div class="sl-stepper-step flex-grow-1 sl-step-done">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div class="sl-step-circle sc-active"><i class="bi bi-mic-fill"></i></div>
                    <span class="fw-semibold small">Pitch Event</span>
                </div>
                <div class="sl-step-desc">Schedule & score pitches</div>
                <div class="sl-step-count text-primary">20 apps</div>
            </div>
            <div class="sl-stepper-step flex-grow-1 sl-step-future">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div class="sl-step-circle sc-future"><i class="bi bi-shield-check"></i></div>
                    <span class="fw-semibold small">Due Diligence</span>
                </div>
                <div class="sl-step-desc">DD checks for Top 10</div>
                <div class="sl-step-count text-muted">11 apps</div>
            </div>
            <div class="sl-stepper-step flex-grow-1 sl-step-future">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div class="sl-step-circle sc-future"><i class="bi bi-people-fill"></i></div>
                    <span class="fw-semibold small">Final Cohort</span>
                </div>
                <div class="sl-step-desc">Confirm final 10 enterprises</div>
                <div class="sl-step-count text-muted">3 apps</div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         KPI STRIP
    ══════════════════════════════════════ --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3 col-xl">
            <div class="sl-kpi">
                <div class="sl-kpi-icon" style="background:rgba(59,130,246,.1);color:#3b82f6;"><i class="bi bi-list-check"></i></div>
                <div>
                    <div class="sl-kpi-val text-primary">22</div>
                    <div class="sl-kpi-label">Total Evaluated</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl">
            <div class="sl-kpi">
                <div class="sl-kpi-icon" style="background:rgba(245,158,11,.1);color:#f59e0b;"><i class="bi bi-star-fill"></i></div>
                <div>
                    <div class="sl-kpi-val text-warning">20</div>
                    <div class="sl-kpi-label">Top 20 Shortlisted</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl">
            <div class="sl-kpi">
                <div class="sl-kpi-icon" style="background:rgba(6,182,212,.1);color:#06b6d4;"><i class="bi bi-mic-fill"></i></div>
                <div>
                    <div class="sl-kpi-val" style="color:#06b6d4;">9</div>
                    <div class="sl-kpi-label">Pitched</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl">
            <div class="sl-kpi">
                <div class="sl-kpi-icon" style="background:rgba(139,92,246,.1);color:#8b5cf6;"><i class="bi bi-award-fill"></i></div>
                <div>
                    <div class="sl-kpi-val" style="color:#8b5cf6;">8</div>
                    <div class="sl-kpi-label">Top 10 Confirmed</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl">
            <div class="sl-kpi">
                <div class="sl-kpi-icon" style="background:rgba(16,185,129,.1);color:#10b981;"><i class="bi bi-shield-check"></i></div>
                <div>
                    <div class="sl-kpi-val text-success">3</div>
                    <div class="sl-kpi-label">DD Passed</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl">
            <div class="sl-kpi">
                <div class="sl-kpi-icon" style="background:rgba(239,68,68,.1);color:#ef4444;"><i class="bi bi-shield-x"></i></div>
                <div>
                    <div class="sl-kpi-val text-danger">1</div>
                    <div class="sl-kpi-label">DD Failed</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl">
            <div class="sl-kpi">
                <div class="sl-kpi-icon" style="background:rgba(16,185,129,.1);color:#10b981;"><i class="bi bi-check-circle-fill"></i></div>
                <div>
                    <div class="sl-kpi-val text-success">3</div>
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
                    <h6 class="fw-bold mb-0">Pitch Event</h6>
                    <span class="sl-count-badge">20 applications</span>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <div class="sl-search-wrap">
                        <i class="bi bi-search sl-search-icon"></i>
                        <input type="text" class="sl-search-input" placeholder="Search enterprise, ID…" value="Agri">
                    </div>
                    <select class="sl-select">
                        <option value="">All Sectors</option>
                        <option>Agriculture</option>
                        <option selected>Technology</option>
                        <option>Textile</option>
                        <option>Manufacturing</option>
                        <option>Food & Beverage</option>
                    </select>
                    <button class="btn sl-btn-ghost btn-sm">
                        <i class="bi bi-x-circle me-1"></i>Reset
                    </button>
                </div>
            </div>
            <div class="sl-tab-row">
                <button class="sl-tab sl-tab-active">All <span class="sl-tab-badge">22</span></button>
                <button class="sl-tab">Top 20 <span class="sl-tab-badge">20</span></button>
                <button class="sl-tab">Pitched <span class="sl-tab-badge">9</span></button>
                <button class="sl-tab">Top 10 <span class="sl-tab-badge">8</span></button>
                <button class="sl-tab">Prov. Top 10 <span class="sl-tab-badge">1</span></button>
                <button class="sl-tab">DD Pass <span class="sl-tab-badge">3</span></button>
                <button class="sl-tab">DD Fail <span class="sl-tab-badge">1</span></button>
                <button class="sl-tab">Final Accepted <span class="sl-tab-badge">3</span></button>
                <button class="sl-tab">Not Shortlisted <span class="sl-tab-badge">2</span></button>
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
                    {{-- ROW 1: Final Accepted - shows shield (DD) icon only --}}
                    <tr class="sl-row-accepted">
                        <td class="px-4 py-3"><div class="sl-rank-badge rank-gold">1</div></td>
                        <td class="py-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="sl-av av-orange"><span>LC</span></div>
                                <div><div class="fw-semibold small text-dark">Lerato Crafts & Arts</div><div class="sl-sub">APP-008 · Berea</div></div>
                            </div>
                        </td>
                        <td><span class="sl-sector-tag">Manufacturing</span></td>
                        <td class="text-center"><span class="fw-bold text-success">88%</span></td>
                        <td class="text-center"><span class="fw-bold text-success">85%</span></td>
                        <td class="text-center"><div class="sl-total-score text-success">87%</div></td>
                        <td><div class="d-flex gap-1"><span class="sl-pdo pdo-w" title="Women-owned">W</span><span class="sl-pdo pdo-y" title="Youth-owned">Y</span><span class="sl-pdo pdo-off" title="Rural">R</span></div></td>
                        <td><div><div class="small fw-medium">15 Mar 2025</div><div class="sl-sub">09:00 · LEHSFF Boardroom</div></div></td>
                        <td><div><span class="sl-dd-badge dd-pass">Pass</span><div class="sl-sub mt-1">20 Mar 2025</div></div></td>
                        <td><span class="sl-status-pill pill-accepted">Final Accepted</span></td>
                        <td class="text-center pe-4">
                            <div class="d-flex justify-content-center gap-1">
                                <button class="sl-action-btn" title="View Application"><i class="bi bi-eye"></i></button>
                                <button class="sl-action-btn" title="Score Pitch" disabled style="opacity:0.5"><i class="bi bi-mic-fill"></i></button>
                                <button class="sl-action-btn" title="Due Diligence"><i class="bi bi-clipboard-check"></i></button>
                                <div class="dropdown"><button class="sl-action-btn" data-bs-toggle="dropdown" title="More actions"><i class="bi bi-three-dots-vertical"></i></button></div>
                            </div>
                        </td>
                    </tr>

                    {{-- ROW 2: Pitched & Top 10 - shows both mic (pitch) and shield (DD) icons --}}
                    <tr>
                        <td class="px-4 py-3"><div class="sl-rank-badge rank-purple">4</div></td>
                        <td class="py-3"><div class="d-flex align-items-center gap-2"><div class="sl-av av-teal"><span>BB</span></div><div><div class="fw-semibold small text-dark">Basali Bakery Co.</div><div class="sl-sub">APP-011 · Maseru</div></div></div></td>
                        <td><span class="sl-sector-tag">Food & Beverage</span></td>
                        <td class="text-center"><span class="fw-bold text-success">80%</span></td>
                        <td class="text-center"><span class="fw-bold text-success">76%</span></td>
                        <td class="text-center"><div class="sl-total-score text-success">78%</div></td>
                        <td><div class="d-flex gap-1"><span class="sl-pdo pdo-w" title="Women-owned">W</span><span class="sl-pdo pdo-off" title="Youth-owned">Y</span><span class="sl-pdo pdo-off" title="Rural">R</span></div></td>
                        <td><div><div class="small fw-medium">16 Mar 2025</div><div class="sl-sub">09:00 · LEHSFF Boardroom</div></div></td>
                        <td><span class="text-muted small">—</span></td>
                        <td><span class="sl-status-pill pill-top10">Top 10</span></td>
                        <td class="text-center pe-4">
                            <div class="d-flex justify-content-center gap-1">
                                <button class="sl-action-btn" title="View Application"><i class="bi bi-eye"></i></button>
                                <button class="sl-action-btn" title="Score Pitch"><i class="bi bi-mic-fill"></i></button>
                                <button class="sl-action-btn" title="Due Diligence"><i class="bi bi-clipboard-check"></i></button>
                                <div class="dropdown"><button class="sl-action-btn" data-bs-toggle="dropdown" title="More actions"><i class="bi bi-three-dots-vertical"></i></button></div>
                            </div>
                        </td>
                    </tr>

                    {{-- ROW 3: Top 20 (not pitched) - shows mic icon only (no shield) --}}
                    <tr>
                        <td class="px-4 py-3"><div class="sl-rank-badge rank-blue">8</div></td>
                        <td class="py-3"><div class="d-flex align-items-center gap-2"><div class="sl-av av-orange"><span>SC</span></div><div><div class="fw-semibold small text-dark">Selemo Crafts & Tourism</div><div class="sl-sub">APP-015 · Mokhotlong</div></div></div></td>
                        <td><span class="sl-sector-tag">Manufacturing</span></td>
                        <td class="text-center"><span class="fw-bold text-warning">75%</span></td>
                        <td class="text-center"><button class="btn sl-score-btn btn-sm"><i class="bi bi-pencil me-1"></i>Score</button></td>
                        <td class="text-center"><div class="sl-total-score text-warning">75%</div></td>
                        <td><div class="d-flex gap-1"><span class="sl-pdo pdo-w" title="Women-owned">W</span><span class="sl-pdo pdo-off" title="Youth-owned">Y</span><span class="sl-pdo pdo-r" title="Rural">R</span></div></td>
                        <td><button class="btn sl-ghost-xs"><i class="bi bi-calendar-plus me-1"></i>Schedule</button></td>
                        <td><span class="text-muted small">—</span></td>
                        <td><span class="sl-status-pill pill-top20">Top 20</span></td>
                        <td class="text-center pe-4">
                            <div class="d-flex justify-content-center gap-1">
                                <button class="sl-action-btn" title="View Application"><i class="bi bi-eye"></i></button>
                                <button class="sl-action-btn" title="Score Pitch"><i class="bi bi-mic-fill"></i></button>
                                <div class="dropdown"><button class="sl-action-btn" data-bs-toggle="dropdown" title="More actions"><i class="bi bi-three-dots-vertical"></i></button></div>
                            </div>
                        </td>
                    </tr>

                    {{-- ROW 4: DD Failed - shows shield icon only (mic disabled) --}}
                    <tr class="sl-row-failed">
                        <td class="px-4 py-3"><div class="sl-rank-badge rank-blue">13</div></td>
                        <td class="py-3"><div class="d-flex align-items-center gap-2"><div class="sl-av av-blue"><span>MT</span></div><div><div class="fw-semibold small text-dark">Molapo Tech Solutions</div><div class="sl-sub">APP-006 · Maseru</div></div></div></td>
                        <td><span class="sl-sector-tag">Technology</span></td>
                        <td class="text-center"><span class="fw-bold text-warning">68%</span></td>
                        <td class="text-center"><span class="fw-bold text-muted">—</span></td>
                        <td class="text-center"><div class="sl-total-score text-danger">68%</div></td>
                        <td><div class="d-flex gap-1"><span class="sl-pdo pdo-off" title="Women-owned">W</span><span class="sl-pdo pdo-y" title="Youth-owned">Y</span><span class="sl-pdo pdo-off" title="Rural">R</span></div></td>
                        <td><div><div class="small fw-medium">17 Mar 2025</div><div class="sl-sub">14:00 · LEHSFF Boardroom</div></div></td>
                        <td><div><span class="sl-dd-badge dd-fail">Fail</span><div class="sl-sub mt-1">22 Mar 2025</div></div></td>
                        <td><span class="sl-status-pill pill-ddfail">DD Fail</span><div class="sl-sub mt-1"><button class="text-primary small border-0 bg-transparent p-0 text-decoration-underline">Find replacement</button></div></td>
                        <td class="text-center pe-4">
                            <div class="d-flex justify-content-center gap-1">
                                <button class="sl-action-btn" title="View Application"><i class="bi bi-eye"></i></button>
                                <button class="sl-action-btn" title="Score Pitch" disabled style="opacity:0.5"><i class="bi bi-mic-fill"></i></button>
                                <button class="sl-action-btn" title="Due Diligence"><i class="bi bi-clipboard-check"></i></button>
                                <div class="dropdown"><button class="sl-action-btn" data-bs-toggle="dropdown" title="More actions"><i class="bi bi-three-dots-vertical"></i></button></div>
                            </div>
                        </td>
                    </tr>

                    {{-- ROW 5: Top 10 pending DD - shows shield icon only (mic disabled) --}}
                    <tr>
                        <td class="px-4 py-3"><div class="sl-rank-badge rank-purple">6</div></td>
                        <td class="py-3"><div class="d-flex align-items-center gap-2"><div class="sl-av av-blue"><span>KS</span></div><div><div class="fw-semibold small text-dark">Khahliso Solar Tech</div><div class="sl-sub">APP-013 · Maseru</div></div></div></td>
                        <td><span class="sl-sector-tag">Technology</span></td>
                        <td class="text-center"><span class="fw-bold text-warning">77%</span></td>
                        <td class="text-center"><span class="fw-bold text-warning">73%</span></td>
                        <td class="text-center"><div class="sl-total-score text-warning">75%</div></td>
                        <td><div class="d-flex gap-1"><span class="sl-pdo pdo-off" title="Women-owned">W</span><span class="sl-pdo pdo-y" title="Youth-owned">Y</span><span class="sl-pdo pdo-off" title="Rural">R</span></div></td>
                        <td><div><div class="small fw-medium">16 Mar 2025</div><div class="sl-sub">11:00 · LEHSFF Boardroom</div></div></td>
                        <td><button class="btn sl-ghost-xs"><i class="bi bi-clipboard-check me-1"></i>Schedule DD</button></td>
                        <td><span class="sl-status-pill pill-prov-top10">Prov. Top 10</span></td>
                        <td class="text-center pe-4">
                            <div class="d-flex justify-content-center gap-1">
                                <button class="sl-action-btn" title="View Application"><i class="bi bi-eye"></i></button>
                                <button class="sl-action-btn" title="Score Pitch" disabled style="opacity:0.5"><i class="bi bi-mic-fill"></i></button>
                                <button class="sl-action-btn" title="Due Diligence"><i class="bi bi-clipboard-check"></i></button>
                                <div class="dropdown"><button class="sl-action-btn" data-bs-toggle="dropdown" title="More actions"><i class="bi bi-three-dots-vertical"></i></button></div>
                            </div>
                        </td>
                    </tr>

                    {{-- ROW 6: Not Shortlisted (replacement candidate) --}}
                    <tr class="sl-row-replaced">
                        <td class="px-4 py-3"><div class="sl-rank-badge rank-default">21</div></td>
                        <td class="py-3"><div class="d-flex align-items-center gap-2"><div class="sl-av av-pink"><span>LS</span></div><div><div class="fw-semibold small text-dark">Lesotho Stitch Co.</div><div class="sl-sub">APP-002 · Leribe</div></div></div></td>
                        <td><span class="sl-sector-tag">Textile</span></td>
                        <td class="text-center"><span class="fw-bold text-warning">65%</span></td>
                        <td class="text-center"><span class="text-muted small">—</span></td>
                        <td class="text-center"><div class="sl-total-score text-warning">65%</div></td>
                        <td><div class="d-flex gap-1"><span class="sl-pdo pdo-off" title="Women-owned">W</span><span class="sl-pdo pdo-off" title="Youth-owned">Y</span><span class="sl-pdo pdo-off" title="Rural">R</span></div></td>
                        <td><span class="text-muted small">—</span></td>
                        <td><span class="text-muted small">—</span></td>
                        <td><span class="sl-status-pill pill-not-shortlisted">Not Shortlisted</span></td>
                        <td class="text-center pe-4">
                            <div class="d-flex justify-content-center gap-1">
                                <button class="sl-action-btn" title="View Application"><i class="bi bi-eye"></i></button>
                                <div class="dropdown"><button class="sl-action-btn" data-bs-toggle="dropdown" title="More actions"><i class="bi bi-three-dots-vertical"></i></button></div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="sl-pag-bar">
            <small class="text-muted">Showing 1–6 of 22</small>
            <div class="d-flex gap-2 align-items-center">
                <select class="sl-select" style="width:auto;">
                    <option value="10" selected>10</option>
                    <option value="20">20</option>
                    <option value="50">All</option>
                </select>
                <div class="d-flex gap-1">
                    <button class="sl-pg-btn" disabled><i class="bi bi-chevron-left"></i></button>
                    <button class="sl-pg-btn sl-pg-active">1</button>
                    <button class="sl-pg-btn">2</button>
                    <button class="sl-pg-btn">3</button>
                    <button class="sl-pg-btn"><i class="bi bi-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </div>


</div>

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
.sl-ev-chip { font-size:.65rem; font-weight:600; background:#f1f5f9; color:var(--muted); border-radius:4px; padding:1px 6px; cursor:default; }

/* ── Pagination ── */
.sl-pag-bar { display:flex; align-items:center; justify-content:space-between; padding:12px 20px; border-top:1px solid var(--border); flex-wrap:wrap; gap:8px; }
.sl-pg-btn { width:30px; height:30px; border-radius:6px; border:1px solid var(--border); background:#fff; color:var(--muted); cursor:pointer; font-size:.75rem; display:inline-flex; align-items:center; justify-content:center; transition:all .12s; }
.sl-pg-btn:disabled { opacity:.35; cursor:default; }
.sl-pg-btn:not(:disabled):hover { border-color:#93c5fd; color:#1d4ed8; background:#eff6ff; }
.sl-pg-active { background:#1d4ed8 !important; border-color:#1d4ed8 !important; color:#fff !important; font-weight:700; }

/* ── Action buttons ── */
.sl-action-btn { width:28px; height:28px; border-radius:6px; border:1px solid var(--border); background:#fff; color:var(--muted); cursor:pointer; font-size:.78rem; display:inline-flex; align-items:center; justify-content:center; transition:all .12s; }
.sl-action-btn:hover { background:#eff6ff; border-color:#93c5fd; color:#1d4ed8; }
.sl-action-btn:disabled { opacity:0.5; cursor:not-allowed; }
.sl-action-link { display:flex; align-items:center; gap:8px; padding:10px 12px; border-radius:var(--r-sm); border:1px solid var(--border); background:#fff; font-size:.82rem; font-weight:500; color:var(--text); cursor:pointer; transition:all .12s; }
.sl-action-link:hover { background:#eff6ff; border-color:#93c5fd; color:#1d4ed8; }

/* ── Panel (hidden static) ── */
.sl-backdrop { position:fixed; inset:0; background:rgba(0,0,0,.4); z-index:1040; display:none; }
.sl-panel { position:fixed; top:0; right:0; bottom:0; width:100%; max-width:520px; z-index:1050; background:#fff; display:none; flex-direction:column; box-shadow:-4px 0 32px rgba(0,0,0,.14); }
@media (min-width:577px) { .sl-panel { display:none; } }

/* ── Tooltip hover effect for icons -- native title attribute will handle this */
</style>
</x-app-layout>