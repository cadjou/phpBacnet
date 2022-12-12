<?php

namespace phpBacnet;

function encodeBacnetObjectIdentifier($objectType, $objectInstance) {
    // Encoder l'identifiant de l'objet en utilisant les types BACnet
    $objectIdentifier = chr(BacnetObjectType::OBJECT_TYPE)
        . chr(2) // Taille de la valeur (2 octets)
        . chr($objectType) // Type d'objet
        . chr($objectInstance); // Instance d'objet

    // Encoder l'identifiant de l'objet en utilisant les types ASN.1
    $encodedObjectIdentifier = chr(BacnetApplicationTags::BACNET_OBJECT_ID)
        . chr(strlen($objectIdentifier)) // Taille de la valeur
        . $objectIdentifier; // Identifiant de l'objet

    return $encodedObjectIdentifier;
}

function encodeBacnetPropertyIdentifier($propertyIdentifier) {
    // Encoder l'identifiant de la propriété en utilisant les types BACnet
    $encodedPropertyIdentifier = chr(BacnetPropertyIdentifier::PROPERTY_IDENTIFIER)
        . chr(1) // Taille de la valeur (1 octet)
        . chr($propertyIdentifier); // Identifiant de la propriété

    // Encoder l'identifiant de la propriété en utilisant les types ASN.1
    $encodedPropertyIdentifier = chr(BacnetApplicationTags::BACNET_PROPERTY_ID)
        . chr(strlen($encodedPropertyIdentifier)) // Taille de la valeur
        . $encodedPropertyIdentifier; // Identifiant de la propriété

    return $encodedPropertyIdentifier;
}


function encodeBacnetPropertyArrayIndex($propertyArrayIndex) {
    // Encoder l'index du tableau de propriété en utilisant les types BACnet
    $encodedPropertyArrayIndex = chr(BacnetPropertyArrayIndex::PROPERTY_ARRAY_INDEX)
        . chr(2) // Taille de la valeur (2 octets)
        . chr($propertyArrayIndex >> 8) // Partie haute de l'index
        . chr($propertyArrayIndex & 0xff); // Partie basse de l'index

    // Encoder l'index du tableau de propriété en utilisant les types ASN.1
    $encodedPropertyArrayIndex = chr(BacnetApplicationTags::BACNET_PROPERTY_ARRAY_INDEX)
        . chr(strlen($encodedPropertyArrayIndex)) // Taille de la valeur
        . $encodedPropertyArrayIndex; // Index du tableau de propriété

    return $encodedPropertyArrayIndex;
}


function encodeBacnetPropertyValue($value) {
    // Encoder la valeur de la propriété en utilisant les types BACnet
    $encodedPropertyValue = chr(BacnetPropertyIdentifier::PROPERTY_VALUE)
        . chr(strlen($value)) // Taille de la valeur
        . $value; // Valeur de la propriété

    // Encoder la valeur de la propriété en utilisant les types ASN.1
    $encodedPropertyValue = chr(BacnetApplicationTags::BACNET_PROPERTY_VALUE)
        . chr(strlen($encodedPropertyValue)) // Taille de la valeur
        . $encodedPropertyValue; // Valeur de la propriété

    return $encodedPropertyValue;
}

function encodeBacnetMonitoredPropertyIdentifier($monitoredPropertyIdentifier) {
    // Encoder l'identifiant de la propriété surveillée en utilisant les types BACnet
    $encodedMonitoredPropertyIdentifier = chr(BacnetApplicationTags::MONITORED_PROPERTY_IDENTIFIER)
        . chr(1) // Taille de la valeur (1 octet)
        . chr($monitoredPropertyIdentifier); // Identifiant de la propriété surveillée

    // Encoder l'identifiant de la propriété surveillée en utilisant les types ASN.1
    $encodedMonitoredPropertyIdentifier = chr(BacnetApplicationTags::BACNET_MONITORED_PROPERTY_ID)
        . chr(strlen($encodedMonitoredPropertyIdentifier)) // Taille de la valeur
        . $encodedMonitoredPropertyIdentifier; // Identifiant de la propriété surveillée

    return $encodedMonitoredPropertyIdentifier;
}


function decode($response) {
    // Décoder le type de PDU de la réponse
    $decodedResponse['type'] = ord($response[0]);

    // Vérifier le type de PDU de la réponse
    switch ($decodedResponse['type']) {
        case BacnetPDUType::ERROR:
            // La réponse contient une erreur, décoder le code d'erreur
            $decodedResponse['error'] = ord($response[1]);
            break;
        case BacnetPDUType::UNCONFIRMED_SERVICE_REQUEST:
            // La réponse contient une requête de service non confirmée, décoder les données
            $decodedResponse['service'] = ord($response[1]);
            $decodedResponse['data'] = substr($response, 2);
            break;
        case BacnetPDUType::CONFIRMED_SERVICE_REQUEST:
            // La réponse contient une requête de service confirmée, décoder les données
            $decodedResponse['service'] = ord($response[1]);
            $decodedResponse['data'] = substr($response, 2);
            break;
    }

    return $decodedResponse;
}

function encodeBacnetFileStart($fileStart) {
    // Encoder la valeur de début du fichier en BACnet FileStart
    $encodedValue = encodeBacnetUnsignedInteger($fileStart);
    $encodedFileStart = chr(BacnetPropertyIdentifier::FILE_START)
        . chr(BacnetApplicationTags::UNSIGNED_INTEGER)
        . $encodedValue;
    return $encodedFileStart;
}

function encodeBacnetUnsignedInteger($value) {
    // Encoder la valeur en entier non signé BACnet
    $encodedValue = "";
    if ($value <= 0xFF) {
        $encodedValue = chr(BacnetUnsigned::UINT_8) . chr($value);
    } elseif ($value <= 0xFFFF) {
        $encodedValue = chr(BacnetUnsigned::UINT_16) . pack("n", $value);
    } elseif ($value <= 0xFFFFFFFF) {
        $encodedValue = chr(BacnetUnsigned::UINT_32) . pack("N", $value);
    } else {
        // Erreur : valeur trop grande pour être encodée en entier non signé BACnet
        echo "Value too large for BACnet Unsigned Integer";
    }
    return $encodedValue;
}

function encodeBacnetRequestedOctetCount($requestedOctetCount) {
    // Encoder la valeur de nombre d'octets demandés en BACnet RequestedOctetCount
    $encodedValue = encodeBacnetUnsignedInteger($requestedOctetCount);
    $encodedRequestedOctetCount = chr(BacnetPropertyIdentifier::REQUESTED_OCTET_COUNT)
        . chr(BacnetApplicationTags::UNSIGNED_INTEGER)
        . $encodedValue;
    return $encodedRequestedOctetCount;
}

function encodeBacnetFileData($data) {
    // Encoder les données du fichier en BACnet FileData
    $encodedData = chr(BacnetPropertyIdentifier::FILE_DATA)
        . chr(BacnetApplicationTags::OCTET_STRING)
        . encodeBacnetOctetString($data);
    return $encodedData;
}

function encodeBacnetOctetString($data) {
    // Encoder les données en chaîne d'octets BACnet
    $length = strlen($data);
    $encodedOctetString = chr($length) . $data;
    return $encodedOctetString;
}
