<?php

class LocationController extends Controller
{
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
				'actions'=>array('getLocationByName'),
				'users'=>array('*'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	
    public function actionGetLocationByName(){
        $cityName = Yii::app()->request->getPost('location');
        $city = City::model()->find('name_en=:name_en', array(':name_en'=>$cityName));
        // Если есть город в БД - сразу отдаем результат
        if ($city) {
            $cityPage = $this->_getCityPage($city);
            //$this->renderJSON(array('response'=>$cityPage));
        }
        
        $location = Location::setFromSearchString($cityName);
        if ($location) {
            $cityPage = $this->_getCityPage($location->city);
            $this->renderJSON(array('response'=>$cityPage));
        }
        
        $this->renderJSON(array('response'=>'false'));
    }
	
}
