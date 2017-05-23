<?php
//класс содержащий костыли для Великого разработчика приложений под Android - Дашкевича тобишь Дмитрия
class OrderPrice
{
    static function calculationDistance($points)
	{
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
			
			$distance = self::getRouteDistance($startPoint, $destination, $path);
		} else {
			$distance = 0;
		}
		return $distance;
	}
	
	static function getRouteDistance($startPoint, $destination, $path)
	{
		$distance = 0;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://maps.googleapis.com/maps/api/directions/json?origin=$startPoint&destination=$destination&waypoints=$path&units=metric&mode=driving&sensor=false&mode=driving ");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$result = json_decode(curl_exec($ch), true);
		
		curl_close($ch);
		
		if(!empty($result['routes'][0]['legs'])) {
			foreach($result['routes'][0]['legs'] as $step) {
				$distance += $step['distance']['value'];	
			}
		}
		return $distance;
	}
	
	static function getDistance($A, $B)
	{	
		
		$R = 6378137; // Earth’s mean radius in meter
		$dLat = deg2rad($B[0] - $A[0]);
		$dLong = deg2rad($B[1] - $A[1]);
		$a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($A[0])) * cos(deg2rad($B[0])) * sin($dLong / 2) *($dLong / 2);
		$c = 2 * atan2(sqrt($a),sqrt(1 - $a));
		$d = $R * $c;
		
		return $d; // returns the distance in meter
	}
	
	static function getSale($curentPrice, $customer)
	{
	  $saleSettings=PermanentSettings::model()->findByAttributes(array('id' => 1));
	  $query = Orders::model()->countByAttributes(array('id_customer'=> $customer, 'execution_status'=>3));

	  if($query >= $saleSettings->orders)
		{ 
		  if($saleSettings->type = 0){
			 $price = $curentPrice - $saleSettings->value;
			 return $price;
		  }elseif($saleSettings->type = 1){
			 $price = $curentPrice - (($curentPrice * $saleSettings->value) / 100);
			 return $price;
		  }
		}else{
			return $curentPrice;
		}
	}
	
	static function PriceTimeTariff($price, $order_date)
	{
		$new_price = $price;
		
		$tariffs_time_day = TariffTimeDay::model()->findAll(array('order'=>'id ASC'));
		if(!empty($tariffs_time_day)) {
			$order_time = strtotime(date('H:i:s', strtotime(''.$order_date)));
			foreach($tariffs_time_day as $i => $t) {	
				if( $order_time > strtotime($t->from) && $order_time < strtotime($t->before) ) {
					if($t->is_percent) {
						$new_price += $price * $t->value / 100;
					} else {
						$new_price += $t->value;
					}	
				}
			}	
		}
		
		$tariffs_day_week = TariffDayWeek::model()->findAll(array('order'=>'id ASC'));
		if(!empty($tariffs_day_week)) {
			$order_day = date('w', strtotime(''.$order_date));
			foreach($tariffs_day_week as $i => $t) {	
				if( $order_day == $t->day_week ) {
					if($t->is_percent) {
						$new_price += $price * $t->value / 100;
					} else {
						$new_price += $t->value;
					}	
				}
			}	
		}
		
		$tariffs_time_interval = TariffTimeInterval::model()->findAll(array('order'=>'id ASC'));
		if(!empty($tariffs_time_interval)) {
			$order_time = strtotime(''.$order_date);
			foreach($tariffs_time_interval as $i => $t) {	
				if( $order_time > strtotime($t->from) && $order_time < strtotime($t->before) ) {
					if($t->is_percent) {
						$new_price += $price * $t->value / 100;
					} else {
						$new_price += $t->value;
					}	
				}
			}	
		}
		
		return $new_price;
	}
	/*
	static function PriceTimeDayTariff($price, $order_date)
	{
	
	}
	*/
	static function BobusesInfo($is_pay_bonuses, $bonuses, $price)
	{
		$rez = 0;
		if($is_pay_bonuses && $bonuses > 0) {
			$sub = $bonuses - $price;
			if($sub > 0) {
				$rez = $price;
			} else {
				$rez = $bonuses;
			}
		}
		return $rez;
	}
	
	static function PriceTariffZones($lat, $lng, $price_distance)
	{
		$price = 0;
		$tariff_zones = TariffZones::model()->findAll();
		$zones = array();
		if(!empty($tariff_zones)) {
			foreach($tariff_zones as $i => $zone){  
				$zones[$i] = MapPoint::CreateZone($zone);
			}
		}	
		$point = new MapPoint($lat, $lng);
		
		foreach($zones as $i => $z) {
			if($point->fPointInsidePolygon($z)) {
				if($tariff_zones[$i]->is_percent) {
					$price += $price_distance * $tariff_zones[$i]->value;
				} else {
					$price += $tariff_zones[$i]->value;
				}	
			} 
		}
		return $price;
	}
	
}