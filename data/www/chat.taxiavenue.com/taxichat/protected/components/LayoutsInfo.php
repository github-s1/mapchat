<?php
class LayoutsInfo
{
	static function getCountModerDrivers()
    {
		$criteria=new CDbCriteria();
		$criteria->mergeWith(array(
			'join'=>'INNER JOIN users driver ON driver.id = t.id_user',
			'condition'=>'driver.id_type = 1'
		));
		$criteria->addCondition("moderation = 2 OR moderation = 3");
		$criteria->addCondition("is_activate = 1");
		$count = UserStatus::model()->count($criteria);
			
		
      return $count;
    }

}