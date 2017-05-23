<?php

class Photo_jsonController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/none';

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
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view','GetPhotoByMarkId','AddPhoto'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
        throw new CHttpException(404,'The requested page does not exist.');
		/*$dataProvider=new CActiveDataProvider('Photo');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));*/
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Photo the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Photo::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

    /**
     * Возвращет фотографии по заданной метке
     * входящие параметры:
     * @param id_mark (int) - id метки по которой нужно вернуть фотографии
     * @param limit (int) - кол-во выводимых стран
     * @param offset (int) - с какой записи делать вывод (нумирация начинается с 0)
     *
     */
    public function actionGetPhotoByMarkId(){
        $this->layout='//layouts/none';
        $id_mark = Yii::app()->request->getPost('id_mark');
        // $id_mark = $_GET['id_mark'];
        $limit = Yii::app()->request->getPost('limit');
        $offset = Yii::app()->request->getPost('offset');
        $Photos = $this->GetPhotoByMarkId($id_mark,$limit,$offset);
        $res = array('response'=>$Photos);
        $res_encode=json_encode($res);
        $this->render('getPhotoByMarkId',array(
            'data'=>$res_encode
        ));
    }

    /**
     * Добавить фото к метке
     */
    public function actionAddPhoto(){
        $id_user = $this->is_authentificate();
        $add = new addData();
        $id_mark = Yii::app()->request->getPost('mark_id');
		$position = Yii::app()->request->getPost('position');
        $idPhoto = Yii::app()->request->getPost('id_photo');
        //$hash = Yii::app()->request->getPost('hash');
		//echo(json_encode($_POST)); exit;
	    //$result = $add->addPhoto($id_mark, $_FILES);
	    if(isset($id_mark)) {
            if ($position === null) {
                $position = $this->getPosition($id_mark, $idPhoto);
                if ($position === false) {
                    $result = array('error'=>array('error_code'=>2,'error_msg'=> 'Нет свободных позиций для сохранения фотографии'));
                    echo(json_encode(array('response'=>$result)));
                    exit;
                }
            }
            
			if(!empty($_FILES)){
                $this->logMsg('files', '-------------------OUT--------------------');
                $this->logMsg('files', getimagesize($_FILES['name']['tmp_name']));
				$_FILES = FilesOperations::FilesProcessing("Photo", "name");
				
				$photo = Photo::model()->findByAttributes(array('id_mark' =>$id_mark, 'position' =>$position));
				if(empty($photo)) {
					$photo = new Photo;  
					$photo->id_mark = $id_mark;
					$photo->name = 'photo';
					$photo->position = $position;				
				}
				if($photo->save()){
                    $this->logMsg('files', '-------------------AFTER_SAVED--------------------');
                    $this->logMsg('files', getimagesize(Yii::getPathOfAlias('webroot.img').'/mark_photos/'.$photo->name));
					$conv = new Converting;   
					$arPhoto = $conv->convertModelToArray($photo);
                    $arPhoto['url'] = Yii::app()->getBaseUrl(true) . '/img/mark_photos/' . $arPhoto['name'];
					$result = $arPhoto;       	
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
    
    /**
     * Если не передана позиция на которую сохранять фотку - берем первую свободную
     * @param int $idMark - id значка к которому привязана фотка
     * @param int $idPhoto - id фотки
     */
    protected function getPosition($idMark, $idPhoto) {
        // Если есть id фото - это ОБНОВЛЕНИЕ, а не ДОБАВЛЕНИЕ
        if ($idPhoto) return Photo::model()->findByPk($idPhoto)->getAttributes()['position'];
        
        $positions = array(0,1,2); // Какие позиции есть вообще
        foreach (Photo::model()->findAllByAttributes(array('id_mark' =>$idMark)) as $value) {
            unset($positions[$value->position]); // Удаляем из массива занятые позиции
        }
        
        if (empty($positions)) return false; // Нет свободных позиций для добавления

        sort($positions); // Сбрасываем индексы массива, чтоб всегда иметь доступ к 0-му элементу
        return $positions[0];
    }

}
