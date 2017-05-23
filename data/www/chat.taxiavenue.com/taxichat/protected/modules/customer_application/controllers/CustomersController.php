<?php

class CustomersController extends MobileApplicationController
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
                'actions'=>array('profile', 'edit_profile', 'all_reviews', 'edit_password', 'orders_archive', 'all_archive', 'delete_orders_archive', 'delete_all_archive', 'bonuses', 'HistoryBonuses', 'password_recovery', 'password_recovery_confirmation'),
                'users'=>array('*'),
            ),
            array('deny',
                'users'=>array('*'),
            ),
        );
    }
	
	public function actionEdit_password()
    {
		$user_id = $this->is_authentificate();
		$customer = Users::model()->findByPk($user_id);
		$request = json_decode(file_get_contents('php://input'));
		
		if(!empty($request->oldPassword)) {
			//проверяем верный ли старый пароль
			if($customer->password !== crypt($request->oldPassword, $customer->password)) {
				echo json_encode(array('result' => 'failure', 'error' => 'Старый пароль не верный.')); 
			} else {
				if(!empty($request->newPassword)) {
					//меняем пароль
					$customer->ChangePassword($request->newPassword);		
					echo json_encode(array('result' => 'success'));
				} else {
					echo json_encode(array('result' => 'failure', 'error' => 'Новый пароль не был отправлен.')); 
				}	
			}		
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Старый пароль не был отправлен.')); 
		}
	}
	
	public function actionProfile()
	{		
		$user_id = $this->is_authentificate();	
		$customer = Users::model()->findByPk($user_id);
		$send_array = array('nickname' =>$customer->nickname, 'photo' => $customer->photo);
		echo json_encode(array('response' => $send_array));
	}
	
	public function actionEdit_profile()
    {	
		$user_id = $this->is_authentificate();	
		$customer = Users::model()->findByPk($user_id);
		
		if(!empty($_POST['Users']))
		{	
			$this->filesRegProcessing("Users", "Cars");
			
			$customer->SetProperties($_POST['Users']);
			if($customer->save()) {
				echo json_encode(array('result' => 'success'));	
			} else {
				$errors = $this->GetErrors($customer);
				echo json_encode(array('result' => 'failure', 'error' => $errors)); 
			}
			
		} else {
			echo json_encode(array('response' => $customer->attributes));	
		}	
    }
	
	public function actionOrders_archive()
	{		
		$user_id = $this->is_authentificate();
		//$user_id = 145;
		//получаем архив заказов клиента (3 последних записи)
		$orders = Customers::GetOrdersArchive($user_id, 3, null, null);
		
		$OrdersData = OrdersOperations::GetOrdersInfo($orders);
		
		echo json_encode(array('response' => $OrdersData['orders_array']));
	}
	
	public function actionAll_archive()
	{		
		$user_id = $this->is_authentificate();
		//$user_id = 145;
		
		$request = json_decode(file_get_contents('php://input'));
		
		$date_interval = array();
		if(!empty($request->start_date)) {
			$date_interval['start_date'] = $request->start_date;
		}
		if(!empty($request->end_date)) {
			$date_interval['end_date'] = $request->end_date;
		}
		//получаем архив заказов клиента
		$orders = Customers::GetOrdersArchive($user_id, 0, $date_interval, null);
		
		$send_array = OrdersOperations::GetOrdersInfo($orders);		
		echo json_encode(array('response' => $send_array));
	}
	
	public function actionDelete_orders_archive()
	{		
		$user_id = $this->is_authentificate();
		//$user_id = 147;
		
		$request = json_decode(file_get_contents('php://input'));
		
		if(!empty($request->orders)) {
			foreach($request->orders as $id) {
				if($id != 0) {
					$order = Orders::model()->findByPk($id);
					if($order->id_customer == $user_id) {
						$order->is_archive_delete = 1;
						$order->save();
					}
				}	
			}
			echo json_encode(array('result' => 'success'));	
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Не выбраны заказы для удаления.')); 
		}
	}
	
	public function actionDelete_all_archive()
	{		
		$user_id = $this->is_authentificate();
		//$user_id = 147;
		//получаем архив заказов клиента
		$orders = Customers::GetOrdersArchive($user_id, 0, null, null);
		
		foreach($orders as $ord) {
			$ord->is_archive_delete = 1;
			$ord->save();
		}
		echo json_encode(array('result' => 'success'));
	}
	
	public function actionAll_reviews()
	{		
		$user_id = $this->is_authentificate();
		//$user_id = 147;
		$request = json_decode(file_get_contents('php://input'));
		$date_interval = array();
		if(!empty($request->start_date)) {
			$date_interval['start_date'] = $request->start_date;
		}
		if(!empty($request->end_date)) {
			$date_interval['end_date'] = $request->end_date;
		}
		//получаем историю отзывов за указаынй период
		$customer_reviews = Customers::GetCustomerReviews($user_id, $date_interval);
		//преобразуем отзывы в удобный формат
		$send_array =  DriverReviews::GetReviewsInfo($customer_reviews);	
		echo json_encode(array('response' => $send_array));
	}
	
	public function actionBonuses()
	{	
		$user_id = $this->is_authentificate();
		//$user_id = 147;
		$customer = Users::model()->findByPk($user_id);
		//получаем историю бонусов (3 последних)
		$bonuses_history = Customers::GetHistoryBonuses($user_id, 3, null, null);
		
		$b_history = BonusesHistory::GetBonusesInfo($bonuses_history);
		
		$send_array = array('bonuses' =>$customer->bonuses, 'bonuses_history' => $b_history['bonuses_history']);	
		echo json_encode(array('response' => $send_array));
	}

	public function actionHistoryBonuses()
	{		
		$user_id = $this->is_authentificate();
		//$user_id = 147;
		$request = json_decode(file_get_contents('php://input'));
		$date_interval = array();
		if(!empty($request->start_date)) {
			$date_interval['start_date'] = $request->start_date;
		}
		if(!empty($request->end_date)) {
			$date_interval['end_date'] = $request->end_date;
		}
		//получаем историю бонусов за указаный период
		$bonuses_history = Customers::GetHistoryBonuses($user_id, 0, $date_interval, null);
		
		$send_array = BonusesHistory::GetBonusesInfo($bonuses_history);
		echo json_encode(array('response' => $send_array));
	}

	public function actionPassword_recovery()
	{	
		$request = json_decode(file_get_contents('php://input'));	
		if(!empty($request->phone))	{
			$customer = Users::model()->findByAttributes(array('phone' => $request->phone, 'id_type' => 2));
			if(!empty($customer)) {
				$verification_code = $this->GeneratePassword(4);
				//записываем в сессию сгенерированый код и id клиента
				Yii::app()->session->add("verification_code", crypt($verification_code));
				Yii::app()->session->add("customer_id", $customer->id);
				//отправляем СМС
				$TurboSMS = new TurboSMS('taxichat', 'taxichat', 'TaxiChat');
				$TurboSMS->setMassage('Вы выбрали восстановление пароля. Код подтверждения: '.$verification_code)->setPhone($request->phone)->sendMassage();	
				
				echo json_encode(array('result' => 'success'));
			} else {
				echo json_encode(array('result' => 'failure', 'error' => 'Клиента с таким телефоном не существует.')); 
			}	
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Не передается телефон.')); 
		}	
	}

	public function actionPassword_recovery_confirmation()
	{
		$request = json_decode(file_get_contents('php://input'));	
		if(!empty($request->verification_code))	{
			//вытягиваем из сессии сгенерированый код и id клиента
			$verification_code = Yii::app()->session->get("verification_code");
			$customer_id = Yii::app()->session->get("customer_id");
			if(isset($verification_code, $customer_id)) {
				$customer = Users::model()->findByPk($customer_id);
				//сверяем код с переданым
				if(!empty($customer) && $verification_code == crypt($request->verification_code,$verification_code)) {
					//если код верный, генерируем новый пароль и отправляем его СМСкой
					$password = $this->GeneratePassword(6);
				
					$customer->ChangePassword($password);
					
					$TurboSMS = new TurboSMS('taxichat', 'taxichat', 'TaxiChat');
					$TurboSMS->setMassage('Вы выбрали восстановление пароля. Ваш новый пароль: '.$password)->setPhone($customer->phone)->sendMassage();	
					
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
	
	
	
}