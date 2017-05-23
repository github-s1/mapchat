<?php
//содержит методы для API моб. приложений
class MobileApplicationController extends Controller
{
	//метод проверяет существует ли пользователь с таким id
	private function IssetUser($id = 0)
	{	
		$user = UserStatus::model()->findByAttributes(array('id_user' => $id));
		if(!empty($user)) {
			return true;
		} else {
			return false;
		}
	}
	
	//метод проверяет авторизирован ли пользователь, если да возвращает его id
	protected function is_authentificate()
	{
		if(!empty(Yii::app()->user->id) && $this->IssetUser(Yii::app()->user->id)) {
			return Yii::app()->user->id;
		} else {
			echo json_encode(array('result' => 'not authorized')); exit;
		}
	}
	
	//метод проверяет авторизирован ли пользователь
	protected function is_login()
	{
		if(!empty(Yii::app()->user->id) && $this->IssetUser(Yii::app()->user->id)) {
			return true;
		} 
		return false;
	}
	
	//метод проверяет авторизирован ли пользователь
	static function GetErrors(CActiveRecord $obj)
	{
		$ErrorsArray = $obj->getErrors();
		$errors = '';
		foreach($ErrorsArray as $arr) {
			foreach($arr as $err) {
				$errors .= $err.' ';
			}
		}
		return stripslashes($errors); 
	}
	
	//метод перобразует массив $_FILES в нужный формат
	protected function filesRegProcessing($index_user = "Users", $index_car = "Cars")
	{
		if(!empty($_FILES)) {
			$files = $_FILES;
			$_FILES = array();
			foreach($files as $i=>$f) {
				$element = $index_car;
				if($i == 'photo') 
					$element = $index_user;
				$_FILES[$element]['name'][$i] = $f['name'];
				$_FILES[$element]['type'][$i] = $f['type'];
				$_FILES[$element]['tmp_name'][$i] = $f['tmp_name'];
				$_FILES[$element]['error'][$i] = $f['error'];
				$_FILES[$element]['size'][$i] = $f['size'];
			}
		}
		return $_FILES; 
	}
	
	/*
	protected function recalculationOrder($price = 0, Orders $order, &$distance = 0, &$price_distance = 0, &$price_without_class = 0, Addresses $this_adress)
	{
		$order_points = OrdersPoints::model()->findAllByAttributes(array('id_order' => $order->id), array('order'=>'id ASC'));

		if(count($order_points) > 1) {
			$points = array();
			
			if(!empty($order_points)) {
				foreach($order_points as $i => $p) {
					if($i == 0 || ($i > 0 && $p->is_traversed == 1))
						$points[] = $p->adress->getAttributes();
				}
			}
			
			if(!empty($this_adress)) {							
				$points[] = $this_adress->getAttributes();
			}
			$distance = OrderPrice::calculationDistance($points);
		} else {
			if(!empty($order->custom_route)) {
				$custom_route = explode("; ", $order->custom_route);
				
				if(!empty($custom_route)) {
					$route_points = array();
					foreach($custom_route as $p) {
						$route_points = explode(", ", $p);
					}
					
					for($i = 0; $i <= count($route_points) - 2; $i ++) {
						$distance += OrderPrice::getDistance($route_points[$i], $route_points[$i + 1]);
					}
					
				}
			}
		}
		
		$price = $this->PriceCalculation($price, $order, $distance, $price_distance, $price_without_class, count($order_points));
		
		
		return $price;
	}
	*/
	
	//метод генерирует случайный пароль
	public static function GeneratePassword($max)
	{
		$chars="1234567890";
		$size=StrLen($chars)-1;
		$password=null;
		while($max--) {
			$password.=$chars[rand(0,$size)];
		}
		return $password;
	}
	
	//метод добавляет адрес в базу
	public static function AddressAdd($properties)
	{
		$adress = new Addresses;
		$adress->attributes = $properties;
		$adress->name = htmlspecialchars($adress->name, ENT_QUOTES);
		$adress->save();
		
		return $adress;
	}
	
	//метод добавляет точку к заказу
	public static function PointsAdd($order, $adress, $entrance = null)
	{
		$new_point = new OrdersPoints;
		$new_point->id_order = $order;
		$new_point->id_adress = $adress;
		if(!empty($entrance)) {
			$new_point->entrance = $entrance;
		}	
		$new_point->save();
		
		return $new_point;
	}
	
	//метод возвращает адрес по координатам
	public static function Geocoder($lat=null,$lng=null)
	{
		if(isset($lat) && (isset($lng))){
            $adress = '';
			$params = array(
                'latlng' => $lat .','. $lng,
                'sensor' => 'false',
                'language'=>'ru'
            );			
			$response = json_decode(file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?' . http_build_query($params, '', '&')));
			if(!empty($response->results))
            {
				$adress =  $response->results[0]->formatted_address;	
			} else {
				$adress = 'unknown point';
			}
			return $adress;
		}	
		else {
			echo json_encode(array('result' => 'failure', 'error' => 'Не передаются координаты текущей точки.')); exit;
		}
	}
	
	//метод активирует пользователя в случае если передается верный активационный ключ
	static function IsActivate($user_id = null)
	{
		$request = json_decode(file_get_contents('php://input'));
		if(!empty($request->activate_key))	{
			$user = UserStatus::model()->findByAttributes(array('id_user' => $user_id));
			
			if(!empty($user->activate_key) && ($user->activate_key !== crypt($request->activate_key,$user->activate_key))) {
				echo json_encode(array('result' => 'failure', 'error' => 'Активационный код не подходит.'));
			} else {
				$user->is_activate = 1;
				$user->activate_key = NULL;
				$user->save();
				echo json_encode(array('result' => 'success'));
			}
			
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Не передается активационный код.'));
		}
	}
	
	public function LogResult($text)
	{
			$str = $text.' - '.date('Y-m-d H:i:s', strtotime("now"));
			$fp = fopen(Yii::getPathOfAlias('webroot.protected').DIRECTORY_SEPARATOR.'result.txt', 'a');
			$test = fwrite($fp, $str.PHP_EOL);
			fclose($fp);
	}
	
	//метод генерирует пароль и отправляет его пользователю СМС-кой
	public static function SendActivateKey() {
		$user_id = $this->is_authentificate();
		$user_status = UserStatus::GetUserById($user_id);
		$activate_key = self::GeneratePassword(4);
		$user_status->activate_key = crypt($activate_key);
		if($user_status->save() && $activate_key !== null) {
			$TurboSMS = new TurboSMS('taxichat', 'taxichat', 'TaxiChat');
			$TurboSMS->setMassage('Ваш ключ для активации: '.$activate_key)->setPhone($user_status->user->phone)->sendMassage();	
			$result = 'success';
		} else {
			$result = 'failure';
		}
		echo json_encode(array('result' => $result));
	}
	
}