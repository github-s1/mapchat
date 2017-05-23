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
				'actions'=>array('tariffs', 'add_service', 'del_service', 'fines', 'pre_filing', 'messages', 'price_class', 'driver_evaluations'),
				'roles'=>array('4'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('permanent'),
				'roles'=>array('3','4'),
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
		if(isset($_POST['Settings']) && count($_POST['Settings'])>0){
			foreach($_POST['Settings'] as $kda => $data){
				if ($kda == 0)
					continue;
				$setting = Settings::model()->findByPk($kda);
				$setting->attributes = $data;
				$setting->save();
			}
		}

		if(isset($_POST['Services_all']) && count($_POST['Services_all'])>0){
			foreach($_POST['Services_all'] as $kda => $data){
				if ($kda == 0)
					continue;
				$service = Services::model()->findByPk($kda);
				$service->attributes = $data;
				if(!isset($data['is_driver']))
					$service->is_driver = 0;
				else
					$service->is_driver = 1;
				$service->save();
			}
		}
		$services = array();
		$services = Services::model()->findAll(array('order'=>'id ASC'));
		$base_settings = Settings::model()->findAllByAttributes(array('group' => 1), array('order'=>'sort ASC'));
		$preliminary_settings = Settings::model()->findAllByAttributes(array('group' => 2), array('order'=>'sort ASC'));
		$this->render('tariffs',array(
			'base_settings'=>$base_settings, 'preliminary_settings'=>$preliminary_settings, 'new_service'=>$new_service, 'services'=>$services,
		));
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
    

    public function actionPermanent()
    {
    $model = PermanentSettings::model()->findByPk(1);
    $specialSales = PermanentUsers::model()->findAll();
    $message = null;
    if(isset($_POST['PermanentSettings']))
    {
        $model = PermanentSettings::model()->findByPk(1);
        $model->attributes=$_POST['PermanentSettings'];
        $model->id = 1;
        if ($model->orders == null){
        	$model->orders = 1;
        }
         if ($model->value == null){
        	$model->value = 0;
        }
        if($model->validate())
        {
         $message="Данные обновлены";
         $model->save(); 
        }
    }
    $this->render('permanent',array('model'=>$model, 'message'=>$message , 'specialSales'=>$specialSales));
    }


	public function actionDel_service($id = null)
	{
		if($id != null) {
			$service = Services::model()->findByPk($id);
			if($service->delete())
				echo 1;
			else
				echo 0;
			exit;
		}
	}

}
