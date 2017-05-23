<?php

class DefaultController extends MobileApplicationController
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
                'actions'=>array('registration', 'login', 'logout', 'is_auth', 'account_activate', 'last_push', 'send_activate_key', 'check_status'),
                'users'=>array('*'),
            ),
            array('deny',
                'users'=>array('*'),
            ),
        );
    }
	
	public function actionIs_auth()
    {
		//echo json_encode(array('result' => getallheaders())); exit;
		if($this->is_login()) {
			echo json_encode(array('result' => 'success'));
		} else {	
			echo json_encode(array('result' => 'failure'));
		}
	}
	
	public function actionLogin()
    {	
		$request = json_decode(file_get_contents('php://input'));
		
		
		if(!empty($request->username) && !empty($request->password) && !empty($request->os) && !empty($request->tokin_id))
		{	
			//авторизаируем клиента
			$is_aut = LoginForm::UserAuthorize($request->username, $request->password, 2);	
		
			if($is_aut)
			{	
				//если авторизация прошла успешно меняем его статус и обновляем токин
				$user_status = UserStatus::GetUserById(Yii::app()->user->id);
			
				if($user_status->moderation != 0) {
					$user_status->ChangeStatus(null, 1);
					
					$user_status->RefreshTokin($request->tokin_id, $request->os);
					echo json_encode(array('result' => 'success', 'status' => Customers::GetStatus($user_status)));
				} else
					echo json_encode(array('result' => 'failure', 'error' => 'Ваша учетная запись заблокирована'));	
			} else
				echo json_encode(array('result' => 'failure', 'error' => 'Неверный логин или пароль'));
		} else
			echo json_encode(array('result' => 'failure', 'error' => 'Данные не передаются'));
    }
	
	public function actionLogout()
	{	
		if(Users::logout()) {
			echo json_encode(array('result' => 'success'));
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Разлогиниться не возможно.'));
		}
    }
	
	public function actionRegistration()
    {	
		$this->filesRegProcessing("Users", "Cars");
		
		$customer = Customers::Сreate();
		//костылек для Дашкевича--------------------------------------------------------------------------------
		if(!isset($_POST['Users']['phone'])) {
			TobishСrutch::СrutchFormatData($_POST);	
		}
		//------------------------------------------------------------------------------------------------------
		
		if(!empty($_POST['Users']))
		{	
			$customer->SetProperties($_POST['Users']);
			
			if($customer->save()) {
				//регистрируем клиента и обновляем его токин
				$user_status = Customers::CreateRecord($customer->id, true, $customer->phone);
				//авторизируем его
				$_FILES = array();
				LoginForm::UserAuthorize($customer->phone, $_POST['Users']['password'], 2);	
				echo json_encode(array('result' => 'success'));	
			} else {
				$errors = $this->GetErrors($customer);
				echo json_encode(array('result' => 'failure', 'error' => $errors)); 
			} 
		}
    }
	
	public function actionAccount_activate()
	{		
		$customer_id = $this->is_authentificate();
		$this->IsActivate($customer_id);
	}
	
	public function actionLast_push() {
		$customer_id = $this->is_authentificate();
		$customer = UserStatus::GetUserById($customer_id);
		
		echo json_encode(array('result' => $customer->GetLastPush()));
	}
	
	public function actionCheck_status() {
		if($this->is_login()) {
			$customer_id = $this->is_authentificate();
			$user_status = UserStatus::GetUserById($customer_id);
			$result = Customers::GetStatus($user_status);
		} else {
			$result = 1;
		}
		echo json_encode(array('result' => $result));
	}
	
	public function actionSend_activate_key() {
		MobileApplicationController::SendActivateKey();
	}
}