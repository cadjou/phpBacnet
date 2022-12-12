<?php
namespace phpBacnet;

class Bacnet {
    private $ip;
    private $port;
    private $conn;

    // Constructeur
    public function __construct($ip, $port) {
        $this->ip = $ip;
        $this->port = $port;
    }

    // Ouvrir une connexion avec le dispositif Bacnet
    public function connect() {
        $conn = fsockopen($this->ip, $this->port, $errno, $errstr);

        if (!$conn) {
            // La connexion a échoué, afficher un message d'erreur
            echo "Erreur lors de la connexion au dispositif Bacnet : $errstr ($errno)";
            exit;
        }

        $this->conn = $conn;
    }

    // Fermer la connexion avec le dispositif Bacnet
    public function disconnect() {
        fclose($this->conn);
    }

    // Construire la trame d'une requête de lecture de propriété
    public function buildReadPropertyRequest($objectType, $objectInstance, $propertyIdentifier, $propertyArrayIndex) {
        // Construire la trame de la requête de lecture de propriété
        $apdu = chr(BacnetPDUType::UNCONFIRMED_SERVICE_REQUEST)  // Type de PDU (Requête de service non confirmée)
            . chr(BacnetUnconfirmedServiceRequest::READ_PROPERTY) // Service non confirmé (Lecture de propriété)
            . encodeBacnetObjectIdentifier($objectType, $objectInstance) // Identifiant de l'objet
            . encodeBacnetPropertyIdentifier($propertyIdentifier) // Identifiant de la propriété
            . encodeBacnetPropertyArrayIndex($propertyArrayIndex); // Index du tableau de propriété

        return $apdu;
    }

    // Envoyer une requête de lecture de propriété au dispositif Bacnet
    public function readProperty($objectType, $objectInstance, $propertyIdentifier, $propertyArrayIndex) {
        // Construire la trame de la requête
        $apdu = $this->buildReadPropertyRequest($objectType, $objectInstance, $propertyIdentifier, $propertyArrayIndex);

        // Envoyer la requête au dispositif
        fwrite($this->conn, $apdu);

        // Recevoir la réponse du dispositif
        $response = fread($this->conn, 1024);

        // Décoder la réponse
        $decodedResponse = decode($response);

        // Vérifier si la réponse contient une erreur
        if ($decodedResponse['type'] == BacnetPDUType::ERROR) {
            // La requête a échoué, afficher le code d'erreur
            echo "Erreur lors de l'envoi de la requête : {$decodedResponse['error']}";
            exit;
        }

        // La réponse ne contient pas d'erreur, retourner la valeur de la propriété
        return $decodedResponse['value'];
    }

    // Construire la trame d'une requête d'écriture de propriété
    public function buildWritePropertyRequest($objectType, $objectInstance, $propertyIdentifier, $propertyArrayIndex, $value) {
        // Construire la trame de la requête d'écriture de propriété
        $apdu = chr(BacnetPDUType::CONFIRMED_SERVICE_REQUEST)  // Type de PDU (Requête de service confirmée)
            . chr(BacnetConfirmedServiceRequest::WRITE_PROPERTY) // Service confirmé (Ecriture de propriété)
            . encodeBacnetObjectIdentifier($objectType, $objectInstance) // Identifiant de l'objet
            . encodeBacnetPropertyIdentifier($propertyIdentifier) // Identifiant de la propriété
            . encodeBacnetPropertyArrayIndex($propertyArrayIndex) // Index du tableau de propriété
            . encodeBacnetPropertyValue($value); // Valeur de la propriété

        return $apdu;
    }

    // Envoyer une requête d'écriture de propriété au dispositif Bacnet
    public function writeProperty($objectType, $objectInstance, $propertyIdentifier, $propertyArrayIndex, $value) {
        // Construire la trame de la requête
        $apdu = $this->buildWritePropertyRequest($objectType, $objectInstance, $propertyIdentifier, $propertyArrayIndex, $value);

        // Envoyer la requête au dispositif
        fwrite($this->conn, $apdu);

        // Recevoir la réponse du dispositif
        $response = fread($this->conn, 1024);

        // Décoder la réponse
        $decodedResponse = decode($response);

        // Vérifier si la réponse contient une erreur
        if ($decodedResponse['type'] == BacnetPDUType::ERROR) {
            // La requête a échoué, afficher le code d'erreur
            echo "Erreur lors de l'envoi de la requête : {$decodedResponse['error']}";
            exit;
        }

        // La requête a réussi, retourner true
        return true;
    }

