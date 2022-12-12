<?php

namespace phpBacnet;

class BacnetPDUType {
    const CONFIRMED_SERVICE_REQUEST = 0x00;
    const UNCONFIRMED_SERVICE_REQUEST = 0x10;
    const ERROR = 0x80;
}