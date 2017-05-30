<?php
class AuthController extends Controller
{
   /* const VK_APP_ID = 4500943;
    const VK_APP_SECRET = 'XAP5RHrljiiy5mV5LYWV';
    const VK_URL_CALLBACK = 'localhost';
    const VK_URL_ACCESS_TOKEN = 'https://oauth.vk.com/access_token';
    const VK_URL_AUTHORIZE = 'https://oauth.vk.com/authorize';
    const VK_URL_GET_PROFILES = 'https://api.vk.com/method/getProfiles';*/
    public $layout='//layouts/none';

    public function actionIndex(){
        $model = new LoginForm();
        if(isset($_POST['LoginForm'])){
            // получаем данные от пользователя
            $identity=new UserIdentity($_POST['LoginForm']['login'],$_POST['LoginForm']['pass']);
            if($identity->authenticate()){
               // $duration = 3600 * 10 * 30;
                Yii::app()->user->login($identity);
                echo Yii::app()->user->id;
            }
            else {
                echo $identity->errorMessage;
			}	
        }
        else {
            $this->render('index',array(
                'model'=>$model,
            ));
        }
    }

    /**
     * Авторизация через соцсети
     */
    public function actionOpenAuth() {
        $str = Yii::app()->request->getQuery('system');
        $system = OpenAuth::getClassSystem($str);
        $openAuth = new OpenAuth($system);

        $user = $openAuth->auth();
        if ($user !== false){
            $identity = new UserIdentity($user->login, $user->pass);
            if($identity->authenticate_social($user->soc_id, $str)){
                   // $duration = 3600 * 10 * 30;
                Yii::app()->user->login($identity);
                //echo Yii::app()->user->id;
            }
        }
        echo "
            <script type=\"text/javascript\">
                if (window.opener != null && (window.name == 'loginSocial' || window.name.indexOf('_e_') > -1))
                {   // Перезагрузка окна
                    window.opener.location.href = 'http://185.159.129.150:8085';
                    window.close();
                }
            </script>";
    }
}