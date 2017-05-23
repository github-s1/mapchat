<?php
class Customers 
{	
	// создает и инициализирует объект для клиента 
	public static function Сreate() {
		$user = new Users;
		$user->id_type = 2;
		return $user;
	}
	
	// возвращает клиентов согласно критериям
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
	
	public static function GetCustomerById($id = null) {
		$customer = UserStatus::model()->findByAttributes(array('id_user' => $id));
		return $customer;
	}
	
	// возвращает данные клиента
	public static function GetCustomerData($id = 0) {
		if($id == 0) {
			$customer = self::Сreate();
			$user_status = new UserStatus;
		} else {
			$customer = Users::model()->findByPk($id);
			$user_status = self::GetCustomerById($id);
		} 
		
		$result = array('customer' => $customer, 'user_status' => $user_status);
	
		return $result;	
	}
	
	// регестрирует клиента и отправляет ему СМС 
	public static function CreateRecord($id_customer, $phone = null)
	{	
		if(!empty($id_customer)) {
			$user_status = new UserStatus;
			$properties = array(
				'moderation' => 1,
				'id_status' => 1
			);
			$activate_key = MobileApplicationController::GeneratePassword(10);
			$properties['activate_key'] = crypt($activate_key);
			$properties['id_user'] = $id_customer;
			$properties['status_update'] = date('Y-m-d H:i:s', strtotime("now"));
			$properties['location_update'] = date('Y-m-d H:i:s', strtotime("now"));
			$user_status->SetProperties($properties);
			
			if($user_status->save()) {
				$TurboSMS = new TurboSMS('taxichat', 'taxichat', 'TaxiChat');
				$TurboSMS->setMassage('Регистрация прошла успешно. Ваш ключ для активации: '.$activate_key)->setPhone($phone)->sendMassage();	
			}
			return $user_status;
		}
		return false;
	}
	
	// возвращает архив заказов клиента за выбраный период
	public static function GetOrdersArchive($id_customer, $limit = 0, $date_interval = null, $order_str = null) {
		$criteria = new CDbCriteria();
		$criteria->addCondition("is_archive_delete = false AND execution_status > 2 AND id_customer =".$id_customer);
		
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
	
	// возвращает отзывы клиента за выбраный период
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
	
	// возвращает бонусы клиента за выбраный период
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
	
	// возвращает стстус клиента в зависимости от того активирован ли он
	public static function GetStatus(UserStatus $user_status) {
		if($user_status->is_activate) {
			$result = 3;
		} else {
			$result = 2;
		}
		return $result;
	}
	
}
?>