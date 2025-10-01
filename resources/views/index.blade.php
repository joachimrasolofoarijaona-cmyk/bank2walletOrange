@extends('layouts.sidebar')

@section('title', ':: Accueil ::')

@section('content')
<style>
    .bg-opacity-10 {
        height: 100px;
        width: 100px;
        background-color: #f2ebfb;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .metric-card {
        transition: transform 0.2s ease-in-out;
    }

    .metric-card:hover {
        transform: translateY(-5px);
    }

    .chart-container {
        position: relative;
        height: 300px;
    }
</style>

<div class="container-fluid">
    <div class="row pt-1">
        {{-- subs chart --}}
        <div class="col-lg-3 col-md-6 col-xs-12 gap-3">
            <div class="card border-0 shadow-md metric-card">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="me-3">
                        <div class="bg-opacity-10 rounded-pill p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="ri-login-circle-line fs-1 text-primary"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="fw-bold text-dark mb-1">{{ number_format($total_subscriptions) }}</h3>
                        <p class="text-muted mb-0 fs-6">Souscriptions</p>
                        <small class="text-success">+{{ $daily_subscriptions }} aujourd'hui</small>
                    </div>
                    <div class="ms-auto">
                        <i class="fas fa-ellipsis-h text-muted"></i>
                    </div>
                </div>
            </div>
        </div>
        {{-- balance chart --}}
        <div class="col-lg-3 col-md-6 col-xs-12 gap-3">
            <div class="card border-0 shadow-md metric-card">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="me-3">
                        <div class="bg-opacity-10 rounded-pill p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="ri-file-list-line fs-1 text-primary"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="fw-bold text-dark mb-1">{{ number_format($total_balance) }}</h3>
                        <p class="text-muted mb-0 fs-6">Mini Relevés</p>
                        <small class="text-success">+{{ number_format($daily_balance) }} aujourd'hui</small>
                    </div>
                    <div class="ms-auto">
                        <i class="fas fa-ellipsis-h text-muted"></i>
                    </div>
                </div>
            </div>
        </div>
        {{-- statement chart --}}
        <div class="col-lg-3 col-md-6 col-xs-12 gap-3">
            <div class="card border-0 shadow-md metric-card">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="me-3">
                        <div class="bg-opacity-10 rounded-pill p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="ri-logout-circle-line fs-1 text-warning"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="fw-bold text-dark mb-1">{{ number_format($total_unsubscriptions) }}</h3>
                        <p class="text-muted mb-0 fs-6">Désabonnements</p>
                        <small class="text-warning">+{{ $daily_unsubscriptions }} aujourd'hui</small>
                    </div>
                    <div class="ms-auto">
                        <i class="fas fa-ellipsis-h text-muted"></i>
                    </div>
                </div>
            </div>
        </div>
        {{-- transactions chart --}}
        <div class="col-lg-3 col-md-6 col-xs-12 gap-3">
            <div class="card border-0 shadow-md metric-card">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="me-3">
                        <div class="bg-opacity-10 rounded-pill p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="ri-eye-line fs-1 text-info"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="fw-bold text-dark mb-1">{{ number_format($total_transactions) }}</h3>
                        <p class="text-muted mb-0 fs-6">Consultations Solde</p>
                        <small class="text-info">+{{ $daily_transactions }} aujourd'hui</small>
                    </div>
                    <div class="ms-auto">
                        <i class="fas fa-ellipsis-h text-muted"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid pt-2">
    <div class="row pt-1">
        {{-- Souscription vs. Mois --}}
        <div class="col-lg-6 col-md-6 col-xs-6">
            <div class="card border-0 shadow-md" style="height: 400px;">
                <div class="card-header bg-white border-bottom">
                    <h6 class="fw-bold text-dark mb-1 text-start pt-2 pb-1 ps-2">Souscription vs. Mois</h6>
                </div>
                <div class="card-body">
                    {{-- chart monthly subs --}}
                    <div class="chart-container">
                        <canvas id="monthly_subs" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Transaction vs. Mois --}}
        <div class="col-lg-6 col-md-6 col-xs-6">
            <div class="card border-0 shadow-md" style="height: 400px;">
                <div class="card-header bg-white border-bottom">
                    <h6 class="fw-bold text-dark mb-1 text-start pt-2 pb-1 ps-2">Transaction vs. Mois</h6>
                </div>
                <div class="card-body">
                    {{-- chart monthly transactions --}}
                    <div class="chart-container">
                        <canvas id="monthly_transactions" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Nouvelles sections -->
