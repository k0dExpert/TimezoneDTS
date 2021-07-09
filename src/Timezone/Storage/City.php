<?php


namespace Mirai\Timezone\Storage;


class City
{
    private $id;
    private $country_iso3;
    private $name;
    private $latitude;
    private $longitude;

    public function __construct(
        ?string $id,
        string $country_iso3,
        string $name,
        float $latitude,
        float $longitude
    ) {
        $this->id = $id;
        $this->country_iso3 = $country_iso3;
        $this->name = $name;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public static function fromState(array $state): City
    {
        return new self(
            $state['id'],
            $state['country_iso3'],
            $state['name'],
            $state['latitude'],
            $state['longitude']
        );
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
    public function getCountryIso3()
    {
        return $this->country_iso3;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

}