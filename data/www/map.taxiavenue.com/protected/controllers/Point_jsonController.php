<?php

class Point_jsonController extends Controller
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
				'actions'=>array('index','view','GetPointByCityId', 'GetPointsByMarkId','updatePoint','delPoint'),
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
        $dataProvider=new CActiveDataProvider('Point');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Point the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Point::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

    /**
     * Возвращает список всех точек которые относятся к заданному городу
     * входящие параметры:
     * id_city (int) - id города по котору нужно вернуть список точек
     * limit (int) - кол-во выводимых записей
     * offset (int) - с какой записи делать вывод (нумирация начинается с 0)
     */
    public function actionGetPointByCityId(){
        $id_city = Yii::app()->request->getPost('id_city');
        //$id_city =$_GET['id_city'];

        if (isset($id_city)):
            $criteria = new CDbCriteria;
            $criteria->condition='id_city=:id_city';
            $criteria->params=array(':id_city'=>$id_city);
            $limit = Yii::app()->request->getPost('limit');
            if (isset($limit)){
                $criteria->limit=$limit;
            }
            $offset = Yii::app()->request->getPost('offset');
            if (isset($offset)){
                $criteria->offset=$offset;
            }
            $Points = Point::model()->findAll($criteria);
            $conv = new Converting;
            if (!empty($Points)){
                foreach($Points as $objPoint){
                    $arPoints[]=$conv->convertModelToArray($objPoint);
                }
                $res=array('response'=>$arPoints);
            }
            else
                $res=array('response'=>'false');
        else:
            $res=array('response'=>'false');
        endif;
        $res_encode=json_encode($res);
        $this->render('getPointByCityId',array(
            'data'=>$res_encode
        ));
    }

    public function actionGetPointsByMarkId(){
        $id_mark = Yii::app()->request->getPost('id_mark');
        //$id_mark = $_GET['id_mark'];
        $limit = Yii::app()->request->getPost('limit');
        $offset = Yii::app()->request->getPost('offset');
        $result = $this->GetPointsByMarkId($id_mark, $limit, $offset);
        $res = array('response'=>$result);
        $res_encode=json_encode($res);
        $this->render('getPointsByMarkId',array(
            'data'=>$res_encode
        ));
    }

    public function actionUpdatePoint(){
        $id_point = Yii::app()->request->getPost('id_point');
        $id_mark = Yii::app()->request->getPost('id_mark');
        $id_city = Yii::app()->request->getPost('id_city');
        $lat = Yii::app()->request->getPost('lat');
        $lng = Yii::app()->request->getPost('lng');
        $order = Yii::app()->request->getPost('order');
        $hash = Yii::app()->request->getPost('hash');
        if (isset($id_point)){
            $update = new updateData();
            $result = $update->updatePoint($id_point, $id_mark, $id_city, $lat, $lng, $order, $hash);
        }
        else
            $result = 'false';
        $res = array('response'=>$result);
        $res_encode=json_encode($res);
        $this->render('updatePoint',array(
            'data'=>$res_encode
        ));
    }

    public function actionDelPoint(){
        $id_point = Yii::app()->request->getPost('id_point');
        $hash = Yii::app()->request->getPost('hash');
        if ((isset($id_point))&&(isset($hash))){
            $del = new delData();
            $result = $del->delPoint($id_point,$hash);
        }
        else
            $result = 'false';
        $res = array('response'=>$result);
        $res_encode=json_encode($res);
        $this->render('delPoint',array(
            'data'=>$res_encode
        ));
    }
}
