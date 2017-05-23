<?php

class DefaultController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	 
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view', 'type_atributes', 'admin', 'model_atributes'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{	
		$this->_edit(0);
	}

	public function actionUpdate($id)
	{	
		
		$this->_edit($id);
	}
	
	public function _edit($id = 0)
	{	
		$is_ajax = Yii::app()->request->isAjaxRequest;
		$is_create = false;
		if($id == 0) {
			$user = new Users;
			$is_create = true;
			
		} else
			$user = $this->loadModel($id);

		if(isset($_POST['Users']))
		{	
			if(isset($_POST['Cars'])) {
				if(!empty($_POST['Cars']['id']))
					$car = Cars::model()->findByPk($_POST['Cars']['id']);
				else {
					$car = new Cars;
					 unset($_POST['Cars']['id']);
				}	
				if($id != 0) 	
					$car->image_old = array('1' => $car->photo1, '2' => $car->photo2, '3' => $car->photo3, '4' => $car->photo4, '5' => $car->photo5, '6' => $car->photo6, '7' => $car->photo7);
				
				$car->attributes = $_POST['Cars'];
				if(!empty($_FILES['Cars']['name']['photo1']))
					$car->photo1 = $_FILES['Cars']['name']['photo1'];
				
				if(isset($_POST['Models']) && $_POST['Models']['is_new_model']) {
					$model_new = new Models;
					$model_new->attributes = $_POST['Models'];
					if($model_new->save()) 
						$car->model = $model_new->id;
				}
				
				$car->save(); 
				if(empty($_POST['Cars']['id']))
					$car->save();
			}
			
			$user->attributes = $_POST['Users'];
			if($id != 0)	
				$user->image_old = $user->photo;
			if(isset($car))
				$user->car = $car->id;
			if(!empty($user->password))
				$user->password = crypt($user->password);	
			if($user->validate()) {
				
				if(!empty($_FILES['Users']['name']['photo']))
					$user->photo = $_FILES['Users']['name']['photo'];
				if($user->type != 1 && $id != 0) {
					DriverService::model()->deleteAll('driver = ?' , array($user->id));
					DriverClass::model()->deleteAll('driver = ?' , array($user->id));
				}
				if($user->save()) {
					if($id == 0)
						$user->save();
					
					if(isset($_POST['DriverService']['id']) && !empty($_POST['DriverService']['id'])) {
						DriverService::model()->deleteAll('driver = ?' , array($user->id));
				
						foreach($_POST['DriverService']['id'] as $service_id) {
							$service_dr = new DriverService;
							$service_dr->driver = $user->id;
							$service_dr->service = $service_id;
							$service_dr->save();
						}	
					}
					
					if(isset($_POST['DriverClass']['id']) && !empty($_POST['DriverClass']['id'])) {
						DriverClass::model()->deleteAll('driver = ?' , array($user->id));
				
						foreach($_POST['DriverClass']['id'] as $class_id) {
							$service_dr = new DriverClass;
							$service_dr->driver = $user->id;
							$service_dr->price_class = $class_id;
							$service_dr->save();
						}	
					}
						
					$this->redirect(array('view','id'=>$user->id));		
				}
			}
		}
		/*
		$price_class = PriceClass::model()->findAll();
		$list = CHtml::listData($price_class, 'id', 'name');
		$this->render('update',array(
			'model'=>$user, 'list'=>$list,
		));
		*/
		$list_types = CHtml::listData(UserTypes::model()->findAll(), 'id', 'name');
		$this->render('update',array(
			'model'=>$user, 'list_types'=>$list_types, 'id'=>$id,
		));
	}
	
	public function actionType_atributes($id) {		
		if($id == 0) {
			$user = new Users;
			$is_create = true;
			
		} else
			$user = $this->loadModel($id);
			
		if(!empty($_POST['id_type'])) {
			$this->layout = false;
			$statuses = CHtml::listData(Statuses::model()->findAllByAttributes(array('type' => $_POST['id_type'])), 'id', 'name');
			if($_POST['id_type'] == 1) {
				$marks_all = CHtml::listData(Marks::model()->findAll(), 'id', 'name');
				//$user->user_car->model_car->marka_car
				//print_r($marks_all); exit;
				$services_all = CHtml::listData(Services::model()->findAll(), 'id', 'name');
				$services_driver = CHtml::listData(DriverService::model()->findAllByAttributes(array('driver' => $user->id)), 'service', 'service');
				
				$price_class_all = CHtml::listData(PriceClass::model()->findAll(), 'id', 'name');
				$price_class_driver = CHtml::listData(DriverClass::model()->findAllByAttributes(array('driver' => $user->id)), 'price_class', 'price_class');
				
				$this->render('type_atributes',array('model'=>$user, 'statuses'=>$statuses, 'services_all'=>$services_all, 'services_driver'=>$services_driver, 'price_class_all'=>$price_class_all, 'price_class_driver'=>$price_class_driver, 'statuses'=>$statuses, 'type'=>$_POST['id_type'], 'marks_all'=>$marks_all, 'id'=>$id,));
			} else
				$this->render('type_atributes',array('model'=>$user, 'statuses'=>$statuses, 'type'=>$_POST['id_type']));
		}	
	}
	
	public function actionModel_atributes($id) {		
		
		if($id == 0) {
			$user = new Users;
			$is_create = true;
			
		} else
			$user = $this->loadModel($id);
		
		if(!empty($_POST['id_marka'])) {
			$this->layout = false;
			
			$all_models_mark = CHtml::listData(Models::model()->findAllByAttributes(array('marka' => $_POST['id_marka'])), 'id', 'name');
			
			$bodytypes_all = CHtml::listData(Bodytypes::model()->findAll(), 'id', 'name');
			$this->render('model_atributes',array('model'=>$user, 'all_models_mark'=>$all_models_mark, 'bodytypes_all'=>$bodytypes_all, 'marka'=>$_POST['id_marka']));	
		}	
	}
	
	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{	
		$user = Users::model()->findByPk($id);
		if($user->delete()) {
			if($user->type == 1) {
				$car = Cars::model()->findByPk($user->car);
				$car->delete();
				//Cars::model()->deleteAll('id = ?' , array($user->car));
				DriverService::model()->deleteAll('driver = ?' , array($user->id));
				DriverClass::model()->deleteAll('driver = ?' , array($user->id));
			}	
		}
		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Users');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$user=new Users('search');
		$user->unsetAttributes();  // clear any default values
		if(isset($_GET['Users']))
			$user->attributes=$_GET['Users'];

		$this->render('admin',array(
			'model'=>$user,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Users the loaded model
	 * @throws CHttpException
	 */
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
	protected function performAjaxValidation($user)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='users-form')
		{
			echo CActiveForm::validate($user);
			Yii::app()->end();
		}
	}
}