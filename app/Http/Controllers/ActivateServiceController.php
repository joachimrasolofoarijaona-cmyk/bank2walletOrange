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

class ActivateServiceController extends Controller
{
    # Function to show activation form
    public function showActivationForm(Request $request)
    {
        # log 
        logActivity(
            session('username'),
            'activation_service',
            'show_activation_form',
        );
        return view('activateService');
    }

    # Function to activate service
    public function activateService(Request $request)
    {
        # __Validate the request inputs__
        $request->validate([
            'ticket' => 'required|string',
            'mobile_no' => 'required|string',
            'key' => 'required|string',
            'account_no' => 'required|string',
        ]);

        # __Retrieve the ticket and mobile number from the request__
        $ticket = $request->input('ticket');
        $msisdn = $request->input('mobile_no');
        $key = $request->input('key');
        $account_no = $request->input('account_no');

        # Musoni 
        $api_username = env('API_USERNAME');
        $api_password = env('API_PASSWORD');
        $api_url = env('API_URL');

        # __check if request already validated__
        $is_validated = DB::table('validation')
            ->select('status')
            ->where('ticket', $ticket)
            ->first();

        if ($is_validated->status === "1") {
            # __Get customer data__
            $get_customer_data = DB::table('validation')
                ->select('client_id', 'account_no', 'code_service', 'client_cin', 'client_firstname', 'client_lastname', 'client_dob')
                ->where('ticket', $ticket)
                ->first();

            $customer_id = $get_customer_data->client_id;
            $customer_cin = $get_customer_data->client_cin;
            $customer_firtsname = $get_customer_data->client_firstname;
            $customer_lastname = $get_customer_data->client_lastname;
            $customer_birthdate = $get_customer_data->client_dob;
            $code_service = $get_customer_data->code_service;

            # __Generate Alias__
            $lastRecord = Subscription::latest()->first();
            $lastNumber = $lastRecord ? intval(substr($lastRecord->alias, -8)) : 1;
            $newNumber = str_pad($lastNumber + 1, 14, '0', STR_PAD_LEFT);

            # __SOAP OM datas required__
            $alias = "BNKSBLCC" . $newNumber;
            $currency = "OUV";
            $formated_date = Carbon::now()->format('Y-m-d\TH:i:s');

            # __Check if already subscribed__
            $is_subscribed = DB::table('subscription')
                ->select('account_status')
                ->where('account_no', $account_no)
                ->first();

            if ($is_subscribed && $is_subscribed->account_status == 1) {
                return redirect()->back()->with('error', 'Ce compte : ' . $account_no . ' est déjà abonné.');
            } else {
                # __Get libellé from musoni API::savingsProductName__
                try {
                    $get_account_libelle = $api_url . '/savingsaccounts/' . $account_no;
                    $account_libelle_response = Http::withBasicAuth($api_username, $api_password)
                        ->withHeaders([
                            'X-Fineract-Platform-TenantId' => env('API_SECRET'),
                            'x-api-key' => env('API_KEY'),
                            'Accept' => 'application/json',
                        ])
                        ->withoutVerifying()
                        ->get($get_account_libelle);

                    # __Check if the response is successful__
                    if (!$account_libelle_response->successful()) {
                        return redirect()->back()->with('error', 'Erreur! Compte ' . $account_no . ' introuvable.');
                    }

                    $libelle = $account_libelle_response->json()['savingsProductName'] ?? 'Indisponible';

                    $om_subscription_request = '<?xml version="1.0" encoding="UTF-8"?>
                    <soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                                    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                                    xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
                                    xmlns:reg="http://om.btow.com/register">
                        <soapenv:Header/>
                        <soapenv:Body>
                            <reg:ombRequest soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
                                <msisdn xsi:type="xsd:string">' . $msisdn . '</msisdn>
                                <alias xsi:type="xsd:string">' . $alias . '</alias>
                                <code_service xsi:type="xsd:short">' . $code_service . '</code_service>
                                <libelle xsi:type="xsd:string">' . $libelle . '</libelle>
                                <devise xsi:type="xsd:string">' . $currency . '</devise>
                                <key xsi:type="xsd:string">' . $key . '</key>
                                <active_date xsi:type="xsd:dateTime">' . $formated_date . '</active_date>
                            </reg:ombRequest>
                        </soapenv:Body>
                    </soapenv:Envelope>';

                    $wsdl_url = "https://sandbox.orange-money.com/b2wg4/register?bic=BNKSBLCC";
                    try {
                        // Envoi de la requête SOAP avec Guzzle
                        $client = new Client();
                        $response = $client->post($wsdl_url, [
                            'headers' => [
                                'Content-Type' => 'text/xml; charset=utf-8',
                                'SOAPAction' => 'urn:#doRegister',
                            ],
                            'body' => $om_subscription_request,
                            'http_errors' => false,
                            'verify' => false,
                        ]);

                        # __Parsing OM response__
                        $om_response = $response->getBody()->getContents();

                        try {
                            $dom = new DOMDocument();
                            $dom->loadXML($om_response);
                            if (!$dom) {
                                throw new \Exception("Réponse XML invalide !");
                            }
                        } catch (\Exception $e) {
                            Log::error("Erreur lors du chargement du XML : " . $e->getMessage());
                            return redirect()->back()->with('error', 'Erreur lors du traitement de la réponse XML.');
                        }

                        # __Parsing the XML response__
                        $xpath = new DOMXPath($dom);
                        $xpath->registerNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
                        $xpath->registerNamespace('ns1', 'http://om.btow.com/register');

                        $testNode = $xpath->query("//soap:Body/*[local-name()='ombRequestResponse']");

                        if ($testNode->length == 0) {
                            Log::error("Le nœud ombResponse n'a pas été trouvé !");
                            return redirect()->back()->with('error', 'Réponse invalide du service SOAP.');
                        }

                        # __Extracting the response code and message__
                        $fields = ['return_code', 'alias'];
                        $data = [];
                        foreach ($fields as $field) {
                            $xpathQuery = "//soap:Body/*[local-name()='ombRequestResponse']/*[local-name()='$field']";
                            $node = $xpath->query($xpathQuery);

                            $data[$field] = $node->length > 0 ? trim($node->item(0)->nodeValue) : 'Indisponible';
                        }
                        $response_status = $data['return_code'] ?? null;

                        # __Check the response status__
                        $errors_messages = [
                            '302' => 'Requête Invalide',
                            '303' => 'Erreur de format du numéro de ligne',
                            '304' => 'Alias inconnu',
                            '306' => 'Monnaie invalide',
                            '307' => 'Code Service incorrecte',
                            '601' => "L'alias existe déjà",
                            '602' => "Délais de requête dépassé, relancer la demande de clé d'activation",
                            '603' => "Clé d'activation expirée !",
                        ];

                        if (isset($response_status) && $response_status === "200") {
                        } elseif (isset($errors_messages[$response_status])) {
                            Log::info("Erreur trouvée : " . $errors_messages[$response_status]);
                            return redirect()->back()->with('error', $errors_messages[$response_status]);
                        } else {
                            Log::info("Code inconnu : " . $response_status);
                            return redirect()->back()->with('error', $errors_messages[$response_status]);
                        }
                    } catch (\Exception $e) {
                        Log::error("Erreur lors de la requête SOAP : " . $e->getMessage());
                        return redirect()->back()->with('error', 'Erreur de connexion au service.');
                    }

                    # __Get Customer datas from Musoni API__
                    $get_customer_data = $api_url . '/clients/' . $customer_id;

                    $customer_data_response = Http::withBasicAuth($api_username, $api_password)
                        ->withHeaders([
                            'X-Fineract-Platform-TenantId' => env('API_SECRET'),
                            'x-api-key' => env('API_KEY'),
                            'Accept' => 'application/json',
                        ])
                        ->withoutVerifying()
                        ->get($get_customer_data);

                    if (!$customer_data_response->successful()) {
                        return redirect()->back()->with('error', 'Erreur lors de la récupération des informations du client.');
                    }
                    # __Json format__
                    $customer = $customer_data_response->json();
                    if (empty($customer)) {
                        return redirect()->back()->with('error', 'Aucune donnée trouvée.');
                    }

                    $mobile_no = $customer['mobileNo'] ?? 'Indisponible';
                    $office_name = $customer['officeName'] ?? 'Indisponible';
                    $get_current_user = session('firstname') . ' ' . session('lastname');

                    # __Insert into Subscription table__
                    try {
                        $subscription = new Subscription();
                        $subscription->client_id = $customer_id;
                        $subscription->account_no = $account_no;
                        $subscription->msisdn = $msisdn;
                        $subscription->alias = $alias;
                        $subscription->code_service = $code_service;
                        $subscription->key = $key;
                        $subscription->date_sub = Carbon::now();
                        $subscription->bank_agent = $get_current_user;
                        $subscription->account_status = "1";
                        $subscription->libelle = $libelle;
                        $subscription->mobile_no = $mobile_no;
                        $subscription->officeName = $office_name;
                        $subscription->client_cin = $customer_cin;
                        $subscription->client_lastname = $customer_lastname;
                        $subscription->client_firstName = $customer_firtsname;
                        $subscription->client_dob = $customer_birthdate;
                        $subscription->save();

                        # Une fois l'abonnement créé avec succès :
                        DB::table('validation')
                            ->where('account_no', $account_no)
                            ->update([
                                'final_status' => 'activated',
                                'updated_at' => now(),
                            ]);
                        
                    } catch (\Exception $e) {
                        Log::error("Erreur lors l'insertion dans la table subscription : " . $e->getMessage());
                        return redirect()->back()->with('error', "Erreur lors l'insertion dans la table subscription. ");
                    }
                } catch (\Exception $e) {
                    Log::error("Erreur lors du chargement du XML : " . $e->getMessage());
                    return redirect()->back()->with('error', 'Erreur lors du traitement de la réponse XML.');
                }

                # log 
                logActivity(
                    session('username'),
                    'activation_service',
                    'activate_service',
                );

                return view('activateService', compact(
                    'msisdn',
                    'alias',
                    'code_service',
                    'libelle',
                    'currency',
                    'key',
                    'customer_id',
                    'customer_cin',
                    'customer_firtsname',
                    'customer_lastname',
                    'customer_birthdate'
                ));
            }
        }
    }
}
