<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\Validation;
use GuzzleHttp\Client;
use Carbon\Carbon;
use DOMDocument;
use DOMXPath;

class DoSubscriptionController extends Controller
{
    # __Function to show doSubscription page__
    public function showSubscriptionForm(Request $request)
    {
        // Vérifier si les données de session nécessaires existent
        if (!session('msisdn') || !session('om_cin')) {
            # log 
            logActivity(
                session('username'),
                'subscription',
                'error_sub_kyc_verification',
            );
            return redirect()->route('show.subscribe')->with('error', 'Veuillez d\'abord effectuer la validation KYC.');
        }

        // Récupérer les comptes du client depuis l'API Musoni
        try {
            $api_username = env('API_USERNAME');
            $api_password = env('API_PASSWORD');
            $api_url = env('API_URL');

            $customer_id = session('client_id');

            if (!$customer_id) {
                # log 
                logActivity(
                    session('username'),
                    'subscription',
                    'error_sub_missing_information',
                );
                return redirect()->route('show.subscribe')->with('error', 'Informations client manquantes.');
            }

            $get_customer_accounts = $api_url . '/clients/' . $customer_id . '/accounts';

            $customer_accounts_response = Http::withBasicAuth($api_username, $api_password)
                ->withHeaders([
                    'X-Fineract-Platform-TenantId' => env('API_SECRET'),
                    'x-api-key' => env('API_KEY'),
                    'Accept' => 'application/json',
                ])
                ->withoutVerifying()
                ->get($get_customer_accounts);

            if ($customer_accounts_response->successful()) {
                $customer_accounts = $customer_accounts_response->json();
                $customer_account = $customer_accounts['savingsAccounts'] ?? [];
            } else {
                $customer_account = [];
            }
        } catch (\Exception $e) {
            Log::error("Erreur lors de la récupération des comptes : " . $e->getMessage());
            $customer_account = [];
        }

        return view('doSubscription', [
            'msisdn' => session('msisdn'),
            'key' => session('bank_activation_key'),
            'customer_account' => $customer_account
        ]);
    }

    # __Function to get user by his CIN in musoni__
    public function getCustomerMusoni(Request $request)
    {
        # Musoni 
        $api_username = env('API_USERNAME');
        $api_password = env('API_PASSWORD');
        $api_url = env('API_URL');

        # Gestion des erreurs Orange Money
        $errorMessages = [
            '302' => 'Requête Invalide',
            '303' => 'Erreur de format du numéro de ligne',
            '304' => 'Alias inconnu',
            '306' => 'Monnaie invalide',
            '307' => 'Code Service incorrecte',
            '601' => "L'alias existe déjà",
            '602' => "Délais de requête dépassé, relancer la demande de clé d'activation",
            '603' => "Clé d'activation expirée !",
        ];

        # Validation des inputs dans le formulaire
        $request->validate([
            'msisdn' => 'required|string|max:10',
            'key' => 'required|string|max:8',
            'omCin' => 'required|string|max:10',
        ]);

        $msisdn = $request->input('msisdn');
        $key = $request->input('key');
        $om_cin = $request->input('omCin');

        $cin_url = $api_url . '/search?resource=clientidentifiers&query=11' . $om_cin;

        # Doing API Request to find user by CIN information
        try {
            $response = Http::withBasicAuth($api_username, $api_password)
                ->withHeaders([
                    'X-Fineract-Platform-TenantId' => env('API_SECRET'),
                    'x-api-key' => env('API_KEY'),
                    'Accept' => 'application/json',
                ])
                ->withoutVerifying()
                ->get($cin_url);

            if ($response->successful()) {

                $customer = $response->json();

                $customer_cin = $customer[0]['entityName'];
                $customer_id = $customer[0]['parentId'];

                # get customer id to find account_no
                try {
                    $get_customer_by_id = $api_url . '/clients/' . $customer_id;

                    $customer_id_response = Http::withBasicAuth($api_username, $api_password)
                        ->withHeaders([
                            'X-Fineract-Platform-TenantId' => env('API_SECRET'),
                            'x-api-key' => env('API_KEY'),
                            'Accept' => 'application/json',
                        ])
                        ->withoutVerifying()
                        ->get($get_customer_by_id);

                    # __Get customer accounts by id__
                    $get_customer_accounts = $api_url . '/clients/' . $customer_id . '/accounts';

                    $customer_accounts_response = Http::withBasicAuth($api_username, $api_password)
                        ->withHeaders([
                            'X-Fineract-Platform-TenantId' => env('API_SECRET'),
                            'x-api-key' => env('API_KEY'),
                            'Accept' => 'application/json',
                        ])
                        ->withoutVerifying()
                        ->get($get_customer_accounts);

                    $customer_accounts = $customer_accounts_response->json();

                    # __Targeting all the accounts to pear__
                    $customer_account = $customer_accounts['savingsAccounts'];

                    if ($customer_id_response->successful()) {
                        $customer_infos = $customer_id_response->json();

                        # Group Customer informations 
                        $customer_lastname = $customer_infos['lastname'] ?? 'Non disponible';
                        $customer_firstname = $customer_infos['firstname'] ?? 'Non disponible';
                        $customer_dob = $customer_infos['dateOfBirth'] ?? 'Non disponible';
                        $customer_office_name = $customer_infos['officeName'] ?? 'Non disponible';

                        # Format Date 
                        $date = $customer_dob[0];
                        $month = $customer_dob[1];
                        $year = $customer_dob[2];
                        $customer_birthdate = $customer_dob[0] . '-' . $customer_dob[1] . '-' . $customer_dob[2];

                        # data for sendValidationRequest 
                        session([
                            'customer_id' => $customer_id,
                            'msisdn' => $msisdn,
                            'om_cin' => $om_cin,
                            'customer_lastname' => $customer_lastname,
                            'customer_firstname' => $customer_firstname,
                            'customer_birthdate' => $customer_birthdate,
                            'customer_office_name' => $customer_office_name,
                        ]);

                        # log 
                        logActivity(
                            session('username'),
                            'subscription',
                            'sub_successfully_done',
                        );
                        return view(
                            'doSubscription',
                            compact(
                                'msisdn',
                                'key',
                                'customer_account',
                                'customer_cin',
                                'customer_lastname',
                                'customer_firstname',
                                'customer_birthdate'
                            )
                        );
                    } elseif (empty($customer_id_response)) {
                        # log 
                        logActivity(
                            session('username'),
                            'subscription',
                            'error_sub_no_data_avalaible',
                        );
                        return view('kycControl', ['error' => 'Aucune donnée client disponible']);
                    }
                } catch (\Exception $e) {
                    # log 
                    logActivity(
                        session('username'),
                        'subscription',
                        'error_sub_missing_informations',
                    );
                    Log::error("Erreur lors de la récupération des informations client : " . $e->getMessage());
                    return view('kycControl', ['error' => 'Erreur de connexion à l\'API']);
                }
            } else {
                # log 
                logActivity(
                    session('username'),
                    'subscription',
                    'error_sub_cin_search',
                );
                return view('kycControl', ['error' => 'Erreur lors de la recherche du client par CIN']);
            }
        } catch (\Exception $e) {
            # log 
            logActivity(
                session('username'),
                'subscription',
                'error_sub_client_not_found',
            );
            Log::error("Erreur lors de la recherche du client : " . $e->getMessage());
            return view('kycControl', ['error' => 'Erreur de connexion à l\'API']);
        }
    }


