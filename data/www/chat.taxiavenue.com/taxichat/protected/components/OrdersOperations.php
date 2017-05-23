<?php
class OrdersOperations 
{	
	// формирует свободный маршрут во время выполнения
	public static function StoreCustomRoute($driver_id, $lat = null, $lng = null)
	{	
		$order = Orders::model()->findByAttributes(array('execution_status' => 2, 'id_status' => 5, 'is_custom_route' => 0, 'id_driver' => $driver_id), array('order'=>'order_date DESC'));
		if(!empty($order)) {
			if(!empty($order->where)) {
				$order_points = OrdersPoints::model()->findAllByAttributes(array('id_order' => $order->id), array('order'=>'id ASC'));
				$count_points = count($order_points);
				if($count_points > 2) {
					foreach($order_points as $i => $point) {
						if($i > 0 && $i < ($count_points - 1) && $point->is_traversed == 0) {
							$length = (rad2deg(acos((sin(deg2rad($lat)) * sin(deg2rad($point->adress->latitude))) + (cos(deg2rad($lat)) * cos(deg2rad($point->adress->latitude)) * cos(deg2rad($lng - $point->adress->longitude)))))) * 60 * 1.1515 * 1.609344;	
							// помечаем точку как пройденую
							if($length < 0.05) {
								$point->is_traversed = 1;
								$point->save();
							}
						}
					}
				}
			} else {
				if(empty($order->custom_route)) {
					$custom_route = array();
				} else {
					$custom_route = explode("; ", $order->custom_route);
				}
				$custom_route[] = $lat.', '.$lng;					
				$order->custom_route = implode("; ", $custom_route);
				$order->save();
			}	
		}
	}
	
	// возвращает массив заказов
	public static function GetOrdersInfo($orders = null)
	{	
		$orders_array  = array();
		$start_date = NULL;
		$end_date = NULL;
		if(!empty($orders)) {
			foreach($orders as $i=>$ord) {
				$orders_array[$i] = $ord->GetOrderInfo();
			}
			$start_date = $orders_array[count($orders_array) - 1]['order_date'];
			$end_date = $orders_array[0]['order_date'];
		}
		
		return array('orders_array' => $orders_array, 'start_date' => $start_date, 'end_date' => $end_date);
	}

}
?>