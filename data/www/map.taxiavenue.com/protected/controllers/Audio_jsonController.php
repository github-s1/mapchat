<?php

class Audio_jsonController extends Controller
{
    public $layout='//layouts/none';
    public function accessRules()
    {
        return array(
            array('allow',  // allow all users to perform 'index' and 'view' actions
                'actions'=>array('index','AddAudio'),
                'users'=>array('*'),
            ),
//            array('allow', // allow authenticated user to perform 'create' and 'update' actions
//                'actions'=>array('create','update'),
//                'users'=>array('@'),
//            ),
//            array('allow', // allow admin user to perform 'admin' and 'delete' actions
//                'actions'=>array('admin','delete'),
//                'users'=>array('admin'),
//            ),
//            array('deny',  // deny all users
//                'users'=>array('*'),
//            ),
        );
    }

	public function action()
	{
		$this->render('index');
	}

    public function actionAddAudio(){
        $add = new addData();
        $id_mark = Yii::app()->request->getPost('id_mark');
        $hash = Yii::app()->request->getPost('hash');
        if ((isset($id_mark))&&(!empty($_FILES))){
            $result = $add->addAudio($id_mark, $_FILES, $hash);
        }
        elseif(empty($_FILES)){
            $result=array('error'=>'Файл не выбрал или превышает допустимый размер');
        }
        else{
            $result = 'false';
        }
        $res = array('response'=>$result);
        $res_encode=json_encode($res);
        $this->render('addAudio',array(
            'data'=>$res_encode
        ));


    }


}