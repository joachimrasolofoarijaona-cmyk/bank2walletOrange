<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Unsubscription;
use App\Models\Subscription;
use App\Models\Validation;
use GuzzleHttp\Client;
use Carbon\Carbon;
use DOMDocument;
use DOMXPath;


class UnsubscribeController extends Controller
{
    # Function to handle the unsubscribe process
    public function showUnsubscribeForm()
    {
        return view('unsubscribe');
    }

    # Function to search for a customer to unsubscribe
    public function searchCustomer(Request $request)
    {
        # Validation des inputs dans le formulaire
        $request->validate([
            'msisdn' => 'required|string|min:10|max:15',
        ]);
        $msisdn = $request->input('msisdn');

        # Find the customer by msisdn in subscription table if status = 1
        $customer = DB::table('subscription')
            ->where('msisdn', $msisdn)
            ->where('account_status', "1")
            ->get();

        if ($customer->isEmpty()) {
            return redirect()->back()->with('error', 'Aucun client trouvé avec ce numéro de téléphone.');
        }

        return view('unsubscribe', ['customer' => $customer]);
    }

    # Function to get data form form then send to validation table with résiliation status
    public function unsubToValidation(Request $request)
    {
        # Validate the request inputs
        $request->validate([
            'msisdn' => 'required|string|min:10|max:15',
            'id' => 'required|integer',
            'account_no' => 'required|string',
            'code_service' => 'required|string',
            'libelle' => 'required|string',
        ]);
        $msisdn = $request->input('msisdn');
        $id = $request->input('id');
        $account_no = $request->input('account_no');
        $code_service = $request->input('code_service');
        $libelle = $request->input('libelle');
        $date_unsub = Carbon::now()->format('Y-m-d\TH:i:s');

        # Récupération de l'alias depuis la base de donnée
        $get_sub_client = DB::table('subscription')
            ->select('client_id', 'account_no', 'msisdn', 'alias', 'code_service', 'key', 'date_sub', 'bank_agent', 'account_status', 'libelle', 'officeName', 'mobile_no', 'client_cin', 'client_lastname', 'client_firstname', 'client_dob')
            ->where('account_no', $account_no)
            ->first();

        $last_record = Validation::latest()->first();
        $last_number = $last_record ? intval(substr($last_record->ticket, -8)) : 1;
        $new_number = str_pad($last_number + 1, 9, '0', STR_PAD_LEFT);

        # Create a new validation record
        Validation::create([
            'msisdn' => $msisdn,
            'id' => $id,
            'account_no' => $account_no,
            'code_service' => $code_service,
            'libelle' => $libelle,
            'alias' => $alias->alias ?? null, // Use null if alias is not found
            'status' => 0, // Set status to 0 for unsubscribe
        ]);

        return redirect()->route('show.unsubscribe.form')->with('success', 'Résiliation demandée avec succès.');
    }

