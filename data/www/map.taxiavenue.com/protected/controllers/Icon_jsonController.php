<?php

class Icon_jsonController extends Controller
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
				'actions'=>array('index','view','GetIconByKindId','addIcon','GetAllIcons'),
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
		$dataProvider=new CActiveDataProvider('Icon');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}



	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Icon the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Icon::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

    /**
     * возвращает данные по иконке
     * вход id вида метки
     */
    public function actionGetIconByKindId(){
        $this->layout='//layouts/none';
        $id_kind = Yii::app()->request->getPost('id_kind');
        //$id_kind = $_GET['id_kind'];
        $Icon = $this->GetIconByKindId($id_kind);
        $res = array('response'=>$Icon);
        $res_encode=json_encode($res);
        $this->render('getIconByKindId',array(
            'data'=>$res_encode
        ));
    }

    public function actionAddIcon(){
        $this->layout='//layouts/none';
        //echo '<pre>'; print_r($_FILES); echo '</pre>';
        $add = new addData();
        $hash = Yii::app()->request->getPost('hash');
        if (isset($hash)){
            $result = $add->addIcon($_FILES, $hash);
        }
        else {
            $result = array('error'=>'Не передан хеш');
        }
        $res = array('response'=>$result);
        $res_encode=json_encode($res);
        $this->render('addIcon',array(
            'data'=>$res_encode
        ));
    }

    public function actionGetAllIcons(){
        $icons = Icons::getAllIcons();
        echo json_encode(array('response'=>$icons));
    }
}
