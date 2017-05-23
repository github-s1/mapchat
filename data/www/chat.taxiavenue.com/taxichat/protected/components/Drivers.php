<?php
class Drivers 
{	
	// создает и инициализирует объект для водителя 
	public static function Сreate() {
		$user = new Users;
		$user->id_type = 1;
		return $user;
	}
	
	// возвращает водителей согласно критериям
	public static function GetAllDriversByCriteria($pageSize = 0, $driver_search = null, $condition_flag = 0, $order_str = null) {
		$criteria=new CDbCriteria();
		$criteria->mergeWith(array(
			'join'=>'INNER JOIN users driver ON driver.id = t.id_user',
			'condition'=>'driver.id_type = 1'
		));
		if($driver_search !== null) {
			$criteria->mergeWith(array(
				'join'=>'INNER JOIN users driver ON driver.id = t.id_user',
				'condition'=>'LOWER(driver.phone) LIKE :driver OR LOWER(driver.name) LIKE :driver OR LOWER(driver.surname) LIKE :driver OR LOWER(driver.email) LIKE :driver OR LOWER(driver.nickname) LIKE :driver',
				'params'=>array(':driver'=>'%'.mb_strtolower($driver_search, 'UTF-8').'%')
			));
		}
		$criteria->addCondition("is_activate = 1");
		
		switch ($condition_flag) {
			case 1:
				$criteria->addCondition("id_status != 3 AND moderation != 0");
				break;
		}
	
		if(empty($order_str)) {
			$criteria->order = 'moderation DESC, id DESC';
		} else {
			$criteria->order = $order_str;
		}
		$pages = 0;
		if($pageSize > 0) {
			$count=UserStatus::model()->count($criteria);
			$pages = new CPagination($count);
			$pages->pageSize = $pageSize;
			$pages->applyLimit($criteria);
		}
		
		$drivers=UserStatus::model()->findAll($criteria);
		
		return array('drivers' => $drivers, 'pages' => $pages);
	}
	
	// возвращает активных водителей
	public static function GetActiveDrivers() {
		$criteria=new CDbCriteria;
		$criteria->mergeWith(array(
			'join'=>'INNER JOIN users driver ON driver.id = t.id_user',
			'condition'=>'driver.id_type = 1 AND driver.balance > 0 AND moderation != 0'
		));		
		$drivers = UserStatus::model()->findAll($criteria);
		
		return $drivers;
	}
	
	// возвращает услуги водителей
	public static function GetDriverServices($id_driver = 0) {
		$services_driver = DriverService::model()->findAllByAttributes(array('id_driver' => $id_driver), array('order'=>'id ASC'));
		
		$services_dr = array();
		if(!empty($services_driver)) {
			foreach($services_driver as $serv) {
				$services_dr[] = $serv->id_service;
			}
		}
		
		return $services_dr;
	}
	
	// возвращает статусы клиентов согласно критериям
	public static function GetDriverCommissions($id_driver) {
		$driver_commissions = DriverCommission::model()->findAllByAttributes(array('id_driver' => $id_driver), array('order'=>'id ASC'));		
		
		return $driver_commissions;
	}
	
	// возвращает комиссию с заказа водителя
	public static function GetDriverOrderCommission($rating) {
		$average_commission = Settings::model()->findByAttributes(array('param' => 'average_commission'));
		$order_commission = $average_commission->value - ($rating - 3);
		
		return $order_commission;
	}
	
	// возвращает историю денежных средств водителя
	public static function GetDriverPaymentsHistory($id_driver, $pageSize = 0, $is_mobile = false, $date_interval = null, $order_str = null) {
		$criteria = new CDbCriteria();
		$criteria->addCondition("id_user = ".$id_driver);
		
		if(isset($date_interval['date_from']) && !empty($date_interval['date_from'])) {
			$criteria->addCondition("date_create >= '".date('Y-m-d', strtotime($date_interval['date_from']))."'");
		}
		if(isset($date_interval['date_to']) && !empty($date_interval['date_to'])) {
			$time = strtotime($date_interval['date_to']);
			$criteria->addCondition("date_create <= '".date('Y-m-d 23:59:59', $time)."'");	
		}
		if(!empty($order_str)) {
			$criteria->order = $order_str;
		} else {
			$criteria->order = 'id DESC';
		}
		
		$count = PaymentsHistory::model()->count($criteria);
		$pages = new CPagination($count);
		$pages->pageSize = $pageSize;
		$pages->applyLimit($criteria);
		
		$payments_driver = PaymentsHistory::model()->findAll($criteria);
	
		if($pageSize > 0 && !$is_mobile) {	
			return array('payments_driver' => $payments_driver, 'pages' => $pages);
		} else {
			return $payments_driver;
		}	
	}
		
