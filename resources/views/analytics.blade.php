@extends('layouts.sidebar')

@section('title', ':: Analytics ::')

@section('content')
<div class="container-fluid">
    <style>
        .chart-container { position: relative; height: 320px; }
        /* Compact DataTable styles inside modal */
        #txModal .dataTables_wrapper .row { margin-left: 0; margin-right: 0; }
        #txModal .dataTables_wrapper .dataTables_paginate .pagination { margin: 0; gap: 0; }
        #txModal .dataTables_wrapper .dataTables_info { padding-top: .25rem; }
        #txModal .dataTables_wrapper .dataTables_filter input { padding: .25rem .5rem; height: 30px; }
        #txModal .dataTables_wrapper .form-select { padding: .25rem 1.5rem .25rem .5rem; height: 30px; }
        #txModal table.table-sm td, 
        #txModal table.table-sm th { padding: .25rem .4rem; }
        #txModal table { font-size: .85rem; }
        #txModal .dataTables_wrapper .dataTables_length,
        #txModal .dataTables_wrapper .dataTables_filter { margin: 0 0 .25rem 0; }
        #txModal .dataTables_wrapper .dataTables_paginate .page-link { padding: .15rem .4rem; font-size: .8rem; }
        #txModal thead th { position: sticky; top: 0; background: #ffffff; z-index: 1; }
    </style>
    <div class="card border-0 shadow-md mb-3">
        <div class="card-header bg-white border-bottom">
            <h6 class="fw-bold text-dark mb-0">Filtres</h6>
        </div>
        <div class="card-body">
            <form method="get" action="{{ route('analytics.index') }}" class="row g-2 align-items-end">
                <div class="col-auto">
                    <label class="form-label mb-0">Période</label>
                    <select name="period" id="periodSelect" class="form-select form-select-sm">
                        <option value="day" {{ ($period ?? '')==='day' ? 'selected' : '' }}>Aujourd'hui</option>
                        <option value="week" {{ ($period ?? '')==='week' ? 'selected' : '' }}>Semaine</option>
                        <option value="month" {{ ($period ?? 'month')==='month' ? 'selected' : '' }}>Mois courant</option>
                        <option value="all" {{ ($period ?? '')==='all' ? 'selected' : '' }}>Tout</option>
                        <option value="range" {{ ($period ?? '')==='range' ? 'selected' : '' }}>Plage</option>
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label mb-0">Office</label>
                    <select name="office" class="form-select form-select-sm">
                        <option value="">Tous</option>
                        @foreach(($offices ?? []) as $o)
                        <option value="{{ $o }}" {{ ($office ?? '')===$o ? 'selected' : '' }}>{{ $o }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label mb-0">Type</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">Tous</option>
                        <option value="W2A" {{ ($type ?? '')==='W2A' ? 'selected' : '' }}>Dépôt (W2A)</option>
                        <option value="A2W" {{ ($type ?? '')==='A2W' ? 'selected' : '' }}>Retrait (A2W)</option>
                    </select>
                </div>
                <div id="rangeFields" class="col-auto align-items-end gap-2" style="display: none;">
                    <div class="me-2">
                        <label class="form-label mb-0">Du</label>
                        <input type="date" name="start" class="form-control form-control-sm" value="{{ $startStr ?? '' }}">
                    </div>
                    <div>
                        <label class="form-label mb-0">Au</label>
                        <input type="date" name="end" class="form-control form-control-sm" value="{{ $endStr ?? '' }}">
                    </div>
                </div>
                <div class="col-auto d-flex align-items-end gap-2">
                    <button class="btn btn-sm btn-primary" type="submit">Appliquer</button>
                    <a class="btn btn-sm btn-outline-secondary" id="exportBtn" href="#">Exporter CSV</a>
                    <div class="d-flex align-items-end gap-2">
                        <div>
                            <label class="form-label mb-0">Série</label>
                            <select id="seriesSelect" class="form-select form-select-sm">
                                <option value="tx">Transactions vs. Mois</option>
                                <option value="charges">Charges vs. Mois</option>
                                @if($hasAmount ?? false)
                                <option value="amounts">Volumes (MGA) vs. Mois</option>
                                @endif
                            </select>
                        </div>
                        <div class="p-0">
                            <a class="btn btn-sm btn-outline-secondary pt-1 " id="exportSeriesBtn" href="#">Exporter série</a>
                        </div>
                    </div>
                    
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-md mb-3">
        <div class="card-header bg-white border-bottom">
            <h6 class="fw-bold text-dark mb-0">Indicateurs</h6>
        </div>
        <div class="card-body">
            <div class="row g-3 mt-1">
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-md" style="border-left: 4px solid #6f42c1;">
                        <div class="card-body d-flex align-items-center">
                            <div class="me-3"><i class="ri-login-circle-line" style="color:#6f42c1; font-size: 24px;"></i></div>
                            <div>
                                <p class="text-muted mb-1">Souscriptions</p>
                                <h3 class="mb-0 fw-bold" style="color: #6f42c1;">{{ number_format($kpis['subscriptions'] ?? 0) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-md" style="border-left: 4px solid #ff6b35;">
                        <div class="card-body d-flex align-items-center">
                            <div class="me-3"><i class="ri-logout-circle-line" style="color:#ff6b35; font-size: 24px;"></i></div>
                            <div>
                                <p class="text-muted mb-1">Désabonnements</p>
                                <h3 class="mb-0 fw-bold" style="color: #ff6b35;">{{ number_format($kpis['unsubscriptions'] ?? 0) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-md" style="border-left: 4px solid #198754;">
                        <div class="card-body d-flex align-items-center">
                            <div class="me-3"><i class="ri-exchange-dollar-line" style="color:#198754; font-size: 24px;"></i></div>
                            <div>
                                <p class="text-muted mb-1">Transactions</p>
                                <h3 class="mb-0 fw-bold" style="color: #198754;">{{ number_format($kpis['transactions'] ?? 0) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-md" style="border-left: 4px solid #0dcaf0;">
                        <div class="card-body d-flex align-items-center">
                            <div class="me-3"><i class="ri-money-dollar-circle-line" style="color:#0dcaf0; font-size: 24px;"></i></div>
                            <div>
                                <p class="text-muted mb-1">Charges (MGA)</p>
                                <h3 class="mb-0 fw-bold" style="color: #0dcaf0;">{{ number_format($kpis['charges'] ?? 0) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-lg-6">
            <div class="card border-0 shadow-md" style="height: 400px;">
                <div class="card-header bg-white border-bottom">
                    <h6 class="fw-bold text-dark mb-0">Souscriptions vs. Mois</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="chartSubs"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-md" style="height: 400px;">
                <div class="card-header bg-white border-bottom">
                    <h6 class="fw-bold text-dark mb-0">Transactions vs. Mois</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="chartTx"></canvas>
                    </div>
                    <small class="text-muted">Astuce: cliquez une barre pour voir les transactions du mois.</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-lg-4">
            <div class="card border-0 shadow-md" style="height: 320px;">
                <div class="card-header bg-white border-bottom">
                    <h6 class="fw-bold text-dark mb-0">Dépôts vs Retraits</h6>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    <div style="width: 180px; height: 180px;">
                        <canvas id="donutDw"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card border-0 shadow-md" style="height: 320px;">
                <div class="card-header bg-white border-bottom">
                    <h6 class="fw-bold text-dark mb-0">Charges vs. Mois</h6>
                </div>
                <div class="card-body">
                    <canvas id="chartCharges"></canvas>
                </div>
            </div>
        </div>
    </div>

    @if($hasAmount ?? false)
    <div class="row mt-3">
        <div class="col-lg-6">
            <div class="card border-0 shadow-md" style="height: 380px;">
                <div class="card-header bg-white border-bottom">
                    <h6 class="fw-bold text-dark mb-0">Volumes (MGA) vs. Mois</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 320px;">
                        <canvas id="chartAmounts"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-md" style="height: 380px;">
                <div class="card-header bg-white border-bottom">
                    <h6 class="fw-bold text-dark mb-0">Montants Dépôts vs Retraits (MGA)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 320px;">
                        <canvas id="chartAmountsSplit"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row mt-3">
        <div class="col-12">
            <div class="card border-0 shadow-md">
                <div class="card-header bg-white border-bottom d-flex align-items-center">
                    <h6 class="fw-bold text-dark mb-0">Activité (Jour × Heure)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered text-center align-middle mb-0" id="heatTable" style="table-layout: fixed;">
                            <thead>
                                <tr>
                                    <th class="text-start" style="width: 80px;">Jour/Heure</th>
                                    @for($h=0;$h<24;$h++)
                                        <th style="font-size: .7rem; padding: .2rem .25rem; width: 36px;">{{ str_pad($h,2,'0',STR_PAD_LEFT) }}h</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $days = [1=>'Dim',2=>'Lun',3=>'Mar',4=>'Mer',5=>'Jeu',6=>'Ven',7=>'Sam'];
                                    $max = max(1, $heatMax ?? 1);
                                @endphp
                                @for($d=1;$d<=7;$d++)
                                    <tr>
                                        <th class="text-start" style="width: 80px; font-size:.75rem; padding:.25rem .35rem;">{{ $days[$d] }}</th>
                                        @for($h=0;$h<24;$h++)
                                            @php
                                                $val = $heatmap[$d][$h] ?? 0;
                                                $t = $val / $max;
                                                // Dégradé orange: de rgb(255,239,224) vers rgb(255,107,53)
                                                $start = [255,239,224];
                                                $end   = [255,107,53];
                                                $r = (int) round($start[0] + ($end[0] - $start[0]) * $t);
                                                $g = (int) round($start[1] + ($end[1] - $start[1]) * $t);
                                                $b = (int) round($start[2] + ($end[2] - $start[2]) * $t);
                                                $bg = "rgb($r,$g,$b)";
                                                $color = $t > 0.6 ? '#fff' : '#333';
                                            @endphp
                                            <td data-dow="{{ $d }}" data-hr="{{ $h }}" style="cursor:pointer; background: {{ $bg }}; color: {{ $color }}; font-size:.7rem; padding:.25rem .2rem;">{{ $val ?: '' }}</td>
                                        @endfor
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                        <small class="text-muted">Plus la case est foncée, plus le nombre de transactions est élevé.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-lg-4">
            <div class="card border-0 shadow-md h-100">
                <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark mb-0">Top Offices (Nombre)</h6>
                    <a href="#" id="exportTopOfficesCount" class="btn btn-sm btn-outline-secondary">Exporter</a>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse(($topOfficesCount ?? []) as $row)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <a href="#" class="link-secondary text-decoration-none drill-office" data-office="{{ $row->office_name }}">{{ $row->office_name ?? 'N/A' }}</a>
                                <span class="badge bg-primary rounded-pill">{{ $row->total }}</span>
                            </li>
                        @empty
                            <li class="list-group-item px-0 text-muted">Aucune donnée</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-md h-100">
                <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark mb-0">Top Offices (Montants)</h6>
                    <a href="#" id="exportTopOfficesAmount" class="btn btn-sm btn-outline-secondary">Exporter</a>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse(($topOfficesAmount ?? []) as $row)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <a href="#" class="link-secondary text-decoration-none drill-office" data-office="{{ $row->office_name }}">{{ $row->office_name ?? 'N/A' }}</a>
                                <span class="badge bg-success rounded-pill">{{ number_format($row->total_amount ?? 0) }}</span>
                            </li>
                        @empty
                            <li class="list-group-item px-0 text-muted">Aucune donnée</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-md h-100">
                <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark mb-0">Top Libellés</h6>
                    <a href="#" id="exportTopLibelles" class="btn btn-sm btn-outline-secondary">Exporter</a>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse(($topLibelles ?? []) as $row)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <a href="#" class="link-secondary text-decoration-none drill-libelle" data-libelle="{{ $row->libelle }}">{{ $row->libelle ?? 'N/A' }}</a>
                                <span class="badge bg-info rounded-pill">{{ $row->total }}</span>
                            </li>
                        @empty
                            <li class="list-group-item px-0 text-muted">Aucune donnée</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle des champs de plage
        const periodSelect = document.getElementById('periodSelect');
        const rangeFields = document.getElementById('rangeFields');
        const updateRangeVisibility = () => {
            rangeFields.style.display = periodSelect.value === 'range' ? 'flex' : 'none';
        };
        periodSelect.addEventListener('change', updateRangeVisibility);
        updateRangeVisibility();

        // Construire l'URL d'export avec les filtres courants
        const exportBtn = document.getElementById('exportBtn');
        const exportSeriesBtn = document.getElementById('exportSeriesBtn');
        const seriesSelect = document.getElementById('seriesSelect');
        const formEl = document.querySelector('form[action$="analytics"]');
        const buildExportUrl = () => {
            const params = new URLSearchParams(new FormData(formEl));
            const url = new URL("{{ route('analytics.export') }}", window.location.origin);
            url.search = params.toString();
            exportBtn.href = url.toString();
        };
        
        const buildSeriesUrl = () => {
            const params = new URLSearchParams(new FormData(formEl));
            let route = "";
            if (seriesSelect.value === 'tx') route = "{{ route('analytics.export.transactions') }}";
            if (seriesSelect.value === 'charges') route = "{{ route('analytics.export.charges') }}";
            if (seriesSelect.value === 'amounts') route = "{{ route('analytics.export.amounts') }}";
            const url = new URL(route, window.location.origin);
            url.search = params.toString();
            exportSeriesBtn.href = url.toString();
        };

        const buildAllExportUrls = () => { buildExportUrl(); buildSeriesUrl(); };
        buildAllExportUrls();
        formEl.addEventListener('change', buildAllExportUrls);
        if (seriesSelect) seriesSelect.addEventListener('change', buildSeriesUrl);


        // Exports séries
        const bindSeriesExport = (anchorId, route) => {
            const a = document.getElementById(anchorId);
            if (!a) return;
            const params = new URLSearchParams(new FormData(document.querySelector('form[action$="analytics"]')));
            const url = new URL(route, window.location.origin);
            url.search = params.toString();
            a.href = url.toString();
        };
        bindSeriesExport('exportTxSeries', "{{ route('analytics.export.transactions') }}");
        bindSeriesExport('exportChargesSeries', "{{ route('analytics.export.charges') }}");
        @if($hasAmount ?? false)
        bindSeriesExport('exportAmountsSeries', "{{ route('analytics.export.amounts') }}");
        @endif

        // Datepickers pour Du/Au
        if (window.flatpickr) {
            flatpickr('input[name="start"]', {
                dateFormat: 'Y-m-d',
                locale: flatpickr.l10ns.fr
            });
            flatpickr('input[name="end"]', {
                dateFormat: 'Y-m-d',
                locale: flatpickr.l10ns.fr
            });
        }

        const series = @json($series ?? []);
        const labels = series.map(s => s.month);
        const subs = series.map(s => s.subscriptions);
        const unsubs = series.map(s => s.unsubscriptions);
        const txs = series.map(s => s.transactions);

        const subsCtx = document.getElementById('chartSubs').getContext('2d');
        new Chart(subsCtx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                        label: 'Souscriptions',
                        data: subs,
                        backgroundColor: '#6f42c1',
                        borderRadius: 4,
                        borderSkipped: false
                    },
                    {
                        label: 'Désabonnements',
                        data: unsubs,
                        backgroundColor: '#ff6b35',
                        borderRadius: 4,
                        borderSkipped: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        const txCtx = document.getElementById('chartTx').getContext('2d');
        const txChart = new Chart(txCtx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Transactions',
                    data: txs,
                    backgroundColor: '#198754',
                    borderRadius: 4,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Donut Dépôts vs Retraits (filtré)
        const deposits = @json($deposits ?? 0);
        const withdrawals = @json($withdrawals ?? 0);
        const dwCtx = document.getElementById('donutDw').getContext('2d');
        new Chart(dwCtx, {
            type: 'doughnut',
            data: {
                labels: ['Dépôts (W2A)', 'Retraits (A2W)'],
                datasets: [{
                    data: [deposits, withdrawals],
                    backgroundColor: ['#ff6b35', '#6f42c1'],
                    borderWidth: 0,
                    cutout: '60%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Charges vs. Mois
        const monthlyCharges = @json($monthlyCharges ?? []);
        const chLabels = monthlyCharges.map(m => m.month);
        const chValues = monthlyCharges.map(m => m.charges);
        const chCtx = document.getElementById('chartCharges').getContext('2d');
        new Chart(chCtx, {
            type: 'line',
            data: { labels: chLabels, datasets: [{ label: 'Charges (MGA)', data: chValues, borderColor: '#08adcf', backgroundColor: '#0dcaf0', fill: true, tension: 0.3 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true }}}
        });

        // Volumes MGA vs. Mois (si disponible)
        const hasAmount = @json($hasAmount ?? false);
        if (hasAmount) {
            const monthlyAmounts = @json($monthlyAmounts ?? []);
            const aLabels = monthlyAmounts.map(m => m.month);
            const aValues = monthlyAmounts.map(m => m.amount);
            const aCtx = document.getElementById('chartAmounts').getContext('2d');
            new Chart(aCtx, {
                type: 'bar',
                data: { labels: aLabels, datasets: [{ label: 'Volumes (MGA)', data: aValues, backgroundColor: '#6f42c1', borderRadius: 4, borderSkipped: false }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true }}}
            });

            // Split W2A vs A2W en montants
            const split = @json($monthlyAmountsSplit ?? []);
            const sLabels = split.map(m => m.month);
            const dValues = split.map(m => m.deposits);
            const wValues = split.map(m => m.withdrawals);
            const sCtx = document.getElementById('chartAmountsSplit').getContext('2d');
            const amountsSplitChart = new Chart(sCtx, {
                type: 'bar',
                data: { labels: sLabels, datasets: [
                    { label: 'Dépôts (W2A)', data: dValues, backgroundColor: '#ff6b35', borderRadius: 4, borderSkipped: false },
                    { label: 'Retraits (A2W)', data: wValues, backgroundColor: '#6f42c1', borderRadius: 4, borderSkipped: false }
                ]},
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true }}, interaction: { intersect: false, mode: 'index' }}
            });

            // Drilldown clic sur Montants Dépôts vs Retraits
            const sCanvas = document.getElementById('chartAmountsSplit');
            sCanvas.onclick = async (evt) => {
                const points = amountsSplitChart.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
                if (!points.length) return;
                const p = points[0];
                const idx = p.index;
                const dsIdx = p.datasetIndex; // 0: W2A, 1: A2W
                const label = sLabels[idx];
                const [monStr, yearStr] = label.split(' ');
                const monthIndex = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'].indexOf(monStr) + 1;
                const year = parseInt(yearStr, 10);
                if (!monthIndex || !year) return;

                const chosenType = dsIdx === 0 ? 'W2A' : 'A2W';

                const formEl2 = document.querySelector('form[action$="analytics"]');
                const params2 = new URLSearchParams(new FormData(formEl2));
                params2.set('year', year);
                params2.set('month', monthIndex.toString());
                params2.set('type', chosenType);
                const url2 = new URL("{{ route('analytics.transactions') }}", window.location.origin);
                url2.search = params2.toString();

            // Préparer lien export direct pour ce drilldown (montants split)
            const txExportBtn2 = document.getElementById('txExportBtn');
            const exportUrl2 = new URL(url2.toString());
            exportUrl2.searchParams.set('export', '1');
            txExportBtn2.href = exportUrl2.toString();

            const res2 = await fetch(url2.toString());
                if (!res2.ok) return;
                const json2 = await res2.json();
                const tbody2 = document.querySelector('#txTable tbody');
                tbody2.innerHTML = '';
                (json2.data || []).forEach(row => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${row.created_at ?? ''}</td>
                        <td>${row.office_name ?? ''}</td>
                        <td>${row.request_type ?? ''}</td>
                        <td>${row.charge ?? ''}</td>
                        <td>${row.amount ?? ''}</td>
                    `;
                    tbody2.appendChild(tr);
                });

            // Init/refresh DataTable
            if (window.jQuery && $.fn.dataTable) {
                if ($.fn.dataTable.isDataTable('#txTable')) {
                    $('#txTable').DataTable().destroy();
                }
                $('#txTable').DataTable({
                    pageLength: 25,
                    lengthMenu: [10, 25, 50, 100],
                    order: [[0, 'desc']],
                    destroy: true
                });
            }

            const modal2 = new bootstrap.Modal(document.getElementById('txModal'));
                modal2.show();
            };
        }

        // Drilldown sur clic barre Transactions
        const txCanvas = document.getElementById('chartTx');
        txCanvas.onclick = async (evt) => {
            const points = txChart.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
            if (!points.length) return;
            const firstPoint = points[0];
            const idx = firstPoint.index;
            const label = labels[idx]; // 'Jan 2025'
            const [monStr, yearStr] = label.split(' ');
            const monthIndex = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'].indexOf(monStr) + 1;
            const year = parseInt(yearStr, 10);
            if (!monthIndex || !year) return;

            const formEl = document.querySelector('form[action$="analytics"]');
            const params = new URLSearchParams(new FormData(formEl));
            params.set('year', year);
            params.set('month', monthIndex.toString());
            const url = new URL("{{ route('analytics.transactions') }}", window.location.origin);
            url.search = params.toString();

            // Lien export direct pour ce mois
            const txExportBtn = document.getElementById('txExportBtn');
            const exportUrl = new URL(url.toString());
            exportUrl.searchParams.set('export', '1');
            txExportBtn.href = exportUrl.toString();

            const res = await fetch(url.toString());
            if (!res.ok) return;
            const json = await res.json();
            const tbody = document.querySelector('#txTable tbody');
            tbody.innerHTML = '';
            (json.data || []).forEach(row => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${row.created_at ?? ''}</td>
                    <td>${row.office_name ?? ''}</td>
                    <td>${row.request_type ?? ''}</td>
                    <td>${row.charge ?? ''}</td>
                    <td>${row.amount ?? ''}</td>
                `;
                tbody.appendChild(tr);
            });

            // Init/refresh DataTable
            if (window.jQuery && $.fn.dataTable) {
                if ($.fn.dataTable.isDataTable('#txTable')) {
                    $('#txTable').DataTable().destroy();
                }
                $('#txTable').DataTable({
                    pageLength: 25,
                    lengthMenu: [10, 25, 50, 100],
                    order: [[0, 'desc']],
                    destroy: true
                });
            }

            const modal = new bootstrap.Modal(document.getElementById('txModal'));
            modal.show();
        };

        // Drilldown Heatmap Jour × Heure
        const heatTable = document.getElementById('heatTable');
        if (heatTable) {
            heatTable.addEventListener('click', async (e) => {
                const cell = e.target.closest('td[data-dow]');
                if (!cell) return;
                const dow = cell.getAttribute('data-dow');
                const hr = cell.getAttribute('data-hr');

                const formElH = document.querySelector('form[action$="analytics"]');
                const paramsH = new URLSearchParams(new FormData(formElH));
                paramsH.set('dow', dow);
                paramsH.set('hr', hr);
                const urlH = new URL("{{ route('analytics.transactions') }}", window.location.origin);
                urlH.search = paramsH.toString();

                // Config export direct
                const txExportBtnH = document.getElementById('txExportBtn');
                const exportUrlH = new URL(urlH.toString());
                exportUrlH.searchParams.set('export', '1');
                txExportBtnH.href = exportUrlH.toString();

                const resH = await fetch(urlH.toString());
                if (!resH.ok) return;
                const jsonH = await resH.json();
                const tbodyH = document.querySelector('#txTable tbody');
                tbodyH.innerHTML = '';
                (jsonH.data || []).forEach(row => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${row.created_at ?? ''}</td>
                        <td>${row.office_name ?? ''}</td>
                        <td>${row.request_type ?? ''}</td>
                        <td>${row.charge ?? ''}</td>
                        <td>${row.amount ?? ''}</td>
                    `;
                    tbodyH.appendChild(tr);
                });

                if (window.jQuery && $.fn.dataTable) {
                    if ($.fn.dataTable.isDataTable('#txTable')) {
                        $('#txTable').DataTable().destroy();
                    }
                    $('#txTable').DataTable({
                        pageLength: 25,
                        lengthMenu: [10, 25, 50, 100],
                        order: [[0, 'desc']],
                        destroy: true
                    });
                }

                const modalH = new bootstrap.Modal(document.getElementById('txModal'));
                modalH.show();
            });
        }

        // Drill Top Offices / Libellés
        document.querySelectorAll('.drill-office').forEach(a => {
            a.addEventListener('click', async (ev) => {
                ev.preventDefault();
                const officeExact = a.getAttribute('data-office');
                const params = new URLSearchParams(new FormData(formEl));
                params.set('office_exact', officeExact);
                const url = new URL("{{ route('analytics.transactions') }}", window.location.origin);
                url.search = params.toString();
                const txExportBtn = document.getElementById('txExportBtn');
                const exportUrl = new URL(url.toString()); exportUrl.searchParams.set('export','1'); txExportBtn.href = exportUrl.toString();
                const res = await fetch(url.toString()); if (!res.ok) return; const json = await res.json();
                const tbody = document.querySelector('#txTable tbody'); tbody.innerHTML = '';
                (json.data||[]).forEach(row=>{ const tr=document.createElement('tr'); tr.innerHTML = `
                    <td>${row.created_at ?? ''}</td>
                    <td>${row.office_name ?? ''}</td>
                    <td>${row.request_type ?? ''}</td>
                    <td>${row.charge ?? ''}</td>
                    <td>${row.amount ?? ''}</td>`; tbody.appendChild(tr);
                });
                if (window.jQuery && $.fn.dataTable) {
                    if ($.fn.dataTable.isDataTable('#txTable')) { $('#txTable').DataTable().destroy(); }
                    $('#txTable').DataTable({ pageLength:25, lengthMenu:[10,25,50,100], order:[[0,'desc']], destroy:true });
                }
                new bootstrap.Modal(document.getElementById('txModal')).show();
            });
        });

        document.querySelectorAll('.drill-libelle').forEach(a => {
            a.addEventListener('click', async (ev) => {
                ev.preventDefault();
                const libelle = a.getAttribute('data-libelle');
                const params = new URLSearchParams(new FormData(formEl));
                params.set('libelle', libelle);
                const url = new URL("{{ route('analytics.transactions') }}", window.location.origin);
                url.search = params.toString();
                const txExportBtn = document.getElementById('txExportBtn');
                const exportUrl = new URL(url.toString()); exportUrl.searchParams.set('export','1'); txExportBtn.href = exportUrl.toString();
                const res = await fetch(url.toString()); if (!res.ok) return; const json = await res.json();
                const tbody = document.querySelector('#txTable tbody'); tbody.innerHTML = '';
                (json.data||[]).forEach(row=>{ const tr=document.createElement('tr'); tr.innerHTML = `
                    <td>${row.created_at ?? ''}</td>
                    <td>${row.office_name ?? ''}</td>
                    <td>${row.request_type ?? ''}</td>
                    <td>${row.charge ?? ''}</td>
                    <td>${row.amount ?? ''}</td>`; tbody.appendChild(tr);
                });
                if (window.jQuery && $.fn.dataTable) {
                    if ($.fn.dataTable.isDataTable('#txTable')) { $('#txTable').DataTable().destroy(); }
                    $('#txTable').DataTable({ pageLength:25, lengthMenu:[10,25,50,100], order:[[0,'desc']], destroy:true });
                }
                new bootstrap.Modal(document.getElementById('txModal')).show();
            });
        });

        // Exports Top lists
        const bindTopExport = (anchorId, route) => {
            const el = document.getElementById(anchorId); if (!el) return;
            const params = new URLSearchParams(new FormData(formEl));
            const url = new URL(route, window.location.origin); url.search = params.toString();
            el.href = url.toString();
        };
        bindTopExport('exportTopOfficesCount', "{{ route('analytics.export.top.offices.count') }}");
        bindTopExport('exportTopOfficesAmount', "{{ route('analytics.export.top.offices.amount') }}");
        bindTopExport('exportTopLibelles', "{{ route('analytics.export.top.libelles') }}");
    });
</script>

<!-- Modal Drilldown -->
<div class="modal fade" id="txModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Transactions du mois</h5>
        <a id="txExportBtn" href="#" class="btn btn-sm btn-outline-secondary ms-2 me-2">Exporter CSV</a>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-sm align-middle" id="txTable">
            <thead>
              <tr>
                <th>Date</th>
                <th>Office</th>
                <th>Type</th>
                <th>Charge</th>
                <th>Montant</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  </div>
@endsection