    # Function to unsubscribe a customer
    public function sendValidation(Request $request)
    {
        # Validate the request inputs
        $request->validate([
            'msisdn' => 'required|string|min:10|max:15',
            'account_no' => 'required|string',
            'origin' => 'required|string',
            'motif' => 'required|string',

        ]);
        $msisdn = $request->input('msisdn');
        $account_no = $request->input('account_no');
        $origin = $request->input('origin');
        $motif = $request->input('motif');

        # Récupération de l'alias depuis la base de donnée
        $get_sub_client = DB::table('subscription')
            ->select('client_id', 'account_no', 'msisdn', 'alias', 'code_service', 'key', 'date_sub', 'bank_agent', 'account_status', 'libelle', 'officeName', 'mobile_no', 'client_cin', 'client_lastname', 'client_firstname', 'client_dob')
            ->where('account_no', $account_no)
            ->first();

        # Create a ticket number 
        $last_record = Validation::latest()->first();
        $last_number = $last_record ? intval(substr($last_record->ticket, -8)) : 1;
        $new_number = str_pad($last_number + 1, 9, '0', STR_PAD_LEFT);
        $ticket = "unsub-" . $new_number;

        # get request_type, if request_type === subscription then continue
        $get_request_type = DB::table('validation')
            ->select('request_type')
            ->where('account_no', $account_no)
            ->first();

        try {
            $get_user_name = session('firstname') . ' ' . session('lastname');
            // Enregistrement des données dans la base
            $exists = Validation::where('key', $get_sub_client->key)->exists();

            if ($exists) {
                $validation = new Validation();
                $validation->client_id = $get_sub_client->client_id;
                $validation->mobile_no = $get_sub_client->msisdn;
                $validation->om_lastname = $get_sub_client->client_lastname;
                $validation->om_firstname = $get_sub_client->client_firstname;
                $validation->om_birthdate = $get_sub_client->client_dob;
                $validation->om_cin = $get_sub_client->client_cin;
                $validation->office_name = $get_sub_client->officeName;
                $validation->bank_agent = $get_user_name;
                $validation->status = "0";
                $validation->key = $get_sub_client->key;
                $validation->ticket = $ticket;
                $validation->account_no = $account_no;
                $validation->code_service = $get_sub_client->code_service;
                $validation->client_cin = $get_sub_client->client_cin;;
                $validation->client_firstname = $get_sub_client->client_firstname;
                $validation->client_lastname = $get_sub_client->client_lastname;
                $validation->client_dob = $get_sub_client->client_dob;
                $validation->motif = $motif;
                $validation->request_type = "RESILIATION";
                $validation->origin = $origin;

                $validation->save();
            } else {
                return redirect()->back()->with('error', 'La demande existe déjà ou vous avez actualisé la page');
            }
        } catch (\Exception $e) {
            Log::error("Erreur lors l'enregistrement de la requête : " . $e->getMessage());
            return redirect()->back()->with('error', "Erreur lors l'enregistrement de la requête.");
        }

        return view('subscribeValidation');
    }

