<?php


namespace Mirai\Timezone\Time;


class TimeAdapterCsv implements TimeDataInterface
{
    private $data = [];

    public function setConfig(array $config): void
    {
        // TODO: Implement setConfig() method.


        if (isset($config['csv']) && isset($config['csv']['path'])) {
            if (($handle = fopen($config['csv']['path'], "r")) !== false) {

                while (($data = fgetcsv($handle, 0, ";")) !== false) {

                    $latitude = (string)$data[0];
                    $longitude = (string)$data[1];
                    $this->data[$latitude][$longitude] = [
                        'latitude' => $data[0],
                        'longitude' => $data[1],
                        'dst' => $data[2],
                        'zone1' => $data[3],
                        'zone2' => $data[4],
                        'zone3' => $data[5],
                        'gmtOffset1' => $data[6],
                        'gmtOffset2' => $data[7],
                        'tzone' => $data[8],
                    ];
                }
            } else {
                throw new \Exception('The file with timezone was not found');
            }
        } else {
            throw new \Exception('File setup for timezone is not set');
        }

//        print '<pre>';
//        print_r($this->data);
//        print '</pre>';
    }

    public function getDataWithCoordinate(float $latitude, float $longitude): ?array
    {
        // TODO: Implement getDataWithCoordinate() method.

        $key_lat = (string)$latitude;
        $key_lon = (string)$longitude;
        if (!empty($this->data) && isset($this->data[$key_lat])
            && isset($this->data[$key_lat][$key_lon])) {
            return $this->data[$key_lat][$key_lon];
        } else {
            return null;
        }
    }

}