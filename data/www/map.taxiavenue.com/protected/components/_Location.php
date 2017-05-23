<?php
/**
 * 
 */
class _Location {
    
    public $lat;
    public $lng;
    public $bounds;
    public $city;
    public $region;
    public $country;

    public static function setFromSearchString($address){
        if(empty($address)){
            throw new Exception("Не передана строка адреса.");
        }
        
        $geoResult = GeoService::Geocode($address);
        if(!$geoResult){
            return FALSE;
        }
        
        $location = new Location();
        $location->lat = $geoResult['coordinates']['lat'];
        $location->lng = $geoResult['coordinates']['lng'];
        
        $countryName = $geoResult['country']['name_en'];
        $regionName = $geoResult['region']['name_en'];
        $cityName = $geoResult['city']['name_en'];
        $location->country = $location->_findCountryByGeocodingName($countryName);
        if(empty($location->country)){
            $location->country = new Country();
            $location->country->name_en = $countryName;
            
            $location->region = new Region();
            $location->region->name_en = $regionName;
            
            $location->city = new City();
            $location->city->name_en = $cityName;
        }
        else {
            $location->region = $location->_findRegionByGeocodingNameAndCountryId($regionName, $location->country->id);
            
            if(empty($location->region)){
                $location->region = new Region();
                $location->region->name_en = $regionName;

                $location->city = new City();
                $location->city->name_en = $cityName;
            }
            else {
                $location->city = $location->_findCityByGeocodingNameAndRegionId($cityName, $location->region->id);
                if(empty($location->city)){
                    $location->city = new City();
                    $location->city->name_en = $cityName;
                }
            }
        }
        return $location;
    }

//    private function _findCountryBySearchString($name)
//    {
//        $criteria = new CDbCriteria;
//        $criteria->condition = 'name_en=:name OR name_ru=:name';
//        $criteria->params=array(
//            ':name'=>$name
//        );
//        return Country::model()->find($criteria);
//    }
    
    /**************************************************************************/

    public static function setFromGeocoding($geoLocation){
        if(empty($geoLocation)){
            throw new Exception("Не переданы данные геокодирования.");
        }
        self::_checkCoordinates($geoLocation['coordinates']);
        
        $location = new Location();
        $location->lat = $geoLocation['coordinates'][0];
        $location->lng = $geoLocation['coordinates'][1];
        $result = $location->_findLocationInDbByGeocodingData($geoLocation);
        if($result){
            $location->country = $result['country'];
            $location->region = $result['region'];
            $location->city = $result['city'];
        }
        else {
            $location->_reverseGeocoding();
            $location->_findLocationInDb();
        }
        return $location;
    }
    
    public static function setFromCoordinates($coordinates){
        self::_checkCoordinates($coordinates);
        $location = new Location();
        $location->lat = $coordinates[0];
        $location->lng = $coordinates[1];
        $location->_reverseGeocoding();
        $location->_findLocationInDb();
        return $location;
    }
    
    private static function _checkCoordinates($coordinates){
        if(empty($coordinates)){
            throw new Exception("Не переданы координаты местоположения.");
        }
        elseif(!isset($coordinates[0]) || !isset($coordinates[1])){
            throw new Exception("Переданы не все координаты местоположения.");
        }
    }
    
    private function __construct() {}
    
    private function _findLocationInDbByGeocodingData($geoLocation)
    {
        $country = $this->_findCountryByGeocodingName($geoLocation['country']);
        if(empty($country)){
            return FALSE;
        }
        
        $region = $this->_findRegionByGeocodingNameAndCountryId($geoLocation['region'], $country->id);
        if(empty($region)){
            return FALSE;
        }
        
        $city = $this->_findCityByGeocodingNameAndRegionId($geoLocation['city'], $region->id);
        if(empty($city)){
            return FALSE;
        }
        
        return array( 'country'=>$country, 'region'=>$region, 'city'=>$city );
    }

    private function _findCountryByGeocodingName($name)
    {
        $criteria = new CDbCriteria;
        $criteria->condition = 'name_en=:name OR name_ru=:name';
        $criteria->params=array(
            ':name'=>$name
        );
        return Country::model()->find($criteria);
    }

    private function _findRegionByGeocodingNameAndCountryId($name, $countryId)
    {
        $criteria = new CDbCriteria;
        $criteria->condition = '(name_en=:name OR name_ru=:name) AND id_country=:id_country';
        $criteria->params = array(
            ':name'=>$name, 
            'id_country'=>$countryId
        );
        return Region::model()->find($criteria);
    }