    # __Function de send validation request__
    public function sendValidationRequest(Request $request)
    {
        # Validation des inputs dans le formulaire
        $request->validate([
            'msisdn' => 'required|string',
            'key' => 'required|string',
            'code_service' => 'required|string',
            'accounts' => 'required|string',
        ]);


        $msisdn = $request->input('msisdn');
        $key = $request->input('key');
        $code_service = $request->input('code_service');
        $account_no = $request->input('accounts');


        # __sessions data from getCustomerMusoni__
        $customer_id = session('customer_id');
        $customer_lastname = session('customer_lastname');
        $customer_firstname = session('customer_firstname');
        $customer_birthdate = session('customer_birthdate');
        $customer_office_name = session('customer_office_name');

        # __get data from session subscribeController__
        $current_user = session('firstname') . ' ' . session('lastname');

        # __get OM data from session__
        $om_lastname = session('om_lastname');
        $om_firtsname = session('om_firstname');
        $om_birthdate = session('om_date_of_birth');
        $om_cin = session('om_cin');

        #__get bank data from session__
        $bank_lastname = session('bank_lastname');
        $bank_firstname = session('bank_firstname');
        $bank_date_of_birth = session('bank_date_of_irth');
        $bank_mobile_no = session('bank_mobile_no');
        $bank_office_name = session('bank_office_name');
        $bank_client_cin = session('bank_client_cin');

        # __generate a new ticket number__
        $last_record = Validation::latest()->first();
        $last_number = $last_record ? intval(substr($last_record->ticket, -8)) : 1;
        $new_number = str_pad($last_number + 1, 9, '0', STR_PAD_LEFT);
        $ticket = "sub-" . $new_number;

        # __check if the account is already subscribed__
        $account_subscribed = DB::table('subscription')
            ->select('account_no', 'account_status')
            ->where('account_no', $account_no)
            ->first();

        # __check if key already exists and status = 1 in validation table__
        $key_exists = DB::table('validation')
            ->select('key')
            ->where('key', $key)
            ->first();

        # __check if the account is already subscribed__
        if (isset($account_subscribed) &&  $account_subscribed->account_status == "1") {
            # log 
            # log 
            logActivity(
                session('username'),
                'subscription',
                'error_linking_client_already_exist',
            );
            return redirect()->back()->with('error', 'Le compte :' . $account_no . ' est déjà lié et actif.');
        } elseif ($key_exists) {
            # log 
            logActivity(
                session('username'),
                'subscription',
                'error_linking_request_pending',
            );
            return redirect()->back()->with('error', 'Cette demande est déjà en attente de validation.');
        } else {
            # __save to table validation__
            try {
                $validation = new Validation();
                $validation->client_id = $customer_id;
                $validation->mobile_no = $msisdn;
                $validation->om_lastname = $om_lastname;
                $validation->om_firstname = $om_firtsname;
                $validation->om_birthdate = $om_birthdate;
                $validation->om_cin = $om_cin;
                $validation->office_name = $customer_office_name;
                $validation->bank_agent = $current_user;
                $validation->status = "0";
                $validation->key = $key;
                $validation->ticket = $ticket;
                $validation->account_no = $account_no;
                $validation->code_service = $code_service;
                $validation->client_cin = $bank_client_cin;
                $validation->client_firstname = $bank_firstname;
                $validation->client_lastname = $bank_lastname;
                $validation->client_dob = $bank_date_of_birth;
                $validation->request_type = "SOUSCRIPTION";
                $validation->motif = "SOUSCRIPTION";
                $validation->request_status = "1";


                $validation->save();
            } catch (\Exception $e) {
                # log 
                logActivity(
                    session('username'),
                    'subscription',
                    'error_linking_error_save_request',
                );
                Log::error("Erreur lors de l'inserstion : " . $e->getMessage());
                return redirect()->back()->with('error', 'Erreur de connexion au service.');
            }
        }
        return view('subscribeValidation');
    }
}
