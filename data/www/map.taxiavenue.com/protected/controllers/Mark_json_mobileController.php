<?php

class Mark_json_mobileController extends Controller
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
				'actions'=>array('AddMark', 'UpdateMark', 'GetMarksByAll', 'GetThirdPartyMark', 'GetMarkById', 'ClickSpam'),
				'users'=>array('*'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	
	public function actionGetMarkById(){
        $id_mark = Yii::app()->request->getPost('id_mark');
		$result = Marks::GetMarkById($id_mark, true);
		if(!empty($result)) {
			echo json_encode(array('response'=>$result));
		} else {
			echo json_encode(array('response'=>array('error' => array('error_code' => 2, 'error_msg' => Errors::ERROR_FILDS_EMPTY))));
		}	
    }

    public function actionAddMark(){
		$id_user = $this->is_authentificate();
		//$id_user = 3;
        $id_kind = Yii::app()->request->getPost('id_kind');
		$createData = Yii::app()->request->getPost('createData');
		$point = Yii::app()->request->getPost('point');
        $description = Yii::app()->request->getPost('description');
        $address = Yii::app()->request->getPost('address');
		$anonymous= Yii::app()->request->getPost('anonymous');
		//$hash = Yii::app()->request->getPost('hash');		
		$period = Yii::app()->request->getPost('period');
		
		
		
            //$result = addData::addMark($id_kind, $id_user, $description, $address, $anonymous, $point, $period, $createData);
		$result = Marks::addMark($id_kind, $id_user, $createData, $description, $address, $anonymous, $point, $period, true);	
		
        echo json_encode(array('response'=>$result));
    }
	
	public function actionUpdateMark(){
		$id_user = $this->is_authentificate();
		//$id_user = 176;
        $id = Yii::app()->request->getPost('id_mark');
        if (isset($id)) {
            $id_kind = Yii::app()->request->getPost('id_kind');
            $description = Yii::app()->request->getPost('description');
            $address = Yii::app()->request->getPost('address');
            $active = Yii::app()->request->getPost('active');
            $point = Yii::app()->request->getPost('point');
			$period = Yii::app()->request->getPost('period');
           
            $result = Marks::updateMark($id, $id_kind, $id_user, $description, $address, $point, $active, $period, true);
        }
        else {
           $result = array('error'=>array('error_code'=>2,'error_msg'=>updateData::ERROR_FILDS_EMPTY));
        }
        echo json_encode(array('response'=>$result));
    }
	
    public function actionGetMarksByAll(){
        $id_theme = Yii::app()->request->getPost('id_theme');
        $id_city = Yii::app()->request->getPost('id_city');
        $code_sorting = Yii::app()->request->getPost('code_sorting');
        
		$result = $this->getMarksByMobile($id_theme, $id_city, $code_sorting);
                
		
		echo json_encode(array('response'=>$result));
    }
    
    public function actionGetThirdPartyMark() {
        $id_city = Yii::app()->request->getPost('id_city');
        $result = json_decode(ThirdPartyMark::getAllServicesMarks($id_city, true));
        if($result) {
            for($i = 0; $i < count($result); $i++) {
                $result[$i]->lat = (float)$result[$i]->lat;
                $result[$i]->lng = (float)$result[$i]->lng;
            }
        }
        echo json_encode(array('response'=>$result));
    }
    
    protected function getMarksByMobile($id_theme = null, $id_city = null, $code_sorting = 0) {
        if ($code_sorting == 1){
			$sorting = 'ORDER BY views DESC';
		} else{
			$sorting = 'ORDER BY createDatatime DESC';
		}
		//SELECT * FROM  mark WHERE  active = "Y" AND id_kind IN (SELECT id FROM  kind WHERE  id_theme= 1) AND id IN (SELECT id_mark FROM  mark_city WHERE  id_city= 12) ORDER BY createDatatime DESC
		
		$query = 'SELECT * FROM  mark WHERE  active = "Y"';
		 if(isset($id_theme)) {
			$query .= 'AND id_kind IN (SELECT id FROM  kind WHERE  id_theme='.$id_theme.') ';
		}
		
		if(isset($id_city)) {
			$query .= 'AND id IN (SELECT id_mark FROM  mark_city WHERE  id_city = '.$id_city.') ';
		}
		$query .= $sorting;
		/*
		$command = Yii::app()->db->createCommand($query);
		$dataReader=$command->query();
		$marks = $dataReader->readAll();
		*/
		$marks = Mark::model()->findAllBySql($query);
		
		$mark_array = array();
		if (!empty($marks)){
            foreach($marks as $mark) {
				//if($is_mobile) {
					$mark_array[] = self::GetMobileMarkInfo($mark);
				//} else {
					//$mark_array[] = self::GeMarkInfo($mark);
				//}
			}	
		} 
		
		return $mark_array;
    }
    
    protected static function GetMobileMarkInfo( Mark $mark)
	{
		$Rez = array();
		if(!empty($mark)) {  
			$Rez = $mark->getAttributes();  
			
			$Rez['kind'] = Kinds::GetKindInfo($mark->idKind, true);
			$Rez['kind']['countOfMarks'] = Controller::GetNumberThisType($mark->id);	
			$audio = Controller::GetAudioByMarkId($mark->id);
			if($audio != 'false') {
				$Rez['audio'] = $audio['name'];
			} else {
				$Rez['audio'] = NULL;
			}
			
			$photos = Controller::GetPhotoByMarkId($mark->id);
			if($photos != 'false') {
				$Rez['photos'] = $photos;
			} else {
				$Rez['photos'] = array();
			}

			$points = Controller::GetPointsByMarkId($mark->id);
			if($points != 'false') {
				$Rez['points'] = $points;
			} else {
				$Rez['points'] = NULL;
			}
			
		}	
		return $Rez;
    }
    
    /**
     * функция увеличивает счетчик "это спам", и если этот счетчик больше двух
     * то снимает флаг активности метки
     * @param $id
     * @return array|string
     */
    public function actionClickSpam(){
        $id_mark = Yii::app()->request->getPost('id_mark');
        if (isset($id_mark)){
            $update = new updateData;
            $result = $update->clickSpam($id_mark, true);
            $result = array('response' => $result);
        } else {
            $result = array('response' => 'failed');
        }
        echo json_encode($result);
        exit;
    }
}