    private function _findCityByGeocodingNameAndRegionId($name, $regionId)
    {
        $criteria = new CDbCriteria;
        $criteria->condition = '(name_en=:name OR name_ru=:name) AND id_region=:id_region';
        $criteria->params = array(
            ':name'=>$name, 
            'id_region'=>$regionId
        );
        return City::model()->find($criteria);
    }

    private function _reverseGeocoding(){
        $params = array(
            'latlng' => "{$this->lat},{$this->lng}",
            'sensor' => 'false',
            'language'=>'en'
        );
        $url = 'http://maps.googleapis.com/maps/api/geocode/json';
        $output = Yii::app()->curl->get($url, $params);
        $response = json_decode($output);
        if(empty($response->results)){
            throw new Exception("Невозможно определить местоположение по переданным координатам.");
        }
        
        $this->_setFromGeoResults($response->results[0]);
        $this->_translateLocation();
    }
    
    private function _setFromGeoResults($result){
        $this->country = array();
        $this->region = array();
        $this->city = array();
        foreach ($result->address_components as $address){
            switch ($address->types[0]){
                case "country":
                    $this->country['name_en'] = $address->long_name;
                    $this->country['short_name'] = $address->short_name;
                break;
                case "administrative_area_level_1":
                    $this->region['name_en'] = $address->long_name;
                break;
                case "locality":
                    $this->city['name_en'] = $address->long_name;
                break;
            }
        }
    }
    
    private function _translateLocation(){
        $translates = GeoService::TranslateEnRu(array(
            $this->country['name_en'], $this->region['name_en'], $this->city['name_en']
        ));
        
        if(!empty($translates[0])){
            $this->country['name_ru'] = $translates[0];
        }
        if(!empty($translates[1])){
            $this->region['name_ru'] = $translates[1];
        }
        if(!empty($translates[2])){
            $this->city['name_ru'] = $translates[2];
        }
    }
    
    private function _findLocationInDb()
    {
        $this->country = $this->_findCountryByName();
        $this->region = $this->_findRegionByNameAndCountryId();
        $this->city = $this->_findCityByNameAndRegionId();
    }

    private function _findCountryByName()
    {
        $criteria = new CDbCriteria;
        $criteria->condition = 'name_en=:name_en OR name_ru=:name_ru';
        $criteria->params=array(
            ':name_en'=>$this->country['name_en'], 
            ':name_ru'=>$this->country['name_ru']
        );
        
        $country = Country::model()->find($criteria);
        if(empty($country)){
            $country = new Country();
            $country->name_en = $this->country['name_en'];
            $country->name_ru = $this->country['name_ru'];
        }
        return $country;
    }

    private function _findRegionByNameAndCountryId()
    {
        if(!$this->country->isNewRecord){
            $criteria = new CDbCriteria;
            $criteria->condition = '(name_en=:name_en OR name_ru=:name_ru) AND id_country=:id_country';
            $criteria->params = array(
                ':name_en'=>$this->region['name_en'], 
                ':name_ru'=>$this->region['name_ru'], 
                'id_country'=>$this->country->id
            );
            $region = Region::model()->find($criteria);
        }
        if(empty($region)){
            $region = new Region();
            $region->name_en = $this->region['name_en'];
            $region->name_ru = $this->region['name_ru'];
        }
        return $region;
    }

    private function _findCityByNameAndRegionId()
    {
        if(!$this->region->isNewRecord){
            $criteria = new CDbCriteria;
            $criteria->condition = '(name_en=:name_en OR name_ru=:name_ru) AND id_region=:id_region';
            $criteria->params = array(
                ':name_en'=>$this->city['name_en'], 
                ':name_ru'=>$this->city['name_ru'], 
                'id_region'=>$this->region->id
            );
            $city = City::model()->find($criteria);
        }
        if(empty($city)){
            $city = new City();
            $city->name_en = $this->city['name_en'];
            $city->name_ru = $this->city['name_ru'];
        }
        return $city;
    }

    public function toJSON(){
        return CJSON::encode($this->toArray());
    }

    public function toArray(){
        return array(
            'coordinates'=> $this->getCoordinates(),
            'city'=> $this->city,
            'region'=> $this->region,
            'country'=> $this->country
        );
    }
    
    public function getCoordinates(){
        return array( 'lat'=> $this->lat, 'lng'=> $this->lng );
    }
    
}
