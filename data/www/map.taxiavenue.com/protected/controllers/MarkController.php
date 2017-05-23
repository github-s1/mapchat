<?php

class MarkController extends Controller
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout='//layouts/detail';

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



    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        $request = Yii::app()->request;
        $id_mark = $request->getParam('id');
        $cityName = $request->getParam('name_en');
        
//        $id_mark = $_REQUEST['id'];
//        $cityName = $_REQUEST['name_en'];
        $objCity=City::model()->find('name_en=:name_en', array(':name_en'=>$cityName));
//        var_dump($objCity->getAttributes());
        if (isset($objCity)){
            $id_city = $objCity->id;
        }
        else{
            $id_city=null;
        }

        $mark = $this->GetMarkById($id_mark); 		
                /*		$now = strtotime("now") + 3600; 						$hours = round($now - strtotime($mark['createDatatime'])/3600);						echo($hours); exit;*/						
        if ($mark!='false'){

            $mark_city = MarkCity::model()->findByAttributes(array('id_mark'=>$id_mark, 'id_city'=>$id_city));
            if(empty($mark_city)) {
                throw new CHttpException(404,'The requested page does not exist.'); 
                    //$this->redirect('/site/error');
            }

            $audio = $this->getAudioByMarkId($id_mark);
            $photos = $this->GetPhotoByMarkId($id_mark);

            $user = $this->GetUserById($mark['id_user']);
            $kind = $this->GetKindByMarkId($id_mark);
//            $objKind = Mark::model()->findByPk($id_mark);
            $objKind = Kind::model()->findByPk($kind['id']);
            $kind['countOfMarks'] = count($objKind->marks);

            $type = $this->GetTypeByKindId($kind['id']);
            $comments = $this->GetCommentsByMarkId($id_mark);						
            $number_type = $kind['countOfMarks'] - 1;							/*
            $userMarks = $this->GetMarksByUserId($mark['id_user']);
            if ($userMarks!='false'){
                $i=0;
                foreach($userMarks as $userMark){
                    $userMarks[$i]['kind'] = $this->GetKindByMarkId($userMark['id']);
                    $icon = $this->GetIconByKindId($userMarks[$i]['kind']['id']);
                    $userMarks[$i]['icon_url']='http://' . $_SERVER['SERVER_NAME'] . Yii::app()->request->baseUrl . '/img/mark_icons/' . $icon['name'];
                    $userMarks[$i]['point'] = $this->GetPointsByMarkId($userMark['id']);

                    if ($userMarks[$i]['point']!='false'){
                        foreach ($userMarks[$i]['point'] as $point){
                            $city = $this->GetCityByPointId($point['id']);
                            $userMarks[$i]['url']='http://' . $_SERVER['SERVER_NAME'] . Yii::app()->request->baseUrl .'/'. $city->name_en .'/' . $userMarks[$i]['kind']['code'] .'/'.$userMarks[$i]['id'];

                        }
                    }
                }
            }			*/
            $points = $this->GetPointsByMarkId($id_mark);
            $icon = $this->GetIconByKindId($kind['id']);
            $icon['url']='http://' . $_SERVER['SERVER_NAME'] . Yii::app()->request->baseUrl . '/img/mark_icons/' . $icon['name'];

            $this->render('index', array(
                'photos'=>$photos,
                'user'=>$user,
                'kind'=>$kind,
                'mark'=>$mark,
                'number_type'=>$number_type,
                //'userMarks'=>$userMarks,
                'points'=>$points,
                'icon'=>$icon,
                'audio'=>$audio,
                'type'=>$type,
                'city'=>$objCity->getAttributes(true, true),
                'comments'=>$comments,
                'selfUser'=> $this->GetSelfUserJSON()
            ));

        }//$mark!='false'
        else {
            throw new CHttpException(404,'The requested page does not exist.');
        }
    }


    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Mark the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model=Mark::model()->findByPk($id);
        if($model===null) {
            throw new CHttpException(404,'The requested page does not exist.');
        }
        return $model;
    }
}
