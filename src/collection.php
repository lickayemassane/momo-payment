<?php
require '../vendor/autoload.php';

use MomoPayment\MomoPayment;

$userId = '';
$apiKey = '';
$subscriptionKey = '';
$baseUrl = 'https://proxy.momoapi.mtn.com/collection/';//production
$environmentTarget='mtncongo';  //mtncongo production

$momo = new MomoPayment($userId, $apiKey, $subscriptionKey,$baseUrl,$environmentTarget);


    // Effectuer la demande de paiement
    $response = $momo->requestToPay('06000000', 25);
    
    // Si vous voulez récupérer l'état de la transaction après le paiement, décommentez cette ligne
  

    // Afficher la réponse JSON de l'API
    echo $response;
