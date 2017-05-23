<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();

	protected function recalculationPrice($price = 0, &$distance = 0, &$price_distance = 0, &$price_without_class = 0, $customer_id)
	{
		$set = Settings::model()->findAll();
		$settings = array();
		foreach($set as $i => $s) {
			$settings[$s->param] = array('value'=>$s->value, 'type'=>$s->type);
		}
		
		if(isset($_POST['order_points']) || !empty($_POST['point_add'][0]['name'])) {
			//$tariff_zones = TariffZones::model()->findAll(array('order'=>'id ASC'));
			//print_r($_POST); exit;
			$points = array();
			if(isset($_POST['order_points'])) {
				foreach($_POST['order_points'] as $order_points) {
					$points[] = $order_points;
				}
			}
			if(isset($_POST['point_add'])) {
				foreach($_POST['point_add'] as $point_add) {
					if(!empty($point_add['latitude']) && !empty($point_add['longitude']))
						$points[] = $point_add;
				}
			}			
			$distance = OrderPrice::calculationDistance($points);
			
			$distance /= 1000;
			
			if($distance != 0) {
				$price_distance = $settings['price_kilometer']['value'] * $distance;
				$price = $price_distance;
				$price_without_class = $price_distance;
			}
			
			if($_POST['Orders']['is_preliminary'] == 1) {
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
			
			if(isset($_POST['OrderService']['id']) && !empty($_POST['OrderService']['id'][0])) {
				foreach($_POST['OrderService']['id'] as $service_id) {
					$service = Services::model()->findByPk($service_id);
					if($service->is_percent)
						$price += $price_distance * $service->value / 100;
					else
						$price += $service->value;
				}	
			}
			
			$price_tariff_zones = OrderPrice::PriceTariffZones($points[0]['latitude'], $points[0]['longitude'], $price_distance);
			
			$price += $price_tariff_zones;
			
			$price = OrderPrice::PriceTimeTariff($price, $_POST['Orders']['order_date']);
			
			$price_without_class = round($price, 2);
			
			if($_POST['Orders']['id_price_class'] != 1) {
				$price_class = PriceClass::model()->findByPk($_POST['Orders']['id_price_class']);
				if($price_class->is_percent) {
					$price += $price_distance * $price_class->value /100;
				} else {
					$price += $price_class->value;
				}	
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
	
	protected function FineForFailure($driver_id, $is_preliminary)
	{
		$set = Settings::model()->findAll();
		$settings = array();
		foreach($set as $i => $s) {
			$settings[$s->param] = array('value'=>$s->value, 'type'=>$s->type);
		}
		$driver = Users::model()->findByPk($driver_id);
		
		$new_fine = new PaymentsHistory;
		$new_fine->id_user = $driver_id;
		$new_fine->id_type = 4;
		$new_fine->descr = 'Отказ от заказа';
		if($is_preliminary) {
			$fine_rejection_order = $settings['fine_rejection_proposed_order']['value'];	
			$downgrade_rejection_order = $settings['downgrade_rejection_proposed_order']['value'];
		} else {
			$fine_rejection_order = $settings['fine_rejection_preorder']['value'];
			$downgrade_rejection_order = $settings['downgrade_rejection_preorder']['value'];	
		}
		$new_fine->value = $fine_rejection_order;
		$balance = $driver->balance - $new_fine->value;
		$rating = $driver->rating - $downgrade_rejection_order;
		if($rating < 0)
			$rating = 0;
		$new_fine->balance = $balance;
		$new_fine->rating = $rating;
		$new_fine->save();
		$driver->balance = $balance;
		$driver->rating = $rating;
		$driver->save();
		
		return true; 
	}
	
	protected function FineForRemoval($driver_id, $is_preliminary)
	{
		$set = Settings::model()->findAll();
		$settings = array();
		foreach($set as $i => $s) {
			$settings[$s->param] = array('value'=>$s->value, 'type'=>$s->type);
		}
		
		$driver = Users::model()->findByPk($driver_id);
		
		$new_fine = new PaymentsHistory;
		$new_fine->id_user = $driver_id;
		$new_fine->id_type = 4;
		$new_fine->descr = 'Снятие с заказа';
		
		//if($is_preliminary) {
		if($is_preliminary) {
			$removal_from_order_price = $settings['removal_from_order_price']['value'];
			$removal_from_order = $settings['removal_from_order']['value'];	
		} else {
			$removal_from_order_price = $settings['removal_from_pre_order_price']['value'];	
			$removal_from_order = $settings['removal_from_pre_order']['value'];
		}
		$new_fine->value = $removal_from_order_price;
		$balance = $driver->balance - $new_fine->value;
		$rating = $driver->rating - $removal_from_order;
		if($rating < 0) {
			$rating = 0;
		}	
		$new_fine->balance = $balance;
		$new_fine->rating = $rating;
		$new_fine->save();
		$driver->balance = $balance;
		$driver->rating = $rating;
		$driver->save();
		
		return true;  
	}
	
	public function ApplyFilter(array $Data, $url='index')
	{
		if(!empty($Data)) {
			$parameters = '?';
			foreach ($Data['filter'] as $k=>$v) { 
				if(!empty($v)) {
					$parameters.=$k."=".$v.'&';
				}
	        }
			$this->redirect(array($url.$parameters)); 
		} 
	}
	
	public function backgroundPost($url){
	  $parts = parse_url($url);
		
	  $fp = fsockopen($parts['host'],
			  isset($parts['port'])?$parts['port']:80,
			  $errno, $errstr, 30);
	 
	  if (!$fp) {
		  return false;
	  } else {
		  $out = "POST ".$parts['path']." HTTP/1.1\r\n";
		  $out.= "Host: ".$parts['host']."\r\n";
		  $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
		 
		  if (isset($parts['query'])) {
			$out.= "Content-Length: ".strlen($parts['query'])."\r\n";
			$out.= "Connection: Close\r\n\r\n";
			$out.= $parts['query'];
		  } else {
			$out.= "Content-Length: 0\r\n";
			$out.= "Connection: Close\r\n\r\n";	
		  }
	 
		  fwrite($fp, $out);
		  fclose($fp);
		  return true;
	  }
	}
}