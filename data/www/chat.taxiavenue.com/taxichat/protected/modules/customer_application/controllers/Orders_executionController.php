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
                'actions'=>array('get_driver_location', 'delay_accept', 'closing_order', 'update_order', 'time_expired', 'driver_route_accept', 'refusal_driver_route', 'set_reviews', 'get_start_point'),
                'users'=>array('*'),
            ),
            array('deny',
                'users'=>array('*'),
            ),
        );
    }
	
	public function actionGet_driver_location($id) {
		$customer_id = $this->is_authentificate();
		//$customer_id = 145;
		$order = Orders::model()->findByPk($id);
		if(!empty($order)) {
			//if($order->execution_status == 2 && ($order->id_status == 2 || $order->id_status == 3 || $order->id_status == 4 || $order->id_status == 5) && !empty($order->id_driver)) {
				$driver = UserStatus::GetUserById($order->id_driver);
				echo json_encode(array('result' => 'success', 'lat' => $driver->lat, 'lng' => $driver->lng)); 
			/* } else {
				echo json_encode(array('result' => 'failure', 'error' => 'Заказ находится не на той стадии выполнения.'));
			} */
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.'));
		}
	}
	
	public function actionGet_start_point($id) {
		$customer_id = $this->is_authentificate();
		//$customer_id = 145;
		$order = Orders::model()->findByPk($id);
		if(!empty($order)) {
			if($order->execution_status == 2 && ($order->id_status == 2 || $order->id_status == 3)) {
				echo json_encode(array('result' => 'success', 'lat' => $order->from_adress->adress->latitude, 'lng' => $order->from_adress->adress->longitude)); 
			} else {
				echo json_encode(array('result' => 'failure', 'error' => 'Заказ находится не на той стадии выполнения.')); 
			}
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.')); 
		}
	}
	
	public function actionDelay_accept($id) {
		$customer_id = $this->is_authentificate();
		//$customer_id = 145;
		$order = Orders::model()->findByPk($id);
		if(!empty($order)) {
			// подтверждаем опоздание
			$DelayData = OrdersDelay::ApplyDelay($id);
			
			if($DelayData['request']) {	
				if(!$DelayData['cancel']) {
					$time_delay = $DelayData['order_delay']->value;
					$driver = UserStatus::GetUserById($order->id_driver);
					$driver->SendPush('Клиент согласился ожидать дополнительные '.$time_delay.' минут.', ['push_type' => 11, 'delay' => $time_delay], true);
					
					echo json_encode(array('result' => 'success')); 
				} else {
					echo json_encode(array('result' => 'failure', 'error' => 'Запрос был отменен.'));
				}
			} else {
				echo json_encode(array('result' => 'failure', 'error' => 'Запрос был отменен.'));
			}
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.'));
		}	
	}
	
	public function actionClosing_order($id) {
		$customer_id = $this->is_authentificate();
		//$customer_id = 176;
		$order = Orders::model()->findByPk($id);
		if(!empty($order)) {
			if($order->id_customer == $customer_id) {
				if($order->execution_status == 2 && $order->id_status == 5 && !empty($order->id_driver)) {
					$request = json_decode(file_get_contents('php://input'));
					
					if(!empty($request->lat) && !empty($request->lng)) {
						$data = array('name' =>$this->Geocoder($request->lat,$request->lng), 'latitude' => $request->lat, 'longitude' => $request->lng);
						$this_adress = Addresses::model()->findByAttributes(array('name' => $data['name'], 'latitude' => $data['latitude'], 'longitude' => $data['longitude']));
						if(empty($this_adress)) {
							$this_adress = $this->AddressAdd($data);
						}	
						// закрываем заказ
						$order->CompletionOrder($this_adress, true);
						// пересчитываем полную стоимость заказа
						$order->SummaryPrice();
						
						$driver = UserStatus::GetUserById($order->id_driver);
						
						if(!empty($driver)) {
							$driver->ChangeStatus(null, 1);
							
							$driver->SendPush('Клиент завершил заказ', ['push_type' => 8, 'order_id' => $order->id, 'price' => $order->price, 'price_without_bonuses' => $order->price_without_bonuses, 'bonuses' => $order->bonuses], true);
						}
						echo json_encode(array('result' => 'success', 'price' => $order->price, 'bonuses' => $order->bonuses, 'price_without_bonuses' => $order->price_without_bonuses));
					} else {
						echo json_encode(array('result' => 'failure', 'error' => 'Не передается точка завершения.')); 
					}
				} else {
					echo json_encode(array('result' => 'failure', 'error' => 'Заказ находится не на стадии выполнения. Завершение невозможно.')); 
				}
			} else {
				echo json_encode(array('result' => 'failure', 'error' => 'Вы пытаетесь отменить чужой заказ.')); 
			}
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.')); 
		}	
	}

	
	public function actionUpdate_order($id) {
		
		$customer_id = $this->is_authentificate();
		//$customer_id = 145;
		$order = Orders::model()->findByPk($id);
		$request = json_decode(file_get_contents('php://input'), true);
		//echo json_encode(array('result' => $request)); exit;
		if(!empty($order)) {
			if($order->id_customer == $customer_id) {
				if($order->execution_status == 2 && $order->id_status == 5 && !empty($order->id_driver)) {
					$new_order = new Orders;
					$new_order->CopyParentOrder($order);
					
					if(isset($request['Orders']))	{
						$new_order->attributes = $request['Orders'];
						if($new_order->save()) {
							
							if(isset($request['points']) && count($request['points'])>0) {
								foreach($request['points'] as $kda => $gdata){	
									if($kda == 0) {
										$gdata->name = $this->Geocoder($gdata->latitude,$gdata->longitude);
									}
									$this_adress = Addresses::model()->findByAttributes(array('name' => $gdata->name, 'latitude' => $gdata->latitude, 'longitude' => $gdata->longitude));	
									if(empty($this_adress)) {
										$this_adress = $this->AddressAdd($gdata);
									}
									$this_point = $this->PointsAdd($new_order->id, $this_adress->id, $gdata->entrance);
								}
							}
							
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
							$where = OrdersPoints::model()->findByAttributes(array('id_order' => $new_order->id), array('order'=>'id DESC'), array('limit'=>1));
							if(!empty($where) && !empty($from)) {
								$new_order->where = $where->id;
							}
							if(!$new_order->save()) {
								$errors = MobileApplicationController::GetErrors($new_order);
								echo json_encode(array('result' => 'failure', 'error' => $errors)); exit;
							}
							// добавляет запись о запросе на выполнение заказа
							$new_order->SandRequestPerform($new_order->id_driver, 0);
							$driver = UserStatus::GetUserById($new_order->id_driver);
							
							$driver->SendPush('Клиент изменил заказ', ['push_type' => 7, 'order_id' => $order->id, 'new_order_id' => $new_order->id, 'time' => time()], true);
							
							echo json_encode(array('result' => 'success', 'id' => $new_order->id));
						} else {
							$errors = MobileApplicationController::GetErrors($new_order);
							echo json_encode(array('result' => 'failure', 'error' => $errors)); 
						}
					
					} else {
						echo json_encode(array('result' => 'failure', 'error' => 'Не передаются данные заказа.')); 
					}
				} else {
					echo json_encode(array('result' => 'failure', 'error' => 'Заказ находится не на стадии выполнения. Завершение невозможно.')); 
				}
			} else {
				echo json_encode(array('result' => 'failure', 'error' => 'Вы пытаетесь изменить чужой заказ.')); 
			}
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.')); 
		}	
	}
	
	public function actionTime_expired($id) {
		$customer_id = $this->is_authentificate();
		//$customer_id = 145;
		$order = Orders::model()->findByPk($id);
		if(!empty($order)) {
			// получаем текущую информацию по запросу на выполнение
			$order_driver = OrderDriver::GetDriverRequest($order->id, $id, 0);
			if($order_driver->adopted == 1) {
				echo json_encode(array('result' => 'success')); 
			} else {
				if($order_driver->adopted != 2) {
					// помечаем запрос как отклоненный
					$order_driver->DriverRefused($order->id, $id);
					/*
					$order->execution_status = 4;
					$order->id_status = 9;
					$order->save();
					*/
					$order->delete();
					
					echo json_encode(array('result' => 'failure', 'error' => 'Водитель не дал ответ.')); 
				} else {
					echo json_encode(array('result' => 'failure', 'error' => 'Водитель отказался.')); 
				}			
			}
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.')); 
		}	
	}
	
	public function actionDriver_route_accept($id) 
	{
		$customer_id = $this->is_authentificate();
		
		$order = Orders::model()->findByPk($id);
		if(!empty($order)) {
			$driver = UserStatus::GetUserById($order->id_driver);
			if(!empty($driver)) {	
				$order_driver = OrderDriver::model()->findByAttributes(array('id_order' => $order->id, 'id_driver' => $order->id_driver));
				
				if(!empty($order_driver)) {
					if($order_driver->adopted == 0) {
						// помечаем запрос как принятый
						$order_driver->OrderAccept($order->id, $order->id_driver);
						// помечаем заказ как выполняющийся
						$order->ChangeExecuting(5, $order->id_driver, 0 );
						
						$start_point = OrdersPoints::model()->findByPk($order->from);
						$parent_order = Orders::model()->findByPk($order->id_parent);
						// закрываем родительский заказ
						$parent_order->CompletionOrder($start_point->adress, true);
					
						$driver->SendPush('Клиент дал согласие на свободный маршрут', ['push_type' => 9, 'order_id' => $order->id], true);
						echo json_encode(array('result' => 'success', 'order_id' => $order->id));
					} else {
						echo json_encode(array('result' => 'failure', 'error' => 'Заказ был отменен.'));
					}	
				} else {
					echo json_encode(array('result' => 'failure', 'error' => 'Водитель отменил запрос.'));
				}
					
			} else {
				echo json_encode(array('result' => 'failure', 'error' => 'Указаный водитель не существует.'));
			}
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.'));
		}
		
	}
	
	public function actionRefusal_driver_route($id) {
		$customer_id = $this->is_authentificate();
		
		$order = Orders::model()->findByPk($id);
		if(!empty($order)) {
			if($order->id_customer == $customer_id) {
				$driver = UserStatus::GetUserById($order->id_driver);
				if(!empty($driver)) {	
					$order_driver = OrderDriver::model()->findByAttributes(array('id_order' => $order->id, 'id_driver' => $order->id_driver));
					if(!empty($order_driver)) {
						// помечаем запрос как отклоненный
						$order_driver->ClientRefused($order->id, $order->id_driver);
	
						$id_parent_order = $order->id_parent;
							
						$order->delete();
						
						$driver->SendPush('Клиент отказался от свободного маршрута', ['push_type' => 10, 'order_id' => $id_parent_order], true);
						
						echo json_encode(array('result' => 'success', 'order_id' => $id_parent_order));
					} else {
						echo json_encode(array('result' => 'failure', 'error' => 'Водитель отменил запрос.'));
					}			
				} else {
					echo json_encode(array('result' => 'failure', 'error' => 'Указаный водитель не существует.'));
				}
			} else {
				echo json_encode(array('result' => 'failure', 'error' => 'Заказ пренадлежит не вам.')); 	
			}	
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.'));
		}
	}
	
	public function actionSet_reviews($id) {
		$customer_id = $this->is_authentificate();
		//$customer_id = 147;
		
		$order = Orders::model()->findByPk($id);
		if(!empty($order)) {
			if($order->id_customer == $customer_id) {
				if($order->execution_status == 3) {
					$request = json_decode(file_get_contents('php://input'));
					if(!empty($request->evaluation) && !empty($request->text)) {
		
						$evaluation = Evaluations::model()->findByPk($request->evaluation);
						if(empty($evaluation)) {
							echo json_encode(array('result' => 'failure', 'error' => 'Такой оценки не существует.')); exit;
						}
						$reviews = new DriverReviews;
						$reviews->id_driver = $order->id_driver;
						$reviews->id_customer = $order->id_customer;
						$reviews->id_evaluation = $request->evaluation;
						$reviews->text = $request->text;
						$reviews->date_review = date('Y-m-d H:i:s', strtotime("now"));
						$reviews->id_order = $id;
						
						$driver = Users::model()->findByPk($order->id_driver);
						
						$driver->rating += $evaluation->value; 
						
						$reviews->rating = $driver->rating;
						
						if($reviews->save()) {
							if($driver->save()) {
								//начилсяем клиенту бонусы
								$order->accrualBonusesOrder();
								//------------------------------------
								echo json_encode(array('result' => 'success'));	
							} else {
								$errors = MobileApplicationController::GetErrors($driver);
								echo json_encode(array('result' => 'failure', 'error' => $errors));
							}
						} else {
							$errors = MobileApplicationController::GetErrors($reviews);
							echo json_encode(array('result' => 'failure', 'error' => $errors));
						}
					} else {
						$order_bonuses = BonusesHistory::model()->findByAttributes(array('id_user' => $order->id_customer, 'id_order' =>$id, 'id_type' => 1), array('order'=>'id DESC'));
						if(!empty($order_bonuses)) {
							$received_bonuses = $order_bonuses->value;
						} else {
							$received_bonuses = 0;
						} 
						$evaluations = CHtml::listData(Evaluations::model()->findAll(array('order'=>'id ASC')), 'id', 'name');
						echo json_encode(array('response' => array('received_bonuses' => $received_bonuses, 'evaluations' => $evaluations)));
					}
				} else {
					echo json_encode(array('result' => 'failure', 'error' => 'Оценивать водителя можно только после закрытия заказа.')); 	
				}
			} else {
				echo json_encode(array('result' => 'failure', 'error' => 'Заказ пренадлежит не вам.')); 	
			}
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.')); 	
		}
	}
}