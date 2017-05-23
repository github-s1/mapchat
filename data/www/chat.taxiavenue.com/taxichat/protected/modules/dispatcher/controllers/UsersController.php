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
                'roles'=>array('4'),
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
        if($customer->id_type == 3) {        //
            $this->redirect(array('index')); //если тип юзера  = администратор то запрещаем его редактирование
        }                                   //
        if(isset($_POST['Users'])) {
            $customer->rememberPhoto();
			$customer->attributes = $_POST['Users'];
            if(!empty($_POST['Users']['password_new'])) {
                $customer->password = $_POST['Users']['password_new'];
				$customer->password_old = $customer->password;
            }

            if($customer->validate()) {
                if(!empty($_FILES['Users']['name']['photo']))
                    $customer->photo = $_FILES['Users']['name']['photo'];
                if($customer->save()) {
                    if($id == 0)
                        $customer->save();
                    $this->redirect(array('update','id'=>$customer->id));
                }
            }
        }
        $this->render('update',array('customer'=>$customer, 'id'=>$id));
    }

    public function actionDelete($id)
    {
        $user = Users::model()->findByPk($id);
        $user->delete();
        $this->redirect(array('index'));
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {
		$criteria=new CDbCriteria();
		$criteria->condition = 'id_type = 3 or id_type = 4';

		$criteria->order = 'id DESC';
		$count=Users::model()->count($criteria);
		$pages=new CPagination($count);

		$pages->pageSize=15;
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