    # Function to do the unsubscription
    public function doUnsubscribe(Request $request)
    {
        # Validate the request inputs
        $request->validate([
            'ticket' => 'required|string|max:15',
            'key' => 'required|string|max:15',
            'account_no' => 'required|string|max:155',
            'msisdn' => 'required|string|max:155',
        ]);

        $ticket = $request->input('ticket');
        $key = $request->input('key');
        $account_no = $request->input('account_no');
        $msisdn = $request->input('msisdn');

        # Get alias from subscription table
        $get_alias = DB::table('subscription')
            ->select('alias')
            ->where('account_no', $account_no)
            ->first();
        $alias = $get_alias ? $get_alias->alias : null;
        # Get origin and motif from validation table
        $origin_motif = DB::table('validation')
            ->select('origin', 'motif')
            ->where('ticket', $ticket)
            ->first();

        $origin = $origin_motif ? $origin_motif->origin : null;
        $motif = $origin_motif ? $origin_motif->motif : null;
        $date_unsub = Carbon::now()->format('Y-m-d\TH:i:s');

        # Prepare the XML request
        $xmlRequest = '<?xml version="1.0" encoding="UTF-8"?>
        <soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
            xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
            xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
            xmlns:reg="http://om.btow.com/register">
            <soapenv:Header/>
            <soapenv:Body>
                <reg:ombClose soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
                    <alias xsi:type="xsd:string">' . $alias . '</alias>
                    <close_date xsi:type="xsd:dateTime">' . $date_unsub . '</close_date>
                    <orig xsi:type="xsd:string">' . $origin . '</orig>
                    <motif xsi:type="xsd:string">' . $motif . '</motif>
                </reg:ombClose>
            </soapenv:Body>
        </soapenv:Envelope>';

        # url of the SOAP service
        $unsubUrl = "https://sandbox.orange-money.com/b2wg4/register?bic=BNKSBLCC";

        # Process the SOAP request
        try {
            # Envoi de la requête SOAP avec Guzzle
            $client = new Client();
            $response = $client->post($unsubUrl, [
                'headers' => [
                    'Content-Type' => 'text/xml; charset=utf-8',
                    'SOAPAction' => 'urn:#doClose',
                ],
                'body' => $xmlRequest,
                'http_errors' => false,
                'verify' => false,
            ]);

            # Récupération et parsing de la réponse SOAP
            $xmlResponse = $response->getBody()->getContents();
            Log::info('la réponse XML est : ' . $xmlResponse);

            try {
                $dom = new DOMDocument();
                $dom->loadXML($xmlResponse);
                if (!$dom) {
                    throw new \Exception("Réponse XML invalide !");
                }
            } catch (\Exception $e) {
                Log::error("Erreur lors du chargement du XML : " . $e->getMessage());
                return redirect()->back()->with('error', 'Erreur lors du traitement de la réponse XML.');
            }

            $xpath = new DOMXPath($dom);
            $xpath->registerNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
            $xpath->registerNamespace('ns1', 'http://om.btow.com/register');

            # Vérification du nœud de réponse
            $testNode = $xpath->query("//soap:Body/*[local-name()='ombCloseResponse']");
            if ($testNode->length == 0) {
                Log::error("Le nœud ombCloseResponse n'a pas été trouvé !");
                return redirect()->back()->with('error', 'Réponse invalide du service SOAP.');
            }

            # Extraction des champs utiles
            $fields = ['return_code', 'alias'];
            $data = [];

            foreach ($fields as $field) {
                $xpathQuery = "//soap:Body/*[local-name()='ombCloseResponse']/*[local-name()='$field']";
                $node = $xpath->query($xpathQuery);
                $data[$field] = $node->length > 0 ? trim($node->item(0)->nodeValue) : 'Indisponible';
            }

            # Gestion du statut
            $status = $data['return_code'] ?? null;

            # Gestion des erreurs    
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

            if (isset($status) && $status === "200") {
                # MàJ de la colonne 'account_status' en 0 pour inactif
                $change_status = DB::table('subscription')
                    ->where('account_no', $account_no)
                    ->update(['account_status' => '0']);

                $get_info_client = DB::table('subscription')
                    ->where('account_no', $account_no)
                    ->first();

                $get_client = DB::table('subscription')
                    ->select('msisdn', 'account_no', 'date_sub', 'libelle', 'officeName', 'mobile_no', 'client_cin', 'client_lastname', 'client_firstname')
                    ->where('account_no', $account_no)
                    ->get();
                    
                # Add values in table unsubscription
                try {
                    $get_user_name = session('firstname') . ' ' . session('lastname');
                    # Enregistrement des données dans la base
                    $unsubscription = new Unsubscription();
                    $unsubscription->client_id = $get_info_client->client_id;
                    $unsubscription->account_no = $get_info_client->account_no;
                    $unsubscription->client_lastname = $get_info_client->client_lastname;
                    $unsubscription->client_firstname = $get_info_client->client_firstname;
                    $unsubscription->client_cin = $get_info_client->client_cin;
                    $unsubscription->libelle = $get_info_client->libelle;
                    $unsubscription->alias = $get_info_client->alias;
                    $unsubscription->msisdn = $get_info_client->msisdn;
                    $unsubscription->origin = $origin;
                    $unsubscription->motif = $motif;
                    $unsubscription->bank_agent = $get_user_name;
                    $unsubscription->office_name = $get_info_client->officeName;
                    $unsubscription->date_unsub = Carbon::now();
                    $unsubscription->save();

                    session([
                        "info_client" => $get_client,
                        'motif' => $motif,
                        'unsub_date' => $date_unsub,
                    ]);

                } catch (\Exception $e) {
                    Log::error("Erreur lors de la requête BDD : " . $e->getMessage());
                    return redirect()->back()->with('error', 'Erreur lors de la requête BDD.');
                }

                $get_data_unsub = DB::table('unsubscription')
                    ->where('account_no', $account_no)
                    ->first();
                    
                # redirect to unsubscribe view with success message
                return redirect()->route('show.unsubscribe.form')->with([
                    'success' => 'Résiliation effectuée avec succès.',
                    'get_data_unsub' => $get_data_unsub,
                    'motif' => $motif,
                    'unsub_date' => $date_unsub,
                ]);

            } elseif (isset($errorMessages[$status])) {
                Log::info("Erreur trouvée : " . $errorMessages[$status]);
                return redirect()->back()->with('error', $errorMessages[$status]);

            } else {
                Log::info("Code inconnu : " . $status);
                return redirect()->back()->with('error', $errorMessages[$status]);
            }

            return redirect()->back()->with('error', "Erreur inconnue avec le code : " . $status);

        } catch (\Exception $e) {
            Log::error("Erreur lors de la requête SOAP : " . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur de connexion au service.');
        }
    }
}
