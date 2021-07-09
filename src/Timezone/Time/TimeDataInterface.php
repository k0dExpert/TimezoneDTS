<?php


namespace Mirai\Timezone\Time;


interface TimeDataInterface
{
    public function setConfig(array $config): void;

    public function getDataWithCoordinate(float $latitude, float $longitude): ?array;
}