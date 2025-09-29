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
    // ========================================
    // CONSTANTES DE CONFIGURATION
    // ========================================
    
    // URLs des webhooks N8N
    private const N8N_BASE_URL = 'https://acepmg.it4life.org/webhook';
    private const N8N_ENDPOINTS = [
        'cancel_transfert' => '/cancel_transfert',
        'get_account_balance' => '/getAccBal',
        'withdraw_account' => '/withdraw_account',
        'deposit_account' => '/deposit_account',
        'statement_account' => '/statement_account'
    ];
    
    // Codes de réponse
    private const RESPONSE_CODES = [
        'SUCCESS' => '000',
        'TRANSACTION_NOT_FOUND' => 'E01',
        'NO_DATA' => 'E16',
        'PENDING' => 'E11',
        'CLIENT_NOT_FOUND' => '300'
    ];
    
    // Types de transactions
    private const TRANSACTION_TYPES = [
        'DEPOSIT' => 'Deposit',
        'WITHDRAWAL' => 'Withdrawal',
        'PAY_CHARGE' => 'Pay Charge',
        'WAIVE_CHARGE' => 'Waive Charge'
    ];
    
    // Types de requêtes SOAP
    private const SOAP_REQUESTS = [
        'GET_ACCOUNT_BALANCE' => 'GetAccountBalance',
        'ACCOUNT_TO_WALLET' => 'AccountToWalletTransfer',
        'WALLET_TO_ACCOUNT' => 'WalletToAccountTransfer',
        'GET_MINI_STATEMENT' => 'GetMiniStatement',
        'TRANSFER_STATUS_INQUIRY' => 'TransferStatusInquiry',
        'CANCEL_TRANSFER' => 'CancelTransfer'
    ];
    
    // ========================================
    // MÉTHODES PUBLIQUES
    // ========================================
    
    /**
     * Affiche la page d'accueil de test
     */
    public function showRequest()
    {
        return view('taratra.BankToWallet.soap');
    }
    
    /**
     * Méthode principale pour gérer les requêtes SOAP
     */
    public function handle(Request $request)
    {
        Log::info('Requête SOAP reçue', ['content' => $request->getContent()]);
        
        try {
            // Parser la requête SOAP
            $soapData = $this->parseSoapRequest($request->getContent());
            if (!$soapData) {
                throw new \Exception("Impossible de parser la requête SOAP");
            }
            
            // Traiter selon le type de requête
            return $this->processRequest($soapData);
            
        } catch (\Exception $e) {
            Log::error("Erreur lors du traitement de la requête SOAP", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response($this->buildErrorResponse($e->getMessage()), 500)
                ->header('Content-Type', 'text/xml; charset=utf-8');
        }
    }
    
    // ========================================
    // MÉTHODES PRIVÉES - PARSING SOAP
    // ========================================
    
    /**
     * Parse la requête SOAP et extrait les données
     */
    private function parseSoapRequest(string $soapContent): ?array
    {
        try {
            $dom = new DOMDocument();
            $dom->loadXML($soapContent);
            
            if (!$dom) {
                throw new \Exception("Document XML invalide");
            }
            
            $xpath = new DOMXPath($dom);
            $xpath->registerNamespace('soapenv', 'http://schemas.xmlsoap.org/soap/envelope/');
            $xpath->registerNamespace('b2w', 'http://b2w.banktowallet.com/b2w');
            
            // Détecter le type de requête
            $requestType = $this->detectRequestType($xpath);
            if (!$requestType) {
                throw new \Exception("Type de requête non reconnu");
            }
            
            // Extraire les données selon le type
            $data = $this->extractRequestData($xpath, $requestType);
            
            return [
                'type' => $requestType,
                'data' => $data
            ];
            
        } catch (\Exception $e) {
            Log::error("Erreur lors du parsing SOAP", ['error' => $e->getMessage()]);
            return null;
        }
    }
    
    /**
     * Détecte le type de requête SOAP
     */
    private function detectRequestType(DOMXPath $xpath): ?string
    {
        $query = "//soapenv:Body//*[local-name()='GetAccountBalance' or local-name()='AccountToWalletTransfer' or local-name()='WalletToAccountTransfer' or local-name()='GetMiniStatement' or local-name()='TransferStatusInquiry' or local-name()='CancelTransfer']";
        
        $nodeList = $xpath->query($query);
        if ($nodeList->length === 0) {
            return null;
        }
        
        $nodeName = $nodeList->item(0)->nodeName;
        return explode(":", $nodeName)[1] ?? null;
    }
    
    /**
     * Extrait les données de la requête selon le type
     */
    private function extractRequestData(DOMXPath $xpath, string $requestType): array
    {
        $fields = $this->getFieldsForRequestType($requestType);
        $data = [];
        
        foreach ($fields as $field) {
            $query = $this->buildXPathQuery($requestType, $field);
            $nodeList = $xpath->query($query);
            $data[$field] = ($nodeList->length > 0) ? trim($nodeList->item(0)->nodeValue) : 'Indisponible';
        }
        
        return $data;
    }
    
    /**
     * Retourne les champs à extraire selon le type de requête
     */
    private function getFieldsForRequestType(string $requestType): array
    {
        $fieldsMap = [
            self::SOAP_REQUESTS['GET_ACCOUNT_BALANCE'] => [
                'operatorCode', 'requestId', 'requestType', 'affiliateCode', 'accountAlias'
            ],
            self::SOAP_REQUESTS['ACCOUNT_TO_WALLET'] => [
                'operatorCode', 'requestId', 'requestToken', 'requestType', 'affiliateCode',
                'externalRefNo', 'mobileNo', 'mobileName', 'mobileAlias', 'accountNo',
                'accountAlias', 'accountName', 'transferDescription', 'ccy', 'amount',
                'charge', 'tranDate', 'udf1', 'udf2', 'udf3'
            ],
            self::SOAP_REQUESTS['WALLET_TO_ACCOUNT'] => [
                'operatorCode', 'requestId', 'requestToken', 'requestType', 'affiliateCode',
                'externalRefNo', 'mobileNo', 'mobileName', 'mobileAlias', 'accountNo',
                'accountAlias', 'accountName', 'transferDescription', 'ccy', 'amount',
                'charge', 'tranDate', 'udf1', 'udf2', 'udf3'
            ],
            self::SOAP_REQUESTS['GET_MINI_STATEMENT'] => [
                'operatorCode', 'requestId', 'requestToken', 'requestType', 'affiliateCode',
                'accountNo', 'accountAlias', 'accountName', 'reason'
            ],
            self::SOAP_REQUESTS['TRANSFER_STATUS_INQUIRY'] => [
                'operatorCode', 'requestId', 'requestToken', 'requestType', 'affiliateCode', 'externalRefNo'
            ],
            self::SOAP_REQUESTS['CANCEL_TRANSFER'] => [
                'operatorCode', 'requestId', 'requestToken', 'requestType', 'affiliateCode', 'externalRefNo'
            ]
        ];
        
        return $fieldsMap[$requestType] ?? [];
    }
    
    /**
     * Construit la requête XPath selon le type et le champ
     */
    private function buildXPathQuery(string $requestType, string $field): string
    {
        $requestMap = [
            self::SOAP_REQUESTS['GET_ACCOUNT_BALANCE'] => 'GetAccountBalance/AccountBalanceInquiryRequest',
            self::SOAP_REQUESTS['ACCOUNT_TO_WALLET'] => 'AccountToWalletTransfer/MobileTransferRequest',
            self::SOAP_REQUESTS['WALLET_TO_ACCOUNT'] => 'WalletToAccountTransfer/MobileTransferRequest',
            self::SOAP_REQUESTS['GET_MINI_STATEMENT'] => 'GetMiniStatement/MiniStatementRequest',
            self::SOAP_REQUESTS['TRANSFER_STATUS_INQUIRY'] => 'TransferStatusInquiry/TranRequestInfo',
            self::SOAP_REQUESTS['CANCEL_TRANSFER'] => 'CancelTransfer/TranRequestInfo'
        ];
        
        $requestPath = $requestMap[$requestType] ?? '';
        return "//soapenv:Body/b2w:$requestPath/mmHeaderInfo/$field | //soapenv:Body/b2w:$requestPath/$field";
    }
    
    // ========================================
    // MÉTHODES PRIVÉES - TRAITEMENT DES REQUÊTES
    // ========================================
    
    /**
     * Traite la requête selon son type
     */
    private function processRequest(array $soapData)
    {
        $requestType = $soapData['type'];
        $data = $soapData['data'];
        
        switch ($requestType) {
            case self::SOAP_REQUESTS['GET_ACCOUNT_BALANCE']:
                return $this->handleGetAccountBalance($data);
                
            case self::SOAP_REQUESTS['ACCOUNT_TO_WALLET']:
                return $this->handleAccountToWallet($data);
                
            case self::SOAP_REQUESTS['WALLET_TO_ACCOUNT']:
                return $this->handleWalletToAccount($data);
                
            case self::SOAP_REQUESTS['GET_MINI_STATEMENT']:
                return $this->handleGetMiniStatement($data);
                
            case self::SOAP_REQUESTS['TRANSFER_STATUS_INQUIRY']:
                return $this->handleTransferStatusInquiry($data);
                
            case self::SOAP_REQUESTS['CANCEL_TRANSFER']:
                return $this->handleCancelTransfer($data);
                
            default:
                throw new \Exception("Type de requête non supporté: $requestType");
        }
    }
    
    // ========================================
    // MÉTHODES PRIVÉES - GESTION DES TYPES DE REQUÊTES
    // ========================================
    
    /**
     * Gère la requête GetAccountBalance
     */
    private function handleGetAccountBalance(array $data)
    {
        $account = $this->getAccountByAlias($data['accountAlias']);
        if (!$account || $account->account_status === "0") {
            throw new \Exception("Compte non trouvé ou déjà résilié");
        }
        
        $balanceData = $this->callN8nWebhook('get_account_balance', [
            'compte' => $account->account_no,
            'date' => Carbon::now()->format('d F Y')
        ]);
        
        return $this->buildBalanceResponse($data, $account, $balanceData);
    }
    
    /**
     * Gère la requête AccountToWalletTransfer (Retrait)
     */
    private function handleAccountToWallet(array $data)
    {
        $account = $this->getAccountByAlias($data['accountAlias']);
        if (!$account || $account->account_status === "0") {
            throw new \Exception("Compte non trouvé ou déjà résilié");
        }
        
        $receipt = "B2W" . $data['requestId'];
        $withdrawData = $this->callN8nWebhook('withdraw_account', [
            'compte' => $account->account_no,
            'amount' => $data['amount'],
            'date' => Carbon::now()->format('d m Y'),
            'receipt' => $receipt
        ]);
        
        return $this->buildBankToWalletResponse($data, $account, $withdrawData);
    }
    
    /**
     * Gère la requête WalletToAccountTransfer (Dépôt)
     */
    private function handleWalletToAccount(array $data)
    {
        $account = $this->getAccountByAlias($data['accountAlias']);
        if (!$account || $account->account_status === "0") {
            throw new \Exception("Compte non trouvé ou déjà résilié");
        }
        
        $receipt = $data['requestId'];
        $depositData = $this->callN8nWebhook('deposit_account', [
            'compte' => $account->account_no,
            'amount' => $data['amount'],
            'date' => Carbon::now()->format('d M Y'),
            'receipt' => $receipt
        ]);
        
        return $this->buildWalletToBankResponse($data, $account, $depositData);
    }
    
    /**
     * Gère la requête GetMiniStatement
     */
    private function handleGetMiniStatement(array $data)
    {
        $account = $this->getAccountByAlias($data['accountAlias']);
        if (!$account || $account->account_status === "0") {
            throw new \Exception("Compte non trouvé ou déjà résilié");
        }
        
        $statementData = $this->callN8nWebhook('statement_account', [
            'compte' => $account->account_no,
            'date' => Carbon::now()->format('d F Y')
        ]);
        
        return $this->buildStatementResponse($data, $account, $statementData);
    }
    
    /**
     * Gère la requête TransferStatusInquiry
     */
    private function handleTransferStatusInquiry(array $data)
    {
        $transaction = DB::table('transaction')
            ->select('TransactionId')
            ->where('external_ref_no', $data['externalRefNo'])
            ->first();
        
        if (!$transaction) {
            return $this->buildTransferStatusResponse($data, self::RESPONSE_CODES['TRANSACTION_NOT_FOUND'], "Transaction not found");
        }
        
        return $this->buildTransferStatusResponse($data, self::RESPONSE_CODES['SUCCESS'], "Success");
    }
    
    /**
     * Gère la requête CancelTransfer
     */
    private function handleCancelTransfer(array $data)
    {
        $transaction = DB::table('transaction')
            ->select('client_id', 'client_lastname', 'client_firstname', 'musoni_account_no', 'libelle', 'amount', 'alias', 'msisdn', 'office_name', 'bank_agent', 'resourceId', 'officeId', 'clientId', 'savingId', 'charge_id')
            ->where('TransactionId', $data['externalRefNo'])
            ->first();
        
        if (!$transaction) {
            throw new \Exception("Transaction non trouvée");
        }
        
        $cancelData = $this->callN8nWebhook('cancel_transfert', [
            'compte' => $transaction->musoni_account_no,
            'trasanctionId' => $transaction->resourceId,
            'officeId' => $transaction->officeId,
            'clientId' => $transaction->clientId,
            'savingId' => $transaction->savingId,
            'chargeId' => $transaction->charge_id,
            'amount' => $transaction->amount
        ]);
        
        return $this->buildCancelTransferResponse($data, $transaction, $cancelData);
    }
    
    // ========================================
    // MÉTHODES PRIVÉES - UTILITAIRES
    // ========================================
    
    /**
     * Récupère un compte par son alias
     */
    private function getAccountByAlias(string $alias)
    {
        return DB::table('subscription')
            ->select('client_id', 'client_lastname', 'client_firstname', 'account_no', 'libelle', 'alias', 'msisdn', 'officeName', 'bank_agent', 'account_status')
            ->where('alias', $alias)
            ->first();
    }
    
    /**
     * Appelle un webhook N8N
     */
    private function callN8nWebhook(string $endpoint, array $params)
    {
        $username = config('app.n8n_username', env('N8N_USERNAME'));
        $password = config('app.n8n_password', env('N8N_PASSWORD'));
        
        if (!$username || !$password) {
            throw new \Exception("Identifiants N8N manquants");
        }
        
        $url = self::N8N_BASE_URL . self::N8N_ENDPOINTS[$endpoint] . '?' . http_build_query($params);
        
        Log::info("Appel webhook N8N", ['endpoint' => $endpoint, 'url' => $url]);
        
        $response = Http::withBasicAuth($username, $password)
            ->withoutVerifying()
            ->timeout(30)
            ->get($url);
        
        if (!$response->successful()) {
            throw new \Exception("Erreur lors de l'appel au webhook N8N: " . $response->status());
        }
        
        return $response->json();
    }
    
    // ========================================
    // MÉTHODES PRIVÉES - CONSTRUCTION DES RÉPONSES
    // ========================================
    
    /**
     * Construit la réponse pour GetAccountBalance
     */
    private function buildBalanceResponse(array $data, $account, $balanceData)
    {
        $responseCode = $balanceData[0]['error_code'] ?? self::RESPONSE_CODES['NO_DATA'];
        $responseMessage = $balanceData[0]['error_msg'] ?? "No data";
        $clientAccountBalance = $balanceData[0]['balance'] ?? 0;
        
        if ($responseCode === self::RESPONSE_CODES['NO_DATA']) {
            $clientAccountBalance = 0;
        }
        
        // Sauvegarder en base
        $this->saveAccountBalance($data, $account, $responseCode, $responseMessage);
        
        return $this->buildXmlResponse('GetAccountBalanceResponse', [
            'operatorCode' => $data['operatorCode'],
            'requestId' => $data['requestId'],
            'affiliateCode' => $data['affiliateCode'],
            'responseCode' => $responseCode,
            'responseMessage' => $responseMessage,
            'accountAlias' => $data['accountAlias'],
            'availableBalance' => $clientAccountBalance,
            'currentBalance' => $clientAccountBalance
        ]);
    }
    
    /**
     * Construit la réponse pour AccountToWalletTransfer
     */
    private function buildBankToWalletResponse(array $data, $account, $withdrawData)
    {
        $responseCode = $withdrawData[0]['error_code'] ?? 'UNKNOWN';
        $responseMessage = $withdrawData[0]['error_message'] ?? 'No message';
        $charges = $withdrawData[0]['client_data']['charges'] ?? 0;
        $transactionId = $withdrawData[0]['resourceId'] ?? 0;
        
        // Données pour l'annulation
        $resourceId = $withdrawData[0]['resourceId'] ?? 0;
        $officeId = $withdrawData[0]['charge_officeId'] ?? 0;
        $clientId = $withdrawData[0]['charge_clientId'] ?? 0;
        $savingId = $withdrawData[0]['charge_savingsId'] ?? 0;
        $chargeId = $withdrawData[0]['charge_resourceId'] ?? 0;
        
        $externalRefNo = $withdrawData[0]['resourceId'] ?? 0;
        $CBAReferenceNo = $withdrawData[0]['resourceId'] ?? 0;
        
        // Sauvegarder la transaction
        $this->saveTransaction($data, $account, $externalRefNo, $charges, $transactionId, $CBAReferenceNo, $resourceId, $officeId, $clientId, $savingId, $chargeId, $responseCode, $responseMessage);
        
        return $this->buildXmlResponse('AccountToWalletTransferResponse', [
            'operatorCode' => $data['operatorCode'],
            'requestId' => $data['requestId'],
            'affiliateCode' => $data['affiliateCode'],
            'responseCode' => $responseCode,
            'responseMessage' => $responseMessage,
            'externalRefNo' => $data['externalRefNo'],
            'CBAReferenceNo' => $CBAReferenceNo
        ]);
    }
    
    /**
     * Construit la réponse pour WalletToAccountTransfer
     */
    private function buildWalletToBankResponse(array $data, $account, $depositData)
    {
        if (isset($depositData['clientId'])) {
            $responseCode = self::RESPONSE_CODES['SUCCESS'];
            $responseMessage = "Success";
            $transactionId = $depositData['savingsAccountTransactionId'];
            $externalRefNo = $data['externalRefNo'];
            
            $resourceId = $depositData['resourceId'] ?? 0;
            $officeId = $depositData['officeId'] ?? 0;
            $clientId = $depositData['clientId'] ?? 0;
            $savingId = $depositData['savingsId'] ?? 0;
            $chargeId = $depositData['charge_resourceId'] ?? 0;
            $CBAReferenceNo = $externalRefNo;
        } else {
            $responseCode = $depositData[0]['error_code'] ?? 'UNKNOWN';
            $responseMessage = $depositData[0]['error_msg'] ?? 'UNKNOWN';
            $transactionId = $depositData[0]['resource_id'] ?? 0;
            $externalRefNo = $depositData[0]['resource_id'] ?? 0;
            
            $resourceId = $depositData['resourceId'] ?? 0;
            $officeId = $depositData['officeId'] ?? 0;
            $clientId = $depositData['clientId'] ?? 0;
            $savingId = $depositData['savingsId'] ?? 0;
            $chargeId = $depositData['charge_resourceId'] ?? 0;
            $CBAReferenceNo = $externalRefNo;
        }
        
        // Sauvegarder la transaction
        $this->saveTransaction($data, $account, $externalRefNo, $data['charge'], $transactionId, $CBAReferenceNo, $resourceId, $officeId, $clientId, $savingId, $chargeId, $responseCode, $responseMessage);
        
        return $this->buildXmlResponse('WalletToAccountTransferResponse', [
            'operatorCode' => $data['operatorCode'],
            'requestId' => $data['requestId'],
            'affiliateCode' => $data['affiliateCode'],
            'responseCode' => $responseCode,
            'responseMessage' => $responseMessage,
            'externalRefNo' => $externalRefNo,
            'CBAReferenceNo' => $CBAReferenceNo
        ]);
    }
    
    /**
     * Construit la réponse pour GetMiniStatement
     */
    private function buildStatementResponse(array $data, $account, $statementData)
    {
        if (isset($statementData['data']['error_code'])) {
            $responseCode = $statementData['data']['error_code'];
            $responseMessage = $statementData['data']['error_msg'];
            $transactions = $statementData['transactions'];
        } elseif (isset($statementData[0]['data']['error_code'])) {
            $responseCode = $statementData[0]['data']['error_code'];
            $responseMessage = $statementData[0]['data']['error_msg'];
            $transactions = $statementData[0]['transactions'];
        } else {
            $responseCode = self::RESPONSE_CODES['NO_DATA'];
            $responseMessage = "No data";
            $transactions = [];
        }
        
        // Sauvegarder en base
        $this->saveAccountBalance($data, $account, $responseCode, $responseMessage);
        
        return $this->buildStatementXmlResponse($data, $responseCode, $responseMessage, $transactions);
    }
    
    /**
     * Construit la réponse pour TransferStatusInquiry
     */
    private function buildTransferStatusResponse(array $data, string $responseCode, string $responseMessage)
    {
        return $this->buildXmlResponse('TransferStatusInquiryResponse', [
            'operatorCode' => $data['operatorCode'],
            'requestId' => $data['requestId'],
            'affiliateCode' => $data['affiliateCode'],
            'responseCode' => $responseCode,
            'responseMessage' => $responseMessage
        ]);
    }
    
    /**
     * Construit la réponse pour CancelTransfer
     */
    private function buildCancelTransferResponse(array $data, $transaction, $cancelData)
    {
        $responseCode = $cancelData[0]['error_code'] ?? "";
        $responseMessage = $cancelData[0]['error_message'] ?? "";
        $referenceCancel = $cancelData[0]['reference_cancel'] ?? 0;
        
        // Sauvegarder l'annulation
        $this->saveCancelTransaction($transaction, $responseCode, $responseMessage, $referenceCancel);
        
        return $this->buildXmlResponse('CancelTransferResponse', [
            'operatorCode' => $data['operatorCode'],
            'requestId' => $data['requestId'],
            'affiliateCode' => $data['affiliateCode'],
            'responseCode' => $responseCode,
            'responseMessage' => $responseMessage,
            'CBAReferenceNo' => $referenceCancel
        ]);
    }
    
    // ========================================
    // MÉTHODES PRIVÉES - SAUVEGARDE EN BASE
    // ========================================
    
    /**
     * Sauvegarde une consultation de solde
     */
    private function saveAccountBalance(array $data, $account, string $responseCode, string $responseMessage)
    {
        try {
            $balance = new AccountBalance();
            $balance->client_id = $account->client_id;
            $balance->client_lastname = $account->client_lastname;
            $balance->client_firstname = $account->client_firstname;
            $balance->musoni_account_no = $account->account_no;
            $balance->libelle = $account->libelle;
            $balance->alias = $account->alias;
            $balance->msisdn = $account->msisdn;
            $balance->operator_code = $data['operatorCode'];
            $balance->request_id = $data['requestId'];
            $balance->requestToken = $data['requestToken'] ?? "";
            $balance->request_type = $data['requestType'];
            $balance->affiliate_code = $data['affiliateCode'];
            $balance->reason = $data['reason'] ?? "";
            $balance->transaction_date = Carbon::now()->format('Y-m-d\TH:i:s');
            $balance->acep_responde_code = $responseCode;
            $balance->acep_responde_message = $responseMessage;
            $balance->office_name = $account->officeName;
            $balance->save();
            
            Log::info("Consultation de solde sauvegardée", ['account' => $account->account_no]);
        } catch (\Exception $e) {
            Log::error("Erreur lors de la sauvegarde de la consultation de solde", ['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Sauvegarde une transaction
     */
    private function saveTransaction(array $data, $account, $externalRefNo, $charges, $transactionId, $CBAReferenceNo, $resourceId, $officeId, $clientId, $savingId, $chargeId, string $responseCode, string $responseMessage)
    {
        try {
            $transaction = new Transactions();
            $transaction->client_id = $account->client_id;
            $transaction->client_lastname = $account->client_lastname;
            $transaction->client_firstname = $account->client_firstname;
            $transaction->musoni_account_no = $account->account_no;
            $transaction->libelle = $account->libelle;
            $transaction->alias = $account->alias;
            $transaction->msisdn = $account->msisdn;
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
            $transaction->office_name = $account->officeName;
            $transaction->bank_agent = $account->bank_agent;
            $transaction->TransactionId = $transactionId;
            $transaction->CBAReferenceNo = $CBAReferenceNo;
            $transaction->resourceId = $resourceId;
            $transaction->officeId = $officeId;
            $transaction->clientId = $clientId;
            $transaction->savingId = $savingId;
            $transaction->charge_id = $chargeId;
            $transaction->save();
            
            Log::info("Transaction sauvegardée", ['externalRefNo' => $externalRefNo]);
        } catch (\Exception $e) {
            Log::error("Erreur lors de la sauvegarde de la transaction", ['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Sauvegarde une annulation de transaction
     */
    private function saveCancelTransaction($transaction, string $responseCode, string $responseMessage, $referenceCancel)
    {
        try {
            $cancel = new CancelTrans();
            $cancel->client_id = $transaction->client_id;
            $cancel->client_lastname = $transaction->client_lastname;
            $cancel->client_firstname = $transaction->client_firstname;
            $cancel->musoni_account_no = $transaction->musoni_account_no;
            $cancel->libelle = $transaction->libelle;
            $cancel->amount = $transaction->amount;
            $cancel->alias = $transaction->alias;
            $cancel->msisdn = $transaction->msisdn;
            $cancel->office_name = $transaction->office_name;
            $cancel->bank_agent = $transaction->bank_agent;
            $cancel->resourceId = $transaction->resourceId ?? '';
            $cancel->officeId = $transaction->officeId ?? '';
            $cancel->clientId = $transaction->clientId ?? '';
            $cancel->savingId = $transaction->savingId ?? '';
            $cancel->error_code = $responseCode;
            $cancel->error_message = $responseMessage;
            $cancel->reference_cancel = $referenceCancel ?? '';
            $cancel->save();
            
            Log::info("Annulation de transaction sauvegardée", ['reference' => $referenceCancel]);
        } catch (\Exception $e) {
            Log::error("Erreur lors de la sauvegarde de l'annulation", ['error' => $e->getMessage()]);
        }
    }
    
    // ========================================
    // MÉTHODES PRIVÉES - CONSTRUCTION XML
    // ========================================
    
    /**
     * Construit une réponse XML générique
     */
    private function buildXmlResponse(string $responseType, array $data): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<S:Envelope xmlns:S="http://schemas.xmlsoap.org/soap/envelope/">' . "\n";
        $xml .= '    <S:Body>' . "\n";
        $xml .= "        <ns2:{$responseType} xmlns:ns2=\"http://b2w.banktowallet.com/b2w\">\n";
        $xml .= '            <return>' . "\n";
        $xml .= '                <mmHeaderInfo>' . "\n";
        $xml .= "                    <operatorCode>{$data['operatorCode']}</operatorCode>\n";
        $xml .= "                    <requestId>{$data['requestId']}</requestId>\n";
        $xml .= "                    <affiliateCode>{$data['affiliateCode']}</affiliateCode>\n";
        $xml .= "                    <responseCode>{$data['responseCode']}</responseCode>\n";
        $xml .= "                    <responseMessage>{$data['responseMessage']}</responseMessage>\n";
        $xml .= '                </mmHeaderInfo>' . "\n";
        
        // Ajouter les champs spécifiques
        foreach ($data as $key => $value) {
            if (!in_array($key, ['operatorCode', 'requestId', 'affiliateCode', 'responseCode', 'responseMessage'])) {
                $xml .= "                <{$key}>{$value}</{$key}>\n";
            }
        }
        
        $xml .= '            </return>' . "\n";
        $xml .= "        </ns2:{$responseType}>\n";
        $xml .= '    </S:Body>' . "\n";
        $xml .= '</S:Envelope>';
        
        return $xml;
    }
    
    /**
     * Construit une réponse XML pour le mini relevé
     */
    private function buildStatementXmlResponse(array $data, string $responseCode, string $responseMessage, array $transactions): string
    {
        $transactionXml = '';
        
        if ($responseCode === self::RESPONSE_CODES['SUCCESS'] && !empty($transactions)) {
            // Filtrer et trier les transactions
            $filteredTransactions = $this->filterTransactions($transactions);
            
            foreach ($filteredTransactions as $transaction) {
                $transactionDate = $this->formatTransactionDate($transaction['date']);
                $transactionType = $this->getTransactionType($transaction['transactionType']['value']);
                
                $transactionXml .= '<TransactionList>' . "\n";
                $transactionXml .= "    <tranRefNo>{$transaction['id']}</tranRefNo>\n";
                $transactionXml .= "    <tranDate>{$transactionDate}</tranDate>\n";
                $transactionXml .= "    <tranType/>\n";
                $xml .= "    <ccy>OUV</ccy>\n";
                $transactionXml .= "    <crDr>{$transactionType}</crDr>\n";
                $transactionXml .= "    <amount>{$transaction['amount']}</amount>\n";
                $transactionXml .= "    <narration/>\n";
                $transactionXml .= '</TransactionList>' . "\n";
            }
        } else {
            // Générer 5 transactions vides
            for ($i = 0; $i < 5; $i++) {
                $isoDate = Carbon::now('Europe/Paris')->toIso8601String();
                $transactionXml .= '<TransactionList>' . "\n";
                $transactionXml .= "    <tranRefNo></tranRefNo>\n";
                $transactionXml .= "    <tranDate>{$isoDate}</tranDate>\n";
                $transactionXml .= "    <tranType/>\n";
                $transactionXml .= "    <ccy>OUV</ccy>\n";
                $transactionXml .= "    <crDr></crDr>\n";
                $transactionXml .= "    <amount>0</amount>\n";
                $transactionXml .= "    <narration/>\n";
                $transactionXml .= '</TransactionList>' . "\n";
            }
        }
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">' . "\n";
        $xml .= '    <soap:Body>' . "\n";
        $xml .= '        <ns2:GetMiniStatementResponse xmlns:ns2="http://b2w.banktowallet.com/b2w">' . "\n";
        $xml .= '            <return>' . "\n";
        $xml .= '                <mmHeaderInfo>' . "\n";
        $xml .= "                    <operatorCode>{$data['operatorCode']}</operatorCode>\n";
        $xml .= "                    <requestId>{$data['requestId']}</requestId>\n";
        $xml .= "                    <affiliateCode>{$data['affiliateCode']}</affiliateCode>\n";
        $xml .= "                    <responseCode>{$responseCode}</responseCode>\n";
        $xml .= "                    <responseMessage>{$responseMessage}</responseMessage>\n";
        $xml .= '                </mmHeaderInfo>' . "\n";
        $xml .= $transactionXml;
        $xml .= '            </return>' . "\n";
        $xml .= '        </ns2:GetMiniStatementResponse>' . "\n";
        $xml .= '    </soap:Body>' . "\n";
        $xml .= '</soap:Envelope>';
        
        return $xml;
    }
    
    /**
     * Filtre et trie les transactions
     */
    private function filterTransactions(array $transactions): array
    {
        $filtered = [];
        
        foreach ($transactions as $transaction) {
            $reversed = $transaction['reversed'] ?? false;
            $value = $transaction['transactionType']['value'] ?? '';
            
            // Exclure les transactions annulées et les charges annulées
            if ($reversed || $value === self::TRANSACTION_TYPES['WAIVE_CHARGE']) {
                continue;
            }
            
            $filtered[] = $transaction;
            
            // Limiter à 5 transactions
            if (count($filtered) >= 5) {
                break;
            }
        }
        
        // Trier par date (plus récent en premier)
        usort($filtered, function ($a, $b) {
            $dateA = Carbon::createFromDate($a['date'][0], $a['date'][1], $a['date'][2]);
            $dateB = Carbon::createFromDate($b['date'][0], $b['date'][1], $b['date'][2]);
            return $dateB->timestamp - $dateA->timestamp;
        });
        
        return $filtered;
    }
    
    /**
     * Formate la date d'une transaction
     */
    private function formatTransactionDate(array $date): string
    {
        return $date[0] . '-' . str_pad($date[1], 2, '0', STR_PAD_LEFT) . '-' . str_pad($date[2], 2, '0', STR_PAD_LEFT);
    }
    
    /**
     * Détermine le type de transaction
     */
    private function getTransactionType(string $type): string
    {
        if ($type === self::TRANSACTION_TYPES['DEPOSIT']) {
            return 'C';
        } elseif (in_array($type, [self::TRANSACTION_TYPES['WITHDRAWAL'], self::TRANSACTION_TYPES['PAY_CHARGE']])) {
            return 'D';
        }
        return '';
    }
    
    /**
     * Construit une réponse d'erreur
     */
    private function buildErrorResponse(string $message): string
    {
        return $this->buildXmlResponse('ErrorResponse', [
            'operatorCode' => 'SYSTEM',
            'requestId' => 'ERROR',
            'affiliateCode' => 'SYSTEM',
            'responseCode' => 'E99',
            'responseMessage' => $message
        ]);
    }
}
