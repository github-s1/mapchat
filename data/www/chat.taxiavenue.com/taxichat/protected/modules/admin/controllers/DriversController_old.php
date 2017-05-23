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
				'actions'=>array('index', 'create','update','delete', 'delete_commission', 'create_review', 'activate'),
				'roles'=>array('3'),
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
		if(!empty($_GET['driver'])) {
			$criteria->mergeWith(array(
				'join'=>'INNER JOIN users driver ON driver.id = t.id_driver',
				'condition'=>'driver.phone LIKE :driver OR driver.name LIKE :driver OR driver.surname LIKE :driver OR driver.patronymic LIKE :driver OR driver.email LIKE :driver OR driver.nickname LIKE :driver','params'=>array(':driver'=>'%'.$_GET['driver'].'%')
			));
		}
		$criteria->order = 'is_moderation ASC, id DESC';
		$count=UserStatus::model()->count($criteria);
		$pages=new CPagination($count);
		
		$pages->pageSize=15;
		$pages->applyLimit($criteria);
		
		$drivers=UserStatus::model()->findAll($criteria);
		
		$this->render('index',array(
			'drivers'=>$drivers, 'pages'=>$pages,
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
			$payments_history = PaymentsHistory::model()->findAllByAttributes(array('id_driver' => $driver->id));
			$user_status = UserStatus::model()->findByAttributes(array('id_driver' => $driver->id));
		}	
		
		$new_fine = new PaymentsHistory;
		
		$services_all = CHtml::listData(Services::model()->findAllByAttributes(array('is_driver' => 1)), 'id', 'name');
		$services_driver = CHtml::listData(DriverService::model()->findAllByAttributes(array('id_driver' => $driver->id)), 'id_service', 'id_service');
		
		$price_class_all = CHtml::listData(PriceClass::model()->findAll(), 'id', 'name');
		
		$driver_commissions = array();
		$reviews_driver = array();
		if($id != 0) {
			$driver_commissions = DriverCommission::model()->findAllByAttributes(array('id_driver' => $driver->id));
			$reviews_driver = DriverReviews::model()->findAllByAttributes(array('id_driver' => $driver->id));
			//print_r($driver_commissions); exit;
		}
		
		
		if(isset($_POST['Users']))
		{	
			$driver->password_old = $driver->password;
			$driver->attributes = $_POST['Users'];
			if($id != 0)
				$driver->image_old = $driver->photo;
			$driver->id_type = 1;	
			/*
			if(!empty($driver->password))
				$driver->password = crypt($driver->password);	
			*/
			if($driver->validate()) {
		
				if(isset($_POST['Cars'])) {
					if(empty($_POST['Cars']['id']))
						unset($_POST['Cars']['id']);
					if($id != 0) 	
						$car->image_old = array('1' => $car->photo1, '2' => $car->photo2, '3' => $car->photo3, '4' => $car->photo4, '5' => $car->photo5, '6' => $car->photo6, '7' => $car->photo7);
					
					unset($_POST['Cars']['photo1']);
					unset($_POST['Cars']['photo2']);
					unset($_POST['Cars']['photo3']);
					unset($_POST['Cars']['photo4']);
					unset($_POST['Cars']['photo5']);
					unset($_POST['Cars']['photo6']);
					unset($_POST['Cars']['photo7']);
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
					
					$car->attributes = $_POST['Cars'];
					
					if($car->save()) {
						if(empty($_POST['Cars']['id']))
							$car->save();
						$driver->id_car = $car->id;
						
						if(!empty($_FILES['Users']['name']['photo']))
							$driver->photo = $_FILES['Users']['name']['photo'];
						if($driver->save()) {
							if($id == 0) {
								$driver->password_old = $driver->password;
								$driver->save();
								$user_status = new UserStatus;
								$user_status->id_status = 4;
								$user_status->id_driver = $driver->id;
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
								$new_fine->id_driver = $driver->id;
								$new_fine->id_type = 4;
								$val = $driver->balance - $new_fine->value;
								$new_fine->balance = $val;
								$new_fine->save();
								$driver->balance = $val;
								$driver->save();
							}
								
							$this->redirect(array('update','id'=>$driver->id));		
						}
								
							
					}
					
				}
			}
		}
		$this->render('update',array(
			'driver'=>$driver, 'car'=>$car, 'id'=>$id, 'services_all'=>$services_all, 'services_driver'=>$services_driver, 'price_class_all'=>$price_class_all, 'driver_commissions'=>$driver_commissions, 'new_fine'=>$new_fine, 'payments_history'=>$payments_history, 'reviews_driver'=>$reviews_driver, 'user_status'=>$user_status,
		));
	}
	
	public function actionDelete($id)
	{	
		
		$user = Users::model()->findByPk($id);
		
		if($user->delete()) {
			//$car = Cars::model()->findByPk($user->id_car);
			//$car->delete();
			
			UserStatus::model()->deleteAll('id_driver = ?' , array($user->id));
			$car = Cars::model()->findByPk($user->id_car);
			$car->delete();
			DriverService::model()->deleteAll('id_driver = ?' , array($user->id));
			DriverCommission::model()->deleteAll('id_driver = ?' , array($user->id));
			PaymentsHistory::model()->deleteAll('id_driver = ?' , array($user->id));
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
		$drivers = CHtml::listData(Users::model()->findAllByAttributes(array('id_type' => 2)), 'id', 'phone'); 
        if(isset($_POST['DriverReviews'])) {
            $review->attributes = $_POST['DriverReviews'];
			if($review->save())
				$this->redirect(array('update','id'=>$review->driver->id));
        }
		$this->render('review_create',array(
			'review'=>$review, 'drivers' => $drivers, 'drivers' => $drivers,
		));
    }
	
	public function actionActivate($id)
    {	
        $driver_status = UserStatus::model()->findByAttributes(array('id_driver' => $id));
		$driver_status->id_status = 4;
		$driver_status->is_moderation = true;
		if($driver_status->save())
			$this->redirect(array('update','id'=>$driver_status->id_driver));
    }
	
	public function loadModel($id)
	{
		$user=Users::model()->findByPk($id);
		if($user===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $user;
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