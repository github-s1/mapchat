<?php

class Check_auth_jsonController extends Controller
{
    public $layout='//layouts/none';
	public function actionIndex()
	{			$user_id = $this->is_authentificate();		if(!empty($user_id)) {			echo json_encode(array('response' => 'success')); exit;		}		/*
        $hash=Yii::app()->request->getPost('hash');
        if ($hash==Yii::app()->session->getSessionID()){
            $result = 'success';
        }
        elseif(!Yii::app()->session->getSessionID()){
            $new = new updateData();
            $result = array('error'=>array('error_code'=>2,'error_msg'=>'Токена больше нет'));
        }
        else{
            $new = new updateData();
            $result = array('error'=>array('error_code'=>1,'error_msg'=>Yii::app()->session->getSessionID()));
        }
      
        $res = array('response'=>$result);
        $res_encode=json_encode($res);
        $this->render('index',array(
            'data'=>$res_encode
        ));
		$this->render('index');		*/
	}

}