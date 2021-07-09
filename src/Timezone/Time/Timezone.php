<?php

namespace Mirai\Timezone\Time;

use Mirai\Timezone\Storage\CityTm;

class Timezone
{
    private $citytm;

    public function __construct(CityTm $citytm)
    {
        $this->citytm = $citytm;
    }

    public function getLocalTimeFromUtc(int $utc_time): int
    {
        $citytm = $this->citytm;
        if ($citytm->isDstTransition()) {
            if ($utc_time >= $citytm->getZone1() && $utc_time < $citytm->getZone2()) {

                $local_time = $utc_time + $citytm->getGmtOffset1();

            } elseif ($utc_time >= $citytm->getZone2() && $utc_time < $citytm->getZone3()) {

                $local_time = $utc_time + $citytm->getGmtOffset2();

            } else {
                throw new \Exception('The specified UTC time cannot be converted to local time');
            }
        } else {
            $local_time = $utc_time + $citytm->getGmtOffset1();
        }

        return $local_time;
    }

    public function getUtcTimeFromLocal(int $local_time): int
    {

        $citytm = $this->citytm;


        if ($citytm->isDstTransition()) {

            $utc_t1 = $local_time - $citytm->getGmtOffset1();
            if ($utc_t1 >= $citytm->getZone1() && $utc_t1 < $citytm->getZone2()) {

                $utc_time = $utc_t1;

            } else {

                $utc_t2 = $local_time - $citytm->getGmtOffset2();
                if ($utc_t2 >= $citytm->getZone2() && $utc_t2 < $citytm->getZone3()) {

                    $utc_time = $utc_t2;

                } else {
                    throw new \Exception('The specified local time cannot be converted to UTC');
                }
            }

        } else {
            $offset = $citytm->getGmtOffset1();
            $utc_time = $local_time - $offset;
        }

        return $utc_time;
    }
}