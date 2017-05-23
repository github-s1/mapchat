<?php

class KindController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/main';
    public $pageTitle = 'Онлайн Карта - City';

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
				'actions'=>array('index','view'),
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


	public function actionIndex()
	{
            $objCity = $this->GetCityByCode($_REQUEST['name_en']);
            $objKind = $this->GetKindByCode($_REQUEST['code']);
            if ($objKind != 'false' && $objCity != 'false'){
				$objKind->lider = self::getCityLider($objKind->id, $objCity->id);
                $marks = $this->MarksByKindIdCityId($objCity->id, $objKind->id);			
                $theme = $this->GetThemeByKindId($objKind->id);

                $rez = array('location'=>array('country' => false, 'region' => false, 'city' => $objCity->getAttributes()));
                $rez = array_merge($rez, $this->GetByAddress(3, $objCity->id));

                $this->render('index',array(
                    'data'=>$objKind,			
                    'marks'=>$marks,				
                    'countMarks'=>count($marks),			
                    'city'=>$objCity,				
                    'theme'=>$theme,
                    'kindPage' => json_encode($rez),
                    'selfUser'=>  $this->GetSelfUserJSON()
                ));
            }
            else {
                throw new CHttpException(404,'The requested page does not exist.');
            }
	}


	public function loadModel($id)
	{
		$model=Kind::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}


}
