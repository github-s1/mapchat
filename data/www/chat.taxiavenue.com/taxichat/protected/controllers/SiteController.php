<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	
	public $layout='//layouts/login';
	 
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{	
		//print_r(Yii::app()->user->id); exit;
		if(Yii::app()->user->checkAccess('3')){
			$this->redirect(array('admin/orders/order_archive'));
		} elseif(Yii::app()->user->checkAccess('4')){
			$this->redirect(array('dispatcher/orders/index'));
		} elseif(Yii::app()->user->checkAccess('2')){
			$this->redirect(array('customer_application/'));
		}elseif(Yii::app()->user->checkAccess('5')){
            $this->redirect(array('dispatcher/orders/index'));
		}elseif(Yii::app()->user->checkAccess('6') || Yii::app()->user->checkAccess('8')){
            $this->redirect(array('agent/orders/index'));
		}elseif(Yii::app()->user->checkAccess('7')){
            $this->redirect(array('admin/orders/index'));
		}else{
			$this->redirect(array('/site/login'));
		}
		/*
		if(!Yii::app()->user->isGuest) {
			echo('asdads'); exit;
		} else {
			$this->redirect(array('/site/login'));
		}
		*/
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-Type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{	
		$model=new LoginForm;
		
		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{	
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{	
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			//if($model->validate() && $model->login())
			if($model->validate() && $model->login()) {
				$this->redirect(Yii::app()->user->returnUrl);
			}	
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Users::logout();
		$this->redirect(Yii::app()->homeUrl);
	}
}