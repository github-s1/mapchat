<?php

class Orders_oldController extends Controller
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
			//'postOnly + delete', // we only allow deletion via POST request
		);
	}
	
	public function accessRules()
	{
		return array(
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('view', 'index', 'order_archive', 'create','update','delete','delete_point', 'new_route'),
				'roles'=>array('3'),
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

	private function _edit($id = 0)
	{	
		//$users = Users::model()->with(array('car'=>array('select'=>false,'joinType'=>'INNER JOIN','condition'=>'car.number=:number','params'=>array(':number'=>'2323'))))->findAll();
		
		if($id == 0) {
			$order = new Orders;
			$address = new Addresses;
			$where_points = array();
		} else {
			$order = $this->loadModel($id);
			$address = Addresses::model()->findByPk($order->from);
			$where_points = WherePoints::model()->findAllByAttributes(array('id_order' => $order->id), array('order'=>'id ASC'));
		}
		$customers_all = CHtml::listData(Users::model()->findAllByAttributes(array('id_type' => 2)), 'id', 'phone');
		$drivers_all = CHtml::listData(Users::model()->findAllByAttributes(array('id_type' => 1)), 'id', 'phone');
		$price_class_all = CHtml::listData(PriceClass::model()->findAll(), 'id', 'name');
		$statuses_all = CHtml::listData(OrderStatuses::model()->findAll(), 'id', 'name');
		//$tariff_zones = TariffZones::model()->findAll(array('order'=>'id ASC'));
		$services_all = CHtml::listData(Services::model()->findAll(), 'id', 'name');
		$services_order = CHtml::listData(OrderService::model()->findAllByAttributes(array('id_order' => $order->id)), 'id_service', 'id_service');
		
		$set = Settings::model()->findAll();
		$settings = array();
		foreach($set as $i => $s) {
			$settings[$s->param] = array('value'=>$s->value, 'type'=>$s->type);
		}
		$order->price = $settings['min_order_price']['value'];
		$order->price_distance = $settings['min_order_price']['value'];
		if(!empty($_POST['Addresses']['name'])) {
			//print_r($_POST); exit;
			$this_adress = Addresses::model()->findByAttributes(array('name' => $_POST['Addresses']['name']));
			if(empty($this_adress)) {
				$this_adress = new Addresses;
				$this_adress->attributes = $_POST['Addresses'];
				$this_adress->name = htmlspecialchars($this_adress->name, ENT_QUOTES);
				$this_adress->save();
			}
			
			if(isset($_POST['Orders']))	{
				$order->attributes=$_POST['Orders'];
				
				//print_r($order->attributes); exit;
				
				$order->from = $this_adress->id;
				$order->id_creator = Yii::app()->user->id;
				
				if($order->id_price_class != 1) {
					$price_class = PriceClass::model()->findByPk($order->id_price_class);
					if($price_class->is_percent)
						$order->price += $order->price_distance * $price_class->value;
					else
						$order->price += $price_class->value;
				}
				if(isset($_POST['OrderService']['id']) && !empty($_POST['OrderService']['id'])) {
					foreach($_POST['OrderService']['id'] as $service_id) {
						$service = Services::model()->findByPk($service_id);
						if($service->is_percent)
							$order->price += $order->price_distance * $service->value;
						else
							$order->price += $service->value;
					}	
				}
				
				if(strtotime($order->order_date) - strtotime("now") >= 1690) {
					$order->is_preliminary = true;
					if($settings['preliminary']['type'] == '1')
						$order->price += $settings['preliminary']['value'] * $order->price_distance / 100;
					else
						$order->price += $settings['preliminary']['value'];
				} else
					$order->is_preliminary = false;
				
				if($order->save()) {		
					if(isset($_POST['point_add']) && count($_POST['point_add'])>0){
						foreach($_POST['point_add'] as $kda => $gdata){
							$this_adress = Addresses::model()->findByAttributes(array('name' => $gdata['name']));
							if(empty($this_adress)) {
								$this_adress = new Addresses;
								$this_adress->attributes = $gdata;
								$this_adress->name = htmlspecialchars($this_adress->name, ENT_QUOTES);
								$this_adress->save();
							}
							$this_point = new WherePoints;
							$this_point->id_order = $order->id;
							$this_point->id_adress = $this_adress->id;
							$this_point->save();
						}
					}
					
					if(isset($_POST['where_points']) && count($_POST['where_points'])>0){
						foreach($_POST['where_points'] as $kda => $gdata){
							if ($kda == 0)
								continue;
							$this_adress = Addresses::model()->findByAttributes(array('name' => $gdata['name']));
							if(empty($this_adress)) {
								$this_adress = new Addresses;
								$this_adress->attributes = $gdata;
								$this_adress->name = htmlspecialchars($this_adress->name, ENT_QUOTES);
								$this_adress->save();
							}
						
						
							$this_point = WherePoints::model()->findByPk($kda);
							$this_point->id_adress = $this_adress->id;
							$this_point->id_order = $order->id;
							$this_point->save();
						}
					}
					
					if($id != 0) {
						OrderService::model()->deleteAll('id_order = ?' , array($order->id));
					}
					if(isset($_POST['OrderService']['id']) && !empty($_POST['OrderService']['id'])) {
						foreach($_POST['OrderService']['id'] as $service_id) {
							$service_or = new OrderService;
							$service_or->id_order = $order->id;
							$service_or->id_service = $service_id;
							$service_or->save();
						}	
					}
					$where = WherePoints::model()->findByAttributes(array('id_order' => $order->id), array('order'=>'id DESC'), array('limit'=>1));
					if(!empty($where)) {
						$order->where = $where->id_adress;
						$order->save();
					}	
					$this->redirect(array('index'));
				}	
			}
		}	
		$this->render('update',array(
			'order'=>$order, 'id'=>$id, 'customers_all'=>$customers_all, 'drivers_all'=>$drivers_all, 'price_class_all'=>$price_class_all, 'statuses_all'=>$statuses_all, 'address'=>$address, 'where_points'=>$where_points, 'settings'=>$settings, 'services_all'=>$services_all, 'services_order'=>$services_order
		));
	}
	
	public function actionDelete($id)
	{
		$order = Orders::model()->findByPk($id);
		if($order->delete()) {
			OrderService::model()->deleteAll('id_order = ?' , array($order->id));
			WherePoints::model()->deleteAll('id_order = ?' , array($order->id));
		}
		$this->redirect(array('index'));
	}
	
	public function actionNew_route()
	{	
		if(!empty($_POST['Addresses']['name']) && (isset($_POST['where_points']) || isset($_POST['point_add']))) {
			$tariff_zones = TariffZones::model()->findAll(array('order'=>'id ASC'));
			
			$set = Settings::model()->findAll();
			$settings = array();
			foreach($set as $i => $s) {
				$settings[$s->param] = array('value'=>$s->value, 'type'=>$s->type);
			}
			
			$points = array();
			if(!empty($_POST['Addresses']['name'])) {
				$points[0] = $_POST['Addresses'];
			}
			if(isset($_POST['where_points'])) {
				foreach($_POST['where_points'] as $where_points) {
					$points[] = $where_points;
				}
			}		
			if(isset($_POST['point_add'])) {
				foreach($_POST['point_add'] as $point_add) {
					$points[] = $point_add;
				}
			}
			$this->layout = false;
			$this->render('new_route',array(
				'points'=>$points, 'tariff_zones'=>$tariff_zones, 'settings'=>$settings
			));
		} else {
			$this->layout = false;
			$this->render('new_route');
		}
	}
	
	public function actionIndex()
	{	
		if(!empty($_POST)) {
			$parameters = '?';
			foreach ($_POST['filter'] as $k=>$v) { 
				if(!empty($v))
					$parameters.=$k."=".$v.'&';
	        }
			$this->redirect(array('index'.$parameters)); 
		}
		$criteria=new CDbCriteria();
		if(!empty($_GET)) {
			if(!empty($_GET['driver'])) {
				//$criteria->with = array('driver'=>array('select'=>false,'joinType'=>'INNER JOIN','condition'=>'driver.phone LIKE :driver OR driver.name LIKE :driver OR driver.surname LIKE :driver OR driver.patronymic LIKE :driver OR driver.email LIKE :driver OR driver.nickname LIKE :driver','params'=>array(':driver'=>'%'.$_GET['driver'].'%')));
				$criteria->mergeWith(array(
					'join'=>'INNER JOIN users driver ON driver.id = t.id_driver',
					'condition'=>'driver.phone LIKE :driver OR driver.name LIKE :driver OR driver.surname LIKE :driver OR driver.patronymic LIKE :driver OR driver.email LIKE :driver OR driver.nickname LIKE :driver','params'=>array(':driver'=>'%'.$_GET['driver'].'%')
				));
			}
			if(!empty($_GET['date_from'])) {
				$criteria->addCondition("order_date > '".date('Y-m-d', strtotime($_GET['date_from']))."'");
			}
			if(!empty($_GET['date_to'])) {
				$criteria->addCondition("order_date < '".date('Y-m-d', strtotime($_GET['date_to']))."'");
			}
		}
		$criteria->addCondition("execution_status != 3 and execution_status != 4");
		$criteria->order = 'id DESC';
		$count=Orders::model()->count($criteria);
		$pages=new CPagination($count);
		
		$pages->pageSize=15;
		$pages->applyLimit($criteria);
		
		$orders=Orders::model()->findAll($criteria);
		
		$this->render('index',array(
			'orders'=>$orders, 'pages'=>$pages,
		));	
	}
	
	public function actionOrder_archive()
	{	
		if(!empty($_POST)) {
			$parameters = '?';
			foreach ($_POST['filter'] as $k=>$v) { 
				if(!empty($v))
					$parameters.=$k."=".$v.'&';
	        }
			$this->redirect(array('order_archive'.$parameters)); 
		}
		$criteria=new CDbCriteria();
		if(!empty($_GET)) {
			if(!empty($_GET['driver'])) {
				//$criteria->with = array('driver'=>array('select'=>false,'joinType'=>'INNER JOIN','condition'=>'driver.phone LIKE :driver OR driver.name LIKE :driver OR driver.surname LIKE :driver OR driver.patronymic LIKE :driver OR driver.email LIKE :driver OR driver.nickname LIKE :driver','params'=>array(':driver'=>'%'.$_GET['driver'].'%')));
				$criteria->mergeWith(array(
					'join'=>'INNER JOIN users driver ON driver.id = t.id_driver',
					'condition'=>'driver.phone LIKE :driver OR driver.name LIKE :driver OR driver.surname LIKE :driver OR driver.patronymic LIKE :driver OR driver.email LIKE :driver OR driver.nickname LIKE :driver','params'=>array(':driver'=>'%'.$_GET['driver'].'%')
				));
			}
			if(!empty($_GET['date_from'])) {
				$criteria->addCondition("order_date > '".date('Y-m-d', strtotime($_GET['date_from']))."'");
			}
			if(!empty($_GET['date_to'])) {
				$criteria->addCondition("order_date < '".date('Y-m-d', strtotime($_GET['date_to']))."'");
			}
		}
		$criteria->addCondition("execution_status != 3 and execution_status != 4");
		$criteria->order = 'id DESC';
		$count=Orders::model()->count($criteria);
		$pages=new CPagination($count);
		
		$pages->pageSize=15;
		$pages->applyLimit($criteria);
		
		$orders=Orders::model()->findAll($criteria);
		
		
		$this->render('order_archive',array(
			'orders'=>$orders, 'pages'=>$pages,
		));
	}
	
	public function actionDelete_point($id = null)
	{	
		if($id != null) {
			$point = WherePoints::model()->findByPk($id);
			if($point->delete())
				echo 1;
			else
				echo 0;
			exit;	
		}
	}

	public function loadModel($id)
	{	
		$model=Orders::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Orders $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='orders-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
