<?php

class Register_jsonController extends Controller
{
    public $layout='//layouts/none';
	
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
                'actions' => array('index', 'RegisterMobile', 'CheckCodeFP'),
                'users' => array('*'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }
	
	public function actionIndex()
    {
        $login = Yii::app()->request->getPost('login');
        $pass = Yii::app()->request->getPost('pass');
        //$login = $_GET['login'];
        //$pass = $_GET['pass'];
        $update = new updateData();		
        if ((isset($login))&&(isset($pass)))
        {			
            $user = new Users();
            $user->setScenario('register');
            // Безопасное присваивание значений атрибутам
            $user->login = $login;			
            if (mb_strlen($pass)>=5) {
                $user->pass = password_hash($pass, PASSWORD_DEFAULT);			
				
			}  else {
                $result = array('error'=>array('error_code'=>2,'error_msg'=>$update::PASSWORD_LITTLE_CHAR));
                echo json_encode(array('response' => $result));
                exit;
            }

            // Проверка данных			
            if($user->validate())
            {
                // Сохранить полученные данные
                // false нужен для того, чтобы не производить повторную проверку
                $user->date_register = date('Y-m-d H:i:s');
                $user->active='n';
                $user->confirm_date = time();
                $key_animals=array_rand(Yii::app()->params['animals']);
                $user->confirm_code = Yii::app()->params['animals'][$key_animals];
                if ($user->save()){
                    if(filter_var($user->login, FILTER_VALIDATE_EMAIL)){
                        /*отправка письма для подтверждения регистрации*/
                        /* получатели */
                        $to= $user->login;
                        /* тема/subject */
                        $subject = "Регистрация. Онлайн карта";
                        /* сообщение */
                        $message = '<html><body>Добрый день! Вы зарегистрировались на сайте onlineMap.org. <br/>

                                    Для подтверждения регистрации необходимо перейти по ссылке и указать животное <b>'.$user->confirm_code.'</b>

                                    <a href="http://'.$_SERVER['HTTP_HOST'].'/api/users_json/checkCodeFP?animal='.$user->confirm_code.'&id_user=' . $user->id . '&action=register">Подтверждение email</a></body></html>';
                        /* Для отправки HTML-почты вы можете установить шапку Content-type. */
                        $headers= "MIME-Version: 1.0\r\n";
                        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
                        /* дополнительные шапки */
                        $headers .= "From: onlineMap.org <no-reply@onlineMap.org>\r\n";
                        /* и теперь отправим из */
                        mail($to, $subject, $message, $headers);
                        $result = array('id_user'=>$user->id);
						$result['animals'] = Yii::app()->params['animals'];
						
                    }
                    elseif($this->validate_phone_number($user->login)) {
                        $api = new MainSMS();
                        $message = 'Для подтверждения регистрации необходимо указать животное - '.$user->confirm_code;
                        if ($api->sendSMS($user->login, $message , 'onlinemap')) {
                            $result = array('id_user'=>$user->id);
                            $result['animals'] = Yii::app()->params['animals'];
                        } else {
                            $result = array('error'=>array('error_code'=>2,'error_msg'=> 'Смс не отправлено (код: 1)'));
                        }
                    }
                    else{
                        $result = array('error'=>array('error_code'=>2,'error_msg'=>$update::ERROR_USER_LOGIN_INCORRECT));
                    }

                } else {
                    $errors = Errors::GetErrors($user);
                    $result = array('error'=>array('error_code'=>2,'error_msg'=>$errors));
                }

            }  else {				
                $result = array('error'=>array('error_code'=>4,'error_msg'=>$update::ERROR_USER_LOGIN_EXIST));
            }
        }
        else{			
            $result = array('error'=>array('error_code'=>2,'error_msg'=>$update::ERROR_FILDS_EMPTY));
        }
        echo json_encode(array('response' => $result));	
	}

