<?php


namespace Mirai\Timezone\Storage;


class CityTm
{
    private $id;
    private $cityId;
    private $dst;
    private $zone1;
    private $zone2;
    private $zone3;
    private $gmtOffset1;
    private $gmtOffset2;
    private $tzone;

    public function __construct(
        ?int $id,
        string $cityId,
        int $dst,
        ?int $zone1,
        ?int $zone2,
        ?int $zone3,
        ?int $gmtOffset1,
        ?int $gmtOffset2,
        string $tzone
    ) {

        if ($zone1 <= 0) {
            $zone1 = null;
        }
        if ($zone2 <= 0) {
            $zone2 = null;
        }
        if ($zone3 <= 0) {
            $zone3 = null;
        }

        $this->id = $id;
        $this->cityId = $cityId;

        $this->dst = $dst;
        $this->zone1 = $zone1;
        $this->zone2 = $zone2;
        $this->zone3 = $zone3;
        $this->gmtOffset1 = $gmtOffset1;
        $this->gmtOffset2 = $gmtOffset2;

        $this->tzone = $tzone;
    }

    public static function fromState(array $state): CityTm
    {
        return new self(
            $state['id'],
            $state['cityId'],
            $state['dst'],
            $state['zone1'],
            $state['zone2'],
            $state['zone3'],
            $state['gmtOffset1'],
            $state['gmtOffset2'],
            $state['tzone']
        );
    }

    public function isDstTransition(): bool
    {
        if (empty($this->zone1) || empty($this->zone2)) {
            return false;
        }

        return true;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getCityId()
    {
        return $this->cityId;
    }

    /**
     * @return mixed
     */
    public function getDst()
    {
        return $this->dst;
    }

    /**
     * @return mixed
     */
    public function getTzone()
    {
        return $this->tzone;
    }

    /**
     * @return mixed
     */
    public function getZone1()
    {
        return $this->zone1;
    }

    /**
     * @return mixed
     */
    public function getZone2()
    {
        return $this->zone2;
    }

    /**
     * @return mixed
     */
    public function getZone3()
    {
        return $this->zone3;
    }

    /**
     * @return mixed
     */
    public function getGmtOffset1()
    {
        return $this->gmtOffset1;
    }

    /**
     * @return mixed
     */
    public function getGmtOffset2()
    {
        return $this->gmtOffset2;
    }
}