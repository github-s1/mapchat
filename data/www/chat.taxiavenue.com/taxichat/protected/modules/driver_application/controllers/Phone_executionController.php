<?php 

 class Orders_executionController extends MobileApplicationController
 {
    /*
       Принять заказ 
    */

    public function actionRequest_order($id)
	{		
		$driver_id = $this->is_authentificate();
		//$driver_id = 138;
		$order = Orders::model()->findByPk($id);
		if(!empty($order)) {
			if($order->execution_status == 1) {
				
				$order->SetToPhoneClient($driver_id, 0);

				$TurboSMS = new TurboSMS('taxichat', 'taxichat', 'TaxiChat');
                $TurboSMS->setMassage('Водитель взял ваш заказ')->setPhone($order->phone)->sendMassage();
				
				echo json_encode(array('result' => 'success'));
			} else {
				echo json_encode(array('result' => 'failure', 'error' => 'Клиент занят либо не в сети.'));
			}
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.')); 
		}
	}

    /*
        Водитель приехал
    */

    public function actionReached($id)
	{		
		$driver_id = $this->is_authentificate();
		//$driver_id = 205;
		$order = Orders::model()->findByPk($id);
		if(!empty($order)) {
			$order->ChangeStatus(4);
			$free_expectation = Settings::model()->findByAttributes(array('param' =>'free_expectation'));
			$free_simple = $free_expectation->value;
			if($order->is_preliminary) {
				$sub_sec = strtotime($order->order_date) - strtotime("now");
				if($sub_sec > 0) {
					$free_simple += round($sub_sec / 60);
				}
			}

			//Отправка смс пользователю о том что водитель приехал
			$TurboSMS = new TurboSMS('taxichat', 'taxichat', 'TaxiChat');
            $TurboSMS->setMassage('Машина подьехала')->setPhone($order->phone)->sendMassage();
			
			echo json_encode(array('result' => 'success', 'free_simple' => $free_simple)); 
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.')); 
		}
	}

	/*
        Клиент вышел
    */

    public function actionClient_came($id)
	{	
		$driver_id = $this->is_authentificate();
		//$driver_id = 205;
		$order = Orders::model()->findByPk($id);
		if(!empty($order)) {
			$order->ChangeStatus(5);

			//Отправка смс пользователю о том что водитель приехал
			$TurboSMS = new TurboSMS('taxichat', 'taxichat', 'TaxiChat');
            $TurboSMS->setMassage('Выполнение заказа началось')->setPhone($order->phone)->sendMassage();

			echo json_encode(array('result' => 'success')); 
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.')); 	
		}	
	}

	/*
       Клиент не вышел
	*/
    
    public function actionClient_not_came($id)
	{
		$driver_id = $this->is_authentificate();
		//$driver_id = 205;
		$order = Orders::model()->findByPk($id);
		if(!empty($order)) {
			$order->SetCancel(7);
			$driver = UserStatus::GetUserById($driver_id);
			$driver->ChangeStatus(null, 1);

			$TurboSMS = new TurboSMS('taxichat', 'taxichat', 'TaxiChat');
            $TurboSMS->setMassage('Вы не вышли на заказ. Заказ отменён.')->setPhone($order->phone)->sendMassage();

			echo json_encode(array('result' => 'success')); 
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Заказ не существует.')); 
		}	
	}
  

	/*
       Закрыть заказ
	*/

    public function actionComplete_order($id)
    {
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
					
					$order->CompletionOrder($this_adress, false);
					
					$order->SummaryPrice();
					$driver = UserStatus::GetUserById($order->id_driver);
					if(!empty($driver)) {
						$driver->ChangeStatus(null, 1);
					}

					$TurboSMS = new TurboSMS('taxichat', 'taxichat', 'TaxiChat');
                    $TurboSMS->setMassage('Водитель завершил заказ. Цена: '. $order->price . '. Бонусы: ' .$order->bonuses)->setPhone($order->phone)->sendMassage();

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
	






 }