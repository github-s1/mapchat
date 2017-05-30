<?php

class SocialAuth_jsonController extends Controller
{
    public $layout='//layouts/none';

    /*функция позволяет скачивать файлы с удаленного сервера*/
    private function download($url, $target) {
        if(!$hfile = fopen($target, "w"))return false;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.95 Safari/537.11');

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FILE, $hfile);

        if(!curl_exec($ch)){
            curl_close($ch);
            fclose($hfile);
            unlink($target);
            return false;
        }

        fflush($hfile);
        fclose($hfile);
        curl_close($ch);
        return true;
    }

    private function getAge($y, $m, $d) {
        if($m > date('m') || $m == date('m') && $d > date('d'))
            return (date('Y') - $y - 1);
        else
            return (date('Y') - $y);
    }
	public function actionVK()
	{
            if(!isset($_GET['code'])) {
                exit;
            }
        $client_id = Vkontakte::getAppId(); //4533710;
        $code = $_GET['code'];
        $secret = Vkontakte::getSecretKey(); //'sVl0Ca6HNztky7yU9VPj';
        $token_url='https://oauth.vk.com/access_token?client_id='.$client_id.'&scopre=email&client_secret='.$secret.'&code='.$code.'&redirect_uri=http://185.159.129.150:8085/api/socialAuth_json/vk/&v=5.24';
        $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Устанавливаем параметр, чтобы curl возвращал данные, вместо того, чтобы выводить их в браузер.
            curl_setopt($ch, CURLOPT_URL, $token_url);
        $resp = curl_exec($ch);
        curl_close($ch);
        $errors = new updateData();
        $data = json_decode($resp, true);
        if (isset($data['access_token'])) {
            $user_json = file_get_contents('https://api.vk.com/method/users.get?user_id='.$data['user_id'].'&fields=sex,bdate,city,photo_200_orig,photo_50,contacts,about&access_token='.$data['access_token']);
            $data_user = json_decode($user_json,true);
            $objUser = Users::model()->findByAttributes(array('soc_id'=>$data_user['response'][0]['uid']));
            if(!isset($objUser)){
                $model = new Users();
                $model->setScenario('openAuth');
                $model->name = $data_user['response'][0]['first_name'];
                $model->family = $data_user['response'][0]['last_name'];
                if ($data_user['response'][0]['sex']==2){
                    $model->sex='м';
                }
                elseif($data_user['response'][0]['sex']==1){
                    $model->sex='ж';
                }

                if (isset($data_user['response'][0]['bdate'])){
                    $bdate = explode('.',$data_user['response'][0]['bdate']);
                    if (isset($bdate[2])&&isset($bdate[1])&&(isset($bdate[0])))
                        $model->age = $this->getAge($bdate[2],$bdate[1],$bdate[0]);
                }
                $model->login = Transliteration::file($data_user['response'][0]['first_name'].ucfirst($data_user['response'][0]['last_name']));
                $model->pass = crypt($data_user['response'][0]['uid'].time());
                if ($data_user['response'][0]['city']!=0){
                    $city_json = file_get_contents('https://api.vk.com/method/database.getCitiesById?city_ids='.$data_user['response'][0]['city'].'&v=5.24');
                    $city_data = json_decode($city_json,true);
                    $model->city = $city_data['response'][0]['title'];
                }
                $model->soc_register = 'vkontakte';
                $model->soc_id = $data_user['response'][0]['uid'];
                $model->active='y';
                if ($model->save()){
                    $lastIdUser = Yii::app()->db->getLastInsertId();
                } else {
                    echo json_encode(array('error' => 'Данные не сохранены'));
                    exit;
                }
                /*загрузка аватарок, большой и маленькой*/
                $avatar = new Avatar();

                $pathImgBig = pathinfo($data_user['response'][0]['photo_200_orig']);//узнаем имя файла лежащий на удаленном сервере
                $downloadBig = $this->download($data_user['response'][0]['photo_200_orig'],$_SERVER['DOCUMENT_ROOT'].'/img/users_avatar/'.$data_user['response'][0]['uid'].'_'.$pathImgBig['basename']);
                if ($downloadBig==true){
                    $avatar->big_photo = $data_user['response'][0]['uid'].'_'.$pathImgBig['basename'];
                }

                $pathImgSmall = pathinfo($data_user['response'][0]['photo_50']);//узнаем имя файла лежащий на удаленном сервере
                $downloadSmall = $this->download($data_user['response'][0]['photo_50'],$_SERVER['DOCUMENT_ROOT'].'/img/users_avatar/'.$data_user['response'][0]['uid'].'_'.$pathImgSmall['basename']);
                if ($downloadSmall==true){
                    $avatar->small_photo = $data_user['response'][0]['uid'].'_'.$pathImgSmall['basename'];
                }


                if ($avatar->save()){
                    $lastIdAvatar = Yii::app()->db->getLastInsertId();
                }
                $objUserNew = Users::model()->findByPk($lastIdUser);
                if (isset($objUserNew)){
                    $objUserNew->id_avatar = $lastIdAvatar;
                    $objUserNew->save();
                }
                $identity=new UserIdentity($objUserNew->login,$objUserNew->pass);
                if($identity->authenticate_social($model->soc_id, 'vkontakte')){
					$duration = 3600 * 10 * 30;
                    Yii::app()->user->login($identity, $duration);
                    $result = 'success';
                    //$result['id_user']=Yii::app()->user->id;
                    //$result['hash'] = Yii::app()->getSession()->getSessionId();
                }else
                    $result = array('error'=>array('error_code'=>1,'error_msg'=>$errors::ERROR_AUTH));
            }
            else{
                $identity=new UserIdentity($objUser->login,$objUser->pass);
                if($identity->authenticate_social($objUser->soc_id, 'vkontakte')){
                    $duration = 3600 * 10 * 30;
                    Yii::app()->user->login($identity, $duration);
                    $result = 'success';
                    //$result['id_user']=Yii::app()->user->id;
                    //$result['hash'] = Yii::app()->getSession()->getSessionId();
                }else
                    $result = array('error'=>array('error_code'=>2,'error_msg'=>$errors::ERROR_AUTH));
            }
        }
        else
            $result =array('error'=>array('error_code'=>3,'error_msg'=>$errors::ERROR_AUTH_TOKEN));
        $res = array('response'=>$result);
        $res_encode = json_encode($res);
        echo $res_encode;
        exit;
        /*$this->render('VK',array(
            'data'=>$res_encode,
        ));*/

	}
    
    public function actionMailRu()
    {
        if (!isset($_GET['code'])) {
            echo json_encode(array('error'=>array('error_code'=>4,'error_msg'=> 'Не передан параметр code.')));
            exit;
        }
        $errors = new updateData();
        $client_id = Mailru::getAppId(); //'724330'; // ID
        $client_secret = Mailru::getSecretKey(); //'8536ed98701ae70454eaea10def4b2ea'; // Секретный ключ
        $redirect_uri = 'http://185.159.129.150:8085/api/socialAuth_json/mailRu/'; // Ссылка на приложение
        $result = false;
        $params = array(
            'client_id'     => $client_id,
            'client_secret' => $client_secret,
            'grant_type'    => 'authorization_code',
            'code'          => $_GET['code'],
            'redirect_uri'  => $redirect_uri
        );
        $url = 'https://connect.mail.ru/oauth/token';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode(http_build_query($params)));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($curl);
        curl_close($curl);

        $tokenInfo = json_decode($result, true);
        if (!isset($tokenInfo['access_token'])) {
            echo json_encode(array('error'=>array('error_code'=>3,'error_msg'=> 'Неверный access_token.')));
            exit;
        }
        $sign = md5("app_id={$client_id}method=users.getInfosecure=1session_key={$tokenInfo['access_token']}{$client_secret}");

        $params = array(
            'method'       => 'users.getInfo',
            'secure'       => '1',
            'app_id'       => $client_id,
            'session_key'  => $tokenInfo['access_token'],
            'sig'          => $sign
        );
        $userInfo = json_decode(file_get_contents('http://www.appsmail.ru/platform/api' . '?' . urldecode(http_build_query($params))), true);
        $objUser = Users::model()->findByAttributes(array('soc_id'=>$userInfo[0]['uid']));
        $this->logMsg('MAILRU_USERINFO', $userInfo);
        if(!isset($objUser)){
            $model = new Users();
            $model->setScenario('openAuth');
            $model->name = $userInfo[0]['first_name'];
            $model->family = $userInfo[0]['last_name'];
            if ($userInfo[0]['sex']==0){
                $model->sex='м';
            }
            elseif($userInfo[0]['sex']==1){
                $model->sex='ж';
            }
            $model->age = $userInfo[0]['age'];

            $model->login = $userInfo[0]['email'];
            $model->pass = crypt($userInfo[0]['uid'].time());
            if (isset($userInfo[0]['location']) and isset($userInfo[0]['location']['city'])){
                $model->city = $userInfo[0]['location']['city']['name'];
            }
            $model->soc_register = 'mailru';
            $model->soc_id = $userInfo[0]['uid'];
            $model->active='y';
            if ($model->save()){
                $lastIdUser = Yii::app()->db->getLastInsertId();
            }
            else
                $result = array('error'=>array('error_code'=>2,'error_msg'=>$errors::ERROR_SAVE));
            if (isset($lastIdUser)){
                $avatar = new Avatar();
/*загрузка большой картинки на сервер*/
                $pathImgBig = pathinfo($userInfo[0]['pic_190']);//узнаем имя файла лежащий на удаленном сервере
                $downloadBig = $this->download($userInfo[0]['pic_190'],$_SERVER['DOCUMENT_ROOT'].'/img/users_avatar/'.$userInfo[0]['uid'].'_'.$pathImgBig['basename']);
                if ($downloadBig==true){
                    $avatar->big_photo = $userInfo[0]['uid'].'_'.$pathImgBig['basename'];
                }
/*загрузка маленькой картинки на сервер*/
                $pathImgSmall = pathinfo($userInfo[0]['pic_32']);//узнаем имя файла лежащий на удаленном сервере
                $downloadSmall = $this->download($userInfo[0]['pic_32'],$_SERVER['DOCUMENT_ROOT'].'/img/users_avatar/small/'.$userInfo[0]['uid'].'_'.$pathImgSmall['basename']);
                if ($downloadSmall==true){
                    $avatar->small_photo = $userInfo[0]['uid'].'_'.$pathImgSmall['basename'];
                }
                if ($avatar->save()){
                    $lastIdAvatar = Yii::app()->db->getLastInsertId();
                }
                $objUserNew = Users::model()->findByPk($lastIdUser);
                if (isset($objUserNew)){
                    $objUserNew->id_avatar = $lastIdAvatar;
                    $objUserNew->save();
                }
                $identity=new UserIdentity($objUserNew->login,$objUserNew->pass);
                if($identity->authenticate_social($model->soc_id, 'mailru')){
                    $duration = 3600 * 10 * 30;
                    Yii::app()->user->login($identity, $duration);
                    //$result = array("id_user"=>Yii::app()->user->id,"hash"=>Yii::app()->getSession()->getSessionId());
                    $result = 'success';
                }else
                    $result = array('error'=>array('error_code'=>1,'error_msg'=>$errors::ERROR_AUTH));
            }
        }
        else{
            $identity=new UserIdentity($objUser->login,$objUser->pass);
            if($identity->authenticate_social($objUser->soc_id, 'mailru')){
                $duration = 3600 * 10 * 30;
                Yii::app()->user->login($identity, $duration);
                //$result = array("id_user"=>Yii::app()->user->id,"hash"=>Yii::app()->getSession()->getSessionId());
                $result = 'success';
            }else
                $result = array('error'=>array('error_code'=>2,'error_msg'=>$errors::ERROR_AUTH));
        }
        $res = array('response'=>$result);
        $res_encode = json_encode($res);
        echo $res_encode;
        exit;
        /*$this->render('mailru',array(
            'data'=>$res_encode,
        ));*/
    }

    public function actionFacebook()
    {
        if (isset($_GET['code'])) {
            $result = false;
            $client_idFB = Facebook::getAppId(); //'682436305170121'; // Client ID
            $client_secretFB = Facebook::getSecretKey(); //'9fa9716c3077a3468737d995b10de9a3'; // Client secret
            $redirect_uriFB = 'http://185.159.129.150:8085/api/socialAuth_json/facebook/'; // Redirect URIs
            $params = array(
                'client_id'     => $client_idFB,
                'redirect_uri'  => $redirect_uriFB,
                'client_secret' => $client_secretFB,
                'code'          => $_GET['code']
            );

            $url = 'https://graph.facebook.com/oauth/access_token';
            $tokenInfo = null;
            parse_str(file_get_contents($url . '?' . http_build_query($params)), $tokenInfo);

            if (count($tokenInfo) > 0 && isset($tokenInfo['access_token'])) {
                 $errors = new updateData();
                $params = array('access_token' => $tokenInfo['access_token']);

                $userInfo = json_decode(file_get_contents('https://graph.facebook.com/me' . '?' . urldecode(http_build_query($params))), true);
                if (isset($userInfo['id'])) {

                    $objUser = Users::model()->findByAttributes(array('soc_id'=>$userInfo['id']));
                    if(!isset($objUser)){
                        $model = new Users();
                        $model->setScenario('openAuth');
                        $model->name = $userInfo['first_name'];
                        $model->family = $userInfo['last_name'];
                        if ($userInfo['gender']=='male'){
                            $model->sex='м';
                        }
                        elseif($userInfo['gender']=='female'){
                            $model->sex='ж';
                        }

                        if (isset($userInfo['birthday'])){
                            $bdate = explode('/',$userInfo['birthday']);
                            if (isset($bdate[2])&&isset($bdate[1])&&(isset($bdate[0])))
                                $model->age = $this->getAge($bdate[2],$bdate[0],$bdate[1]);
                        }

                        $model->login = $userInfo['email'];
                        $model->email = $userInfo['email'];
                        $model->pass = crypt($userInfo['id'].time());
                        $model->soc_register = 'facebook';
                        $model->soc_id = $userInfo['id'];
                        $model->active='y';
                        if ($model->save()){
                            $lastIdUser = Yii::app()->db->getLastInsertId();
                            $identity=new UserIdentity($model->login,$model->pass);
                            if($identity->authenticate_social($model->soc_id, 'facebook')){
                                $duration = 3600 * 10 * 30;
                                Yii::app()->user->login($identity, $duration);
                                //$result = array("id_user"=>Yii::app()->user->id,"hash"=>Yii::app()->getSession()->getSessionId());
                                $result = 'success';
                            }else
                                $result = array('error'=>array('error_code'=>1,'error_msg'=>$errors::ERROR_AUTH));

                        }
                        else
                            $result = array('error'=>array('error_code'=>2,'error_msg'=>$model->getErrors()));
                    }
                    else{
                        $identity=new UserIdentity($objUser->login,$objUser->pass);
                        if($identity->authenticate_social($objUser->soc_id, 'facebook')){
                            $duration = 3600 * 10 * 30;
                            Yii::app()->user->login($identity, $duration);
                            //$result = array("id_user"=>Yii::app()->user->id,"hash"=>Yii::app()->getSession()->getSessionId());
                            $result = 'success';
                        }else
                            $result = array('error'=>array('error_code'=>3,'error_msg'=>$errors::ERROR_AUTH));

                    }
            }
                else
                    $result = array('error'=>array('error_code'=>4,'error_msg'=>'Не получена информация о пользователе'));

        }
            else
                $result = array('error'=>array('error_code'=>5,'error_msg'=>'ошибка получения токена'));
        }
        $res = array('response'=>$result);
        $res_encode=json_encode($res);
        echo $res_encode;
        exit;
        /*$this->render('facebook',array(
            'data'=>$res_encode,
        ));*/
    }

}