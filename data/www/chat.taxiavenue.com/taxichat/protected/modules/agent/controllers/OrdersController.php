<?php

class OrdersController extends Controller
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
				'actions'=>array('index', 'pre_orders', 'term_orders', 'taken_orders', 'exp_orders', 'run_orders', 'order_archive', 'create','update','delete','delete_point', 'new_route', 'get_points', 'getMarkers'),
				'roles'=>array('6', '8'),
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
			$order_points = array();
		} else {
			$order = Orders::model()->findByPk($id);
			$order_points = OrdersPoints::model()->findAllByAttributes(array('id_order' => $order->id), array('order'=>'id ASC'));
		}
		$customers_all = CHtml::listData(Users::model()->findAllByAttributes(array('id_type' => 2)), 'id', 'phone');
		$drivers_all = CHtml::listData(Users::model()->findAllByAttributes(array('id_type' => 1)), 'id', 'phone');
		$price_class_all = CHtml::listData(PriceClass::model()->findAll(), 'id', 'name');
		$statuses_all = CHtml::listData(OrderStatuses::model()->findAll(), 'id', 'name');
		//$tariff_zones = TariffZones::model()->findAll(array('order'=>'id ASC'));
		$customer ="";
        if ($order->id_customer != 0 && !empty($order->id_customer))
        {
          $customer = Users::model()->findByPk($order->id_customer);
          $customer = $customer->phone;
        }
		$services_all = CHtml::listData(Services::model()->findAll(), 'id', 'name');
		$services_order = CHtml::listData(OrderService::model()->findAllByAttributes(array('id_order' => $order->id)), 'id_service', 'id_service');
		
		$set = Settings::model()->findAll();
		$settings = array();
		foreach($set as $i => $s) {
			$settings[$s->param] = array('value'=>$s->value, 'type'=>$s->type);
		}
	
		if(!empty($_POST['order_points']) || (empty($_POST['order_points']) && !empty($_POST['point_add'][0]['latitude']) && !empty($_POST['point_add'][0]['longitude'])) && !empty($_POST['Orders'])) {
			/*
			if(!empty($_POST['order_points'][0]))
				$point = $_POST['order_points'][0];
			else
				$point = $_POST['point_add'][0];
			$this_adress = Addresses::model()->findByAttributes(array('name' => $point['name']));	
			if(empty($this_adress)) {
				$this_adress = new Addresses;
				$this_adress->attributes = $point;
				$this_adress->name = htmlspecialchars($this_adress->name, ENT_QUOTES);
				$this_adress->save();
			}
			*/
			if(isset($_POST['Orders']))	{
				/*
				$old_order = $order->getAttributes();
				$new_order = $order->getAttributes();
				print_r($this->is_identically($new_order, $old_order)); exit; 
				*/
				$order->attributes=$_POST['Orders'];
				$user = Users::model()->findByAttributes(array('phone' => trim($order->id_customer)));
                
                if (!empty($user))
                {
                  $order->id_customer = $user->id;
                }else{
                  $order->is_client_use_application = false;
                  $order->phone = $order->id_customer;
                  $order->id_customer = 0;
                }
				//print_r($order->attributes); exit;
				
				//$order->from = $this_adress->id;
				$order->id_creator = Yii::app()->user->id;
				$order->is_use_commission = 1;
				$agent_commission = AgentCommission::model()->findByAttributes(array('id_agent' => Yii::app()->user->id));
				if (!empty($agent_commission))
				{
                  $order->commission = $agent_commission->commission;
				}
				 
				/*
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
				
				if($order->is_preliminary == 1) {
					if($settings['preliminary']['type'] == '1')
						$order->price += $settings['preliminary']['value'] * $order->price_distance / 100;
					else
						$order->price += $settings['preliminary']['value'];
				}
				*/
				$order->change_date = date('Y-m-d H:i:s');
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
							$this_point = new OrdersPoints;
							$this_point->id_order = $order->id;
							$this_point->id_adress = $this_adress->id;
							$this_point->entrance = $gdata['entrance'];
							$this_point->save();
						}
					}
					
					if(isset($_POST['order_points']) && count($_POST['order_points'])>0){
						foreach($_POST['order_points'] as $kda => $gdata){
							if ($kda == 0)
								continue;
							$this_adress = Addresses::model()->findByAttributes(array('name' => $gdata['name']));
							if(empty($this_adress)) {
								$this_adress = new Addresses;
								$this_adress->attributes = $gdata;
								$this_adress->name = htmlspecialchars($this_adress->name, ENT_QUOTES);
								$this_adress->save();
							}
						
						
							$this_point = OrdersPoints::model()->findByPk($kda);
							$this_point->id_adress = $this_adress->id;
							$this_point->id_order = $order->id;
							$this_point->entrance = $gdata['entrance'];
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
					$where = OrdersPoints::model()->findByAttributes(array('id_order' => $order->id), array('order'=>'id DESC'), array('limit'=>1));
					$from = OrdersPoints::model()->findByAttributes(array('id_order' => $order->id), array('order'=>'id ASC'), array('limit'=>1));
					if(!empty($where) && !empty($from)) {
						$order->where = $where->id;
						$order->from = $from->id;
						$order->save();
					}	
					$this->redirect(array('index'));
				}	
			}
		}	
		$this->layout = false;
		$this->render('update',array(
			'order'=>$order, 'id'=>$id, 'customer'=>$customer, 'drivers_all'=>$drivers_all, 'price_class_all'=>$price_class_all, 'statuses_all'=>$statuses_all, 'order_points'=>$order_points, 'settings'=>$settings, 'services_all'=>$services_all, 'services_order'=>$services_order
		));
	}
	
	private function is_identically($arrayA, $arrayB) {
		$rez = true;
		foreach($arrayA as $key => $val) {
			if($arrayB[$key] != $val) {
				$rez = false;
			}
		}
		return $rez;
	}
	
	public function actionDelete($id)
	{
		$order = Orders::model()->findByPk($id);
		if($order->delete()) {
			OrderService::model()->deleteAll('id_order = ?' , array($id));
			OrdersPoints::model()->deleteAll('id_order = ?' , array($id));
			OrderDriver::model()->deleteAll('id_order = ?' , array($id));
		}
		$this->redirect(array('index'));
	}
	
	public function actionNew_route()
	{	
		$customer = $_POST['Orders']['id_customer'];
		
		$distance = 0;
		$price = 0;
		$price_distance = 0;
		$price_without_class = 0;
		
		$price = $this->recalculationPrice($price, $distance, $price_distance, $price_without_class, $customer);
		$price = round($price, 2);
		$this->layout = false;
		$this->render('new_route',array(
			'price'=>$price, 'distance'=>$distance, 'price_distance'=>$price_distance, 'price_without_class'=>$price_without_class,
		));
	}
	
	public function actionIndex()
	{	
		if(!empty($_POST)) {
			$parameters = '';
			foreach ($_POST['filter'] as $k=>$v) { 
				if(!empty($v))
					$parameters.='/'.$k."/".$v;
	        }
			$this->redirect(array('orders/index'.$parameters)); 	
		}
		
		$ajax = false;
		$criteria=new CDbCriteria();
		$criteria->addCondition("id_creator = ".$this->OrdersCreator(Yii::app()->user->id));
		if(!empty($_GET)) {
			if(!empty($_GET['date_from'])) {
				$criteria->addCondition("order_date > '".date('Y-m-d', strtotime($_GET['date_from']))."'");
			}
			if(!empty($_GET['date_to'])) {
				$criteria->addCondition("order_date < '".date('Y-m-d', strtotime($_GET['date_to']))."'");
			}
			
			if(!empty($_GET['order'])) {
				if (is_numeric($_GET['order'])){	
					$criteria->addCondition("id =".$_GET['order']);
				}
			}
			
			if(!empty($_GET['ajax'])) {
				$this->layout = false;
				$ajax = true;
			}
		}
		
		$criteria->addCondition("execution_status = 1 AND is_preliminary = 0 AND id_status = 1");
		$criteria->addCondition("order_date > '".date('Y-m-d H:i:s', strtotime("now") + 2400)."'");
		$criteria->order = 'id DESC';
		
		$count=Orders::model()->count($criteria);
		$pages=new CPagination($count);
		
		$pages->pageSize = 15;
		$pages->applyLimit($criteria);
		
		$orders = Orders::model()->findAll($criteria);
		
		$this->render('index',array(
			'orders'=>$orders, 'pages'=>$pages, 'ajax'=>$ajax,
		));	
	}
	
	public function actionPre_orders()
	{	
		$criteria = $this->apply_filter();
		$criteria->addCondition("execution_status = 1 AND is_preliminary = 1");
		$criteria->addCondition("order_date > '".date('Y-m-d H:i:s', strtotime("now") + 2400)."'");
		$criteria->order = 'id DESC';
		
		$count=Orders::model()->count($criteria);
		$pages=new CPagination($count);
		
		$pages->pageSize=15;
		$pages->applyLimit($criteria);
		
		$orders = Orders::model()->findAll($criteria);
		$this->layout = false;
		$this->render('pre_orders',array(
			'orders'=>$orders, 'pages'=>$pages, 
		));	
	}
	
	
	public function actionTerm_orders()
	{	
		$criteria = $this->apply_filter();
		$criteria->addCondition("execution_status = 1 AND id_status = 1");
		
		$criteria->addCondition("order_date < '".date('Y-m-d H:i:s', strtotime("now") + 2400)."'");
		$criteria->order = 'id DESC';
		
		$count=Orders::model()->count($criteria);
		$pages=new CPagination($count);
		
		$pages->pageSize=15;
		$pages->applyLimit($criteria);
		
		$orders = Orders::model()->findAll($criteria);
		$this->layout = false;
		$this->render('term_orders',array(
			'orders'=>$orders, 'pages'=>$pages, 
		));	
	}
	
	public function actionTaken_orders()
	{	
		$criteria = $this->apply_filter();
		$criteria->addCondition("execution_status = 2");
		$criteria->addCondition("id_status = 2 OR id_status = 3");
		$criteria->order = 'id DESC';
		
		$count=Orders::model()->count($criteria);
		$pages=new CPagination($count);
		
		$pages->pageSize=15;
		$pages->applyLimit($criteria);
		
		$orders = Orders::model()->findAll($criteria);
		$this->layout = false;
		$this->render('taken_orders',array(
			'orders'=>$orders, 'pages'=>$pages, 
		));	
	}
	
	public function actionExp_orders()
	{	
		$criteria = $this->apply_filter();
		$criteria->addCondition("execution_status = 2");
		$criteria->addCondition("id_status = 4 OR id_status = 8");
		$criteria->order = 'id DESC';
		
		$count=Orders::model()->count($criteria);
		$pages=new CPagination($count);
		
		$pages->pageSize=15;
		$pages->applyLimit($criteria);
		
		$orders = Orders::model()->findAll($criteria);
		$this->layout = false;
		$this->render('exp_orders',array(
			'orders'=>$orders, 'pages'=>$pages, 
		));	
	}
	
	public function actionRun_orders()
	{	
		$criteria = $this->apply_filter();
		$criteria->addCondition("execution_status = 2 AND id_status = 5");
		$criteria->order = 'id DESC';
		
		$count=Orders::model()->count($criteria);
		$pages=new CPagination($count);
		
		$pages->pageSize=15;
		$pages->applyLimit($criteria);
		
		$orders = Orders::model()->findAll($criteria);
		$this->layout = false;
		$this->render('run_orders',array(
			'orders'=>$orders, 'pages'=>$pages, 
		));	
	}
	
	public function apply_filter()
	{
		$criteria = new CDbCriteria();
		$criteria->addCondition("id_creator = ".$this->OrdersCreator(Yii::app()->user->id));
		if(!empty($_GET)) {
			if(!empty($_GET['date_from'])) {
				$criteria->addCondition("order_date > '".date('Y-m-d', strtotime($_GET['date_from']))."'");
			}
			if(!empty($_GET['date_to'])) {
				$criteria->addCondition("order_date < '".date('Y-m-d H:i:s', strtotime($_GET['date_to']) + 86399)."'");
			}
			
			if(!empty($_GET['order'])) {
				if (is_numeric($_GET['order'])){	
					$criteria->addCondition("id =".$_GET['order']);
				}
			}
		}
		return $criteria;
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
		$criteria->addCondition('id_creator ='. Yii::app()->user->id);
		if(!empty($_GET)) {
			if(!empty($_GET['driver'])) {
				//$criteria->with = array('driver'=>array('select'=>false,'joinType'=>'INNER JOIN','condition'=>'driver.phone LIKE :driver OR driver.name LIKE :driver OR driver.surname LIKE :driver OR driver.patronymic LIKE :driver OR driver.email LIKE :driver OR driver.nickname LIKE :driver','params'=>array(':driver'=>'%'.$_GET['driver'].'%')));
				$criteria->mergeWith(array(
					'join'=>'INNER JOIN users driver ON driver.id = t.id_driver',
					'condition'=>'LOWER(driver.phone) LIKE :driver OR LOWER(driver.name) LIKE :driver OR LOWER(driver.surname) LIKE :driver OR LOWER(driver.email) LIKE :driver OR LOWER(driver.nickname) LIKE :driver',
					'params'=>array(':driver'=>'%'.mb_strtolower($_GET['driver'], 'UTF-8').'%')
				));
			}
			if(!empty($_GET['date_from'])) {
				$criteria->addCondition("order_date > '".date('Y-m-d', strtotime($_GET['date_from']))."'");
			}
			if(!empty($_GET['date_to'])) {
				$criteria->addCondition("order_date < '".date('Y-m-d', strtotime($_GET['date_to']) + 86399)."'");
			}
		}
		$criteria->addCondition("execution_status != 1");
		$criteria->order = 'id DESC';
		$count=Orders::model()->count($criteria);
		$pages=new CPagination($count);
		
		$pages->pageSize=15;
		$pages->applyLimit($criteria);
		
		$orders = Orders::model()->findAll($criteria);
		
		if(!empty($orders)) {
			foreach($orders as $i => $ord) {
				$orders[$i]['review'] = DriverReviews::model()->findByAttributes(array('id_order' => $ord->id));
			}
		}
		
		$this->render('order_archive',array(
			'orders'=>$orders, 'pages'=>$pages,
		));
	}
	
	public function actionGetMarkers()
	{
		$criteria = new CDbCriteria();
		$criteria->addCondition("id_creator = ".$this->OrdersCreator(Yii::app()->user->id));
		$criteria->addCondition("execution_status = 1 AND is_preliminary = 0 AND id_status = 1");
		$criteria->addCondition("order_date > '".date('Y-m-d H:i:s', strtotime("now") + 2400)."'");
		$criteria->addCondition("change_date > '".date('Y-m-d H:i:s', strtotime("now") + 3300)."'");
		$new_count = Orders::model()->count($criteria);
		
		$criteria = new CDbCriteria();
		$criteria->addCondition("id_creator = ".$this->OrdersCreator(Yii::app()->user->id));
		$criteria->addCondition("execution_status = 1 AND is_preliminary = 1");
		$criteria->addCondition("order_date > '".date('Y-m-d H:i:s', strtotime("now") + 2400)."'");
		$criteria->addCondition("change_date > '".date('Y-m-d H:i:s', strtotime("now") + 3300)."'");
		$pre_count = Orders::model()->count($criteria);
		
		$criteria = new CDbCriteria();
		$criteria->addCondition("id_creator = ".$this->OrdersCreator(Yii::app()->user->id));
		$criteria->addCondition("execution_status = 1 AND id_status = 1");
		$criteria->addCondition("order_date < '".date('Y-m-d H:i:s', strtotime("now") + 2400)."'");
		$criteria->addCondition("change_date > '".date('Y-m-d H:i:s', strtotime("now") + 3300)."'");
		$term_count = Orders::model()->count($criteria);
     	
		$criteria = new CDbCriteria();
		$criteria->addCondition("id_creator = ".$this->OrdersCreator(Yii::app()->user->id));
		$criteria->addCondition("execution_status = 2");
		$criteria->addCondition("id_status = 2 OR id_status = 3");
		$criteria->addCondition("change_date > '".date('Y-m-d H:i:s', strtotime("now") + 3300)."'");
		$taken_count = Orders::model()->count($criteria);
		
		$criteria = new CDbCriteria();
		$criteria->addCondition("id_creator = ".$this->OrdersCreator(Yii::app()->user->id));
		$criteria->addCondition("execution_status = 2");
		$criteria->addCondition("id_status = 4 OR id_status = 8");
		$criteria->addCondition("change_date > '".date('Y-m-d H:i:s', strtotime("now") + 3300)."'");
		$exp_count = Orders::model()->count($criteria);
		
		$criteria = new CDbCriteria();
		$criteria->addCondition("id_creator = ".$this->OrdersCreator(Yii::app()->user->id));
		$criteria->addCondition("execution_status = 2 AND id_status = 5");
		$criteria->addCondition("change_date > '".date('Y-m-d H:i:s', strtotime("now") + 3300)."'");
		$run_count = Orders::model()->count($criteria);

	   	print_r(json_encode(array(
		 'new' => $new_count,
		 'run' => $run_count,
	   	 'exp'  => $exp_count,
	   	 'taken'=> $taken_count,
	   	 'term' => $term_count,
	   	 'pre'  => $pre_count )));
	   	exit;
		 
	}
	
	public function actionDelete_point($id = null)
	{	
		if($id != null) {
			$point = OrdersPoints::model()->findByPk($id);
			if($point->delete())
				echo 1;
			else
				echo 0;
			exit;	
		}
	}
	
	public function actionGet_points($id)
	{	
		
		$order_points = OrdersPoints::model()->findAllByAttributes(array('id_order' => $id), array('order'=>'id ASC'));
		if(!empty($order_points)) {
			$points = array();
			foreach($order_points as $idx => $p) {
				if(!empty($p->id_adress) && isset($p->adress)) {
					$points[$idx]['lat'] = $p->adress->latitude;
					$points[$idx]['lng'] = $p->adress->longitude;
				}
			}
			echo json_encode($points);
		} else {
			echo 0;
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

	protected function OrdersCreator($id)
	{
		$model = Users::model()->findByPk($id);
		if ($model->id_type == 6)
		{
          return $model->id;
		}else{
		  return $model->id_creator;
		}
	}

}
