<?php

class DefaultController extends Controller
{
	public $layout='//layouts/column2';
	
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			//'postOnly + delete', // we only allow deletion via POST request
		);
	}
	
	public function accessRules()
	{
		return array(
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('index','ShowNewBaikal'),
				'roles'=>array('4'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),			
		);
	}
	
	public function actionIndex()
	{
		$this->render('index');
	}

	 public function actionShowNewBaikal()
      {
        $Baikal = Baikals::model()->findByAttributes(array('actual' => 1, 'dispatcher_view' => 0));
        if (!empty($Baikal)){

          $Baikal->dispatcher_view = 1;

            if ($Baikal->save())
              {
     	       echo $Baikal->id;
               exit;  
               }else
                {
     	         echo 0;
     	         exit;
                }
          }else{
           echo 0;
           exit;
     
       }
     }
    
   


}