<?php

namespace phpBacnet;

class BacnetConfirmedServiceRequest {
    const WRITE_PROPERTY = 0x00; ///
    const SUBSCRIBE_COV= 0x00; ///
    
    const ATOMIC_READ_FILE = 0x14;
    const ATOMIC_WRITE_FILE = 0x15;
}