    // Construire la trame d'une requête d'abonnement au changement de valeur (COV)
    public function buildSubscribeCOVRequest($objectType, $objectInstance, $monitoredPropertyIdentifier) {
        // Construire la trame de la requête d'abonnement au COV
        $apdu = chr(BacnetPDUType::CONFIRMED_SERVICE_REQUEST)  // Type de PDU (Requête de service confirmée)
            . chr(BacnetConfirmedServiceRequest::SUBSCRIBE_COV) // Service confirmé (Abonnement au COV)
            . encodeBacnetObjectIdentifier($objectType, $objectInstance) // Identifiant de l'objet
            . encodeBacnetMonitoredPropertyIdentifier($monitoredPropertyIdentifier) // Identifiant de la propriété surveillée
            . chr(BacnetPropertyArrayIndex::ALL_ELEMENTS); // Tous les éléments

        return $apdu;
    }

    public function subscribeCOV($objectType, $objectInstance, $monitoredPropertyIdentifier) {
        // Construire la trame de la requête d'abonnement au COV
        $apdu = $this->buildSubscribeCOVRequest($objectType, $objectInstance, $monitoredPropertyIdentifier);
    
        // Envoyer la requête au dispositif
        fwrite($this->conn, $apdu);
    
        // Recevoir la réponse du dispositif
        $response = fread($this->conn, 1024);
    
        // Décoder la réponse
        $decodedResponse = decode($response);
    
        // Vérifier si la réponse contient une erreur
        if ($decodedResponse['type'] == BacnetPDUType::ERROR) {
            // La requête a échoué, afficher le code d'erreur
            echo "Erreur lors de l'envoi de la requête : {$decodedResponse['error']}";
            exit;
        }
    
        // La requête a réussi, retourner true
        return true;
    }
    
    public function buildAtomicReadFileRequest($fileIdentifier, $offset, $length) {
        // Construire la trame de la requête de lecture atomique de fichier
        $fileStart = encodeBacnetFileStart($offset);
        $requestedOctetCount = encodeBacnetRequestedOctetCount($length);
        $apdu = chr(BacnetPDUType::UNCONFIRMED_SERVICE_REQUEST)  // Type de PDU (Requête de service non confirmée)
            . chr(BacnetUnconfirmedServiceRequest::ATOMIC_READ_FILE) // Service non confirmé (Lecture de fichier atomique)
            . encodeBacnetObjectIdentifier(BacnetObjectType::FILE, $fileIdentifier) // Identifiant de l'objet de fichier
            . $fileStart // Début du fichier à lire
            . $requestedOctetCount; // Nombre d'octets demandés
        return $apdu;
    }
    

    // Envoyer une requête de lecture atomique de fichier au dispositif Bacnet
    public function atomicReadFile($fileIdentifier, $offset, $length) {
        // Construire la trame de la requête de lecture atomique de fichier
        $apdu = $this->buildAtomicReadFileRequest($fileIdentifier, $offset, $length); ///

        // Envoyer la requête au dispositif
        fwrite($this->conn, $apdu);

        // Recevoir la réponse du dispositif
        $response = fread($this->conn, 1024);

        // Décoder la réponse
        $decodedResponse = decode($response);

        // Vérifier si la réponse contient une erreur
        if ($decodedResponse['type'] == BacnetPDUType::ERROR) {
            // La requête a échoué, afficher le code d'erreur
            echo "Erreur lors de l'envoi de la requête : {$decodedResponse['error']}";
            exit;
        }

        // La requête a réussi, retourner les octets lus
        return $decodedResponse['data'];
    }

    // Construire la trame d'une requête d'écriture atomique de fichier
    public function buildAtomicWriteFileRequest($fileIdentifier, $offset, $data) {
        // Construire la trame de la requête d'écriture atomique de fichier
        $apdu = chr(BacnetPDUType::CONFIRMED_SERVICE_REQUEST)  // Type de PDU (Requête de service confirmée)
            . chr(BacnetConfirmedServiceRequest::ATOMIC_WRITE_FILE) // Service confirmé (Ecriture atomique de fichier)
            . encodeBacnetObjectIdentifier(BacnetObjectType::FILE, $fileIdentifier) // Identifiant du fichier ///
            . encodeBacnetFileStart($offset) // Position de départ du fichier ///
            . encodeBacnetFileData($data); // Données à écrire ///

        return $apdu;
    }

        // Envoyer une requête d'écriture atomique de fichier au dispositif Bacnet
    public function atomicWriteFile($fileIdentifier, $offset, $data) {
        // Construire la trame de la requête d'écriture atomique de fichier
        $apdu = $this->buildAtomicWriteFileRequest($fileIdentifier, $offset, $data);

        // Envoyer la requête au dispositif
        fwrite($this->conn, $apdu);

        // Recevoir la réponse du dispositif
        $response = fread($this->conn, 1024);

        // Décoder la réponse
        $decodedResponse = decode($response);

        // Vérifier si la réponse contient une erreur
        if ($decodedResponse['type'] == BacnetPDUType::ERROR) {
            // La requête a échoué, afficher le code d'erreur
            echo "Erreur lors de l'envoi de la requête : {$decodedResponse['error']}";
            exit;
        }

        // La requête a réussi, retourner true
        return true;
    }

}