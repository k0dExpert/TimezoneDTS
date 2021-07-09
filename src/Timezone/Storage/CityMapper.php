<?php


namespace Mirai\Timezone\Storage;

use Mirai\Timezone\Storage\Db;
use Mirai\Timezone\Storage\City;

class CityMapper
{
    /**
     * @var Db $db
     */
    private $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function findById(string $id)
    {
        $row = $this->db->query('SELECT * FROM `city` WHERE id = :id', ['id' => $id]);
        $row = array_shift($row);

        if (empty($row)) {
            return false;
        }

        return $this->mapRowToCity($row);
    }

    public function getAll()
    {
        $rows = $this->db->query('SELECT * FROM `city`');

        $arCities = [];
        foreach ($rows as $row) {
            $arCities[] = $this->mapRowToCity($row);
        }

        return $arCities;
    }

    public function update(City $city)
    {
        $this->db->execute('UPDATE `city` SET country_iso3 = :country_iso3, name = :name, 
            latitude = :latitude, longitude = :longitude WHERE id = :id',
            [
                'id' => $city->getId(),
                'country_iso3' => $city->getCountryIso3(),
                'name' => $city->getName(),
                'latitude' => $city->getLatitude(),
                'longitude' => $city->getLongitude(),
            ]);
    }

    private function mapRowToCity(array $row)
    {
        return City::fromState($row);
    }
}