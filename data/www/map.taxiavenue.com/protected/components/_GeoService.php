<?php
/**
 * 
 */
class _GeoService {
    
    const TranslateUrl = 'https://translate.yandex.net/api/v1.5/tr.json/translate';
    const TranslateKey = 'trnsl.1.1.20140627T080013Z.5758668491fd5631.3d19252e4806c2c0dabcf296cd4076edb9bb9f67';
    const GoogleGeocodeUrl = 'http://maps.googleapis.com/maps/api/geocode/json';
    const YandexGeocodeUrl = 'http://geocode-maps.yandex.ru/1.x/?format=json&lang=en_US&geocode=';

    public static function TranslateEnRu(array $words){
        $url = self::TranslateUrl . '?lang=en-ru&key=' . self::TranslateKey;
        
        foreach ($words as $word){
            $url .= "&text=$word";
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = json_decode(curl_exec($ch));
        curl_close($ch);
        
        return $response->text;
    }

    public static function GeocodeY($address){
        $url = self::YandexGeocodeUrl . $address;
        $output = Yii::app()->curl->get($url);
        $response = json_decode($output);
        return $response;
    }

    public static function Geocode($address){
        if(empty($address)){
            return FALSE;
        }
        
        $params = array(
            'address' =>$address,
            'sensor' =>FALSE,
            'language'=>'en'
        );
        
        $output = Yii::app()->curl->get(self::GoogleGeocodeUrl, $params);
        $response = json_decode($output);
        if(empty($response->results)){
            return FALSE;
        }
        
        return self::_parseGeoResult($response->results[0]);
    }

    public static function ReverseGeocode($lat, $lng){
        $params = array(
            'latlng' => "$lat,$lng",
            'sensor' => 'false',
            'language'=>'en'
        );
        $output = Yii::app()->curl->get(self::GoogleGeocodeUrl, $params);
        $response = json_decode($output);
        if(empty($response->results)){
            throw new Exception("Невозможно определить местоположение по переданным координатам.");
        }
        
        return self::_parseGeoResult($response->results[0]);
    }
    
    private static function _parseGeoResult($geoResult){
        $location = self::_parseLocation($geoResult->address_components);
        $coordinates = self::_parseCoordinates($geoResult->geometry);
        return array_merge($location, $coordinates);
    }
    
    private static function _parseLocation($addresses){
        $location = array();
        foreach ($addresses as $address){
            switch ($address->types[0]){
                case "country":
                    $location['country'] = $address->long_name;
                break;
                case "administrative_area_level_1":
                    $location['region'] = $address->long_name;
                break;
                case "locality":
                    $location['city'] = $address->long_name;
                break;
            }
        }
        
        return $location;
    }
    
    private static function _parseCoordinates($geometry){
        $coordinates = array();
        $bounds = array();
        if(!empty($geometry->location)){
            $coordinates[0] = $geometry->location->lat;
            $coordinates[1] = $geometry->location->lng;
        }
        if(!empty($geometry->bounds)){
            $bounds['northeast']['lat'] = $geometry->bounds->northeast->lat;
            $bounds['northeast']['lng'] = $geometry->bounds->northeast->lng;
            $bounds['southwest']['lat'] = $geometry->bounds->southwest->lat;
            $bounds['southwest']['lng'] = $geometry->bounds->southwest->lng;
        }
        
        return array( 'coordinates'=>$coordinates, 'bounds'=>$bounds );
    }
}
