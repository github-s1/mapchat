<?php

class ZonesController extends Controller
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
				'actions'=>array('index','create','update','delete'),
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
		if($id == 0) {
			$tariff_zone = new TariffZones;
		} else {
			$tariff_zone = $this->loadModel($id);
		}

		if(isset($_POST['TariffZones']))
		{	
			$tariff_zone->attributes = $_POST['TariffZones'];
			if($tariff_zone->save()) {
				Yii::app()->user->setFlash('success','Данные были успешно сохранены.');
				$this->redirect(array('update','id'=>$tariff_zone->id));
			}	
		}
		$this->layout = false;
		$this->render('update',array(
			'tariff_zone'=>$tariff_zone, 'id'=>$id,
		));
	}
	
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
	}

	public function actionIndex()
	{
		$criteria=new CDbCriteria();
		$criteria->order = 'id DESC';
		$count=TariffZones::model()->count($criteria);
		$pages=new CPagination($count);
		
		$pages->pageSize=15;
		$pages->applyLimit($criteria);
		
		$tariff_zones = TariffZones::model()->findAll($criteria);
		
		$this->render('index',array(
			'tariff_zones'=>$tariff_zones, 'pages' => $pages,
		));
	}

	public function loadModel($id)
	{
		$model=TariffZones::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param TariffZones $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='tariff_zones-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
