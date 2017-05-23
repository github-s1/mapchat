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
				'actions'=>array('order_archive',  'orderLog', 'delete','delete_point', 'new_route'),
				'roles'=>array('3', '4', '6', '7'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('Archive_update'),
				'roles'=>array('3', '4', '6', '7'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),			
		);
	}
	
	
	public function actionArchive_update($id = 0)
	{
		$car = "";
		if (Yii::app()->user->checkAccess('7'))
		{
           $render_view = 'archive_update';
		}else
		{
           $render_view = 'archive_view';
		}

		$this->layout = false;
        $order = Orders::model()->findByPk($id);
		$order_points = OrdersPoints::model()->findAllByAttributes(array('id_order' => $order->id), array('order'=>'id ASC'));
        
        $customer = Users::model()->findByPk($order->id_customer);
        
        $driver = Users::model()->findByPk($order->id_driver);

        if (!empty($driver))
        {
          $car = Cars::model()->findByPk($driver->id_car);
        }

        $status = OrderStatuses::model()->findByPk($order->id_status);
        $price_class = PriceClass::model()->findByPk($order->id_price_class);

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

		if(!empty($_POST['order_points']) || (empty($_POST['order_points']) && !empty($_POST['point_add'][0]['latitude']) && !empty($_POST['point_add'][0]['longitude']))) {
 
         

            	if(isset($_POST['Orders']))	
            	{
                $order->attributes=$_POST['Orders'];
                
                   
                $order->change_date = date('Y-m-d H:i:s', strtotime("now"));
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
		        }else{

		           	$this->render($render_view ,array(
			           'order'=>$order, 'id'=>$id, 'status' => $status, 'price_class' => $price_class, 'customer' =>$customer, 'driver' => $driver, 'customers_all'=>$customers_all, 'drivers_all'=>$drivers_all, 'price_class_all'=>$price_class_all, 'statuses_all'=>$statuses_all, 'order_points'=>$order_points, 'settings'=>$settings, 'services_all'=>$services_all, 'services_order'=>$services_order
		             ));
		        }	
		}else{
			$this->render($render_view ,array(
			'order'=>$order, 'id'=>$id, 'price_class' => $price_class, 'status' => $status,  'customer' =>$customer, 'driver' => $driver, 'customers_all'=>$customers_all, 'drivers_all'=>$drivers_all, 'price_class_all'=>$price_class_all, 'statuses_all'=>$statuses_all, 'order_points'=>$order_points, 'settings'=>$settings, 'services_all'=>$services_all, 'services_order'=>$services_order, 'car' => $car
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
		if(!empty($order)) {
			if($order->delete()) {
				OrderService::model()->deleteAll('id_order = ?' , array($id));
				OrdersPoints::model()->deleteAll('id_order = ?' , array($id));
				OrderDriver::model()->deleteAll('id_order = ?' , array($id));
				echo(1);
			} else {
				echo(0);
			}
		} else {
			echo(0);
		}	
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
	
	public function actionOrder_archive()
	{	
		$this->ApplyFilter($_POST, 'order_archive');
		
		$criteria=new CDbCriteria();
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
				$criteria->addCondition("order_date < '".date('Y-m-d', strtotime($_GET['date_to']))."'");
			}
		}
		$criteria->addCondition("execution_status > 2");
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

	public function actionOrderLog($id)
    {
       	$orderLog = OrdersChanges::model()->findAllByAttributes(array('order_id' => $id), array('order'=>'date DESC'));
       	$this->layout = false;
       	$result = array();
       	foreach ($orderLog as $log)
       	{
         $type = ChangesTypes::model()->findByPk($log->type_id);
         $values = orderLog::getActualValues($log);
         $creator = Users::model()->findByPk($log->creator_id);
         $result[] = array('type' => $type->value, 'old' => $values['old'], 'new' => $values['new'], 'date' => substr($log->date,0,19), 'creator'=>$creator->name);
        }
       	$this->render('orderlog', array('orderLog' => $result));
    }

}
