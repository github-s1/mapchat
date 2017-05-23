<?php

class CustomersController extends Controller
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
				'actions'=>array('index','create','update','delete', 'activate', 'banned'),
				'roles'=>array('3', '7'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),			
		);
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
		//$is_ajax = Yii::app()->request->isAjaxRequest;
		
		$CustomerData = Customers::GetCustomerData($id);
		
		$customer = $CustomerData['customer'];
		$user_status = $CustomerData['user_status'];	
		
		if(isset($_POST['Users'])) {
			
			$customer->SetProperties($_POST['Users']);
	
			if($customer->save()) {
				if($id == 0) {
					$user_status = Customers::CreateRecord($customer->id, false);
				}	
				Yii::app()->user->setFlash('success','Данные были успешно сохранены.');
				$this->redirect(array('update','id'=>$customer->id));		
			}
		}
		$this->layout = false;
		$this->render('update',array('customer'=>$customer, 'id'=>$id, 'user_status'=>$user_status,));
	}
	
	public function actionDelete($id)
	{	
		$user = Users::model()->findByPk($id);
		if(!empty($user)) {
			$orders = Orders::model()->findAllByAttributes(array('id_customer' => $id));
			if(!empty($orders)) {
				echo(0); exit;
			}
			if($user->delete()) {
				UserStatus::model()->deleteAll('id_user = ?' , array($id));
				BonusesHistory::model()->deleteAll('id_user = ?' , array($id));
				DriverReviews::model()->deleteAll('id_customer = ?' , array($id));
				echo(1); exit;
			} 
		}
		echo(0);
	}
	
	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{	
		$this->ApplyFilter($_POST, 'index');
		
		$search = null;
		if(!empty($_GET['customer'])) {
			$search = $_GET['customer'];	
		} 
		$result = Customers::GetAllCustomersByCriteria(15, $search);
		$this->render('index',array(
			'customers'=>$result['customers'], 'pages'=>$result['pages']
		));	
	}
	
	public function actionActivate($id)
    {	
		$customer = UserStatus::GetUserById($id);
		if(!empty($customer)) {
			$customer->ChangeStatus(1, null);
			
			$customer->SendPush('Ваш профиль был активирован админом.', ['push_type' => 17], false, false);
			
			
			Yii::app()->user->setFlash('success','Клиент был успешно активирован.');
		} else {
			Yii::app()->user->setFlash('success','Не удалось активировать клиента.');
		}
		$this->redirect(array('index'));
    }
	
	public function actionBanned($id)
    {	
		$customer = UserStatus::GetUserById($id);
		
		if(!empty($customer)) {
			$orders = Orders::model()->findAllByAttributes(array('id_customer' => $id, 'execution_status' => 2));
			if(!empty($orders)) {
				Yii::app()->user->setFlash('success','Выполняется заказ клиента. На данный момент его бан не возможен.');
			} else {
				$customer->ChangeStatus(0, null);
				$customer->SendPush('Ваш профиль был забанен админом.', ['push_type' => 18], false, false);
				Yii::app()->user->setFlash('success','Клиент был успешно забанен.');
			}
			
		}
		$this->redirect(array('index'));
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