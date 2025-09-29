<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\FuncCall;
use App\Models\Settings;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function showSettings()
    {
        # get last data from settings table
        $last_row = DB::table('settings')
            ->orderBy('id', 'desc')
            ->first();

        if ($last_row->pause == "0") {
            $status = "ACTIF";
        } else {
            $status = "INACTIF";
        };

        # Get data from activity logs
        $activity_logs = DB::table('activity_logs')
            ->orderBy('id', 'desc')
            ->paginate(15);

        $datas = DB::table('settings')
            ->orderBy('id', 'desc')
            ->paginate(15);

        // dd($last_row);  

        return view('settings', compact([
            'last_row',
            'datas',
            'status',
            'activity_logs'
        ]));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'motif' => 'required|string|min:15',
            'commentaire' => 'required|string|min:25',
            'pause' => 'required|in:0,1'
        ]);

        $motif = $request->motif;
        $commentaire = $request->commentaire;
        $pause = $request->input('pause');

        # Check if pause is 0 or 1
        if ($pause == "0") {
            $idle = "true";
        } else {
            $idle = "false";
        }

        # Build XML request
        $xmlRequest = '<?xml version="1.0" encoding="UTF-8"?>
            <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Body>
                    <ns1:setIdle xmlns:ns1="http://om.btow.com">
                        <idle>' . $idle . '</idle>
                    </ns1:setIdle>
                </soap:Body>
            </soap:Envelope>';


        $url = "https://sandbox.orange-money.com/b2wg4/idle?bic=BNKSBLCC";

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'text/xml; charset=utf-8',
                    'SOAPAction' => 'urn:#setIdle',
                ],
                'body' => $xmlRequest,
                'http_errors' => false,
                'verify' => false,
            ]);

            $xml_response = $response->getBody()->getContents();
            Log::info('Réponse XML reçue', ['response' => $xml_response]);

            $dom = new \DOMDocument();
            @$dom->loadXML($xml_response);

            $xpath = new \DOMXPath($dom);
            $xpath->registerNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
            $xpath->registerNamespace('ns1', 'http://om.btow.com');

            $response_code_node = $xpath->query("//soap:Body/*[local-name()='setIdleResponse']/*[local-name()='responseCode']");

            if ($response_code_node->length === 0) {
                Log::error("Champ responseCode introuvable dans la réponse Orange Money");
                return redirect()->back()->with('error', 'Réponse invalide du service Orange Money.');
            }

            $idle_response = trim($response_code_node->item(0)->nodeValue);

            if (!in_array($idle_response, ['true', 'false'])) {
                Log::error("Réponse Orange Money invalide", ['response' => $idle_response]);
                return redirect()->back()->with('error', "La requête Orange Money a échoué. Réponse: $idle_response");
            }

            $current_user = session('firstname') . ' ' . session('lastname');

            if ($idle_response == "true") {
                $pause = "1";
            } else {
                $pause = "0";
            }

            # Save to settings table
            $settings = new Settings();
            $settings->user_name = $current_user;
            $settings->user_id = auth()->user()->id ?? 0;
            $settings->motif = $motif;
            $settings->commentaire = $commentaire;
            $settings->pause = $pause;
            $settings->save();

            $message = ($pause == "0") ? 'Service activé avec succès.' : 'Service désactivé avec succès.';
            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la communication avec Orange Money', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Erreur lors de la communication avec le service Orange Money: ' . $e->getMessage());
        }
    }

    public function updateSettings1(Request $request)
    {
        $request->validate([
            'motif' => 'required|string|min:15',
            'commentaire' => 'required|string|min:25',
            'pause' => 'required|in:0,1'
        ]);

        $motif = $request->motif;
        $commentaire = $request->commentaire;
        $pause = $request->boolean('pause');

        dd($pause);


        Log::info('Requête settings reçue', compact('motif', 'commentaire', 'pause'));

        # XML request construction
        $xmlRequest = '<?xml version="1.0" encoding="UTF-8"?>
            <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Body>
                    <ns1:setIdle xmlns:ns1="http://om.btow.com">
                        <idle>' . ($pause ? 'true' : 'false') . '</idle>
                    </ns1:setIdle>
                </soap:Body>
            </soap:Envelope>';

        Log::info('Requête XML envoyée : ' . $xmlRequest);

        $url = "https://sandbox.orange-money.com/b2wg4/idle?bic=BNKSBLCC";

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'text/xml; charset=utf-8',
                    'SOAPAction' => 'urn:#setIdle',
                ],
                'body' => $xmlRequest,
                'http_errors' => false,
                'verify' => false,
            ]);

            $xml_response = $response->getBody()->getContents();
            Log::info('Réponse XML reçue : ' . $xml_response);

            $dom = new \DOMDocument();
            @$dom->loadXML($xml_response);

            $xpath = new \DOMXPath($dom);
            $xpath->registerNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
            $xpath->registerNamespace('ns1', 'http://om.btow.com');

            $response_code_node = $xpath->query("//soap:Body/*[local-name()='setIdleResponse']/*[local-name()='responseCode']");

            if ($response_code_node->length === 0) {
                Log::error("Champ responseCode introuvable !");
                return redirect()->back()->with('error', 'Réponse invalide du service SOAP.');
            }

            $idle_response = trim($response_code_node->item(0)->nodeValue);

            if (!in_array($idle_response, ['true', 'false'])) {
                return redirect()->back()->with('error', "La requête SOAP a échoué.");
            }
            $current_user = session('firstname') . ' ' . session('lastname');
            # Save to settings table
            $settings = new Settings();
            $settings->user_name = $current_user;
            $settings->user_id = auth()->user()->id ?? 0;
            $settings->motif = $motif;
            $settings->commentaire = $commentaire;
            $settings->pause = $pause;
            $settings->save();

            return redirect()->back()->with('success', $pause ? 'Service désactivé.' : 'Service activé.');
        } catch (\Exception $e) {
            Log::error('Erreur enregistrement données : ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la communication avec le service Orange.');
        }
    }
}
