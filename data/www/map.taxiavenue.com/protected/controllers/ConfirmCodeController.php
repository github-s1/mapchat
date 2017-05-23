<?php
/**
 * Created by PhpStorm.
 * User: vitek25
 * Date: 01.09.14
 * Time: 10:24
 */
class ConfirmCodeController extends Controller
{
    public $layout='//layout/none';
    public function actionIndex(){
        die('211312231321');
        $users = new Users();
        $objRes = $users->findByAttributes(array('confirm_code'=>$code));
        if (isset($objRes)){
            if(($objRes->confirm_date)<=(3600*24)){
                $objRes->active='y';
                $identity=new UserIdentity($objRes->login,$objRes->pass);
                if($identity->authenticate()){
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
                $update = new updateData();
                $result = array('error'=>array('error_code'=>2,'error_msg'=>$update::ERROR_CONFIRM_CODE));
            }

        }
        else{
            $update = new updateData();
            $result =array('error'=>array('error_code'=>2,'error_msg'=>$update::ERROR_USER_NOT_EXIST));
        }
        return $result;
    }
}
