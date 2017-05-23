<?php

/**
 * @class Главный класс приложения
 */
class CityController extends Controller
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout='//layouts/main';
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
                'actions'=>array('index','view','GetPoints','GetRegionByCityId', 'Policy'),
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
     * 
     */
    public function actionIndex()
    {
        //$this->layout = '/layouts/test';
        $request = Yii::app()->request;
        $cityName = $request->getParam('name_en');

        
//        $this->renderJSON(array('cityName'=>$cityName));
//        return;
        
        if(empty($cityName)){
            $this->render('empty');
            return;
        }
        
        $data = $this->_getFromDb($cityName);

        if(!$data){
            $data = $this->_getFromGoogle($cityName);
        }


        if(!$data){
            // Такого города не знает даже Гугл

            throw new CHttpException(404,'Города с таким названием не существует.');
        }
//        $this->pageTitle = Yii::app()->name . '/' . $objCity->name_ru;
        $this->render('index', $data);
    }
    
    
    public function actionError()
    {
//        var_dump($this->layout);
        $error=Yii::app()->errorHandler->error;
        if($error)
        {
            if(Yii::app()->request->isAjaxRequest) {
                echo $error['message'];
            }
            else {
                $this->render('error', $error);
            }
        }
    }
    
    public function actionPolicy() {
        $this->layout = '//layouts/empty';
        $this->render('policy');
    }
}
