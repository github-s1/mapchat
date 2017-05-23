<?php


class Users_jsonController extends Controller

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
                'actions' => array('index', 'view', 'GetActiveUser', 'GetUserById', 'UpdateUser', 'UpdateUserWeb', 'UpdateStatus',
                    'Logout', 'UserBan','forgotPassword','checkCode','forgotPassword','CheckCodeFP',
                    'NewPass'),
                'users' => array('*'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('create', 'update'),
                'users' => array('@'),
            ),
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('admin', 'delete'),
                'users' => array('admin'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * Lists all models.
    */

    public function actionIndex()
    {
        $dataProvider = new CActiveDataProvider('Users');
        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }
    
    /*
     * Добавил во время отутствия серверника (28/09/14)
     */
    public function actionGetActiveUser()
    {
        $result = $this->GetUserById(Yii::app()->user->id);
        $this->renderJSON(array('response' => $result));
    }

    /**
     * Возвращает массив данных пользователя по его id.
     * @param id_user
     */
    public function actionGetUserById()
    {
        $id_user = Yii::app()->request->getPost('id_user');
        if (!$id_user) $id_user = Yii::app()->user->id;
        $result = $this->GetUserById($id_user);
        $this->renderJSON(array('response' => $result));
        
        /*
        $id_user = Yii::app()->request->getPost('id_user');
        $user = Users::model()->findByPk($id_user);
        if (isset($user)){
            $this->renderJSON(array('response' => $user));
        }
        else {
            $this->renderJSON(array('response' => FALSE));
        }
        */
    }

    /**
     * Походу - только для МОБИЛЫ. Для web используется updateUserWeb
     * Позволяет редактировать данные пользователя
     *  входящие параметры:
     * @param id_user    id пользователя по которуму нужно вернуть данные
     * @param hash    id автарки пользователя
     *  Необязательные входящие параметры:
     * @param id_avatar    id автарки пользователя
     * @param name    имя пользователя
     * @param family    фамилия пользователя
     * @param sex    пол
     * @param age    возвраст
     * @param about    информация о себе, интересы
     * @param telephone    телефон пользователя
     * @param email    email пользователя
     * @param city    город пользователя
     */
    public function actionUpdateUser()
    {
        $id_user = Yii::app()->request->getPost('id_user');
        /*$id_avatar = Yii::app()->request->getPost('id_avatar');
        $name = Yii::app()->request->getPost('name');
        $family = Yii::app()->request->getPost('family');
        $sex = Yii::app()->request->getPost('sex');
        $age = Yii::app()->request->getPost('age');
        $about = Yii::app()->request->getPost('about');
        $telephone = Yii::app()->request->getPost('telephone');
        $email = Yii::app()->request->getPost('email');
        $city = Yii::app()->request->getPost('city');
        $status = Yii::app()->request->getPost('status');
        $hash = Yii::app()->request->getPost('hash');*/
        if ($id_user != Yii::app()->user->id) {
            $result = array('error' => array('error_code' => 2, 'error_msg' => 'Нету прав для редактирования пользователя'));
        } else {
            $update = new updateData();
            $result = $update->updateUser($id_user);
        }
        echo json_encode(array('response' => $result));
        exit;
        /*if ((isset($id_user)) && $id_user == Yii::app()->user->id) {
            $result = $update->updateUser($id_user, $id_avatar, $name, $family, $sex, $age, $about, $telephone, $email, $city, $status, $hash);
        } else
            $result = array('error' => array('error_code' => 2, 'error_msg' => $update::ERROR_FILDS_EMPTY));
        $res = array('response' => $result);
        $res_encode = json_encode($res);
        $this->render('updateUser', array(
            'data' => $res_encode
        ));*/
    }
    
    public function actionUpdateUserWeb()
    {
        $id = Yii::app()->request->getPost('id');
        $model = Users::model()->findByPk($id);
        if(empty($model)){
            $this->renderJSON(FALSE);
            return;
        }
        
        $id_avatar = Yii::app()->request->getPost('id_avatar');
        if(isset($id_avatar)){
            $model->id_avatar = $id_avatar;
        }
        $name = Yii::app()->request->getPost('name');
        $model->name = $name;
        $model->family = Yii::app()->request->getPost('family');
        //$model->login = Yii::app()->request->getPost('login'); // пока не меняем (это email)
        $model->sex = Yii::app()->request->getPost('sex');
        $model->age = Yii::app()->request->getPost('age');
        $model->about = Yii::app()->request->getPost('about');
        $model->telephone = Yii::app()->request->getPost('telephone');
        $model->email = Yii::app()->request->getPost('email');
        $model->city = Yii::app()->request->getPost('city');
        $model->status = Yii::app()->request->getPost('status');
        $pass = Yii::app()->request->getPost('pass');
        if (mb_strlen($pass) >= 5) $model->pass = crypt($pass);
        $model->save();
        
        if ($name == '') $model->name = $model->login;
        Yii::app()->user->setState('first_name', $model->name); // Обновляем имя в кэше
        
        $model->pass = "";
        $this->renderJSON($model);
//        $this->renderJSON(["isset"=>isset($age), "age"=>$age]);
    }

    public function actionUpdateStatus()
    {
        $id_user = Yii::app()->request->getPost('id_user');
        $status = Yii::app()->request->getPost('status');
        $hash = Yii::app()->request->getPost('hash');
        $update = new updateData();
        if ((isset($id_user)) && (isset($hash)) && (isset($status))) {
            $result = $update->updateStatus($id_user, $status, $hash);
        } else
            $result = array('error' => array('error_code' => 2, 'error_msg' => $update::ERROR_FILDS_EMPTY));
        $res = array('response' => $result);
        $res_encode = json_encode($res);
        $this->render('updateStatus', array(
            'data' => $res_encode
        ));
    }

    public function actionLogout()
    {		/*
        $id_user = Yii::app()->request->getPost('id_user');
        $hash = Yii::app()->request->getPost('hash');
        if ((isset($id_user)) && (isset($hash))) {
            Yii::app()->user->logout();
           // Yii::app()->session->destroySession();
            $model = Users::model()->findByPk($id_user);
            $model->online = 0;
            $model->save();
            $result = 'success';
        } else
            $result = array('error' => array('error_code' => 2, 'error_msg' => updateData::ERROR_FILDS_EMPTY));
        $res = array('response' => $result);
        $res_encode = json_encode($res);
        $this->render('logout', array(
            'data' => $res_encode
        ));		*/		
		if(!empty(Yii::app()->user->id)) {			
			$model = Users::model()->findByPk(Yii::app()->user->id);   
			$model->online = 0;          
			$model->save();					
			Yii::app()->user->logout();		
		}		
		echo json_encode(array('response' => 'success'));
    }

    /**
     * метод отправляет письмо админу, в котором содержиться информация о пользователе который пожаловался и на которого пожаловались
     */
    public function actionUserBan()
    {
        $id_user_ban = Yii::app()->request->getPost('id_user_ban');
        $id_user_sender = Yii::app()->request->getPost('id_user_sender');
        $update = new updateData();
        if ((isset($id_user_ban)) && (isset($id_user_sender))) {
            $userBan = $this->GetUserById($id_user_ban);
            $userSender = $this->GetUserById($id_user_sender);
            $to= Yii::app()->params['adminEmail'];
            /* тема/subject */
            $subject = "Жалоба на пользователя. Онлайн карта";
            /* сообщение */
            $nameBan = $userBan['name'] . $userBan['family'];
            if ($nameBan == '') $nameBan = $userBan['login'];
            $nameSender = $userSender['name'] . $userSender['family'];
            if ($nameSender == '') $nameSender = $userSender['login'];
            
            $message = "<html><body>Поступила жалоба на пользователя <a href='http://" .$_SERVER['HTTP_HOST'] . "/user/" . $userBan['id'] . "'>" . $nameBan . "</a> от пользователя <a href='http://" .$_SERVER['HTTP_HOST'] . "/user/" . $userSender['id'] . "'>" . $nameSender . "</a></body></html>";
            //$message = 'Поступила жалоба на пользователя ' . $user['name'] . $user['family'] . ' id=' . $user['id'];

            $this->sendMail($to, $subject, $message);
            $result = 'success';
        } else
            $result = array('error' => array('error_code' => 2, 'error_msg' => $update::ERROR_FILDS_EMPTY));
        $res = array('response' => $result);
        $res_encode = json_encode($res);
        $this->render('userBan', array(
            'data' => $res_encode
        ));
    }

    /**
     * Метод отпровляет проверочное животное пользователю который хочет восстановить пароль.
     * @param login - логин пользователя, которому необходимо восстановить пароль
     */
    public function actionForgotPassword(){
        $login = Yii::app()->request->getPost('login');
        $user = Users::model()->findByAttributes(array('login'=>$login, 'soc_register' => null));
        
        if (isset($user)){
            $user->setScenario('update');
            $user->confirm_date = time();
            $key_animals=array_rand(Yii::app()->params['animals']);
            $user->confirm_code = Yii::app()->params['animals'][$key_animals];
            $user->save();
            if(filter_var($user->login, FILTER_VALIDATE_EMAIL)){
                /*отправка письма для подтверждения регистрации*/
                /* получатели */
                $to= $user->login;
                /* тема/subject */
                $subject = "Восстановление пароля. Онлайн карта";
                /* сообщение */
                $message = '<html><body>Добрый день! '
                        .'Для восстановления пароля необходимо указать животное "<b>'.$user->confirm_code.'</b>" в сплывающем окошке на сайте, либо перейти по ссылке '
                        .'<a href="http://'.$_SERVER['HTTP_HOST'].'/api/users_json/checkCodeFP?animal='.$user->confirm_code.'&id_user=' . $user->id . '">Подтвердить восстановление пароля</a>.<br />'
                        .'После успешного подтверждения Вам будет выслано письмо с новым сгенерированым паролем, который вы всегда сможете поменять в своем личном кабинете.'
                        .'</body></html>';
                /* Для отправки HTML-почты вы можете установить шапку Content-type. */
                /*$headers= "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=UTF-8\r\n";

                $headers .= "From: onlineMap.org <no-reply@onlineMap.org>\r\n";*/
                /* и теперь отправим из */
                mail($to, $subject, $message); //$headers
                $result = array('id_user'=>$user->id);
                $result['animals'] = Yii::app()->params['animals'];
            }
            elseif($this->validate_phone_number($user->login)) {
                $api = new MainSMS();
                $message = 'Для восстановления пароля необходимо указать животное - '.$user->confirm_code;
                if ($api->sendSMS($user->login, $message , 'onlinemap')) {
                    $result = array('id_user'=>$user->id);
                    $result['animals'] = Yii::app()->params['animals'];
                    $this->logMsg('FORGOT_SMS_SUCCESS', $api->getResponse());
                } else {
                    $result = array('error'=>array('error_code'=>2, 'error_msg'=> 'Смс не отправлено (код: 3)'));
                    $this->logMsg('FORGOT_SMS_FAILED', $api->getResponse());
                }
            }
            else{
                $result = array('error'=>array('error_code'=>2,'error_msg'=>updateData::ERROR_USER_LOGIN_INCORRECT));
            }
        }
        else {
            $result =array('error'=>array('error_code'=>2,'error_msg'=>updateData::ERROR_USER_NOT_EXIST));
        }
        $res = array('response' => $result);
        $res_encode = json_encode($res);
        $this->render('forgotPassword', array(
            'data' => $res_encode
        ));
    }

    /**
     * метод проверяет на соответсвие животных при восстановлении пароля
     */
    public function actionCheckCodeFP(){
        Yii::import('application.components.user.checkPassword.*');

        $action = Yii::app()->request->getParam('action');
        if ($action == 'register') {
            $strategy = new RegisterCheck();
        } else {
            $strategy = new ForgotCheck();
        }
        $checkPassword = new CheckPassword($strategy);
        
        try {
            $checkPassword->doCheck();
            $checkPassword->showResult();
        } catch (Exception $ex) {
            $checkPassword->showError($ex);
        }
    }

    public function actionNewPass(){
        $id_user = Yii::app()->request->getPost('id_user');
        $pass = Yii::app()->request->getPost('pass');
        $objRes = Users::model()->findByPk($id_user);

        if (isset($objRes)){
            $objRes->pass = crypt($pass);
            if ($objRes->save()){
                $identity=new UserIdentity($objRes->login,$objRes->pass);
                if($identity->authenticate_with_crypt()){
                    Yii::app()->user->login($identity);
                    $result['id_user']=Yii::app()->user->id;
                    $result['hash'] = Yii::app()->getSession()->getSessionId();
                }
                else {
                    $result = array('error'=>array('error_code'=>1,'error_msg'=>updateData::ERROR_AUTH));
                }

            }
            else{
                $result = array('error'=>array('error_code'=>2,'error_msg'=>updateData::ERROR_SAVE));
            }
        }
        else {
            $result = array('error'=>array('error_code'=>2,'error_msg'=>updateData::ERROR_USER_NOT_EXIST));
        }
        $res = array('response'=>$result);
        $res_encode = json_encode($res);
        $this->render('newPass',array(
            'data'=>$res_encode,
        ));
    }





    /**
     * Метод производит подтвержение регистрации, который проверяет преданного животного с тем которое храниться в базе
     * если пользователь с первого раза не правильно ввел животное то пользователь удаляется из бд
     * @param id_user - id пользователя
     * @param animal - животное
     */
    public function actionCheckCode(){
        $id_user = Yii::app()->request->getPost('id_user');
        $animal = Yii::app()->request->getPost('animal');
        $objRes = Users::model()->findByPk($id_user);
            if (isset($objRes)){
                if ($objRes->confirm_code==$animal){
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
                            $result = array('error'=>array('error_code'=>1,'error_msg'=>updateData::ERROR_AUTH));
                        }
                    }
                    else{
                        $objRes->delete();
                        $result = array('error'=>array('error_code'=>2,'error_msg'=>updateData::ERROR_CONFIRM_CODE));
                    }
                }
                elseif($objRes->confirm_code!=''){
                    $objRes->delete();
                    $result = array('error'=>array('error_code'=>2,'error_msg'=>updateData::ACCOUNT_NOT_ACTIVE));
                }
                else {
                    $result = array('error'=>array('error_code'=>2,'error_msg'=>updateData::ACCOUNT_ALREADY_ACTIVE));
                }

            }
            else{
                $result =array('error'=>array('error_code'=>2,'error_msg'=>updateData::ERROR_USER_NOT_EXIST));
            }
        $res = array('response' => $result);
        $res_encode = json_encode($res);
        $this->render('checkCode',array(
            'data'=>$res_encode,
        ));
    }


    private function getField($field) {
        return Yii::app()->request->isAjaxRequest ? Yii::app()->request->getPost($field) : Yii::app()->request->getParam($field);
    }


}

