<?php

namespace phpBacnet;

class BacnetPropertyIdentifier
{
    const FILE_START = 0x3C;
    const REQUESTED_OCTET_COUNT = 0x3D;
    const FILE_DATA = 0x0A;
    const PROPERTY_IDENTIFIER = 0x0B;
    const PROPERTY_VALUE = 0x0A;
    // D'autres codes de propriété BACnet peuvent être ajoutés ici...
}