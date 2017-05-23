<?php

class AddressesController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column3';

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
				'roles'=>array('4'),
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
			$model->name = htmlspecialchars($_POST['Addresses']['name'], ENT_QUOTES);
			//print_r($model->name); exit;
			if(isset($_POST['lat']) && isset($_POST['lng'])) {
				$model->latitude = $_POST['lat'];
				$model->longitude = $_POST['lng'];
			}
			if($model->save())
				$this->redirect(array('index'));
		}

		$this->render('update',array(
			'model'=>$model, 'id'=>$id,
		));
	}

	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	public function actionIndex()
	{
		$criteria=new CDbCriteria();
		$criteria->order = 'id DESC';
		$criteria->condition = "popular_name IS NOT NULL || popular_name <> ''";
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
