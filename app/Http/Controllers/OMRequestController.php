<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AccountBalance;
use App\Models\CancelTrans;
use App\Models\Transactions;
use App\Models\MiniStatement;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Arr;

class OMRequestController extends Controller
{
    # variables privées pour récupérer musoni & n8n dans le .env file
    private $musoni_username;
    private $musoni_password;
    private $musoni_secret;
    private $musoni_key;
    private $musoni_url;
    private $n8n_username;
    private $n8n_password;

    # Constructor
    public function __construct()
    {
        $this->musoni_username = config('app.musoni_username', env('API_USERNAME'));
        $this->musoni_password = config('app.musoni_password', env('API_PASSWORD'));
        $this->musoni_secret = config('app.musoni_secret', env('API_SECRET'));
        $this->musoni_key = config('app.musoni_key', env('API_KEY'));
        $this->musoni_url = config('app.musoni_url', env('API_URL'));

        $this->n8n_username = config('app.n8n_username', env('N8N_USERNAME'));
        $this->n8n_password = config('app.n8n_password', env('N8N_PASSWORD'));
    }

    # Exemple simple d'utilisation des variables dans une fonction
    public function exemple_appel_variables()
    {
        // Appel d'une API Musoni avec authentification
        $response = Http::withBasicAuth($this->musoni_username, $this->musoni_password)
            ->withHeaders([
                'X-Fineract-Platform-TenantId' => $this->musoni_secret,
                'x-api-key' => $this->musoni_key,
                'Accept' => 'application/json',
            ])
            ->withoutVerifying()
            ->get($this->musoni_url . '/clients');

        // Exemple d'appel avec les variables N8N
        $n8n_data = [
            'username' => $this->n8n_username,
            'password' => $this->n8n_password
        ];

        Log::info('Variables configurées avec succès', [
            'musoni_url' => $this->musoni_url,
            'n8n_username' => $this->n8n_username
        ]);

        return $response;
    }


    // balance function
    private function balanceResponseToOrange(array $data, object $get_account, string $responseCode, string $responseMessage, $client_account_balance)
    {

        // Response construct
        $orangeResponse = '<?xml version="1.0" encoding="UTF-8"?> 
        <S:Envelope xmlns:S="http://schemas.xmlsoap.org/soap/envelope/">
            <S:Body>
                <ns2:GetAccountBalanceResponse xmlns:ns2="http://b2w.banktowallet.com/b2w"> 
                    <return> 
                        <mmHeaderInfo> 
                            <operatorCode>' . $data['operatorCode'] . '</operatorCode> 
                            <requestId>' . $data['requestId'] . '</requestId> 
                            <affiliateCode>' . $data['affiliateCode'] . '</affiliateCode> 
                            <responseCode>' . $responseCode . '</responseCode> 
                            <responseMessage>' . $responseMessage . '</responseMessage> 
                        </mmHeaderInfo> 
                        <accountAlias>' . $data['accountAlias'] . '</accountAlias> 
                        <ccy>OUV</ccy> 
                        <availableBalance>' . $client_account_balance . '</availableBalance> 
                        <currentBalance>' . $client_account_balance . '</currentBalance> 
                    </return> 
                </ns2:GetAccountBalanceResponse> 
            </S:Body> 
        </S:Envelope>';

        Log::info('Orange Sent Response is : ' . $orangeResponse);

        // Sauvegarde en base
        try {
            $balance = new AccountBalance();
            $balance->client_id = $get_account->client_id;
            $balance->client_lastname = $get_account->client_lastname;
            $balance->client_firstname = $get_account->client_firstname;
            $balance->musoni_account_no = $get_account->account_no;
            $balance->libelle = $get_account->libelle;
            $balance->alias = $get_account->alias;
            $balance->msisdn = $get_account->msisdn;
            $balance->operator_code = $data['operatorCode'];
            $balance->request_id = $data['requestId'];
            $balance->requestToken = $data['requestToken'] ?? "";
            $balance->request_type = $data['requestType'];
            $balance->affiliate_code = $data['affiliateCode'];
            $balance->reason = $data['reason'] ?? "";
            $balance->transaction_date = Carbon::now()->format('Y-m-d\TH:i:s');
            $balance->acep_responde_code = $responseCode;
            $balance->acep_responde_message = $responseMessage;
            $balance->office_name = $get_account->officeName;
            $balance->save();

            if ($responseCode === "000") {
                $status = '1';
            } else {
                $status = '0';
            }

            transactionLogActivity(
                $data['requestId'],
                $data['requestType'],
                $get_account->libelle,
                $get_account->account_no,
                $status,
                100.0,
                'MGA',
                'Consultation solde ' . $get_account->account_no . ' / ' . $get_account->libelle,
                json_encode(['msisdn' => $get_account->msisdn]),
                json_encode(['code' => $responseCode, 'message' => $responseMessage])
            );
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'insertion : " . $e->getMessage());
        }

        return response($orangeResponse, 200)
            ->header('Content-Type', 'text/xml');
    }