    // TODO - NOT USE
    public function actionRegisterMobile()
    {
        $login = Yii::app()->request->getPost('login');
        $pass = Yii::app()->request->getPost('pass');
        //$login = $_GET['login'];
        //$pass = $_GET['pass'];
        $update = new updateData();
        if ((isset($login))&&(isset($pass)))
        {
            $user = new Users();
            // Безопасное присваивание значений атрибутам
            $user->login = $login;
            $user->pass = crypt($pass);
            // Проверка данных
            if($user->validate())
            {
                // Сохранить полученные данные
                // false нужен для того, чтобы не производить повторную проверку
                $user->date_register = date('Y-m-d H:i:s');
                $user->active='n';
                $user->confirm_date = time();
                $key_animals=array_rand(Yii::app()->params['animals']);
                $user->confirm_code = Yii::app()->params['animals'][$key_animals];
                if ($user->save(false)){
                    if(filter_var($user->login, FILTER_VALIDATE_EMAIL)){
                        /*отправка письма для подтверждения регистрации*/
                        /* получатели */
                        $to= $user->login;
                        /* тема/subject */
                        $subject = "Регистрация. Онлайн карта";
                        /* сообщение */
                        $message = '<html><body>Добрый день! Вы зарегистрировались на сайте onlineMap.org. <br/>

                                    Для подтверждения регистрации необходимо перейти по ссылке и указать животное <b>'.$user->confirm_code.'</b>

                                    <a href="http://'.$_SERVER['HTTP_HOST'].'/confirm_code/?code='.$user->id.'">Подтверждение email</a></body></html>';
                        /* Для отправки HTML-почты вы можете установить шапку Content-type. */
                        $headers= "MIME-Version: 1.0\r\n";
                        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
                        /* дополнительные шапки */
                        $headers .= "From: onlineMap.org <no-reply@onlineMap.org>\r\n";
                        /* и теперь отправим из */
                        mail($to, $subject, $message, $headers);
                        $result = array('id_user'=>$user->id);
                        $result['animals'] = Yii::app()->params['animals'];
                        $result['msg'] = 'На Ваш электронный адрес отправлено письмо';
                    }
                    elseif($this->validate_phone_number($user->login)) {
                        $api = new MainSMS ( 'onlinemap.org' , 'ccfccab583b43', false, false );
                        $message = 'Для подтверждения регистрации необходимо указать животное - '.$user->confirm_code;
                        $api->sendSMS ($user->login, $message , 'onlinemap');
                        $result = $this->GetUserById($user->id);
                        $result['animals'] = Yii::app()->params['animals'];
                        $result['msg'] = 'На Ваш телефон отправлено сообщение';

                    }
                    else{
                        $result = array('error'=>array('error_code'=>2,'error_msg'=>$update::ERROR_USER_LOGIN_INCORRECT));
                    }

                }

            }
            else {
                $result = array('error'=>array('error_code'=>4,'error_msg'=>$update::ERROR_USER_LOGIN_EXIST));

            }
        }
        else{
            $result = array('error'=>array('error_code'=>2,'error_msg'=>$update::ERROR_FILDS_EMPTY));
        }
        $res = array('response'=>$result);
        $res_encode = json_encode($res);
        $this->render('index',array(
            'data'=>$res_encode,
        ));
    }
	
	public function actionCheckCodeFP(){
        $id_user = Yii::app()->request->getPost('id_user');
        $animal = Yii::app()->request->getPost('animal');
		if(!empty($id_user) && !empty($animal)) {
			$objRes = Users::model()->findByPk($id_user);
			if(isset($objRes)){
				//echo json_encode($objRes->confirm_code.'   '.$animal); exit;
				if ($objRes->confirm_code==$animal){
					$time_live=$objRes->confirm_date-time();
					if($time_live<=Yii::app()->params['time_code']){
						$objRes->confirm_code='';
						$objRes->confirm_date='0000-00-00 00:00:00';
						$objRes->active='y';
						$objRes->save();
					   // $result = 'success';
						
						$identity = new UserIdentity($objRes->login,$objRes->pass);

						if($identity->authenticate_register()){
							Yii::app()->user->login($identity);
						} 
						$result = $this->GetUserById($id_user);
					}
					else{
						$objRes->delete();
						$result = array('error'=>array('error_code'=>2,'error_msg'=>updateData::ERROR_CONFIRM_CODE));
					}
				}
				else {
					$objRes->delete();
					$result = array('error'=>array('error_code'=>2,'error_msg'=>updateData::WRONG_ANIMAL));
				}
			}
			else{
				$result =array('error'=>array('error_code'=>2,'error_msg'=>updateData::ERROR_USER_NOT_EXIST));
			}
		} else {
			$result = array('error'=>array('error_code'=>2,'error_msg'=>updateData::MISSING_DATA));
		}
		
        echo json_encode(array('response'=>$result));
        
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