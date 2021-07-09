<?php


namespace Mirai\Timezone\Controller;


use Mirai\Timezone\Storage\City;
use Mirai\Timezone\Storage\CityTm;
use Mirai\Timezone\Storage\Db;
use Mirai\Timezone\Storage\CityMapper;
use Mirai\Timezone\Storage\CityTmMapper;
use Mirai\Timezone\Time\TimeDataInterface;
use Mirai\Timezone\Time\Timezone;

class TimeController extends Controller
{
    public function getRoutes()
    {
        return [
            'post' => [
                '/' => 'getTimeWithUTC',
                '/local' => 'getTimeWithLocal',
            ],
            'get' => [
                '/refresh' => 'refreshTimezone',
                '/test' => 'test',
            ]
        ];
    }

    public function test()
    {

        $db = new Db($this->config->get('db'));

        $mapper = new CityMapper($db);
        $cities = $mapper->getAll();

        $mapper = new CityTmMapper($db);
        $citiestm = $mapper->getAll();


        $arData = [];
        foreach ($cities as $item){
            $arData[$item->getId()] = $item;
        }

//        print '<pre>';
//        print_r($arData);
//        print '</pre>';
//
//        exit;

        $file = fopen(dirname(__FILE__) . '/timezone.csv', 'w');

        fputcsv($file, [
            'latitude',
            'longitude',
            'dst',
            'zone1',
            'zone2',
            'zone3',
            'gmtOffset1',
            'gmtOffset2',
            'tzone',
        ], ';');

        foreach ($citiestm as $ctm) {

            $id = $ctm->getCityId();

            fputcsv($file, [
                $arData[$id]->getLatitude(),
                $arData[$id]->getLongitude(),
                $ctm->getDst(),
                $ctm->getZone1(),
                $ctm->getZone2(),
                $ctm->getZone3(),
                $ctm->getGmtOffset1(),
                $ctm->getGmtOffset2(),
                $ctm->getTzone(),
            ], ';');
        }



    }

    public function getTimeWithUTC()
    {

        $params = $this->request->getData();

        if (!isset($params['city_id'])) {
            throw new \Exception('The city id is not set');
        }

        if (!isset($params['time'])) {
            throw new \Exception('No date/time set');
        }


        $db = new Db($this->config->get('db'));
        $mapper = new CityTmMapper($db);

        $city_id = (string)$params['city_id'];
        $citytm = $mapper->findByCityId($city_id);

        if (!$citytm) {
            throw new \Exception('The city with this id was not found in the database');
        }


        $time = (string)$params['time'];
        $time_utc = strtotime($time);
        if ($time_utc === false) {
            throw new \Exception('Invalid date/time format');
        }

        $tmzone = new Timezone($citytm);
        $time_local = $tmzone->getLocalTimeFromUtc($time_utc);


        $this->response->addParameters([
            'status' => 'ok',
            'time' => date('Y-m-d H:i:s', $time_local)
        ]);
    }

    public function getTimeWithLocal()
    {

        $params = $this->request->getData();

        if (!isset($params['city_id'])) {
            throw new \Exception('The city id is not set');
        }

        if (!isset($params['time'])) {
            throw new \Exception('No date/time set');
        }


        $db = new Db($this->config->get('db'));
        $mapper = new CityTmMapper($db);

        $city_id = (string)$params['city_id'];
        $citytm = $mapper->findByCityId($city_id);

        if (!$citytm) {
            throw new \Exception('The city with this id was not found in the database');
        }


        $time = (string)$params['time'];
        $time_local = strtotime($time);
        if ($time_local === false) {
            throw new \Exception('Invalid date/time format');
        }

        $tmzone = new Timezone($citytm);
        $time_utc = $tmzone->getUtcTimeFromLocal($time_local);


        $this->response->addParameters([
            'status' => 'ok',
            'time' => date('Y-m-d H:i:s', $time_utc)
        ]);
    }

    public function refreshTimezone()
    {
        /**
         * @var City $city
         * @var TimeDataInterface $timeAdapter
         */
        $db = new Db($this->config->get('db'));
        $mapper = new CityMapper($db);
        $cities = $mapper->getAll();

        $timeAdapterClassName = $this->config->get('time.adapter');
        if (empty($timeAdapterClassName)) {
            throw new \Exception('The time adapter class is not set');
        }

        $timeAdapter = new $timeAdapterClassName();
        $timeAdapter->setConfig($this->config->get('time.settings'));

        $mapper = new CityTmMapper($db);

        foreach ($cities as $city) {
            $dataTm = $timeAdapter->getDataWithCoordinate($city->getLatitude(), $city->getLongitude());


            $cityId = $city->getId();
            $citytm = $mapper->findByCityId($cityId);

            $id = null;
            if ($citytm) {
                $id = $citytm->getId();
            }


            $citytm = CityTm::fromState([
                'id' => $id,
                'cityId' => (string)$cityId,
                'dst' => (int)$dataTm['dst'],
                'zone1' => (int)$dataTm['zone1'],
                'zone2' => (int)$dataTm['zone2'],
                'zone3' => (int)$dataTm['zone3'],
                'gmtOffset1' => (int)$dataTm['gmtOffset1'],
                'gmtOffset2' => (int)$dataTm['gmtOffset2'],
                'tzone' => (string)$dataTm['zoneName'],
            ]);


            $mapper->save($citytm);

        }

        $this->response->addParameters([
            'status' => 'ok'
        ]);
    }
}