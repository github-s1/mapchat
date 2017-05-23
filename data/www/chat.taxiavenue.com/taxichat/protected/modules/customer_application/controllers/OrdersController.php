<?php

class OrdersController extends MobileApplicationController
{	
	//private $glob_id_price_class;
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
                'actions'=>array('coming_drivers', 'driver_choice', 'driver_choice_cancel', 'time_expired', 'order_cancel', 'create', 'update', 'new_price', 'active_orders', 'address_search', 'autoselection_drivers', 'driver_view', 'driver_accept', 'refusal_driver', 'driver_reviews', 'time_update_expired', 'autoselection', 'drivers_subscribe'),
                'users'=>array('*'),
            ),
            array('deny',
                'users'=>array('*'),
            ),
        );
    }
	
	public function actionCreate()
	{	
		$this->_edit(0);
	}

	public function actionUpdate($id)
	{	
		$this->_edit($id);
	}
	
	public function actionNew_price()
	{	
		$customer_id = $this->is_authentificate();
		//$customer_id = 145;	
		$distance = 0;
		$price = 0;
		$price_distance = 0;
		$price_without_class = 0;
		
		$request = json_decode(file_get_contents('php://input'), true);
		//пересчитываем стоимость заказа
		$price = $this->recalculationPrice($request, $price, $distance, $price_distance, $price_without_class, $customer_id);
		$price = round($price, 2);
		$send_array = array('price'=>$price, 'distance'=>$distance, 'price_distance'=>$price_distance, 'price_without_class'=>$price_without_class);
		
		echo json_encode(array('response' => $send_array));
	}

	public function actionActive_orders()
	{
		$customer_id = $this->is_authentificate();
		$orders = Orders::model()->findAllByAttributes(array('execution_status' => 1, 'id_status' => 1, 'id_customer' => $customer_id), array('order'=>'id DESC'));
		$orders_array  = array();
		if(!empty($orders)) {
			foreach($orders as $ord) {
				$orders_array[] = $ord->GetOrderInfo();	
			}
		} 
		echo json_encode(array('response' => $orders_array));
		
	}
	
	private function _edit($id = 0)
	{	
		$customer_id = $this->is_authentificate();
		
		if($id == 0) {
			$order = new Orders;
			$order_points = array();
		} else {
			$order = Orders::model()->findByPk($id);
			$order_points = OrdersPoints::model()->findAllByAttributes(array('id_order' => $order->id), array('order'=>'id ASC'));
		}
		if(!empty($order->id)) {
			if($order->id_customer != $customer_id) {
				echo json_encode(array('result' => 'failure', 'error' => 'Вы пытаетесь редактировать чужой заказ.')); exit;
			}
			/*
			if($order->execution_status > 2 || $order->id_status > 4){
				echo json_encode(array('result' => 'failure', 'error' => 'Заказ находится не на той стадии выполнения.')); exit;
			}
			*/
		}
		$request = json_decode(file_get_contents('php://input'), true);
		if(isset($request['Orders']))	{
			$order->attributes = $request['Orders'];
			if(isset($request['Orders']['is_pay_bonuses']) && $request['Orders']['is_pay_bonuses'] == 1) {
				$order->is_pay_bonuses = 1;
			}
			$order->id_customer = $customer_id;
			$order->id_creator = $customer_id;
			$order->change_date = date('Y-m-d H:i:s', strtotime("now"));
			//	echo json_encode(array('result' => $order->attributes)); exit;
			if($order->save()) {
				OrdersPoints::model()->deleteAll('id_order = ?' , array($order->id));
				if(isset($request['point_add']) && count($request['point_add'])>0) {
					foreach($request['point_add'] as $kda => $gdata){
						
						$this_adress = Addresses::model()->findByAttributes(array('name' => $gdata['name'], 'latitude' => $gdata['latitude'], 'longitude' => $gdata['longitude']));
						
						if(empty($this_adress)) {
							$this_adress = new Addresses;
							$this_adress->attributes = $gdata;
							$this_adress->name = htmlspecialchars($this_adress->name, ENT_QUOTES);
							$this_adress->save();
						}
						$this_point = new OrdersPoints;
						$this_point->id_order = $order->id;
						$this_point->id_adress = $this_adress->id;
						$this_point->entrance = $gdata['entrance'];
						$this_point->save();
					}
				}
				/*
				if(isset($request->order_points) && count($request->order_points)>0){
					OrdersPoints::model()->deleteAll('id_order = ?' , array($order->id));
					foreach((array)$request->order_points as $kda => $gdata){
						
						$this_adress = Addresses::model()->findByAttributes(array('name' => $gdata->name, 'latitude' => $gdata->latitude, 'longitude' => $gdata->longitude));
						
						if(empty($this_adress)) {
							$this_adress = new Addresses;
							$this_adress->attributes = (array)$gdata;
							$this_adress->name = htmlspecialchars($this_adress->name, ENT_QUOTES);
							$this_adress->save();
						}
						$this_point = new OrdersPoints;
						$this_point->id_order = $order->id;
						$this_point->id_adress = $this_adress->id;
						$this_point->entrance = $gdata->entrance;
						$this_point->save();
					}
				}
				*/
				if($id != 0) {
					OrderService::model()->deleteAll('id_order = ?' , array($order->id));
				}
				
				if(isset($request['OrderService']['id']) && !empty($request['OrderService']['id'])) {
					foreach($request['OrderService']['id'] as $service_id) {
						$service_or = new OrderService;
						$service_or->id_order = $order->id;
						$service_or->id_service = $service_id;
						$service_or->save();
					}	
				}
				
				$from = OrdersPoints::model()->findByAttributes(array('id_order' => $order->id), array('order'=>'id ASC'), array('limit'=>1));
				if(!empty($from)) {
					$order->from = $from->id;
				}
				$where = OrdersPoints::model()->findByAttributes(array('id_order' => $order->id), array('order'=>'id DESC'), array('limit'=>1));
				if(!empty($where) && !empty($from)) {
					$order->where = $where->id;
				}
				if($order->save()) {
					if($order->execution_status == 2 && !empty($order->id_driver)){
						$driver = UserStatus::GetUserById($order->id_driver);
						if(!empty($driver)) {
							$driver->SendPush('Клиент изменил заказ.', ['push_type' => 12, 'order_id' => $order->id], true);
						}
					}
				
					echo json_encode(array('result' => 'success', 'id' => $order->id));
					//отсылаем водителям поблизости пушь
					if($order->execution_status == 1) {
						$this->backgroundPost('http://'.Yii::app()->params['siteIP'].'/customer_application/orders/drivers_subscribe/id/'.$order->id);
					}	
				} else {
					$errors = MobileApplicationController::GetErrors($order);
					echo json_encode(array('result' => 'failure', 'error' => $errors));	
				}
				
			} else {
				$errors = MobileApplicationController::GetErrors($order);
				echo json_encode(array('result' => 'failure', 'error' => $errors));
			}	 
		} else {
			$price_class = PriceClass::model()->findAll(array('order'=>'id ASC'));
			$price_class_name = array();
			$price_class_index = array();
			if(!empty($price_class)) {
				foreach($price_class as $class) {
					$price_class_name[] = $class->name;
					$price_class_index[] = $class->id;
				}
			}
			$services = Services::model()->findAllByAttributes(array('is_driver' => 1), array('order'=>'id ASC'));
			$serv_name_all = array();
			$serv_index_all = array();
			if(!empty($services)) {
				foreach($services as $serv) {
					$serv_name_all[] = $serv->name;
					$serv_index_all[] = $serv->id;
				}
			}
			
			$services_ord = OrderService::model()->findAllByAttributes(array('id_order' => $order->id), array('order'=>'id ASC'));
			$services_order = array(); 
			if(!empty($services_ord)) {
				foreach($services_ord as $serv) {
					$services_order[] = $serv->id_service;
				}
			}
	
			$customer = Users::model()->findByPk($customer_id);
			if($id == 0) {				
				$send_array = array('Services_name'=>$serv_name_all, 'Services_index'=>$serv_index_all, 'PriceClass_name'=>$price_class_name, 'PriceClass_index'=>$price_class_index, 'bonuses'=>$customer->bonuses);
				
				$customer_status = UserStatus::model()->findByAttributes(array('id_user' => $customer_id));
				if($customer_status->is_activate) {
					$activate = 1;
				} else {
					$activate = 0;
				}
				$send_array['is_activate'] = $activate;
				
			} else {
				$points = array();
				if(!empty($order_points)) {
					foreach($order_points as $p) {
						$points[] = $p->GetPointInfo();
					}
				}
				$send_array = array('Order'=>$order->getAttributes(), 'OrdersPoints'=>$points, 'Services_name'=>$serv_name_all, 'Services_index'=>$serv_index_all, 'PriceClass_name'=>$price_class_name, 'PriceClass_index'=>$price_class_index, 'services_order' =>$services_order, 'bonuses'=>$customer->bonuses);
			}
			$through = Settings::model()->findByAttributes(array('param' =>'value_through'));
			$send_array['through'] = $through->value;
			
			echo json_encode(array('response' => $send_array));
		}	
	}
	
	//рассылает водителям пуши, сообщает о необходимости обновить своб. эфир 
	public function actionDrivers_subscribe($id) {
		$order = Orders::model()->findByPk($id);
		if(!empty($order)) {
			$display_drivers_for = Settings::model()->findByAttributes(array('param' =>'display_drivers_for'));
			if($order->order_date < date('Y-m-d H:i:s', strtotime("now") + ($display_drivers_for->value * 60))) {
				$drivers = $order->getDriverNearby();
				if(!empty($drivers)) {
					foreach($drivers as $dr) {
						if($order->id_driver != $dr->id) {
							$dr->SendPush('Свободный эфир обновился.', ['push_type' => 15], false);
						}	
					}
					
				}
			} 	
		}		
	}
	
	public function actionTime_update_expired($id) {
		$customer_id = $this->is_authentificate();
		//$customer_id = 176;
		
		$order = Orders::model()->findByPk($id);
		if(!empty($order)) {
			if($order->id_customer != $customer_id) {
				echo json_encode(array('result' => 'failure', 'error' => 'Вы пытаетесь редактировать чужой заказ.')); exit;
			}
			if($order->execution_status > 2 || $order->id_status > 4){
				echo json_encode(array('result' => 'failure', 'error' => 'Заказ находится не на той стадии выполнения.')); exit;
			}
			
			if($order->execution_status == 2) {
				echo json_encode(array('result' => 'success'));
			} else {
				echo json_encode(array('result' => 'failure', 'error' => 'Водитель отказался.'));
			}
		}  else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.')); 
		}
	}
	 
	public function actionOrder_cancel($id) {
		$customer_id = $this->is_authentificate();
		$order = Orders::model()->findByPk($id);
		if(!empty($order)) {
			if($order->execution_status == 2 && !empty($order->id_driver)) {
				if($order->id_status == 2 || $order->id_status == 3 || $order->id_status == 4) {
					$driver = UserStatus::GetUserById($order->id_driver);
					if(!empty($driver)) {
						$driver->ChangeStatus(null, 1);
						$driver->SendPush('Клиент отменил заказ.', ['push_type' => 6, 'order_id' => $order->id], true);
					}
				}
			}
			
			$order->SetCancel(4);
			
			echo json_encode(array('result' => 'success')); 
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.')); 
		}	
	}
	
	public function actionComing_drivers($id) {
		$customer_id = $this->is_authentificate();
		$order = Orders::model()->findByPk($id);
		$drivers_array  = array();
		if(!empty($order)) {
			$display_drivers_for = Settings::model()->findByAttributes(array('param' =>'display_drivers_for'));
			if(($order->is_preliminary && ((strtotime("now")) > (strtotime($order->order_date) - ($display_drivers_for->value * 60)))) || !$order->is_preliminary )  {
				
				$drivers = $order->getDriverNearby();
				
				$order_drivers =  CHtml::listData(OrderDriver::model()->findAllByAttributes(array('id_order' => $order->id, 'adopted' => array(2,3)), array('order' => 'id ASC')), 'id', 'id_driver');
				
				if(!empty($drivers)) {
					foreach($drivers as $i=>$dr) {
						$drivers_array[$i] = Drivers::GetDriverInfoArray($dr, $order_drivers);
					
						$drivers_array[$i]['price'] = $order->PriceWithClass($dr->user);
					}
				}
				
				$customer = Users::model()->findByPk($customer_id);
				echo json_encode(array('response' => $drivers_array, 'is_pay_bonuses' => $order->is_pay_bonuses, 'bonuses' => $customer->bonuses)); 
			} else {
				echo json_encode(array('result' => 'failure', 'error' => 'Поиск водителей будет доступен за пол часа до времени выполнения заказа.')); 
			}
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.'));
		}
		
		
	}
	
	public function actionDriver_view($id) {
		$customer_id = $this->is_authentificate();
		//$customer_id = 147;
		$request = json_decode(file_get_contents('php://input'));
		
		
		if(!empty($request->order_id)) {
			$order = Orders::model()->findByPk($request->order_id);
			if(!empty($order)) {
				$driver = Users::model()->findByPk($id);
				
				if(!empty($driver)) {
					$driver_resp = $driver->GetDriverInfo();
					//цена заказа с учетом ценового класса водителя
					$driver_resp['price'] = $order->PriceWithClass($driver);
					
					$driver_resp['distance'] = $order->distance;
					echo json_encode(array('response' => $driver_resp)); exit;
				} else {
					echo json_encode(array('result' => 'failure', 'error' => 'Указаный водитель не существует.')); 
				}
			} else {
				echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.')); 
			}	
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Не передается информация о заказе.')); 
		}		
	}
	
	public function actionDriver_reviews($id) {
		//$customer_id = $this->is_authentificate();
		
		$driver_reviews = Drivers::GetDriverReviews($id, 0, true);
		
		$ReviewsData = DriverReviews::GetReviewsInfo($driver_reviews);
		
		echo json_encode(array('response' => $ReviewsData['driver_reviews']));
	}
	
	public function actionDriver_choice($id) {
		$customer_id = $this->is_authentificate();
		//$customer_id = 145;
		$request = json_decode(file_get_contents('php://input'));
		if(!empty($request->order_id)) {
			$order = Orders::model()->findByPk($request->order_id);
			if(!empty($order)) {
				$driver = UserStatus::GetUserById($id);
				if(!empty($driver)) {
					if($driver->id_status != 1 && $driver->user->balance > 0) {
						echo json_encode(array('result' => 'failure', 'error' => 'Водитель занят либо не в сети.'));  exit;
					}
					if($order->execution_status != 1) {
						echo json_encode(array('result' => 'failure', 'error' => 'Выбор водителя доступен только на этапе взятия заказа.'));  exit;
					}
					$order->SandRequestPerform($id, 0);
					$driver->SendPush('Вам поступил принудительный заказ.', ['push_type' => 1, 'order_id' => $order->id, 'time' => time()], true);
					
					echo json_encode(array('result' => 'success')); 	
				} else {
					echo json_encode(array('result' => 'failure', 'error' => 'Указаный водитель не существует.'));
				}
			} else {
				echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.')); 	
			}
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Не передается информация о заказе.'));
		}	
	}
	
	public function actionDriver_choice_cancel($id) {
		$customer_id = $this->is_authentificate();
		//$customer_id = 145;
		$request = json_decode(file_get_contents('php://input'));
		if(!empty($request->order_id)) {
			$order = Orders::model()->findByPk($request->order_id);
			if(!empty($order)) {
				// получаем текущую информацию по запросу на выполнение
				$order_driver = OrderDriver::GetDriverRequest($order->id, $id,  0);
				// помечаем запрос как отказаный
				$order_driver->ClientRefused($order->id, $id);
				$driver = UserStatus::GetUserById($id);
				if(!empty($driver)) {
					$driver->SendPush('Клиент отменил запрос на выполнение заказа.', ['push_type' => 2, 'order_id' => $order->id], true);
				}
				echo json_encode(array('result' => 'success'));
			} else {
				echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.')); 
			}
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Не передается информация о заказе.')); 
		}	
				
	}
	
	public function actionTime_expired($id) {
		$customer_id = $this->is_authentificate();
		//$customer_id = 145;
		$request = json_decode(file_get_contents('php://input'));
		if(!empty($request->order_id)) {
			$order = Orders::model()->findByPk($request->order_id);
			if(!empty($order)) {
				// получаем текущую информацию по запросу на выполнение
				$order_driver = OrderDriver::GetDriverRequest($order->id, $id,  0);
				if($order_driver->adopted == 1) {
					echo json_encode(array('result' => 'success')); 
				} else {
					if($order_driver->adopted != 2) {
						// помечаем запрос как отказаный
						$order_driver->DriverRefused($order->id, $id);
						$this->FineForFailure($id, $order->is_preliminary);
						
						echo json_encode(array('result' => 'failure', 'error' => 'Водитель не дал ответ.')); 
					} else {
						echo json_encode(array('result' => 'failure', 'error' => 'Водитель отказался.')); 
					}			
				}
			} else {
				echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.')); 
			}
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Не передается информация о заказе.')); 
		}	
	}
	
	public function actionAddress_search() {
	
		$customer_id = $this->is_authentificate();
		//$customer_id = 145;
		$request = json_decode(file_get_contents('php://input'));
		
		if(!empty($request->adress)) {
			$google_flag = 1;
			$criteria = new CDbCriteria(array(
				'condition' => 'LOWER(name) LIKE :adress OR LOWER(popular_name) LIKE :adress',      // DON'T do it this way!
				'params'    => array(':adress'=>'%'.mb_strtolower($request->adress, 'UTF-8').'%')
			));
            //$criteria->addCondition('name LIKE :adress OR popular_name LIKE :adress', array('params'=>array(':adress'=>'%'.$request->adress.'%')));
			
			$criteria->order = 'id ASC';	
			$addresses = Addresses::model()->findAll($criteria);
			$search_addresses = array();
			// если результатов из базы нет делаем запрос на гугл
			if(!empty($addresses)) {
				$google_flag = 0;
				foreach($addresses as $adr) {
					$search_addresses[] = $adr->getAttributes();
				}
			} else {
				//$key = 'AIzaSyCXzbwa6_RYvuvlaypWFagPoH-A82uVOyY';
				$components = "components=country:ua";
				$lang = 'ru';
				$region = 'ua';
				$street =  str_replace(' ', '+', trim($request->adress));

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "https://maps.googleapis.com/maps/api/geocode/json?address=$street&$components&region=$region&language=$lang");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

				$results = json_decode(curl_exec($ch));
				curl_close($ch);
				
				if(!empty($results->results)) {
					foreach($results->results as $idx => $adr) {
						$search_addresses[$idx]['name'] = $adr->formatted_address;
						$search_addresses[$idx]['short_name'] = (isset($adr->address_components[1])?$adr->address_components[1]->long_name.', ':'').$adr->address_components[0]->long_name;
						$search_addresses[$idx]['latitude'] = $adr->geometry->location->lat;
						$search_addresses[$idx]['longitude'] = $adr->geometry->location->lng;
					}
				}
			}
			echo json_encode(array('response' => $search_addresses, 'is_google' => $google_flag));
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Поисковая строка не была передана.')); 
		}
	} 
	
	
	public function actionDriver_accept($id) 
	{
		$customer_id = $this->is_authentificate();
		
		$request = json_decode(file_get_contents('php://input'));
		if(!empty($request->order_id)) {
			$order = Orders::model()->findByPk($request->order_id);
			if(!empty($order)) {
				$driver = UserStatus::GetUserById($id);
				if(!empty($driver)) {
					if($driver->id_status == 1 && $order->execution_status == 1) {
						$order_driver = OrderDriver::model()->findByAttributes(array('id_order' => $order->id, 'id_driver' => $id));
						
						if(!empty($order_driver)) {
							if($order_driver->adopted == 0) {
								//помечаем запрос на согласие
								$order_driver->OrderAccept($order->id, $id);
								//помечаем заказ как выполняющийся
								$order->ChangeExecuting(2, $id, 0);
								//меняем водителю статус на занят
								$driver->ChangeStatus(null, 2);
								$driver->SendPush('Клиент дал согласие на выполнение заказа.', ['push_type' => 4, 'order_id' => $order->id, 'time' => time()], true);
								
								echo json_encode(array('result' => 'success'));
								//рассылаем всем водителям поблизости пушь
								$this->backgroundPost('http://'.Yii::app()->params['siteIP'].'/customer_application/orders/drivers_subscribe/id/'.$order->id);
							} else {
								echo json_encode(array('result' => 'failure', 'error' => 'Заказ был отменен.')); 
							}	
						} else {
							echo json_encode(array('result' => 'failure', 'error' => 'Водитель отменил запрос.')); 
						}
					} else {
						echo json_encode(array('result' => 'failure', 'error' => 'Водитель занят либо не в сети.')); 	
					}	
				} else {
					echo json_encode(array('result' => 'failure', 'error' => 'Указаный водитель не существует.')); 	
				}
			} else {
				echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.')); 	
			}
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Не передается информация о заказе.')); 	
		}
	}
	
	public function actionRefusal_driver($id) {
		$customer_id = $this->is_authentificate();
		
		$request = json_decode(file_get_contents('php://input'));
		if(!empty($request->order_id)) {
			$order = Orders::model()->findByPk($request->order_id);
			if(!empty($order)) {
				$driver = UserStatus::GetUserById($id);
				if(!empty($driver)) {
					if($driver->id_status == 1 && $order->execution_status == 1) {
						$order_driver = OrderDriver::model()->findByAttributes(array('id_order' => $order->id, 'id_driver' => $id));
						if(!empty($order_driver)) {
							if($order_driver->adopted != 2 && $order_driver->adopted != 3) {
								$order_driver->ClientRefused($order->id, $id);
							} else {
								echo json_encode(array('result' => 'failure', 'error' => 'Водитель отменил запрос.')); exit;
							}	
							//if($driver->id_status == 1 ) {
								$driver->SendPush('Клиент отказался от выполнения заказа.', ['push_type' => 5, 'order_id' => $order->id], true);
							//}
							echo json_encode(array('result' => 'success'));	
						} else {
							echo json_encode(array('result' => 'failure', 'error' => 'Водитель отменил запрос.'));
						}		
					} else {
						echo json_encode(array('result' => 'failure', 'error' => 'Водитель занят либо не в сети.'));
					}	
				} else {
					echo json_encode(array('result' => 'failure', 'error' => 'Указаный водитель не существует.')); 	
				}
			} else {
				echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.'));	
			}
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Не передается информация о заказе.')); 
		}
	}
	
	public function actionAutoselection_drivers($id) 
	{	
		
		$customer_id = $this->is_authentificate();
		$order = Orders::model()->findByPk($id);
		if(!empty($order)) {
			if($order->id_customer == $customer_id) {
				if((strtotime($order->order_date) - 1800) < strtotime("now")  )  {
					echo json_encode(array('result' => 'success'));
					//рассылаем всем водителям поблизости принудительный заказ
					$this->backgroundPost('http://'.Yii::app()->params['siteIP'].'/customer_application/orders/autoselection/id/'.$id);
					
				} else {
					echo json_encode(array('result' => 'failure', 'error' => 'Автопоиск водителей будет доступен за пол часа до времени выполнения заказа.')); 
				}
			} else {
				echo json_encode(array('result' => 'failure', 'error' => 'Автопоиск водителей может производить только владелец заказа.')); 
			}	
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.')); 
		}
	}
	
	public function actionAutoselection($id) 
	{		
		$order = Orders::model()->findByPk($id);
		$drivers = $order->getDriverNearby();
		
		if(!empty($drivers)) {
			foreach($drivers as $dr) {
				// отправляем запрос на выполнение заказа
				$order->SandRequestPerform($dr->user->id, 1);
				
				$dr->SendPush('Вам поступил принудительный заказ.', ['push_type' => 1, 'order_id' => $order->id], true);
				// ждем 3 минуты и запрашиваем ответ
				sleep(180);
				
				$order_driver = OrderDriver::model()->findByAttributes(array('id_order' => $order->id, 'id_driver' => $dr->user->id));
				if(!empty($order_driver)) {
					if($order_driver->adopted == 1) {
						/*
						$customer = UserStatus::model()->findByAttributes(array('id_user' => $customer_id));
						$customer->SendPush('Для вашего заказа был найден водитель.', ['push_type' => 16, 'order_id' => $order->id, 'driver_id' => $order_driver->id_driver], true);
						
						echo json_encode(array('result' => 'success'));
						*/
						exit;
					} else {
						// помечаем запрос как отказаный
						$order_driver->DriverRefused($order->id, $order_driver->id_driver);
						// списываем штраф с водителя
						$this->FineForFailure($dr->user->id, $order->is_preliminary);
					}
				}
			}
			
		}
		$customer = UserStatus::GetUserById($order->id_customer);
		$customer->SendPush('Водитель не был найден.', ['push_type' => 16, 'order_id' => $order->id], true);
	}
}