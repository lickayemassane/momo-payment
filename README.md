
# Intégration du Paiement Momo (MTN)

Ce projet contient deux classes PHP permettant d'intégrer les services de paiement Momo (MTN) pour effectuer des paiements et des transferts de fonds via l'API de MoMo. Les deux principales classes sont `MomoPayment` et `Disbursement`.

## Prérequis

Avant d'utiliser ces classes, vous devez avoir les éléments suivants :
- Un **ID utilisateur** (userId) fourni par MTN
- Une **clé API** (apiKey) fournie par MTN
- Une **clé d'abonnement** (subscriptionKey) fournie par MTN
- L'URL de base de l'API de MoMo (baseUrl)
- L'environnement cible (par exemple, `mtncongo` pour l'environnement de production de MTN Congo)

Vous pouvez obtenir ces informations après avoir créé un compte sur le portail développeur de MTN MoMo.

## Installation

1. **Installation des dépendances** :

   Assurez-vous d'avoir installé les dépendances nécessaires via **Composer** :

   ```bash
   composer require massane/massane/momo-payment
   ```

2. **Configuration des clés API** :

   Modifiez les paramètres suivants dans le fichier de configuration ou dans votre script principal :

   ```php
   $userId = 'votre-userId';
   $apiKey = 'votre-apiKey';
   $subscriptionKey = 'votre-subscriptionKey';
   $baseUrl = 'https://proxy.momoapi.mtn.com/collection/';
   $environmentTarget = 'mtncongo';  // mtncongo or sandbox
   ```

## Utilisation

### 1. Effectuer une Demande de Paiement

La classe `MomoPayment` permet d'effectuer une demande de paiement en utilisant le numéro de téléphone du payeur et le montant.

```php
use MomoPayment\MomoPayment;

$userId = 'votre-userId';
$apiKey = 'votre-apiKey';
$subscriptionKey = 'votre-subscriptionKey';
$baseUrl = 'https://proxy.momoapi.mtn.com/collection/';
$environmentTarget = 'mtncongo';  // mtncongo or sandbox

$momo = new MomoPayment($userId, $apiKey, $subscriptionKey, $baseUrl, $environmentTarget);

// Effectuer une demande de paiement de 25 XAF pour le numéro de téléphone '060000000'
$response = $momo->requestToPay('060000000', 25);

// Afficher la réponse JSON de l'API
echo $response;
```

### 2. Effectuer un Transfert de Fonds

La classe `Disbursement` permet d'effectuer un transfert de fonds (disbursement) vers un autre numéro de téléphone.

```php
use Disbursement;

$userId = 'votre-userId';
$apiKey = 'votre-apiKey';
$subscriptionKey = 'votre-subscriptionKey';
$baseUrl = 'https://proxy.momoapi.mtn.com/disbursement/';
$environmentTarget = 'mtncongo';  // mtncongo pour la production

$disbursement = new Disbursement($userId, $apiKey, $subscriptionKey, $baseUrl, $environmentTarget);

// Effectuer un transfert de 100 XAF vers le numéro de téléphone '069832678'
$response = $disbursement->disburse('069832678', 100);

// Afficher la réponse JSON de l'API
echo $response;
```

## Fonctionnalités

### `getAccessToken()`

Cette fonction permet de récupérer un token d'accès pour effectuer des requêtes à l'API de MoMo.

### `generateUUID()`

Génère un identifiant unique (UUID) pour chaque transaction.

### `requestToPay($phoneNumber, $amount)`

Effectue une demande de paiement en envoyant une requête à l'API de MoMo avec le numéro de téléphone et le montant spécifié.

### `disburse($telephone, $amount)`

Effectue un transfert de fonds (disbursement) vers un numéro de téléphone donné avec un montant spécifié.

## Gestion des Erreurs

Si une erreur survient lors de l'exécution des requêtes API, une exception sera levée avec un message d'erreur détaillant le problème. Assurez-vous d'encapsuler les appels API dans des blocs `try-catch` pour capturer les erreurs.

```php
try {
    $response = $momo->requestToPay('060000000', 25);
} catch (Exception $e) {
    echo 'Erreur : ' . $e->getMessage();
}
```
