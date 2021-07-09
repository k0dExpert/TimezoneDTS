<?php


namespace Mirai\Timezone;

use UnexpectedValueException;

class Config
{
    private $data;

    public function __construct(Array $config)
    {
        $this->data = $config;
    }

    public function get(String $key)
    {
        $current = $this->data;

        $arKeys = explode('.', $key);
        foreach ($arKeys as $subkey) {
            if (is_array($current) && isset($current[$subkey])) {
                $current = $current[$subkey];
            } else {
                $current = null;
                break;
                //throw new UnexpectedValueException('');
            }
        }

        return $current;
    }
}