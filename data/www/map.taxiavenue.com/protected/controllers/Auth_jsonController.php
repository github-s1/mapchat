<?php

class Auth_jsonController extends Controller
{
    public $layout='//layouts/none';
	
	public function actionIndex()
	{
        $update = new updateData();
        if (!isset(Yii::app()->user->id)){
            $login = Yii::app()->request->getPost('login');
            $pass = Yii::app()->request->getPost('pass');
            //$login = $_GET['login'];
            //$pass = $_GET['pass'];
            if((isset($pass))&&(isset($login))){
                // получаем данные от пользователя
                $identity=new UserIdentity($login,$pass);
                if($identity->authenticate()){
                    $duration = 3600 * 24 * 7;
                    Yii::app()->user->login($identity, $duration);
                    $result = $this -> GetUserById(Yii::app()->user->id);
                    //$result['id_user']=Yii::app()->user->id;
                    //$result['hash'] = Yii::app()->getSession()->getSessionId();
                }
                else
                    $result = array('error'=>array('error_code'=>3,'error_msg'=>$update::ERROR_AUTH_DATA));
            }
            else {
                $result = array('error'=>array('error_code'=>3,'error_msg'=>$update::ERROR_AUTH_DATA));
            }
        }
        else
            $result = array('error'=>array('error_code'=>3,'error_msg'=>$update::ERROR_AUTH_DATA));
        $res = array('response'=>$result);
        $res_encode = json_encode($res);
        $this->render('index',array(
            'data'=>$res_encode,
        ));
	}

    public function actionAuthMobile()
    {
        $update = new updateData();
        if (!isset(Yii::app()->user->id)){
            $login = Yii::app()->request->getPost('login');
            $pass = Yii::app()->request->getPost('pass');
            //$login = $_GET['login'];
            //$pass = $_GET['pass'];
            if((isset($pass))&&(isset($login))){
                // получаем данные от пользователя
                $identity=new UserIdentity($login,$pass);
                if($identity->authenticate()){
					$duration = 3600 * 10 * 30;
                    Yii::app()->user->login($identity, $duration);
                    $result['id_user']=Yii::app()->user->id;
                    $result['hash'] = Yii::app()->getSession()->getSessionId();
                }
                else
                    $result = array('error'=>array('error_code'=>3,'error_msg'=>$update::ERROR_AUTH_DATA));
            }
            else {
                $result = array('error'=>array('error_code'=>3,'error_msg'=>$update::ERROR_AUTH_DATA));
            }
        }
        else {
            $result = array('error'=>array('error_code'=>3,'error_msg'=>$update::ERROR_AUTH_DATA));
		}	
        $res = array('response'=>$result);
        $res_encode = json_encode($res);
        $this->render('index',array(
            'data'=>$res_encode,
        ));
    }


    // Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}