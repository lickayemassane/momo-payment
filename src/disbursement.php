<?php

use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use Exception;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;;

class Disbursement
{ // Disbursement subscription key

    private $accessToken;

    private $userId;
    private $apiKey;
    private $subscriptionKey;
    private $client;
    private $baseUrl;
    private $environmentTarget;
  

    public function __construct($userId, $apiKey, $subscriptionKey,$baseUrl,$environmentTarget)
    
    {
        // Optional: Automatically get an access token when an instance is created
        $this->accessToken = $this->getAccessToken();
        $this->userId = $userId;
        $this->apiKey = $apiKey;
        $this->subscriptionKey = $subscriptionKey;
        $this->baseUrl = $baseUrl;
       

        // Création du HandlerStack et ajout du middleware pour loguer les requêtes
        $stack = HandlerStack::create();
        
        // Création d'un logger Monolog compatible avec Psr\Log\LoggerInterface
        $logger = new Logger('logger', [new StreamHandler('php://stdout', Logger::DEBUG)]);

        // Ajout du middleware de log
        $stack->push(Middleware::log(
            $logger,  // Le logger ici doit implémenter Psr\Log\LoggerInterface
            new MessageFormatter('{req_body} - {res_body}')
        ));

        // Initialisation du client Guzzle avec le HandlerStack
        $this->client = new Client(['handler' => $stack]);
    }

    // Get the access token from MoMo API
    public function getAccessToken()
    {
        $credentials = base64_encode("{$this->userId}:{$this->apiKey}");

        $response = $this->client->post("{$this->baseUrl}token/", [
            'headers' => [
                'Authorization' => "Basic $credentials",
                'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
                'Content-Length' => 0
            ]
        ]);

        $body = json_decode($response->getBody(), true);

        if (!isset($body['access_token'])) {
            throw new Exception("Erreur lors de la récupération du token d'accès");
        }

        return $body['access_token'];
    }


    // Generate a unique UUID for the transaction
    private function getUuid()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    // Request a disbursement transfer to the given phone number
    public function disburse($telephone, $amount)
    {
        $uid = $this->getUuid();
        $accessToken = $this->accessToken;

        //$endpointUrl = 'https://proxy.momoapi.mtn.com/disbursement/v1_0/transfer';
        $endpointUrl = $this->baseUrl."v1_0/transfer";



        $data = array(
            "amount" => $amount,
            "currency" => "XAF", // default for sandbox
            "externalId" => $uid, // reference number
            "payee" => array(
                "partyIdType" => "MSISDN",
                "partyId" => $telephone, // user phone number (test numbers)
            ),
            "payerMessage" => "Funds Transfer",
            "payeeNote" => "We have transferred funds"
        );

        $data_string = json_encode($data);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $endpointUrl);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken,
            'X-Reference-Id: ' . $uid,
            'X-Target-Environment: '.$this->environmentTarget,
            'Ocp-Apim-Subscription-Key: ' .  $this->subscriptionKey,
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $curl_response = curl_exec($curl); // will respond with HTTP 202 Accepted
        curl_close($curl);

        return $curl_response;
    }
}

// Usage example

 

?>
