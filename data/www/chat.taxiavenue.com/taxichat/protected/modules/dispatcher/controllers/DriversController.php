<?php

use Softline\Mobile\PHP\PushNotifications\iOS as iOS;

require_once(Yii::getPathOfAlias('webroot').'/lib/Message/Message.php');
require_once(Yii::getPathOfAlias('webroot').'/lib/Sender.php');


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
				'actions'=>array('index', 'create','update','delete', 'delete_commission', 'create_review', 'activate', 'edit_moderation', 'drivers_map', 'banned', 'push_driver', 'push_client', 'push'),
				'roles'=>array('4'),
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
		/*
		$drivers=new UserStatus('search');
		
		$drivers->unsetAttributes();  // clear any default values
		if(isset($_GET['UserStatus']))
			$drivers->attributes=$_GET['UserStatus'];
		*/
		if(!empty($_POST)) {
			$parameters = '?';
			foreach ($_POST['filter'] as $k=>$v) { 
				if(!empty($v))
					$parameters.=$k."=".$v.'&';
	        }
			$this->redirect(array('index'.$parameters)); 
		}
		$criteria=new CDbCriteria();
		$criteria->mergeWith(array(
			'join'=>'INNER JOIN users driver ON driver.id = t.id_user',
			'condition'=>'driver.id_type = 1'
		));
		if(!empty($_GET['driver'])) {
			$criteria->mergeWith(array(
				'join'=>'INNER JOIN users driver ON driver.id = t.id_user',
				'condition'=>'driver.phone LIKE :driver OR driver.name LIKE :driver OR driver.surname LIKE :driver OR driver.email LIKE :driver OR driver.nickname LIKE :driver','params'=>array(':driver'=>'%'.$_GET['driver'].'%')
			));
			
		}
		
		
		$criteria->order = 'moderation DESC, id DESC';
		$count=UserStatus::model()->count($criteria);
		$pages=new CPagination($count);
		
		$pages->pageSize=15;
		$pages->applyLimit($criteria);
		
		$drivers=UserStatus::model()->findAll($criteria);
		 
		$users=null;
        foreach ($drivers as $driver)
		{
           if ($driver->moderation != 0){
           	$users[] =  $driver ;
           }
		}

		$this->render('index',array(
			'drivers'=>$users, 'pages'=>$pages,
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
	
	public function actionEdit_moderation($id)
	{	
		$user_status = UserStatus::model()->findByAttributes(array('id_user' => $id));
		$driver_temp = UsersTemp::model()->findByAttributes(array('id_driver' => $id));
		if(!empty($driver_temp)) {
			$cars_temp = CarsTemp::model()->findByPk($driver_temp->id_car);
		} else {
			$user_status->moderation = 1;
			$user_status->save();
			$this->redirect(array('index'));
		}
		
		$services_all = CHtml::listData(Services::model()->findAllByAttributes(array('is_driver' => 1), array('order'=>'id ASC')), 'id', 'name');
		$services_driver_temp = CHtml::listData(DriverServiceTemp::model()->findAllByAttributes(array('id_driver' => $driver_temp->id)), 'id_service', 'id_service');
		if(empty($services_driver_temp)) {
			$services_driver_temp = CHtml::listData(DriverService::model()->findAllByAttributes(array('id_driver' => $driver_temp->id_driver)), 'id_service', 'id_service');
		}
		
		$price_class_all = CHtml::listData(PriceClass::model()->findAll(array('order'=>'id ASC')), 'id', 'name');
		$bodytype_all = CHtml::listData(Bodytypes::model()->findAll(array('order'=>'id ASC')), 'id', 'name');
		
		if(isset($_POST['Users']))
		{	
			$driver = Users::model()->findByPk($id);
			$driver->rememberPhoto();
			$photo = $driver_temp->photo;
			$id_user = $driver->id;
			$id_car = $driver->id_car;
			//$driver = $this->loadModel($id);
			$car = Cars::model()->findByPk($driver->id_car);
			$driver->attributes = $_POST['Users'];
			$driver->password_old = $driver->password;
			$driver->id = $id_user;
			$driver->id_car = $id_car;
			$driver->photo = $photo;
			
			if($driver->validate()) {
		
				if(isset($_POST['Cars'])) {
					if(empty($_POST['Cars']['id']))
						unset($_POST['Cars']['id']);
						
					$car->rememberPhoto();
					
					$car->changePhoto($cars_temp);
					/*
					if(!empty($_FILES['Cars']['name']['photo1'])) {
						$_POST['Cars']['photo1'] = $_FILES['Cars']['name']['photo1'];
					}	
					if(!empty($_FILES['Cars']['name']['photo2']))
						$_POST['Cars']['photo2'] = $_FILES['Cars']['name']['photo2'];
						
					if(!empty($_FILES['Cars']['name']['photo3']))
						$_POST['Cars']['photo3'] = $_FILES['Cars']['name']['photo3'];
						
					if(!empty($_FILES['Cars']['name']['photo4']))
						$_POST['Cars']['photo4'] = $_FILES['Cars']['name']['photo4'];
						
					if(!empty($_FILES['Cars']['name']['photo5']))
						$_POST['Cars']['photo5'] = $_FILES['Cars']['name']['photo5'];
						
					if(!empty($_FILES['Cars']['name']['photo6']))
						$_POST['Cars']['photo6'] = $_FILES['Cars']['name']['photo6'];
						
					if(!empty($_FILES['Cars']['name']['photo7']))
						$_POST['Cars']['photo7'] = $_FILES['Cars']['name']['photo7'];
					*/	
					$car->attributes = $_POST['Cars'];
					$car->id = $id_car;
					
					if($car->save()) {
						if(!empty($_FILES['Users']['name']['photo']))
							$driver->photo = $_FILES['Users']['name']['photo'];
						$car->copy_photos();
						
						if($driver->save()) {
							$driver->copy_photo();
							$user_status->moderation = 1;
							$user_status->save();
							if(isset($_POST['DriverService']['id']) && !empty($_POST['DriverService']['id'])) {
								DriverService::model()->deleteAll('id_driver = ?' , array($driver->id));
						
								foreach($_POST['DriverService']['id'] as $service_id) {
									$service_dr = new DriverService;
									$service_dr->id_driver = $driver->id;
									$service_dr->id_service = $service_id;
									$service_dr->save();
								}
								if($driver_temp->delete()) {
									$cars_temp->delete();
									DriverServiceTemp::model()->deleteAll('id_driver = ?' , array($driver_temp->id));
								}	
							}
							
							$user_status->SendPush('Ваши данные успешно промодерированы', ['push_type' => 3], false);
								
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
		$this->render('edit_moderation',array(
			'driver'=>$driver, 'car'=>$car, 'id'=>$id, 'services_all'=>$services_all, 'services_driver'=>$services_driver_temp, 'price_class_all'=>$price_class_all, 'bodytype_all'=>$bodytype_all, 'user_status'=>$user_status,
		));
	}
	
	
	
	private function _edit($id = 0)
	{	
		//$is_ajax = Yii::app()->request->isAjaxRequest;
		if($id == 0) {
			$driver = new Users;
			$car = new Cars;
			$payments_history = array();
			$user_status = new UserStatus;
		} else {
			$driver = Users::model()->findByPk($id);
			//$driver = $this->loadModel($id);
			$car = Cars::model()->findByPk($driver->id_car);
			$payments_history = PaymentsHistory::model()->findAllByAttributes(array('id_user' => $driver->id),array('order'=>'id ASC'));
			$user_status = UserStatus::model()->findByAttributes(array('id_user' => $driver->id));
		}	
		
		$new_fine = new PaymentsHistory;
		
		//$services_all = CHtml::listData(Services::model()->findAllByAttributes(array('id_user' => 1), array('order'=>'id ASC')), 'id', 'name');
		$services_driver = CHtml::listData(DriverService::model()->findAllByAttributes(array('id_driver' => $driver->id), array('order'=>'id ASC')), 'id_service', 'id_service');
		
		$price_class_all = CHtml::listData(PriceClass::model()->findAll(array('order'=>'id ASC')), 'id', 'name');
		$bodytype_all = CHtml::listData(Bodytypes::model()->findAll(array('order'=>'id ASC')), 'id', 'name');
		
		$driver_commissions = array();
		$reviews_driver = array();
		if($id != 0) {
			$driver_commissions = DriverCommission::model()->findAllByAttributes(array('id_driver' => $driver->id), array('order'=>'id ASC'));
			$reviews_driver = DriverReviews::model()->findAllByAttributes(array('id_driver' => $driver->id), array('order'=>'id ASC'));
			//print_r($driver_commissions); exit;
		}
		if(isset($_POST['Users']))
		{	
			$driver->password_old = $driver->password;
			
			$driver->rememberPhoto();
			$driver->attributes = $_POST['Users'];
			
			$driver->id_type = 1;
			
			/*
			if(!empty($driver->password))
				$driver->password = crypt($driver->password);	
			*/
			
			if($driver->validate()) {
				
				if(isset($_POST['Cars'])) {
					if(empty($_POST['Cars']['id']))
						unset($_POST['Cars']['id']);
					$car->rememberPhoto();
					
					$car->attributes = $_POST['Cars'];
					
					if($car->save()) {
						
						$driver->id_car = $car->id;
						
						if(!empty($_FILES['Users']['name']['photo']))
							$driver->photo = $_FILES['Users']['name']['photo'];
						
						if($driver->save()) {
							if($id == 0) {
								$user_status = new UserStatus;
								$user_status->moderation = 1;
								$user_status->id_user = $driver->id;
								$user_status->id_status = $driver->id;
								$user_status->id_status = 3;
								//print_r($user_status); exit;
								$user_status->save();
							}	
							else {
								DriverService::model()->deleteAll('id_driver = ?' , array($driver->id));
							}
							if(isset($_POST['DriverService']['id']) && !empty($_POST['DriverService']['id'])) {
								DriverService::model()->deleteAll('id_driver = ?' , array($driver->id));
						
								foreach($_POST['DriverService']['id'] as $service_id) {
									$service_dr = new DriverService;
									$service_dr->id_driver = $driver->id;
									$service_dr->id_service = $service_id;
									$service_dr->save();
								}	
							}
							
							if(isset($_POST['commission_add']) && count($_POST['commission_add'])>0){
								foreach($_POST['commission_add'] as $kda => $gdata){
									if ($kda == 0)
										continue;
									$commission_new = new DriverCommission;
									$commission_new->attributes = $gdata;
									$commission_new->id_driver = $driver->id;
									$commission_new->save();	
								}
							}
							
							if(isset($_POST['driver_commission']) && count($_POST['driver_commission'])>0){
								foreach($_POST['driver_commission'] as $kda => $gdata){
									if ($kda == 0)
										continue;
									$commission = DriverCommission::model()->findByPk($kda);
									$commission->attributes = $gdata;
									$commission->save();	
								}
							}
							
							if(!empty($_POST['PaymentsHistory']['value'])){
								
								$new_fine->attributes = $_POST['PaymentsHistory'];
								$new_fine->id_user = $driver->id;
								$new_fine->id_type = 4;
								$balance = $driver->balance - $new_fine->value;
								$rating = $driver->rating - 0.1;
							
								if($rating < 0)
									$rating = 0;
								$new_fine->balance = $balance;
								$new_fine->rating = $rating;
								$new_fine->value = - $new_fine->value;
								$new_fine->save();
								$driver->balance = $balance;
								$driver->rating = $rating;
								$driver->save();
							}
								
							$this->redirect(array('update','id'=>$driver->id));		
						}	
					}
				}
			}
		}
		$this->render('update',array(
			'driver'=>$driver, 'car'=>$car, 'id'=>$id,  'services_driver'=>$services_driver, 'price_class_all'=>$price_class_all, 'bodytype_all'=>$bodytype_all, 'driver_commissions'=>$driver_commissions, 'new_fine'=>$new_fine, 'payments_history'=>$payments_history, 'reviews_driver'=>$reviews_driver, 'user_status'=>$user_status,
		));
	}
	
	public function actionDelete($id)
	{	
		$user = Users::model()->findByPk($id);
		
		if($user->delete()) {
			//$car = Cars::model()->findByPk($user->id_car);
			//$car->delete();
			
			UserStatus::model()->deleteAll('id_user = ?' , array($user->id));
			$car = Cars::model()->findByPk($user->id_car);
			$car->delete();
			DriverService::model()->deleteAll('id_driver = ?' , array($user->id));
			DriverCommission::model()->deleteAll('id_driver = ?' , array($user->id));
			PaymentsHistory::model()->deleteAll('id_user = ?' , array($user->id));
		}
		$this->redirect(array('index'));
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
		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		//if(!isset($_GET['ajax']))
		//	$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}
	
	public function actionCreate_review()
    {
        $review = new DriverReviews;
		$drivers = CHtml::listData(Users::model()->findAllByAttributes(array('id_type' => 1)), 'id', 'nickname');
		$customers = CHtml::listData(Users::model()->findAllByAttributes(array('id_type' => 2)), 'id', 'phone'); 
		$evaluations = CHtml::listData(Evaluations::model()->findAll(array('order' => 'id ASC')), 'id', 'name'); 
        if(isset($_POST['DriverReviews'])) {
            $review->attributes = $_POST['DriverReviews'];
			$driver = Users::model()->findByPk($review->id_driver);
			$driver->password_old = $driver->password;
			$driver->rating += $review->evaluation->value;
			$review->rating = $driver->rating;
			$review->date_review = date('Y-m-d H:i:s');
			if(empty($review->text))
				$review->text = NULL;
			if($review->save()) {
				$driver->save();
				$this->redirect(array('update','id'=>$review->driver->id));
			}	
        }
		$this->render('review_create',array(
			'review'=>$review, 'drivers' => $drivers, 'customers' => $customers, 'evaluations' => $evaluations,
		));
    }
	
	public function actionActivate($id)
    {	
		$driver = UserStatus::GetUserById($id);
		if(!empty($driver)) {
			$driver->ChangeStatus(1, null);
			$driver->SendPush('Ваш профиль был активирован админом.', ['push_type' => 3], false);
			
			Yii::app()->user->setFlash('success','Водитель был успешно активирован.');
		} else {
			Yii::app()->user->setFlash('success','Не удалось активировать водителя.');
		}
		$this->redirect(array('index'));	
    }
	
	public function actionDrivers_map()
    {	
		$criteria=new CDbCriteria();
		$criteria->mergeWith(array(
			'join'=>'INNER JOIN users driver ON driver.id = t.id_user',
			'condition'=>'driver.id_type = 1'
		));
		$criteria->addCondition("id_status != 3");
		$drivers = UserStatus::model()->findAll($criteria);
		$ajax = 0;
		if(isset($_POST['ajax'])) {
			$ajax = 1;
			$this->layout = false;
		}
		$this->render('drivers_map',array(
			'drivers'=>$drivers, 'ajax'=>$ajax,
		));
    }
	
	
	public function actionBanned($id)
    {	
        $driver_status = UserStatus::model()->findByAttributes(array('id_user' => $id));
		$driver_status->moderation = 0;
		if($driver_status->save()) {
			$this->redirect(array('index'));
		}	
    }
	
	public function loadModel($id)
	{
		$user=Users::model()->findByPk($id);
		if($user===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $user;
	}
	
	public function actionPush_driver()
	{

		$oMessage = iOS\Message::getInstance()->setAlertBody('Тест, не обращай внимания')->setBadge(1)->setValue('push_type', 1)->setValue('order_id', 11)->setSound('default')->setDeviceId("b82af19261e052d55d05971daee470a174bd61e48317989bbd929ceff4aa205c");
		$oSender = new iOS\Sender(Yii::getPathOfAlias('webroot') . "/cert/CertificateTaxiChatDeveloper.pem");
		$oSender->setIsProduction(false);
		$res = $oSender->send($oMessage);
		//echo(iconv('utf-8', 'windows-1251','Валэра ну сколько можно?').'<div><img src="http://forum.na-svyazi.ru/uploads/201402/post-134907-1391810364.png"></img></div>'); exit;
		echo('<div><img src="http://lichnosti.net/photos/1306/13216224575.jpg"></img></div>'); exit;
	}
	
	public function actionPush_client()
	{

		$oMessage = iOS\Message::getInstance()->setAlertBody('Тест, не обращай внимания')->setBadge(1)->setValue('push_type', 1)->setValue('order_id', 11)->setSound('default')->setDeviceId("a4ce77fba5187717e8674546e9aa3e31879a5f472dbe473ffaf84fc7bf0f892f");
		$oSender = new iOS\Sender(Yii::getPathOfAlias('webroot') . "/cert/CertificateTaxiChatClientDevelopment.pem");
		$oSender->setIsProduction(false);
		$res = $oSender->send($oMessage);
		
		//echo(iconv('utf-8', 'windows-1251','Валэра ну сколько можно?').'<div><img src="http://forum.na-svyazi.ru/uploads/201402/post-134907-1391810364.png"></img></div>'); exit;
		echo('<div><img src="http://lichnosti.net/photos/1306/13216224575.jpg"></img></div>'); exit;
	}
	
	public function actionPush()
	{
		/*$notification = new Notification('AIzaSyALlHs53zlD6RtIRDRoBndvP_GKO7PO0Bw');
		$res = $notification->setValue('sender', 'Валэра')->setValue('recipient', 'Миша')->SendPush('APA91bFsmVrdeL65UY_YsXU2frKYfkLrgfAo6u8J1h9kPpsUJiDtZ2Ec0fxBEgLuIvB_WXMBfk8--kNRxtWJw4TDhNNxbKInaz09j0dnDi5qNmjsuQcnn4PftBxgZ4FZGrhVtgLtJ5rvx-OiM-kWU4WjIntFZwt6YQ','фывфывфывaasdasd');
		echo('ыфвфывфыв<div><img src="http://forum.na-svyazi.ru/uploads/201402/post-134907-1391810364.png"></img></div>'); exit;
		print_r($res); exit;
		*/
		//print_r(get_headers('http://91.203.60.46:8888/taxi/taxichat/driver_application/drivers/profile',1)); exit;
		
		$driver_id = 140;
		$driver = UserStatus::model()->findByAttributes(array('id_user' => $driver_id));
		$push = new Push(true, $driver->tokin_id, $driver->mobile_os);
		$res = $push->setMassage('Тунь')->setValue('customer', 'гг')->sendPush();
		print_r($res); exit;
		
	}

	/**
	 * Performs the AJAX validation.
	 * @param Users $user the model to be validated
	 */
	protected function performAjaxValidation($user)	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='users-form')
		{
			echo CActiveForm::validate($user);
			Yii::app()->end();
		}
	}
	
}
