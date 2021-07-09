<?php


namespace Mirai\Timezone\Time;

use \GuzzleHttp\Client as GuzzleClient;

class TimeAdapterTimezonedb implements TimeDataInterface
{
    private $api_key;
    private $request_url;
    private $client;

    public function setConfig(array $config): void
    {
        // TODO: Implement setConfig() method.

        if (!isset($config['timezonedb'])) {
            throw new \Exception('No settings are set for timezonedb');
        }

        if (!isset($config['timezonedb']['api_key'])) {
            throw new \Exception('The api key for timezonedb is not set');
        }

        if (!isset($config['timezonedb']['url'])) {
            throw new \Exception('The request url for timezonedb is not set');
        }

        $this->api_key = $config['timezonedb']['api_key'];
        $this->request_url = $config['timezonedb']['url'];

        $this->client = new GuzzleClient();

        $params = [
            'key' => $this->api_key,
            'format' => 'json',
            'by' => 'zone',
            'zone' => 'Europe/Moscow'
        ];

        $response = $this->client->get($this->request_url . '?' . http_build_query($params));
        $data = json_decode($response->getBody());
        if ($data->status == 'FAILED') {
            $message = 'Error timezonedb service';
            if ($data->message) {
                $message .= ': ' . $data->message;
            }
            throw new \Exception($message);
        }
    }

    public function getDataWithCoordinate(float $latitude, float $longitude): array
    {
        // TODO: Implement getDataWithCoordinate() method.

        $params = [
            'key' => $this->api_key,
            'format' => 'json',
            'by' => 'position',
            'lat' => $latitude,
            'lng' => $longitude
        ];

        $response = $this->client->get($this->request_url . '?' . http_build_query($params));
        sleep(1);
        $response_data = json_decode($response->getBody());


        $dst = $response_data->dst;
        $gmtOffset1 = (int)$response_data->gmtOffset;
        $zoneName = $response_data->zoneName;

        $zone1 = (int)$response_data->zoneStart;
        $zone2 = (int)$response_data->zoneEnd;

        $zone3 = null;
        $gmtOffset2 = 0;

        if ($zone1 > 0 && $zone2 > 0) {

            $params['time'] = $zone2;
            $response = $this->client->get($this->request_url . '?' . http_build_query($params));
            sleep(1);

            $response_data = json_decode($response->getBody());

            $zone3 = (int)$response_data->zoneEnd;
            $gmtOffset2 = (int)$response_data->gmtOffset;
        }


        return [
            'dst' => $dst,
            'zone1' => $zone1,
            'zone2' => $zone2,
            'zone3' => $zone3,
            'gmtOffset1' => $gmtOffset1,
            'gmtOffset2' => $gmtOffset2,
            'zoneName' => $zoneName,
        ];
    }

}