<div class="row mt-4">
    <!-- Graphique par office_name (copié exactement du style monthly_subs) -->
    <div class="col-lg-3">
        <div class="card border-0 shadow-md" style="height: 300px;">
            <div class="card-body d-flex flex-column">
                <h6 class="fw-bold text-dark mb-3">Transactions par Office</h6>
                <div class="flex-grow-1 overflow-auto" style="max-height: 250px;">
                    <div id="officeContent">
                        <!-- Le contenu sera inséré ici par JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des libelle -->
    <div class="col-lg-3">
        <div class="card border-0 shadow-md" style="height: 300px;">
            <div class="card-body d-flex flex-column">
                <h6 class="fw-bold text-dark mb-3">Top Libellés</h6>
                <div class="flex-grow-1 overflow-auto" style="max-height: 250px;">
                    <div class="account-list">
                        @if(isset($account_names) && count($account_names) > 0)
                        @foreach($account_names->take(8) as $libelle)
                        <div class="d-flex align-items-center justify-content-between mb-2 p-2 rounded bg-light">
                            <span class="text-muted small">{{ $libelle->libelle ?? 'N/A' }}</span>
                            <span class="badge bg-primary">{{ $libelle->transaction_count }}</span>
                        </div>
                        @endforeach
                        @else
                        <p class="text-muted text-center">Aucun libellé trouvé</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Résumé des Charges -->
    <div class="col-lg-3">
        <div class="card border-0 shadow-md" style="height: 300px;">
            <div class="card-body text-center d-flex flex-column justify-content-between">
                <div>
                    <h6 class="fw-bold text-dark mb-3">Résumé des Charges</h6>
                    <div class="mb-3">
                        <hr>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted">Dépôts (W2A)</span>
                            <span class="fw-bold" style="color: #ff6b35;">{{ number_format($current_month_deposits ?? 0) }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted">Retraits (A2W)</span>
                            <span class="fw-bold" style="color: #6f42c1;">{{ number_format($current_month_withdrawals ?? 0) }}</span>
                        </div>
                        <hr>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted fw-bold">Total Charges</span>
                            <span class="fw-bold fs-5 text-success">{{ number_format($current_month_charges ?? 0) }} MGA</span>
                        </div>
                        <hr>
                    </div>
                </div>

                <!-- Mini graphique en anneau -->
                    <div class="mt-4" style="width: 140px; height: 140px; margin: 0 auto;">
                        <canvas id="miniDonut-1" style="width: 140px !important; height: 140px !important;"></canvas>
                    </div>
            </div>
        </div>
    </div>

    <!-- Total des transactions avec mini graphique -->
    <div class="col-lg-3">
        <div class="card border-0 shadow-md  bg-dark text-white" style="height: 300px;">
            <div class="card-body text-center d-flex flex-column justify-content-between">
                <div>
                    <h6 class="fw-bold text-white mb-3">Total Transactions</h6>
                    <div class="mb-3">
                        <h2 class="fw-bold text-primary mb-0">{{ number_format($total_all_transactions ?? 0) }}</h2>
                        <small class="text-muted">Toutes les transactions</small>
                    </div>
                </div>
                <!-- Mini graphique en anneau -->
                <div class="" style="width: 140px; height: 140px; margin: 0 auto;">
                    <canvas id="transactionsDonut" style="width: 140px !important; height: 140px !important;"></canvas>
                </div>
            </div>
        </div>
    </div>
    
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    /* Organisation du dashboard */
    .dashboard-container {
        padding: 20px;
    }

    /* Cartes métriques du haut - hauteur fixe */
    .metric-row .card {
        height: 120px;
        margin-bottom: 20px;
    }

    /* Ligne des graphiques - hauteur alignée */
    .chart-row .card {
        height: 400px;
        margin-bottom: 20px;
    }

    /* Ligne des nouvelles sections - hauteur alignée */
    .sections-row .card {
        height: 400px;
        margin-bottom: 20px;
    }

    /* Espacement entre les sections */
    .container-fluid {
        margin-bottom: 20px;
    }

    .row {
        margin-bottom: 20px;
    }

    /* Style des cartes */
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border-radius: 0.5rem;
    }

    .card-body {
        padding: 1.5rem;
    }

    /* Canvas des graphiques */
    canvas {
        max-height: 100%;
        width: 100% !important;
    }

    /* Responsive */
    @media (max-width: 768px) {

        .chart-row .card,
        .sections-row .card {
            height: auto;
            min-height: 300px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Données pour le graphique principal
        const monthlyData = @json($monthly_data);

        const labels = monthlyData.map(item => item.month);
        const subscriptionsData = monthlyData.map(item => item.subscriptions);
        const unsubscriptionsData = monthlyData.map(item => item.unsubscriptions);

        // Configuration du graphique
        const ctx = document.getElementById('monthly_subs').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                        label: 'Souscriptions',
                        data: subscriptionsData,
                        backgroundColor: '#6f42c1', // Couleur primaire existante
                        borderColor: '#6f42c1',
                        borderWidth: 1,
                        borderRadius: 4,
                        borderSkipped: false,
                    },
                    {
                        label: 'Désabonnements',
                        data: unsubscriptionsData,
                        backgroundColor: '#ff6b35', // Orange Money
                        borderColor: '#ff6b35',
                        borderWidth: 1,
                        borderRadius: 4,
                        borderSkipped: false,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#6f42c1',
                        borderWidth: 1
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)',
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });

        // Graphique Transaction vs. Mois
        const monthlyTransactionsCtx = document.getElementById('monthly_transactions').getContext('2d');
        const transactionsData = monthlyData.map(item => item.transactions || 0);

        new Chart(monthlyTransactionsCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Transactions',
                    data: transactionsData,
                    backgroundColor: '#198754', // Couleur info
                    borderColor: '#17a2b8',
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#17a2b8',
                        borderWidth: 1
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)',
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });

        // Mini graphique en anneau
        const donutCtx = document.getElementById('miniDonut').getContext('2d');
        const currentMonthSubs = @json($monthly_data[5]['subscriptions'] ?? 0);
        const currentMonthUnsubs = @json($monthly_data[5]['unsubscriptions'] ?? 0);

        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: ['Souscriptions', 'Désabonnements'],
                datasets: [{
                    data: [currentMonthSubs, currentMonthUnsubs],
                    backgroundColor: ['#6f42c1', '#ff6b35'],
                    borderWidth: 0,
                    cutout: '70%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false
                    }
                }
            }
        });

        // Affichage simple des données office (sans graphique pour éviter le crash)
        console.log('=== AFFICHAGE SIMPLE OFFICE ===');
        const officeData = @json($office_data ?? []);
        console.log('Données office reçues:', officeData);

        if (officeData.length > 0) {
            // Insérer le contenu dans le nouveau conteneur avec scroll
            const officeContent = document.getElementById('officeContent');
            officeContent.innerHTML = officeData.map(item => `
                <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                    <span class="fw-bold">${item.office_name || 'N/A'}</span>
                    <span class="badge bg-primary fs-6">${item.total_transactions}</span>
                </div>
            `).join('');

            console.log('Affichage simple créé avec succès !');
        } else {
            const officeContent = document.getElementById('officeContent');
            officeContent.innerHTML = '<p class="text-muted text-center">Aucune donnée office trouvée</p>';
            console.log('AUCUNE DONNÉE OFFICE TROUVÉE !');
        }

        // Mini graphique en anneau pour les transactions (Dépôts vs Retraits)
        const transactionsDonutCtx = document.getElementById('transactionsDonut').getContext('2d');
        const deposits = @json($current_month_deposits ?? 0);
        const withdrawals = @json($current_month_withdrawals ?? 0);

        new Chart(transactionsDonutCtx, {
            type: 'doughnut',
            data: {
                labels: ['Dépôts', 'Retraits'],
                datasets: [{
                    data: [deposits, withdrawals],
                    backgroundColor: ['#ff6b35', '#6f42c1'],
                    borderWidth: 0,
                    cutout: '70%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: true
                    }
                }
            }
        });
    });
</script>
@endsection