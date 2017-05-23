<?php

class ShowBaikalController extends Controller
{
    public $layout='//layouts/baykal';

	

	// Uncomment the following methods and override them if needed
	
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
                'actions'=>array('index','BaikalDetails'),
                'roles'=>array('4'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }
	

	public function actionIndex($id=null)
	{

    $criteria = new CDbCriteria();
    $criteria->addCondition("actual = 0");
    $criteria->order = 'id DESC';
    $count=Baikals::model()->count($criteria);
    $pages=new CPagination($count);
    $pages->pageSize=15;
    $pages->applyLimit($criteria);
    $baikalsNonActual =Baikals::model()->findAll($criteria);
    $baikals =Baikals::model()->findAllByAttributes(array('actual'=> 1));
    
    if (!empty($baikals))
    {
        foreach ($baikals as $Baikal)
        {
            if ($Baikal->status == 0)
            {
                $items[] =  array('label'=>'Байкал №'.$Baikal->id, 'url'=>array('/dispatcher/ShowBaikal/index/id/'.$Baikal->id));
            }elseif($Baikal->status == 1)
            {
                $seconditems[] =  array('label'=>'Байкал №'.$Baikal->id, 'url'=>array('/dispatcher/ShowBaikal/index/id/'.$Baikal->id));
            }
        }
    }
    if(empty($seconditems))
        $seconditems[] =  array('label'=>'Данных нет', 'url'=>array(''));
    if(empty($items))
        $items[] =  array('label'=>'Данных нет', 'url'=>array(''));

		if($id!=null)
        {
		  $baikal=Baikals::model()->findByPk($id);
            if(!empty($baikal))
            {
			    $DangerDriver = UserStatus::model()->findByAttributes(array('id_user'=>$baikal->id_driver));
                if ($baikal->message == "")
                    $baikal->message = "Отсутствует";

			    $this->render('index', array('items' => $items, 'seconditems' => $seconditems, 'baikalsNonActual' => $baikalsNonActual, 'pages' =>$pages ,'DangerDriver'=>$DangerDriver, 'msg' => $baikal->message, 'id' => $id ));
            }else
            {
                $this->render('index', array('items' => $items, 'seconditems' => $seconditems, 'baikalsNonActual' => $baikalsNonActual, 'pages' =>$pages ));
            }
        }else{
           $this->render('index', array('items' => $items, 'seconditems' => $seconditems, 'baikalsNonActual' => $baikalsNonActual, 'pages' =>$pages ));
		}
		
	}
    
      public function actionBaikalDetails($id=null)
       {

        $helpers = array(); 
        $helpersID = array();
        $nonHelpers = array();
        $nonHelpersID = array();
        $Baikal = Baikals::model()->findAllByPk($id);
        if (!empty($Baikal))
        {
           $responses = BaikalsResponses::model()->findAllByAttributes(array('id_baikal'=>$id));
           if (!empty($responses)){
             foreach ($responses as $response) {
               $driver = Users::model()->findByPk($response->id_driver);
               if (!empty($driver))
               {
                 if($response->response==0)
                 {
                    $helpers[] = $driver->name;
                    $helpersID[] = $response->id_driver;
                 }else
                 {
                    $nonHelpers[] = $driver->name;
                    $nonHelpersID[] = $response->id_driver;
                 }
               }
             }
          }
         $this->renderPartial('details', array('baikal'=>$id, 'helpers'=>$helpers,'helpersID'=>$helpersID,'nonHelpers'=>$nonHelpers,'nonHelpersID'=>$nonHelpersID));
        }else{
            $this->renderPartial('details');
        }
     }  


}