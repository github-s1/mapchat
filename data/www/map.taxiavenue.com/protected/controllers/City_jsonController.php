<?php

class City_jsonController extends Controller
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
                'actions'=>array(
                    'index', 'view', 'GetPoints','GetCityByRegionId', 'GetByAddress', 
                    'GetInfoByURL','GetCityByCoordinats', 'GetCityByName', 'GetByCoordinatesWeb'),
                'users'=>array('*'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    /**
     * функция выводит всю информацию по точкам,входом служит id города
     * см. документацию по api
     *
     * @param null $id_city\
     */
    public function actionIndex($id_city=null) {
        if (Yii::app()->request->getPost('id_city')){
            $id_city = Yii::app()->request->getPost('id_city');
        }
        if (!isset($id_city)){
            $id_city = 7;
        }
        $arPoints = $this->GetPointsByCity($id_city);
        if (isset(Yii::app()->user->id)) {
            $arPoints ['user'] = $this->GetUserById(Yii::app()->user->id);
        }
        else {
            $arPoints ['user'] = false;
        }
        $res = array('response'=>$arPoints);
        $res_encode = json_encode($res);
        $this->render('index', array( 'data'=>$res_encode ));
    }

/**
 * функция выводит всю информацию по точкам,
 * входом служит названия страны, области и города
 * см. документацию по api
 * @param null $id_city\
 */


    public function actionGetByAddress(){
        $country = $this->getCountryByName();

        if(empty($country)){
            echo json_encode(array('response'=>'false')); exit;
        } 
        else {
            $flag = 1;
            $result = array('location'=>array('country' => false, 'region' => false, 'city' => false));

            $result['location']['country'] = $country->getAttributes();
            $region = $this->getRegionOfCountryByRegionName($country->id);
            if(!empty($region)){
                $flag = 2;
                $result['location']['region'] = $region->getAttributes();
                $city = $this->getCityOfRegionByCityName($region->id);
                if(!empty($city)){
                    $flag = 3;
                    $result['location']['city'] = $city->getAttributes();
                } else {
                    echo json_encode(array('response'=>'false')); exit;
                }
            } else {
                echo json_encode(array('response'=>'false')); exit;
            }

            switch ($flag){   
                case 3:  
                    $id = $result['location']['city']['id'];
                    break;
                case 2:   
                    $id = $result['location']['region']['id'];
                    break;
                case 1: 
                    $id = $result['location']['country']['id'];
                    break;
            }

            $data = $this->GetByAddress($flag, $id);
            $result = array_merge($result, $data);
            echo json_encode(array('result' => $result)); exit;
        }
    }

    public function actionGetCityByName(){
        $cityName = Yii::app()->request->getPost('cityName');
        if(isset($cityName)){
            $city = City::model()->findByAttributes(array('name_en' =>$cityName));
            if(!empty($city)) {
                $result = array('location'=>array('country' => false, 'region' => false, 'city' => false));
                $result['location']['city'] = $city->getAttributes();
                if(!empty($city->idRegion)) {
                        $result['location']['region'] = $city->idRegion->getAttributes();
                        if(!empty($city->idRegion->idCountry)) {
                                $result['location']['country'] = $city->idRegion->idCountry->getAttributes();
                        }
                }

                $data = $this->GetByAddress(3, $city->id);

                $result = array_merge($result, $data);

                echo json_encode(array('result' => $result)); exit;
            } 
            else {
                echo json_encode(array('response'=>'false')); exit;
            }	
        } 
        else {
            echo json_encode(array('response'=>'false')); exit;
        }	
    }

	
	
    /**
     * Функция возвращает информацию по городу
     * входом служит id региона
     */
    public function actionGetCityByRegionId(){
        $id_region = Yii::app()->request->getPost('id_region');
        //$id_region = $_GET['id_region'];
        $limit = Yii::app()->request->getPost('limit');
        $offset = Yii::app()->request->getPost('offset');
        $Cities = $this->GetCityByRegionId($id_region,$limit,$offset);
		// Убираем повторы
		$Cities = $this->removeDubl($Cities);
		
		if($id_region == 38) {
			$id_region = 4;
			$Cities2 = $this->GetCityByRegionId($id_region,$limit,$offset);
			$Cities = array_merge($Cities, $this->removeDubl($Cities2));
		}
		
        $res = array('response'=>$Cities);
        $res_encode = json_encode($res);
        $this->render('getCityByRegionId',array(
            'data'=>$res_encode
        ));
    }
	
	function removeDubl($Cities) {
		$ids = "";
		if(count($Cities) > 1) {
			for($i = 0; $i < count($Cities)-1; $i++) {
				for($j = $i+1; $j < count($Cities); $j++) {
					if($Cities[$i]['lat'] == $Cities[$j]['lat'] && $Cities[$i]['lng'] == $Cities[$j]['lng']) {
						$ids[] = $j;
					}
				}
			}
			if($ids) {
				foreach($ids as $i) {
					unset($Cities[$i]);
				}
				$temp = $Cities;
				unset($Cities);
				foreach($temp as $v) {
					$Cities[] = $v;
				}
				unset($temp);
			}
		}
		return $Cities;
	}

    /**
     * функция выводит всю информацию по точкам,
     * входом служит названия страны, области и города
     * см. документацию по api
     * @param null $id_city\
     */
    public function actionGetInfoByURL(){
        $codeCity = Yii::app()->request->getPost('code_city');
        $result = $this->GetInfoByURL($codeCity);
        $res = array('response'=>$result);
        $res_encode = json_encode($res);
        $this->render('index',array(
            'data'=>$res_encode,
        ));
    }

    /**
     * возвращает информацию по городу, если города нет в базе, то через геокодер добавляем 
     * город в бд. точно так же добавляются регионы
     */
    public function actionGetCityByCoordinats( ){
        $lat = floatval(Yii::app()->request->getPost('lat'));
        $lng = floatval(Yii::app()->request->getPost('lng'));
       if ((isset($lat)) && (isset($lng)) && (!empty($lat)) && (!empty($lng))){
            $result = $this->GetCityByCoordinats($lat, $lng);
       }
        else {
            $result='false';
        }
        $res = array('response'=>$result);
        $res_encode = json_encode($res);
        $this->render('getCityByCoordinats',array(
            'data'=>$res_encode,
        ));
    }

}
