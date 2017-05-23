<?php

class Mark_jsonController extends Controller
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
				'actions'=>array('index','view','GetMarkByPointId','GetTypeByMarkId','AddMark','DeleteMark','UpdateMark','ClickSpam',
                            'GetMarksByCityId','GetMarksByKindId','GetMarksByCityIdMobile','GetMarksByThemeId','GetPhotosByMarkId',
                            'GetMarkById','GetMarkByIdMobile','Test','GetMarksByAll', 'ViewsIncrement', 'SetAnon'),
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

		throw new CHttpException(404,'The requested page does not exist.');
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
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

    /**
     * функция возвращает данные по меткам
     * вход
     * @param id_point (int) - id точки
     */
    public function actionGetMarkByPointId(){
        $this->layout='//layouts/none';
        $id_point = Yii::app()->request->getPost('id_point');
        //$id_point = $_GET['id_point'];
        $Marks = $this->GetMarkByPointId($id_point);
        $res = array('response'=>$Marks);
        $res_encode=json_encode($res);
        $this->render('getMarkByPointId',array(
            'data'=>$res_encode
        ));
    }
    public function actionTest(){
        $session= json_encode(Yii::app()->session->toArray());
        echo '<pre>'; var_dump($session); echo '</pre>';
        //var_dump(Yii::app()->getSession());
//        $text ='c8efe9465519dc7138eab7747953f0bf__id|s:2:"77";c8efe9465519dc7138eab7747953f0bf__name|s:13:"+380502276956";c8efe9465519dc7138eab7747953f0bfuser_id|s:2:"77";c8efe9465519dc7138eab7747953f0bf__states|a:1:{s:7:"user_id";b:1;}';
//        $data = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $text);
//        var_dump(unserialize($data));
        //$data=unserialize($text);
        //echo json_encode($data);
    }

    /**
     * функция позволяет добавить метку
     * вход
     * обязательные поля, активность метки по умолчанию устанавливается в Y
     * id_kind (int) - id вида к которому относиться метка
     * id_user (int) - id пользователя которомый добавил метку
     * description (string) - описание метки
     * необязательное поле:
     * address (string) - адрес метки
     */
    public function actionAddMark(){
        $id_user = $this->is_authentificate();
        //$id_user = 3;
        $id_kind = Yii::app()->request->getPost('id_kind');
        $createData = false;//Yii::app()->request->getPost('createData');
        $point = Yii::app()->request->getPost('point');
        $description = Yii::app()->request->getPost('description');
        $address = Yii::app()->request->getPost('address');
        $anonymous = Yii::app()->request->getPost('anonymous');
        //$hash = Yii::app()->request->getPost('hash');		
        $period = Yii::app()->request->getPost('period');
        //$result = addData::addMark($id_kind, $id_user, $description, $address, $anonymous, $point, $period, $createData);
        $result = Marks::addMark($id_kind, $id_user, $createData, $description, $address, $anonymous, $point, $period, false);		
        
        echo json_encode(array('response'=>$result));
    }

    /**
     * метод позволяет изменить метку
     * входящие параметры:
     * обязательные поля
     * id_mark - id метки которую необходимо изменить
     * необязательное поле:
     * id_kind - id вида к которому относиться метка
     * id_user - id пользователя которомый добавил метку
     * description - описание метки
     * address - адрес метки
     */
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
           
            $result = Marks::updateMark($id, $id_kind, $id_user, $description, $address, $point, $active, $period, false);
        }
        else {
           $result = array('error'=>array('error_code'=>2,'error_msg'=>updateData::ERROR_FILDS_EMPTY));
        }
        echo json_encode(array('response'=>$result));
    }

    /**
     * метод позволяет удалоить метку с заданым id
     * вход:
     * @param id_mark - id метки которую нудно удалить
     * @return json string =)
     */
    public function actionDeleteMark(){
		$id_user = $this->is_authentificate();
		//$id_user = 3;
        $id_mark = Yii::app()->request->getPost('id_mark');
       
        $delete = new delData;
		
		
        $result = $delete->delMark($id_user, $id_mark);
        echo json_encode(array('response'=>$result));
    }


	public function actionSetAnon() {
		$id = intval(Yii::app()->request->getPost('id_mark'));
		if(!$id) return;
		
		$query = "SELECT anonymous FROM  mark WHERE id='{$id}'";
         $command = Yii::app()->db->createCommand($query);
		 $dataReader = $command->query();
		 $data=$dataReader->readAll();
		 
		 $query = "UPDATE  mark SET anonymous='".(($data[0]['anonymous'] == 'y') ? "n" : "y")."' WHERE id='{$id}'";
         $command = Yii::app()->db->createCommand($query);
         if($command->query()) {
			echo 'ok';
		 }
	}
    /**
     * Получить все значки в городе
     */
    public function actionGetMarksByCityId(){
        $id_city = Yii::app()->request->getPost('id_city');
        $resMarks = $this->GetByAddress(3, $id_city);
        $resMarks['otherMarks'] = ThirdPartyMark::getAllServicesMarks($id_city);
        //$resMarks[] = ThirdPartyMark::getAllServicesMarks($id_city);
        
        //$resMarks = $this->GetByAddress(2, 3);
        //$resMarks['otherMarks'] = ThirdPartyMark::getAllServicesMarks(3);
        
        $this->renderJSON($resMarks);
        
    }

    /**
     * Возвращает массив активных меток по id города.
     * @param id_city - id города по которуму нужно вернуть метки
     */
    public function actionGetMarksByCityIdMobile(){
        $id_city = Yii::app()->request->getPost('id_city');
        $resMarks = $this->GetMarksByCityId($id_city);
        if($resMarks!='false'){
            $i=0;
            foreach ($resMarks as $mark){
                $resMarks[$i]['points'] = $this->getPointsByMarkId($mark['id']);
                $resMarks[$i]['kind'] = $this->getKindByid($mark['id_kind']);
                $i++;
            }
        }
        $res = array('response'=>$resMarks);
        $res_encode=json_encode($res);
        $this->render('getMarksByCityId',array(
            'data'=>$res_encode
        ));
    }

    /**
     * позволяет получить информацию по меткам, точкам и виду меток
     * @param id_kind - id вида по которому надо вернуть инфу
     */
    public function actionGetMarksByKindId(){
	    $id_kind = Yii::app()->request->getPost('id_kind');
        $id_city = Yii::app()->request->getPost('id_city');
        
        $isMobile = true; //! этот метод используется только для мобильного, походу))
        
		$marks = $this->MarksByKindIdCityId($id_city, $id_kind);
		if($marks != 'false'){
			$result = $this->getMarksInfo($marks, $isMobile);
		} else {
			$result = $marks;
		}
        echo json_encode(array('response'=>$result));
    }

    /**
     * получение информации по виду,метки, точки по id темы
     * @params id_theme - id темы по которой нужно вернуть инфу
     */
    public function actionGetMarksByThemeId(){
        $id_theme = Yii::app()->request->getPost('id_theme');
        $id_city = Yii::app()->request->getPost('id_city');
        if (isset($id_theme)){
            $query = 'SELECT * FROM  mark WHERE  id_kind IN (SELECT id FROM  kind WHERE  id_theme='.$id_theme.')';
            $command = Yii::app()->db->createCommand($query);
            $dataReader=$command->query();
            $marks=$dataReader->readAll();
            if (!empty($marks)){
            $j=0;
            foreach ($marks as $mark ){
                    $arPoints = $this->GetPointsByMarkId($mark['id']);
                    if ((isset($id_city)) && ($arPoints!='false')){
                        foreach ($arPoints as $pnt){
                            if ($pnt['id_city']==$id_city){
                                $objMarks = Mark::model()->findAllByPK($pnt['id_mark']);
                                $conv = new Converting();
                                $arMarks[] = $conv->convertModelToArray($objMarks);
                            }
                        }
                    }
                    /*$icon = $this->GetIconByKindId($mark['id_kind']);

                    $marks[$j]['icon_url']='http://' . $_SERVER['SERVER_NAME'] . Yii::app()->request->baseUrl . '/img/mark_icons/' . $icon['name'];*/
                $marks[$j]['points'] = $arPoints;
                $marks[$j]['kind'] = $this->getKindByid($mark['id_kind']);

                    $j++;
                }
            if (isset($id_city) && (isset($arMarks))){
                $k=0;
                $arMarks = $conv->user_array_unique($arMarks);
                foreach ($arMarks as $needMark){
                    $resMark[$k] = $needMark[0];
                    $arNeedPoints = $this->GetPointsByMarkId($needMark[0]['id']);
                    $resMark[$k]['points'] = $arNeedPoints;
                    $resMark[$k]['kind'] = $this->getKindByid($needMark[0]['id_kind']);
                    $k++;

                }
                $result=$resMark;
            }
            else
                $result=$marks;
            }
            else
                $result='false';
            }
        else
            $result = 'false';
        $res = array('response'=>$result);
        $res_encode=json_encode($res);
        $this->render('getMarksByThemeId',array(
            'data'=>$res_encode
        ));

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
            $result = $update->clickSpam($id_mark);
        } else {
            $result = array('res' => 'failed');
		}	
        $this->renderJSON($result);
    }

    public function actionGetPhotosByMarkId(){
        $id_mark = Yii::app()->request->getPost('id_mark');
        //$id_mark = $_GET['id_mark'];
        if (isset($id_mark)){
            $result = $this->GetPhotoByMarkId($id_mark);
        }
        else
            $result = 'false';
        $res = array('response'=>$result);
        $res_encode=json_encode($res);
        $this->render('getPhotosByMarkId',array(
            'data'=>$res_encode
        ));
    }

    public function actionGetMarkById(){
        $id_mark = Yii::app()->request->getPost('id_mark');
        $location = Yii::app()->request->getPost('location');
        if (!empty($id_mark)){
            $result['mark'] = $this->GetMarkById($id_mark);						
            if(!empty($result['mark']['id'])) {
                $result['location'] = Controller::GetLocationByMarkId($id_mark);
                $result['kind'] = $this->GetKindByMarkId($id_mark);
                
                $objKind = Kind::model()->findByPk($result['kind']['id']);
                $result['kind']['countOfMarks'] = count($objKind->marks);
                
                $result['type'] = $this->GetTypeByKindId( $result['kind']['id']);

                $result['icon'] = $this->GetIconByKindId( $result['kind']['id']);
                $result['audio'] = $this->GetAudioByMarkId($id_mark);
                $result['points'] = $this->GetPointsByMarkId($id_mark);
                $result['photos'] = $this->GetPhotoByMarkId($id_mark);
                $result['user'] = $this->GetUserById($result['mark']['id_user']);
                $result['comments'] = $this->GetCommentsByMarkId($id_mark);		
            }
            else {
                $result = FALSE;
            }
        } 
        else {
            $result = FALSE;
        }
        $this->renderJSON(array('response'=>$result));
    }

    public function actionGetMarkByIdMobile(){
        $id_mark = Yii::app()->request->getPost('id_mark');
        //$id_mark = $_GET['id_mark'];
        if (isset($id_mark)){
            $result = $this->GetMarkById($id_mark);
            $result['kind'] = $this->GetKindByMarkId($id_mark);
            $result['kind']['icon'] = $this->GetIconByKindId( $result['kind']['id']);
            $result['kind']['icon']['icon_url']='http://' . $_SERVER['SERVER_NAME'] . Yii::app()->request->baseUrl . '/img/mark_icons/' . $result['kind']['icon']['name'];
            $result['audio'] = $this->GetAudioURLByMarkId($id_mark);
            $result['points'] = $this->GetPointsByMarkId($id_mark);
            $result['photos'] = $this->GetPhotoURLByMarkId($id_mark);
            //$result['user'] = $this->GetUserById($result['id_user']);
           //$result['comments'] = $this->GetCommentsByMarkId($id_mark);
        }
        else {
            $result = 'false';
		}	
        $res = array('response'=>$result);
        $res_encode=json_encode($res);
        $this->render('getMarkByIdMobile',array(
            'data'=>$res_encode
        ));

    }

    /**метод позволяет получить метки по городу или теме. А так же можно получить и по городу и по теме
     * взависимости от передаваемы
     * @param id_theme
     * @param id_city
     * @param code_sorting
     *  0 - sort by create data
     *  1 - sort by count views
     */
    public function actionGetMarksByAll(){
    
		$id_theme = Yii::app()->request->getPost('id_theme');
        $id_city = Yii::app()->request->getPost('id_city');
        $choosen_city_id = Yii::app()->request->getPost('choosen_city_id'); // Город по умолчанию, чтоб возвратится на сортировку "в городе"
        $bounds = Yii::app()->request->getPost('bounds'); // Сортировка на карте
        $code_sorting = Yii::app()->request->getPost('code_sorting');
        $marks = Marks::GetMarksBy($id_theme, $id_city, $code_sorting, false, $bounds);
        $result = self::getMarksInfoByAddress($marks, $choosen_city_id);
		$this->renderJSON(array('response'=>$result));
    }

	public function actionViewsIncrement()	{
		$id_mark = Yii::app()->request->getPost('id_mark');		
		if (isset($id_mark)){
			$marker = Mark::model()->findByPk($id_mark);		
			if(!empty($marker)) {		
				$marker->ViewsIncrement();		
				echo json_encode(array('result' => 'success', 'views' => $marker->views));
			} else {
				echo json_encode(array('result' => 'failure', 'error' => 'Маркер с указаным id не сущестувет.'));				
			}       
		} else {
			echo json_encode(array('result' => 'failure', 'error' => 'Не передается id маркера.'));
		}
	}
}
