<?php

require '../vendor/autoload.php';

use MomoPayment\MomoPayment;


define('USER_ID', ''); // API user id
define('API_KEY', ''); // API kEY
define('COLLECTION_SUBSCRIPTION_KEY', '');

$baseUrl = 'https://proxy.momoapi.mtn.com/collection/';//production    Sandbox: https://sandbox.momodeveloper.mtn.com/ Production: https://proxy.momoapi.mtn.com/
$environmentTarget='mtncongo';  //mtncongo production

$momo = new MomoPayment(USER_ID, API_KEY, COLLECTION_SUBSCRIPTION_KEY,$baseUrl,$environmentTarget);


$momo->getAccessToken(); //pour recuperer le token de la transaction 

//var_dump($momo->getAccessToken());

    // Effectuer la demande de paiement
    $response = $momo->requestToPay('06000000', 25);
    
    // Si vous voulez récupérer l'état de la transaction après le paiement, décommentez cette ligne
  

    // Afficher la réponse JSON de l'API
    echo $response;
