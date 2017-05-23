<?php

class UsersController extends Controller
{
    public $layout='//layouts/column1';

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
        } else {
            $customer = $this->loadModel($id);
        }
		/*
        if($customer->id_type == 3) {        //
            $this->redirect(array('index')); //если тип юзера  = администратор то запрещаем его редактирование
        } 
		*/
        if(isset($_POST['Users'])) {
			$customer->SetProperties($_POST['Users']);
            
            if($customer->save()) {
				Yii::app()->user->setFlash('success','Данные были успешно сохранены.');
				$this->redirect(array('update','id'=>$customer->id));
			}
        }
		$criteria = new CDbCriteria();
        if (Yii::app()->user->checkAccess('7')){
		   $criteria->addCondition("id > 2");
        }else{
           $criteria->addCondition("id > 2 and id < 7");
        }
		$list_types = CHtml::listData(UserTypes::model()->findAll($criteria), 'id', 'name');
		
		$this->layout = false;
        $this->render('update',array('customer'=>$customer, 'list_types'=>$list_types, 'id'=>$id));
    }

    public function actionDelete($id)
    {
		$user = Users::model()->findByPk($id);
		
		$orders = Orders::model()->findAllByAttributes(array('id_creator' => $id));
		$drivers = Users::model()->findAllByAttributes(array('id_creator' => $id));
		if(!empty($orders) || !empty($drivers)) {
			echo(0); exit;
		}
		if($user->delete()) {
			echo(1);
		} else {
			echo(0);
		}
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {
		$criteria=new CDbCriteria();
		$criteria->condition = 'id_type != 1 and id_type != 2';

		$criteria->order = 'id DESC';
		$count=Users::model()->count($criteria);
		$pages=new CPagination($count);
		
		$pages->pageSize = 15;
		$pages->applyLimit($criteria);
		
		$users=Users::model()->findAll($criteria);
		
		$this->render('index',array(
			'users'=>$users, 'pages'=>$pages,
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

}