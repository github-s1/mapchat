<?php

class OrdersController extends MobileApplicationController
{
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            //'postOnly + delete', // we only allow deletion via POST request
        );
    }

    public function accessRules()
    {
        return array(
            array('allow',
                'actions'=>array('free_ester', 'order_view', 'order_accept', 'refusal_order', 'customer_location', 'pre_orders', 'request_order', 'request_order_cancel', 'time_expired', 'refusal_update', 'update_accept'), 
                'users'=>array('*'),
            ),
            array('deny',
                'users'=>array('*'),
            ),
        );
    }
	
	public function actionFree_ester() {
		$driver_id = $this->is_authentificate();
		
		$driver_status = UserStatus::GetUserById($driver_id);
		if($driver_status->moderation != 0) {
			if($driver_status->moderation == 2) {
				echo json_encode(array('result' => 'failure', 'error' => 'Ваш профиль пока не промодерирован.')); exit;
			}
						
				$criteria=new CDbCriteria();
				
				//ищем заказы поблизости
				$criteria->mergeWith(array(
					'join'=>'INNER JOIN orders_points from_point ON from_point.id = t.from INNER JOIN addresses from_adress ON from_adress.id = from_point.id_adress',
					'condition'=>'((DEGREES(ACOS((SIN(RADIANS('.$driver_status->lat.')) * SIN(RADIANS(cast(from_adress.latitude as DECIMAL(8,2))))) + (COS(RADIANS('.$driver_status->lat.')) * COS(RADIANS(cast(from_adress.latitude as DECIMAL(8,2)))) * COS(RADIANS('.$driver_status->lng.' - cast(from_adress.longitude as DECIMAL(8,2)))))))) * 60 * 1.1515 * 1.609344) < 5'
				));
				
				$criteria->addCondition("execution_status = 1 AND is_show_free_ester = 1");
				//$criteria->addCondition("id_price_class = ".$driver_status->user->id_price_class);
				$display_drivers_for = Settings::model()->findByAttributes(array('param' =>'display_drivers_for'));
				$criteria->addCondition("order_date < '".date('Y-m-d H:i:s', strtotime("now") + ($display_drivers_for->value * 60))."'");
				
				$criteria->order = 'id DESC';
				//формируем массив с с полной инфой по этим заказам  
				$orders = Orders::model()->findAll($criteria);
				$orders_array  = array();
				if(!empty($orders)) {
					foreach($orders as $ord) {
						$orders_array[] = $ord->GetOrderAdvancedInfo($driver_id);	
					}
				}
				//$send_array = array('Orders' => $orders_array);
				echo json_encode(array('response' => $orders_array, 'balance' => $driver_status->user->balance)); 	
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Ваш профиль был заблокирован')); exit;
		}	
	}
	
	public function actionPre_orders()
	{
		$driver_id = $this->is_authentificate();
		//$driver_id = 129;
		$orders = Orders::model()->findAllByAttributes(array('execution_status' => 1, 'id_status' => 1, 'id_driver' => $driver_id), array('order'=>'order_date DESC'));
		$orders_array  = array();
		if(!empty($orders)) {
			foreach($orders as $i=>$ord) {
				$orders_array[] = $ord->GetOrderAdvancedInfo($driver_id);
			}
		} 
		echo json_encode(array('response' => $orders_array));
	}
	
	public function actionOrder_view($id)
	{		
		$driver_id = $this->is_authentificate();
		//$driver_id = 129;
		$order = Orders::model()->findByPk($id);
		$order_array = array();
		if(!empty($order)) {
			$order_array = $order->GetOrderAdvancedInfo($driver_id);
		}		
		echo json_encode(array('response' => $order_array)); 	
	}
	
	public function actionRequest_order($id)
	{		
		$driver_id = $this->is_authentificate();
		//$driver_id = 138;
		
		$driver = UserStatus::GetUserById($driver_id);
		if($driver->id_status == 1) {
			$order = Orders::model()->findByPk($id);
			
			if(!empty($order)) {
				$customer = UserStatus::GetUserById($order->id_customer);
				if($customer->id_status == 1 && $order->execution_status == 1) {
					// отправляем запрос клиенту на выполнение заказа
					$order->SandRequestPerform($driver_id, 0);
					$customer->SendPush('Водитель хочет взять ваш заказ.', ['push_type' => 3, 'driver_id' => $driver_id, 'order_id' => $order->id, 'time' => time()], true);
					
					echo json_encode(array('result' => 'success'));
				} else {
					echo json_encode(array('result' => 'failure', 'error' => 'Клиент занят либо не в сети.'));
				}
			} else {
				echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.')); 
			}
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Чтобы взять заказ задайте статус "Свободен".')); 
		}	
	}
	
	public function actionRequest_order_cancel($id) { 
		$driver_id = $this->is_authentificate();
		
		$order = Orders::model()->findByPk($id);
		if(!empty($order)) {
			// получаем информацию по запросу на выполнение
			$order_driver = OrderDriver::GetDriverRequest($order->id, $driver_id, 0);
		
			if($order_driver->adopted == 1) {
				$customer = UserStatus::GetUserById($order->id_customer);
				$customer->SendPush('Водитель отказался от заказа.', ['push_type' => 4, 'driver_id' => $driver_id, 'order_id' => $order->id, 'time' => time()], true);
			}
			// выполняем отказ клиента от заказа
			$order_driver->DriverRefused($order->id, $driver_id);
			
			echo json_encode(array('result' => 'success'));
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.'));
		}
			
	}
	
	public function actionTime_expired($id) {
		$driver_id = $this->is_authentificate();
		$order = Orders::model()->findByPk($id);
		if(!empty($order)) {
			// получаем информацию по запросу на выполнение
			$order_driver = OrderDriver::GetDriverRequest($order->id, $driver_id, 0);
			if($order_driver->adopted == 1) {
				echo json_encode(array('result' => 'success'));
			} else {
				// выполняем отказ клиента от заказа
				$order_driver->ClientRefused($order->id, $driver_id);
				echo json_encode(array('result' => 'failure', 'error' => 'Клиент не дал ответ или отказался.'));			
			}	
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.'));	
		}
	}
	
	public function actionOrder_accept($id) { 
		$driver_id = $this->is_authentificate();
		//$driver_id = 138;
		$order = Orders::model()->findByPk($id);
		
		if(!empty($order)) {
			$customer = UserStatus::GetUserById($order->id_customer);
			if($customer->id_status == 1 && $order->execution_status == 1) {
				$order_driver = OrderDriver::model()->findByAttributes(array('id_order' => $order->id, 'id_driver' => $driver_id ));
				if(!empty($order_driver)) {
					if($order_driver->adopted == 0) {
						$driver = UserStatus::GetUserById($driver_id);
						if($driver->user->balance > 0) {
							$driver->ChangeStatus(null, 2);
							
							// помечаем запрос как принятый
							$order_driver->OrderAccept($order->id, $driver_id);
							// помечаем заказ как выполняющийся
							$order->ChangeExecuting(2, $driver_id, 1);							
							
							$customer->SendPush('Водитель принял заказ.', ['push_type' => 1, 'order_id' => $order->id, 'time' => time()], true);

							echo json_encode(array('result' => 'success'));	
							// отсылаем пуши водителям поблизости
							$this->backgroundPost('http://'.Yii::app()->params['siteIP'].'/customer_application/orders/drivers_subscribe/id/'.$order->id);
						} else {
							echo json_encode(array('result' => 'failure', 'error' => 'У Вас на счету недостаточно средств для принятия заказа.'));
						}	
					} else {
						echo json_encode(array('result' => 'failure', 'error' => 'Клиент отменил заказ.'));
					}	
				} else {
					echo json_encode(array('result' => 'failure', 'error' => 'Клиент отменил заказ.')); 
				}	
			} else {
				echo json_encode(array('result' => 'failure', 'error' => 'Клиент занят либо не в сети.')); 	
			}
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.')); 	
		}
	}
	
	public function actionRefusal_order($id) {
		$driver_id = $this->is_authentificate();
		//$driver_id = 205;
		$order = Orders::model()->findByPk($id);
		
		if(!empty($order)) {
		
			$order_driver = OrderDriver::model()->findByAttributes(array('id_order' => $id, 'id_driver' => $driver_id));
			
			if(!empty($order_driver)) {
				
				if($order_driver->adopted != 2 && $order_driver->adopted != 3) {
					//помечаем запрос как не приянтый
					$order_driver->DriverRefused($order->id, $driver_id);
					//накладываем штарф водителю
					$this->FineForFailure($driver_id, $order->is_preliminary);
					// помечаем заказ как заказ с нулевой комиссией
					$order->SetCommission(0);
				} else {
					echo json_encode(array('result' => 'failure', 'error' => 'Клиент отменил заказ.')); exit;
				}	
				$customer = UserStatus::GetUserById($order->id_customer);
				
				if(!$order_driver->is_dispatcher_creator) {	
					$customer->SendPush('Водитель отказался от заказа.', ['push_type' => 2, 'order_id' => $order->id], true);
				}
				echo json_encode(array('result' => 'success'));	
			} else {
					echo json_encode(array('result' => 'failure', 'error' => 'Клиент отменил заказ.'));
			}
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.')); 
		}
	}
	
	public function actionUpdate_accept($id)
	{
		$driver_id = $this->is_authentificate();
		
		$order = Orders::model()->findByPk($id);
		if(!empty($order)) {
			$customer = UserStatus::GetUserById($order->id_customer);
				
			if(!empty($customer)) {	
				$customer->SendPush('Водитель принял измененный заказ.', ['push_type' => 11, 'order_id' => $order->id, 'time' => time()], true);
			}
			echo json_encode(array('result' => 'success'));
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.'));	
		}
	}
	
	public function actionRefusal_update($id)
	{
		$driver_id = $this->is_authentificate();
		
		$order = Orders::model()->findByPk($id);
		if(!empty($order)) {
			// помечаем заказ как ожидающий выполнения
			$order->SetAvailable();
			
			$driver = UserStatus::GetUserById($driver_id);
			if(!empty($driver)) {
				$driver->ChangeStatus(null, 1);
			}
			
			$customer = UserStatus::GetUserById($order->id_customer);
			if(!empty($customer)) {	
				$customer->SendPush('Водитель отказался выполнять измененный заказ.', ['push_type' => 14, 'order_id' => $order->id],true);
			}
			echo json_encode(array('result' => 'success'));	
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.'));
		}
	}
}