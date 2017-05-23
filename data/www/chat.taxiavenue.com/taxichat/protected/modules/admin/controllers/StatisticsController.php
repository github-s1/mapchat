<?php



class StatisticsController extends Controller
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

				'actions'=>array('index', 'drivers', 'get_driver', 'driver_view', 'driver_info', 'payments_history', 'new_payment'),

				'roles'=>array('3', '4', '7'),

			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions

				'actions'=>array('orderChart'),

				'roles'=>array('3', '4', '7', '6'),

			),

			array('deny',  // deny all users

				'users'=>array('*'),

			),			

		);

	}

	

	public function actionGet_driver()

	{

		if (isset($_GET['q']) && ($_GET['q'] != '')){
			
			$result = Drivers::GetAllDriversByCriteria(0, $_GET['q']);
			$drivers = $result['drivers'];

			

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

	

	public function actionDriver_view()

	{

		if(isset($_POST['phone']) && ($_POST['phone'] != '')){

			$driver = Users::model()->findByAttributes(array('phone' => $_POST['phone']));

			if(!empty($driver)) {

				$this->redirect(array('driver_info','id'=>$driver->id));	

			} else {

				$this->redirect(array('drivers'));

			}

		} else {

			$this->redirect(array('drivers'));

		}	

	}

	

	public function actionDriver_info($id = null)

	{

		//print_r($_GET); exit;

		$driver = Users::model()->findByPk($id);

		if(empty($driver)) {

			$this->redirect(array('drivers'));	

		} else {
			$this->ApplyFilter($_POST, '/admin/statistics/driver_info/id/'.$id);

			$criteria=new CDbCriteria();

			if(!empty($_GET['date_from'])) {
				$date_from = $_GET['date_from'];
	 			$criteria->addCondition("order_date > '".date('Y-m-d', strtotime($_GET['date_from']))."'");
			}else{
                $time = strtotime(date("d.m.Y")) - 2629743;
			    $date_from = date("d.m.Y", $time); 
			    $criteria->addCondition("order_date > '".date('Y-m-d', strtotime($date_from))."'");
			}
			if(!empty($_GET['date_to'])) {
				$date_to = $_GET['date_to'];
				$criteria->addCondition("order_date < '".date('Y-m-d', strtotime($_GET['date_to']))."'");
			}else{
				$date_to = date("d.m.Y");
			    $criteria->addCondition("order_date < '".date('Y-m-d', strtotime($date_to))."'");
			}

			

			$criteria->addCondition("execution_status = 3 AND id_driver =".$id);

			$criteria->order = 'id DESC';

			

			$all_completed_orders = Orders::model()->findAll($criteria);

			

			$completed_count = count($all_completed_orders);

			$completed_summ = 0;

			$completed_income = 0;

			$average_cost = 0; 

			

			if($completed_count > 0) {

				foreach($all_completed_orders as $ord) {

					$completed_summ += $ord->price;

					$completed_income += $ord->income;

				}	

				$average_cost = round($completed_summ / $completed_count, 2);

			}

			

			$count = Orders::model()->count($criteria);

			$pages_orders = new CPagination($count);
			$pages_orders->pageSize = 5;
			$pages_orders->applyLimit($criteria);
			$completed_orders = Orders::model()->findAll($criteria);
			$average_commission = Settings::model()->findByAttributes(array('param' => 'average_commission'));

			$order_commission = $average_commission->value - ($driver->rating - 3);
			$this->render('driver_info',array(

				'completed_count'=>$completed_count, 'average_cost'=>$average_cost,

				'completed_summ'=>$completed_summ, 'completed_income'=>$completed_income,

				'completed_orders'=>$completed_orders, 'pages_orders'=>$pages_orders,

				'driver'=>$driver, 'id'=>$id,
				'date_from' => $date_from, 'date_to' => $date_to,

			));	

		}

	}

	

	public function actionPayments_history($id = null)
	{
		$driver = Users::model()->findByPk($id);
		
		$payment = Drivers::GetDriverPaymentsHistory($id, 5, false, $_GET);
		//print_r($payment); exit;
		$average_commission = Settings::model()->findByAttributes(array('param' => 'average_commission'));

		$order_commission = $average_commission->value - ($driver->rating - 3);

		$new_payment = new PaymentsHistory;

		$this->layout = false;

		$this->render('payments_history',array(

			'payments_driver'=>$payment['payments_driver'], 'pages_payments'=>$payment['pages'],

			'driver'=>$driver, 'order_commission'=>$order_commission, 

			'id'=>$id, 'new_payment'=>$new_payment,

		));

	}

	

	public function actionIndex()

	{	

		$this->ApplyFilter($_POST, 'index');

		$criteria=new CDbCriteria();

			if(!empty($_GET['date_from'])) {
				$date_from = $_GET['date_from'];
	 			$criteria->addCondition("order_date > '".date('Y-m-d', strtotime($_GET['date_from']))."'");
			}else{
                $time = strtotime(date("d.m.Y")) - 2629743;
			    $date_from = date("d.m.Y", $time); 
			    $criteria->addCondition("order_date > '".date('Y-m-d', strtotime($date_from))."'");
			}
			if(!empty($_GET['date_to'])) {
				$date_to = $_GET['date_to'];
				$criteria->addCondition("order_date < '".date('Y-m-d', strtotime($_GET['date_to']))."'");
			}else{
				$date_to = date("d.m.Y");
			    $criteria->addCondition("order_date < '".date('Y-m-d', strtotime($date_to))."'");
			}

		$completed_criteria = clone $criteria;

		$cancel_criteria = clone $criteria;

		$criteria->addCondition("execution_status = 3 OR execution_status = 4");

		$orders_count = Orders::model()->count($criteria);



		$completed_criteria->addCondition("execution_status = 3");

		$completed_count = Orders::model()->count($completed_criteria);

		$completed_orders = Orders::model()->findAll($completed_criteria);

		$completed_summ = 0;

		$completed_income = 0;

		$average_cost = 0; 

		$distance_summ = 0;

		$average_distance = 0; 

		if(!empty($completed_orders)) {

			foreach($completed_orders as $ord) {

				$completed_summ += $ord->price;

				$completed_income += $ord->income;

				$distance_summ += $ord->distance;

			}

			$count = count($completed_orders);

			$average_cost = round($completed_summ / $count, 2);

			$average_distance =  round($distance_summ / $count, 2);

			

		}

	

		$cancel_criteria->addCondition("execution_status = 4");

		$cancel_count = Orders::model()->count($cancel_criteria);

		$this->render('index',array(

			'completed_count'=>$completed_count,

			'completed_summ'=>$completed_summ, 'completed_income'=>$completed_income,

			'average_cost'=>$average_cost, 'average_distance'=>$average_distance,

			'cancel_count'=>$cancel_count, 'orders_count'=>$orders_count,

			'date_from' => $date_from, 'date_to' => $date_to,

		));	

	}

	

	public function actionDrivers()

	{	
		$this->ApplyFilter($_POST, 'drivers');

		$criteria=new CDbCriteria();

			if(!empty($_GET['date_from'])) {
				$date_from = $_GET['date_from'];
	 			$criteria->addCondition("order_date > '".date('Y-m-d', strtotime($_GET['date_from']))."'");
			}else{
                $time = strtotime(date("d.m.Y")) - 2629743;
			    $date_from = date("d.m.Y", $time); 
			    $criteria->addCondition("order_date > '".date('Y-m-d', strtotime($date_from))."'");
			}
			if(!empty($_GET['date_to'])) {
				$date_to = $_GET['date_to'];
				$criteria->addCondition("order_date < '".date('Y-m-d', strtotime($_GET['date_to']))."'");
			}else{
				$date_to = date("d.m.Y");
			    $criteria->addCondition("order_date < '".date('Y-m-d', strtotime($date_to))."'");
			}

		$criteria->addCondition("execution_status = 3");

		$completed_count = Orders::model()->count($criteria);

		$completed_orders = Orders::model()->findAll($criteria);

		$completed_summ = 0;

		$completed_income = 0;

		$average_cost = 0; 



		if(!empty($completed_orders)) {

			foreach($completed_orders as $ord) {

				$completed_summ += $ord->price;

				$completed_income += $ord->income;

			}

			$count = count($completed_orders);

			$average_cost = round($completed_summ / $count, 2);

		}

		$drivers_criteria=new CDbCriteria();

		$drivers_criteria->order = 'moderation DESC, id DESC';

		$count_dr = UserStatus::model()->count($drivers_criteria);

		$pages = new CPagination($count_dr);

		$pages->pageSize = 5;

		$pages->applyLimit($drivers_criteria);

		$drivers = UserStatus::model()->findAll($drivers_criteria);

		$drivers_array = array();

		if(!empty($drivers)) {

			foreach($drivers as $i => $dr) {

				$orders = Orders::model()->findAllByAttributes(array('id_driver' => $dr->id_user, 'execution_status' => 3));

				$dr_summ = 0;

				$dr_income = 0;



				if(!empty($orders)) {

					foreach($orders as $ord) {

						$dr_summ += $ord->price;

						$dr_income += $ord->income;

					}

				}

				$drivers_array[$i] = $dr->getAttributes();

				$drivers_array[$i]['phone'] = $dr->user->phone;

				$drivers_array[$i]['orders_count'] = count($orders);

				$drivers_array[$i]['orders_summ'] = $dr_summ;

				$drivers_array[$i]['orders_income'] = $dr_income;

				

			}

		}

		$this->render('drivers',array(

			'completed_count'=>$completed_count, 'average_cost'=>$average_cost,

			'completed_summ'=>$completed_summ, 'completed_income'=>$completed_income,

			'drivers'=>$drivers_array, 'pages'=>$pages,
			'date_from' => $date_from, 'date_to' => $date_to,

		));	

	}

	

	public function actionNew_payment($id)

	{

		$driver = Users::model()->findByPk($id);

		$this->layout = false;
		

		if(isset($_POST['PaymentsHistory'])) {

			if($_POST['flag'] == 1) {
				$balance = round($driver->balance + $_POST['PaymentsHistory']['value'], 2);
				PaymentsHistory::Depositing($id, 6, $balance, $driver->rating, $_POST['PaymentsHistory']['value'], $_POST['PaymentsHistory']['descr']);
			} else {		
				$balance = round($driver->balance - $_POST['PaymentsHistory']['value'], 2);
				
				PaymentsHistory::RemoveFine($id, $balance, $driver->rating, $_POST['PaymentsHistory']['value'], $_POST['PaymentsHistory']['descr']);
			}
			
			$driver->balance = $balance;
			
			
			$driver->save();
			echo 1;			

		} else {

			echo 0;

		}

		exit;

	}



    public function actionOrderChart()
    {
       $id_operation = $_GET['id_op'];
       $type = $_GET['type'];
       $variants = [1 => "price", 2 => "income", 3 => "avarage", 4 => "completed", 5 => "canceled", 6 => 'All'];
       if ($id_operation == 3){
       	$id = 1;
       }else{
       	$id =  $id_operation;
       }
       $result = Array();
       $count = Array();
       $criteria=new CDbCriteria();
			if(!empty($_GET['date_from'])) {
				$date_from = $_GET['date_from'];
	 			$criteria->addCondition("order_date > '".date('Y-m-d', strtotime($_GET['date_from']))."'");
			}else{
                $time = strtotime(date("d.m.Y")) - 2629743;
			    $date_from = date("d.m.Y", $time); 
			    $criteria->addCondition("order_date > '".date('Y-m-d', strtotime($date_from))."'");
			}
			if(!empty($_GET['date_to'])) {
				$date_to = $_GET['date_to'];
				$criteria->addCondition("order_date < '".date('Y-m-d', strtotime($_GET['date_to']))."'");
			}else{
				$date_to = date("d.m.Y");
			    $criteria->addCondition("order_date < '".date('Y-m-d', strtotime($date_to))."'");
			}
         
		if ($id_operation == 1 || $id_operation == 2 || $id_operation == 3 || $id_operation == 4 ){
		  $criteria->addCondition("execution_status = 3");
		}elseif($id_operation == 5){
			$criteria->addCondition("execution_status = 4");
		}
		if (!empty($_GET['driver'])){
			$criteria->addCondition("id_driver = " .$_GET['driver']);
		}
        $criteria = $this->agentCheck($criteria);
		$criteria->order = 'order_date';
	    $orders = Orders::model()->findAll($criteria);

        $result = $this->createHash($date_from,$date_to, $_GET['type']);
       # print_r($result);exit;
        $count = $result; 
	    foreach ($orders as $order)
	    {
           $year = substr($order->order_date, 0, 4);
           $month = substr($order->order_date, 5, 2);
           $day = substr($order->order_date, 8, 2);
            if ($type == 0){
              $date = $day.'.'.$month.'.'.$year;
            }else{
              $date = $month.'.'.$year;
            }
           if (!empty($count[$date]))
           {
           	$count[$date] += 1;
           }else{
           	$count[$date] = 1;
           }
           if (!isset($result[$date]))
           {
            $result[$date] = 0;
           }
           if ($id_operation == 1 || $id_operation == 2)
           {
             $result[$date] +=  $order->$variants[$id];
           }elseif($id_operation == 3){
             $result[$date] +=  $order->price;
           }else{
             $result[$date] = $count[$date];
           }

	    }
        
        if ($id_operation == 3)
        {
          foreach ($result as $key => $res) {
          	if ($count[$key]!=0){
          	 $result[$key] = $res / $count[$key];
          	}
          	 $result[$key] = round($result[$key], 2);
          }

        }
	    print_r(json_encode($result)); exit();

    }

    public function createHash($date_from, $date_to, $type)
    {
    
       $hash = array();
       $date_from = strtotime($date_from);
       $date_to = strtotime($date_to);
       if ($type == 0){
       	# $date_to += 2629743;
       	$date_to += 86400;
       }
       while ($date_from < $date_to)
       {
       	  if ($type == 0){
            $date_key = date("d.m.Y", $date_from);	 
       	    $hash[$date_key] = 0;
       	    $date_from += 86400;
          }elseif($type == 1){
            $date_key = date("d.m.Y", $date_from);	
            $date_key = substr($date_key, 3);
            $hash[$date_key] = 0; 
            $date_from += 2629743; 
          }
       } 
       return $hash;
    }
    
    public function agentCheck($criteria)
    {
      $user = Users::model()->findByPk(Yii::app()->user->id);
      if (!empty($user)){
      	if ($user->id_type == 6 )
        {
           $criteria->addCondition("id_creator = '".Yii::app()->user->id."'");
      	}
      }
      return $criteria;
    }




}