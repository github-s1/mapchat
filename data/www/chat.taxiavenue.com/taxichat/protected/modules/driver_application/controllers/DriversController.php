<?php

class DriversController extends MobileApplicationController
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
                'actions'=>array('profile', 'edit_profile', 'edit_photos', 'edit_password', 'service_driver', 'change_status', 'balance', 'movement_balance', 'statistics_reviews', 'all_reviews', 'password_recovery', 'password_recovery_confirmation','get_balance', 'account_activate'),
                'users'=>array('*'),
            ),
            array('deny',
                'users'=>array('*'),
            ),
        );
    }

	

	public function actionEdit_password()
    {
		$driver_id = $this->is_authentificate();
		$driver = Users::model()->findByPk($driver_id);
		
		$request = json_decode(file_get_contents('php://input'));
		if(!empty($request->oldPassword)) {
			//проверяем верный ли старый пароль
			if($driver->password !== crypt($request->oldPassword, $driver->password)) {
				echo json_encode(array('result' => 'failure', 'error' => 'Старый пароль не верный.'));
			} else {
				if(!empty($request->newPassword)) {
					//меняем пароль
					$driver->ChangePassword($request->newPassword);	
					//если водитель менял данные, то меняем пароль и там					
					$dr_temp = DriversTemp::GetDriverTempByParentId($driver_id);
					if(!empty($dr_temp)) {
						$dr_temp->ChangePassword($driver->password);
					}
					echo json_encode(array('result' => 'success'));
				} else {
					echo json_encode(array('result' => 'failure', 'error' => 'Новый пароль не был отправлен.'));
				}	
			}		
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Старый пароль не был отправлен.')); 
		}
	}
	
	public function actionEdit_photos()
    {
		$driver_id = $this->is_authentificate();
		//получаем данные водителя 
		$DriverData = Drivers::GetDriverData($driver_id, true);
		$driver = $DriverData['driver'];
		$car = $DriverData['car'];
		$driver_status = $DriverData['user_status'];
		
		if(!empty($_FILES)) {
			//приводим файлы к нужному формату
			$this->filesRegProcessing('UsersTemp', 'CarsTemp');
			
			//получаем данные водителя из буфера либо создаем их
			$TempData = DriversTemp::GetDriverTempDataPhotos($driver, $car);
			//echo json_encode(array('response' => $TempData)); exit;	
			$driver_temp = $TempData['driver_temp'];
			$cars_temp = $TempData['cars_temp'];
			
			if($driver_temp->validate()) {
				if($cars_temp->save()) {
					$driver_temp->SetProperties(array('id_car' => $cars_temp->id));
					
					if($driver_temp->save()) {
						$driver_status->ChangeStatus(3, null);
						echo json_encode(array('result' => 'success')); exit;
					} else {
						$errors = $this->GetErrors($driver_temp);
						echo json_encode(array('result' => 'failure', 'error' => $errors)); exit;
					}	
				} else {	
					$errors = $this->GetErrors($cars_temp);
					echo json_encode(array('result' => 'failure', 'error' => $errors)); exit;
				}
			} else	{
				$errors = $this->GetErrors($driver_temp);
				echo json_encode(array('result' => 'failure', 'error' => $errors)); exit;
			}	
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Файлы не были отправлены.')); exit;
		}
	}
	
	public function actionGet_balance()
    {	
		$driver_id = $this->is_authentificate();
		$driver = Users::model()->findByPk($driver_id);
		echo json_encode(array('result' => $driver->balance));	
	}	

    public function actionEdit_profile()
    {	
		$driver_id = $this->is_authentificate();
		//$driver_id = 219;
		//получаем данные водителя 
		$DriverData = Drivers::GetDriverData($driver_id, true);
		$driver = $DriverData['driver'];
		$car = $DriverData['car'];
		$driver_status = $DriverData['user_status'];
		//костылек для Дашкевича--------------------------------------------------------------------------------
		if(!isset($_POST['Users']['nickname'])) {
			TobishСrutch::СrutchFormatData($_POST);	
		}
		//------------------------------------------------------------------------------------------------------
		
		if(!empty($_POST['Users']))
		{	
			$this->filesRegProcessing('UsersTemp', 'CarsTemp');
			//получаем данные водителя из буфера либо создаем их
			$TempData = DriversTemp::GetDriverTempDataProfile($driver, $car);
			
			$driver_temp = $TempData['driver_temp'];
			$cars_temp = $TempData['cars_temp'];
			
			$driver_temp->SetProperties($_POST['Users']);
			
			//$driver_temp->copy_photo();
			
			if($driver_temp->validate()) {
		
				if(isset($_POST['Cars'])) {	
					$cars_temp->SetProperties($_POST['Cars']);
					//echo json_encode(array('result' => $cars_temp->attributes)); exit; 
					if($cars_temp->save()) {
						$driver_temp->SetProperties(array('id_car' => $cars_temp->id));
						
						if($driver_temp->save()) {
							$driver_status->ChangeStatus(3, null);
							echo json_encode(array('result' => 'success'));
						} else {
							$errors = $this->GetErrors($driver_temp);
							echo json_encode(array('result' => 'failure', 'error' => $errors)); 
						}	
					} else {
						$errors = $this->GetErrors($cars_temp);
						echo json_encode(array('result' => 'failure', 'error' => $errors)); 
					} 
				}
			} else {
				$errors = $this->GetErrors($driver_temp);
				echo json_encode(array('result' => 'failure', 'error' => $errors)); 
			}
		} else {
			$bodytype_all = Bodytypes::GetAll(true);
			
			$send_array = array('Driver' =>$driver->getAttributes(), 'Cars' => $car->getAttributes(), 'Bodytypes_name'=>$bodytype_all['names'], 'Bodytypes_index'=>$bodytype_all['indexes']);
			echo json_encode(array('response' => $send_array));
		}
    }
	
	public function actionService_driver()
    {	
		$driver_id = $this->is_authentificate();
		//$driver_id = 219;
		//получаем данные водителя 
		$DriverData = Drivers::GetDriverData($driver_id, true);
	
		$driver = $DriverData['driver'];
		$car = $DriverData['car'];
		$driver_status = $DriverData['user_status'];
		
		$request = json_decode(file_get_contents('php://input'), true);
		if(!empty($request)) {
			//получаем данные водителя из буфера либо создаем их
			$TempData = DriversTemp::GetDriverTempDataServices($driver, $car);
			
			$driver_temp = $TempData['driver_temp'];
			if(isset($request['price_class']) && !empty($request['price_class'])) {		
				$driver_temp->SetProperties(array('id_price_class' => $request['price_class']));
		
				if($driver_temp->save()) {
					$driver_temp->copy_photo();
					$driver_status->ChangeStatus(3, null);
					
					if(isset($request['DriverService'])) {
						//костыль для Щербинина
						$services_data = TobishСrutch::ParseString($request['DriverService']);
						DriverServiceTemp::UpdateServices($driver_temp->id, $services_data, true);	
					} 
				}
			}
		
				
			echo json_encode(array('result' => 'success'));	 exit;
		}
		
		$services_all = Services::GetAll(1, true);
		$price_class_all = PriceClass::GetAll(true);
		
		
		$services_driver = Drivers::GetDriverServices($driver_id);
		
		$send_array = array('Services_name'=>$services_all['names'], 'Services_index'=>$services_all['indexes'], 'PriceClass_name'=>$price_class_all['names'], 'PriceClass_index'=>$price_class_all['indexes'], 'price_class' =>$driver->id_price_class, 'services_driver' =>$services_driver);
	
		echo json_encode(array('response' => $send_array));
    }
	
	public function actionProfile()
	{		
		$driver_id = $this->is_authentificate();
		$driver_status = UserStatus::GetUserById($driver_id);	
		
		$send_array = $driver_status->user->getAttributes();
		$send_array['status'] = $driver_status->status->name;
		$average_commission = Settings::model()->findByAttributes(array('param' => 'average_commission'));
		$send_array['commission'] = $average_commission->value - ($send_array['rating'] - 3);
		echo json_encode(array('response' => $send_array));
	}
	
	public function actionChange_status()
	{		
		$driver_id = $this->is_authentificate();	
		
		$driver_status = UserStatus::GetUserById($driver_id);
		
		$request = json_decode(file_get_contents('php://input'));
		if(isset($request->status) && !empty($request->status)) {
			$driver_status->ChangeStatus(null, $request->status);
			echo json_encode(array('result' => 'success'));
		} 
	}
	
	//возвращает историю денежных средств в удобном формате
	public function _getPaymentsInfo(array $payments_history)
	{	
		
		$p_history = array();
		$start_date = NULL;
		$end_date = NULL;
		if(!empty($payments_history)) {
			foreach($payments_history as $i => $p) {
				$p_history[$i] = $p->GetPaymentInfo();
			}
			$start_date = $p_history[count($p_history) - 1]['date_create'];
			$end_date = $p_history[0]['date_create'];
		}
		
		return array('payments_history' => $p_history, 'start_date' => $start_date, 'end_date' => $end_date);
	}
	
	public function actionBalance()
	{		
		$driver_id = $this->is_authentificate();		
		
		$driver = Users::model()->findByPk($driver_id);
		
		//получаем историю денежных средств водителя (5 последних)
		$payments_history = Drivers::GetDriverPaymentsHistory($driver_id, 5, true);
		
		$PaymentsData = $this->_getPaymentsInfo($payments_history);
		
		$average_commission = Settings::model()->findByAttributes(array('param' => 'average_commission'));
		$send_array = array('balance' =>$driver->balance, 'average_commission' =>$average_commission->value, 'payments_history' => $PaymentsData['payments_history']);	
		echo json_encode(array('response' => $send_array));
	}
	
	public function actionMovement_balance()
	{	
		$driver_id = $this->is_authentificate();	
		//$driver_id = 138;	
		$date_interval = array();
		
		$request = json_decode(file_get_contents('php://input'));
		if(!empty($request->start_date)) {
			$date_interval['date_from'] = $request->start_date;
		}
		if(!empty($request->end_date)) {
			$date_interval['date_to'] = $request->end_date;
		}
		//получаем историю денежных средств водителя
		$payments_history = Drivers::GetDriverPaymentsHistory($driver_id, 0, true, $date_interval);
		
		$send_array = $this->_getPaymentsInfo($payments_history);
		
		$average_commission = Settings::model()->findByAttributes(array('param' => 'average_commission'));
		$send_array['average_commission'] = $average_commission->value;	
		
		echo json_encode(array('response' => $send_array));
	}
	
	public function actionStatistics_reviews()
	{		
		$driver_id = $this->is_authentificate();		
		
		$driver = Users::model()->findByPk($driver_id);
		
		//получаем историю отзывов водителя (2 последних)
		$driver_reviews = Drivers::GetDriverReviews($driver_id, 2, true);
		
		$ReviewsData = DriverReviews::GetReviewsInfo($driver_reviews);
	
		$count_completed = Orders::model()->countByAttributes(array('id_driver'=> $driver_id, 'execution_status'=>3));
		$count_rejected = OrderDriver::model()->countByAttributes(array('id_driver'=> $driver_id, 'adopted'=>2));
		$send_array = array('rating' =>$driver->rating, 'driver_reviews' => $ReviewsData['driver_reviews'], 'count_completed' => $count_rejected, 'count_rejected' => $count_rejected);	
		
		echo json_encode(array('response' => $send_array));
	}
	
	public function actionAll_reviews()
	{		
		$driver_id = $this->is_authentificate();	
		$request = json_decode(file_get_contents('php://input'));
		$date_interval = array();
		if(!empty($request->start_date)) {
			$date_interval['start_date'] = $request->start_date;
		}
		if(!empty($request->end_date)) {
			$date_interval['end_date'] = $request->end_date;
		}
		//получаем историю отзывов водителя
		$driver_reviews = Drivers::GetDriverReviews($driver_id, 0, true, $date_interval);

		$send_array =  DriverReviews::GetReviewsInfo($driver_reviews);	
		//print_r($send_array); exit;
		echo json_encode(array('response' => $send_array));
	}
	
	public function actionPassword_recovery()
	{	
		$request = json_decode(file_get_contents('php://input'));	
		if(!empty($request->phone))	{
			$driver = Users::model()->findByAttributes(array('phone' => $request->phone, 'id_type' => 1));
			if(!empty($driver)) {
				$verification_code = $this->GeneratePassword(4);
				//записываем в сессию сгенерированый код и id водителя
				Yii::app()->session->add("verification_code", crypt($verification_code));
				Yii::app()->session->add("driver_id", $driver->id);
				//отправляем СМС водителю
				$TurboSMS = new TurboSMS('taxichat', 'taxichat', 'TaxiChat');
				$TurboSMS->setMassage('Вы выбрали восстановление пароля. Код подтверждения: '.$verification_code)->setPhone($request->phone)->sendMassage();	
				
				echo json_encode(array('result' => 'success'));
			} else {
				echo json_encode(array('result' => 'failure', 'error' => 'Водителя с таким телефоном не существует.')); 
			}	
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Не передается телефон.')); 
		}	
	}
	
	public function actionPassword_recovery_confirmation()
	{
		$request = json_decode(file_get_contents('php://input'));	
		if(!empty($request->verification_code))	{
			//вытягиваем из сессии сгенерированый код и id водителя
			$verification_code = Yii::app()->session->get("verification_code");
			$driver_id = Yii::app()->session->get("driver_id");
			if(isset($verification_code, $driver_id)) {
				$driver = Users::model()->findByPk($driver_id);
				//сверяем код с переданым
				if(!empty($driver) && $verification_code == crypt($request->verification_code,$verification_code)) {
					//если код верный, генерируем новый пароль и отправляем его СМСкой
					$password = $this->GeneratePassword(6);
				
					$driver->ChangePassword($password);
					
					$TurboSMS = new TurboSMS('taxichat', 'taxichat', 'TaxiChat');
					$TurboSMS->setMassage('Вы выбрали восстановление пароля. Ваш новый пароль: '.$password)->setPhone($driver->phone)->sendMassage();	
					
					echo json_encode(array('result' => 'success'));
				} else {
					echo json_encode(array('result' => 'failure', 'error' => 'Не верный проверочный код.')); 
				}
			} else {
				echo json_encode(array('result' => 'failure', 'error' => 'Проверочный код уже не действителен.')); 
			}		
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Не передается проверочный код.')); 
		}
	}
	
	public function actionAccount_activate()
	{		
		$driver_id = $this->is_authentificate();
		$this->IsActivate($driver_id);
	}
	
	
}