<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log; // Added Log facade

class IndexController extends Controller
{
    public function showIndex(){
        try {
            # log 
            logActivity(
                session('username'),
                'index',
                'index_visit_',
            );
            
            // Total des souscriptions (account_status = 1)
            $total_subscriptions = DB::table('subscription')
                ->where('account_status', 1)
                ->count();
            #Loginfo('Requête subscription réussie', ['count' => $total_subscriptions]);
            
            $daily_subscriptions = DB::table('subscription')
                ->where('account_status', 1)
                ->whereDate('created_at', Carbon::today())
                ->count();
            #Loginfo('Requête daily_subscriptions réussie', ['count' => $daily_subscriptions]);

            // Total des désabonnements
            $total_unsubscriptions = DB::table('unsubscription')->count();
            #Loginfo('Requête unsubscription réussie', ['count' => $total_unsubscriptions]);
            
            $daily_unsubscriptions = DB::table('unsubscription')
                ->whereDate('created_at', Carbon::today())
                ->count();
            #Loginfo('Requête daily_unsubscriptions réussie', ['count' => $daily_unsubscriptions]);

            // Total des consultations de solde (get_balance)
            $total_transactions = DB::table('get_balance')->count();
            #Loginfo('Requête get_balance réussie', ['count' => $total_transactions]);
            
            $daily_transactions = DB::table('get_balance')
                ->whereDate('created_at', Carbon::today())
                ->count();
            #Loginfo('Requête daily_get_balance réussie', ['count' => $daily_transactions]);

            // Total des mini relevés (mini_statement)
            $total_balance = DB::table('mini_statement')->count();
            #Loginfo('Requête mini_statement réussie', ['count' => $total_balance]);
            
            $daily_balance = DB::table('mini_statement')
                ->whereDate('created_at', Carbon::today())
                ->count();
            #Loginfo('Requête daily_mini_statement réussie', ['count' => $daily_balance]);

            // Données pour le graphique mensuel (6 derniers mois) - INCHANGÉ
            $monthly_data = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $month = $date->format('M Y');

                $monthly_subscriptions = DB::table('subscription')
                    ->where('account_status', 1)
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();

                $monthly_unsubscriptions = DB::table('unsubscription')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();

                // Ajout des transactions mensuelles
                $monthly_transactions = DB::table('transaction')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();

                $monthly_data[] = [
                    'month' => $month,
                    'subscriptions' => $monthly_subscriptions,
                    'unsubscriptions' => $monthly_unsubscriptions,
                    'transactions' => $monthly_transactions
                ];
            }
            #Loginfo('Requêtes mensuelles réussies', ['monthly_data' => $monthly_data]);

            // Calcul du nombre de transactions et de la somme des charges par type
            try {
                #Loginfo('Début du calcul du nombre de transactions et des charges');
                
                // Nombre de retraits (A2W = Account to Wallet)
                $current_month_withdrawals = DB::table('transaction')
                    ->where('request_type', 'A2W')
                    ->count();
                
                // Nombre de dépôts (W2A = Wallet to Account)
                $current_month_deposits = DB::table('transaction')
                    ->where('request_type', 'W2A')
                    ->count();
                
                // Somme des charges A2W (retraits)
                $current_month_withdrawals_charges = DB::table('transaction')
                    ->where('request_type', 'A2W')
                    ->get()
                    ->sum(function($item) {
                        return floatval($item->charge) ?? 0;
                    });
                
                // Somme des charges W2A (dépôts)
                $current_month_deposits_charges = DB::table('transaction')
                    ->where('request_type', 'W2A')
                    ->get()
                    ->sum(function($item) {
                        return floatval($item->charge) ?? 0;
                    });
                
                // Total des charges
                $current_month_charges = $current_month_deposits_charges + $current_month_withdrawals_charges;
                
                // Données par office_name pour le graphique (juste count request_type)
                $office_data = DB::table('transaction')
                    ->selectRaw('office_name, COUNT(*) as total_transactions')
                    ->whereNotNull('office_name')
                    ->groupBy('office_name')
                    ->orderBy('total_transactions', 'desc')
                    ->get();
                
                // Liste des libelle (pas account_name)
                $account_names = DB::table('transaction')
                    ->selectRaw('libelle, COUNT(*) as transaction_count')
                    ->whereNotNull('libelle')
                    ->groupBy('libelle')
                    ->orderBy('transaction_count', 'desc')
                    ->limit(10)
                    ->get();
                
                // Total général des transactions
                $total_all_transactions = DB::table('transaction')->count();
                
                #Loginfo('Résultats des transactions', [
                //     'deposits_w2a_count' => $current_month_deposits,
                //     'withdrawals_a2w_count' => $current_month_withdrawals,
                //     'deposits_w2a_charges' => $current_month_deposits_charges,
                //     'withdrawals_a2w_charges' => $current_month_withdrawals_charges,
                //     'total_charges' => $current_month_charges,
                //     'office_data_count' => $office_data->count(),
                //     'libelle_count' => $account_names->count(),
                //     'total_all_transactions' => $total_all_transactions
                // ]);
            } catch (\Exception $e) {
                $current_month_deposits = 0;
                $current_month_withdrawals = 0;
                $current_month_charges = 0;
                $office_data = collect();
                $account_names = collect();
                $total_all_transactions = 0;
                #Logwarning('Erreur calcul transactions et charges', ['error' => $e->getMessage()]);
            }

            // Debug: vérifier que toutes les variables sont définies
            #Loginfo('Variables du dashboard:', [
            //     'total_subscriptions' => $total_subscriptions,
            //     'daily_subscriptions' => $daily_subscriptions,
            //     'total_unsubscriptions' => $total_unsubscriptions,
            //     'daily_unsubscriptions' => $daily_unsubscriptions,
            //     'total_transactions' => $total_transactions,
            //     'daily_transactions' => $daily_transactions,
            //     'total_balance' => $total_balance,
            //     'daily_balance' => $daily_balance,
            //     'monthly_data' => $monthly_data,
            //     'current_month_deposits' => $current_month_deposits ?? 0,
            //     'current_month_withdrawals' => $current_month_withdrawals ?? 0,
            //     'current_month_charges' => $current_month_charges ?? 0,
            //     'office_data_count' => $office_data->count() ?? 0,
            //     'account_names_count' => $account_names->count() ?? 0,
            //     'total_all_transactions' => $total_all_transactions ?? 0
            // ]);

            #Loginfo('IndexController showIndex appelé avec succès');
            
        } catch (\Exception $e) {
            // En cas d'erreur, utiliser des valeurs par défaut
            #Logerror('Erreur générale dans IndexController: ' . $e->getMessage());
            $total_subscriptions = 0;
            $daily_subscriptions = 0;
            $total_unsubscriptions = 0;
            $daily_unsubscriptions = 0;
            $total_transactions = 0;
            $daily_transactions = 0;
            $total_balance = 0;
            $daily_balance = 0;
            $current_month_deposits = 0;
            $current_month_withdrawals = 0;
            $current_month_charges = 0;
            $office_data = collect();
            $account_names = collect();
            $total_all_transactions = 0;
            
            $monthly_data = [
                ['month' => 'Jan 2025', 'subscriptions' => 0, 'unsubscriptions' => 0, 'transactions' => 0],
                ['month' => 'Feb 2025', 'subscriptions' => 0, 'unsubscriptions' => 0, 'transactions' => 0],
                ['month' => 'Mar 2025', 'subscriptions' => 0, 'unsubscriptions' => 0, 'transactions' => 0],
                ['month' => 'Apr 2025', 'subscriptions' => 0, 'unsubscriptions' => 0, 'transactions' => 0],
                ['month' => 'May 2025', 'subscriptions' => 0, 'unsubscriptions' => 0, 'transactions' => 0],
                ['month' => 'Jun 2025', 'subscriptions' => 0, 'unsubscriptions' => 0, 'transactions' => 0],
            ];
        }

        return view('index', compact(
            'total_subscriptions',
            'daily_subscriptions',
            'total_unsubscriptions',
            'daily_unsubscriptions',
            'total_transactions',
            'daily_transactions',
            'total_balance',
            'daily_balance',
            'monthly_data',
            'current_month_deposits',
            'current_month_withdrawals',
            'current_month_charges',
            'office_data',
            'account_names',
            'total_all_transactions'
        ));
    }
}
