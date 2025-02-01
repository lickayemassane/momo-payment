![Logo](https://www.cleanpay.africa/wp-content/uploads/2020/10/paiement-par-mtn-mobile-money-en-ligne.jpg)

# Intégration du Paiement Momo (MTN)

Ce projet contient des classes PHP permettant d'intégrer les services de paiement Momo (MTN) pour effectuer des paiements, des transferts de fonds et la vérification des transactions via l'API de MoMo. Les principales classes utilisées sont `MomoPayment` et `Verify`.

## Prérequis

Avant d'utiliser ces classes, vous devez disposer des éléments suivants :
- Un **ID utilisateur** (`userId`) fourni par MTN
- Une **clé API** (`apiKey`) fournie par MTN
- Une **clé d'abonnement** (`subscriptionKey`) fournie par MTN
- L'URL de base de l'API de MoMo (`baseUrl`)
- L'environnement cible (ex. `mtncongo` pour l'environnement de production de MTN Congo)

Ces informations sont accessibles après la création d'un compte sur le portail développeur de MTN MoMo.

## Installation

1. **Installation des dépendances** :

   Assurez-vous d'avoir installé les dépendances nécessaires via **Composer** :

   ```bash
   composer require lickayemassane/momo-payment
   ```

2. **Configuration des clés API** :

   Modifiez les paramètres suivants dans votre script principal :

   ```php
   define('USER_ID', 'votre-userId');
   define('API_KEY', 'votre-apiKey');
   define('COLLECTION_SUBSCRIPTION_KEY', 'votre-subscriptionKey');
   
   $baseUrl = 'https://proxy.momoapi.mtn.com/collection/'; // Production
   $environmentTarget = 'mtncongo'; // mtncongo pour l'environnement de production
   ```

## Utilisation

### 1. Effectuer une Demande de Paiement

La classe `MomoPayment` permet d'effectuer une demande de paiement en utilisant le numéro de téléphone du payeur et le montant.
require '../vendor/autoload.php'; 

```php


use MomoPayment\MomoPayment;

define('USER_ID', 'votre-userId');
define('API_KEY', 'votre-apiKey');
define('COLLECTION_SUBSCRIPTION_KEY', 'votre-subscriptionKey');

$baseUrl = 'https://proxy.momoapi.mtn.com/collection/';
$environmentTarget = 'mtncongo';

$momo = new MomoPayment(USER_ID, API_KEY, COLLECTION_SUBSCRIPTION_KEY, $baseUrl, $environmentTarget);

// Récupérer le token de la transaction
$momo->getAccessToken();

// Effectuer une demande de paiement de 25 XAF pour le numéro '06000000'
$response = $momo->requestToPay('06000000', 25);

// Afficher la réponse JSON de l'API
echo $response;
```

### 2. Vérifier une Transaction

La classe `Verify` permet de vérifier l'état d'une transaction après un paiement.

```php


use MomoPayment\Verify;

define('COLLECTION_SUBSCRIPTION_KEY', 'votre-subscriptionKey');

$accessToken = ""; // Token de la transaction
$price = '';
$uid = ""; // ID de transaction
$phone = "242060000000"; // Numéro de téléphone avec indicatif

$baseUrl = "https://proxy.momoapi.mtn.com/collection/v1_0/requesttopay/";
$environmentTarget = 'mtncongo';

$momo = new Verify($accessToken, $price, $uid, $phone, COLLECTION_SUBSCRIPTION_KEY, $baseUrl, $environmentTarget);

// Vérifier l'état de la transaction
$response = $momo->getVerifyTransaction();

// Afficher la réponse JSON de l'API
echo $response;
```



### 3. Disbursement

La classe `decaissement`

```php


use MomoPayment\Disbursement;

define('USER_ID', 'votre-userId');//disbursement
define('API_KEY', 'votre-apiKey');//disbursement
define('COLLECTION_SUBSCRIPTION_KEY', 'votre-subscriptionKey');//disbursement

$baseUrl = 'https://proxy.momoapi.mtn.com/disbursement/';
$environmentTarget = 'mtncongo';

$momo = new Disbursement(USER_ID, API_KEY, COLLECTION_SUBSCRIPTION_KEY, $baseUrl, $environmentTarget);

// Récupérer le token de la transaction
$momo->getAccessToken();

// Effectuer une demande de paiement de 25 XAF pour le numéro '06000000'
$response = $momo->requestToPay('06000000', 25);

// Afficher la réponse JSON de l'API
echo $response;
```

## Fonctionnalités

### `getAccessToken()`

Récupère un token d'accès pour effectuer des requêtes à l'API MoMo.

### `generateUUID()`

Génère un identifiant unique (UUID) pour chaque transaction.

### `requestToPay($phoneNumber, $amount)`

Effectue une demande de paiement en envoyant une requête à l'API MoMo avec le numéro de téléphone et le montant spécifié.

### `getVerifyTransaction()`

Vérifie l'état d'une transaction après le paiement.

## Gestion des Erreurs

Si une erreur survient lors de l'exécution des requêtes API, une exception sera levée avec un message d'erreur détaillant le problème. Il est recommandé d'encapsuler les appels API dans des blocs `try-catch` pour capturer les erreurs.

```php
try {
    $response = $momo->requestToPay('060000000', 25);
} catch (Exception $e) {
    echo 'Erreur : ' . $e->getMessage();
}
```