    // statement function
    private function statementResponseToOrange(array $data, object $get_account, string $responseCode, string $responseMessage, $transactions)
    {
        // Tri du plus récent au plus ancien
        $filteredTransactions = [];
        $transaction_table = [];

        Log::info('le code de réponse est : ' . $responseCode);

        if ($responseCode === '000') {
            foreach ($transactions as $transaction) {
                $reversed = $transaction['reversed'];
                $value = $transaction['transactionType']['value'];

                // Get last 5 transactions
                usort($transactions, function ($a, $b) {
                    $dateA = Carbon::createFromDate($a['date'][0], $a['date'][1], $a['date'][2]);
                    $dateB = Carbon::createFromDate($b['date'][0], $b['date'][1], $b['date'][2]);
                    return $dateB->timestamp - $dateA->timestamp;
                });

                // On EXCLUT les transactions reversed + Waive Charge
                if ($reversed || $value === "Waive Charge") {
                    continue;
                } else {
                    $filteredTransactions[] = $transaction;
                }

                // On s'arrête dès qu'on a 5 transactions valides
                if (count($filteredTransactions) >= 5) {
                    break;
                }
            }

            foreach ($filteredTransactions as $transaction) {
                $transaction_date = $transaction['date'][0] . '-' . str_pad($transaction['date'][1], 2, '0', STR_PAD_LEFT) . '-' . str_pad($transaction['date'][2], 2, '0', STR_PAD_LEFT);

                if ($transaction['transactionType']['value'] === 'Deposit') {
                    $transaction_type = 'C';
                } elseif ($transaction['transactionType']['value'] === 'Withdrawal' || $transaction['transactionType']['value'] === 'Pay Charge') {
                    $transaction_type = 'D';
                }
                $transaction_list =
                    '<TransactionList>
                        <tranRefNo>' . $transaction['id'] . '</tranRefNo>
                        <tranDate>' . $transaction_date . '</tranDate>
                        <tranType/>
                        <ccy>OUV</ccy>
                        <crDr>' . $transaction_type . '</crDr>
                        <amount>' . $transaction['amount'] . '</amount>
                        <narration/>
                    </TransactionList>';
                $response_code = $responseCode;
                $response_message = $responseMessage;
                $transaction_table[] = $transaction_list;
            }

        } elseif ($responseCode === 'E16'|| $responseCode === 'E22') {
            for ($i = 0; $i <= 4; $i++) {
                $format_date = Carbon::now('Europe/Paris');
                $isoDate = $format_date->toIso8601String();
                $transaction_list =
                    '<TransactionList>
                        <tranRefNo></tranRefNo>
                        <tranDate>' . $isoDate . '</tranDate>
                        <tranType/>
                        <ccy>OUV</ccy>
                        <crDr></crDr>
                        <amount>0</amount>
                        <narration/>
                    </TransactionList>';
                $response_code = $responseCode;
                $response_message = $responseMessage;
                $transaction_table[] = $transaction_list;
            }
        }

        $allTransactionXml = implode("", $transaction_table);

        $orangeResponse =
            '<?xml version="1.0" encoding="UTF-8"?> 
        <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"> 
            <soap:Body> 
                <ns2:GetMiniStatementResponse xmlns:ns2="http://b2w.banktowallet.com/b2w"> 
                    <return> 
                        <mmHeaderInfo> 
                            <operatorCode>' . $data['operatorCode'] . '</operatorCode> 
                            <requestId>' . $data['requestId'] . '</requestId> 
                            <affiliateCode>' . $data['affiliateCode'] . '</affiliateCode> 
                            <responseCode>' . $response_code . '</responseCode> 
                            <responseMessage>' . $response_message . '</responseMessage> 
                        </mmHeaderInfo>'
            . $allTransactionXml .
            '</return> 
                </ns2:GetMiniStatementResponse> 
            </soap:Body> 
        </soap:Envelope>';

        Log::info('Orange Sent Response is : ' . $orangeResponse);

        try {
            $statement = new MiniStatement();
            $statement->client_id = $get_account->client_id;
            $statement->client_lastname = $get_account->client_lastname;
            $statement->client_firstname = $get_account->client_firstname;
            $statement->musoni_account_no = $get_account->account_no;
            $statement->libelle = $get_account->libelle;
            $statement->alias = $get_account->alias;
            $statement->msisdn = $get_account->msisdn;
            $statement->operator_code = $data['operatorCode'];
            $statement->request_id = $data['requestId'];
            $statement->request_token = $data['requestToken'] ?? "";
            $statement->request_type = $data['requestType'];
            $statement->affiliate_code = $data['affiliateCode'];
            $statement->orange_account_no = $data['orangeAccountNo'] ?? '';
            $statement->reason = $data['reason'] ?? "";
            $statement->acep_responde_code = $responseCode;
            $statement->acep_responde_message = $responseMessage;
            $statement->office_name = $get_account->officeName;
            $statement->bank_agent = 'system';
            $statement->save();

            if ($responseCode === "000") {
                $status = '1';
            } else {
                $status = '0';
            }

            #Log statement request
            transactionLogActivity(
                $data['requestId'],
                $data['requestType'],
                $get_account->libelle,
                $get_account->account_no,
                $status,
                500.0,
                'MGA',
                '5 dernièrees transactions ' . $get_account->account_no . ' / ' . $get_account->libelle,
                json_encode(['msisdn' => $get_account->msisdn]),
                json_encode(['code' => $responseCode, 'message' => $responseMessage])
            );
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'insertion : " . $e->getMessage());
        }
        // Log::info('La réponse à envoyer est : ' . print_r($orangeResponse, true));
        return response($orangeResponse, 200)
            ->header('Content-Type', 'text/xml; charset=utf-8');
    }

