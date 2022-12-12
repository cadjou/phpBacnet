<?php

namespace phpBacnet;

class BacnetObjectType
{
    const ANALOG_INPUT = 0;
    const ANALOG_OUTPUT = 1;
    const ANALOG_VALUE = 2;
    const BINARY_INPUT = 3;
    const BINARY_OUTPUT = 4;
    const BINARY_VALUE = 5;
    const CALENDAR = 6;
    const COMMAND = 7;
    const DEVICE = 8;
    const EVENT_ENROLLMENT = 9;
    const FILE = 10;
    const GROUP = 11;
    const LOOP = 12;
    const MULTI_STATE_INPUT = 13;
    const MULTI_STATE_OUTPUT = 14;
    const NOTIFICATION_CLASS = 15;
    const PROGRAM = 16;
    const SCHEDULE = 17;
    const AVERAGING = 18;
    const MULTI_STATE_VALUE = 19;
    const TREND_LOG = 20;
    const ACCUMULATOR = 23;
    const EVENT_LOG = 25;
    const TIMER = 31;

    const OBJECT_TYPE = 0x85;
}
    