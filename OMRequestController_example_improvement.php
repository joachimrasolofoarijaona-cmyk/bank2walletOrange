<?php
// EXEMPLE D'AMÉLIORATION - Méthode handle refactorisée

/**
 * Méthode principale refactorisée pour gérer les requêtes SOAP
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

/**
 * Appelle un webhook N8N de manière centralisée
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

/**
 * Construit une réponse d'erreur standardisée
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
