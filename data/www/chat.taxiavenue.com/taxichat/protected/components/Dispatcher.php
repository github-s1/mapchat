<?php

class Dispatcher 
{
    public static $contentUploader = "ajax";

    public static function GetNearestDrivers($lat, $long, $radius, $id=0)
    {
		$criteria=new CDbCriteria();
		$criteria->mergeWith(array(
			'join'=>'INNER JOIN users driver ON driver.id = t.id_user',
			'condition'=>'driver.id_type = 1',			
		));
	    $criteria->addCondition('((DEGREES(ACOS((SIN(RADIANS('.$lat.')) * SIN(RADIANS(CAST(lat AS DECIMAL(8,2))))) + (COS(RADIANS('.$lat.')) * COS(RADIANS(CAST(lat AS DECIMAL(8,2)))) * COS(RADIANS('.$long.' - CAST(lng AS DECIMAL(8,2)))))))) * 60 * 1.1515 * 1.609344) <'.$radius.'');
		$criteria->addCondition("id_status = 1");
		$criteria->addCondition("moderation != 0 AND moderation != 2");
                $criteria->addCondition("id_user <> :id");
                $criteria->params = array(":id" => $id);
		$criteria->order = 'status_update DESC';
		$free_drivers = UserStatus::model()->findAll($criteria); 
		return  $free_drivers;
    }
}

?>
