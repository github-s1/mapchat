<?php

//use Softline\Mobile\PHP\PushNotifications\iOS as iOS;

//require_once(Yii::getPathOfAlias('webroot').'/lib/Message/Message.php');
//require_once(Yii::getPathOfAlias('webroot').'/lib/Sender.php');


class DriversController extends Controller
{
	public $layout='//layouts/column2';
	
	
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
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('index', 'create','update','delete', 'activate', 'moderation', 'edit_moderation', 'drivers_map', 'banned', 'push_driver', 'cancel_changes'),
				'roles'=>array('3', '4', '7'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('reviews', 'delete_commission'),
				'roles'=>array('3', '4', '6', '7'),
			),
			 
			array('deny',  // deny all users
				'users'=>array('*'),
			),			
		);
	} 

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	
	public function actionIndex()
	{	
		//преобразуем фильтр в GET параметры
		$this->ApplyFilter($_POST, 'index');
		$search = null;
		if(!empty($_GET['driver'])) {
			$search = $_GET['driver'];
		} 
		
		$result = Drivers::GetAllDriversByCriteria(15, $search);
		
		$this->render('index',array(
			'drivers'=>$result['drivers'], 'pages'=>$result['pages']
		));	
	}
	 
	public function actionCreate()
	{	
		$this->_edit(0);
	}

	public function actionUpdate($id)
	{	
		$this->_edit($id);
	}
	
	//модерация изменения данных водителя
	public function actionEdit_moderation($id)
	{	
		//получаем данные по водителю
		$DriverTempData = DriversTemp::GetDriverTempData($id);
		$user_status = UserStatus::GetUserById($id);
		if(empty($DriverTempData)) {
			$user_status->ChangeStatus(1, null);
			$this->redirect(array('index'));
		} else {
			$driver_temp = $DriverTempData['driver_temp'];
			$cars_temp = $DriverTempData['cars_temp'];
			
			$services_all = Services::GetAll(1, false);
		
			//$services_driver_old = CHtml::listData(DriverService::model()->findAllByAttributes(array('id_driver' => $driver_temp->id_driver)), 'id_service', 'id_service'); 
			
			$price_class_all = PriceClass::GetAll();
			$bodytype_all = Bodytypes::GetAll();
			
			$services_driver_temp = $DriverTempData['services_driver_temp'];
			
			if(isset($_POST['Users']))
			{	
				$driver = Users::model()->findByPk($id);
				
				unset($_POST['Users']['id']);
				unset($_POST['Users']['id_car']);
				unset($_POST['Users']['photo']);
				
				$_POST['Users']['photo'] = $driver_temp->photo;
				
				$driver->SetProperties($_POST['Users']);
				
				$car = Cars::model()->findByPk($driver->id_car);
				
				if($driver->validate()) {
			
					if(isset($_POST['Cars'])) {
						//считываем новые фото
						$car->changePhoto($cars_temp);
						
						unset($_POST['Cars']['id']);
						$car->SetProperties($_POST['Cars']);
						
						if($car->save()) {
							//копируем новые фото на место старых
							$car->copy_photos();
							
							if($driver->save()) {
								//копируем новое фото на место старого
								$driver->copy_photo();
								
								//применяем новые услуги
								if(isset($_POST['DriverService'])) {
									DriverService::UpdateServices($driver->id, $_POST['DriverService'], false);
								} else {
									DriverService::UpdateServices($driver->id, null, false);
								}
								//удаляем данные из буфера
								DriversTemp::deleteTempData($driver_temp, $cars_temp);
								//меняем статус модерации водителя
								$user_status->ChangeStatus(1, null);
								$user_status->SendPush('Ваши данные были успешно промодерированы.', ['push_type' => 3], true, false);
								
								
								//Yii::app()->user->setFlash('success','Данные были успешно промодерированы.');
								$this->redirect(array('index'));		
							}
						}
						
					}
				}
			}
			
			$driver = new Users;
			$car = new Cars;
			$driver->attributes = $driver_temp->attributes;
			$car->attributes = $cars_temp->attributes;
			$this->layout = false;
			$this->render('edit_moderation',array(
				'driver'=>$driver, 'car'=>$car, 'id'=>$id, 'services_all'=>$services_all, 'services_driver'=>$services_driver_temp, 'price_class_all'=>$price_class_all, 'bodytype_all'=>$bodytype_all, 'user_status'=>$user_status,
			));
			
		}
	}
	
	//отклоняет изменения данных водителя 
	public function actionCancel_changes($id)
	{
		//получаем данные по водителю
		$DriverTempData = DriversTemp::GetDriverTempData($id);
		$user_status = UserStatus::GetUserById($id);
		
		if(empty($DriverTempData)) {
			$user_status->ChangeStatus(1, null);
			$this->redirect(array('index'));
		} else {
			$driver_temp = $DriverTempData['driver_temp'];
			$cars_temp = $DriverTempData['cars_temp'];
		
		}
		//меняем статус модерации водителя
		$user_status->ChangeStatus(1, null);
		//удаляем данные из буфера
		DriversTemp::deleteTempData($driver_temp, $cars_temp);
		$user_status->SendPush('Админ отклонил изменения личных данных', ['push_type' => 3], true, false);
		
		$this->redirect(array('index'));
	}
	
	
	
	private function _edit($id = 0)
	{	
		//получаем данные по водителю
		$DriverData = Drivers::GetDriverData($id);
		
		$driver = $DriverData['driver'];
		$car = $DriverData['car'];
		$user_status = $DriverData['user_status'];
		
		$services_all = Services::GetAll(1, false);
		$price_class_all = PriceClass::GetAll();
		$bodytype_all = Bodytypes::GetAll();
		
		$services_driver = $DriverData['services_driver'];
		$driver_commissions = $DriverData['driver_commissions'];
		
		if(isset($_POST['Users']))
		{	
			$driver->SetProperties($_POST['Users']);
			
			if($driver->validate()) {
				
				if(isset($_POST['Cars'])) {
					
					$car->SetProperties($_POST['Cars']);
					
					if($car->save()) {
						$driver->SetProperties(array('id_car' => $car->id));
						
						if($driver->save()) {
							if($id == 0) {
								// регестрируем водителя
								$user_status = Drivers::CreateRecord($driver->id, false);
							}	
							// меняем услуги
							if(isset($_POST['DriverService'])) {
								DriverService::UpdateServices($driver->id, $_POST['DriverService'], false);
							} else {
								DriverService::UpdateServices($driver->id, null, false);
							}
							// добавляем комиссии
							if(isset($_POST['commission_add'])){
								DriverCommission::CreateCommissions($driver->id, $_POST['commission_add']);
							}
							// меняем уже имеющиеся комиссии
							if(isset($_POST['driver_commission'])){
								
								DriverCommission::UpdateCommissions($_POST['driver_commission']);
							}
							
							Yii::app()->user->setFlash('success','Данные были успешно сохранены.');
								
							$this->redirect(array('update','id'=>$driver->id));		
						}	
					}
				}
			}
		}
		$this->layout = false;
		$this->render('update',array(
			'driver'=>$driver, 'car'=>$car, 'id'=>$id, 'services_all'=>$services_all, 'services_driver'=>$services_driver, 'price_class_all'=>$price_class_all, 'bodytype_all'=>$bodytype_all, 'driver_commissions'=>$driver_commissions, 'user_status'=>$user_status,
		));
	}
	
	public function actionReviews($id) {
		
		$driver = Users::model()->findByPk($id);
		if(!empty($driver)) {
			$order_commission = Drivers::GetDriverOrderCommission($driver->rating);
			
			$dataReviews = Drivers::GetDriverReviews($driver->id, 4);
		} else {
			exit;
		}
		
		$this->layout = false;
		$this->render('reviews',array(
			'driver'=>$driver, 'reviews_driver'=>$dataReviews['driver_reviews'], 'order_commission'=>$order_commission, 'pages'=>$dataReviews['pages']
		));
		
	}
	
	//модерация новых водителей
	public function actionModeration($id)
	{	
		//получаем данные по водителю
		$DriverData = Drivers::GetDriverData($id);
		
		$driver = $DriverData['driver'];
		$car = $DriverData['car'];
		$user_status = $DriverData['user_status'];
		
		$services_all = Services::GetAll(1, false);
		$price_class_all = PriceClass::GetAll();
		$bodytype_all = Bodytypes::GetAll();
		
		$services_driver = $DriverData['services_driver'];
		$driver_commissions = $DriverData['driver_commissions'];
		
		if(isset($_POST['Users']))
		{	
			
			$driver->SetProperties($_POST['Users']);
			
			if($driver->validate()) {
				
				if(isset($_POST['Cars'])) {
				
					$car->SetProperties($_POST['Cars']);
					
					if($car->save()) {
						
						$driver->SetProperties(array('id_car' => $car->id));
						
						if($driver->save()) {
							//применяем новые услуги
							if(isset($_POST['DriverService'])) {
								DriverService::UpdateServices($driver->id, $_POST['DriverService'], false);
							} else {
								DriverService::UpdateServices($driver->id, null, false);
							}
							// добавляем комиссии
							if(isset($_POST['commission_add'])){
								DriverCommission::CreateCommissions($driver->id, $_POST['commission_add']);
							}
							// меняем уже имеющиеся комиссии
							if(isset($_POST['driver_commission'])){
								
								DriverCommission::UpdateCommissions($_POST['driver_commission']);
							}
							
							$user_status->ChangeStatus(1, null);
							$user_status->SendPush('Ваши данные были успешно промодерированы', ['push_type' => 3], true, false);
							
							Yii::app()->user->setFlash('success','Данные были успешно промодерированы.');
								
							$this->redirect(array('update','id'=>$driver->id));							
						}	
					}
				}
			}
		}
		$this->layout = false;
		$this->render('moderation',array(
			'driver'=>$driver, 'car'=>$car, 'id'=>$id, 'services_all'=>$services_all, 'services_driver'=>$services_driver, 'price_class_all'=>$price_class_all, 'bodytype_all'=>$bodytype_all, 'driver_commissions'=>$driver_commissions, 'user_status'=>$user_status,
		));
	}
	
	public function actionDelete($id)
	{	
		//получаем данные по водителю
		$DriverData = Drivers::GetDriverData($id);
		
		$driver = $DriverData['driver'];
		if(!empty($driver)) {
			$car = $DriverData['car'];
			$user_status = $DriverData['user_status'];
			
			//если у водителя были заказы, запрещаем его удаление
			$orders = Orders::model()->findAllByAttributes(array('id_driver' => $id));
			if(!empty($orders)) {
				echo(0); exit;
			}
			//удаляем все данные водителя
			if($driver->delete()) {
				$user_status->delete();
				$car->delete();
				
				DriverService::model()->deleteAll('id_driver = ?' , array($id));
				DriverCommission::model()->deleteAll('id_driver = ?' , array($id));
				PaymentsHistory::model()->deleteAll('id_user = ?' , array($id));
				DriverReviews::model()->deleteAll('id_driver = ?' , array($id));
				OrderDriver::model()->deleteAll('id_driver = ?' , array($id));
				
				echo(1); exit;
			} 
		} 
		echo(0);
	}
	
	public function actionDelete_commission($id = null)
	{	
		if($id != null) {
			$commission = DriverCommission::model()->findByPk($id);
			if($commission->delete())
				echo 1;
			else
				echo 0;
			exit;	
		}
	}
	
	public function actionActivate($id)
    {	
		$driver = UserStatus::GetUserById($id);
		if(!empty($driver)) {
			$driver->ChangeStatus(1, null);
			$driver->SendPush('Ваш профиль был активирован админом.', ['push_type' => 3], true, false);
			
			Yii::app()->user->setFlash('success','Водитель был успешно активирован.');
		} else {
			Yii::app()->user->setFlash('success','Не удалось активировать водителя.');
		}
		$this->redirect(array('index'));	
    }
	
	public function actionDrivers_map()
    {	
		//получаем всех активных водителей которые в сети
		$drivers = Drivers::GetAllDriversByCriteria(0, null, 1);
		if(isset($_POST['ajax'])) {
			$this->layout = false;
			$drivers_array = array();
			if(!empty($drivers['drivers'])) {
				foreach($drivers['drivers'] as $idx => $dr) {
					$drivers_array[$idx] = $dr->getAttributes();
					$drivers_array[$idx]['phone'] = $dr->user->phone;
					$drivers_array[$idx]['car'] = $dr->user->car->getAttributes();
				}
			} 
			echo json_encode($drivers_array); exit;
		}
		$this->render('drivers_map',array(
			'drivers'=>$drivers['drivers'],
		));
    }
	
	
	public function actionBanned($id)
    {	
		//получаем данные по водителю
		$DriverData = Drivers::GetDriverData($id);
		$driver = $DriverData['driver'];
		if(!empty($driver)) {
			$car = $DriverData['car'];
			$user_status = $DriverData['user_status'];
			//удаляем все данные водителя
			if($driver->delete()) {
				$user_status->delete();
				$car->delete();
				
				DriverService::model()->deleteAll('id_driver = ?' , array($id));
				DriverCommission::model()->deleteAll('id_driver = ?' , array($id));
				PaymentsHistory::model()->deleteAll('id_user = ?' , array($id));
				DriverReviews::model()->deleteAll('id_driver = ?' , array($id));
				OrderDriver::model()->deleteAll('id_driver = ?' , array($id));
			} 
		} 
		$this->redirect(array('index'));	
    }

	//тестовый метод отправляет любой пушь любому пользователю
	public function actionPush_driver()
	{
		$driver = UserStatus::GetUserById(322);
		$res = $driver->SendPush('Ваш профиль был забанен админом.', ['push_type' => 16], false);
		print_r($res); exit;
		echo('<div><img src="http://lichnosti.net/photos/1306/13216224575.jpg"></img></div>'); exit;
	}
}
