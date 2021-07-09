<?php

namespace Tests\Timezone\Time;

use Mirai\Timezone\Time\Timezone;
use Mirai\Timezone\Storage\CityTm;
use PHPUnit\Framework\TestCase;

class TimezoneTest extends TestCase
{
    /**
     * @covers \Mirai\Timezone\Time\Timezone
     */
    public function testTimezone1(): void
    {

        $gmtOffset1 = 3600; // 1 hour
        $citytm = new CityTm(null, '', 0, null, null, null,
            $gmtOffset1, null, '');
        $tm = new Timezone($citytm);

        $time = 1635620400; // '2021-10-30 19:00:00'

        $this->assertEquals($time + $gmtOffset1, $tm->getLocalTimeFromUtc($time));
        $this->assertEquals($time - $gmtOffset1, $tm->getUtcTimeFromLocal($time));
    }

    /**
     * @covers \Mirai\Timezone\Time\Timezone
     */
    public function testTimezone2(): void
    {

        $zone1 = 1616893200; // 2021-03-28 01:00:00
        $zone2 = 1635642000; // 2021-10-31 01:00:00
        $zone3 = 1648342800; // 2022-03-27 01:00:00

        $gmtOffset1 = 3600; // 1 hour
        $gmtOffset2 = 0;

        $citytm = new CityTm(null, '', 1, $zone1, $zone2, $zone3,
            $gmtOffset1, $gmtOffset2, '');
        $tm = new Timezone($citytm);

        $time = 1625776377; // '2021-07-09 20:32:57'
        $this->assertEquals($time + $gmtOffset1, $tm->getLocalTimeFromUtc($time));
        $this->assertEquals($time - $gmtOffset1, $tm->getUtcTimeFromLocal($time));


        $time = $zone2 + $gmtOffset1;
        $this->assertEquals($time + $gmtOffset2, $tm->getLocalTimeFromUtc($time));
        $this->assertEquals($time - $gmtOffset2, $tm->getUtcTimeFromLocal($time));
    }

}