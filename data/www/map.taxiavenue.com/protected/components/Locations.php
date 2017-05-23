<?php
class Locations
{	

    public static function GetInfoByLocation( CDbCriteria $criteria)
    {
        $flag = 0;
        $location_id = 0;

        $result = array('location'=>array('country' => false, 'region' => false, 'city' => false));

        $city = City::model()->find($criteria);	
        if(!empty($city)) {
                $result['location'] = self::GetInfoCity($city);		
                $flag = 3;
                $location_id = $result['location']['city']['id'];
        } 
        else {
            $region = Region::model()->find($criteria);
            if(!empty($region)) {
                $result['location'] = self::GetInfoRegion($region);	
                $flag = 2;
                $location_id = $result['location']['region']['id'];
            } 
            else {
                $country = Country::model()->find($criteria);
                if(!empty($country)) {
                    $result['location'] = self::GetInfoCountry($country);
                    $flag = 1;
                    $location_id = $result['location']['country']['id'];
                }
            }	
        }


        $data = Controller::GetByAddress($flag, $location_id);

        $result = array_merge($result, $data);

        return $result;
    }

    public static function GetInfoCity( City $city = null)
    {
        $Rez = array();
        $Rez['city'] = $city->getAttributes();
        if(!empty($city->idRegion)) {
            $Rez['region'] = $city->idRegion->getAttributes();
            if(!empty($city->idRegion->idCountry)) {
                $Rez['country'] = $city->idRegion->idCountry->getAttributes();
            }
        }	
        return $Rez;
    }

    public static function GetInfoRegion( Region $region = null)
    {
        $Rez = array();
        $Rez['region'] = $region->getAttributes();
        if(!empty($region->idCountry)) {
            $Rez['country'] = $region->idCountry->getAttributes();
        }	
        return $Rez;
    }

    public static function GetInfoCountry( Country $country = null)
    {
        $Rez = array();
        $Rez['country'] = $country->getAttributes();	
        return $Rez;
    }
    
}
