<?php

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
				'actions'=>array('index', 'create','update','delete', 'delete_commission', 'create_review', 'activate', 'moderation', 'edit_moderation', 'drivers_map', 'banned', 'push_driver', 'push_client', 'push', 'reviews', 'cancel_changes'),
				'roles'=>array('6'),
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
			'condition'=>'driver.id_type = 1 AND driver.id_creator = '.Yii::app()->user->id,
		));
		if(!empty($_GET['driver'])) {
			$criteria->mergeWith(array(
				'join'=>'INNER JOIN users driver ON driver.id = t.id_user',
				'condition'=>'LOWER(driver.phone) LIKE :driver OR LOWER(driver.name) LIKE :driver OR LOWER(driver.surname) LIKE :driver OR LOWER(driver.email) LIKE :driver OR LOWER(driver.nickname) LIKE :driver',
				'params'=>array(':driver'=>'%'.mb_strtolower($_GET['driver'], 'UTF-8').'%')
			));
		}
		
		
		$criteria->order = 'moderation DESC, id DESC';
		$count=UserStatus::model()->count($criteria);
		$pages=new CPagination($count);
		
		$pages->pageSize = 15;
		$pages->applyLimit($criteria);
		
		$drivers=UserStatus::model()->findAll($criteria);
		
		$this->render('index',array(
			'drivers'=>$drivers, 'pages'=>$pages,
		));	
	}
	 
	public function actionCreate()
	{	
		$driver = new Users;
		$car = new Cars;
		$payments_history = array();
		$user_status = new UserStatus;
		
		$new_fine = new PaymentsHistory;
		
		$services_all = CHtml::listData(Services::model()->findAllByAttributes(array('is_driver' => 1), array('order'=>'id ASC')), 'id', 'name');
		$services_driver = CHtml::listData(DriverService::model()->findAllByAttributes(array('id_driver' => $driver->id), array('order'=>'id ASC')), 'id_service', 'id_service');
		
		$price_class_all = CHtml::listData(PriceClass::model()->findAll(array('order'=>'id ASC')), 'id', 'name');
		$bodytype_all = CHtml::listData(Bodytypes::model()->findAll(array('order'=>'id ASC')), 'id', 'name');
		
		$driver_commissions = array();
		
		if(isset($_POST['Users']))
		{	
			$driver->password_old = $driver->password;
			
			$driver->rememberPhoto();
			$driver->attributes = $_POST['Users'];
			
			$driver->id_type = 1;
			$driver->id_creator = Yii::app()->user->id;
			
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
							$user_status->moderation = 2;
							$user_status->id_user = $driver->id;
							$user_status->id_status = 3;
							//print_r($user_status); exit;
							$user_status->save();
							
							if(isset($_POST['DriverService']['id']) && !empty($_POST['DriverService']['id'])) {
								DriverService::model()->deleteAll('id_driver = ?' , array($driver->id));
						
								foreach($_POST['DriverService']['id'] as $serv_id => $val) {
									$service_dr = new DriverService;
									$service_dr->id_driver = $driver->id;
									$service_dr->id_service = $serv_id;
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
							Yii::app()->user->setFlash('success','Водитель был успешно добавлен и убдет активирован после модерации.');
								
							$this->redirect(array('update','id'=>$driver->id));		
						}	
					}
				}
			}	
		}
		$this->layout = false;
		$this->render('update',array(
			'driver'=>$driver, 'car'=>$car, 'id'=>0, 'services_all'=>$services_all, 'services_driver'=>$services_driver, 'price_class_all'=>$price_class_all, 'bodytype_all'=>$bodytype_all, 'driver_commissions'=>$driver_commissions, 'new_fine'=>$new_fine, 'payments_history'=>$payments_history, 'user_status'=>$user_status,
		));
	}

	public function actionUpdate($id)
	{	
		$driver = Users::model()->findByPk($id);
		if(empty($driver) || $driver->id_creator != Yii::app()->user->id) {
			$this->redirect(array('index'));	
		}
		//$driver = $this->loadModel($id);
		$car = Cars::model()->findByPk($driver->id_car);
		$payments_history = PaymentsHistory::model()->findAllByAttributes(array('id_user' => $driver->id),array('order'=>'id ASC'));
		$user_status = UserStatus::model()->findByAttributes(array('id_user' => $driver->id));
		
		$driver_temp = UsersTemp::model()->findByAttributes(array('id_driver' => $id));
		if(!empty($driver_temp)) {
			$cars_temp = CarsTemp::model()->findByPk($driver_temp->id_car);
			$services_driver_temp = CHtml::listData(DriverServiceTemp::model()->findAllByAttributes(array('id_driver' => $driver_temp->id), array('order'=>'id ASC')), 'id_service', 'id_service');
		} else {
			$driver_temp = new UsersTemp;
			$driver_temp->attributes = $driver->attributes;
			$driver_temp->password_old = $driver_temp->password;
			$driver_temp->copy_photo();
			$driver_temp->id_driver = $id;
			$driver_temp->id = null;
			
			$cars_temp = new CarsTemp;
			$cars_temp->attributes = $car->attributes;
			$cars_temp->copy_photos();
			$cars_temp->id = null;
			
			$services_driver_temp = CHtml::listData(DriverService::model()->findAllByAttributes(array('id_driver' => $driver->id), array('order'=>'id ASC')), 'id_service', 'id_service');
		}
		
		$new_fine = new PaymentsHistory;
		
		$services_all = CHtml::listData(Services::model()->findAllByAttributes(array('is_driver' => 1), array('order'=>'id ASC')), 'id', 'name');
		
		$price_class_all = CHtml::listData(PriceClass::model()->findAll(array('order'=>'id ASC')), 'id', 'name');
		$bodytype_all = CHtml::listData(Bodytypes::model()->findAll(array('order'=>'id ASC')), 'id', 'name');
		$driver_commissions = DriverCommission::model()->findAllByAttributes(array('id_driver' => $driver->id), array('order'=>'id ASC'));
		
		if(isset($_POST['UsersTemp']))
		{	
			
			if($user_status->moderation == 0 || $user_status->moderation == 2) {
				Yii::app()->user->setFlash('success','Водитель не был промодерирован или был забанен. Редактирование данных запрещено.');				
				$this->redirect(array('update','id'=>$driver->id));
			}
			
			
			$driver_temp->password_old = $driver_temp->password;
			
			$driver_temp->rememberPhoto();
			$driver_temp->attributes = $_POST['UsersTemp'];
			
			if($driver_temp->validate()) {
		
				if(isset($_POST['CarsTemp'])) {	
					if(empty($_POST['CarsTemp']['id'])) {
						unset($_POST['CarsTemp']['id']);
					}	
					$cars_temp->rememberPhoto();
					$cars_temp->attributes = $_POST['CarsTemp'];
					//echo json_encode(array('result' => $cars_temp->attributes)); exit; 
					if($cars_temp->save()) {
						$driver_temp->id_car = $cars_temp->id;
						
						if($driver_temp->save()) {
							$driver_status = UserStatus::model()->findByAttributes(array('id_user' => $id));
							$driver_status->moderation = 3;
							$driver_status->save();
							
							DriverServiceTemp::model()->deleteAll('id_driver = ?' , array($driver_temp->id));
							
							if(isset($_POST['DriverService']['id']) && !empty($_POST['DriverService']['id'])) {
								foreach($_POST['DriverService']['id'] as $serv_id => $val) {
									$service_dr = new DriverServiceTemp;
									$service_dr->id_driver = $driver_temp->id;
									$service_dr->id_service = $serv_id;
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
							Yii::app()->user->setFlash('success','Данные были успешно изменены. Их активация будет выполнена после модерации админом.');
								
							$this->redirect(array('update','id'=>$driver->id));
							
						}	
					} 
				}
			}
			
		} 
		$this->layout = false;
		$this->render('update',array(
			'driver'=>$driver_temp, 'car'=>$cars_temp, 'id'=>$id, 'services_all'=>$services_all, 'services_driver'=>$services_driver_temp, 'price_class_all'=>$price_class_all, 'bodytype_all'=>$bodytype_all, 'driver_commissions'=>$driver_commissions, 'new_fine'=>$new_fine, 'payments_history'=>$payments_history, 'user_status'=>$user_status,
		));
	}

	public function actionDelete($id)
	{	
		$user = Users::model()->findByPk($id);
		
		$orders = Orders::model()->findAllByAttributes(array('id_driver' => $id));
		if(!empty($orders) || (!empty($user) && $user->id_creator != Yii::app()->user->id) ) {
			echo(0); exit;
		}
		if($user->delete()) {
			//$car = Cars::model()->findByPk($user->id_car);
			//$car->delete();
			
			UserStatus::model()->deleteAll('id_user = ?' , array($user->id));
			$car = Cars::model()->findByPk($user->id_car);
			$car->delete();
			DriverService::model()->deleteAll('id_driver = ?' , array($user->id));
			DriverCommission::model()->deleteAll('id_driver = ?' , array($user->id));
			PaymentsHistory::model()->deleteAll('id_user = ?' , array($user->id));
			echo(1);
		} else {
			echo(0);
		}
		//$this->redirect(array('index'));
		
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
	
	
	public function actionDrivers_map()
    {	
		$criteria=new CDbCriteria();
		$criteria->mergeWith(array(
			'join'=>'INNER JOIN users driver ON driver.id = t.id_user',
			'condition'=>'driver.id_type = 1'
		));
		$criteria->addCondition("id_status != 3 AND moderation != 0");
		$drivers = UserStatus::model()->findAll($criteria);
		if(isset($_POST['ajax'])) {
			$this->layout = false;
			$drivers_array = array();
			if(!empty($drivers)) {
				foreach($drivers as $idx => $dr) {
					$drivers_array[$idx] = $dr->getAttributes();
					$drivers_array[$idx]['phone'] = $dr->user->phone;
					$drivers_array[$idx]['car'] = $dr->user->car->getAttributes();
				}
			} 
			echo json_encode($drivers_array); exit;
		}
		$this->render('drivers_map',array(
			'drivers'=>$drivers,
		));
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
