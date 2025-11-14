<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->input('period', 'month'); // day|week|month|range
        $office = $request->input('office');
        $type = $request->input('type'); // A2W|W2A
        $start = $request->input('start');
        $end = $request->input('end');

        // Détermination de la plage selon le preset
        if ($period === 'all') {
            $startDate = Carbon::minValue();
            $endDate = Carbon::maxValue();
        } elseif ($period === 'day') {
            $startDate = Carbon::today()->startOfDay();
            $endDate = Carbon::today()->endOfDay();
        } elseif ($period === 'week') {
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
        } elseif ($period === 'range' && $start && $end) {
            $startDate = Carbon::parse($start)->startOfDay();
            $endDate = Carbon::parse($end)->endOfDay();
        } else { // month par défaut
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
            $period = 'month';
        }

        // KPIs simples
        $transactionsQuery = DB::table('transaction')->whereBetween('created_at', [$startDate, $endDate]);
        if ($office) {
            $transactionsQuery->where('office_name', $office);
        }
        if ($type) {
            $transactionsQuery->where('request_type', $type);
        }

        $subsKpiQ = DB::table('subscription')
            ->where('account_status', 1)
            ->whereBetween('created_at', [$startDate, $endDate]);
        if ($office && Schema::hasColumn('subscription', 'office_name')) {
            $subsKpiQ->where('office_name', $office);
        }

        $unsubsKpiQ = DB::table('unsubscription')
            ->whereBetween('created_at', [$startDate, $endDate]);
        if ($office && Schema::hasColumn('unsubscription', 'office_name')) {
            $unsubsKpiQ->where('office_name', $office);
        }

        // KPIs pour les 4 types d'opérations
        $balanceQuery = DB::table('get_balance')->whereBetween('created_at', [$startDate, $endDate]);
        $miniStatementQuery = DB::table('mini_statement')->whereBetween('created_at', [$startDate, $endDate]);
        $depositsKpiQ = DB::table('transaction')->where('request_type', 'W2A')->whereBetween('created_at', [$startDate, $endDate]);
        $withdrawalsKpiQ = DB::table('transaction')->where('request_type', 'A2W')->whereBetween('created_at', [$startDate, $endDate]);
        
        if ($office) {
            if (Schema::hasColumn('get_balance', 'office_name')) {
                $balanceQuery->where('office_name', $office);
            }
            if (Schema::hasColumn('mini_statement', 'office_name')) {
                $miniStatementQuery->where('office_name', $office);
            }
            $depositsKpiQ->where('office_name', $office);
            $withdrawalsKpiQ->where('office_name', $office);
        }

        $kpis = [
            'subscriptions' => $subsKpiQ->count(),
            'unsubscriptions' => $unsubsKpiQ->count(),
            'transactions' => (clone $transactionsQuery)->count(),
            'charges' => (clone $transactionsQuery)->get()->sum(function ($t) {
                return floatval($t->charge) ?? 0;
            }),
            'balance_consultations' => $balanceQuery->count(),
            'mini_statements' => $miniStatementQuery->count(),
            'deposits_count' => $depositsKpiQ->count(),
            'withdrawals_count' => $withdrawalsKpiQ->count(),
        ];

        // Séries mensuelles 6 mois
        $series = [];
        // Séries pour les 4 types d'opérations (Consultation solde, Mini relevé, Retrait, Dépôt)
        $operationsSeries = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->format('M Y');

            // Transactions filtrées par office/type
            $monthlyTxQ = DB::table('transaction')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);
            if ($office) {
                $monthlyTxQ->where('office_name', $office);
            }
            if ($type) {
                $monthlyTxQ->where('request_type', $type);
            }

            $monthlySubsQ = DB::table('subscription')
                ->where('account_status', 1)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);
            if ($office && Schema::hasColumn('subscription', 'office_name')) {
                $monthlySubsQ->where('office_name', $office);
            }

            $monthlyUnsubsQ = DB::table('unsubscription')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);
            if ($office && Schema::hasColumn('unsubscription', 'office_name')) {
                $monthlyUnsubsQ->where('office_name', $office);
            }

            // Séries pour les 4 types d'opérations
            $monthlyBalanceQ = DB::table('get_balance')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);
            if ($office && Schema::hasColumn('get_balance', 'office_name')) {
                $monthlyBalanceQ->where('office_name', $office);
            }

            $monthlyMiniStatementQ = DB::table('mini_statement')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);
            if ($office && Schema::hasColumn('mini_statement', 'office_name')) {
                $monthlyMiniStatementQ->where('office_name', $office);
            }

            $monthlyDepositsQ = DB::table('transaction')
                ->where('request_type', 'W2A')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);
            if ($office) {
                $monthlyDepositsQ->where('office_name', $office);
            }

            $monthlyWithdrawalsQ = DB::table('transaction')
                ->where('request_type', 'A2W')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);
            if ($office) {
                $monthlyWithdrawalsQ->where('office_name', $office);
            }

            $series[] = [
                'month' => $month,
                'subscriptions' => $monthlySubsQ->count(),
                'unsubscriptions' => $monthlyUnsubsQ->count(),
                'transactions' => $monthlyTxQ->count(),
            ];

            $operationsSeries[] = [
                'month' => $month,
                'balance_consultations' => $monthlyBalanceQ->count(),
                'mini_statements' => $monthlyMiniStatementQ->count(),
                'deposits' => $monthlyDepositsQ->count(),
                'withdrawals' => $monthlyWithdrawalsQ->count(),
            ];
        }

        // Donut Dépôts (W2A) vs Retraits (A2W)
        $depositsQ = DB::table('transaction')->where('request_type', 'W2A')->whereBetween('created_at', [$startDate, $endDate]);
        $withdrawalsQ = DB::table('transaction')->where('request_type', 'A2W')->whereBetween('created_at', [$startDate, $endDate]);
        if ($office) {
            $depositsQ->where('office_name', $office);
            $withdrawalsQ->where('office_name', $office);
        }
        $deposits = $depositsQ->count();
        $withdrawals = $withdrawalsQ->count();

        $startStr = $startDate->toDateString();
        $endStr = $endDate->toDateString();

        // Options de filtre (offices distincts)
        $offices = DB::table('transaction')->whereNotNull('office_name')->distinct()->pluck('office_name');

        // Séries Charges vs. Mois (filtré)
        $monthlyCharges = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->format('M Y');
            $chargesQ = DB::table('transaction')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);
            if ($office) {
                $chargesQ->where('office_name', $office);
            }
            if ($type) {
                $chargesQ->where('request_type', $type);
            }
            $sumCharges = $chargesQ->get()->sum(function ($t) {
                return floatval($t->charge) ?? 0;
            });
            $monthlyCharges[] = ['month' => $month, 'charges' => $sumCharges];
        }

        // Séries Volumes MGA vs. Mois si colonne présente (amount ou montant)
        $hasAmount = Schema::hasColumn('transaction', 'amount') || Schema::hasColumn('transaction', 'montant');
        $monthlyAmounts = [];
        $monthlyAmountsSplit = [];
        if ($hasAmount) {
            $amountColumn = Schema::hasColumn('transaction', 'amount') ? 'amount' : 'montant';
            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $month = $date->format('M Y');
                $amountQ = DB::table('transaction')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month);
                if ($office) {
                    $amountQ->where('office_name', $office);
                }
                if ($type) {
                    $amountQ->where('request_type', $type);
                }
                $sumAmount = $amountQ->sum($amountColumn);
                $monthlyAmounts[] = ['month' => $month, 'amount' => floatval($sumAmount)];

                // Split Dépôts (W2A) vs Retraits (A2W) pour le même mois
                $baseQ = DB::table('transaction')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month);
                if ($office) {
                    $baseQ->where('office_name', $office);
                }
                // Ne pas appliquer le filtre type ici afin d'afficher les deux séries

                $depositsSum = (clone $baseQ)->where('request_type', 'W2A')->sum($amountColumn);
                $withdrawalsSum = (clone $baseQ)->where('request_type', 'A2W')->sum($amountColumn);
                $monthlyAmountsSplit[] = [
                    'month' => $month,
                    'deposits' => floatval($depositsSum),
                    'withdrawals' => floatval($withdrawalsSum),
                ];
            }
        }

        // Heatmap Jour x Heure (sur la plage sélectionnée)
        $heatQuery = DB::table('transaction')
            ->selectRaw('DAYOFWEEK(created_at) as dow, HOUR(created_at) as hr, COUNT(*) as cnt')
            ->whereBetween('created_at', [$startDate, $endDate]);
        if ($office) {
            $heatQuery->where('office_name', $office);
        }
        if ($type) {
            $heatQuery->where('request_type', $type);
        }
        $heatRows = $heatQuery->groupBy('dow', 'hr')->get();

        // Init matrice 7x24 à 0
        $heatmap = [];
        for ($d = 1; $d <= 7; $d++) { // 1:Dimanche ... 7:Samedi
            $row = array_fill(0, 24, 0);
            $heatmap[$d] = $row;
        }
        $heatMax = 0;
        foreach ($heatRows as $r) {
            $heatmap[$r->dow][(int)$r->hr] = (int)$r->cnt;
            if ((int)$r->cnt > $heatMax) {
                $heatMax = (int)$r->cnt;
            }
        }

        // Top lists (période/office/type filtrés)
        $topOfficesCount = DB::table('transaction')
            ->selectRaw('office_name, COUNT(*) as total')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($office, fn($q) => $q->where('office_name', $office))
            ->when($type, fn($q) => $q->where('request_type', $type))
            ->whereNotNull('office_name')
            ->groupBy('office_name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $topOfficesAmount = collect();
        if ($hasAmount) {
            $amountColumn = Schema::hasColumn('transaction', 'amount') ? 'amount' : 'montant';
            $topOfficesAmount = DB::table('transaction')
                ->selectRaw('office_name, SUM(COALESCE(' . $amountColumn . ',0)) as total_amount')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->when($office, fn($q) => $q->where('office_name', $office))
                ->when($type, fn($q) => $q->where('request_type', $type))
                ->whereNotNull('office_name')
                ->groupBy('office_name')
                ->orderByDesc('total_amount')
                ->limit(10)
                ->get();
        }

        $topAgents = collect();
        if (Schema::hasColumn('transaction', 'agent_name')) {
            $topAgents = DB::table('transaction')
                ->selectRaw('agent_name, COUNT(*) as total')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->when($office, fn($q) => $q->where('office_name', $office))
                ->when($type, fn($q) => $q->where('request_type', $type))
                ->whereNotNull('agent_name')
                ->groupBy('agent_name')
                ->orderByDesc('total')
                ->limit(10)
                ->get();
        }

        $topLibelles = collect();
        if (Schema::hasColumn('transaction', 'libelle')) {
            $topLibelles = DB::table('transaction')
                ->selectRaw('libelle, COUNT(*) as total')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->when($office, fn($q) => $q->where('office_name', $office))
                ->when($type, fn($q) => $q->where('request_type', $type))
                ->whereNotNull('libelle')
                ->groupBy('libelle')
                ->orderByDesc('total')
                ->limit(10)
                ->get();
        }

        return view('analytics', compact(
            'kpis',
            'series',
            'operationsSeries',
            'period',
            'startStr',
            'endStr',
            'deposits',
            'withdrawals',
            'office',
            'type',
            'offices',
            'monthlyCharges',
            'hasAmount',
            'monthlyAmounts',
            'monthlyAmountsSplit',
            'heatmap',
            'heatMax',
            'topOfficesCount',
            'topOfficesAmount',
            'topAgents',
            'topLibelles'
        ));
    }

    public function export(Request $request)
    {
        $period = $request->input('period', 'month');
        $office = $request->input('office');
        $type = $request->input('type');
        $start = $request->input('start');
        $end = $request->input('end');

        // Déterminer la plage
        if ($period === 'day') {
            $startDate = Carbon::today()->startOfDay();
            $endDate = Carbon::today()->endOfDay();
        } elseif ($period === 'week') {
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
        } elseif ($period === 'range' && $start && $end) {
            $startDate = Carbon::parse($start)->startOfDay();
            $endDate = Carbon::parse($end)->endOfDay();
        } else {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        }

        $q = DB::table('transaction')->whereBetween('created_at', [$startDate, $endDate]);
        if ($office) {
            $q->where('office_name', $office);
        }
        if ($type) {
            $q->where('request_type', $type);
        }

        // Sélectionner toutes les colonnes disponibles
        $selectColumns = ['created_at', 'office_name', 'request_type', 'charge'];
        
        // Ajouter les colonnes optionnelles si elles existent
        if (Schema::hasColumn('transaction', 'msisdn')) {
            $selectColumns[] = 'msisdn';
        }
        if (Schema::hasColumn('transaction', 'account_no')) {
            $selectColumns[] = 'account_no';
        } elseif (Schema::hasColumn('transaction', 'musoni_account_no')) {
            $selectColumns[] = 'musoni_account_no as account_no';
        }
        if (Schema::hasColumn('transaction', 'libelle')) {
            $selectColumns[] = 'libelle';
        }
        if (Schema::hasColumn('transaction', 'client_lastname')) {
            $selectColumns[] = 'client_lastname';
        }
        if (Schema::hasColumn('transaction', 'client_firstname')) {
            $selectColumns[] = 'client_firstname';
        }
        if (Schema::hasColumn('transaction', 'agent_name')) {
            $selectColumns[] = 'agent_name';
        }
        if (Schema::hasColumn('transaction', 'operator_code')) {
            $selectColumns[] = 'operator_code';
        }
        if (Schema::hasColumn('transaction', 'request_id')) {
            $selectColumns[] = 'request_id';
        }
        if (Schema::hasColumn('transaction', 'response_code')) {
            $selectColumns[] = 'response_code';
        }
        if (Schema::hasColumn('transaction', 'response_message')) {
            $selectColumns[] = 'response_message';
        }
        
        $hasAmount = Schema::hasColumn('transaction', 'amount') || Schema::hasColumn('transaction', 'montant');
        if ($hasAmount) {
            $amountColumn = Schema::hasColumn('transaction', 'amount') ? 'amount' : 'montant';
            $selectColumns[] = DB::raw($amountColumn . ' as amount');
        }

        $rows = $q->select($selectColumns)->get();

        $filename = 'analytics_export_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            // Entêtes avec toutes les colonnes disponibles
            $headerRow = [];
            if (count($rows) > 0) {
                $firstRow = $rows[0];
                if (property_exists($firstRow, 'created_at')) $headerRow[] = 'Date';
                if (property_exists($firstRow, 'office_name')) $headerRow[] = 'Office';
                if (property_exists($firstRow, 'request_type')) $headerRow[] = 'Type requête';
                if (property_exists($firstRow, 'msisdn')) $headerRow[] = 'MSISDN';
                if (property_exists($firstRow, 'account_no')) $headerRow[] = 'Numéro de compte';
                if (property_exists($firstRow, 'libelle')) $headerRow[] = 'Libellé';
                if (property_exists($firstRow, 'client_lastname')) $headerRow[] = 'Nom client';
                if (property_exists($firstRow, 'client_firstname')) $headerRow[] = 'Prénom client';
                if (property_exists($firstRow, 'operator_code')) $headerRow[] = 'Code opérateur';
                if (property_exists($firstRow, 'request_id')) $headerRow[] = 'ID Requête';
                if (property_exists($firstRow, 'response_code')) $headerRow[] = 'Code réponse';
                if (property_exists($firstRow, 'response_message')) $headerRow[] = 'Message réponse';
                if (property_exists($firstRow, 'charge')) $headerRow[] = 'Charge';
                if (property_exists($firstRow, 'amount')) $headerRow[] = 'Montant';
                if (property_exists($firstRow, 'agent_name')) $headerRow[] = 'Agent';
            }
            fputcsv($out, $headerRow);
            
            foreach ($rows as $r) {
                $line = [];
                if (property_exists($r, 'created_at')) $line[] = $r->created_at ?? '';
                if (property_exists($r, 'office_name')) $line[] = $r->office_name ?? '';
                if (property_exists($r, 'request_type')) $line[] = $r->request_type ?? '';
                if (property_exists($r, 'msisdn')) $line[] = $r->msisdn ?? '';
                if (property_exists($r, 'account_no')) $line[] = $r->account_no ?? '';
                if (property_exists($r, 'libelle')) $line[] = $r->libelle ?? '';
                if (property_exists($r, 'client_lastname')) $line[] = $r->client_lastname ?? '';
                if (property_exists($r, 'client_firstname')) $line[] = $r->client_firstname ?? '';
                if (property_exists($r, 'operator_code')) $line[] = $r->operator_code ?? '';
                if (property_exists($r, 'request_id')) $line[] = $r->request_id ?? '';
                if (property_exists($r, 'response_code')) $line[] = $r->response_code ?? '';
                if (property_exists($r, 'response_message')) $line[] = $r->response_message ?? '';
                if (property_exists($r, 'charge')) $line[] = $r->charge ?? '';
                if (property_exists($r, 'amount')) $line[] = $r->amount ?? '';
                if (property_exists($r, 'agent_name')) $line[] = $r->agent_name ?? '';
                fputcsv($out, $line);
            }
            fclose($out);
        };
        # log 
        logActivity(
            session('username'),
            'exports',
            'export_data_global',
        );

        return response()->stream($callback, 200, $headers);
    }

    private function resolveDateRange(Request $request): array
    {
        $period = $request->input('period', 'month');
        $start = $request->input('start');
        $end = $request->input('end');
        if ($period === 'all') {
            return [Carbon::minValue(), Carbon::maxValue()];
        } elseif ($period === 'day') {
            return [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()];
        } elseif ($period === 'week') {
            return [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()];
        } elseif ($period === 'range' && $start && $end) {
            return [Carbon::parse($start)->startOfDay(), Carbon::parse($end)->endOfDay()];
        }
        return [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()];
    }

    public function exportTransactionsSeries(Request $request)
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);
        $office = $request->input('office');
        $type = $request->input('type');

        $q = DB::table('transaction')
            ->selectRaw("YEAR(created_at) as y, MONTH(created_at) as m, COUNT(*) as transactions")
            ->whereBetween('created_at', [$startDate, $endDate]);
        if ($office) {
            $q->where('office_name', $office);
        }
        if ($type) {
            $q->where('request_type', $type);
        }
        $rows = $q->groupBy('y', 'm')
            ->orderBy('y')->orderBy('m')
            ->get()
            ->map(fn($r) => ['month' => \Carbon\Carbon::create($r->y, $r->m, 1)->format('M Y'), 'transactions' => (int) $r->transactions])
            ->toArray();

        # log 
        logActivity(
            session('username'),
            'exports',
            'export_transaction_series',
        );

        $filename = 'transactions_series_' . now()->format('Ymd_His') . '.csv';
        return $this->streamSeriesCsv($rows, ['month', 'transactions'], $filename);
    }

    public function exportChargesSeries(Request $request)
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);
        $office = $request->input('office');
        $type = $request->input('type');

        $q = DB::table('transaction')
            ->selectRaw("YEAR(created_at) as y, MONTH(created_at) as m, SUM(COALESCE(charge,0)) as charges")
            ->whereBetween('created_at', [$startDate, $endDate]);
        if ($office) {
            $q->where('office_name', $office);
        }
        if ($type) {
            $q->where('request_type', $type);
        }
        $rows = $q->groupBy('y', 'm')
            ->orderBy('y')->orderBy('m')
            ->get()
            ->map(fn($r) => ['month' => \Carbon\Carbon::create($r->y, $r->m, 1)->format('M Y'), 'charges' => (float) $r->charges])
            ->toArray();

        # log 
        logActivity(
            session('username'),
            'exports',
            'export_charges_series',
        );

        $filename = 'charges_series_' . now()->format('Ymd_His') . '.csv';
        return $this->streamSeriesCsv($rows, ['month', 'charges'], $filename);
    }

    public function exportAmountsSeries(Request $request)
    {
        $hasAmount = Schema::hasColumn('transaction', 'amount') || Schema::hasColumn('transaction', 'montant');
        if (!$hasAmount) {
            return response('Amount column not found', 404);
        }
        $amountColumn = Schema::hasColumn('transaction', 'amount') ? 'amount' : 'montant';

        $office = $request->input('office');
        $type = $request->input('type');
        [$startDate, $endDate] = $this->resolveDateRange($request);
        $q = DB::table('transaction')
            ->selectRaw("YEAR(created_at) as y, MONTH(created_at) as m, SUM(COALESCE($amountColumn,0)) as amount")
            ->whereBetween('created_at', [$startDate, $endDate]);
        if ($office) {
            $q->where('office_name', $office);
        }
        if ($type) {
            $q->where('request_type', $type);
        }
        $rows = $q->groupBy('y', 'm')
            ->orderBy('y')->orderBy('m')
            ->get()
            ->map(fn($r) => ['month' => \Carbon\Carbon::create($r->y, $r->m, 1)->format('M Y'), 'amount' => (float) $r->amount])
            ->toArray();

        # log 
        logActivity(
            session('username'),
            'exports',
            'export_amount_series',
        );

        $filename = 'amounts_series_' . now()->format('Ymd_His') . '.csv';
        return $this->streamSeriesCsv($rows, ['month', 'amount'], $filename);
    }

    private function streamSeriesCsv(array $rows, array $headersRow, string $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        $callback = function () use ($rows, $headersRow) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headersRow);
            foreach ($rows as $r) {
                fputcsv($out, array_values($r));
            }
            fclose($out);
        };

        # log 
        logActivity(
            session('username'),
            'stream',
            'stream_series_csv',
        );

        return response()->stream($callback, 200, $headers);
    }

    public function exportTopOfficesCount(Request $request)
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);
        $office = $request->input('office');
        $type = $request->input('type');
        $rows = DB::table('transaction')
            ->selectRaw('office_name, COUNT(*) as total')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($office, fn($q) => $q->where('office_name', $office))
            ->when($type, fn($q) => $q->where('request_type', $type))
            ->whereNotNull('office_name')
            ->groupBy('office_name')
            ->orderByDesc('total')
            ->limit(100)
            ->get()
            ->map(fn($r) => ['office_name' => $r->office_name, 'total' => $r->total])->toArray();

        # log 
        logActivity(
            session('username'),
            'exports',
            'export_top_office_count',
        );
        return $this->streamSeriesCsv($rows, ['office_name', 'total'], 'top_offices_count_' . now()->format('Ymd_His') . '.csv');
    }

    public function exportTopOfficesAmount(Request $request)
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);
        $office = $request->input('office');
        $type = $request->input('type');
        $hasAmount = Schema::hasColumn('transaction', 'amount') || Schema::hasColumn('transaction', 'montant');
        if (!$hasAmount) return response('Amount column not found', 404);
        $amountColumn = Schema::hasColumn('transaction', 'amount') ? 'amount' : 'montant';
        $rows = DB::table('transaction')
            ->selectRaw('office_name, SUM(COALESCE(' . $amountColumn . ',0)) as total_amount')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($office, fn($q) => $q->where('office_name', $office))
            ->when($type, fn($q) => $q->where('request_type', $type))
            ->whereNotNull('office_name')
            ->groupBy('office_name')
            ->orderByDesc('total_amount')
            ->limit(100)
            ->get()
            ->map(fn($r) => ['office_name' => $r->office_name, 'total_amount' => $r->total_amount])->toArray();

        # log 
        logActivity(
            session('username'),
            'exports',
            'export_top_office_amount',
        );

        return $this->streamSeriesCsv($rows, ['office_name', 'total_amount'], 'top_offices_amount_' . now()->format('Ymd_His') . '.csv');
    }

    public function exportTopLibelles(Request $request)
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);
        $office = $request->input('office');
        $type = $request->input('type');
        if (!Schema::hasColumn('transaction', 'libelle')) return response('libelle column not found', 404);
        $rows = DB::table('transaction')
            ->selectRaw('libelle, COUNT(*) as total')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($office, fn($q) => $q->where('office_name', $office))
            ->when($type, fn($q) => $q->where('request_type', $type))
            ->whereNotNull('libelle')
            ->groupBy('libelle')
            ->orderByDesc('total')
            ->limit(100)
            ->get()
            ->map(fn($r) => ['libelle' => $r->libelle, 'total' => $r->total])->toArray();

        # log 
        logActivity(
            session('username'),
            'exports',
            'export_top_libelles',
        );

        return $this->streamSeriesCsv($rows, ['libelle', 'total'], 'top_libelles_' . now()->format('Ymd_His') . '.csv');
    }

    public function exportOperations(Request $request)
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);
        $office = $request->input('office');
        $operationType = $request->input('operation_type'); // balance, mini_statement, deposit, withdrawal

        $rows = [];

        // Consultation de solde
        if (!$operationType || $operationType === 'balance') {
            $balanceQ = DB::table('get_balance')->whereBetween('created_at', [$startDate, $endDate]);
            if ($office && Schema::hasColumn('get_balance', 'office_name')) {
                $balanceQ->where('office_name', $office);
            }
            $balanceRows = $balanceQ->get();
            foreach ($balanceRows as $row) {
                $rows[] = [
                    'date' => $row->created_at ?? $row->transaction_date ?? '',
                    'type_operation' => 'Consultation de solde',
                    'office_name' => $row->office_name ?? $row->officeName ?? '',
                    'msisdn' => $row->msisdn ?? '',
                    'account_no' => $row->musoni_account_no ?? $row->account_no ?? '',
                    'libelle' => $row->libelle ?? '',
                    'client_lastname' => $row->client_lastname ?? '',
                    'client_firstname' => $row->client_firstname ?? '',
                    'operator_code' => $row->operator_code ?? '',
                    'request_id' => $row->request_id ?? '',
                    'response_code' => $row->acep_responde_code ?? $row->response_code ?? '',
                    'response_message' => $row->acep_responde_message ?? $row->response_message ?? '',
                ];
            }
        }

        // Mini relevé
        if (!$operationType || $operationType === 'mini_statement') {
            $miniQ = DB::table('mini_statement')->whereBetween('created_at', [$startDate, $endDate]);
            if ($office && Schema::hasColumn('mini_statement', 'office_name')) {
                $miniQ->where('office_name', $office);
            }
            $miniRows = $miniQ->get();
            foreach ($miniRows as $row) {
                $rows[] = [
                    'date' => $row->created_at ?? $row->transaction_date ?? '',
                    'type_operation' => 'Mini relevé',
                    'office_name' => $row->office_name ?? $row->officeName ?? '',
                    'msisdn' => $row->msisdn ?? '',
                    'account_no' => $row->musoni_account_no ?? $row->account_no ?? '',
                    'libelle' => $row->libelle ?? '',
                    'client_lastname' => $row->client_lastname ?? '',
                    'client_firstname' => $row->client_firstname ?? '',
                    'operator_code' => $row->operator_code ?? '',
                    'request_id' => $row->request_id ?? '',
                    'response_code' => $row->acep_responde_code ?? $row->response_code ?? '',
                    'response_message' => $row->acep_responde_message ?? $row->response_message ?? '',
                ];
            }
        }

        // Dépôts (W2A)
        if (!$operationType || $operationType === 'deposit') {
            $depositQ = DB::table('transaction')
                ->where('request_type', 'W2A')
                ->whereBetween('created_at', [$startDate, $endDate]);
            if ($office) {
                $depositQ->where('office_name', $office);
            }
            $depositRows = $depositQ->get();
            foreach ($depositRows as $row) {
                $hasAmount = Schema::hasColumn('transaction', 'amount') || Schema::hasColumn('transaction', 'montant');
                $amountColumn = $hasAmount ? (Schema::hasColumn('transaction', 'amount') ? 'amount' : 'montant') : null;
                $rows[] = [
                    'date' => $row->created_at ?? '',
                    'type_operation' => 'Dépôt',
                    'office_name' => $row->office_name ?? '',
                    'msisdn' => $row->msisdn ?? '',
                    'account_no' => $row->account_no ?? $row->musoni_account_no ?? '',
                    'libelle' => $row->libelle ?? '',
                    'client_lastname' => $row->client_lastname ?? '',
                    'client_firstname' => $row->client_firstname ?? '',
                    'charge' => $row->charge ?? '',
                    'amount' => $amountColumn ? ($row->$amountColumn ?? '') : '',
                    'request_type' => $row->request_type ?? 'W2A',
                    'agent_name' => $row->agent_name ?? '',
                ];
            }
        }

        // Retraits (A2W)
        if (!$operationType || $operationType === 'withdrawal') {
            $withdrawalQ = DB::table('transaction')
                ->where('request_type', 'A2W')
                ->whereBetween('created_at', [$startDate, $endDate]);
            if ($office) {
                $withdrawalQ->where('office_name', $office);
            }
            $withdrawalRows = $withdrawalQ->get();
            foreach ($withdrawalRows as $row) {
                $hasAmount = Schema::hasColumn('transaction', 'amount') || Schema::hasColumn('transaction', 'montant');
                $amountColumn = $hasAmount ? (Schema::hasColumn('transaction', 'amount') ? 'amount' : 'montant') : null;
                $rows[] = [
                    'date' => $row->created_at ?? '',
                    'type_operation' => 'Retrait',
                    'office_name' => $row->office_name ?? '',
                    'msisdn' => $row->msisdn ?? '',
                    'account_no' => $row->account_no ?? $row->musoni_account_no ?? '',
                    'libelle' => $row->libelle ?? '',
                    'client_lastname' => $row->client_lastname ?? '',
                    'client_firstname' => $row->client_firstname ?? '',
                    'charge' => $row->charge ?? '',
                    'amount' => $amountColumn ? ($row->$amountColumn ?? '') : '',
                    'request_type' => $row->request_type ?? 'A2W',
                    'agent_name' => $row->agent_name ?? '',
                ];
            }
        }

        // Trier par date décroissante
        usort($rows, function($a, $b) {
            return strcmp($b['date'], $a['date']);
        });

        $filename = 'operations_export_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            // En-têtes avec toutes les colonnes
            $headersRow = [
                'Date',
                'Type d\'opération',
                'Office',
                'MSISDN',
                'Numéro de compte',
                'Libellé',
                'Nom client',
                'Prénom client',
                'Code opérateur',
                'ID Requête',
                'Code réponse',
                'Message réponse',
                'Charge',
                'Montant',
                'Type requête',
                'Agent'
            ];
            fputcsv($out, $headersRow);
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r['date'] ?? '',
                    $r['type_operation'] ?? '',
                    $r['office_name'] ?? '',
                    $r['msisdn'] ?? '',
                    $r['account_no'] ?? '',
                    $r['libelle'] ?? '',
                    $r['client_lastname'] ?? '',
                    $r['client_firstname'] ?? '',
                    $r['operator_code'] ?? '',
                    $r['request_id'] ?? '',
                    $r['response_code'] ?? '',
                    $r['response_message'] ?? '',
                    $r['charge'] ?? '',
                    $r['amount'] ?? '',
                    $r['request_type'] ?? '',
                    $r['agent_name'] ?? '',
                ]);
            }
            fclose($out);
        };

        logActivity(
            session('username'),
            'exports',
            'export_operations',
        );

        return response()->stream($callback, 200, $headers);
    }

    public function exportOperationsSeries(Request $request)
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);
        $office = $request->input('office');

        $rows = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->format('M Y');

            $balanceQ = DB::table('get_balance')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);
            if ($office && Schema::hasColumn('get_balance', 'office_name')) {
                $balanceQ->where('office_name', $office);
            }

            $miniQ = DB::table('mini_statement')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);
            if ($office && Schema::hasColumn('mini_statement', 'office_name')) {
                $miniQ->where('office_name', $office);
            }

            $depositQ = DB::table('transaction')
                ->where('request_type', 'W2A')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);
            if ($office) {
                $depositQ->where('office_name', $office);
            }

            $withdrawalQ = DB::table('transaction')
                ->where('request_type', 'A2W')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);
            if ($office) {
                $withdrawalQ->where('office_name', $office);
            }

            $rows[] = [
                'month' => $month,
                'consultation_solde' => $balanceQ->count(),
                'mini_releve' => $miniQ->count(),
                'depot' => $depositQ->count(),
                'retrait' => $withdrawalQ->count(),
            ];
        }

        logActivity(
            session('username'),
            'exports',
            'export_operations_series',
        );

        $filename = 'operations_series_' . now()->format('Ymd_His') . '.csv';
        return $this->streamSeriesCsv($rows, ['month', 'consultation_solde', 'mini_releve', 'depot', 'retrait'], $filename);
    }

    public function transactionsList(Request $request)
    {
        $office = $request->input('office');
        $type = $request->input('type');
        $year = (int) $request->input('year');
        $month = (int) $request->input('month');
        $dow = $request->input('dow'); // 1..7
        $hr = $request->input('hr');   // 0..23

        // Déterminer la plage: priorité au mois si fourni, sinon période formulaire
        if ($year >= 2000 && $month >= 1 && $month <= 12) {
            $start = Carbon::create($year, $month, 1)->startOfMonth();
            $end = (clone $start)->endOfMonth();
        } else {
            [$start, $end] = $this->resolveDateRange($request);
        }

        $q = DB::table('transaction')
            ->whereBetween('created_at', [$start, $end]);
        if ($office) {
            $q->where('office_name', $office);
        }
        if ($request->filled('office_exact')) {
            $q->where('office_name', $request->input('office_exact'));
        }
        if ($type) {
            $q->where('request_type', $type);
        }
        if ($dow) {
            $q->whereRaw('DAYOFWEEK(created_at) = ?', [(int)$dow]);
        }
        if ($hr !== null && $hr !== '') {
            $q->whereRaw('HOUR(created_at) = ?', [(int)$hr]);
        }
        if ($request->filled('libelle') && Schema::hasColumn('transaction', 'libelle')) {
            $q->where('libelle', $request->input('libelle'));
        }
        if ($request->filled('agent') && Schema::hasColumn('transaction', 'agent_name')) {
            $q->where('agent_name', $request->input('agent'));
        }

        // Sélectionner toutes les colonnes disponibles
        $select = ['created_at', 'office_name', 'request_type', 'charge'];
        
        // Ajouter les colonnes optionnelles si elles existent
        if (Schema::hasColumn('transaction', 'msisdn')) {
            $select[] = 'msisdn';
        }
        if (Schema::hasColumn('transaction', 'account_no')) {
            $select[] = 'account_no';
        } elseif (Schema::hasColumn('transaction', 'musoni_account_no')) {
            $select[] = 'musoni_account_no as account_no';
        }
        if (Schema::hasColumn('transaction', 'libelle')) {
            $select[] = 'libelle';
        }
        if (Schema::hasColumn('transaction', 'client_lastname')) {
            $select[] = 'client_lastname';
        }
        if (Schema::hasColumn('transaction', 'client_firstname')) {
            $select[] = 'client_firstname';
        }
        if (Schema::hasColumn('transaction', 'agent_name')) {
            $select[] = 'agent_name';
        }
        if (Schema::hasColumn('transaction', 'operator_code')) {
            $select[] = 'operator_code';
        }
        if (Schema::hasColumn('transaction', 'request_id')) {
            $select[] = 'request_id';
        }
        if (Schema::hasColumn('transaction', 'response_code')) {
            $select[] = 'response_code';
        }
        if (Schema::hasColumn('transaction', 'response_message')) {
            $select[] = 'response_message';
        }
        
        $hasAmount = Schema::hasColumn('transaction', 'amount') || Schema::hasColumn('transaction', 'montant');
        if ($hasAmount) {
            $amountColumn = Schema::hasColumn('transaction', 'amount') ? 'amount' : 'montant';
            $select[] = DB::raw($amountColumn . ' as amount');
        }

        $rows = $q->orderBy('created_at', 'desc')->limit(500)->get($select);

        // Export direct
        if ($request->boolean('export')) {
            $filename = 'transactions_' . $year . '_' . str_pad($month, 2, '0', STR_PAD_LEFT) . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];
            $callback = function () use ($rows) {
                $out = fopen('php://output', 'w');
                // Entêtes avec toutes les colonnes disponibles
                $headerRow = [];
                if (count($rows) > 0) {
                    $firstRow = $rows[0];
                    if (property_exists($firstRow, 'created_at')) $headerRow[] = 'Date';
                    if (property_exists($firstRow, 'office_name')) $headerRow[] = 'Office';
                    if (property_exists($firstRow, 'request_type')) $headerRow[] = 'Type requête';
                    if (property_exists($firstRow, 'msisdn')) $headerRow[] = 'MSISDN';
                    if (property_exists($firstRow, 'account_no')) $headerRow[] = 'Numéro de compte';
                    if (property_exists($firstRow, 'libelle')) $headerRow[] = 'Libellé';
                    if (property_exists($firstRow, 'client_lastname')) $headerRow[] = 'Nom client';
                    if (property_exists($firstRow, 'client_firstname')) $headerRow[] = 'Prénom client';
                    if (property_exists($firstRow, 'operator_code')) $headerRow[] = 'Code opérateur';
                    if (property_exists($firstRow, 'request_id')) $headerRow[] = 'ID Requête';
                    if (property_exists($firstRow, 'response_code')) $headerRow[] = 'Code réponse';
                    if (property_exists($firstRow, 'response_message')) $headerRow[] = 'Message réponse';
                    if (property_exists($firstRow, 'charge')) $headerRow[] = 'Charge';
                    if (property_exists($firstRow, 'amount')) $headerRow[] = 'Montant';
                    if (property_exists($firstRow, 'agent_name')) $headerRow[] = 'Agent';
                }
                fputcsv($out, $headerRow);
                
                foreach ($rows as $r) {
                    $line = [];
                    if (property_exists($r, 'created_at')) $line[] = $r->created_at ?? '';
                    if (property_exists($r, 'office_name')) $line[] = $r->office_name ?? '';
                    if (property_exists($r, 'request_type')) $line[] = $r->request_type ?? '';
                    if (property_exists($r, 'msisdn')) $line[] = $r->msisdn ?? '';
                    if (property_exists($r, 'account_no')) $line[] = $r->account_no ?? '';
                    if (property_exists($r, 'libelle')) $line[] = $r->libelle ?? '';
                    if (property_exists($r, 'client_lastname')) $line[] = $r->client_lastname ?? '';
                    if (property_exists($r, 'client_firstname')) $line[] = $r->client_firstname ?? '';
                    if (property_exists($r, 'operator_code')) $line[] = $r->operator_code ?? '';
                    if (property_exists($r, 'request_id')) $line[] = $r->request_id ?? '';
                    if (property_exists($r, 'response_code')) $line[] = $r->response_code ?? '';
                    if (property_exists($r, 'response_message')) $line[] = $r->response_message ?? '';
                    if (property_exists($r, 'charge')) $line[] = $r->charge ?? '';
                    if (property_exists($r, 'amount')) $line[] = $r->amount ?? '';
                    if (property_exists($r, 'agent_name')) $line[] = $r->agent_name ?? '';
                    fputcsv($out, $line);
                }
                fclose($out);
            };

            return response()->stream($callback, 200, $headers);
        }

        return response()->json(['data' => $rows]);
    }
}