    // bank to wallet function __withdrawal
    private function bankToWallet(array $data, object $get_account, string $responseCode, string $responseMessage, $externalRefNo, $charges, $transaction_id, $CBAReferenceNo, $resourceId, $officeId, $clientId, $savingId, $chargeId)
    {

        // Insertion transaction WITHDRAWAL table transaction 
        try {
            $transaction = new Transactions();
            $transaction->client_id = $get_account->client_id;
            $transaction->client_lastname = $get_account->client_lastname;
            $transaction->client_firstname = $get_account->client_firstname;
            $transaction->musoni_account_no = $get_account->account_no;
            $transaction->libelle = $get_account->libelle;
            $transaction->alias = $get_account->alias;
            $transaction->msisdn = $get_account->msisdn;
            $transaction->transaction_ref_no = $externalRefNo;
            $transaction->operator_code = $data['operatorCode'] ?? 'NA';
            $transaction->request_id = $data['requestId'] ?? 'NA';
            $transaction->requestToken = $data['requestToken'] ?? 'NA';
            $transaction->request_type = $data['requestType'] ?? 'NA';
            $transaction->affiliate_code = $data['affiliateCode'] ?? 'NA';
            $transaction->external_ref_no = $data['externalRefNo'] ?? 'NA';
            $transaction->mobile_no = $data['mobileNo'] ?? 'NA';
            $transaction->mobile_name = $data['mobileName'] ?? 'NA';
            $transaction->mobile_alias = $data['mobileAlias'] ?? 'NA';
            $transaction->orange_account_no = $data['accountNo'] ?? 'NA';
            $transaction->orange_account_name = $data['accountName'] ?? 'NA';
            $transaction->transfer_description = $data['transferDescription'] ?? 'NA';
            $transaction->currency = $data['ccy'] ?? 'NA';
            $transaction->amount = $data['amount'] ?? 'NA';
            $transaction->charge = $charges ?? '';
            $transaction->transaction_date = Carbon::now()->format('Y-m-d\TH:i:s');
            $transaction->udf1 = $data['udf1'] ?? 'NA';
            $transaction->udf2 = $data['udf2'] ?? 'NA';
            $transaction->udf3 = $data['udf3'] ?? 'NA';
            $transaction->acep_responde_code = $responseCode;
            $transaction->acep_responde_message = $responseMessage;
            $transaction->office_name = $get_account->officeName;
            $transaction->bank_agent = $get_account->bank_agent;
            $transaction->TransactionId = $transaction_id;
            $transaction->CBAReferenceNo = $CBAReferenceNo;
            $transaction->resourceId = $resourceId;
            $transaction->officeId = $officeId;
            $transaction->clientId = $clientId;
            $transaction->savingId = $savingId;
            $transaction->charge_id = $chargeId;
            $transaction->save();
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'insertion : " . $e->getMessage());
        }
        // Si la réponse est E16 on ne fait pas de transaction
        if ($responseCode != '000') {
            $CBAReferenceNo = 0;
            $transaction_id = 0;
        }

        // TransctionStatusInquiry Save For later request
        if ($responseCode === 'E11') {
        }

        $orangeResponse =
            '<?xml version="1.0" encoding="UTF-8"?> 
        <S:Envelope xmlns:S="http://schemas.xmlsoap.org/soap/envelope/"> 
            <S:Body> 
                <ns2:AccountToWalletTransferResponse xmlns:ns2="http://b2w.banktowallet.com/b2w"> 
                    <return> 
                        <mmHeaderInfo> 
                        <operatorCode>' . $data['operatorCode'] . '</operatorCode> 
                        <requestId>' . $data['requestId'] . '</requestId> 
                        <affiliateCode>' . $data['affiliateCode'] . '</affiliateCode> 
                        <responseCode>' . $responseCode . '</responseCode> 
                        <responseMessage>' . $responseMessage . '</responseMessage> 
                        </mmHeaderInfo> 
                        <externalRefNo>' . $data['externalRefNo'] . '</externalRefNo> 
                        <CBAReferenceNo>' . $CBAReferenceNo . '</CBAReferenceNo> 
                    </return> 
                </ns2:AccountToWalletTransferResponse> 
            </S:Body> 
        </S:Envelope>';

        Log::info('Orange Sent Response is : ' . $orangeResponse);

        #Log statement request
        if ($responseCode === "000") {
            $status = '1';
        } else {
            $status = '0';
        }

        transactionLogActivity(
            $data['requestId'],
            $data['requestType'],
            $get_account->libelle,
            $get_account->account_no,
            $status,
            $data['amount'],
            'MGA',
            'Withdrawal operation ' . $get_account->account_no . ' / ' . $get_account->libelle,
            json_encode(['msisdn' => $get_account->msisdn]),
            json_encode(['code' => $responseCode, 'message' => $responseMessage])
        );

        return response($orangeResponse, 200)
            ->header('Content-Type', 'text/xml; charset=utf-8');
    }

    // Wallet to bank fuction 
    private function walletToBank(array $data, object $get_account, string $responseCode, string $responseMessage, $externalRefNo, $transaction_id, $CBAReferenceNo, $resourceId, $officeId, $clientId, $savingId, $chargeId)
    {

        // Insertion transaction DEPOSIT table transaction
        try {
            $transaction = new Transactions();
            $transaction->client_id = $get_account->client_id;
            $transaction->client_lastname = $get_account->client_lastname;
            $transaction->client_firstname = $get_account->client_firstname;
            $transaction->musoni_account_no = $get_account->account_no;
            $transaction->libelle = $get_account->libelle;
            $transaction->alias = $get_account->alias;
            $transaction->msisdn = $get_account->msisdn;
            $transaction->transaction_ref_no = $CBAReferenceNo;
            $transaction->operator_code = $data['operatorCode'];
            $transaction->request_id = $data['requestId'];
            $transaction->requestToken = $data['requestToken'];
            $transaction->request_type = $data['requestType'];
            $transaction->affiliate_code = $data['affiliateCode'];
            $transaction->external_ref_no = $data['externalRefNo'];
            $transaction->mobile_no = $data['mobileNo'];
            $transaction->mobile_name = $data['mobileName'];
            $transaction->mobile_alias = $data['mobileAlias'];
            $transaction->orange_account_no = $data['accountNo'];
            $transaction->orange_account_name = $data['accountName'];
            $transaction->transfer_description = $data['transferDescription'];
            $transaction->currency = $data['ccy'];
            $transaction->amount = $data['amount'];
            $transaction->charge = $data['charge'];
            $transaction->transaction_date = Carbon::now()->format('Y-m-d\TH:i:s');
            $transaction->udf1 = $data['udf1'];
            $transaction->udf2 = $data['udf2'];
            $transaction->udf3 = $data['udf3'];
            $transaction->acep_responde_code = $responseCode;
            $transaction->acep_responde_message = $responseMessage;
            $transaction->office_name = $get_account->officeName;
            $transaction->bank_agent = $get_account->bank_agent;
            $transaction->TransactionId  = $transaction_id;
            $transaction->CBAReferenceNo = $CBAReferenceNo;
            $transaction->resourceId = $resourceId;
            $transaction->officeId = $officeId;
            $transaction->clientId = $clientId;
            $transaction->savingId = $savingId;
            $transaction->charge_id = $chargeId;

            $transaction->save();
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'insertion : " . $e->getMessage());
        }
        $orangeResponse =
            '<?xml version="1.0" encoding="UTF-8"?> 
            <S:Envelope xmlns:S="http://schemas.xmlsoap.org/soap/envelope/"> 
                <S:Body> 
                    <ns2:WalletToAccountTransferResponse xmlns:ns2="http://b2w.banktowallet.com/b2w">
                        <return> 
                            <mmHeaderInfo> 
                                <operatorCode>' . $data['operatorCode'] . '</operatorCode> 
                                <requestId>' . $data['requestId'] . '</requestId> 
                                <affiliateCode>' . $data['affiliateCode'] . '</affiliateCode> 
                                <responseCode>' . $responseCode . '</responseCode> 
                                <responseMessage>' . $responseMessage . '</responseMessage> 
                            </mmHeaderInfo> 
                            <externalRefNo>' . $externalRefNo . '</externalRefNo> 
                            <CBAReferenceNo>' . $CBAReferenceNo . '</CBAReferenceNo> 
                        </return> 
                        </ns2:WalletToAccountTransferResponse>
                </S:Body> 
            </S:Envelope> ';

        Log::info('Orange Sent Response is : ' . $orangeResponse);

        #Log deposit
        if ($responseCode === "000") {
            $status = '1';
        } else {
            $status = '0';
        }

        transactionLogActivity(
            $data['requestId'],
            $data['requestType'],
            $get_account->libelle,
            $get_account->account_no,
            $status,
            $data['amount'],
            'MGA',
            'Deposit operation ' . $get_account->account_no . ' / ' . $get_account->libelle,
            json_encode(['msisdn' => $get_account->msisdn]),
            json_encode(['code' => $responseCode, 'message' => $responseMessage])
        );


        return response($orangeResponse, 200)
            ->header('Content-Type', 'text/xml; charset=utf-8');
    }

