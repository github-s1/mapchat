<?php

class Avatar_jsonController extends Controller
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
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('AddAvatar','DelAvatarByUserId'),
				'users'=>array('*'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
    
    public function actionAddAvatar()
    {		
        $id_user = $this->is_authentificate();
		//$id_user = 3;
        $add = new addData();
        if(isset($id_user)) {
			if(!empty($_FILES)){  
				$User = Users::model()->findByPk($id_user); 
			
				$_FILES = FilesOperations::FilesProcessing("Avatar", "big_photo");
				
				$model = Avatar::model()->findByPk($User->id_avatar);
				if(empty($model) || (!empty($model) && $User->id_avatar == 1)) {
					$model = new Avatar;  
					$model->big_photo = 'avatar';
					$model->small_photo = 'avatar';				
				}
				if($model->save()){  
					$old_avatar = $User->id_avatar;
					$User->id_avatar = $model->id;   
					$User->save();
					//узнаем размеры загруженного файла  
					$conv = new Converting;   
					$arAvatar = $conv->convertModelToArray($model);

					$result = $arAvatar;       	
				}       
				else {
					$result = array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_SAVE));
				}	
			}      
			else {
				$result = array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_FILE)); 
			}
        }
        else {
            $result = array('error'=>array('error_code'=>2,'error_msg'=>$add::ERROR_FILDS_EMPTY));
        }
       
	   echo(json_encode(array('response'=>$result)));        
    }

    public function actionDelAvatarByUserId()
    {
    $delete = new delData();
    $id_user = Yii::app()->request->getPost('id_user');
    $hash = Yii::app()->request->getPost('hash');
    if ((isset($id_user))&&(isset($hash))){
        $result = $delete->delAvatar($id_user, $hash);
    }
    else {
        $result = array('error'=>array('error_code'=>2,'error_msg'=>$delete::ERROR_FILDS_EMPTY));
	}	
    $res = array('response'=>$result);
    $res_encode=json_encode($res);
            $this->render('delAvatarByUserId',array(
        'data'=>$res_encode
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