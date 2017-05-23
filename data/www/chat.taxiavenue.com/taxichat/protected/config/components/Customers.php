<?php
class Customers 
{	
	public static function Сreate() {
		$user = new Users;
		$user->id_type = 2;
		return $user;
	}
	
	public static function GetAllCustomersByCriteria($pageSize = 0, $search = null, $order_str = null) {
		$criteria=new CDbCriteria();
		$criteria->mergeWith(array(
			'join'=>'INNER JOIN users customer ON customer.id = t.id_user',
			'condition'=>'customer.id_type = 2'
		));
		if($search !== null) {
			$criteria->mergeWith(array(
				'join'=>'INNER JOIN users customer ON customer.id = t.id_user',
				'condition'=>'LOWER(customer.phone) LIKE :customer OR LOWER(customer.name) LIKE :customer OR LOWER(customer.surname) LIKE :customer OR LOWER(customer.email) LIKE :customer OR LOWER(customer.nickname) LIKE :customer',
				'params'=>array(':customer'=>'%'.mb_strtolower($search, 'UTF-8').'%')
			));
		}
		
		
		if(empty($order_str)) {
			$criteria->order = 'moderation DESC, id DESC';
		} else {
			$criteria->order = $order_str;
		}
		$pages = 0;
		if($pageSize > 0) {
			$count = UserStatus::model()->count($criteria);
			$pages = new CPagination($count);
			$pages->pageSize = $pageSize;
			$pages->applyLimit($criteria);
		}
		
		$customers = UserStatus::model()->findAll($criteria);
		
		return array('customers' => $customers, 'pages' => $pages);
	}
	
	/*
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
	*/

	public static function GetCustomerData($id = 0) {
		if($id == 0) {
			$customer = self::Сreate();
			$user_status = new UserStatus;
		} else {
			$customer = Users::model()->findByPk($id);
			$user_status = UserStatus::GetUserById($id);
		} 
		
		$result = array('customer' => $customer, 'user_status' => $user_status);
	
		return $result;	
	}
	
	public static function CreateRecord($id_customer, $is_mobile = false, $phone = null)
	{	
		if(!empty($id_customer)) {
			$user_status = new UserStatus;
			if($is_mobile) {
				$properties = array(
					'id_status' => 1
				);
				$activate_key = MobileApplicationController::GeneratePassword(10);
				$properties['activate_key'] = crypt($activate_key);
			} else {
				$properties = array(
					'id_status' => 3,
					'is_activate' => 1
				);
			}	
				
			$properties['moderation'] = 1;
			$properties['id_user'] = $id_customer;
			$properties['status_update'] = date('Y-m-d H:i:s', strtotime("now"));
			$properties['location_update'] = date('Y-m-d H:i:s', strtotime("now"));
			
			$user_status->SetProperties($properties);
			
			if($user_status->save() && $is_mobile) {
				$TurboSMS = new TurboSMS('taxichat', 'taxichat', 'TaxiChat');
				$TurboSMS->setMassage('Регистрация прошла успешно. Ваш ключ для активации: '.$activate_key)->setPhone($phone)->sendMassage();	
			}
			return $user_status;
		}
		return false;
	}
	
	public static function GetOrdersArchive($id_customer, $limit = 0, $date_interval = null, $order_str = null) {
		$criteria = new CDbCriteria();
		$criteria->addCondition("is_archive_delete = 0 AND execution_status > 2 AND id_customer =".$id_customer);
		
		if(isset($date_interval['start_date']) && !empty($date_interval['start_date'])) {
			$criteria->addCondition("order_date >= '".date('Y-m-d', strtotime($date_interval['start_date']))."'");
		}
		if(isset($date_interval['end_date']) && !empty($date_interval['end_date'])) {
			$time = strtotime($date_interval['end_date']);
			$criteria->addCondition("order_date <= '".date('Y-m-d 23:59:59', $time)."'");	
		}
		if(!empty($order_str)) {
			$criteria->order = $order_str;
		} else {
			$criteria->order = 'order_date DESC';
		}
		if(!empty($limit)) {
			$criteria->limit = $limit;
		}	

		$driver_archive = Orders::model()->findAll($criteria);
	
		return $driver_archive;	
	}
	
	public static function GetCustomerReviews($id_customer, $date_interval = null, $order_str = null) {
		$criteria = new CDbCriteria();
		$criteria->addCondition("id_customer = ".$id_customer);
		
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
		
		$customer_reviews = DriverReviews::model()->findAll($criteria);
	
		return $customer_reviews;	
	}
	
	public static function GetHistoryBonuses($id_customer, $limit = 0, $date_interval = null, $order_str = null) {
		$criteria = new CDbCriteria();
		$criteria->addCondition("id_user = ".$id_customer);
		
		if(isset($date_interval['start_date']) && !empty($date_interval['start_date'])) {
			$criteria->addCondition("date_create >= '".date('Y-m-d', strtotime($date_interval['start_date']))."'");
		}
		if(isset($date_interval['end_date']) && !empty($date_interval['end_date'])) {
			$time = strtotime($date_interval['end_date']);
			$criteria->addCondition("date_create <= '".date('Y-m-d 23:59:59', $time)."'");	
		}
		if(!empty($order_str)) {
			$criteria->order = $order_str;
		} else {
			$criteria->order = 'id DESC';
		}
		if(!empty($limit)) {
			$criteria->limit = $limit;
		}
		
		$bonuses_history = BonusesHistory::model()->findAll($criteria);
	
		return $bonuses_history;	
	}
	
}
?>