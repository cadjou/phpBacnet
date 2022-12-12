<?php

use phpBacnet\Bacnet;
use phpBacnet\BacnetObjectType;
use phpBacnet\BacnetPropertyId;

// Définir l'adresse IP et le numéro de port du dispositif Bacnet
$ip = '192.168.1.100';
$port = 47808;

// Ouvrir une connexion avec le dispositif Bacnet
$bacnet = new Bacnet($ip,$port);

// Construire la trame de la requête de lecture de propriété
echo $bacnet->readProperty(BacnetObjectType::ANALOG_INPUT, 1, 1, BacnetPropertyId::PRESENT_VALUE);

// Fermer la connexion
$bacnet->disconnect();