    // Cancel transfer function
    private function cancelTransfer(array $data, object $get_account, string $responseCode, string $responseMessage, $client_account_balance,  $resourceId, $officeId, $clientId,  $savingId,  $chargeId, $reference_cancel)
    {
        $orangeResponse =
            '<?xml version="1.0" encoding="UTF-8"?> 
            <S:Envelope xmlns:S="http://schemas.xmlsoap.org/soap/envelope/"> 
                <S:Body> 
                    <ns2:CancelTransferResponse xmlns:ns2="http://b2w.banktowallet.com/b2w"> 
                        <return> 
                            <mmHeaderInfo> 
                            <operatorCode>' . $data['operatorCode'] . '</operatorCode> 
                            <requestId>' . $data['requestId'] . '</requestId> 
                            <affiliateCode>' . $data['affiliateCode'] . '</affiliateCode> 
                            <responseCode>' . $responseCode . '</responseCode> 
                            <responseMessage>' . $responseMessage . '</responseMessage> 
                            </mmHeaderInfo>
                            <CBAReferenceNo>' . $reference_cancel . '</CBAReferenceNo>
                        </return> 
                    </ns2:CancelTransferResponse> 
                </S:Body> 
            </S:Envelope>';

        Log::info('Orange Sent Response is : ' . $orangeResponse);

        // save to table cancel_transfert
        try {
            $cancel = new CancelTrans();
            $cancel->client_id = $get_account->client_id;
            $cancel->client_lastname = $get_account->client_lastname;
            $cancel->client_firstname = $get_account->client_firstname;
            $cancel->musoni_account_no = $get_account->musoni_account_no;
            $cancel->libelle = $get_account->libelle;
            $cancel->amount = $get_account->amount;
            $cancel->alias = $get_account->alias;
            $cancel->msisdn = $get_account->msisdn;
            $cancel->office_name = $get_account->office_name;
            $cancel->bank_agent = $get_account->bank_agent;
            $cancel->resourceId = $resourceId ?? '';
            $cancel->officeId = $officeId ?? '';
            $cancel->clientId = $clientId ?? '';
            $cancel->savingId = $savingId ?? '';
            $cancel->error_code = $responseCode;
            $cancel->error_message = $responseMessage;
            $cancel->reference_cancel = $reference_cancel ?? '';
            $cancel->save();
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'insertion : " . $e->getMessage());
        }
        #Log cancel transfert
        if ($responseCode === "000") {
            $status = '1';
        } else {
            $status = '0';
        }

        transactionLogActivity(
            $data['requestId'],
            $data['requestType'],
            $get_account->libelle,
            $get_account->account_no,
            $status,
            $get_account->amount,
            'MGA',
            'Deposit operation ' . $get_account->account_no . ' / ' . $get_account->libelle,
            json_encode(['msisdn' => $get_account->msisdn]),
            json_encode(['code' => $responseCode, 'message' => $responseMessage])
        );

        return response($orangeResponse, 200)
            ->header('Content-Type', 'text/xml; charset=utf-8');
    }

