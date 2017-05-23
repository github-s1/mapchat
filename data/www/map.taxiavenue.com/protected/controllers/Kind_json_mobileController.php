<?php

class Kind_json_mobileController extends Controller
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
				'actions'=>array('GetAllKinds', 'AddKind', 'ChangeTypeKind'),
				'users'=>array('*'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

    public function actionGetAllKinds(){
        $arKinds = Kinds::getAllKindsNoStatic();
        
		echo json_encode(array('response'=>$arKinds));
    }
	
    
    /**
     * Добавить новый вид
     */
	public function actionAddKind(){
		/*global $_POST;
		$str = '';
		foreach($_POST as $k=>$v) {
			$str .= "$k => $v; ";
		}
		mail('yobipoxini@trickmail.net', 'test', 'text:'.$str);*/
		//die($str);
        $id_user = $this->is_authentificate();		
		//$id_user = 3;
		$id_theme = Yii::app()->request->getPost('id_theme');
        $id_icon = Yii::app()->request->getPost('id_icon');
        $id_type = Yii::app()->request->getPost('id_type');
        $name_ru = Yii::app()->request->getPost('name');
        $code = Yii::app()->request->getPost('code');
		$url_icon = Yii::app()->request->getPost('url_icon');
        $description = Yii::app()->request->getPost('description');
		$color = Yii::app()->request->getPost('color');
        $result = Kinds::addKind($id_theme, $id_icon, $id_type, $name_ru, $code, $description, $id_user, true, $color);
        
		echo json_encode(array('response'=>$result));
    }
    
    /**
     * Изменить тип заначка (из общего)
     */
    public function actionChangeTypeKind() {
        $id_kind = Yii::app()->request->getPost('id_kind');
        $id_mark = Yii::app()->request->getPost('id_mark');
        $mark = Mark::model()->findByPk($id_mark);
        if (!$mark) {
            $result = 'error';
             echo json_encode(array('response'=>$result));
             die();
        } else {
            $mark->id_kind = $id_kind;
            $mark->save();
            $MarkInfo = Marks::GetMobileMarkInfo($mark);
        }
        
        echo json_encode(array('response'=> $MarkInfo  ));
    }

}
