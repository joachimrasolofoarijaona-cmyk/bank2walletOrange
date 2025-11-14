@extends('layouts.sidebar')

@section('title', ':: Accueil ::')

@section('content')
<style>
    :root {
        --primary-color: #02564A;
        --accent-color: #4FC9C0;
        --bg-light: #F8F9FA;
        --text-primary: #212529;
        --text-secondary: #6C757D;
        --border-color: #E9ECEF;
        --card-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        --card-shadow-hover: 0 4px 16px rgba(0, 0, 0, 0.08);
    }

    body {
        background-color: var(--bg-light);
        font-family: 'Poppins', sans-serif;
    }

    /* Header Section */
    .page-header {
        background: white;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: var(--card-shadow);
        border: 1px solid var(--border-color);
    }

    .page-title {
        font-size: 28px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    .page-subtitle {
        font-size: 14px;
        color: var(--text-secondary);
        margin-top: 4px;
    }

    /* Metric Cards - Style moderne et sobre */
    .metric-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        border: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .metric-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--primary-color);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .metric-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--card-shadow-hover);
        border-color: var(--accent-color);
    }

    .metric-card:hover::before {
        opacity: 1;
    }

    .metric-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
        color: white;
        font-size: 24px;
    }

    .metric-value {
        font-size: 32px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        line-height: 1.2;
    }

    .metric-label {
        font-size: 14px;
        color: var(--text-secondary);
        margin-top: 4px;
        font-weight: 500;
    }

    .metric-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        margin-top: 8px;
    }

    .metric-badge.success {
        background: rgba(25, 135, 84, 0.1);
        color: #198754;
        border: 1px solid rgba(25, 135, 84, 0.2);
    }

    .metric-badge.danger {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.2);
    }

    /* Chart Cards */
    .chart-card {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
        overflow: hidden;
        height: 100%;
    }

    .chart-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color);
        background: white;
    }

    .chart-card-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    .chart-card-body {
        padding: 24px;
    }

    .chart-container {
        position: relative;
        height: 300px;
    }

    /* Content Cards */
    .content-card {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .content-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color);
        background: white;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .content-card-header i {
        color: var(--primary-color);
        font-size: 20px;
    }

    .content-card-header span {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-primary);
    }

    .content-card-body {
        padding: 20px 24px;
        flex: 1;
        overflow-y: auto;
    }

    /* Summary Card */
    .summary-card {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
        padding: 24px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .summary-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 20px;
        text-align: center;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid var(--border-color);
    }

    .summary-item:last-child {
        border-bottom: none;
    }

    .summary-label {
        font-size: 14px;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .summary-value {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-primary);
    }

    .summary-total {
        font-size: 20px;
        font-weight: 700;
        color: var(--primary-color);
    }

    /* Dark Card */
    .dark-card {
        background: linear-gradient(135deg, var(--primary-color) 0%, #033d35 100%);
        border-radius: 12px;
        border: none;
        box-shadow: var(--card-shadow-hover);
        padding: 24px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        color: white;
    }

    .dark-card-title {
        font-size: 16px;
        font-weight: 600;
        color: white;
        margin-bottom: 16px;
        text-align: center;
    }

    .dark-card-value {
        font-size: 36px;
        font-weight: 700;
        color: var(--accent-color);
        text-align: center;
        margin-bottom: 8px;
    }

    .dark-card-label {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.7);
        text-align: center;
    }

    /* Buttons - Outline Style */
    .btn-outline-primary {
        border: 2px solid var(--primary-color);
        color: var(--primary-color);
        background: transparent;
        font-weight: 500;
        padding: 8px 20px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .btn-outline-primary:hover {
        background: var(--primary-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(2, 86, 74, 0.3);
    }

    .btn-outline-accent {
        border: 2px solid var(--accent-color);
        color: var(--accent-color);
        background: transparent;
        font-weight: 500;
        padding: 8px 20px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .btn-outline-accent:hover {
        background: var(--accent-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(79, 201, 192, 0.3);
    }

    /* List Items */
    .list-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 16px;
        margin-bottom: 8px;
        background: var(--bg-light);
        border-radius: 8px;
        border: 1px solid var(--border-color);
        transition: all 0.2s ease;
    }

    .list-item:hover {
        background: white;
        border-color: var(--accent-color);
        transform: translateX(4px);
    }

    .list-item-label {
        font-size: 14px;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .list-item-badge {
        background: var(--primary-color);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-title {
            font-size: 24px;
        }

        .metric-value {
            font-size: 28px;
        }

        .chart-container {
            height: 250px;
        }
    }

    /* Spacing */
    .section-spacing {
        margin-bottom: 24px;
    }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Tableau de bord</h1>
        <p class="page-subtitle">Vue d'ensemble de vos statistiques et activités</p>
    </div>

    <!-- Metrics Row -->
    <div class="row section-spacing">
        <div class="col-lg-3 col-md-6 col-xs-12 mb-4">
            <div class="metric-card">
                <div class="metric-icon">
                    <i class="ri-login-circle-line"></i>
                </div>
                <h3 class="metric-value">{{ number_format($total_subscriptions) }}</h3>
                <p class="metric-label">Souscriptions</p>
                <span class="metric-badge success">
                    <i class="ri-arrow-up-line"></i>
                    +{{ $daily_subscriptions }} aujourd'hui
                </span>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-xs-12 mb-4">
            <div class="metric-card">
                <div class="metric-icon">
                    <i class="ri-file-list-line"></i>
                </div>
                <h3 class="metric-value">{{ number_format($total_balance) }}</h3>
                <p class="metric-label">Mini Relevés</p>
                <span class="metric-badge success">
                    <i class="ri-arrow-up-line"></i>
                    +{{ number_format($daily_balance) }} aujourd'hui
                </span>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-xs-12 mb-4">
            <div class="metric-card">
                <div class="metric-icon">
                    <i class="ri-logout-circle-line"></i>
                </div>
                <h3 class="metric-value">{{ number_format($total_unsubscriptions) }}</h3>
                <p class="metric-label">Désabonnements</p>
                <span class="metric-badge danger">
                    <i class="ri-arrow-up-line"></i>
                    +{{ $daily_unsubscriptions }} aujourd'hui
                </span>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-xs-12 mb-4">
            <div class="metric-card">
                <div class="metric-icon">
                    <i class="ri-eye-line"></i>
                </div>
                <h3 class="metric-value">{{ number_format($total_transactions) }}</h3>
                <p class="metric-label">Consultations Solde</p>
                <span class="metric-badge success">
                    <i class="ri-arrow-up-line"></i>
                    +{{ $daily_transactions }} aujourd'hui
                </span>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row section-spacing">
        <div class="col-lg-6 col-md-6 col-xs-12 mb-4">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h6 class="chart-card-title">Souscription vs. Mois</h6>
                </div>
                <div class="chart-card-body">
                    <div class="chart-container">
                        <canvas id="monthly_subs"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-xs-12 mb-4">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h6 class="chart-card-title">Transaction vs. Mois</h6>
                </div>
                <div class="chart-card-body">
                    <div class="chart-container">
                        <canvas id="monthly_transactions"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Sections Row -->
    <div class="row section-spacing">
        <div class="col-lg-3 col-md-6 col-xs-12 mb-4">
            <div class="content-card">
                <div class="content-card-header">
                    <i class="ri-building-line"></i>
                    <span>Transactions par Office</span>
                </div>
                <div class="content-card-body">
                    <div id="officeContent">
                        <!-- Le contenu sera inséré ici par JavaScript -->
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-xs-12 mb-4">
            <div class="content-card">
                <div class="content-card-header">
                    <i class="ri-list-check"></i>
                    <span>Top Libellés</span>
                </div>
                <div class="content-card-body">
                    @if(isset($account_names) && count($account_names) > 0)
                        @foreach($account_names->take(8) as $libelle)
                        <div class="list-item">
                            <span class="list-item-label">{{ $libelle->libelle ?? 'N/A' }}</span>
                            <span class="list-item-badge">{{ $libelle->transaction_count }}</span>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">Aucun libellé trouvé</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-xs-12 mb-4">
            <div class="summary-card">
                <h6 class="summary-title">Résumé des Charges</h6>
                <div>
                    <div class="summary-item">
                        <span class="summary-label">Dépôts (W2A)</span>
                        <span class="summary-value" style="color: #ff6b35;">{{ number_format($current_month_deposits ?? 0) }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Retraits (A2W)</span>
                        <span class="summary-value" style="color: #6f42c1;">{{ number_format($current_month_withdrawals ?? 0) }}</span>
                    </div>
                    <hr style="margin: 16px 0; border-color: var(--border-color);">
                    <div class="summary-item">
                        <span class="summary-label">Total Charges</span>
                        <span class="summary-total">{{ number_format($current_month_charges ?? 0) }} MGA</span>
                    </div>
                </div>
                <div class="mt-4" style="width: 140px; height: 140px; margin: 0 auto;">
                    <canvas id="miniDonut"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-xs-12 mb-4">
            <div class="dark-card">
                <h6 class="dark-card-title">Total Transactions</h6>
                <div>
                    <h2 class="dark-card-value">{{ number_format($total_all_transactions ?? 0) }}</h2>
                    <p class="dark-card-label">Toutes les transactions</p>
                </div>
                <div style="width: 140px; height: 140px; margin: 0 auto;">
                    <canvas id="transactionsDonut"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Données pour le graphique principal
        const monthlyData = @json($monthly_data);
        const labels = monthlyData.map(item => item.month);
        const subscriptionsData = monthlyData.map(item => item.subscriptions);
        const unsubscriptionsData = monthlyData.map(item => item.unsubscriptions);

        // Configuration du graphique Souscriptions
        const ctx = document.getElementById('monthly_subs').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Souscriptions',
                    data: subscriptionsData,
                    backgroundColor: '#02564A',
                    borderColor: '#02564A',
                    borderWidth: 2,
                    borderRadius: 6,
                },
                {
                    label: 'Désabonnements',
                    data: unsubscriptionsData,
                    backgroundColor: '#4FC9C0',
                    borderColor: '#4FC9C0',
                    borderWidth: 2,
                    borderRadius: 6,
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
                                size: 12,
                                family: 'Poppins'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#02564A',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 12,
                                family: 'Poppins'
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12,
                                family: 'Poppins'
                            }
                        }
                    }
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
                    backgroundColor: '#4FC9C0',
                    borderColor: '#02564A',
                    borderWidth: 2,
                    borderRadius: 6,
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
                                size: 12,
                                family: 'Poppins'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#4FC9C0',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 12,
                                family: 'Poppins'
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12,
                                family: 'Poppins'
                            }
                        }
                    }
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
                    backgroundColor: ['#02564A', '#4FC9C0'],
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
                        enabled: true,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 8,
                        cornerRadius: 6
                    }
                }
            }
        });

        // Affichage des données office
        const officeData = @json($office_data ?? []);
        const officeContent = document.getElementById('officeContent');

        if (officeData.length > 0) {
            officeContent.innerHTML = officeData.map(item => `
                <div class="list-item">
                    <span class="list-item-label">${item.office_name || 'N/A'}</span>
                    <span class="list-item-badge">${item.total_transactions}</span>
                </div>
            `).join('');
        } else {
            officeContent.innerHTML = '<p class="text-muted text-center">Aucune donnée office trouvée</p>';
        }

        // Mini graphique en anneau pour les transactions
        const transactionsDonutCtx = document.getElementById('transactionsDonut').getContext('2d');
        const deposits = @json($current_month_deposits ?? 0);
        const withdrawals = @json($current_month_withdrawals ?? 0);

        new Chart(transactionsDonutCtx, {
            type: 'doughnut',
            data: {
                labels: ['Dépôts', 'Retraits'],
                datasets: [{
                    data: [deposits, withdrawals],
                    backgroundColor: ['#4FC9C0', '#ff6b35'],
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
                        enabled: true,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 8,
                        cornerRadius: 6,
                        bodyColor: '#fff'
                    }
                }
            }
        });
    });
</script>
@endsection
