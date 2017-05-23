<?php

class AddressesController extends Controller
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
				'actions'=>array('index','create','update','delete', 'view'),
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
			$model=new Addresses;
		} else {
			$model=$this->loadModel($id);
		}

		if(isset($_POST['Addresses']))
		{
			$model->popular_name = $_POST['Addresses']['popular_name'];
			$model->name = htmlspecialchars($_POST['Addresses']['name'], ENT_QUOTES);
			//print_r($model->name); exit;
			if(isset($_POST['lat']) && isset($_POST['lng'])) {
				$model->latitude = $_POST['lat'];
				$model->longitude = $_POST['lng'];
			}
			if($model->save()) {
				Yii::app()->user->setFlash('success','Данные были успешно сохранены.');
				$this->redirect(array('update','id'=>$model->id));
			}	
		}
		$this->layout = false;
		$this->render('update',array(
			'model'=>$model, 'id'=>$id,
		));
	}
	
	public function actionDelete($id)
	{
		$addresses = Addresses::model()->findByPk($id);
		$criteria=new CDbCriteria();
		$criteria->addCondition("id_adress = ".$id);
		$count_points = OrdersPoints::model()->count($criteria);

		if($count_points > 0) {
			echo(0); exit;
		}
		if($addresses->delete()) {
			echo(1);
		} else {
			echo(0);
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
		$criteria->order = 'id DESC';
		$criteria->condition = "popular_name IS NOT NULL";
		if(!empty($_GET['adress'])) {
			$criteria->params=array(':adress'=>'%'.mb_strtolower($_GET['adress'], 'UTF-8').'%');
			$criteria->condition .= ' AND ( LOWER(name) LIKE :adress OR LOWER(popular_name) LIKE :adress)';
			
		}
		$count=Addresses::model()->count($criteria);
		$pages=new CPagination($count);
		
		$pages->pageSize=15;
		$pages->applyLimit($criteria);
		
		$addresses=Addresses::model()->findAll($criteria);
		
		$this->render('index',array(
			'addresses'=>$addresses, 'pages'=>$pages,
		));	
		
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Addresses the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Addresses::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Addresses $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='addresses-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
