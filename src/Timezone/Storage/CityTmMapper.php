<?php


namespace Mirai\Timezone\Storage;

use PDO;
use Mirai\Timezone\Storage\Db;
use Mirai\Timezone\Storage\CityTm;

class CityTmMapper
{
    /**
     * @var Db $db
     */
    private $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function findById(int $id): ?CityTm
    {
        $row = $this->db->query('SELECT id, cityId, dst, UNIX_TIMESTAMP(zone1) as zone1, 
          UNIX_TIMESTAMP(zone2) as zone2, UNIX_TIMESTAMP(zone3) as zone3, 
          gmtOffset1, gmtOffset2, tzone FROM `city_tm` WHERE id = :id', ['id' => $id]);
        $row = array_shift($row);

        if (empty($row)) {
            return null;
        }

        return $this->mapRowToCity($row);
    }

    public function findByCityId(string $cityId): ?CityTm
    {
        $row = $this->db->query('SELECT id, cityId, dst, UNIX_TIMESTAMP(zone1) as zone1, 
          UNIX_TIMESTAMP(zone2) as zone2, UNIX_TIMESTAMP(zone3) as zone3, 
          gmtOffset1, gmtOffset2, tzone FROM `city_tm` WHERE cityId = :cityId', ['cityId' => $cityId]);
        $row = array_shift($row);

        if (empty($row)) {
            return null;
        }

        return $this->mapRowToCity($row);
    }

    public function getAll(): array
    {
        $rows = $this->db->query('SELECT id, cityId, dst, UNIX_TIMESTAMP(zone1) as zone1, 
          UNIX_TIMESTAMP(zone2) as zone2, UNIX_TIMESTAMP(zone3) as zone3, 
          gmtOffset1, gmtOffset2, tzone FROM `city_tm`');

        $arCityTms = [];
        foreach ($rows as $row) {
            $arCityTms[] = $this->mapRowToCity($row);
        }

        return $arCityTms;
    }

    public function save(CityTm $citytm): CityTm
    {
        $pdo = $this->db->getPdo();

        $id = $citytm->getId();
        $cityId = $citytm->getCityId();
        $dst = $citytm->getDst();
        $zone1 = $citytm->getZone1();
        $zone2 = $citytm->getZone2();
        $zone3 = $citytm->getZone3();

        $gmtOffset1 = $citytm->getGmtOffset1();
        $gmtOffset2 = $citytm->getGmtOffset2();

        $tzone = $citytm->getTzone();


        if ($id) {
            $statement = $pdo->prepare('UPDATE `city_tm` SET cityId = :cityId, dst = :dst, 
                  zone1 = FROM_UNIXTIME(:zone1), zone2 = FROM_UNIXTIME(:zone2), zone3 = FROM_UNIXTIME(:zone3), 
                  gmtOffset1 = :gmtOffset1, gmtOffset2 = :gmtOffset2, tzone = :tzone WHERE id = :id');

            $statement->bindValue(':id', $id, PDO::PARAM_INT);

        } else {
            $statement = $pdo->prepare('INSERT INTO `city_tm`(cityId, dst, zone1, zone2, zone3, 
                gmtOffset1, gmtOffset2, tzone) VALUES(:cityId, :dst, FROM_UNIXTIME(:zone1), FROM_UNIXTIME(:zone2), 
                FROM_UNIXTIME(:zone3), :gmtOffset1, :gmtOffset2, :tzone);');
        }


        $statement->bindValue(':cityId', $cityId);
        $statement->bindValue(':tzone', $tzone);
        $statement->bindValue(':dst', $dst, PDO::PARAM_INT);

        if ($zone1) {
            $statement->bindValue(':zone1', $zone1, PDO::PARAM_INT);
        } else {
            $statement->bindValue(':zone1', $zone1, PDO::PARAM_NULL);
        }

        if ($zone2) {
            $statement->bindValue(':zone2', $zone2, PDO::PARAM_INT);
        } else {
            $statement->bindValue(':zone2', $zone2, PDO::PARAM_NULL);
        }

        if ($zone3) {
            $statement->bindValue(':zone3', $zone3, PDO::PARAM_INT);
        } else {
            $statement->bindValue(':zone3', $zone3, PDO::PARAM_NULL);
        }


        $statement->bindValue(':gmtOffset1', $gmtOffset1, PDO::PARAM_INT);
        $statement->bindValue(':gmtOffset2', $gmtOffset2, PDO::PARAM_INT);

        $statement->execute();

        if (!$id) {
            $id = $pdo->lastInsertId();

            $citytm = $this->mapRowToCity([
                'id' => $id,
                'cityId' => $cityId,
                'dst' => $dst,
                'zone1' => $zone1,
                'zone2' => $zone2,
                'zone3' => $zone3,
                'gmtOffset1' => $gmtOffset1,
                'gmtOffset2' => $gmtOffset2,
                'tzone' => $tzone,
            ]);
        }

        return $citytm;
    }

    private function mapRowToCity(array $row): CityTm
    {
        return CityTm::fromState($row);
    }
}