	// возвращает историю отзывов водителя
	public static function GetDriverReviews($id_driver, $pageSize = 0, $is_mobile = false, $date_interval = null, $order_str = null) {
		$criteria = new CDbCriteria();
		$criteria->addCondition("id_driver = ".$id_driver);
		
		if(isset($date_interval['start_date']) && !empty($date_interval['start_date'])) {
			$criteria->addCondition("date_review >= '".date('Y-m-d', strtotime($date_interval['start_date']))."'");
		}
		if(isset($date_interval['end_date']) && !empty($date_interval['end_date'])) {
			$time = strtotime($date_interval['end_date']);
			$criteria->addCondition("date_review <= '".date('Y-m-d 23:59:59', $time)."'");	
		}
		if(!empty($order_str)) {
			$criteria->order = $order_str;
		} else {
			$criteria->order = 'date_review DESC';
		}
		
		$count = DriverReviews::model()->count($criteria);
		$pages = new CPagination($count);
		$pages->pageSize = $pageSize;
		$pages->applyLimit($criteria);
		
		$driver_reviews = DriverReviews::model()->findAll($criteria);
	
		if($pageSize > 0 && !$is_mobile) {	
			return array('driver_reviews' => $driver_reviews, 'pages' => $pages);
		} else {
			return $driver_reviews;
		}	
	}
	
	
	public static function GetDriverById($id_driver = null) {
		$driver = UserStatus::model()->findByAttributes(array('id_user' => $id_driver));
		return $driver;
	}
	
	// возвращает данные водителя
	public static function GetDriverData($id_driver = 0, $is_mobile = false) {
		if($id_driver == 0) {
			$driver = self::Сreate();
			$car = new Cars;
			$user_status = new UserStatus;
		} else {
			$driver = Users::model()->findByPk($id_driver);
			$car = Cars::model()->findByPk($driver->id_car);
			$user_status = self::GetDriverById($id_driver);
		} 
		
		if(!$is_mobile) {
			$services_driver = self::GetDriverServices($id_driver);
			$driver_commissions = self::GetDriverCommissions($id_driver);
			
			$result = array('driver' => $driver, 'car' => $car, 'user_status' => $user_status, 'services_driver' => $services_driver, 'driver_commissions' => $driver_commissions);
		} else {
			$result = array('driver' => $driver, 'car' => $car, 'user_status' => $user_status);
		}
	
		return $result;	
	}
	
	// возвращает преобразованый объект UserStatus водителя в массив
	public static function GetDriverInfoArray(UserStatus $dr, $order_drivers = NULL) {
		$driver = $dr->user->GetDriverInfo();
		
		if(!empty($order_drivers)) {
			if(in_array($dr->user->id, $order_drivers)) {
				$driver['failure'] = 1;
			} else {
				$driver['failure'] = 0;
			}
		}
		$driver['lat'] = $dr->lat;
		$driver['lng'] = $dr->lng;
		
		return $driver;
	}
	
	
	// возвращает регулярных отичслений водителя
	public static function GetSummRegularPayments($id_driver, $is_weekly = false) {
		$driver_commissions = DriverCommission::model()->findAllByAttributes(array('id_driver' => $id_driver, 'is_weekly' =>$is_weekly));
		$comission = 0;
		if(!empty($driver_commissions)) {
			foreach($driver_commissions as $comm) {
				$comission += $comm->value;
			}
		}
		return $comission;
	}
	
	// регестрирует водителя и отправляет ему СМС 
	public static function CreateRecord($id_driver, $is_mobile = false, $driver_phone = null)
	{	
		if(!empty($id_driver)) {
			$user_status = new UserStatus;
			if($is_mobile) {
				$properties = array(
					'moderation' => 2,
					'id_status' => 1
				);
				
				$activate_key = MobileApplicationController::GeneratePassword(4);
				$properties['activate_key'] = crypt($activate_key);
				//print_r($user_status); exit;
			} else {
				$properties = array(
					'moderation' => 1,
					'id_status' => 3
				);
			}
			$properties['id_user'] = $id_driver;
			$properties['status_update'] = date('Y-m-d H:i:s', strtotime("now"));
			$properties['location_update'] = date('Y-m-d H:i:s', strtotime("now"));
			$user_status->SetProperties($properties);
			
			if($user_status->save() && $is_mobile && $activate_key !== null) {
				$TurboSMS = new TurboSMS('taxichat', 'taxichat', 'TaxiChat');
				$TurboSMS->setMassage('Регистрация прошла успешно. Ваш ключ для активации: '.$activate_key)->setPhone($driver_phone)->sendMassage();	
			}
			return $user_status;
		}
		return false;
	}
	
	// возвращает статус водителя в зависимости от того активирован и промодерирован ли он
	public static function GetStatus(UserStatus $driver_status) {
		if($driver_status->is_activate) {
			if($driver_status->moderation == 0) {
				$result = 1;
			} else {
				if($driver_status->moderation != 2) {
					$result = 4;
				} else {
					$result = 3;
				}
			}
		} else {
			$result = 2;
		}
		return $result;
	}
	
}
?>