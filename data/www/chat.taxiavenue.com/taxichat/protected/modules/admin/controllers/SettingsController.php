<?php
class SettingsController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column1';

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
				'actions'=>array('tariffs', 'add_service', 'del_service', 'add_tariffs_time_day', 'del_tariffs_time_day', 'add_tariffs_day_week', 'del_tariffs_day_week', 'add_tariffs_time_interval', 'del_tariffs_time_interval', 'fines', 'pre_filing', 'messages', 'price_class', 'driver_evaluations', 'restrictions_access'),
				'roles'=>array('3', '4', '7'),
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
	public function actionTariffs()
	{
		//print_r($base_settings); exit;
		$new_service = new Services;
		$tariff_time_day = new TariffTimeDay;
		$tariff_day_week = new TariffDayWeek;
		$tariff_time_interval = new TariffTimeInterval;
		
		if(isset($_POST['Settings']) && count($_POST['Settings'])>0){
			foreach($_POST['Settings'] as $kda => $data){
				if ($kda == 0)
					continue;
				$setting = Settings::model()->findByPk($kda);
				$setting->attributes = $data;
				$setting->save();	
			}
			$this->recalc_orders_value();
		}
		
		if(isset($_POST['Services_all']) && count($_POST['Services_all'])>0){
			
			//print_r($_POST['Services_all']); exit;
			foreach($_POST['Services_all'] as $kda => $data){
				if ($kda == 0)
					continue;
				$service = Services::model()->findByPk($kda);
				//$service->attributes = $data;
				if(!isset($data['is_driver']))
					$service->is_driver = 0;
				else
					$service->is_driver = 1;
				$service->value = $data['value'];	
				$service->is_percent = $data['is_percent'];
				$service->save();	
			}
		}
		$services = array();
		$services = Services::model()->findAll(array('order'=>'id ASC'));
		$base_settings = Settings::model()->findAllByAttributes(array('group' => 1), array('order'=>'sort ASC'));
		$preliminary_settings = Settings::model()->findAllByAttributes(array('group' => 2), array('order'=>'sort ASC'));
		
		$tariffs_time_day = TariffTimeDay::model()->findAll(array('order'=>'id ASC')); 
		$tariffs_day_week = TariffDayWeek::model()->findAll(array('order'=>'id ASC')); 
		$tariffs_time_interval = TariffTimeInterval::model()->findAll(array('order'=>'id ASC')); 
	
		$this->render('tariffs',array(
			'base_settings'=>$base_settings, 'preliminary_settings'=>$preliminary_settings, 'new_service'=>$new_service, 'services'=>$services,
			'tariff_time_day'=>$tariff_time_day, 'tariff_day_week'=>$tariff_day_week, 'tariff_time_interval'=>$tariff_time_interval,
			'tariffs_time_day'=>$tariffs_time_day, 'tariffs_day_week'=>$tariffs_day_week, 'tariffs_time_interval'=>$tariffs_time_interval,
		));
	}
	
	private function recalc_orders_value()
	{
		$criteria = new CDbCriteria();
		$criteria->addCondition("execution_status = 1 AND id_status = 1 AND is_preliminary = 1");
		$time = strtotime("now") + 1800;
		$criteria->addCondition("order_date > '".date('Y-m-d H:i:s', $time)."'");	
		$criteria->order = 'id DESC';
		$orders = Orders::model()->findAll($criteria);
		if(!empty($orders))
		{
			foreach($orders as $order)
			{
				$min_order_price = Settings::model()->findByAttributes(array('param' =>'min_order_price'));
				$order->calculationOrder();
				$order->save();
			}
		}
	}
	
	public function actionDriver_evaluations()
	{	
		if(isset($_POST['Evaluations']) && count($_POST['Evaluations'])>0){
			foreach($_POST['Evaluations'] as $kda => $gdata){
				if ($kda == 0)
					continue;
				$ev = Evaluations::model()->findByPk($kda);
				$ev->attributes = $gdata;
				$ev->save();	
			}
		}
		$evaluations = Evaluations::model()->findAll(array('order' => 'id ASC'));
		
		$this->render('driver_evaluations',array(
			'evaluations'=>$evaluations
		));
	}
	
	public function actionPrice_class()
	{
		if(isset($_POST['PriceClass']) && count($_POST['PriceClass'])>0){
			foreach($_POST['PriceClass'] as $kda => $gdata){
				if($kda == 0)
					continue;
				$price_class = PriceClass::model()->findByPk($kda);
				$price_class->attributes = $gdata;
				$price_class->save();	
			}
		}
		if(isset($_POST['PriceClass_new']) && count($_POST['PriceClass_new'])>0){
			foreach($_POST['PriceClass_new'] as $gdata){
				$price_class = new PriceClass;
				$price_class->attributes = $gdata;
				$price_class->save();
			}
		}
		
		$price_class = PriceClass::model()->findAll(array('order' => 'id ASC'));
		
		$this->render('price_class',array(
			'price_class'=>$price_class
		));
	}
	
	
	public function actionFines()
	{
		if(isset($_POST['Settings']) && count($_POST['Settings'])>0){
			foreach($_POST['Settings'] as $kda => $data){
				if ($kda == 0)
					continue;
				$setting = Settings::model()->findByPk($kda);
				$setting->attributes = $data;
				$setting->save();	
			}
		}
		$fines_settings = Settings::model()->findAllByAttributes(array('group' => 3), array('order'=>'sort ASC'));
		$this->render('fines',array(
			'fines_settings'=>$fines_settings,
		));
	}
	
	public function actionPre_filing()
	{
		if(isset($_POST['Settings']) && count($_POST['Settings'])>0){
			foreach($_POST['Settings'] as $kda => $data){
				if ($kda == 0)
					continue;
				$setting = Settings::model()->findByPk($kda);
				$setting->attributes = $data;
				$setting->save();	
			}
		}
		$pre_filing_settings = Settings::model()->findAllByAttributes(array('group' => 4), array('order'=>'sort ASC'));
		$this->render('pre_filing',array(
			'pre_filing_settings'=>$pre_filing_settings,
		));
	}
	
	public function actionMessages()
	{
		if(isset($_POST['Settings']) && count($_POST['Settings'])>0){
			foreach($_POST['Settings'] as $kda => $data){
				if ($kda == 0)
					continue;
				$setting = Settings::model()->findByPk($kda);
				$setting->attributes = $data;
				if(!isset($data['type']))
					$setting->type = 0;
				else
					$setting->type = 1;
				$setting->save();	
			}
		}
		$messages_settings = Settings::model()->findAllByAttributes(array('group' => 5), array('order'=>'sort ASC'));
		$this->render('messages',array(
			'messages_settings'=>$messages_settings,
		));
	}
	
	
	public function actionAdd_service()
	{
		$this->layout = false;
		$new_service = new Services;
		if(isset($_POST['Services'])){
			$new_service->attributes = $_POST['Services'];
			$new_service->save();	
		}
		$services = array();
		$services = Services::model()->findAll(array('order'=>'id ASC'));
		$this->render('add_service',array(
			'services'=>$services,
		));
	}
	
	public function actionDel_service($id = null)
	{	
		$service = Services::model()->findByPk($id);
		if(!empty($service)) {
			$criteria=new CDbCriteria();
			$criteria->addCondition("id_service = ".$id);
			$order_count = OrderService::model()->count($criteria);
			$driver_count = DriverService::model()->count($criteria);
			if($order_count > 0 || $driver_count > 0) {
				echo 0; exit;
			}
			if($service->delete()) {
				echo 1;
			} else {
				echo 0;
			}
		} else {
			echo 0;
		}	
		exit;
	}
	
	public function actionAdd_tariffs_time_day()
	{
		$this->layout = false;
		$new_tariffs_time_day = new TariffTimeDay;
		if(isset($_POST['TariffTimeDay'])){
			$new_tariffs_time_day->attributes = $_POST['TariffTimeDay'];
			$new_tariffs_time_day->save();	
		}
		$tariffs_time_day = TariffTimeDay::model()->findAll(array('order'=>'id ASC')); 
		$this->render('add_tariffs_time_day',array(
			'tariffs_time_day'=>$tariffs_time_day,
		));
	}
	
	public function actionDel_tariffs_time_day($id = null)
	{	
		if($id != null) {
			$TariffTimeDay = TariffTimeDay::model()->findByPk($id);
			if($TariffTimeDay->delete())
				echo 1;
			else
				echo 0;
			exit;	
		}
	}
	
	public function actionAdd_tariffs_day_week()
	{
		$this->layout = false;
		$new_tariff_day_week = new TariffDayWeek;
		if(isset($_POST['TariffDayWeek'])){
			$new_tariff_day_week->attributes = $_POST['TariffDayWeek'];
			$new_tariff_day_week->save();	
		}
		$tariffs_day_week = TariffDayWeek::model()->findAll(array('order'=>'id ASC')); 
		$this->render('add_tariffs_day_week',array(
			'tariffs_day_week'=>$tariffs_day_week,
		));
	}
	
	public function actionDel_tariffs_day_week($id = null)
	{	
		if($id != null) {
			$TariffDayWeek = TariffDayWeek::model()->findByPk($id);
			if($TariffDayWeek->delete())
				echo 1;
			else
				echo 0;
			exit;	
		}
	}
	
	public function actionAdd_tariffs_time_interval()
	{
		$this->layout = false;
		$new_tariffs_time_interval = new TariffTimeInterval;
		if(isset($_POST['TariffTimeInterval'])){
			$new_tariffs_time_interval->attributes = $_POST['TariffTimeInterval'];
			$new_tariffs_time_interval->save();	
		}
		$tariffs_time_interval = TariffTimeInterval::model()->findAll(array('order'=>'id ASC')); 
		$this->render('add_tariffs_time_interval',array(
			'tariffs_time_interval'=>$tariffs_time_interval,
		));
	}
	
	public function actionDel_tariffs_time_interval($id = null)
	{	
		if($id != null) {
			$TariffTimeInterval = TariffTimeInterval::model()->findByPk($id);
			if($TariffTimeInterval->delete())
				echo 1;
			else
				echo 0;
			exit;	
		}
	}
	
	public function actionRestrictions_access()
	{	
		if(isset($_POST['Settings']) && count($_POST['Settings'])>0){
			foreach($_POST['Settings'] as $kda => $data){
				if ($kda == 0)
					continue;
				$setting = Settings::model()->findByPk($kda);
				$setting->attributes = $data;
				$setting->save();	
			}
		}
		$access_settings = Settings::model()->findAllByAttributes(array('group' => 6), array('order'=>'sort ASC'));
		$this->render('restrictions_access',array(
			'access_settings'=>$access_settings,
		));
		
	}

}
