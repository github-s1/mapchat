<?php

class Orders_executionController extends MobileApplicationController
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
                'actions'=>array('send_delay', 'reached', 'time_delay_expired', 'driver_route', 'driver_route_time_expired', 'update_accept', 'refusal_update', 'idle', 'set_idle', 'client_came', 'client_not_came', 'complete_order', 'removal_order', 'order_points'),
                'users'=>array('*'),
            ),
            array('deny',
                'users'=>array('*'),
            ),
        );
    }
	
	public function actionSend_delay($id)
	{		
		$driver_id = $this->is_authentificate();
		//$driver_id = 138;
		$order = Orders::model()->findByPk($id);
		$request = json_decode(file_get_contents('php://input'));
		if(!empty($request->delay)) {
			if(!empty($order)) {
				// создаем запрос на опоздание
				$order_delay = OrdersDelay::Create($order->id, $request->delay);
				if($order_delay->save()) {
					$order->ChangeStatus(3);
					$customer = UserStatus::GetUserById($order->id_customer);
					
					$customer->SendPush('Водитель опаздывает, продлить ожидание на '.$request->delay.' минут?', ['push_type' => 8, 'delay' => $request->delay, 'order_id' => $order->id], true);
					
					echo json_encode(array('result' => 'success'));
				} else {
					$errors = MobileApplicationController::GetErrors($order_delay);
					echo json_encode(array('result' => 'failure', 'error' => $errors));
				}
				
			} else {
				echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.')); 
			}
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Не передается опоздание в минутах.')); 
		}	
	}
	
	public function actionTime_delay_expired($id)
	{		
		$driver_id = $this->is_authentificate();
		//$driver_id = 138;
		$order = Orders::model()->findByPk($id);
		if(!empty($order)) {
			$order->ChangeStatus(3);	
			$customer = UserStatus::GetUserById($order->id_customer);
			// подтверждаем опоздание
			$DelayData = OrdersDelay::ApplyDelay($id);
			
			if($DelayData['request']) {	
				$time_delay = $DelayData['order_delay']->value;
			} else {
				$time_delay = 5;	
			}
			
			$customer->SendPush('Водитель опаздывает, ожидание будет продлено на '.$time_delay.' минут.', ['push_type' => 8, 'delay' => $time_delay, 'order_id' => $order->id], true);
			
			echo json_encode(array('result' => 'success', 'delay_time' => $time_delay));
			
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.'));
		}		
	}
	
	public function actionReached($id)
	{		
		$driver_id = $this->is_authentificate();
		//$driver_id = 205;
		$order = Orders::model()->findByPk($id);
		if(!empty($order)) {
			$order->ChangeStatus(4);
			// получаем бесплатное время простоя
			$free_expectation = Settings::model()->findByAttributes(array('param' =>'free_expectation'));
			$free_simple = $free_expectation->value;
			if($order->is_preliminary) {
				$sub_sec = strtotime($order->order_date) - strtotime("now");
				if($sub_sec > 0) {
					$free_simple += round($sub_sec / 60);
				}
			}
			$customer = UserStatus::GetUserById($order->id_customer);
			$customer->SendPush('Машина подана.', ['push_type' => 9, 'time' => time(), 'free_simple' => $free_simple, 'order_id' => $order->id], true);
			
			echo json_encode(array('result' => 'success', 'free_simple' => $free_simple)); 
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.')); 
		}
	}	
	
	
	
	public function actionIdle($id)
	{	
		$driver_id = $this->is_authentificate();
		//$driver_id = 138;
		$order = Orders::model()->findByPk($id);
		
		if(!empty($order)) {
			$sand_array = array('down_time' => $order->down_time, 'idle_city_distance' => $order->idle_city_distance, 'idle_outside_city_distance' => $order->idle_outside_city_distance);
			echo json_encode(array('result' => $sand_array)); exit;
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.')); exit;	
		}
	}
	
	public function actionSet_idle($id)
	{
		$driver_id = $this->is_authentificate();
		//$driver_id = 138;
		$request = json_decode(file_get_contents('php://input'));
		if(isset($request->down_time, $request->idle_city_distance, $request->idle_outside_city_distance)) {
			$order = Orders::model()->findByPk($id);
			if(!empty($order)) {
				// меняем данные простоев
				$order->down_time = $request->down_time;
				$order->idle_city_distance = $request->idle_city_distance;
				$order->idle_outside_city_distance = $request->idle_outside_city_distance;
				$order->save();
				echo json_encode(array('result' => 'success')); exit;
			} else {
				echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.')); 	
			}
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Данные о простое не передаются.')); 
		}	
	}
	
	public function actionClient_came($id)
	{	
		$driver_id = $this->is_authentificate();
		//$driver_id = 205;
		$order = Orders::model()->findByPk($id);
		if(!empty($order)) {
			$order->ChangeStatus(5);
			$customer = UserStatus::GetUserById($order->id_customer);
			$customer->SendPush('Выполнение заказа началось.', ['push_type' => 11, 'order_id' => $order->id], true);
			echo json_encode(array('result' => 'success')); 
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.')); 	
		}	
	}
	
	public function actionClient_not_came($id)
	{
		$driver_id = $this->is_authentificate();
		//$driver_id = 205;
		$order = Orders::model()->findByPk($id);
		if(!empty($order)) {
			// помечаем заказ как отмененный
			$order->SetCancel(7);
			// меняем статус водителю
			$driver = UserStatus::GetUserById($driver_id);
			$driver->ChangeStatus(null, 1);
			$customer = UserStatus::GetUserById($order->id_customer);
			$customer->SendPush('Вы не вышли на заказ. Заказ был отменен.', ['push_type' => 10, 'order_id' => $order->id], true);
			echo json_encode(array('result' => 'success')); 
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.')); 
		}	
	}
	
	public function actionRemoval_order($id) {
		$driver_id = $this->is_authentificate();
		//$driver_id = 138;
		$order = Orders::model()->findByPk($id);
		if(!empty($order)) {
			// помечаем заказ как ожидающий выполнения
			$order->SetAvailable();
			$driver = UserStatus::GetUserById($driver_id);
			if(!empty($driver)) {
				// меняем статус водителю
				$driver->ChangeStatus(null, 1);
				// списываем штраф с водителя
				$this->FineForRemoval($driver_id, $order->is_preliminary);
			}
			
			$customer = UserStatus::GetUserById($order->id_customer);
			if(!empty($customer)) {	
				$customer->SendPush('Водитель отказался выполнять заказ.', ['push_type' => 15, 'order_id' => $order->id], true);
			}
			
			echo json_encode(array('result' => 'success'));	
			// рзсылаем пуши водителям поблизости
			$this->backgroundPost('http://'.Yii::app()->params['siteIP'].'/customer_application/orders/drivers_subscribe/id/'.$order->id);
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.')); 
		}
	}
	
	
	public function actionDriver_route($id) {
		$driver_id = $this->is_authentificate();
		//$customer_id = 145;
		
		$order = Orders::model()->findByPk($id);
		$request = json_decode(file_get_contents('php://input'));
		if(!empty($order)) {
			if($order->execution_status == 2 && $order->id_status == 5 && !empty($order->id_driver)) {
				if(!empty($request->lat) && !empty($request->lng)) {
					// получаем описание объекта по координатам
					$data = array('name' =>$this->Geocoder($request->lat,$request->lng), 'latitude' => $request->lat, 'longitude' => $request->lng);
					$start_adress = Addresses::model()->findByAttributes(array('name' => $data['name'], 'latitude' => $data['latitude'], 'longitude' => $data['longitude']));
					if(empty($start_adress)) {
						$start_adress = $this->AddressAdd($data);
					}
					//$order->CompletionOrder($start_adress);
					$new_order = new Orders;
					$new_order->CopyParentOrder($order);
					
					if($new_order->save()) {
						$start_point = $this->PointsAdd($new_order->id, $start_adress->id, null);
						
						//копируем услуги родительского заказа
						$services_ord = OrderService::model()->findAllByAttributes(array('id_order' => $order->id), array('order'=>'id ASC'));
						if(!empty($services_ord)) {
							foreach($services_ord as $serv) {
								$this_serv = new OrderService;
								$this_serv->id_service = $serv->id_service;
								$this_serv->id_order = $new_order->id;
								$this_serv->save();
							}
						}
						
						$from = OrdersPoints::model()->findByAttributes(array('id_order' => $new_order->id), array('order'=>'id ASC'), array('limit'=>1));
						if(!empty($from)) {
							$new_order->from = $from->id;
						}

						if(!$new_order->save()) {
							$errors = MobileApplicationController::GetErrors($new_order);
							echo json_encode(array('result' => 'failure', 'error' => $errors)); exit;
						}
						//запрос водителю на выполнение нового заказа
						$new_order->SandRequestPerform($driver_id, 0);
						$customer = UserStatus::GetUserById($order->id_customer);
						$customer->SendPush('Водитель выбрал для заказа свободный маршрут.', ['push_type' => 5, 'order_id' => $order->id, 'new_order_id' => $new_order->id], true);	
						
						echo json_encode(array('result' => 'success', 'id' => $new_order->id));
					} else {
						$errors = MobileApplicationController::GetErrors($new_order);
						echo json_encode(array('result' => 'failure', 'error' => $errors));
					}	
				} else {
					echo json_encode(array('result' => 'failure', 'error' => 'Не передается точка завершения.')); 
				}
			} else {
				echo json_encode(array('result' => 'failure', 'error' => 'Заказ находится не на стадии выполнения. Завершение невозможно.'));
			}
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.'));
		}	
	}
	
	public function actionDriver_route_time_expired($id) {
		$driver_id = $this->is_authentificate();
		$order = Orders::model()->findByPk($id);
		if(!empty($order)) {
			$order_driver = OrderDriver::GetDriverRequest($order->id, $driver_id, 0);
			
			if($order_driver->adopted == 1) {
				echo json_encode(array('result' => 'success')); exit;
			} else {
				// клиент отказался от выполнения заказа
				$order_driver->ClientRefused($order->id, $driver_id);
				$order->delete();
				
				echo json_encode(array('result' => 'failure', 'error' => 'Клиент не дал ответ либо отказался.')); exit;		
			}	
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.')); exit;	
		}
	}
	
	public function actionUpdate_accept($id) { 
		$driver_id = $this->is_authentificate();
		//$driver_id = 219;
		$order = Orders::model()->findByPk($id);
		
		if(!empty($order)) {
			$customer = UserStatus::GetUserById($order->id_customer);
			$order_driver = OrderDriver::model()->findByAttributes(array('id_order' => $order->id, 'id_driver' => $driver_id ));
			if(!empty($order_driver)) {
				if($order_driver->adopted == 0) {
					$order_driver->OrderAccept($order->id, $driver_id);
					// помечаем заказ как выполняющийся
					$order->ChangeExecuting(5, $driver_id, 0);
					
					$start_point = OrdersPoints::model()->findByPk($order->from);
					$parent_order = Orders::model()->findByPk($order->id_parent);
					$parent_order->CompletionOrder($start_point->adress, true); 
					
					if(!$order_driver->is_dispatcher_creator) {
						$customer->SendPush('Водитель принял изменение заказа', ['push_type' => 6, 'order_id' => $order->id, 'new_order_id' => $new_order->id], true);	
					}
					echo json_encode(array('result' => 'success', 'order_id' => $order->id));
				} else {
					echo json_encode(array('result' => 'failure', 'error' => 'Клиент отменил заказ.')); 
				}	
			} else {
				echo json_encode(array('result' => 'failure', 'error' => 'Клиент отменил заказ.'));
			}			
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.')); 	
		}
	}
	
	public function actionRefusal_update($id) {
		$driver_id = $this->is_authentificate();
		//$driver_id = 138;
		$order = Orders::model()->findByPk($id);
		
		if(!empty($order)) {
			$order_driver = OrderDriver::model()->findByAttributes(array('id_order' => $id, 'id_driver' => $driver_id));
			
			if(!empty($order_driver)) {
				if($order_driver->adopted != 2) {
					// помечаем запрос как отазаный
					$order_driver->DriverRefused($order->id, $driver_id);
				
					$id_parent_order = $order->id_parent;
					$order->delete();
				}
				$customer = UserStatus::GetUserById($order->id_customer);
				
				$customer->SendPush('Водитель отказался от изменения заказа', ['push_type' => 7, 'order_id' => $id_parent_order], true);	
				
				echo json_encode(array('result' => 'success', 'order_id' => $id_parent_order));	
			} else {
				echo json_encode(array('result' => 'failure', 'error' => 'Клиент отменил изменение заказа.'));
			}
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.'));
		}
	}
	
	public function actionComplete_order($id) {
		$driver_id = $this->is_authentificate();
		//$driver_id = 129;
		$order = Orders::model()->findByPk($id);
		
		if(!empty($order)) {
			if($order->execution_status == 2 && $order->id_status == 5 && !empty($order->id_driver)) {
				$request = json_decode(file_get_contents('php://input'));
				
				if(!empty($request->lat) && !empty($request->lng)) {
					$data = array('name' =>$this->Geocoder($request->lat,$request->lng), 'latitude' => $request->lat, 'longitude' => $request->lng);
					$this_adress = Addresses::model()->findByAttributes(array('name' => $data['name'], 'latitude' => $data['latitude'], 'longitude' => $data['longitude']));
					if(empty($this_adress)) {
						$this_adress = $this->AddressAdd($data);
					}
					//закрываем заказ
					$order->CompletionOrder($this_adress, false);
					//просчитываем конечную стоимость
					$order->SummaryPrice();
					$driver = UserStatus::GetUserById($order->id_driver);
					if(!empty($driver)) {
						$driver->ChangeStatus(null, 1);
					}
					$customer = UserStatus::GetUserById($order->id_customer);
					$customer->SendPush('Водитель завершил заказ', ['push_type' => 12, 'order_id' => $id, 'price' => $order->price, 'price_without_bonuses' => $order->price_without_bonuses, 'bonuses' => $order->bonuses], true);	
					
					echo json_encode(array('result' => 'success', 'price' => $order->price, 'bonuses' => $order->bonuses, 'price_without_bonuses' => $order->price_without_bonuses)); 
				} else {
					echo json_encode(array('result' => 'failure', 'error' => 'Не передается точка завершения.')); 
				}
			} else {
				echo json_encode(array('result' => 'failure', 'error' => 'Заказ находится не на стадии выполнения. Завершение невозможно.')); 
			}
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.')); 
		}
	}
	
	public function actionOrder_points($id)
	{		
		$driver_id = $this->is_authentificate();
		//$driver_id = 138;
		$order = Orders::model()->findByPk($id);
		$send_array = array();
		if(!empty($order)) {
			$order_points = OrdersPoints::model()->findAllByAttributes(array('id_order' => $order->id), array('order'=>'id ASC'));
			
			if(!empty($order_points)) {
				foreach($order_points as $p) {
					$send_array[] = $p->GetPointInfo();
				}
			}
			echo json_encode(array('result' => $send_array)); 
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.'));
		}			
	}
}