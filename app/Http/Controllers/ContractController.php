<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Subscription;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    # Function to show the contract page
    public function showContractPage()
    {
        # log 
        logActivity(
            session('username'),
            'contract',
            'contract_visit_page',
        );
        return view('contract');
    }

    # Function to generate the contract
    public function generateContract(Request $request)
    {
        # Validation des inputs dans le formulaire
        $request->validate([
            'msisdn' => 'required|string',
            'contract_type' => 'required|string',
        ]);

        $msisdn = $request->input('msisdn');
        $contractType = $request->input('contract_type');

        # get account data according to contract type && msisdn
        if ($contractType == "1") {
            #get data for subscription
            $subscription_data = DB::table('subscription')
                ->where('msisdn', $msisdn)
                ->get();

            # check if data is empty
            if ($subscription_data->isEmpty()) {
                # log 
                logActivity(
                    session('username'),
                    'contract',
                    'contract_error_client_not_found',
                );
                return redirect()->back()->with(['error' => 'Aucune donnée trouvée pour le numéro : ' . $msisdn]);
            } else {

                $subscription_customer = DB::table('subscription')
                    ->where('msisdn', $msisdn)
                    ->first();
                # log 
                logActivity(
                    session('username'),
                    'contract',
                    'contract_search_customer',
                );

                # return $subscription_data to contract view;
                return view('contract', [
                    'msisdn' => $msisdn,
                    'contract_type' => $contractType,
                    'subscription_customer' => $subscription_customer,
                    'subscription_data' => $subscription_data,
                ]);
            }
        } elseif ($contractType == "0") {
            #get data for unsubscription
            $unsubscription_data = DB::table('unsubscription')
                ->where('msisdn', $msisdn)
                ->get();

            # check if data is empty
            if ($unsubscription_data->isEmpty()) {
                # log 
                # log 
                logActivity(
                    session('username'),
                    'contract',
                    'contract_error_unsub_client_not_found',
                );
                return redirect()->back()->with(['error' => 'Aucune donnée trouvée pour le numéro : ' . $msisdn]);
            } else {
                # log 
                logActivity(
                    session('username'),
                    'contract',
                    'contract_do_unsub',
                );

                $unsubscription_customer = DB::table('unsubscription')
                    ->where('msisdn', $msisdn)
                    ->first();

                # return $unsubscription_data to contract view;
                return view('contract', [
                    'msisdn' => $msisdn,
                    'contract_type' => $contractType,
                    'unsubscription_customer' => $unsubscription_customer,
                    'unsubscription_data' => $unsubscription_data,
                ]);
            }
        }

        # return redirect the msisdn
        return view('contract', [
            'msisdn' => $msisdn,
            'contract_type' => $contractType,
        ]);
    }
}
