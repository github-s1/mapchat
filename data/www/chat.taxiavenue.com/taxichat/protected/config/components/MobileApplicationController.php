<?php

class MobileApplicationController extends Controller
{
	private function IssetUser($id = 0)
	{	
		$user = UserStatus::model()->findByAttributes(array('id_user' => $id));
		if(!empty($user)) {
			return true;
		} else {
			return false;
		}
	}
	
	protected function is_authentificate()
	{
		
		//echo json_encode(array('result' => Yii::app()->user->id)); exit;
		if(!empty(Yii::app()->user->id) && $this->IssetUser(Yii::app()->user->id)) {
			return Yii::app()->user->id;
		} else {
			echo json_encode(array('result' => 'not authorized')); exit;
		}
	}
	
	protected function is_login()
	{
		if(!empty(Yii::app()->user->id) && $this->IssetUser(Yii::app()->user->id)) {
			echo json_encode(array('result' => 'success')); exit;
		} else {
			echo json_encode(array('result' => 'failure')); exit;
		}
	}
	
	public static function GetErrors($obj)
	{
		$ErrorsArray = $obj->getErrors();
		$errors = '';
		foreach($ErrorsArray as $arr) {
			foreach($arr as $err) {
				$errors .= $err.' ';
			}
		}
		return addslashes($errors);
	}
	
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
	
	protected function recalculationPrice($price = 0, &$distance = 0, &$price_distance = 0, &$price_without_class = 0, $customer_id)
	{
		$set = Settings::model()->findAll();
		$settings = array();
		foreach($set as $i => $s) {
			$settings[$s->param] = array('value'=>$s->value, 'type'=>$s->type);
		}
		$request = json_decode(file_get_contents('php://input'));

		if(!empty($request->point_add) && count($request->point_add)>0) {
			//$tariff_zones = TariffZones::model()->findAll(array('order'=>'id ASC'));
			//print_r($_POST); exit;
			/*
			$points = array();
			if(isset($request->order_points)) {
				foreach((array)$request->order_points as $order_points) {
					$points[] = (array)$order_points;
				}
			}
			*/
			//echo json_encode(array('response' => $request->point_add)); exit;
			if(isset($request->point_add)) {
				foreach((array)$request->point_add as $point_add) {
					if(!empty($point_add->latitude) && !empty($point_add->longitude))
						$points[] = (array)$point_add;
				}
			}
			
			if(count($points) > 1) {
				$path = '';
				foreach($points as $i => $p) {
					if($i > 0)
						$path .= $p['latitude'] . ',' . $p['longitude'] . '|';
				}
				$path = rtrim($path, '|');
				
				$startPoint = $points[0]['latitude'] . ',' . $points[0]['longitude'];
				
				$countPoint = count($points); 
				$destination = $points[$countPoint - 1]['latitude'] . ',' . $points[$countPoint - 1]['longitude'];
				
				$distance = OrderPrice::getRouteDistance($startPoint, $destination, $path);
			}	
			$distance /= 1000;
			
			if($distance != 0) {
				$price_distance = $settings['price_kilometer']['value'] * $distance;
				$price = $price_distance;
				$price_without_class = $price_distance;
			}
			
			if($request->Orders->is_preliminary == 1) {
				if($settings['preliminary']['type'] == '1')
					$price += $settings['preliminary']['value'] * $price_distance / 100;
				else
					$price += $settings['preliminary']['value'];
			}		
			
			$prom_points_count = count($points) - 2;
			if(($prom_points_count) > 0) { 
				if($settings['intermediate_point']['type'] == '1') 
					$price += $price_distance * $prom_points_count * $settings['intermediate_point']['value'] / 100;
				else
					$price += $prom_points_count * $settings['intermediate_point']['value'];
			}
			
			if(isset($request->OrderService->id) && !empty($request->OrderService->id)) {	
				foreach($request->OrderService->id as $service_id) {
					$service = Services::model()->findByPk($service_id);
					if($service->is_percent) {
						$price += $price_distance * $service->value / 100;
					} else {
						$price += $service->value;
					}	
				}	
			}
			
			$price_tariff_zones = OrderPrice::PriceTariffZones($points[0]['latitude'], $points[0]['longitude'], $price_distance);
			
			$price += $price_tariff_zones;
			
			if(isset($request->Orders->order_date)) {
				$price = OrderPrice::PriceTimeTariff($price, $request->Orders->order_date);
			}
			
			$price_without_class = round($price, 2);
			
			if($request->Orders->id_price_class != 1) {
				$price_class = PriceClass::model()->findByPk($request->Orders->id_price_class);
				if($price_class->is_percent)
					$price += $price_distance * $price_class->value / 100;
				else
					$price += $price_class->value;
			}
			
			$price = OrderPrice::getSale($price, $customer_id);
			
			if($price < $settings['min_order_price']['value']) {
				$price = $settings['min_order_price']['value'];
			} else {
				$price = round($price, 2);		
			}	
		} else {
			$price = $settings['min_order_price']['value'];
		}
		
		return $price;
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
	
	public static function AddressAdd($properties)
	{
		$adress = new Addresses;
		$adress->attributes = $properties;
		$adress->name = htmlspecialchars($adress->name, ENT_QUOTES);
		$adress->save();
		
		return $adress;
	}
	
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
	
}