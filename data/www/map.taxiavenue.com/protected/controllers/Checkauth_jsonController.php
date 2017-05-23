<?php

class Checkauth_jsonController extends Controller
{
    public $layout='//layouts/none';
	public function actionIndex()
	{
        $hash=Yii::app()->request->getPost('hash');
        if ($hash==Yii::app()->session->getSessionID()){
            $result = 'success';
        }
        if(!Yii::app()->session->getSessionID()){
            $new = new updateData();
            $result = array('error'=>array('error_code'=>2,'error_msg'=>'Токена больше нет'));
        }
        else{
            $new = new updateData();
            $result = array('error'=>array('error_code'=>1,'error_msg'=>$new::ERROR_AUTH));
        }
        $res = array('response'=>$result);
        $res_encode=json_encode($res);
        $this->render('index',array(
            'data'=>$res_encode
        ));
		$this->render('index');
	}

}