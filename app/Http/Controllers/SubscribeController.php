<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\taratra\Validation;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Env;

class SubscribeController extends Controller
{
    public function showSubscription(Request $request)
    {
        return view('subscribe');
    }
    public function sendSubscription(Request $request)
    {
        # Musoni - Utiliser config() au lieu de env() pour éviter les problèmes de cache
        $api_username = config('app.api_username');
        $api_password = config('app.api_password');
        $api_url = config('app.api_url');
        $api_secret = config('app.api_secret');
        $api_key = config('app.api_key');

        // Vérifier que toutes les variables sont définies
        if (!$api_username || !$api_password || !$api_url || !$api_secret || !$api_key) {
            Log::error('Variables d\'environnement API manquantes dans SubscribeController');
            return redirect()->back()->with('error', 'Erreur de configuration du serveur. Veuillez contacter l\'administrateur.');
        }

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
        ]);

        $msisdn = $request->input('msisdn');
        $key = $request->input('key');

        $xmlRequest = '<?xml version="1.0" encoding="UTF-8"?>
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
            xmlns:reg="http://om.btow.com/register">
            <soapenv:Header/>
            <soapenv:Body>
                <reg:KYCRequest>
                    <msisdn>' . $msisdn . '</msisdn>
                    <key>' . $key . '</key>
                </reg:KYCRequest>
            </soapenv:Body>
        </soapenv:Envelope>';


        $wsdlUrl = "https://sandbox.orange-money.com/b2wg4/register?bic=BNKSBLCC";
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post($wsdlUrl, [
                'headers' => [
                    'Content-Type' => 'text/xml; charset=utf-8',
                    'SOAPAction' => 'urn:#NewOperation',
                ],
                'body' => $xmlRequest,
                'http_errors' => false,
                'verify' => false,
            ]);

            $xmlResponse = $response->getBody()->getContents();
            try {
                $dom = new DOMDocument();
                $dom->loadXML($xmlResponse);
                if (!$dom) {
                    throw new \Exception("Réponse XML invalide !");
                }
            } catch (\Exception $e) {
                Log::error("Erreur lors du chargement du XML : " . $e->getMessage());
                return redirect()->back()->with('error', 'Erreur lors du chargement de la réponse XML.');
            }
            Log::info('Xml response is : ' . $xmlResponse);

            $xpath = new DOMXPath($dom);
            $xpath->registerNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
            $xpath->registerNamespace('ns1', 'http://om.btow.com/register');

            $testNode = $xpath->query("//soap:Body/*[local-name()='KYCRequestResponse']");
            if ($testNode->length == 0) {
                Log::error("Le nœud KYCRequestResponse n'a pas été trouvé !");
                return redirect()->back()->with('error', 'Réponse invalide du service SOAP.');
            }

            $fields = ['status', 'lastName', 'firstName', 'dob', 'cin'];
            $data = [];

            foreach ($fields as $field) {
                $xpathQuery = "//soap:Body/*[local-name()='KYCRequestResponse']/*[local-name()='$field']";
                $node = $xpath->query($xpathQuery);

                if ($node->length > 0) {
                    $data[$field] = trim($node->item(0)->nodeValue);
                } else {
                    Log::error("Le champ '$field' est introuvable !");
                    $data[$field] = 'Indisponible';
                }
            }

            $om_status = $data['status'];
            $om_cin = $data['cin'] ?? null;
            $om_lastname = $data['lastName'];
            $om_firtsname = $data['firstName'];
            if (!empty($data['dob'])) {
                $om_birthdate = Carbon::createFromFormat('dmY', $data['dob'])->format('Y-m-d');
            } else {
                $om_birthdate = 'Indisponible'; # Ou tout autre valeur par défaut
            }

            if (isset($errorMessages[$om_status])) {
                return redirect()->back()->with('error', $errorMessages[$om_status]);
            }

            $url = $api_url.'/search?resource=clientidentifiers&query=11' . $om_cin;
            $response = Http::withBasicAuth($api_username, $api_password)
                ->withHeaders([
                    'X-Fineract-Platform-TenantId' => $api_secret,
                    'x-api-key' => $api_key,
                    'Accept' => 'application/json',
                ])
                ->withoutVerifying()
                ->get($url);

            if (!$response->successful()) {
                return redirect()->back()->with('error', 'Client non trouvé.');
            }

            $clientData = $response->json();
            if (empty($clientData)) {
                return redirect()->back()->with('error', 'Aucune donnée trouvée pour ce CIN.');
            }

            $customer_cin = $clientData[0]['entityName'] ?? 'Indisponible';
            $customer_id = $clientData[0]['parentId'] ?? null;

            $verified_cin = "11" . $om_cin;

            if (!$customer_id) {
                return redirect()->back()->with('error', 'Client introuvable.');
            }

            $get_customer_info_by_id = $api_url.'/clients/' . $customer_id;
            $customer_info_response = Http::withBasicAuth($api_username, $api_password)
                ->withHeaders([
                    'X-Fineract-Platform-TenantId' => $api_secret,
                    'x-api-key' => $api_key,
                    'Accept' => 'application/json',
                ])
                ->withoutVerifying()
                ->get($get_customer_info_by_id);

            if (!$customer_info_response->successful()) {
                return redirect()->back()->with('error', 'Erreur lors de la récupération des informations du client.');
            }
            # Json format
            $clientDataById = $customer_info_response->json();

            # dd($clientDataById);

            if (empty($clientDataById)) {
                return redirect()->back()->with('error', 'Aucune donnée trouvée.');
            }
            $customer_lastname = $clientDataById['lastname'] ?? 'Indisponible';
            $customer_firstname = $clientDataById['firstname'] ?? 'Indisponible';
            $customer_dob = $clientDataById['dateOfBirth'] ?? 'Indisponible';
            $customer_mobile_no = $clientDataById['mobileNo'] ?? 'Indisponible';
            $office_name = $clientDataById['officeName'] ?? 'Indisponible';
            $customer_date_of_birth = is_array($customer_dob) ? implode('-', $customer_dob) : 'Indisponible';

            session([
                'client_id' => $customer_id,
                'msisdn' => $msisdn,
                'om_lastname' => $om_lastname,
                'om_firstname' => $om_firtsname,
                'om_date_of_birth' => $om_birthdate,
                'om_cin' => $om_cin,
                'bank_lastname' => $customer_lastname,
                'bank_firstname' => $customer_firstname,
                'bank_date_of_irth' => $customer_date_of_birth,
                'bank_mobile_no' => $customer_mobile_no,
                'bank_office_name' => $office_name,
                'bank_client_cin' => $customer_cin,
                'bank_activation_key' => $key,
            ]);

            return view('kycControl', compact(
                'key',
                'msisdn',
                'om_lastname',
                'om_firtsname',
                'om_birthdate',
                'om_cin',
                'data',
                'customer_lastname',
                'customer_firstname',
                'customer_date_of_birth',
                'customer_cin',
                'customer_mobile_no',
                'verified_cin'
            ));
        } catch (\Exception $e) {
            Log::error("Erreur: " . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur de connexion au service.');
        }
    }
}
