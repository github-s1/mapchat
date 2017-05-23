<?php

class Forgot_passwordController extends Controller
{
    public $layout ='//layouts/none';
    
    /**
     * Востановления пароля из письма
     */
    public function actionIndex($code, $user_id){
        $user = Users::model()->findByPk((int) $user_id);

        if (!$user) throw new CHttpException(404, 'Пользователь не найден');
        // Если залогинен - редиректим на главную
        if (Yii::app()->user->id === $user->id) $this->redirect(Yii::app()->baseUrl);
        
        if ($user->active == 'y') throw new CHttpException(404, 'Ваш аккаунт уже активированный. Воспользуйтесь стандартной формой для входа.');
        if (strcasecmp($user->confirm_code, $code) !== 0) throw new CHttpException(404, 'Не верный код подтверждения.');
        CVarDumper::dump($user);exit;












        if (!empty($_POST)){
            $objRes = Users::model()->findByPk($_POST['uid']);
            if (isset($objRes)){
                if ($objRes->confirm_code==$_POST['animals']){
                    $time_live=$objRes->confirm_date-time();
                    if($time_live<=Yii::app()->params['time_code']){
                        $objRes->confirm_code='';
                        $objRes->confirm_date='0000-00-00 00:00:00';
                        $objRes->save();
                        $this->redirect(array('forgot_password/newPass','id'=>$objRes->id));
                    }
                    else{
                        $objRes->delete();
                        $update = new updateData();
                        $result = array('error'=>array('error_code'=>2,'error_msg'=>$update::ERROR_CONFIRM_CODE));
                    }
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
            $res = array('response'=>$result);
            $res_encode = json_encode($res);
            $this->render('index_json',array(
                'data'=>$res_encode,
            ));
        }
        else{
            $this->render('index',array(
                'data'=>$code,
            ));
        }
    }

    public function actionNewPass($id){
        if (!empty($_POST)){
            $objRes = Users::model()->findByPk($_POST['uid']);
            if (isset($objRes)){
                $objRes->pass = crypt($_POST['pass']);
                if ($objRes->save()){
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
                    echo 'не сохранено';
                }
                echo $_POST['pass'];
            }
            else {
                echo 'такого пользователя нет';
            }
        }
        else
           $this->render('newPass',array(
               'data'=>$id,
           ));
       }


}