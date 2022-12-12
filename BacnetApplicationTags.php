<?php

namespace phpBacnet;


class BacnetApplicationTags
{
    const UNSIGNED_INTEGER = 0x67;
    const OCTET_STRING = 0x49;
    const BACNET_OBJECT_ID = 0x83;
    const BACNET_PROPERTY_ID = 0x83;
    const BACNET_PROPERTY_ARRAY_INDEX = 0x87;
    const BACNET_PROPERTY_VALUE = 0x82;
    const MONITORED_PROPERTY_IDENTIFIER = 0x08;
    const BACNET_MONITORED_PROPERTY_ID = 0x88;
    // D'autres tags d'application BACnet peuvent être ajoutés ici...
}
