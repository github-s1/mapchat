<?php


class Users_json_mobileController extends Controller

{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.

     */
    public $layout = '//layouts/none';
    /**
     * @return array action filters
     */

    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow', // allow all users to perform 'index' and 'view' actions
                'actions' => array('UpdateUser'),
                'users' => array('*'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    
    public function actionUpdateUser()
    {
        $id_user = $this->is_authentificate();
	   // $id_user = 3;
        $id_avatar = Yii::app()->request->getPost('id_avatar');
        $name = Yii::app()->request->getPost('name');
        $family = Yii::app()->request->getPost('family');
        $sex = Yii::app()->request->getPost('sex');
        $age = Yii::app()->request->getPost('age');
        $about = Yii::app()->request->getPost('about');
        $telephone = Yii::app()->request->getPost('telephone');
        $email = Yii::app()->request->getPost('email');
        $city = Yii::app()->request->getPost('city');
		$status = Yii::app()->request->getPost('status');

        $result = UsersOperations::updateUser($id_user, $id_avatar, $name, $family, $sex, $age, $about, $telephone, $email, $city, $status);
        
        echo json_encode(array('response'=>$result));
    }
}

