<?php
require '../vendor/autoload.php';

use MomoPayment\Verify;
// API kEY
define('COLLECTION_SUBSCRIPTION_KEY', '');//subcription key collection

 $accessToken=""; //token de la transaction

 $price='';
 $uid=""; //transId de la transaction
 $phone="242060000000"; //le numéro de téléphone précédé du 242


$baseUrl = "https://proxy.momoapi.mtn.com/collection/v1_0/requesttopay/";//production
$environmentTarget='mtncongo';  //mtncongo production

$momo = new Verify($accessToken, $price, $uid,$phone,COLLECTION_SUBSCRIPTION_KEY,$baseUrl,$environmentTarget);


    // Effectuer la demande verification de paiement
    $response = $momo->getVerifyTransaction();
    
    // Si vous voulez récupérer l'état de la transaction après le paiement, décommentez cette ligne

    // Afficher la réponse JSON de l'API
    echo $response;
