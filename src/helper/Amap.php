<?php


namespace yy\helper;


class Amap
{

    protected $key = "";

    public function __construct($key)
    {
        $this->key = $key;
    }

    public static function init($key)
    {

        return new self($key);
    }
    public function convertLngLat($lng , $lat)
    {
        $url = "https://restapi.amap.com/v3/geocode/regeo?";
        $parameters = [
            'key' => $this->key,
            'location' => "{$lng},{$lat}"
        ];
        $url .= http_build_query($parameters);
        $res = httpRequest($url);
        $res = json_decode($res , true);
        if ($res['status'] == 0){
            return [
                'err' => 1,
                'msg' => $res['info']
            ];
        }
        $address = $res['regeocode']['formatted_address'];
        $res = $res['regeocode']['addressComponent'];
        return [
            'area_code' => $res['adcode'],
            'area_text' => $res['province'] . $res['city'] . $res['district'],
            'address' => $address,
        ];

    }
}