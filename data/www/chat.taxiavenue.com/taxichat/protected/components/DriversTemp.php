<?php
class DriversTemp
{	
	// возвращает непромодерированые данные водителя
	public static function GetDriverTempByParentId($id_driver = null) {
		$driver_temp = UsersTemp::model()->findByAttributes(array('id_driver' => $id_driver));
		return $driver_temp;
	}
	
	// возвращает полные непромодерированые данные водителя
	public static function GetDriverTempDataProfile(Users $driver, Cars $car) {
		$driver_temp = self::GetDriverTempByParentId($driver->id);
			
		if(!empty($driver_temp)) {

			$driver_data = $driver->attributes;
			unset($driver_data['id']);
			unset($driver_data['id_car']);
			unset($driver_data['photo']);
			
			$driver_temp->SetProperties($driver_data);
			
			$cars_temp = CarsTemp::model()->findByPk($driver_temp->id_car);
			
			$car_data = $car->attributes;
			unset($car_data['id']);
			for($i=1; $i<8; $i++) {
				unset($car_data['photo'.$i]);
			}
			
			$cars_temp->SetProperties($car_data);
		} else {
			$TempData = self::SetTempAttributes($driver, $car);
			
			$driver_temp = $TempData['driver_temp'];
			$cars_temp = $TempData['cars_temp'];
		}
		
		
		return array('driver_temp' => $driver_temp, 'cars_temp' => $cars_temp);
	}
	
	
	public static function GetDriverTempDataServices(Users $driver, Cars $car) {
		$driver_temp = self::GetDriverTempByParentId($driver->id);
			
		if(!empty($driver_temp)) {
			$cars_temp = CarsTemp::model()->findByPk($driver_temp->id_car);
		} else {
			$TempData = self::SetTempAttributes($driver, $car);
			
			$driver_temp = $TempData['driver_temp'];
			$cars_temp = $TempData['cars_temp'];
			
			if($cars_temp->save()) {
				$driver_temp->SetProperties(array('id_car' => $cars_temp->id));
			}
		}
		return array('driver_temp' => $driver_temp, 'cars_temp' => $cars_temp);
	}
	
	public static function GetDriverTempDataPhotos(Users $driver, Cars $car) {
		$driver_temp = self::GetDriverTempByParentId($driver->id);
			
		if(!empty($driver_temp)) {
			$cars_temp = CarsTemp::model()->findByPk($driver_temp->id_car);
		} else {
			$TempData = self::SetTempAttributes($driver, $car);
			
			$driver_temp = $TempData['driver_temp'];
			$cars_temp = $TempData['cars_temp'];
		}
		return array('driver_temp' => $driver_temp, 'cars_temp' => $cars_temp);
	}
	
	// сетит непромодерированые данные
	public static function SetTempAttributes(Users $driver, Cars $car) {
		$driver_temp = new UsersTemp;
			
		$driver_data = $driver->attributes;
		$driver_data['id_driver'] = $driver->id;
		unset($driver_data['id']);
		
		$driver_temp->SetProperties($driver_data);
		$driver_temp->copy_photo();
		
		$cars_temp = new CarsTemp;
		
		$car_data = $car->attributes;
		unset($car_data['id']);
		$cars_temp->SetProperties($car_data);
		$cars_temp->copy_photos();
		
		return array('driver_temp' => $driver_temp, 'cars_temp' => $cars_temp);
	}
	

	public static function GetDriverTempData($id_driver) {
		if(!empty($id_driver)) {
			$driver_temp = self::GetDriverTempByParentId($id_driver);
			if(empty($driver_temp)) {
				return null;
			}
			$cars_temp = CarsTemp::model()->findByPk($driver_temp->id_car);
			$services_driver_temp = self::GetDriverServices($driver_temp->id);
			
			return array('driver_temp' => $driver_temp, 'cars_temp' => $cars_temp, 'services_driver_temp' => $services_driver_temp);	
		}
		return null;
	}
	
	// возвращает услуги непромодерированого водителя
	public static function GetDriverServices($id_driver = 0) {
		$services_driver = DriverServiceTemp::model()->findAllByAttributes(array('id_driver' => $id_driver), array('order'=>'id ASC'));
		
		$services_dr = array();
		if(!empty($services_driver)) {
			foreach($services_driver as $serv) {
				$services_dr[] = $serv->id_service;
			}
		}
		
		return $services_dr;
	}
	
	// удаление непромодерированых данных
	public static function deleteTempData(UsersTemp $driver_temp, CarsTemp $car_temp) {
		$id_driver = $driver_temp->id; 
		if($driver_temp->delete()) {
			$car_temp->delete();
			DriverServiceTemp::model()->deleteAll('id_driver = ?' , array($id_driver));
			return true;
		} 
		return false;
	}
}
?>