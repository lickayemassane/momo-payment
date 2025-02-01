<?php

namespace MomoPayment;

class Verify{

    private $accessToken;
    private $subscriptionKey;
    private $baseUrl;
    private $environmentTarget;
    private $price;
    private $uid;
    private $phone;
  

    public function __construct($accessToken, $price, $uid,$phone,$subscriptionKey,$baseUrl,$environmentTarget)
    
    {
        // Optional: Automatically get an access token when an instance is created
     
        $this->accessToken = $accessToken;
        $this->price = $price;
        $this->uid = $uid;
        $this->phone = $phone;
        $this->subscriptionKey = $subscriptionKey;
        $this->baseUrl = $baseUrl;
        $this->environmentTarget = $environmentTarget;
   
    }

    public function getVerifyTransaction(){
     
        //$endpoint_url = 'https://proxy.momoapi.mtn.com/collection/v1_0/requesttopay/'.$uid;

        $endpoint_url = $this->baseUrl.$this->uid;

 

        # Parameters
        $data = array(
            "amount" => $this->price,
            "currency" => "XAF", //default for sandbox
            "externalId" => $this->uid, //reference number

            "payer" => array(

                "partyIdType" => "MSISDN",
                "partyId" => $this->phone  //user phone number, these are test numbers)
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

                'Authorization: Bearer ' . $this->accessToken, //optional
                'X-Callback-Url: https://evxpro.net/mtncallback', //optional, not required for sandbox
                'X-Target-Environment: '.$this->environmentTarget,
                'Ocp-Apim-Subscription-Key: ' .  $this->subscriptionKey,

            )
        );

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $curl_response = curl_exec($curl); //will respond with HTTP 202 Accepted


        curl_close($curl);

        $resdata = json_decode($curl_response);
        $status = isset($resdata->{'status'}) ? $resdata->{'status'} : null;
        $financialTransactionId = isset($resdata->{'financialTransactionId'}) ? $resdata->{'financialTransactionId'} : null;
        $externalId = isset($resdata->{'externalId'}) ? $resdata->{'externalId'} : null;
        $amount = isset($resdata->{'amount'}) ? $resdata->{'amount'} : null;
        $partyId = isset($resdata->{'payer'}->{'partyId'}) ? $resdata->{'payer'}->{'partyId'} : null;

   
       
        $response = [
            'status' => $status,
            'financialTransactionId' => $financialTransactionId,
            'externalId' => $externalId,
            'amount' => $amount,
            'partyId' => $partyId,
        ];
    
        // Retourne la réponse au format JSON
        return json_encode($response, JSON_PRETTY_PRINT);
    }

}