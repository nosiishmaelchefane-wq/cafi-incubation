<x-app-layout>

    {{-- ApexCharts CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <div class="incubation-dashboard p-4">

        {{-- ═══════════════════════════════════════════
            PAGE HEADER + FILTERS
        ═══════════════════════════════════════════ --}}
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div>
                <h4 class="fw-bold mb-1">
                    <i class="bi bi-building-fill text-primary me-2"></i>Incubation Dashboard
                </h4>
                <p class="text-muted small mb-0">LEHSFF programme overview · Real-time monitoring &amp; reporting</p>
            </div>
            {{-- Global Filters --}}
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <select class="form-select form-select-sm" style="width:auto;" id="filterCohort" onchange="refreshCharts()">
                    <option value="all">All Cohorts</option>
                    <option value="1">Cohort 1 (Nov 2023)</option>
                    <option value="2" selected>Cohort 2 (Jun 2024)</option>
                    <option value="3">Cohort 3 (Feb 2025)</option>
                </select>
                <select class="form-select form-select-sm" style="width:auto;" id="filterPeriod" onchange="refreshCharts()">
                    <option value="all">All Time</option>
                    <option value="q1">Q1 2025</option>
                    <option value="q2" selected>Q2 2025</option>
                    <option value="q3">Q3 2025</option>
                </select>
                <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
                    <i class="bi bi-download"></i> Export
                </button>
                <button class="btn btn-sm btn-primary d-flex align-items-center gap-1">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </button>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════
            KPI SUMMARY CARDS
        ═══════════════════════════════════════════ --}}
        <div class="row g-3 mb-4">

            <div class="col-6 col-md-4 col-xl-2">
                <div class="card kpi-card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="kpi-icon bg-primary bg-opacity-10 text-primary mb-3">
                            <i class="bi bi-building fs-5"></i>
                        </div>
                        <div class="fw-bold fs-3 lh-1 mb-1">48</div>
                        <div class="text-muted small">Total Enterprises</div>
                        <div class="mt-2">
                            <span class="badge bg-success bg-opacity-10 text-white small">
                                <i class="bi bi-arrow-up-short"></i>+6 this cohort
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-4 col-xl-2">
                <div class="card kpi-card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="kpi-icon bg-success bg-opacity-10 text-white mb-3">
                            <i class="bi bi-check-circle-fill fs-5"></i>
                        </div>
                        <div class="fw-bold fs-3 lh-1 mb-1">31</div>
                        <div class="text-muted small">Active Incubation</div>
                        <div class="mt-2">
                            <span class="badge bg-primary bg-opacity-10 text-primary small">64.6%</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-4 col-xl-2">
                <div class="card kpi-card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="kpi-icon bg-warning bg-opacity-10 text-warning mb-3">
                            <i class="bi bi-mortarboard-fill fs-5"></i>
                        </div>
                        <div class="fw-bold fs-3 lh-1 mb-1">17</div>
                        <div class="text-muted small">Graduated</div>
                        <div class="mt-2">
                            <span class="badge bg-warning bg-opacity-10 text-warning small">35.4%</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-4 col-xl-2">
                <div class="card kpi-card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="kpi-icon bg-info bg-opacity-10 text-info mb-3">
                            <i class="bi bi-people-fill fs-5"></i>
                        </div>
                        <div class="fw-bold fs-3 lh-1 mb-1">5</div>
                        <div class="text-muted small">Active ESOs</div>
                        <div class="mt-2">
                            <span class="badge bg-info bg-opacity-10 text-info small">3 cohorts</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-4 col-xl-2">
                <div class="card kpi-card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="kpi-icon bg-danger bg-opacity-10 text-danger mb-3">
                            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                        </div>
                        <div class="fw-bold fs-3 lh-1 mb-1">7</div>
                        <div class="text-muted small">Reports Overdue</div>
                        <div class="mt-2">
                            <span class="badge bg-danger bg-opacity-10 text-danger small">Action needed</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-4 col-xl-2">
                <div class="card kpi-card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="kpi-icon bg-secondary bg-opacity-10 text-secondary mb-3">
                            <i class="bi bi-journal-check fs-5"></i>
                        </div>
                        <div class="fw-bold fs-3 lh-1 mb-1">142</div>
                        <div class="text-muted small">Engagements Logged</div>
                        <div class="mt-2">
                            <span class="badge bg-success bg-opacity-10 text-white small">
                                <i class="bi bi-arrow-up-short"></i>+23 this week
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ═══════════════════════════════════════════
            ROW 2: APPLICATION PIPELINE + COHORT STATUS
        ═══════════════════════════════════════════ --}}
        <div class="row g-4 mb-4">

            {{-- Application Pipeline Funnel --}}
            <div class="col-12 col-lg-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="fw-bold mb-0">Application Pipeline</h6>
                            <small class="text-muted">Cohort 2 · Intake funnel</small>
                        </div>
                        <span class="badge bg-primary rounded-pill">Cohort 2</span>
                    </div>
                    <div class="card-body p-3">
                        <div id="chart-pipeline"></div>
                    </div>
                </div>
            </div>

            {{-- Cohort Enterprise Status Donut --}}
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h6 class="fw-bold mb-0">Enterprise Status</h6>
                        <small class="text-muted">All cohorts combined</small>
                    </div>
                    <div class="card-body p-3 d-flex flex-column align-items-center justify-content-center">
                        <div id="chart-enterprise-status" class="w-100"></div>
                    </div>
                </div>
            </div>

            {{-- Gender Breakdown Radialbar --}}
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h6 class="fw-bold mb-0">PDO Indicators</h6>
                        <small class="text-muted">Women &amp; youth targets</small>
                    </div>
                    <div class="card-body p-3">
                        <div id="chart-pdo"></div>
                        <div class="row g-2 mt-1 text-center">
                            <div class="col-6">
                                <div class="small text-muted">Women-owned</div>
                                <div class="fw-bold text-primary">58%</div>
                            </div>
                            <div class="col-6">
                                <div class="small text-muted">Youth-owned</div>
                                <div class="fw-bold text-success">43%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ═══════════════════════════════════════════
            ROW 3: REPORTING COMPLIANCE + ENGAGEMENTS
        ═══════════════════════════════════════════ --}}
        <div class="row g-4 mb-4">

            {{-- Monthly Report Submission Compliance --}}
            <div class="col-12 col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h6 class="fw-bold mb-0">Report Submission Compliance</h6>
                            <small class="text-muted">Enterprise &amp; ESO monthly reports · last 6 months</small>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-secondary active-filter" id="btnEnterprise" onclick="switchCompliance('enterprise')">Enterprise</button>
                            <button class="btn btn-sm btn-outline-secondary" id="btnESO" onclick="switchCompliance('eso')">ESO</button>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div id="chart-compliance"></div>
                    </div>
                </div>
            </div>

            {{-- Engagement Sessions by Type --}}
            <div class="col-12 col-lg-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h6 class="fw-bold mb-0">Engagement Sessions by Type</h6>
                        <small class="text-muted">All ESOs · current cohort</small>
                    </div>
                    <div class="card-body p-3">
                        <div id="chart-engagement-type"></div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ═══════════════════════════════════════════
            ROW 4: SECTOR BREAKDOWN + ESO PERFORMANCE
        ═══════════════════════════════════════════ --}}
        <div class="row g-4 mb-4">

            {{-- Enterprises by Sector --}}
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h6 class="fw-bold mb-0">Enterprises by Sector</h6>
                        <small class="text-muted">NSDP II aligned sectors</small>
                    </div>
                    <div class="card-body p-3">
                        <div id="chart-sector"></div>
                    </div>
                </div>
            </div>

            {{-- ESO Engagement Performance --}}
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h6 class="fw-bold mb-0">ESO Engagement Performance</h6>
                        <small class="text-muted">Sessions logged per ESO</small>
                    </div>
                    <div class="card-body p-3">
                        <div id="chart-eso-performance"></div>
                    </div>
                </div>
            </div>

            {{-- Weekly Engagement Trend --}}
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h6 class="fw-bold mb-0">Weekly Engagement Trend</h6>
                        <small class="text-muted">Sessions logged per week</small>
                    </div>
                    <div class="card-body p-3">
                        <div id="chart-weekly-trend"></div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ═══════════════════════════════════════════
            ROW 5: RECENT ACTIVITY TABLE + OVERDUE REPORTS
        ═══════════════════════════════════════════ --}}
        <div class="row g-4">

            {{-- Recent Activity --}}
            <div class="col-12 col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0">Recent Activity</h6>
                        <a href="#" class="btn btn-sm btn-link text-primary p-0">View all</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-4 py-3">Enterprise</th>
                                        <th class="py-3">ESO</th>
                                        <th class="py-3">Activity</th>
                                        <th class="py-3">Status</th>
                                        <th class="py-3">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="ent-avatar bg-primary bg-opacity-10 text-primary">MB</div>
                                                <span class="fw-medium">MoroAgri Basotho</span>
                                            </div>
                                        </td>
                                        <td>Thuso ESO</td>
                                        <td>Monthly Report Submitted</td>
                                        <td><span class="badge bg-success bg-opacity-10 text-white">Submitted</span></td>
                                        <td class="text-muted">12 Mar 2025</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="ent-avatar bg-warning bg-opacity-10 text-warning">LS</div>
                                                <span class="fw-medium">Lesotho Stitch Co.</span>
                                            </div>
                                        </td>
                                        <td>Bophelo Hub</td>
                                        <td>Engagement Session Logged</td>
                                        <td><span class="badge bg-primary bg-opacity-10 text-primary">Logged</span></td>
                                        <td class="text-muted">11 Mar 2025</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="ent-avatar bg-success bg-opacity-10 text-white">TF</div>
                                                <span class="fw-medium">TechForward LS</span>
                                            </div>
                                        </td>
                                        <td>Ntlafatso ESO</td>
                                        <td>Due Diligence Passed</td>
                                        <td><span class="badge bg-info bg-opacity-10 text-info">Passed</span></td>
                                        <td class="text-muted">10 Mar 2025</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="ent-avatar bg-danger bg-opacity-10 text-danger">KF</div>
                                                <span class="fw-medium">Khomo Foods</span>
                                            </div>
                                        </td>
                                        <td>Thuso ESO</td>
                                        <td>Report Overdue</td>
                                        <td><span class="badge bg-danger bg-opacity-10 text-danger">Overdue</span></td>
                                        <td class="text-muted">01 Mar 2025</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="ent-avatar bg-secondary bg-opacity-10 text-secondary">NW</div>
                                                <span class="fw-medium">Naledi Weave</span>
                                            </div>
                                        </td>
                                        <td>Bophelo Hub</td>
                                        <td>Graduated &amp; Archived</td>
                                        <td><span class="badge bg-secondary bg-opacity-10 text-secondary">Graduated</span></td>
                                        <td class="text-muted">28 Feb 2025</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Overdue & Pending Actions --}}
            <div class="col-12 col-lg-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-exclamation-circle-fill text-danger me-2"></i>Overdue Reports
                        </h6>
                        <span class="badge bg-danger rounded-pill">7 overdue</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="d-flex flex-column gap-2">

                            <div class="overdue-item p-3 rounded-3 border border-danger border-opacity-25 bg-danger bg-opacity-10">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div>
                                        <div class="fw-semibold small">Khomo Foods</div>
                                        <div class="text-muted" style="font-size:0.75rem;">Monthly Report · Feb 2025 · Thuso ESO</div>
                                    </div>
                                    <span class="badge bg-danger small flex-shrink-0">14 days late</span>
                                </div>
                                <div class="mt-2 d-flex gap-2">
                                    <button class="btn btn-xs btn-outline-danger py-0 px-2" style="font-size:0.7rem;">Remind</button>
                                    <button class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size:0.7rem;">View</button>
                                </div>
                            </div>

                            <div class="overdue-item p-3 rounded-3 border border-warning border-opacity-25 bg-warning bg-opacity-10">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div>
                                        <div class="fw-semibold small">Lerato Crafts</div>
                                        <div class="text-muted" style="font-size:0.75rem;">Monthly Report · Feb 2025 · Ntlafatso ESO</div>
                                    </div>
                                    <span class="badge bg-warning text-dark small flex-shrink-0">9 days late</span>
                                </div>
                                <div class="mt-2 d-flex gap-2">
                                    <button class="btn btn-xs btn-outline-warning py-0 px-2" style="font-size:0.7rem;">Remind</button>
                                    <button class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size:0.7rem;">View</button>
                                </div>
                            </div>

                            <div class="overdue-item p-3 rounded-3 border border-warning border-opacity-25 bg-warning bg-opacity-10">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div>
                                        <div class="fw-semibold small">Selemo Organics</div>
                                        <div class="text-muted" style="font-size:0.75rem;">ESO Report · Feb 2025 · Bophelo Hub</div>
                                    </div>
                                    <span class="badge bg-warning text-dark small flex-shrink-0">7 days late</span>
                                </div>
                                <div class="mt-2 d-flex gap-2">
                                    <button class="btn btn-xs btn-outline-warning py-0 px-2" style="font-size:0.7rem;">Remind</button>
                                    <button class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size:0.7rem;">View</button>
                                </div>
                            </div>

                            <div class="overdue-item p-3 rounded-3 border border-danger border-opacity-25 bg-danger bg-opacity-10">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div>
                                        <div class="fw-semibold small">Molapo Tech Solutions</div>
                                        <div class="text-muted" style="font-size:0.75rem;">Monthly Report · Jan 2025 · Thuso ESO</div>
                                    </div>
                                    <span class="badge bg-danger small flex-shrink-0">32 days late</span>
                                </div>
                                <div class="mt-2 d-flex gap-2">
                                    <button class="btn btn-xs btn-outline-danger py-0 px-2" style="font-size:0.7rem;">Remind</button>
                                    <button class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size:0.7rem;">View</button>
                                </div>
                            </div>

                            <a href="#" class="btn btn-sm btn-outline-secondary w-100 mt-1">
                                View all 7 overdue reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    {{-- ═══════════════════════════════════════════
        STYLES
    ═══════════════════════════════════════════ --}}
    <style>
    .incubation-dashboard .kpi-card {
        border-radius: 14px !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .incubation-dashboard .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.10) !important;
    }
    .incubation-dashboard .kpi-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .incubation-dashboard .card {
        border-radius: 14px !important;
    }
    .incubation-dashboard .card-header {
        border-radius: 14px 14px 0 0 !important;
    }
    .incubation-dashboard .ent-avatar {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 700;
        flex-shrink: 0;
    }
    .incubation-dashboard .overdue-item {
        transition: transform 0.15s ease;
    }
    .incubation-dashboard .overdue-item:hover {
        transform: translateX(3px);
    }
    .incubation-dashboard .active-filter {
        background-color: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }
    </style>

    {{-- ═══════════════════════════════════════════
        APEXCHARTS SCRIPTS
    ═══════════════════════════════════════════ --}}
    <script>
    const chartOpts = {
        fontFamily: 'inherit',
        toolbar: { show: false },
    };

    // ── 1. Application Pipeline (Funnel) ──
    new ApexCharts(document.getElementById('chart-pipeline'), {
        chart: { ...chartOpts, type: 'bar', height: 240 },
        plotOptions: {
            bar: {
                horizontal: true,
                distributed: true,
                borderRadius: 6,
                dataLabels: { position: 'center' },
            }
        },
        colors: ['#0d6efd','#198754','#ffc107','#0dcaf0','#6c757d','#dc3545','#20c997'],
        dataLabels: {
            enabled: true,
            formatter: (val) => val,
            style: { fontSize: '12px', colors: ['#fff'] }
        },
        series: [{
            name: 'Applications',
            data: [210, 185, 140, 60, 30, 15, 10]
        }],
        xaxis: {
            categories: ['Submitted','Screened Eligible','Evaluated','Top 20 Shortlisted','Pitched','Top 10','Final Cohort'],
            labels: { style: { fontSize: '11px' } }
        },
        legend: { show: false },
        tooltip: { y: { formatter: (v) => `${v} applications` } }
    }).render();

    // ── 2. Enterprise Status Donut ──
    new ApexCharts(document.getElementById('chart-enterprise-status'), {
        chart: { ...chartOpts, type: 'donut', height: 230 },
        series: [31, 17, 4, 6, 3],
        labels: ['Active','Graduated','Dropped Out','Pending Start','Suspended'],
        colors: ['#0d6efd','#198754','#dc3545','#ffc107','#6c757d'],
        legend: { position: 'bottom', fontSize: '11px' },
        plotOptions: { pie: { donut: { size: '65%', labels: { show: true, total: { show: true, label: 'Total', fontSize: '12px' } } } } },
        dataLabels: { enabled: false },
    }).render();

    // ── 3. PDO Radialbar (Women + Youth) ──
    new ApexCharts(document.getElementById('chart-pdo'), {
        chart: { ...chartOpts, type: 'radialBar', height: 180 },
        series: [58, 43],
        labels: ['Women-owned', 'Youth-owned'],
        colors: ['#0d6efd', '#198754'],
        plotOptions: {
            radialBar: {
                offsetY: -10,
                hollow: { size: '30%' },
                dataLabels: {
                    name: { fontSize: '11px' },
                    value: { fontSize: '13px', fontWeight: 700 },
                    total: { show: true, label: 'PDO', fontSize: '10px' }
                }
            }
        },
    }).render();

    // ── 4. Report Compliance (Grouped Bar) ──
    let complianceChart = new ApexCharts(document.getElementById('chart-compliance'), {
        chart: { ...chartOpts, type: 'bar', height: 240, stacked: false },
        plotOptions: { bar: { borderRadius: 5, columnWidth: '60%' } },
        colors: ['#0d6efd','#198754','#dc3545'],
        series: [
            { name: 'Submitted On Time', data: [28, 30, 25, 31, 27, 29] },
            { name: 'Submitted Late',    data: [4, 3, 6, 2, 5, 3] },
            { name: 'Not Submitted',     data: [3, 2, 4, 2, 3, 3] },
        ],
        xaxis: {
            categories: ['Oct 2024','Nov 2024','Dec 2024','Jan 2025','Feb 2025','Mar 2025'],
            labels: { style: { fontSize: '11px' } }
        },
        yaxis: { labels: { style: { fontSize: '11px' } } },
        legend: { position: 'top', fontSize: '11px' },
        dataLabels: { enabled: false },
        tooltip: { y: { formatter: (v) => `${v} reports` } }
    });
    complianceChart.render();

    function switchCompliance(type) {
        const enterprise = [
            { name: 'Submitted On Time', data: [28, 30, 25, 31, 27, 29] },
            { name: 'Submitted Late',    data: [4, 3, 6, 2, 5, 3] },
            { name: 'Not Submitted',     data: [3, 2, 4, 2, 3, 3] },
        ];
        const eso = [
            { name: 'Submitted On Time', data: [5, 5, 4, 5, 5, 5] },
            { name: 'Submitted Late',    data: [0, 0, 1, 0, 0, 0] },
            { name: 'Not Submitted',     data: [0, 0, 0, 0, 0, 0] },
        ];
        complianceChart.updateSeries(type === 'enterprise' ? enterprise : eso);
        document.getElementById('btnEnterprise').classList.toggle('active-filter', type === 'enterprise');
        document.getElementById('btnESO').classList.toggle('active-filter', type === 'eso');
    }

    // ── 5. Engagement Sessions by Type ──
    new ApexCharts(document.getElementById('chart-engagement-type'), {
        chart: { ...chartOpts, type: 'pie', height: 240 },
        series: [42, 35, 28, 19, 12, 6],
        labels: ['Mentorship','Business Coaching','Financial Literacy','Marketing','Legal/Compliance','Networking'],
        colors: ['#0d6efd','#198754','#ffc107','#0dcaf0','#6f42c1','#fd7e14'],
        legend: { position: 'bottom', fontSize: '11px' },
        dataLabels: { style: { fontSize: '11px' } },
    }).render();

    // ── 6. Sector Breakdown (Horizontal Bar) ──
    new ApexCharts(document.getElementById('chart-sector'), {
        chart: { ...chartOpts, type: 'bar', height: 240 },
        plotOptions: { bar: { horizontal: true, borderRadius: 5, barHeight: '55%', distributed: true } },
        colors: ['#0d6efd','#198754','#ffc107','#dc3545','#0dcaf0','#6f42c1','#fd7e14'],
        dataLabels: { enabled: true, style: { fontSize: '11px', colors: ['#fff'] } },
        series: [{ name: 'Enterprises', data: [12, 9, 8, 6, 5, 5, 3] }],
        xaxis: {
            categories: ['Agriculture','Technology','Manufacturing','Retail','Textile','Food & Bev','Services'],
            labels: { style: { fontSize: '11px' } }
        },
        legend: { show: false },
        tooltip: { y: { formatter: (v) => `${v} enterprises` } }
    }).render();

    // ── 7. ESO Performance (Grouped Column) ──
    new ApexCharts(document.getElementById('chart-eso-performance'), {
        chart: { ...chartOpts, type: 'bar', height: 240 },
        plotOptions: { bar: { borderRadius: 5, columnWidth: '55%' } },
        colors: ['#0d6efd','#20c997'],
        series: [
            { name: 'Sessions Logged',  data: [32, 28, 25, 30, 27] },
            { name: 'Target',           data: [30, 30, 30, 30, 30] },
        ],
        xaxis: {
            categories: ['Thuso ESO','Bophelo Hub','Ntlafatso ESO','Khotso Centre','Liteboho ESO'],
            labels: { style: { fontSize: '10px' }, rotate: -15 }
        },
        yaxis: { labels: { style: { fontSize: '11px' } } },
        legend: { position: 'top', fontSize: '11px' },
        dataLabels: { enabled: false },
        tooltip: { y: { formatter: (v) => `${v} sessions` } }
    }).render();

    // ── 8. Weekly Engagement Trend (Area) ──
    new ApexCharts(document.getElementById('chart-weekly-trend'), {
        chart: { ...chartOpts, type: 'area', height: 240, sparkline: { enabled: false } },
        stroke: { curve: 'smooth', width: 2 },
        fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.05 } },
        colors: ['#0d6efd'],
        series: [{ name: 'Sessions', data: [8, 11, 7, 14, 10, 13, 9, 16, 12, 15, 11, 18] }],
        xaxis: {
            categories: ['Wk1','Wk2','Wk3','Wk4','Wk5','Wk6','Wk7','Wk8','Wk9','Wk10','Wk11','Wk12'],
            labels: { style: { fontSize: '10px' } }
        },
        yaxis: { labels: { style: { fontSize: '11px' } } },
        dataLabels: { enabled: false },
        markers: { size: 3 },
        tooltip: { y: { formatter: (v) => `${v} sessions` } }
    }).render();

    // ── Global filter stub (re-renders with fake data on change) ──
    function refreshCharts() {
        // In production: fire Livewire event or AJAX to reload chart data
        console.log('Cohort:', document.getElementById('filterCohort').value,
                    'Period:', document.getElementById('filterPeriod').value);
    }
    </script>
  

</x-app-layout>