    // Principal function
    public function handle(Request $request)
    {
        // === LOGS DE DIAGNOSTIC ===
        Log::info('=== OMRequestController::handle DÉBUT ===');
        Log::info('Méthode HTTP: ' . $request->method());
        Log::info('URL complète: ' . $request->fullUrl());
        Log::info('Contenu brut de la requête: ' . $request->getContent());
        Log::info('Données POST: ', $request->all());
        Log::info('=== OMRequestController::handle FIN DIAGNOSTIC ===');

        Log::info('THE REQUEST SUBMITED BY ORANGE IS : ' . $request->getContent());

        // Les dates pour les différentes transactions dans MUSONI
        $withdraw_dateNow = Carbon::now()->format('d m Y');
        $deposit_dateNow = Carbon::now()->format('d M Y');
        $balance_dateNow = Carbon::now()->format('d F Y');
        $soapRequest = $request->getContent();
        $username = env('N8N_USERNAME');
        $password = env('N8N_PASSWORD');
        $balance_charges = 100;


        Log::info('Contenu brut de la requête SOAP : ' . $soapRequest);

        if (empty($soapRequest)) {
            Log::error('Le contenu SOAP est vide !');
            return response('Empty request', 400);
        }

        // Test de la requête SOAP reçu par l'opérateur partenaire
        try {
            $dom = new DOMDocument();
            $dom->loadXML($soapRequest);
            if (!$dom) {
                throw new \Exception("Réponse XML invalide !");
            }
        } catch (\Exception $e) {
            Log::error("Erreur lors du chargement du XML : " . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors du traitement de la réponse XML.');
        }

        // Création d'un objet XPath pour interroger le XML et faciliter la recherche d'éléments spécifiques dans la structure XML
        $xpath = new DOMXPath($dom);

        // Enregistrer les namespaces pour pouvoir interroger avec les préfixes
        $xpath->registerNamespace('soapenv', 'http://schemas.xmlsoap.org/soap/envelope/');
        $xpath->registerNamespace('b2w', 'http://b2w.banktowallet.com/b2w');

        // Requête XPath pour récupérer tous les éléments sous 'Body' (ou l'élément racine)
        $xpathQuery = "//soapenv:Body//*[local-name()='GetAccountBalance' or local-name()='AccountToWalletTransfer' or local-name()='WalletToAccountTransfer' or local-name()='GetMiniStatement' or local-name()='TransferStatusInquiry' or local-name()='CancelTransfer']";  // Cherche les éléments spécifiques

        $nodeList = $xpath->query($xpathQuery);
        $elementRequest = "";
        foreach ($nodeList as $node) {
            $elements = $node->nodeName;
            $elementRequest = explode(":", $elements)[1];
        }

        Log::info('The element request recieved is : ' . $elementRequest);

        $get_account = "";

        // Si $elementRequest === "CancelTransfert" annulation du dernier transfert après erreur côté Orange
        if ($elementRequest === "CancelTransfer") {

            Log::info('the Element Request sent is : ' . $elementRequest);


            $testNode = $xpath->query("//soapenv:Body/*[local-name()='CancelTransfer']");
            if ($testNode->length == 0) {
                Log::info('Une erreur est survenue : ' . $testNode);
            } else {
                $fields = [
                    'operatorCode',
                    'requestId',
                    'requestToken',
                    'requestType',
                    'affiliateCode',
                    'externalRefNo'
                ];
                $data = [];
            }
            foreach ($fields as $field) {
                $xpathQuery = "//soapenv:Body/b2w:CancelTransfer/TranRequestInfo/mmHeaderInfo/$field 
                    | //soapenv:Body/b2w:CancelTransfer/TranRequestInfo/$field";
                $nodeList = $xpath->query($xpathQuery);
                $data[$field] = ($nodeList->length > 0) ? trim($nodeList->item(0)->nodeValue) : 'Indisponible';
            }

            $externalRefNo = $data['externalRefNo'];

            // A partir de externalRefNo on récupère le compte associé
            $get_account = DB::table('transaction')
                ->select('client_id', 'client_lastname', 'client_firstname', 'musoni_account_no', 'libelle', 'amount', 'alias', 'msisdn', 'office_name', 'bank_agent', 'resourceId', 'officeId', 'clientId', 'savingId', 'charge_id')
                ->where('TransactionId', $externalRefNo)
                ->first();

            // Récupéré les informations de la transaction
            $account_no = $get_account->musoni_account_no ?? '';
            $amount = $get_account->amount ?? 0;
            $resourceId = $get_account->resourceId ?? 0;
            $officeId = $get_account->officeId ?? 0;
            $clientId = $get_account->clientId ?? 0;
            $savingId = $get_account->savingId ?? 0;
            $chargeId = $get_account->charge_id ?? 0;

            // Lancer la requete à n8n pour annuler le transfert
            if ($resourceId && $officeId && $clientId && $savingId && $chargeId) {
                $cancel_transfer_url = 'https://acepmg.it4life.org/webhook/cancel_transfert?compte=' . $account_no . '&trasanctionId=' . $resourceId . '&officeId=' . $officeId . '&clientId=' . $clientId . '&savingId=' . $savingId . '&chargeId=' . $chargeId . '&amount=' . $amount;
                $response = Http::withBasicAuth($username, $password)
                    ->withoutVerifying()
                    ->get($cancel_transfer_url);

                if ($response->successful()) {
                    $clientData = $response->json();
                    if (isset($clientData[0])) {
                        $dataItem = $clientData[0];
                    } else {
                        $dataItem = $clientData;
                    }
                    $responseCode = $dataItem['error_code'] ?? "";
                    $responseMessage = $dataItem['error_message'] ?? "";
                    $resourceId = $dataItem['resourceId'] ?? 0;
                    $officeId = $dataItem['officeId'] ?? 0;
                    $clientId = $dataItem['clientId'] ?? 0;
                    $savingId = $dataItem['savingsId'] ?? 0;
                    $reference_cancel = $dataItem['reference_cancel'] ?? 0;
                    $externalRefNo = $dataItem['resourceId'] ?? 0;

                    return $this->cancelTransfer($data, $get_account, $responseCode, $responseMessage, $externalRefNo, $resourceId, $officeId, $clientId, $savingId, $chargeId, $reference_cancel);
                }
            }
        } // Si $elementRequest === "GetAccountBalance"
        elseif ($elementRequest == "GetAccountBalance") {
            // Requête XPath pour chercher spécifiquement l'élément "GetAccountBalance"
            $testNode = $xpath->query("//soapenv:Body/*[local-name()='GetAccountBalance']");
            // Si $testNode->length == 0 on retourne log::info
            if ($testNode->length == 0) {
                Log::info('Une erreur est survenue : ' . $testNode);
            } else {
                // Extraire les iformations dans la requête xml de GetMiniStatement
                $fields = [
                    'operatorCode',
                    'requestId',
                    'requestType',
                    'affiliateCode',
                    'accountAlias',
                ];
                $data = [];

                // Boucle sur les champs pour les extraire
                foreach ($fields as $field) {
                    $xpathQuery = "//soapenv:Body/b2w:GetAccountBalance/AccountBalanceInquiryRequest/mmHeaderInfo/$field 
                        | //soapenv:Body/b2w:GetAccountBalance/AccountBalanceInquiryRequest/$field";
                    $nodeList = $xpath->query($xpathQuery);
                    $data[$field] = ($nodeList->length > 0) ? trim($nodeList->item(0)->nodeValue) : 'Indisponible';
                }

                foreach ($data as $key => $value) {
                    if ($key === "accountAlias") {
                        $get_account = DB::table('subscription')
                            ->select('client_id', 'client_lastname', 'client_firstname', 'account_no', 'libelle', 'alias', 'msisdn', 'officeName', 'account_status')
                            ->where('alias', $value)
                            ->first();

                        $account_no = $get_account->account_no;

                        if ($get_account->account_status === "0") {
                            Log::info('Le compte ' . $account_no . 'est déjà résilié');
                        } else {
                            // pass the url to n8n webhook
                            $savings_accounts_url = 'https://acepmg.it4life.org/webhook/getAccBal?compte=' . $account_no . '&date=' . $balance_dateNow;

                            $response = Http::withBasicAuth($username, $password)
                                ->withoutVerifying()
                                ->get($savings_accounts_url);

                            $clientData = $response->json();
                            if (isset($clientData[0])) {
                                $dataItem = $clientData[0];
                            } else {
                                $dataItem = $clientData;
                            }

                            $responseCode = $dataItem['error_code'] ?? "";
                            $responseMessage = $dataItem['error_msg'] ?? "";
                            $client_account_balance = $dataItem['balance'] ?? 0;

                            $resourceId = $dataItem['resourceId'] ?? 0;
                            $officeId = $dataItem['officeId'] ?? 0;
                            $clientId = $dataItem['clientId'] ?? 0;
                            $savingId = $dataItem['savingId'] ?? 0;
                            $chargeId = $dataItem['chargeId'] ?? 0;
                            $reference_cancel = $dataItem['reference_cancel'] ?? '';

                            if ($responseCode === 'E16') {
                                $client_account_balance = 0;
                            }

                            return $this->balanceResponseToOrange(
                                $data,
                                $get_account,
                                $responseCode,
                                $responseMessage,
                                $client_account_balance,
                                $resourceId,
                                $officeId,
                                $clientId,
                                $savingId,
                                $chargeId,
                                $reference_cancel
                            );
                        }
                    }
                }
            }
        } // Si $elementRequest === "AccountToWalletTransfer" Bank to wallet -> WITHDRAWAL
        elseif ($elementRequest === "AccountToWalletTransfer") {
            $testNode = $xpath->query("//soapenv:Body/*[local-name()='AccountToWalletTransfer']");

            if ($testNode->length == 0) {
                Log::info('Une erreur est survenue : ' . $testNode);
            } else {
                $fields = [
                    'operatorCode',
                    'requestId',
                    'requestToken',
                    'requestType',
                    'affiliateCode',
                    'externalRefNo',
                    'mobileNo',
                    'mobileName',
                    'mobileAlias',
                    'accountNo',
                    'accountAlias',
                    'accountName',
                    'transferDescription',
                    'ccy',
                    'amount',
                    'charge',
                    'tranDate',
                    'udf1',
                    'udf2',
                    'udf3',
                ];
                $data = [];

                foreach ($fields as $field) {
                    $xpathQuery = "//soapenv:Body/b2w:AccountToWalletTransfer/MobileTransferRequest/mmHeaderInfo/$field 
                        | //soapenv:Body/b2w:AccountToWalletTransfer/MobileTransferRequest/$field";
                    $nodeList = $xpath->query($xpathQuery);
                    $data[$field] = ($nodeList->length > 0) ? trim($nodeList->item(0)->nodeValue) : 'Indisponible';
                }


                $alias = $data['accountAlias'];
                $amount = $data['amount'];
                $receipt = "B2W" . $data['requestId'];

                $get_account = DB::table('subscription')
                    ->select('client_id', 'client_lastname', 'client_firstname', 'account_no', 'libelle', 'alias', 'msisdn', 'officeName', 'bank_agent', 'account_status')
                    ->where('alias', $alias)
                    ->first();

                $account_no = $get_account->account_no;

                if ($get_account->account_status === 0) {
                    dd('Le compte ' . $get_account . 'est déjà résilié');
                } else {
                    $withdraw_url = "https://acepmg.it4life.org/webhook/withdraw_account?compte=" . $account_no . "&amount=" . $amount . "&date=" . $withdraw_dateNow . "&receipt=" . $receipt;

                    $response = Http::withBasicAuth($username, $password)
                        ->withoutVerifying()
                        ->get($withdraw_url);

                    $clientData = $response->json();
                    if (!empty($clientData) && isset($clientData[0]) && isset($clientData[0]['client_data'])) {
                        // Si la réponse contient des données client
                        $responseCode = $clientData[0]['error_code'] ?? 'UNKNOWN';
                        $responseMessage = $clientData[0]['error_message'] ?? 'No message';
                        $charges = $clientData[0]['client_data']['charges'];
                        $transaction_id = $clientData[0]['resourceId'];

                        // --------- Data for cancel transfer -----------
                        $resourceId = $clientData[0]['resourceId'] ?? 0;
                        $officeId = $clientData[0]['charge_officeId'] ?? 0;
                        $clientId = $clientData[0]['charge_clientId'] ?? 0;
                        $savingId = $clientData[0]['charge_savingsId'] ?? 0;
                        $chargeId = $clientData[0]['charge_resourceId'] ?? 0;

                        $CBAReferenceNo = $clientData[0]['resourceId'] ?? 0;

                        $externalRefNo = $clientData[0]['resourceId'];
                    } else {
                        $responseCode = $clientData[0]['error_code'] ?? 'UNKNOWN';
                        $responseMessage = $clientData[0]['error_msg'] ?? 'No error message';
                        $charges = 0;
                        $transaction_id = 0;
                        // --------- Data for cancel transfer -----------
                        $resourceId = $clientData[0]['resourceId'] ?? 0;
                        $officeId = $clientData[0]['charge_officeId'] ?? 0;
                        $clientId = $clientData[0]['charge_clientId'] ?? 0;
                        $savingId = $clientData[0]['charge_savingsId'] ?? 0;
                        $chargeId = $clientData[0]['charge_resourceId'] ?? 0;

                        $externalRefNo = $clientData[0]['resourceId'] ?? 0;
                        $CBAReferenceNo = $clientData[0]['resourceId'] ?? 0;
                    }

                    return $this->bankToWallet($data, $get_account, $responseCode, $responseMessage, $externalRefNo, $charges, $transaction_id, $CBAReferenceNo, $resourceId, $officeId, $clientId, $savingId, $chargeId);
                }
            }
        } // Si $elementRequest === "WalletToAccountTransfer" wallet to bank -> DEPOSIT
        elseif ($elementRequest === "WalletToAccountTransfer") {
            // Requête XPath pour chercher spécifiquement l'élément "WalletToAccountTransfer"
            $testNode = $xpath->query("//soapenv:Body/*[local-name()='WalletToAccountTransfer']");

            if ($testNode->length == 0) {
                Log::info('Une erreur est survenue : ' . $testNode);
            } else {
                // Extraire les iformations dans la requête xml de GetMiniStatement
                $fields =
                    [
                        'operatorCode',
                        'requestId',
                        'requestToken',
                        'requestType',
                        'affiliateCode',
                        'externalRefNo',
                        'mobileNo',
                        'mobileName',
                        'mobileAlias',
                        'accountNo',
                        'accountAlias',
                        'accountName',
                        'transferDescription',
                        'ccy',
                        'amount',
                        'charge',
                        'tranDate',
                        'udf1',
                        'udf2',
                        'udf3',
                    ];
                $data = [];
            }

            foreach ($fields as $field) {
                $xpathQuery = "//soapenv:Body/b2w:WalletToAccountTransfer/MobileTransferRequest/mmHeaderInfo/$field 
                    | //soapenv:Body/b2w:WalletToAccountTransfer/MobileTransferRequest/$field";
                $nodeList = $xpath->query($xpathQuery);
                $data[$field] = ($nodeList->length > 0) ? trim($nodeList->item(0)->nodeValue) : 'Indisponible';
            }

            $alias = $data['accountAlias'];
            $amount = $data['amount'];
            $receipt = $data['requestId'];
            $externalRefNo = $data['externalRefNo'];

            $get_account = DB::table('subscription')
                ->select('client_id', 'client_lastname', 'client_firstname', 'account_no', 'libelle', 'alias', 'msisdn', 'officeName', 'bank_agent', 'account_status')
                ->where('alias', $alias)
                ->first();

            $account_no = $get_account->account_no;

            if ($get_account->account_status === "0") {
                Log::info('Le compte ' . $get_account . 'est déjà résilié');
            } else {
                $deposit_url = "https://acepmg.it4life.org/webhook/deposit_account?compte=" . $account_no . "&amount=" . $amount . "&date=" . $deposit_dateNow . "&receipt=" . $receipt;
                $response = Http::withBasicAuth($username, $password)
                    ->withoutVerifying()
                    ->get($deposit_url);

                $clientData = $response->json();

                Log::info('Données JSON du client récupérées :', ['clientData' => $clientData]);


                if (!$response->successful()) {
                    $responseCode = "300";
                    dd('Client non trouvé');
                }


                if (isset($response['clientId'])) {
                    $responseCode = "000";
                    $responseMessage = "Success";
                    $transaction_id = $response['savingsAccountTransactionId'];
                    $externalRefNo = $data['externalRefNo'];

                    // --------- Data for cancel transfer -----------
                    $resourceId = $response['resourceId'] ?? 0;
                    $officeId = $response['officeId'] ?? 0;
                    $clientId = $response['clientId'] ?? 0;
                    $savingId = $response['savingsId'] ?? 0;
                    $chargeId = $response['charge_resourceId'] ?? 0;
                    $CBAReferenceNo = $externalRefNo;

                    return $this->walletTobank($data, $get_account, $responseCode, $responseMessage, $externalRefNo, $transaction_id, $CBAReferenceNo, $resourceId, $officeId, $clientId, $savingId, $chargeId);
                } else {
                    $responseCode = $clientData[0]['error_code'] ?? 'UNKNOWN';
                    $responseMessage = $clientData[0]['error_msg'] ?? 'UNKNOWN';
                    $transaction_id =  $clientData[0]['resource_id'] ?? 0;
                    $externalRefNo = $clientData[0]['resource_id'] ?? 0;
                    Log::info(('la response code est  : ' . $responseCode . ' et la response message est : ' . $responseMessage));
                    // --------- Data for cancel transfer -----------
                    $resourceId = $response['resourceId'] ?? 0;
                    $officeId = $response['officeId'] ?? 0;
                    $clientId = $response['clientId'] ?? 0;
                    $savingId = $response['savingsId'] ?? 0;
                    $chargeId = $response['charge_resourceId'] ?? 0;
                    $CBAReferenceNo = $externalRefNo;

                    return $this->walletTobank($data, $get_account, $responseCode, $responseMessage, $externalRefNo, $transaction_id, $CBAReferenceNo, $resourceId, $officeId, $clientId, $savingId, $chargeId);
                }
            }
        } // Si $elementRequest === "GetMiniStatement" bank to wallet por le mini relevé 
        elseif ($elementRequest === "GetMiniStatement") {
            $testNode = $xpath->query("//soapenv:Body/*[local-name()='GetMiniStatement']");
            Log::info('La requete est : ' . $elementRequest);

            if ($testNode->length == 0) {
                Log::info('Une erreur est survenue : ' . $testNode);
            } else {
                $fields = [
                    'operatorCode',
                    'requestId',
                    'requestToken',
                    'requestType',
                    'affiliateCode',
                    'accountNo',
                    'accountAlias',
                    'accountName',
                    'reason',
                ];
                $data = [];
            }

            foreach ($fields as $field) {
                $xpathQuery = "//soapenv:Body/b2w:GetMiniStatement/MiniStatementRequest/mmHeaderInfo/$field 
                    | //soapenv:Body/b2w:GetMiniStatement/MiniStatementRequest/$field";
                $nodeList = $xpath->query($xpathQuery);
                $data[$field] = ($nodeList->length > 0) ? trim($nodeList->item(0)->nodeValue) : 'Indisponible';
            }

            $alias = $data['accountAlias'];
            $get_account = DB::table('subscription')
                ->select(
                    'client_id',
                    'client_lastname',
                    'client_firstname',
                    'account_no',
                    'libelle',
                    'alias',
                    'msisdn',
                    'officeName',
                    'bank_agent',
                    'account_status'
                )
                ->where('alias', $alias)
                ->first();

            $account_no = $get_account->account_no;

            if ($get_account->account_status === "0") {
                Log::info('Le compte ' . $get_account . 'est déjà résilié');
            } else {
                $statement_url = "https://acepmg.it4life.org/webhook/statement_account?compte=" . $account_no . '&date=' . $balance_dateNow;
                Log::info('URL is : ' . $statement_url);

                $response = Http::withBasicAuth($username, $password)
                    ->withoutVerifying()
                    ->get($statement_url);

                $clientData = $response->json();

                // Vérification de la structure
                if (isset($clientData['data']['error_code'])) {
                    // if success
                    $responseCode = $clientData['data']['error_code'];
                    $responseMessage = $clientData['data']['error_msg'];
                    $transactions = $clientData['transactions'];
                } elseif (isset($clientData[0]['data']['error_code'])) {
                    // if errors
                    $responseCode = $response[0]['data']['error_code'];
                    $responseMessage = $response[0]['data']['error_msg'];
                    $transactions = $response[0]['transactions'];
                }
                return $this->statementResponseToOrange($data, $get_account, $responseCode, $responseMessage, $transactions);
            }
        } // Si $elementRequest === "TransferStatusInquiry" return information à propos du dernier transfert
        elseif ($elementRequest === "TransferStatusInquiry") {
            $testNode = $xpath->query("//soapenv:Body/*[local-name()='TransferStatusInquiry']");
            Log::info('La requete est : ' . $elementRequest);

            if ($testNode->length == 0) {
                Log::info('Une erreur est survenue : ' . $testNode);
            } else {
                $fields = [
                    'operatorCode',
                    'requestId',
                    'requestToken',
                    'requestType',
                    'affiliateCode',
                    'externalRefNo'
                ];
                $data = [];
            }
            foreach ($fields as $field) {
                $xpathQuery = "//soapenv:Body/b2w:TransferStatusInquiry/TranRequestInfo/mmHeaderInfo/$field 
                    | //soapenv:Body/b2w:TransferStatusInquiry/TranRequestInfo/$field";
                $nodeList = $xpath->query($xpathQuery);
                $data[$field] = ($nodeList->length > 0) ? trim($nodeList->item(0)->nodeValue) : 'Indisponible';
            }

            $externalRefNo = $data['externalRefNo'];
            $operatorCode = $data['operatorCode'];
            $requestId = $data['requestId'];
            $affiliateCode = $data['affiliateCode'];

            // get transactionIf from table transaction by $externalRefNo

            $get_transaction_id = DB::table('transaction')
                ->select('TransactionId')
                ->where('external_ref_no', $externalRefNo)
                ->first();

            Log::info('Transaction ID récupéré : ', ['data' => $get_transaction_id]);

            if (!isset($get_transaction_id)) {
                $responseCode = "E01";
                $responseMessage = "Transaction not found";

                $orangeResponse =
                    '<?xml version="1.0" encoding="UTF-8"?> 
                <S:Envelope xmlns:S="http://schemas.xmlsoap.org/soap/envelope/"> 
                    <S:Body> 
                        <ns2:TransferStatusInquiryResponse xmlns:ns2="http://b2w.banktowallet.com/b2w"> 
                            <return> 
                                <mmHeaderInfo> 
                                    <operatorCode>' . $operatorCode . '</operatorCode> 
                                    <requestId>' . $requestId . '</requestId> 
                                    <affiliateCode>' . $affiliateCode . '</affiliateCode> 
                                    <responseCode>' . $responseCode . '</responseCode> 
                                    <responseMessage>' . $responseMessage . '</responseMessage> 
                                </mmHeaderInfo> 
                            </return> 
                        </ns2:TransferStatusInquiryResponse> 
                    </S:Body> 
                </S:Envelope>';

                Log::info('Response to orange is : ' . $orangeResponse);
            } else {
                Log::info('ID de la transaction est : ' . $get_transaction_id->TransactionId);
                $responseCode = "000";
                $responseMessage = "Success";

                $orangeResponse =
                    '<?xml version="1.0" encoding="UTF-8"?> 
                <S:Envelope xmlns:S="http://schemas.xmlsoap.org/soap/envelope/"> 
                    <S:Body> 
                        <ns2:TransferStatusInquiryResponse xmlns:ns2="http://b2w.banktowallet.com/b2w"> 
                            <return> 
                                <mmHeaderInfo> 
                                    <operatorCode>' . $operatorCode . '</operatorCode> 
                                    <requestId>' . $requestId . '</requestId> 
                                    <affiliateCode>' . $affiliateCode . '</affiliateCode> 
                                    <responseCode>' . $responseCode . '</responseCode> 
                                    <responseMessage>' . $responseMessage . '</responseMessage> 
                                </mmHeaderInfo> 
                            </return> 
                        </ns2:TransferStatusInquiryResponse> 
                    </S:Body> 
                </S:Envelope>';
                Log::info('Response to orange is : ' . $orangeResponse);
            }

            return response($orangeResponse, 200)
                ->header('Content-Type', 'text/xml; charset=utf-8');
        } // Si $elementRequest === "CancelTransfert" annulation du dernier transfert après erreur côté Orange

    }
}
