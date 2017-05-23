<?php

class DefaultController extends Controller
{
	public $layout='//layouts/column2';
	
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}
	
	public function accessRules()
	{
		return array(
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array(),
				'roles'=>array('3'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),			
		);
	}
	
	
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Users the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$user=Users::model()->findByPk($id);
		if($user===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $user;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Users $user the model to be validated
	 */
	protected function performAjaxValidation($user)	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='users-form')
		{
			echo CActiveForm::validate($user);
			Yii::app()->end();
		}
	}
	
}