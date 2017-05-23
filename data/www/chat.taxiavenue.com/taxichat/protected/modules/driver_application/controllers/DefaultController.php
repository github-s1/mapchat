<?php

class DefaultController extends MobileApplicationController {
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
                'actions'=>array('registration', 'login', 'logout', 'moderation', 'change_location', 'is_auth', 'last_push', 'check_status', 'send_activate_key'),
                'users'=>array('*'),
            ),
            array('deny',
                'users'=>array('*'),
            ),
        );
    }
	
	public function actionIs_auth()
    {
		if($this->is_login()) {
			echo json_encode(array('result' => 'success'));
		} else {	
			echo json_encode(array('result' => 'failure'));
		}	
	}
	
    public function actionLogin()
    {	
		$request = json_decode(file_get_contents('php://input'));
		if(isset($request->username, $request->password, $request->os, $request->tokin_id))
		{	
			//авторизаируем водителя
			$is_aut = LoginForm::UserAuthorize($request->username, $request->password, 1);	
		
			if($is_aut)
			{	
				//если авторизация прошла успешно меняем его статус и обновляем токин
				$user_status = UserStatus::GetUserById(Yii::app()->user->id);
				
				if($user_status->moderation != 0) {
					$user_status->ChangeStatus(null, 1);
					
					$user_status->RefreshTokin($request->tokin_id, $request->os);
					echo json_encode(array('result' => 'success', 'status' => Drivers::GetStatus($user_status)));
				} else {
					echo json_encode(array('result' => 'failure', 'error' => 'Ваша учетная запись заблокирована'));
				}	
			} else {
				echo json_encode(array('result' => 'failure', 'error' => 'Неверный логин или пароль'));
			}	
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Не передаются все необходимые данные'));
		}		
    }
	
    public function actionLogout() {	
		if(Users::logout()) {
			echo json_encode(array('result' => 'success'));
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Разлогиниться не возможно.'));
		}	
    }
	
    public function actionRegistration()
    {	
		//получаем данные водителя
		$DriverData = Drivers::GetDriverData(0, true);
		$driver = $DriverData['driver'];
		$car = $DriverData['car'];
		
		//костылек для Дашкевича--------------------------------------------------------------------------------
		if(!isset($_POST['Users']['phone'])) {
			TobishСrutch::СrutchFormatData($_POST);	
		}
		//------------------------------------------------------------------------------------------------------
		if(!empty($_POST['Users']) && !empty($_POST['Cars']) && !empty($_POST['UserStatus']))
		{	
			if(empty($_FILES)) {
				echo json_encode(array('result' => 'failure', 'error' => 'Не передаются изображения.')); exit;
			}
			//приводим файлы к нужному формату
			$this->filesRegProcessing("Users", "Cars");
			
			$driver->SetProperties($_POST['Users']);
			
			if($driver->validate()) {
		
				if(isset($_POST['Cars'])) {
				
					$car->SetProperties($_POST['Cars']);
					
					if($car->save()) {
						$driver->SetProperties(array('id_car' => $car->id));
						
						if($driver->save()) {
							//регистрируем водителя и обновляем его токин
							$user_status = Drivers::CreateRecord($driver->id, true, $driver->phone);
							$user_status->RefreshTokin($_POST['UserStatus']['tokin_id'], $_POST['UserStatus']['os']);
							//сохраняем его услуги
							if(isset($_POST['DriverService'])) {
								DriverService::UpdateServices($driver->id, $_POST['DriverService'], true);
							} else {
								DriverService::UpdateServices($driver->id, null, true);
							}
							//авторизируем его
							$_FILES = array();
							LoginForm::UserAuthorize($driver->phone, $_POST['Users']['password'], 1);
							
							echo json_encode(array('result' => 'success'));	
						} else	{
							$errors = $this->GetErrors($driver);
							echo json_encode(array('result' => 'failure', 'error' => $errors)); 
						}	
					} else	{
						$errors = $this->GetErrors($car);
						echo json_encode(array('result' => 'failure', 'error' => $errors)); 
					}
				}
			} else	{
				$errors = $this->GetErrors($driver);
				echo json_encode(array('result' => 'failure', 'error' => $errors)); 
			}	
		} else	{
			$services_all = Services::GetAll(1, true);
			$price_class_all = PriceClass::GetAll(true);
			$bodytype_all = Bodytypes::GetAll(true);
			
			$send_array = array('Services_name'=>$services_all['names'], 'Services_index'=>$services_all['indexes'], 'PriceClass_name'=>$price_class_all['names'], 'PriceClass_index'=>$price_class_all['indexes'], 'Bodytypes_name'=>$bodytype_all['names'], 'Bodytypes_index'=>$bodytype_all['indexes']);
			
			echo json_encode(array('response' => $send_array));
		}
    }
	
	public function actionChange_location()
	{		
		$driver_id = $this->is_authentificate();	
		$driver_status = UserStatus::GetUserById($driver_id);
		
		$request = json_decode(file_get_contents('php://input'));
		
		if(isset($request->lat) && !empty($request->lng)) {
			//сохраняем текущие координаты водителя
			$driver_status->ChangeLocation($request->lat, $request->lng);
			//если сейчас водитель выполняет заказ с свободным маршрутом добавляем точку к маршруту 
			OrdersOperations::StoreCustomRoute($driver_id, $request->lat, $request->lng);
		
			echo json_encode(array('result' => 'success'));
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Координаты не были переданы.')); exit;	
		}	
	}
	
	//возвращает промодерирован и активирован ли водитель
	public function actionModeration() {
		$driver_id = $this->is_authentificate();
		$driver_status = UserStatus::GetUserById($driver_id);
		if($driver_status->moderation == 0) {
			$result = 'banned';
		} else {
			if($driver_status->moderation != 2) {
				$result = 'success';
			} else {
				$result = 'failure';
			}
		}
		echo json_encode(array('result' => $result, 'is_activate' => $driver_status->is_activate));
	}
	
	//возвращает информацию по посл. пушу отправленому водителю
	public function actionLast_push() {
		$driver_id = $this->is_authentificate();
		$driver = UserStatus::GetUserById($driver_id);
		
		echo json_encode(array('result' => $driver->GetLastPush()));
	}
	
	public function actionCheck_status() {
		if($this->is_login()) {
			$driver_id = $this->is_authentificate();
			$driver_status = UserStatus::GetUserById($driver_id);
			$result = Drivers::GetStatus($driver_status);
		} else {
			$result = 1;
		}
		echo json_encode(array('result' => $result));
	}
	
	public function actionSend_activate_key() {
		MobileApplicationController::SendActivateKey();
	}
}