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
				'actions'=>array('index', 'customers_all', 'Customers_permanent', 'create','update','delete', 'PermanentUsers', 'DeleteSale'),
				'roles'=>array('4','7'),
			),
			
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('BlackList', 'RemoveFromBlackList', 'AddToBlackList', 'CustomersBlacklist', 'DriversBlacklist'),
				'roles'=>array('3','4','7'),
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
		if($id == 0) {
			$customer = new Users;
			$user_status = new UserStatus;
		} else {
			$customer = $this->loadModel($id);
			$user_status = UserStatus::model()->findByAttributes(array('id_user' => $customer->id));
		}

		if(isset($_POST['Users'])) {
			$customer->password_old = $customer->password;
			$customer->rememberPhoto();

			$customer->attributes = $_POST['Users'];
			$customer->id_type = 2;
			if($customer->validate()) {
				if(!empty($_FILES['Users']['name']['photo']))
					$customer->photo = $_FILES['Users']['name']['photo'];

				if($customer->save()) {
					if($id == 0) {
						$user_status->id_status = 3;
						$user_status->moderation = 1;
						$user_status->id_user = $customer->id;
						$user_status->save();
					}
					$this->redirect(array('update','id'=>$customer->id));
				}
			}
		}

		$this->layout = false;
		$this->render('update',array('customer'=>$customer, 'id'=>$id, 'user_status'=>$user_status,));
	}

	public function actionDelete($id)
	{
		$user = Users::model()->findByPk($id);
		if($user->delete()) {
			UserStatus::model()->deleteAll('id_user = ?' , array($user->id));
		}
		$this->redirect(array('index'));
	}

	# Функции чёрного списка
    public function actionBlackList()
    {
     $this->render('blacklist');
    }

    public function actionCustomersBlacklist()
    {
    	$criteria=new CDbCriteria();
    	$criteria->addCondition('moderation = 0');
    	$criteria->mergeWith(array(
					'join'=>'INNER JOIN users customer ON customer.id = t.id_user',
					'condition'=>'customer.id_type = 2'));
    	$criteria->order = 'id DESC';

    	$count=UserStatus::model()->count($criteria);
        
        $pages=new CPagination($count);
        $pages->pageSize=5;
        $pages->applyLimit($criteria);

        $customers=UserStatus::model()->findAll($criteria);
        $this->layout = false;

        $this->render('blacklistCustomer', array('customers'=>$customers,'pages'=>$pages));

    }

    public function actionDriversBlacklist()
    {
    	$criteria=new CDbCriteria();
    	$criteria->addCondition('moderation = 0');
    	$criteria->mergeWith(array(
					'join'=>'INNER JOIN users driver ON driver.id = t.id_user',
					'condition'=>'driver.id_type = 1'));
    	$criteria->order = 'id DESC';

    	$count=UserStatus::model()->count($criteria);
        
        $pages=new CPagination($count);
        $pages->pageSize=5;
        $pages->applyLimit($criteria);

        $drivers=UserStatus::model()->findAll($criteria);
        $this->layout = false;

        $this->render('blacklistDrivers', array('drivers'=>$drivers,'pages'=>$pages));

    }


    public function actionAddToBlackList($id)
    {
    	$user = UserStatus::GetUserById($id);
		$flag = 0;
		if(!empty($user)) {
			$id_user = null;
			switch ($user->user->id_type) {
				case 1:
					$id_user = 'id_driver';
					$push_type = 16;
					break;
				case 2:
					$id_user = 'id_customer';
					$push_type = 18;
					break;
			}
			if(!empty($id_user)) {
				$orders = Orders::model()->findAllByAttributes(array($id_user => $id, 'execution_status' => 2));
				
				if(!empty($orders)) {
					echo(0); exit;
				}
			}	
			$user->ChangeStatus(0, null);
			
			$user->SendPush('Ваш профиль был забанен админом.', ['push_type' => $push_type], false);
			
			$flag = 1;
		}
		echo($flag); exit;
    }

    public function actionRemoveFromBlackList($id)
     {

		$user = UserStatus::GetUserById($id);
		$flag = 0;
    	if(!empty($user)) {
			switch ($user->user->id_type) {
				case 1:
					$push_type = 3;
					break;
				case 2:
					$push_type = 17;
					break;
			}
			
			$user->ChangeStatus(1, null);
			
			$user->SendPush('Ваш профиль был активирован админом.', ['push_type' => $push_type], false);
		
			$flag = 1;
		}
		echo($flag); exit;
     }

	/**
	 * Lists all models.
	 */
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
        
        $this->render('index');
		
	}

	public function actionCustomers_all()
	{
        $criteria=new CDbCriteria();
        if(!empty($_GET['customer'])) {
			$criteria->mergeWith(array(
				'join'=>'INNER JOIN users customer ON customer.id = t.id',
				'condition'=>'LOWER(customer.phone) LIKE :customer OR LOWER(customer.name) LIKE :customer OR LOWER(customer.surname) LIKE :customer OR LOWER(customer.email) LIKE :customer OR LOWER(customer.nickname) LIKE :customer',
				'params' => array(':customer'=>'%'.mb_strtolower($_GET['customer'], 'UTF-8').'%')
			));
			$criteria->addCondition('customer.id_type = 2');
		}else{
			$criteria->addCondition('id_type = 2');
		}
       
        $criteria->mergeWith(array(
					'join'=>'INNER JOIN user_status client ON client.id_user = t.id',
					'condition'=>'client.moderation > 0'));
        $criteria->order = 'id DESC';
 
        $count=Users::model()->count($criteria);
        
        $pages=new CPagination($count);
        $pages->pageSize=5;
        $pages->applyLimit($criteria);

        $customers=Users::model()->findAll($criteria);
        $this->layout = false;
        $this->render('customers_all',array(
			'customers'=>$customers, 'pages'=>$pages,
		));
	}

	public function actionCustomers_permanent()
	{
		$limit=PermanentSettings::model()->findByAttributes(array('id' => 1));
		$Perm_users = array();
        $criteria=new CDbCriteria();
        if(!empty($_GET['customer'])) {
			$criteria->mergeWith(array(
				'join'=>'INNER JOIN users customer ON customer.id = t.id',
				'condition'=>'LOWER(customer.phone) LIKE :customer OR LOWER(customer.name) LIKE :customer OR LOWER(customer.surname) LIKE :customer OR LOWER(customer.email) LIKE :customer OR LOWER(customer.nickname) LIKE :customer',
				'params' => array(':customer'=>'%'.mb_strtolower($_GET['customer'], 'UTF-8').'%')
			));
			$criteria->addCondition('customer.id_type = 2');
		}else{
			$criteria->addCondition('id_type = 2');
		}
		$criteria->order = 'id DESC';
		$customers=Users::model()->findAll($criteria);
        
        
        /*
        foreach ($customers as $customer) {
        	$query = Orders::model()->countByAttributes(array('id_customer'=> $customer->id, 'execution_status'=>3));
        	if ($query >= $limit->orders)
        	{
        		$Perm_users[]=$customer;
        	}
        }
        $count=count($Perm_users);
        
        $pages=new CPagination($count);
        $pages->pageSize=5;
        
        */
        
        $this->layout = false;
        $this->render('customers_perm',array(
			'customers'=>$Perm_users, 'pages'=>$pages,
		));
	}
	
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

    # Создание уникальной скидки для пользователя
    public function actionPermanentUsers($id)
    {
    $PUser = PermanentUsers::model()->findByAttributes(array('id_customer' => $id));
    $user = Users::model()->findbyPk($id);
    if (empty($PUser))
      $PUser=new PermanentUsers();
       
    if(!empty($_POST['PermanentUsers']))
    {
        $PUser->attributes=$_POST['PermanentUsers'];
        $PUser->id_customer = $id;
        if($PUser->validate() && $PUser->save())
        {
           $this->redirect(array('settings/permanent'));
        }else{
         $this->render('permanentUsers',array('model'=>$PUser));	
        }
    }
    $this->render('permanentUsers',array('model'=>$PUser));
   }


   public function actionDeleteSale($id)
   {
    $sale = PermanentUsers::model()->findbyPk($id);
    if (!empty($sale))
    	$sale->delete();

    $this->redirect(array('settings/permanent'));
   }

}
?>
