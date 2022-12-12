<?php

namespace phpBacnet;

class BacnetUnconfirmedServiceRequest {
    const SUBSCRIBE_COV = 0x08;
    const READ_PROPERTY = 0x0C;
    
    const ATOMIC_READ_FILE = 0x14;
    const ATOMIC_WRITE_FILE = 0x15;
}