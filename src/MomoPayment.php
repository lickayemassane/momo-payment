<?php

namespace MomoPayment;

use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use Exception;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class MomoPayment
{
    private $userId;
    private $apiKey;
    private $subscriptionKey;
    private $client;
    private $baseUrl;
    private $environmentTarget;

    public function __construct($userId, $apiKey, $subscriptionKey,$baseUrl,$environmentTarget)
    {
       $this->userId = $userId;
        $this->apiKey = $apiKey;
        $this->subscriptionKey = $subscriptionKey;
        $this->baseUrl = $baseUrl;
        $this->environmentTarget = $environmentTarget;

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

    /**
     * Récupérer le token d'accès à l'API Momo
     */
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

    /**
     * Générer un UUID
     */
    private function generateUUID()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    /**
     * Effectuer une requête de paiement
     */
    public function requestToPay($phoneNumber, $amount)
    {
        $uuid = $this->generateUUID();
        $accessToken = $this->getAccessToken();
    
        $data = [
            "amount" => (string)$amount,
            "currency" => "XAF",
            "externalId" => $uuid,
            "payer" => [
                "partyIdType" => "MSISDN",
                "partyId" => "242" . $phoneNumber
            ],
            "payerMessage" => "Paiement de {$amount} XAF",
            "payeeNote" => "Merci de valider le paiement"
        ];
    
        try {
            $response = $this->client->post("{$this->baseUrl}v1_0/requesttopay", [
                'headers' => [
                    'Authorization' => "Bearer $accessToken",
                    'X-Reference-Id' => $uuid,
                    'X-Target-Environment' => 'mtncongo',
                    'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
                    'Content-Type' => 'application/json'
                ],
                'json' => $data
            ]);
    
           // $responseBody = $response->getBody();
            //$responseContents = $responseBody->getContents();  // Lire le contenu du flux
            
            //checkTransactionStatus();

           // var_dump("GET_TOKEN".$access_token);
    $endpoint_url = $this->baseUrl."v1_0/requesttopay/$uuid";


 

    # Parameters
    $data = array(
        "amount" => "$amount",
        "currency" => "XAF", //default for sandbox
        "externalId" => "$uuid", //reference number

        "payer" => array(

            "partyIdType" => "MSISDN",
            "partyId"     => "$amount"  //user phone number, these are test numbers)
        ),

        


    );

    $data_string = json_encode($data);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $endpoint_url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
    //curl_setopt($curl, CURLOPT_TIMEOUT, 50);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

    curl_setopt(
        $curl,
        CURLOPT_HTTPHEADER,
        array(
            
            'Authorization: Bearer '.$accessToken, //optional
            'X-Callback-Url: ', //optional, not required for sandbox
            'X-Target-Environment: '.$this->environmentTarget,
            'Ocp-Apim-Subscription-Key: ' . $this->subscriptionKey,

        )
    );



    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

 

     $curl_response = curl_exec($curl); //will respond with HTTP 202 Accepted
    // close curl resource to free up system resources
   // var_dump($curl_response);
    curl_close($curl);

    //$resdata = json_decode($curl_response);
    header('Content-Type: application/json; charset=utf-8');
    return $curl_response;
        } catch (Exception $e) {
            echo "Erreur : " . $e->getMessage();
            return null;
        }
    }

  
    
    
    
}
