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
				'actions'=>array('view', 'MainCounter', 'makeForcedRequest', 'FinishOrder', 'CloseOrder', 'NearestDrivers', 'CustomerCanceled', 'DriverCancel', 'Finished_orders', 'New_ordersOp', 'Run_ordersOp', 'Exp_ordersOp', 'Pre_ordersOp', 'Term_ordersOp', 'Taken_ordersOp', 'Get_driver', 'Get_customer', 'GetMarkers', 'index','New_orders',  'Pre_orders', 'Term_orders','Run_orders', 'Exp_orders', 'Taken_orders', 'order_archive', 'create','update','delete','delete_point', 'new_route'),
				'roles'=>array('4', '5', '3', '6'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('view', 'index', 'create','delete_point', 'new_route','Socket'),
				'roles'=>array('5'),
			),
                array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions'=>array('Get_points'),
                'roles'=>array('4', '5', '3', '6', '7'),
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
        if ($id != 0){
            $waypts_new_array= array(); $waypts_old_array= array(); 
            if (empty($waypts_old_array)){
                $criteria=new CDbCriteria;
                $criteria->select='id_adress';  // выбираем только поле 'title'
                $criteria->condition='id_order=:ID';
                $criteria->params=array(':ID'=>$id);
                $old_waypts = OrdersPoints::model()->findAll($criteria);
                foreach ($old_waypts as $waypoint) {
                    $waypts_old_array[] = $waypoint->id_adress;
                }
            }
        }
        if($id == 0) {
            $order = new Orders;
            $order_points = array();
        } else {
            $old = Orders::model()->findByPk($id);
            $order = $this->loadModel($id);
            $order_points = OrdersPoints::model()->findAllByAttributes(array('id_order' => $order->id), array('order'=>'id ASC'));
            $orderLog = OrdersChanges::model()->findAllByAttributes(array('order_id' => $id), array('order'=>'date DESC'));
        }
        $customer ="";
        if ($order->id_customer != 0 && !empty($order->id_customer))
        {
          $customer = Users::model()->findByPk($order->id_customer);
          $customer = $customer->phone;
        }
        $drivers_all = CHtml::listData(Users::model()->findAllByAttributes(array('id_type' => 1)), 'id', 'name');
 
        $price_class_all = CHtml::listData(PriceClass::model()->findAll(), 'id', 'name');
        $statuses_all = CHtml::listData(OrderStatuses::model()->findAll(), 'id', 'name');
        $services_all = CHtml::listData(Services::model()->findAll(), 'id', 'name');
        $services_order = CHtml::listData(OrderService::model()->findAllByAttributes(array('id_order' => $order->id)), 'id_service', 'id_service');

        $set = Settings::model()->findAll();
        $settings = array();
        foreach($set as $i => $s) {
            $settings[$s->param] = array('value'=>$s->value, 'type'=>$s->type);
        }

        if(!empty($_POST['order_points']) || (empty($_POST['order_points']) && !empty($_POST['point_add'][0]['latitude']) && !empty($_POST['point_add'][0]['longitude']))) {

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
            if(isset($_POST['Orders'])) {

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
                    if ($id != 0){
                     $changeLog = orderLog::ChangesLog($order, $old,$id);
                    }
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
                    if ($id != 0) {
                        $new_services = array();
                        $old_services = OrderService::model()->findAllByAttributes(array('id_order' => $order->id));
                        $old_serv=Array();
                        foreach ($old_services as $serv) 
                        {
                            $old_serv[]=$serv->id_service;
                        }
                    }


                    if($id != 0) {
                        OrderService::model()->deleteAll('id_order = ?' , array($order->id));
                    }
                    if(isset($_POST['OrderService']['id']) && !empty($_POST['OrderService']['id'])) {
                        foreach($_POST['OrderService']['id'] as $service_id) {
                            $new_services[] = $service_id;
                            $service_or = new OrderService;
                            $service_or->id_order = $order->id;
                            $service_or->id_service = $service_id;
                            $service_or->save();
                        }
                    }
                    if ($id != 0){
                        $changeLog = orderLog::ServiseChange($old_serv, $new_services,$id);
                    } 
                    $where = OrdersPoints::model()->findByAttributes(array('id_order' => $order->id), array('order'=>'id DESC'), array('limit'=>1));
                    $from = OrdersPoints::model()->findByAttributes(array('id_order' => $order->id), array('order'=>'id ASC'), array('limit'=>1));
                    if(!empty($where) && !empty($from)) {
                        $order->where = $where->id;
                        $order->from = $from->id;
                        $order->save();
                    }
                    if ($id != 0){
                        $new_waypts = OrdersPoints::model()->findAll($criteria);
                        foreach ($new_waypts as $waypoint)
                        {
                            $waypts_new_array[] = $waypoint->id_adress;
                        }

                        orderLog::way_pointsChange($waypts_old_array, $waypts_new_array,$id);
                    }
                    $this->redirect(array('index'));
                }
            }
        }
        $this->layout = false;
        if (!empty($orderLog)){
            $this->render('update_new',array(
                'order'=>$order, 'id'=>$id, 'customer'=>$customer, 'drivers_all'=>$drivers_all, 'price_class_all'=>$price_class_all, 'statuses_all'=>$statuses_all, 'order_points'=>$order_points, 'settings'=>$settings, 'services_all'=>$services_all, 'services_order'=>$services_order, 'orderLog' => $orderLog
            ));
        } else {
            $this->render('update_new',array(
                'order'=>$order, 'id'=>$id, 'customer'=>$customer, 'drivers_all'=>$drivers_all, 'price_class_all'=>$price_class_all, 'statuses_all'=>$statuses_all, 'order_points'=>$order_points, 'settings'=>$settings, 'services_all'=>$services_all, 'services_order'=>$services_order
            ));
        }	
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
			OrderService::model()->deleteAll('id_order = ?' , array($order->id));
			OrdersPoints::model()->deleteAll('id_order = ?' , array($order->id));
		}
		$this->redirect(Yii::app()->request->urlReferrer);
	}

	public function actionNew_route()
	{
		$customer = $_POST['Orders']['id_customer'];

		$distance = 0;
		$price = 0;
		$price_distance = 0;
		$price_without_class = 0;

		$price = $this->recalculationPrice($_POST, $price, $distance, $price_distance, $price_without_class, $customer);
		$price = round($price, 2);
		$this->layout = false;
		$this->render('new_route',array(
			'price'=>$price, 'distance'=>$distance, 'price_distance'=>$price_distance, 'price_without_class'=>$price_without_class,
		));
	}

	public function actionIndex()
	{
            if(!empty($_POST)) {
                $parameters = '?';
                foreach ($_POST['filter'] as $k=>$v) {
                    if(!empty($v)){
                        $parameters.=$k."=".$v.'&';
                    }
                }
                $this->redirect(array('index'.$parameters));
            }
            
            $criteria=new CDbCriteria();
            if(!empty($_GET)) {
                if(!empty($_GET['driver'])) {
                    //$criteria->with = array('driver'=>array('select'=>false,'joinType'=>'INNER JOIN','condition'=>'driver.phone LIKE :driver OR driver.name LIKE :driver OR driver.surname LIKE :driver OR driver.patronymic LIKE :driver OR driver.email LIKE :driver OR driver.nickname LIKE :driver','params'=>array(':driver'=>'%'.$_GET['driver'].'%')));
                    $criteria->mergeWith(array(
                        'join'=>'INNER JOIN users driver ON driver.id = t.id_driver',
                        'condition'=>'driver.phone LIKE :driver OR driver.name LIKE :driver OR driver.surname LIKE :driver OR driver.email LIKE :driver OR driver.nickname LIKE :driver','params'=>array(':driver'=>'%'.$_GET['driver'].'%')
                    ));
                }
                if (!empty($_GET['client'])){
                    $criteria->mergeWith(array(
                        'join'=>'INNER JOIN users customer ON customer.id = t.id_customer',
                        'condition'=>'customer.phone LIKE :customer OR customer.name LIKE :customer OR customer.surname LIKE :customer OR customer.email LIKE :customer OR customer.nickname LIKE :customer','params'=>array(':customer'=>'%'.$_GET['client'].'%')
                    ));
                }
                if(!empty($_GET['date_from'])) {
                    $criteria->addCondition("order_date > '".date('Y-m-d', strtotime($_GET['date_from']))."'");
                }
                if(!empty($_GET['date_to'])) {
                    $criteria->addCondition("order_date < '".date('Y-m-d', strtotime($_GET['date_to']))."'");
                }

                if(!empty($_GET['order'])) {
                    if (is_numeric($_GET['order'])){	
                        $criteria->addCondition("t.id =".$_GET['order']);
                    }
                }
            }
            $criteria->addCondition("execution_status < 3");
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
		$finished = new CDbCriteria();
		$customerCanceled = new CDbCriteria();
		$driverCanceled = new CDbCriteria();
		if(!empty($_GET)) {
			if(!empty($_GET['driver'])) {
				//$criteria->with = array('driver'=>array('select'=>false,'joinType'=>'INNER JOIN','condition'=>'driver.phone LIKE :driver OR driver.name LIKE :driver OR driver.surname LIKE :driver OR driver.patronymic LIKE :driver OR driver.email LIKE :driver OR driver.nickname LIKE :driver','params'=>array(':driver'=>'%'.$_GET['driver'].'%')));
				$finished->mergeWith(array(
					'join'=>'INNER JOIN users driver ON driver.id = t.id_driver',
					'condition'=>'driver.phone LIKE :driver OR driver.name LIKE :driver OR driver.surname LIKE :driver OR  driver.email LIKE :driver OR driver.nickname LIKE :driver','params'=>array(':driver'=>'%'.$_GET['driver'].'%')
				));
				$customerCanceled->mergeWith(array(
					'join'=>'INNER JOIN users driver ON driver.id = t.id_driver',
					'condition'=>'driver.phone LIKE :driver OR driver.name LIKE :driver OR driver.surname LIKE :driver OR  driver.email LIKE :driver OR driver.nickname LIKE :driver','params'=>array(':driver'=>'%'.$_GET['driver'].'%')
				));
				$driverCanceled->mergeWith(array(
					'join'=>'INNER JOIN users driver ON driver.id = t.id_driver',
					'condition'=>'driver.phone LIKE :driver OR driver.name LIKE :driver OR driver.surname LIKE :driver OR  driver.email LIKE :driver OR driver.nickname LIKE :driver','params'=>array(':driver'=>'%'.$_GET['driver'].'%')
				));
			}
               if (!empty($_GET['client'])){
				$finished->mergeWith(array(
					'join'=>'INNER JOIN users customer ON customer.id = t.id_customer',
					'condition'=>'customer.phone LIKE :customer OR customer.name LIKE :customer OR customer.surname LIKE :customer OR customer.email LIKE :customer OR customer.nickname LIKE :customer','params'=>array(':customer'=>'%'.$_GET['client'].'%')
				));
				$customerCanceled->mergeWith(array(
					'join'=>'INNER JOIN users customer ON customer.id = t.id_customer',
					'condition'=>'customer.phone LIKE :customer OR customer.name LIKE :customer OR customer.surname LIKE :customer OR customer.email LIKE :customer OR customer.nickname LIKE :customer','params'=>array(':customer'=>'%'.$_GET['client'].'%')
				));
				$driverCanceled->mergeWith(array(
					'join'=>'INNER JOIN users customer ON customer.id = t.id_customer',
					'condition'=>'customer.phone LIKE :customer OR customer.name LIKE :customer OR customer.surname LIKE :customer OR customer.email LIKE :customer OR customer.nickname LIKE :customer','params'=>array(':customer'=>'%'.$_GET['client'].'%')
				));

			}

			if(!empty($_GET['date_from'])) {
				$finished->addCondition("order_date > '".date('Y-m-d', strtotime($_GET['date_from']))."'");
				$customerCanceled->addCondition("order_date > '".date('Y-m-d', strtotime($_GET['date_from']))."'");
				$driverCanceled->addCondition("order_date > '".date('Y-m-d', strtotime($_GET['date_from']))."'");
			}
			if(!empty($_GET['date_to'])) {
				$finished->addCondition("order_date < '".date('Y-m-d', strtotime($_GET['date_to']))."'");
				$customerCanceled->addCondition("order_date < '".date('Y-m-d', strtotime($_GET['date_to']))."'");
				$driverCanceled->addCondition("order_date < '".date('Y-m-d', strtotime($_GET['date_to']))."'");
			}
		}
		if(!empty($_GET['order'])) {
		//		$criteria->params = array(':id_order'=>$_GET['order']);
				$finished->addCondition("id =".$_GET['order']);
				$customerCanceled->addCondition("id =".$_GET['order']);
				$driverCanceled->addCondition("id =".$_GET['order']);
			}
		$this->render('order_archive');
	}
    
    public function actionFinished_orders()
    {
       $finished = $this->apply_filter();
       $finished->addCondition("execution_status = 3");
	   $finished->order = 'id DESC';
	   $finishedCount=Orders::model()->count($finished);

	   $pagesFinished=new CPagination($finishedCount);

	   $pagesFinished->pageSize = 15;
	   $pagesFinished->applyLimit($finished);
	   $finishedOrders = Orders::model()->findAll($finished);
	   $this->layout = false;
	   $this->render('finished_orders',array(
			'orders'=>$finishedOrders, 'pages'=>$pagesFinished,
		));	
    }
    public function actionDriverCancel()
    {
    	$driverCanceled = $this->apply_filter();
    	$driverCanceled->addCondition("execution_status = 4");
		$driverCanceled->addCondition("id_status = 9");
		$driverCanceled->order = 'id DESC';
		$driverCanceledCount=Orders::model()->count($driverCanceled);

		$pagesDriver=new CPagination($driverCanceledCount);
		$pagesDriver->pageSize = 15;
		$pagesDriver->applyLimit($driverCanceled);

		$driverCancel = Orders::model()->findAll($driverCanceled);
		$this->layout = false;
	    $this->render('driverCancel',array(
			'orders'=>$driverCancel, 'pages'=>$pagesDriver,
		));	
    }

    public function actionCustomerCanceled()
    {
    	$customerCanceled = $this->apply_filter();
    	$customerCanceled->addCondition("execution_status = 4");
        $customerCanceled->addCondition("id_status = 6 OR id_status = 7");
		$customerCanceled->order = 'id DESC';
		$CustomerCenceledCount=Orders::model()->count($customerCanceled);

		$pagesCust=new CPagination($CustomerCenceledCount);

		$pagesCust->pageSize = 15;
		$pagesCust->applyLimit($customerCanceled);

        $custCancel = Orders::model()->findAll($customerCanceled);
        $this->layout = false;
        $this->render('customerCancel',array(
			'orders'=>$custCancel, 'pages'=>$pagesCust,
		));	
    }


    public function actionMakeForcedRequest()
    {
     if (!empty($_POST['order']) and !empty($_POST['driver']))
      {      
        $order = Orders::model()->findByPk($_POST['order']);
        $driver = UserStatus::model()->findByAttributes(array('id_user' => $_POST['driver']));
        if(!empty($order) && !empty($driver))
        {
            if($driver->id_status == 1 && $order->execution_status == 1)
            {
                $order_driver = OrderDriver::model()->findByAttributes(array('id_order' => $order->id, 'id_driver' =>$_POST['driver']));
                    if(empty($order_driver))
                    {
                        $order_driver = new OrderDriver;
                    }
                    if($order_driver->adopted != 0) {
                        $order_driver->adopted = 0;
                    }
                    $order_driver->id_order = $order->id;
                    $order_driver->id_driver = $driver->id_user;
                    $order_driver->is_dispatcher_creator = 1;

                    $order_driver->save();
					
					$driver->SendPush('Вам поступил принудительный заказ.', ['push_type' => 1], true);
					echo json_encode(array('result' => 'success')); exit;
            }else{
                echo json_encode(array('result' => 'failure', 'errorName' => 'Водитель занят либо не в сети.')); exit; 
            } 
        }else{
            echo json_encode(array('result' => 'failure', 'errorName' => 'Заказ или водитель не существует.')); exit; 
        }
      }
    }
   

    public function actionGetAnswerForced()
    {
    if (!empty($_POST['order']) and !empty($_POST['driver']))
    {   
        $order = Orders::model()->findByPk($_POST['order']);
        if(!empty($order))
        {
            $order_driver = OrderDriver::model()->findByAttributes(array('id_order' => $order->id, 'id_driver' => $_POST['driver']));
            if(empty($order_driver)) 
                $order_driver = new OrderDriver;
       
            if($order_driver->adopted == 1) {
                echo json_encode(array('result' => 'success')); exit;
            }else
            {
                if($order_driver->adopted != 2) {
                    $order_driver->id_order = $order->id;
                    $order_driver->id_driver = $_POST['driver'];
                    $order_driver->adopted = 2;
                    $order_driver->save();
                    $this->FineForFailure($_POST['driver'], $order->is_preliminary);
                    echo json_encode(array('result' => 'failure', 'errorName' => 'Водитель не дал ответ.')); exit; 
                }else{
                    echo json_encode(array('result' => 'failure', 'errorName' => 'Водитель отказался.')); exit; 
                }   
            }
        }else{
            echo json_encode(array('result' => 'failure', 'errorName' => 'Заказ не существует.')); exit; 
        }
    }
    }

	public function actionDelete_point($id = null)
	{
		if($id != null ) {
			$point = OrdersPoints::model()->findByPk($id);
			$order = $point->id_order;
			$adress = $point->id_adress;
			if($point->delete()){
                if ($order!=0 && $adress!=null){
				   orderLog::way_pointsDelete($adress,$order);
				}
				echo 1;
			}else{
				echo 0;
			}
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
    
    
     
	public function OrderCreator($creator)
	{
      $user = Users::model()->findByPk($creator);
      if ($user->id_type == 2)
      {
      	return true;
      }else
      {
      	return false;
      }
	}

    public function apply_filter()
	{
		$criteria = new CDbCriteria();
		if(!empty($_GET)) {
			if(!empty($_GET['driver'])) {
				//$criteria->with = array('driver'=>array('select'=>false,'joinType'=>'INNER JOIN','condition'=>'driver.phone LIKE :driver OR driver.name LIKE :driver OR driver.surname LIKE :driver OR driver.patronymic LIKE :driver OR driver.email LIKE :driver OR driver.nickname LIKE :driver','params'=>array(':driver'=>'%'.$_GET['driver'].'%')));
				$criteria->mergeWith(array(
					'join'=>'INNER JOIN users driver ON driver.id = t.id_driver',
					'condition'=>'LOWER(driver.phone) LIKE :driver OR LOWER(driver.name) LIKE :driver OR LOWER(driver.surname) LIKE :driver OR LOWER(driver.email) LIKE :driver OR LOWER(driver.nickname) LIKE :driver',
					'params'=>array(':driver'=>'%'.mb_strtolower($_GET['driver'], 'UTF-8').'%')
				));
			}
			
			if (!empty($_GET['client'])){
				$criteria->mergeWith(array(
					'join'=>'INNER JOIN users customer ON customer.id = t.id_customer',
					'condition'=>'LOWER(customer.phone) LIKE :customer OR LOWER(customer.name) LIKE :customer OR LOWER(customer.surname) LIKE :customer OR LOWER(customer.email) LIKE :customer OR LOWER(customer.nickname) LIKE :customer',
					'params'=>array(':customer'=>'%'.mb_strtolower($_GET['client'], 'UTF-8').'%')
				));
			}
			
			if(!empty($_GET['date_from'])) {
				$criteria->addCondition("order_date > '".date('Y-m-d', strtotime($_GET['date_from']))."'");
			}
			if(!empty($_GET['date_to'])) {
				$criteria->addCondition("order_date < '".date('Y-m-d', strtotime($_GET['date_to']))."'");
			}
			
			if(!empty($_GET['order'])) {
				if (is_numeric($_GET['order'])){	
					$criteria->addCondition("t.id =".$_GET['order']);
				}
			}
		}
		return $criteria;
	}

    public function actionPre_orders()
    {
        $criteria = $this->apply_filter();
		$criteria->addCondition("execution_status = 1 AND is_preliminary = 1");
		$criteria->addCondition("order_date > '".date('Y-m-d H:i:s', strtotime("now") + 2400)."'");
		$criteria->mergeWith(array(
					'join'=>'INNER JOIN users creator ON creator.id = t.id_creator',
					'condition'=>'creator.id_type = 2'
				));
		$criteria->order = 'id DESC';
		
		$count=Orders::model()->count($criteria);
		$pages=new CPagination($count);
		
		$pages->pageSize = 1000;
		$pages->applyLimit($criteria);
		
		$orders = Orders::model()->findAll($criteria);
		$this->layout = false;
		$this->render('pre_orders',array(
			'orders'=>$orders, 'pages'=>$pages,
		));	
    }

    public function actionPre_ordersOp()
    {
        $criteriaOperators = $this->apply_filter();
		$criteriaOperators->addCondition("execution_status = 1 AND is_preliminary = 1");
		$criteriaOperators->addCondition("order_date > '".date('Y-m-d H:i:s', strtotime("now") + 2400)."'");
		$criteriaOperators->mergeWith(array(
					'join'=>'INNER JOIN users creator ON creator.id = t.id_creator',
					'condition'=>'creator.id_type <> 2'
				));
		$criteriaOperators->order = 'id DESC';
        
        $countOperators = Orders::model()->count($criteriaOperators);
        $pagesOperators = new CPagination($countOperators);
        
        $pagesOperators->pageSize = 1000;
		$pagesOperators->applyLimit($criteriaOperators);
		$ordersOperators = Orders::model()->findAll($criteriaOperators);
		$this->layout = false;
		$this->render('pre_ordersOp',array(
			'orders'=>$ordersOperators, 'pages'=>$pagesOperators,
		));	
    }

    public function actionTerm_orders()
	{	
		$criteria = $this->apply_filter();
		$criteria->addCondition("execution_status = 1 AND id_status = 1");
		$criteria->addCondition("order_date < '".date('Y-m-d H:i:s', strtotime("now") + 2400)."'");
		$criteria->mergeWith(array(
					'join'=>'INNER JOIN users creator ON creator.id = t.id_creator',
					'condition'=>'creator.id_type = 2'
				));
		$criteria->order = 'id DESC';
		
		$count=Orders::model()->count($criteria);
		$pages=new CPagination($count);
		
		$pages->pageSize = 1000;
		$pages->applyLimit($criteria);
		
		$orders = Orders::model()->findAll($criteria);

		$this->layout = false;
		$this->render('term_orders',array(
			'orders'=>$orders, 'pages'=>$pages,
		));	
	}
	public function actionTerm_ordersOp()
	{
		$criteriaOperators = $this->apply_filter();
		$criteriaOperators->addCondition("execution_status = 1 AND id_status = 1");
		$criteriaOperators->addCondition("order_date < '".date('Y-m-d H:i:s', strtotime("now") + 2400)."'");
		$criteriaOperators->mergeWith(array(
					'join'=>'INNER JOIN users creator ON creator.id = t.id_creator',
					'condition'=>'creator.id_type <> 2'
				));
		$criteriaOperators->order = 'id DESC';
        
        $countOperators = Orders::model()->count($criteriaOperators);
        $pagesOperators = new CPagination($countOperators);
        
        $pagesOperators->pageSize = 1000;
		$pagesOperators->applyLimit($criteriaOperators);
		$ordersOperators = Orders::model()->findAll($criteriaOperators);

		$this->layout = false;
		$this->render('term_ordersOp',array(
			'orders'=>$ordersOperators, 'pages'=>$pagesOperators,
		));	
	}

	public function actionTaken_orders()
	{	
		$criteria = $this->apply_filter();
		$criteria->addCondition("execution_status = 2");
		$criteria->addCondition("id_status = 2 OR id_status = 3");
		$criteria->mergeWith(array(
					'join'=>'INNER JOIN users creator ON creator.id = t.id_creator',
					'condition'=>'creator.id_type = 2'
				));
		$criteria->order = 'id DESC';
		
		$count=Orders::model()->count($criteria);
		$pages=new CPagination($count);
		
		$pages->pageSize = 1000;
		$pages->applyLimit($criteria);
		
		$orders = Orders::model()->findAll($criteria);

		$this->layout = false;
		$this->render('taken_orders',array(
			'orders'=>$orders, 'pages'=>$pages,
		));	
	}

	public function actionTaken_ordersOp()
	{
		$criteriaOperators = $this->apply_filter();
		$criteriaOperators->addCondition("execution_status = 2");
		$criteriaOperators->addCondition("id_status = 2 OR id_status = 3");
		$criteriaOperators->mergeWith(array(
					'join'=>'INNER JOIN users creator ON creator.id = t.id_creator',
					'condition'=>'creator.id_type <> 2'
				));
		$criteriaOperators->order = 'id DESC';
        
        $countOperators = Orders::model()->count($criteriaOperators);
        $pagesOperators = new CPagination($countOperators);
        
        $pagesOperators->pageSize = 1000;
		$pagesOperators->applyLimit($criteriaOperators);
		$ordersOperators = Orders::model()->findAll($criteriaOperators);
        
		
		$this->layout = false;
		$this->render('taken_ordersOp',array(
			'orders'=>$ordersOperators, 'pages'=>$pagesOperators,
		));
	}

	public function actionExp_orders()
	{	
		$criteria = $this->apply_filter();
		$criteria->addCondition("execution_status = 2");
		$criteria->addCondition("id_status = 4 OR id_status = 8");
		$criteria->mergeWith(array(
					'join'=>'INNER JOIN users creator ON creator.id = t.id_creator',
					'condition'=>'creator.id_type = 2'
				));
		$criteria->order = 'id DESC';
		
		$count=Orders::model()->count($criteria);
		$pages=new CPagination($count);
		
		$pages->pageSize = 1000;
		$pages->applyLimit($criteria);
		$orders = Orders::model()->findAll($criteria);
 
		$this->layout = false;
		$this->render('exp_orders',array(
			'orders'=>$orders, 'pages'=>$pages,
		));	
	}
    
    public function actionExp_ordersOp()
    {
    	$criteriaOperators = $this->apply_filter();
		$criteriaOperators->addCondition("execution_status = 2");
		$criteriaOperators->addCondition("id_status = 4 OR id_status = 8");
		$criteriaOperators->mergeWith(array(
					'join'=>'INNER JOIN users creator ON creator.id = t.id_creator',
					'condition'=>'creator.id_type <> 2'
				));
		$criteriaOperators->order = 'id DESC';
        
        $countOperators = Orders::model()->count($criteriaOperators);
        $pagesOperators = new CPagination($countOperators);
        
        $pagesOperators->pageSize = 1000;
		$pagesOperators->applyLimit($criteriaOperators);
		$ordersOperators = Orders::model()->findAll($criteriaOperators);
        
		
		$this->layout = false;
		$this->render('exp_ordersOp',array(
			'orders'=>$ordersOperators, 'pages'=>$pagesOperators,
		));	
    }

	public function actionRun_orders()
	{	
		//Заказы из приложения
		$criteria = $this->apply_filter();
		$criteria->addCondition("execution_status = 2 AND id_status = 5");
		$criteria->mergeWith(array(
					'join'=>'INNER JOIN users creator ON creator.id = t.id_creator',
					'condition'=>'creator.id_type = 2'
				));
		$criteria->order = 'id DESC';
		
		$count=Orders::model()->count($criteria);
		$pages=new CPagination($count);
		
		$pages->pageSize = 1000;
		$pages->applyLimit($criteria);
		$orders = Orders::model()->findAll($criteria);

		$this->layout = false;
		$this->render('run_orders',array(
			'orders'=>$orders, 'pages'=>$pages, 
		));	
	}
    
     public function actionRun_ordersOp()
     {

        $criteriaOperators = $this->apply_filter();
        $criteriaOperators->addCondition("execution_status = 2 AND id_status = 5");
		$criteriaOperators->mergeWith(array(
					'join'=>'INNER JOIN users creator ON creator.id = t.id_creator',
					'condition'=>'creator.id_type <> 2'
				));
		$criteriaOperators->order = 'id DESC';
        
        $countOperators = Orders::model()->count($criteriaOperators);
        $pagesOperators = new CPagination($countOperators);
        
        $pagesOperators->pageSize = 1000;
		$pagesOperators->applyLimit($criteriaOperators);
		$ordersOperators = Orders::model()->findAll($criteriaOperators);


		$this->layout = false;
		$this->render('run_ordersOp',array(
			'orders'=>$ordersOperators, 'pages'=>$pagesOperators, 
		));	
     }

	public function actionNew_orders()
	{	
		
		$criteria = $this->apply_filter();
		$criteria->mergeWith(array(
					'join'=>'INNER JOIN users creator ON creator.id = t.id_creator',
					'condition'=>'creator.id_type = 2'
				));
		$criteria->addCondition("execution_status = 1 AND is_preliminary = 0 AND id_status = 1");
		$criteria->addCondition("order_date > '".date('Y-m-d H:i:s', strtotime("now") - 500)."'");
		$criteria->order = 'id DESC';
		$count=Orders::model()->count($criteria);
		$pages=new CPagination($count);
		$pages->pageSize = 1000;
		$pages->applyLimit($criteria);
		
		$orders = Orders::model()->findAll($criteria);
        
		$this->layout = false;
		$this->render('new_orders',array(
			'orders'=>$orders, 'pages'=>$pages, 
		));	
	}

	public function actionNew_ordersOp()
	{
		$criteriaOperators = $this->apply_filter();
        $criteriaOperators->addCondition("execution_status = 1 AND is_preliminary = 0 AND id_status = 1");
		$criteriaOperators->addCondition("order_date > '".date('Y-m-d H:i:s', strtotime("now") + 2400)."'");
		$criteriaOperators->mergeWith(array(
					'join'=>'INNER JOIN users creator ON creator.id = t.id_creator',
					'condition'=>'creator.id_type <> 2'
				));
		$criteriaOperators->order = 'id DESC';
        
        $countOperators = Orders::model()->count($criteriaOperators);
        $pagesOperators = new CPagination($countOperators);
        
        $pagesOperators->pageSize = 1000;
		$pagesOperators->applyLimit($criteriaOperators);
		$ordersOperators = Orders::model()->findAll($criteriaOperators);

		$this->layout = false;
		$this->render('new_ordersOp',array(
			'orders'=>$ordersOperators, 'pages'=>$pagesOperators, 
		));	
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

	
    public function actionGetMarkers()
    {
	      
		   $criteria = new CDbCriteria();
		   $criteria->addCondition("id_creator = ".Yii::app()->user->id);
		   $criteria->addCondition("execution_status = 1 AND is_preliminary = 0 AND id_status = 1");
		   $criteria->addCondition("order_date > '".date('Y-m-d H:i:s', strtotime("now") + 2400)."'");
		   $criteria->addCondition("change_date > '".date('Y-m-d H:i:s', strtotime("now") - 400)."'");
		   $new_count = Orders::model()->count($criteria);
		
		   $criteria = new CDbCriteria();
		   $criteria->addCondition("id_creator = ".Yii::app()->user->id);
		   $criteria->addCondition("execution_status = 1 AND is_preliminary = 1");
		   $criteria->addCondition("order_date > '".date('Y-m-d H:i:s', strtotime("now") + 2400)."'");
		   $criteria->addCondition("change_date > '".date('Y-m-d H:i:s', strtotime("now")  - 400)."'");
		   $pre_count = Orders::model()->count($criteria);
		
		   $criteria = new CDbCriteria();
		   $criteria->addCondition("id_creator = ".Yii::app()->user->id);
		   $criteria->addCondition("execution_status = 1 AND id_status = 1");
		   $criteria->addCondition("order_date < '".date('Y-m-d H:i:s', strtotime("now") + 2400)."'");
		   $criteria->addCondition("change_date > '".date('Y-m-d H:i:s', strtotime("now")  - 400)."'");
		   $term_count = Orders::model()->count($criteria);
     	
		   $criteria = new CDbCriteria();
		   $criteria->addCondition("id_creator = ".Yii::app()->user->id);
		   $criteria->addCondition("execution_status = 2");
		   $criteria->addCondition("id_status = 2 OR id_status = 3");
		   $criteria->addCondition("change_date > '".date('Y-m-d H:i:s', strtotime("now")  - 400)."'");
		   $taken_count = Orders::model()->count($criteria);
		
		   $criteria = new CDbCriteria();
		   $criteria->addCondition("id_creator = ".Yii::app()->user->id);
		   $criteria->addCondition("execution_status = 2");
		   $criteria->addCondition("id_status = 4 OR id_status = 8");
		   $criteria->addCondition("change_date > '".date('Y-m-d H:i:s', strtotime("now")  - 400)."'");
		   $exp_count = Orders::model()->count($criteria);
		
		   $criteria = new CDbCriteria();
		   $criteria->addCondition("id_creator = ".Yii::app()->user->id);
		   $criteria->addCondition("execution_status = 2 AND id_status = 5");
		   $criteria->addCondition("change_date > '".date('Y-m-d H:i:s', strtotime("now")  - 400)."'");
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
         

    public function actionGet_driver()
	{
		if (isset($_GET['q']) && ($_GET['q'] != '')){
			$criteria=new CDbCriteria();
			$criteria->mergeWith(array(
				'join'=>'INNER JOIN users driver ON driver.id = t.id_user',
				'condition'=>'driver.id_type = 1'
			));
			$criteria->mergeWith(array(
				'join'=>'INNER JOIN users driver ON driver.id = t.id_user',
				'condition'=>'LOWER(driver.phone) LIKE :driver OR LOWER(driver.name) LIKE :driver OR LOWER(driver.surname) LIKE :driver OR LOWER(driver.email) LIKE :driver OR LOWER(driver.nickname) LIKE :driver','params'=>array(':driver'=>'%'.mb_strtolower($_GET['q'], 'UTF-8').'%')
			));
			$criteria->order = 'moderation DESC, id DESC';
			$drivers = UserStatus::model()->findAll($criteria);
			
			$drivers_array = array();
			if(!empty($drivers)) {
				foreach($drivers as $idx => $dr) {		
					echo $dr->user->phone;
					echo '|'.$dr->user->surname.' '.$dr->user->name.PHP_EOL;
				}
			} 	
		}
		$this->layout = false;
		exit;
	}

	public function actionGet_customer()
	{
       if (isset($_GET['q']) && ($_GET['q'] != '')){
       	$criteria=new CDbCriteria();
       	$criteria->mergeWith(array(
				'join'=>'INNER JOIN users customer ON customer.id = t.id_user',
				'condition'=>'customer.id_type = 2'
			));
       	$criteria->mergeWith(array(
				'join'=>'INNER JOIN users customer ON customer.id = t.id_user',
				'condition'=>'LOWER(customer.phone) LIKE :customer OR LOWER(customer.name) LIKE :customer OR LOWER(customer.surname) LIKE :customer OR LOWER(customer.email) LIKE :customer OR LOWER(customer.nickname) LIKE :customer','params'=>array(':customer'=>'%'.mb_strtolower($_GET['q'], 'UTF-8').'%')
			));
       	$criteria->order = 'moderation DESC, id DESC';
       	$customers = UserStatus::model()->findAll($criteria);

       	$customers_array = array();
          if(!empty($customers)) {
				foreach($customers as $idx => $cust) {		
					echo $cust->user->phone;
					echo '|'.$cust->user->surname.' '.$cust->user->name.PHP_EOL;
				}
			} 	
	    }
	    $this->layout = false;
		exit;
	}
    
    public function actionNearestDrivers($lat,$lng,$id)
    {
      $lat = $_GET['lat'];
      $lng = $_GET['lng'];
      $id = $_GET['id'];
      $order_driver = OrderDriver::model()->findByAttributes(array('id_order' => $id, 'adopted' => 1));
      if (empty($order_driver)){
       $free_drivers = Dispatcher::GetNearestDrivers($lat,$lng, 10);
       if (!empty($free_drivers)){
       	foreach($free_drivers as $free_driver) {
           $res[] = array('driver_id' => $free_driver->id_user, 'driver_name' => $free_driver->user->name, 'lat' => $free_driver->lat, 'lng' => $free_driver->lng); 
       	}
       	print_r(json_encode($res)); exit();
       }else{
         echo 0; exit();
       }
      }else{
      	echo 0; exit();
      }
    }

    public function actionCloseOrder($id)
    {
      $this->layout = false;
      $order = Orders::model()->findByPk($id);
      if (!empty($order))
      {
         $this->render('close', array('order' => $order ));
      }
      
    }
    
    public function actionFinishOrder($id)
    {
      $order = Orders::model()->findByPk($id);
      if (!empty($order)){
        if (!empty($_POST["finished"]))
        {
           if(!empty($_POST['downtime'])){
             $order->downtime = $_POST['downtime'];
           }else{
            $order->downtime = 0;
           }
           $order->execution_status = 3;
           $order->id_status = 11;
           if ($order->save()){
                print_r(json_encode(array("result" => "success")));
                 //отправка пуша клиенту и водителю
           }else{
                print_r(json_encode(array("result" => "error")));
           }

        }elseif(!empty($_POST['canceled']))
        {
           $order->execution_status = 4;
           $order->id_status = 6;
           $order_driver = OrderDriver::model()->findByAttributes(array("id_order" => $id));

           if (!empty($order_driver))
           {
             $order_driver->adopted = 3;
             $order_driver->save(); 
           }
           if ($order->save()){
              print_r(json_encode(array("result" => "success")));
               //отправка пуша клиенту и водителю
           }else{
               print_r(json_encode(array("result" => "error")));
           }
        }
      }
    }

    public function actionMainCounter()
    {
       $criteria = new CDbCriteria;
       $criteria->addCondition("id_creator = ".Yii::app()->user->id);
       $criteria->addCondition("execution_status = 1 AND id_status = 1");
       $criteria->addCondition("order_date < '".date('Y-m-d H:i:s', strtotime("now") + 2400)."'");
       $criteria->addCondition("change_date > '".date('Y-m-d H:i:s', strtotime("now")  - 400)."'");
       $mainCount = Orders::model()->count($criteria);
       echo json_encode(array('count' => $mainCount)); exit;
    }


}
