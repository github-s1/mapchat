<?php

class Confirmcode_jsonController extends Controller
{
    public $layout='//layouts/none';
    public function actionIndex($code){
        if (!empty($_POST)){
            $objRes = Users::model()->findByPk($_POST['uid']);
            if (isset($objRes)){
                if ($objRes->confirm_code==$_POST['animals']){

                    $time_live=$objRes->confirm_date-time();
                    if($time_live<=Yii::app()->params['time_code']){
                        $objRes->active='y';
                        $objRes->confirm_code='';
                        $objRes->confirm_date='0000-00-00 00:00:00';
                        $objRes->save();
                        $identity=new UserIdentity($objRes->login,$objRes->pass);
                        if($identity->authenticate_with_crypt()){
                            Yii::app()->user->login($identity);
                            $result['id_user']=Yii::app()->user->id;
                            $result['hash'] = Yii::app()->getSession()->getSessionId();
                        }
                        else {
                            $update = new updateData();
                            $result = array('error'=>array('error_code'=>1,'error_msg'=>$update::ERROR_AUTH));
                        }

                    }
                    else{
                        $objRes->delete();
                        $update = new updateData();
                        $result = array('error'=>array('error_code'=>2,'error_msg'=>$update::ERROR_CONFIRM_CODE));
                    }

                }
                elseif($objRes->confirm_code!=''){
                    $objRes->delete();
                    $update = new updateData();
                    $result = array('error'=>array('error_code'=>2,'error_msg'=>$update::ACCOUNT_NOT_ACTIVE));
                }
                else {
                    $update = new updateData();
                    $result = array('error'=>array('error_code'=>2,'error_msg'=>$update::ACCOUNT_ALREADY_ACTIVE));
                }

            }
            else{
                $update = new updateData();
                $result =array('error'=>array('error_code'=>2,'error_msg'=>$update::ERROR_USER_NOT_EXIST));
            }
        }
        else{
            $this->render('index',array(
                'data'=>$code,
            ));
        }



    }

}