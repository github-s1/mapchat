<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();
    
    /*
     * 
     */
    protected function renderJSON($data)
    {
        $this->layout=false;
        header('Content-type: application/json');
        echo CJSON::encode($data);
        Yii::app()->end();
    }

    
    /*
     * Добавил во время отутствия серверника (03/11/14)
     */
    public static function GetLocationByMarkId($id_mark)
    {
        $criteria = new CDbCriteria;
        $criteria->condition='id_mark=:id_mark';
        $criteria->params=array(':id_mark'=>$id_mark);
        
        return array(
            "cities"=>  self::_getCitiesByCriteria($criteria),
            "regions"=>self::_getRegionsByCriteria($criteria),
            "contries"=>self::_getContriesByCriteria($criteria)
        );
    }
    
    private static function _getCitiesByCriteria($criteria)
    {
        $cities = MarkCity::model()->findAll($criteria);
        $_cities = array();
        foreach ($cities as $city){
            array_push($_cities, $city->city);
        }
        return $_cities;
    }
    
    private static function _getRegionsByCriteria($criteria)
    {
        $regions = MarkRegion::model()->findAll($criteria);
        $_regions = array();
        foreach ($regions as $region){
            array_push($_regions, $region->region);
        }
        return $_regions;
    }
    
    private static function _getContriesByCriteria($criteria)
    {
        $countries = MarkCountry::model()->findAll($criteria);
        $_countries = array();
        foreach ($countries as $country){
            array_push($_countries, $country->country);
        }
        return $_countries;
    }

    /**
     * @param null $id_theme
     * @param null $limit
     * @param null $offset
     * @return mixed|string
     * функция возвращает все виды точек по id темы
     */
    protected function GetKindByThemeId($id_theme=null, $limit=null, $offset=null){
        $this->layout='//layouts/none';

        if (isset($id_theme)):
            $criteria = new CDbCriteria;
            $criteria->condition='id_theme=:id_theme';
            $criteria->params=array(':id_theme'=>$id_theme);
            if (isset($limit)){
                $criteria->limit=$limit;
            }
            if (isset($offset)){
                $criteria->offset=$offset;
            }
            $Kinds = Kind::model()->findAll($criteria);
            $conv = new Converting;
            if (!empty($Kinds)){
                $i=0;
                foreach($Kinds as $objKind){
                    $icon = self::GetIconByKindId($objKind->id);
                    $arKinds[]=$conv->convertModelToArray($objKind);

                    $arKinds[$i]['icon_url']='http://' . $_SERVER['SERVER_NAME'] . Yii::app()->request->baseUrl . '/img/mark_icons/' . $icon['name'];
                    $i++;

                }
                $arKindsUnique = $conv->user_array_unique($arKinds);
                return $arKindsUnique;
            }
            else
                return 'false';
        else:
            return 'false';
        endif;
    }

    /**
     * @param null $id_kind
     * @return array|string
     */
    protected function GetIconByKindId($id_kind=null){
        if (isset($id_kind)):
            $objKinds = Kind::model()->findByPk($id_kind);
            if (!empty($objKinds)){
                $id_icon = $objKinds->id_icon;
                $objIcons = Icon::model()->findByPk($id_icon);
                $conv = new Converting;
                $arIcons =$conv->convertModelToArray($objIcons);
               return $arIcons;

            }
        else{
            return 'false';
        }
        else:
            return 'false';
        endif;
    }

    /**
     * @param null $id_mark
     * @return array|string
     */
    public static function GetTypeByKindId($id_kind=null){
        if (isset($id_kind)):
            $objKind = Kind::model()->findByPk($id_kind);
            if (isset($objKind)){
                $id_type = $objKind->id_type;
                $criteria = new CDbCriteria;
                $criteria->condition='id=:id_type';
                $criteria->params=array(':id_type'=>$id_type);
                    /*$limit = Yii::app()->request->getPost('limit');
                    if (isset($limit)){
                        $criteria->limit=$limit;
                    }
                    $offset = Yii::app()->request->getPost('offset');
                    if (isset($offset)){
                        $criteria->offset=$offset;
                    }*/
                $Type = Type::model()->find($criteria);

                if (isset($Type)){
                    $conv = new Converting;
                    $arType=$conv->convertModelToArray($Type);
                    return $arType;
                }
                else
                   return 'false';
                }
            else{
                return 'false';
            }
            else:
            return 'false';
        endif;
    }

    /**
     * @param null $id_mark
     * @return array|string
     */
    protected function GetKindByMarkId ($id_mark=null){
        if (isset($id_mark)):
            $objMark = Mark::model()->findByPk($id_mark);
            if (isset($objMark)){
                $id_kind = $objMark->id_kind;
                $criteria = new CDbCriteria;
                $criteria->condition='id=:id_kind';
                $criteria->params=array(':id_kind'=>$id_kind);
                /*$limit = Yii::app()->request->getPost('limit');
                if (isset($limit)){
                    $criteria->limit=$limit;
                }
                $offset = Yii::app()->request->getPost('offset');
                if (isset($offset)){
                    $criteria->offset=$offset;
                }*/
                $Kind = Kind::model()->find($criteria);
                if (isset($Kind)){
                    $conv = new Converting;
                    $arKind = $conv->convertModelToArray($Kind);

                //$conv = new Converting;
                /*if (!empty($Kinds)){
                    foreach($Kinds as $objKind){
                        $arKindss[]=$conv->convertModelToArray($objKind);
                    }*/
                return $arKind;
                }
                else
                    return 'false';
            }
            else
                return 'false';

            else:
                return 'false';
        endif;
    }			
    
    public static function GetNumberThisType($id_kind=null)	{		
        if(!empty($id_kind)) {			
            $criteria=new CDbCriteria();			
            $criteria->addCondition("id_kind = ".$id_kind);			
            $count = Mark::model()->count($criteria);						
            return $count;
        } else {
            return 0;
        }
    }

    /**
     * @param null $id_kind
     * @return array|string
     */
    protected function GetThemeByKindId($id_kind=null){
    if (isset($id_kind)):
        $objKind = Kind::model()->findByPk($id_kind);
        if (isset($objKind)){
            $id_theme = $objKind->id_theme;
            $criteria = new CDbCriteria;
            $criteria->condition='id=:id_theme';
            $criteria->params=array(':id_theme'=>$id_theme);
            /*$limit = Yii::app()->request->getPost('limit');
            if (isset($limit)){
                $criteria->limit=$limit;
            }
            $offset = Yii::app()->request->getPost('offset');
            if (isset($offset)){
                $criteria->offset=$offset;
            }*/
            $Themes = Theme::model()->find($criteria);
            $conv = new Converting;
            if (isset($Themes)){
                    $arTheme=$conv->convertModelToArray($Themes);
                return $arTheme;
            }
            else
                return 'false';
        }
        else{
            return 'false';
        }
    else:
        return 'false';
    endif;
    }

    /**
     * метод возвращает информацию по метки
     * @param null $id_point
     * @return array|string
     */
    protected function GetMarkByPointId($id_point=null){
        if (isset($id_point)):
            $objPoint = Point::model()->findByPk($id_point);
            if (isset($objPoint)){
                $id_mark = $objPoint->id_mark;
                $criteria = new CDbCriteria;
                $criteria->condition='id=:id_mark';
                $criteria->params=array(':id_mark'=>$id_mark);
                /*$limit = Yii::app()->request->getPost('limit');
                if (isset($limit)){
                    $criteria->limit=$limit;
                }
                $offset = Yii::app()->request->getPost('offset');
                if (isset($offset)){
                    $criteria->offset=$offset;
                }*/
                $Marks = Mark::model()->findAll($criteria);
                $conv = new Converting;
                if (!empty($Marks)){
                    foreach($Marks as $objMark){
                        if ($objMark->active=='Y'){
                            $arMarks[]=$conv->convertModelToArray($objMark);
                            $flag=true;
                        }
                        else{
                            $flag=false;
                        }
                    }
                    if ($flag==true)
                        return $arMarks;
                    else
                        return 'false';
                }
                else
                    return 'false';
            }
            else{
                return 'false';
            }

        else:
            return 'false';
        endif;

    }

    /**метод позволяет получить фотографии метки.
     * @param null $id_mark
     * @param null $limit
     * @param null $offset
     * @return mixed|string
     */
    public static function GetPhotoByMarkId($id_mark=null,$limit=null,$offset=null){
        if (isset($id_mark)):
            $criteria = new CDbCriteria;
            $criteria->condition='id_mark=:id_mark';
            $criteria->params=array(':id_mark'=>$id_mark);

            if (isset($limit)){
                $criteria->limit=$limit;
            }

            if (isset($offset)){
                $criteria->offset=$offset;
            }
            $objPhotos = Photo::model()->findAll($criteria);
            if (!empty($objPhotos)){
                $conv = new Converting;
                $arPhotos =$conv->convertModelToArray($objPhotos);
                $arPhotosUnique = $conv->user_array_unique($arPhotos);
                $i=0;
                foreach ($arPhotosUnique as $photo){
                    $arPhotosUnique[$i]['url'] = 'http://' . $_SERVER['SERVER_NAME'] . Yii::app()->request->baseUrl . '/img/mark_photos/' . $photo['name'];
                    $i++;
                }
                return $arPhotosUnique;

            }
            else{
                return 'false';
            }
        else:
            return 'false';
        endif;
    }


    protected function GetPhotoURLByMarkId($id_mark=null,$limit=null,$offset=null){
        if (isset($id_mark)):
            $criteria = new CDbCriteria;
            $criteria->condition='id_mark=:id_mark';
            $criteria->params=array(':id_mark'=>$id_mark);

            if (isset($limit)){
                $criteria->limit=$limit;
            }

            if (isset($offset)){
                $criteria->offset=$offset;
            }
            $objPhotos = Photo::model()->findAll($criteria);
            if (!empty($objPhotos)){
                $conv = new Converting;
                $arPhotos =$conv->convertModelToArray($objPhotos);
                $arPhotosUnique = $conv->user_array_unique($arPhotos);
                $i=0;
                foreach ($arPhotosUnique as $photo){
                    $arPhotos[$i] = 'http://' . $_SERVER['SERVER_NAME'] . Yii::app()->request->baseUrl . '/img/mark_photos/' . $photo['name'];
                    $i++;
                }
                return $arPhotos;

            }
            else{
                return 'false';
            }
        else:
            return 'false';
        endif;
    }

    /**
     * Метод позволяет получить метки по их виду
     * @param null $id_kind
     * @param null $limit
     * @param null $offset
     * @return mixed|string
     *
     */
    protected function GetMarksByKindId($id_kind=null,$limit=null,$offset=null){
        if (isset($id_kind)):
                $criteria = new CDbCriteria;
                $criteria->condition='id_kind=:id_kind';
                $criteria->params=array(':id_kind'=>$id_kind);
                if (isset($limit)){
                    $criteria->limit=$limit;
                }
                if (isset($offset)){
                    $criteria->offset=$offset;
                }
                $Marks = Mark::model()->findAll($criteria);
                $conv = new Converting;
                if (!empty($Marks)){
                    foreach($Marks as $objMark){
                        if ($objMark->active=='Y'){
                            $arMark[]=$conv->convertModelToArray($objMark);
                            $flag=true;
                        }
                        else
                            $flag=false;

                    }
                    if ($flag==true){
                        $arMarkUnique=$conv->user_array_unique($arMark);
                        return $arMarkUnique;
                    }
                    else{
                        return 'false';
                    }
                }
                else
                    return 'false';
       else:
            return 'false';
        endif;
    }
//    protected function GetPointByMarkId ($id_mark=null,$limit=null,$offset=null){
//        if (isset($id_mark)):
//            $criteria = new CDbCriteria;
//            $criteria->condition='id_mark=:id_mark';
//            $criteria->params=array(':id_mark'=>$id_mark);
//            if (isset($limit)){
//                $criteria->limit=$limit;
//            }
//            if (isset($offset)){
//                $criteria->offset=$offset;
//            }
//            $Points = Point::model()->findAll($criteria);
//            $conv = new Converting;
//            if (!empty($Points)){
//                foreach($Points as $objPoint){
//                    $arPoint[]=$conv->convertModelToArray($objPoint);
//                }
//                $arPointUnique=$conv->user_array_unique($arPoint);
//                return $arPointUnique;
//            }
//            else
//                return 'false';
//        else:
//            return 'false';
//        endif;
//    }

    /**
     * метод позволяет получить информацию по пользователю
     * @param $id_mark
     * @return array|string
     */
    protected function GetUserByMarkId($id_mark){
        if (isset($id_mark)):

            $objMark = Mark::model()->findByPk($id_mark);
            $conv = new Converting;
            if (!empty($objMark)){
                $id_user = $objMark->id_user;
                $objUser = Users::model()->findByPk($id_user);
                $arUsers = $conv->convertModelToArray($objUser);
                return $arUsers;
            }
            else
                return 'false';
        else:
            return 'false';
        endif;
    }



    /**
     * метод возвращает все темы
     * @return CActiveRecord[]
     */
    public static function getAllThemes(){
        /*
		$criteria = new CDbCriteria();
        $criteria->condition='id != -1';
        $arTheme=Theme::model()->findAll($criteria);
		*/
		$arTheme=Theme::model()->findAll();
       return $arTheme;
    }

    /**
     * метод возвращает все выды
     * @return CActiveRecord[]
     */
    public static function getAllKinds(){
        $arKinds=Kind::model()->findAll();
        return $arKinds;
    }

    /**
     * метод возвращает все виды и урл иконки вида
     * @return array
     */


    /**
     * получение региона информации по региону
     * @param null $id_country
     * @param null $limit
     * @param null $offset
     * @return array|string
     */
    protected function GetRegionByCountryId($id_country=null,$limit=null,$offset=null){
        if (isset($id_country)):
            $criteria = new CDbCriteria;
            $criteria->condition='id_country=:id_country';
            $criteria->params=array(':id_country'=>$id_country);
            if (isset($limit)){
                $criteria->limit=$limit;
            }
            if (isset($offset)){
                $criteria->offset=$offset;
            }
            $Regions = Region::model()->findAll($criteria);
            $conv = new Converting;
            if (!empty($Regions)){
                foreach($Regions as $objRegion){
                    $arRegion[]=$conv->convertModelToArray($objRegion);
                }
                return $arRegion;
            }
            else
                return 'false';

        else:
            return 'false';
        endif;

    }

    /**
     * получение информации по стране
     * @param $id_region
     * @return array|string
     */
    protected function GetCountryByRegionId($id_region){
        if (isset($id_region)){
            $objRegion = Region::model()->findByPk($id_region);
            $id_country = $objRegion->id_country;
            if (isset($id_country)){
                $Country = Country::model()->findByPk($id_country);
                $conv = new Converting;
                if (!empty($Country)){
                    $arCountry=$conv->convertModelToArray($Country);
                    return $arCountry;
                }
                else
                    return 'false';
            }
            else
                return 'false';
        }
        else{
            return 'false';
        }

    }

    /**
     * получение информации по региону, вход параметр id города
     * @param null $id_city
     * @return array|string
     */
    protected function GetRegionByCityId($id_city=null){
        if (isset($id_city)){
            $objCity = City::model()->findByPk($id_city);
            $id_region = $objCity->id_region;
            if (isset($id_region)){
                $Regions = Region::model()->findByPk($id_region);
                $conv = new Converting;
                if (!empty($Regions)){
                        $arRegion=$conv->convertModelToArray($Regions);
                    return $arRegion;
                }
                else
                    return 'false';
            }
            else
                return 'false';
            }
        else{
            return 'false';
        }

    }

    /**
     * получение информации по городу вход. параметр ид региона
     * @param null $id_region
     * @param null $limit
     * @param null $offset
     * @return array|string
     */
    protected function GetCityByRegionId($id_region=null,$limit=null,$offset=null){
        if (isset($id_region)):
            $criteria = new CDbCriteria;
            $criteria->condition='id_region=:id_region';
            $criteria->params=array(':id_region'=>$id_region);
            if (isset($limit)){
                $criteria->limit=$limit;
            }
            if (isset($offset)){
                $criteria->offset=$offset;
            }
            $Cities = City::model()->findAll($criteria);
            $conv = new Converting;
            if (!empty($Cities)){
                foreach($Cities as $i => $objCity){
                    $arCity[$i]=$conv->convertModelToArray($objCity);
					$arCity[$i]['name'] = $arCity[$i]['name_ru'];
                    $arCity[$i]['id_country'] = $objCity->idRegion->id_country;
                }
                return $arCity;
            }
            else
                return 'false';

        else:
            return 'false';
        endif;
    }

    /** получение информации по городу, вход параметр код города
     * @param null $code
     * @return array|CActiveRecord|mixed|null|string
     */
    protected function GetCityByCode($code=null){
        if (isset($code)):
            $criteria = new CDbCriteria;
            $criteria->condition='name_en=:name_en';
            $criteria->params=array(':name_en'=>$code);
            $City = City::model()->find($criteria);
            if (isset($City)){
                    return $City;
                }
            else
                return 'false';

        else:
            return 'false';
        endif;

    }

    /**
     * возвращает всю информацию по точкам, меткам, видам и тд. по ид города
     * @param null $id_city
     * @return string
     */
    public function GetPointsByCity($id_city=null){
    if (isset($id_city)){
        $conv = new Converting;
        $objCity=City::model()->findByPk($id_city);
        if (isset($objCity)){
            $arCity=$conv->convertModelToArray($objCity);
            $arRes['city']=$arCity;
            $objPoints = $objCity->points;
            if ( (isset($objPoints))&&(!empty($objPoints)) ){
                $i=0;
                foreach ($objPoints as $point){
                    $arPoint[]=$conv->convertModelToArray($point);
                    $objMark = $point->idMark;
                    if ( (isset($objMark))&&(!empty($objMark)) ){
                        $criteriaMark = new CDbCriteria;
                        $criteriaMark->condition='id_mark=:id_mark';
                        $criteriaMark->params=array(':id_mark'=>$objMark->id);
                        $photos = Photo::model()->findAll($criteriaMark);
                        if (!empty($photos)){
                            foreach($photos as $photo){
                                $arPhoto[]=$conv->convertModelToArray($photo);
                            }

                         }
                    }
                    $arMark[] = $conv->convertModelToArray($objMark);
                    $objKind = $objMark->idKind;
                    $arKind[] = $conv->convertModelToArray($objKind);

                    $objFields = $objKind->fieldsKinds;
                    if (isset($objFields)&&(!empty($objFields))){
                         $arKind[$i]['fields']=$conv->convertModelToArray($objFields);
                    }

                    $objType = $objKind->idType;
                    $arType[] = $conv->convertModelToArray($objType);
                    $objTheme = $objKind->idTheme;
                    $arTheme[] = $conv->convertModelToArray($objTheme);
                    $objIcon = $objKind->idIcon;
                    $arIcon[] = $conv->convertModelToArray($objIcon);

                    /*$objPhoto = $objPhoto->idMark;
                    $arIcon[] = $conv->convertModelToArray($objIcon);*/

                    //$data[] = $point;
                    $i++;
                }
        if (isset($arPhoto))
            $arRes['photos'] = $conv->user_array_unique($arPhoto);
        else
            $arRes['photos'] = 'null';

        if (isset($arPoint))
            $arRes['points'] = $conv->user_array_unique($arPoint);
        else
            $arRes['points'] = 'null';
        if (isset($arMark))
            $arRes['marks'] = $conv->user_array_unique($arMark);
        else
            $arRes['marks']='null';
        if (isset($arKind))
            $arRes['kinds'] = $conv->user_array_unique($arKind);
        else
            $arRes['kinds']='null';

        if (isset($arType))
            $arRes['types'] = $conv->user_array_unique($arType);
        else
            $arRes['types'] = 'null';

        if (isset($arTheme))
            $arRes['themes'] = $conv->user_array_unique($arTheme);
        else
            $arRes['themes'] = 'null';
        if (isset($arIcon))
            $arRes['icons'] = $conv->user_array_unique($arIcon);
        else
            $arRes['icons'] = 'null';
        }
        else
        $arRes=null;
        if (isset($arRes))
            return $arRes;
        else
            return 'false';


        }
        else
			return 'false';
    }
        else
            return 'false';
    }

    /**
     * возвращает информацию по точка, вход. параметр ид метки
     * @param null $id_mark
     * @param null $limit
     * @param null $offset
     * @return array|string
     */
    public static function GetPointsByMarkId($id_mark=null, $limit=null, $offset=null){
        if (isset($id_mark)):
            $criteria = new CDbCriteria;
            $criteria->condition='id_mark=:id_mark';
            $criteria->params=array(':id_mark'=>$id_mark);
            if (isset($limit)){
                $criteria->limit=$limit;
            }
            if (isset($offset)){
                $criteria->offset=$offset;
            }
            $Points = Point::model()->findAll($criteria);
            $conv = new Converting;
            if (!empty($Points)){
                foreach($Points as $i => $objPoint){
                    $arPoints[$i]=$conv->convertModelToArray($objPoint);
					unset($arPoints[$i]['id_mark']);
                }
                return $arPoints;
            }
            else
                return 'false';
        else:
            return 'false';
        endif;
    }

    /**
     * возвращает информациб по меткам, вход. параметр ид города
     * @param null $id_city
     * @return mixed|string
     */
    protected function GetMarksByCityId($id_city=null){
        if (isset($id_city)){
            $conv = new Converting;
            $objCity=City::model()->findByPk($id_city);
            if (isset($objCity)){
                $flag=false;
                $objPoints = $objCity->points;
                foreach ($objPoints as $point){
                    $objMark = $point->idMark;
                    if ($objMark->active=='Y'){
                        $arMarks[] = $conv->convertModelToArray($objMark);
                        $flag = true;
                    }
                }
                if ($flag==true){
                    $arMarksUnique = $conv->user_array_unique($arMarks);
					return $arMarksUnique;
                }
                else
                    return 'false';
            }
            else
                return 'false';

        }
        else
            return 'false';
    }

    /**возвращает всю информацию по пользователю включая информацию из связных таблиц, вход параметр ид пользователя
     * @param null $id_user
     * @return array|string
     */
    public static function GetUserById($id_user=null){
        if (isset($id_user)) {

            $objUser = Users::model()->findByPk($id_user);

            if (isset($objUser)) {
                $conv = new Converting;
                $arUser = $conv->convertModelToArray($objUser);
                if (!$arUser['name']) $arUser['name'] = $arUser['login'];
                if (isset($arUser['id_avatar'])){
                    $objAvatar = $objUser->idAvatar;
                    $arAvatar = $conv->convertModelToArray($objAvatar);

                    $arUser['avatar'] =$arAvatar;
                    if(isset($arAvatar['big_photo']))
                        $arUser['url_big'] ='http://' . $_SERVER['SERVER_NAME'] . Yii::app()->request->baseUrl . '/img/users_avatar/' . $arAvatar['big_photo'];
                    if(isset($arAvatar['small_photo']))
                    $arUser['url_small'] ='http://' . $_SERVER['SERVER_NAME'] . Yii::app()->request->baseUrl . '/img/users_avatar/small/' . $arAvatar['small_photo'];
                }
                //$objIndeterests = $objUser->interests;
               // $arUser['interests'] = $conv->convertModelToArray($objIndeterests);
                /*
                $criteriaStatus = new CDbCriteria;
                $criteriaStatus->condition = 'id_user=:id_user';
                $criteriaStatus->params = array(':id_user' => $id_user);
                $criteriaStatus->order = 'createDatatime DESC';
                $objStatus = StatusUser::model()->find($criteriaStatus);
                if (isset($objStatus)){
                    $arStatus = $conv->convertModelToArray($objStatus);
                    $arUser['status'] = $arStatus['status'];
                }
                else
                    $arUser['status']='';
                */
                unset($arUser['pass']);
                return $arUser;
            } else {
                return false;
            }
        } else return false;
    }
    
    /**
     * 
     */
    protected function GetSelfUserJSON() {
        if(empty(Yii::app()->user->id)){
            $selfUser = NULL;
        }
        else {
            $selfUser = $this->GetUserById(Yii::app()->user->id);
        }
        return json_encode($selfUser);
    }

    /**
     * возвращает информацию по виду включая связные таблицы:
     *  информацию по иконке
     *  информацию по полям вида
     *  информацию по меткам вида
     *  вход. параметр код чпу вида
     * @param null $code
     * @return array|CActiveRecord|mixed|null|string
     */
    protected function GetKindByCode($code=null){
        if(isset($code)){
            $criteria = new CDbCriteria;
            $criteria->condition='code=:code';
            $criteria->params=array(':code'=>$code);
            $objKind = Kind::model()->find($criteria);
            if (isset($objKind)){
                $objIcon = $objKind->idIcon;
                $objFields = $objKind->fieldsKinds;
                $objMarks = $objKind->marks;
               return $objKind;
            }
            else
                return 'false';

        }
        else
            return 'false';
    }

    /**
     * возвращает информацию по городу, вход. параметр ид точки
     * @param null $id_point
     * @return array|CActiveRecord|mixed|null|string
     */
    protected  function GetCityByPointId($id_point=null){
        if (isset($id_point)){
            $objPoint = Point::model()->findByPk($id_point);
            if ((isset($objPoint))&&(!empty($objPoint))){
                $objCity = City::model()->findByPk($objPoint->id_city);
                if ((isset($objCity))&&(!empty($objCity))){
                    return $objCity;
                }
                else
                    return 'false';
            }
            else
                return 'false';
        }
        else
            return 'false';
    }

    /**
     * @param null $id_city
     * @return array|mixed|null|string
     */
    protected function GetOnlyPointsByCity($id_city=null){
        if (isset($id_city)){
            $objCity=City::model()->findByPk($id_city);
            if (isset($objCity)){
                $objPoints = $objCity->points;
                return $objPoints;
            }
            else
                return 'false';
        }
        else
            return 'false';
    }

    /**
     * @param null $id_city
     *
     */
    protected function GetKindsByCity($id_city=null){
        if(isset($id_city)){
            $points = $this->GetOnlyPointsByCity($id_city);
            if ($points!='false'){
                foreach ($points as $point){
                    $marks[] = $this->GetMarkByPointId($point->id);
                }
                if ($marks!='false'){
                    foreach ($marks as $mark){
                        $new_mark[] = $mark[0];
                    }
                    if ($new_mark!='false'){
                        $conv = new Converting();
                        $marksUnique = $conv->user_array_unique($new_mark);
                        foreach ($marksUnique as $markUnique){
                            $kinds[] = self::GetKindByMarkId($markUnique['id']);
                        }
                        if ($kinds!='false'){
                            foreach ($kinds as $kind){
                                $new_kind[] = $kind[0];
                            }
                            $kindUnique = $conv->user_array_unique($new_kind);
                            return $kindUnique;
                        }
                        else
                            return 'false';
                    }
                    else
                    return 'false';
                }
                else
                    return 'false';
            }
            else
                return 'false';

        }
        else
            return 'false';
    }

    /**
     * @return array|string
     */
    protected static function getAllTypes(){
        $arType=Type::model()->findAll();
        if (!empty($arType)){
            $arTypes = Converting::convertModelToArray($arType);
            return $arTypes;
        }
        else
            return 'false';

    }

    /**
     * @param null $codeCity
     * @return string
     */
    protected function GetInfoByURL($codeCity=null){
        if (isset($codeCity)){
                $criteria = new CDbCriteria;
                $criteria->condition='name_en=:name';
                $criteria->params=array(':name'=>$codeCity);
                $objCities=City::model()->find($criteria);
                if (!empty($objCities)){
                    $id_city = $objCities->id;

                }
                else{
                    return 'false';
                }
                if (isset($id_city)){
                    $arData = $this->GetPointsByCity($id_city);
                    $arData['user'] = self::GetUserById(Yii::app()->user->id);
                    return $arData;
                }
                else
                    return 'false';
                }
        else return 'false';
    }

    /**
     * @param null $id
     * @return array|CActiveRecord|mixed|null|string
     */
    protected function GetKindById($id=null){
    if (isset($id)){
        $kind = Kind::model()->findByPk($id);
        if (isset($kind)){
            $conv = new Converting;
            $icon = self::GetIconByKindId($id);

            $kind =   $conv->convertModelToArray($kind);
            $kind['icon_url']='http://' . $_SERVER['SERVER_NAME'] . Yii::app()->request->baseUrl . '/img/mark_icons/' . $icon['name'];
            return $kind;
        }
        else return 'false';
    }
        else return 'false';
    }

    /**
     * @param $id_user
     * @return array|string
     */
    protected function GetMarksByUserId($id_user){
        $criteria = new CDbCriteria();
        $criteria->condition='id_user=:id_user';
        $criteria->params=array(':id_user'=>$id_user);
        $objRes = Mark::model()->findAll($criteria);
        if (!empty($objRes)){
            $conv = new Converting();
            $arRes = $conv->convertModelToArray($objRes);
            return $arRes;
        }
        else
            return 'false';
    }

    /**
     * @param $id
     * @return array|string
     */
    protected function GetMarkById($id){
        $objRes = Mark::model()->findByPk($id);		
        if (!empty($objRes)){
            $conv = new Converting();
            $arRes = $conv->convertModelToArray($objRes);
            return $arRes;
        }
        else
            return 'false';
    }

    /**
     * Функция возвращает информацию по аудиозаписям метки
     * @param $id_mark integer id метки по которой нужно веруть аудио запись
     * @return array
     */
   public static function GetAudioByMarkId($id){
        $criteria = new CDbCriteria();
        $criteria->condition='id_mark=:id_mark';
        $criteria->params=array(':id_mark'=>$id);
        $objRes = Audio::model()->find($criteria);
        if (isset($objRes)){
            $conv = new Converting();
            $arRes = $conv->convertModelToArray($objRes);
            $arRes['url'] = 'http://' . $_SERVER['SERVER_NAME'] . Yii::app()->request->baseUrl . '/audio/' . $arRes['name'];
            return $arRes;
        }
        else
            return 'false';
    }

    protected function GetAudioURLByMarkId($id){
        $criteria = new CDbCriteria();
        $criteria->condition='id_mark=:id_mark';
        $criteria->params=array(':id_mark'=>$id);
        $objRes = Audio::model()->find($criteria);
        if (isset($objRes)){
            $conv = new Converting();
            $arAudio = $conv->convertModelToArray($objRes);
            $arRes = 'http://' . $_SERVER['SERVER_NAME'] . Yii::app()->request->baseUrl . '/audio/' . $arAudio['name'];
            return $arRes;
        }
        else
            return 'false';
    }

    /**
     *
     */
    public static function GetCommentsByMarkId($id){
        $criteria = new CDbCriteria();
        $criteria->condition='id_mark=:id_mark and active="y"';
        $criteria->params=array(':id_mark'=>$id);
        $objRes = Comments::model()->findAll($criteria);
        if (isset($objRes)){
            $conv = new Converting();
            $arRes = $conv->convertModelToArray($objRes);
            $i=0;
            foreach ($arRes as $res){
                $arRes[$i]['user'] = self::GetUserById($res['id_user']);
                $i++;
            }


            return $arRes;
        }
        else
            return 'false';
    }

    protected function validate_phone_number( $string ) {
        if (preg_match( '/^[+]?([\d]{0,3})?[\(\.\-\s]?([\d]{3})[\)\.\-\s]*([\d]{3})[\.\-\s]?([\d]{4})$/', $string)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function generate_password($number)
    {
        $arr = array('a','b','c','d','e','f',
            'g','h','i','j','k','l',
            'm','n','o','p','r','s',
            't','u','v','x','y','z',
            'A','B','C','D','E','F',
            'G','H','I','J','K','L',
            'M','N','O','P','R','S',
            'T','U','V','X','Y','Z',
            '1','2','3','4','5','6',
            '7','8','9','0','.',',',
            '(',')','[',']','!','?',
            '&','^','%','@','*','$',
            '<','>','/','|','+','-',
            '{','}','`','~');
        // Генерируем пароль
        $pass = "";
        for($i = 0; $i < $number; $i++)
        {
            // Вычисляем случайный индекс массива
            $index = rand(0, count($arr) - 1);
            $pass .= $arr[$index];
        }
        return $pass;
    }

	 protected function getCountryByName()
    {
        $countryName = Yii::app()->request->getPost('country');
        if(!isset($countryName)){
            return NULL;
        }
        $criteria = new CDbCriteria;
        $criteria->condition='name_ru=:name';
        $criteria->params=array(':name'=>$countryName);
        return Country::model()->find($criteria);
    }
    
    protected function getRegionOfCountryByRegionName($countryId)
    {	
        $regionName = Yii::app()->request->getPost('region');
        if(!isset($regionName)){
             return NULL;
        }
        
        $criteria = new CDbCriteria;
        $criteria->condition='name_ru=:name and id_country=:id_country';
        $criteria->params=array(':name'=>$regionName, 'id_country'=>$countryId);
        return Region::model()->find($criteria);
    }
    
    protected function getCityOfRegionByCityName($regionId)
    {
        $cityName = Yii::app()->request->getPost('city');
        if(!isset($cityName)){
            return NULL;
        }
        
        $criteria = new CDbCriteria;
        $criteria->condition='name_ru=:name and id_region=:id_region';
        $criteria->params=array(':name'=>$cityName, 'id_region'=>$regionId);
        return City::model()->find($criteria);
    }
	
	static function getMarksIdByCountryId($country_id) {
		$criteria = new CDbCriteria;
        $criteria->condition='id_country='.$country_id;
        $criteria->order = 'id ASC';
		return MarkCountry::model()->findAll($criteria);
	}
	
	static function getMarksIdByRegionId($region_id) {
		$criteria = new CDbCriteria;
        $criteria->condition='id_region='.$region_id;
        $criteria->order = 'id ASC';
		return MarkRegion::model()->findAll($criteria);
	}
	
	static function getMarksIdByCityId($city_id) {
		$criteria = new CDbCriteria;
        $criteria->condition='id_city='.$city_id.' AND m.click_spam < 3';
        $criteria->join = 'INNER JOIN mark AS m ON m.id=id_mark';
        $criteria->order = 'id ASC';
		return MarkCity::model()->findAll($criteria);
	}
	
    /**
     * Получить лидера города (кто оставил наибольше меток)
     */
    protected static function getCityLider($idKind, $idCity) {
        $query = "select CONCAT_WS(' ',u.name, u.family) AS name, u.login, COUNT(t.id_user) AS mcount FROM `mark` `t` 
                  INNER JOIN `mark_city` AS mc ON mc.id_mark=t.id 
                  inner join `users` as u ON u.id=t.id_user
                  WHERE t.id_kind=" . $idKind . " AND mc.id_city=" . $idCity . "
                  GROUP BY t.id_user                  
                  ORDER BY mcount DESC";

        $res = Users::model()->findBySql($query);
        if ($res->name) return $res->name;
        return $res->login;
    }


    /**
     * Оработка значков для отдачи на клиент
     */
	protected static function getMarksInfoByAddress($marks, $choosen_city_id = null) {
		$result = array();
        
		if(!empty($marks)) {
			$kinds = array();
			foreach($marks as $i => $m) {
                if (!isset($m->city->name_en)) continue;
                
				if(!empty($m->mark)) {
					$result['marks'][$i] = $m->mark->getAttributes();
                    $result['marks'][$i]['city_name_en'] = $m->city->name_en;

					if(!empty($m->mark->idKind)) {
						if (!isset($result['kinds'][$m->mark->idKind->id])) {
                            $result['kinds'][$m->mark->idKind->id] = $m->mark->idKind->getAttributes();
                            $result['kinds'][$m->mark->idKind->id]['lider'] = self::getCityLider($m->mark->idKind->id, $m->mark->idCity->id_city);
                        }

						if(!empty($m->mark->idKind->idIcon)) {
							$result['icons'][$m->mark->idKind->idIcon->id] = $m->mark->idKind->idIcon->getAttributes();
						}
						/*
						if(!empty($m->mark->idKind->idTheme)) {
							$result['kinds'][$m->mark->idKind->id]['theme'] = $m->mark->idKind->idTheme->name;
						}
						if(!empty($m->mark->idKind->idType)) {
							$result['kinds'][$m->mark->idKind->id]['type_ru'] = $m->mark->idKind->idType->name_ru;
							$result['kinds'][$m->mark->idKind->id]['type_en'] = $m->mark->idKind->idType->name_en;
						}
						*/							
					}
					
					if(!empty($m->mark->photos)) {
						foreach($m->mark->photos as $photo) {
							$result['marks'][$i]['photos'][] = $photo->name;
						}
					} else {
						$result['marks'][$i]['photos'] = array();
					}
					if(!empty($m->mark->points)) {
						foreach($m->mark->points as $point) {
							$result['marks'][$i]['points'][] = $point->getAttributes();
						}
					} else {
						$result['marks'][$i]['points'] = array();
					}
					if(!empty($m->mark->audios)) {
						foreach($m->mark->audios as $audio) {
							$result['marks'][$i]['audios'][] = $audio->getAttributes();
						}
					} else {
						$result['marks'][$i]['audios'] = array();
					}
				}
			}
		} else {
			$result['marks'] = array(); 
			$result['kinds'] = array(); 
		}
        $result['themes'] = Converting::convertModelToArray(self::getAllThemes());
        $result['types'] = self::getAllTypes();
        
        $city = false;
        if ($choosen_city_id) {
            $res = City::model ()->findByPk ($choosen_city_id);
            $city = $res->getAttributes();
            $city['id_country'] = $res->idRegion->id_country;
        }
        $result['location'] = array('country' => false, 'region' => false, 'city'=> $city);
		return $result;
	}
	
    protected function getMarksInfo($marks, $isMobile = false) {
        $result = array();
        if(!empty($marks)) {
            foreach($marks as $i => $m) {	
                $result[$i] = $m->getAttributes();
                if(!empty($m->idKind)) {
                    $result[$i]['kind'] = $m->idKind->getAttributes();

                    if(!empty($m->idKind->idIcon)) {
                        //$result[$i]['kind']['icon'] = $m->idKind->idIcon->getAttributes();
                        $iconRes = $m->idKind->idIcon->getAttributes();
                        if ($isMobile) {
                            $result[$i]['kind']['icon_url'] = Yii::app()->getBaseUrl(true) .'/img/mark_icons/'.$iconRes['name'];
                        } else {
                            $result[$i]['kind']['icon'] = $iconRes;
                            $result[$i]['kind']['icon']['icon_url'] =  Yii::app()->getBaseUrl(true) .'/img/mark_icons/'.$result[$i]['kind']['icon']['name'];
                            unset($result[$i]['kind']['icon']['name']);
                        }
                    }
                    /*
                    if(!empty($m->idKind->idTheme)) {
                            $result['kinds'][$m->idKind->id]['theme'] = $m->idKind->idTheme->name;
                    }
                    if(!empty($m->idKind->idType)) {
                            $result['kinds'][$m->idKind->id]['type_ru'] = $m->idKind->idType->name_ru;
                            $result['kinds'][$m->idKind->id]['type_en'] = $m->idKind->idType->name_en;
                    }
                    */							
                }

                if(!empty($m->photos)) {
                    foreach($m->photos as $photo) {
                        if ($isMobile) {
                            $photoItem = $photo->getAttributes();
                            $photoItem['url'] = Yii::app()->getBaseUrl(true) . '/img/mark_photos/' . $photoItem['name'];
                            $result[$i]['photos'][] = $photoItem;
                        } else {
                            $result[$i]['photos'][] = $photo->name;
                        }
                    }
                } else {
                    $result[$i]['photos'] = array();
                }
                if(!empty($m->points)) {
                    foreach($m->points as $point) {
                        $result[$i]['points'][] = $point->getAttributes();
                    }
                } else {
                    $result[$i]['points'] = array();
                }
            }
        } else {
            $result = array(); 
        }
        return $result;
    }
	
    public static function GetByAddress($flag, $id){ 
            switch ($flag){   
                    case 3:  
                            $marks = self::getMarksIdByCityId($id);
                            break;
                    case 2:   
                            $marks = self::getMarksIdByRegionId($id);
                            break;
                    case 1:  
                            $marks = self::getMarksIdByCountryId($id);
                            break;
            }
            $result = array();
            $result['themes'] = Converting::convertModelToArray(self::getAllThemes());
            $result['types'] = self::getAllTypes();
            if($flag != 0) {
                    $result = array_merge($result, self::getMarksInfoByAddress($marks, $id));
            }	

            return $result;
    }
	
    private function IssetUser($id = 0)
    {	
            $user = Users::model()->findByPk($id);
            if(!empty($user)) {
                    return true;
            } else {
                    return false;
            }
    }

    protected function is_authentificate()
    {

            //echo json_encode(array('result' => Yii::app()->user->id)); exit;
            if(!empty(Yii::app()->user->id) && $this->IssetUser(Yii::app()->user->id)) {
                    return Yii::app()->user->id;
            } else {
                    echo json_encode(array('response' => array('error'=>array('error_code'=>1,'error_msg'=>Errors::NOT_AUTHORIZED)))); exit;
            }
    }
	
    protected static function MarksByKindIdCityId($id_city, $id_kind) {
        if(isset($id_kind) || (isset($id_city))){
            $query = 'SELECT * FROM  mark WHERE  active = "Y"';
            if(isset($id_kind)) {
                $query .= 'AND id_kind = '.$id_kind.' ';
            }

            if(isset($id_city)) {
                $query .= 'AND id IN (SELECT id_mark FROM  mark_city WHERE  id_city = '.$id_city.') ';
            }
            $query .= 'ORDER BY createDatatime DESC';
            $result = Mark::model()->findAllBySql($query);

        } else {
            $result = 'false';
        }
        return $result;
    }
	
    public static function FilesProcessing($element = "Avatar", $index_photo = "photo")
    {
        if(!empty($_FILES)) {
            $files = $_FILES;
            $_FILES = array();
            foreach($files as $i=>$f) {
                if($i == $index_photo) {
                    $_FILES[$element]['name'][$i] = $f['name'];
                    $_FILES[$element]['type'][$i] = $f['type'];
                    $_FILES[$element]['tmp_name'][$i] = $f['tmp_name'];
                    $_FILES[$element]['error'][$i] = $f['error'];
                    $_FILES[$element]['size'][$i] = $f['size'];
                }	
            }
        }
        return $_FILES; 
    }

    /**
     * @param null $lat
     * @param null $lng
     * @return array|string
     */
    protected function GetCityByCoordinats($lat=null,$lng=null){
        if (isset($lat) && (isset($lng))){
            $params = array(
                'latlng' => $lat .','. $lng,
                'sensor' => 'false',
                'language'=>'ru'
            );
            $response = json_decode(file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?' . http_build_query($params, '', '&')));
            
            if (!empty($response->results))
            {	
                $count_city = 0;

                foreach ($response->results[0]->address_components as $address){
                    if ($address->types[0]=='locality'){
                        $count_city++;
                        /* отправляем запрос к геокодировщику по городу, что бы определить координаты
                          северо-запада и юго-востока*/
                        $cityName = $address->long_name;

                        $City = addData::addLocalityFromGeocoder($cityName,'city');
                        if ($City!='false'){
                          /*
                                                      $params = array(
                              'address' =>$City['name_ru'], 							//
                              'sensor' => 'false',                               // êîëè÷åñòâî âûâîäèìûõ ðåçóëüòàòîâ
                              'language' => 'ru',
                          );
                          $responseCity = json_decode(file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?' . http_build_query($params, '', '&')));

                                                      $northeast_lat = $responseCity->results[0]->geometry->bounds->northeast->lat;
                          $northeast_lng = $responseCity->results[0]->geometry->bounds->northeast->lng;
                          $southwest_lat = $responseCity->results[0]->geometry->bounds->southwest->lat;
                          $southwest_lng =  $responseCity->results[0]->geometry->bounds->southwest->lng;
                          */

                            $result = array(
                                'id'=>$City['id'], 
                                'name'=>$City['name_ru'],
                                'id_region' => $City['id_region'],
                                'id_country' => Region::model()->findByPk($City['id_region'])->id_country,
                                'northeast_lat'=>$City['northeast_lat'],
                                'northeast_lng'=>$City['northeast_lng'], 
                                'southwest_lat'=>$City['southwest_lat'],
                                'southwest_lng'=>$City['southwest_lng']
                            );
                            return $result;
                        }
                    }
                }
                if($count_city == 0) {
                    return array('error'=>array('error_code'=>2,'error_msg'=>Errors::CITY_NOT_FOUND));
                }

            }
            else {
                return array('error'=>array('error_code'=>2,'error_msg'=>Errors::CITY_NOT_FOUND));
            }
        }
        else {
            return array('error'=>array('error_code'=>2,'error_msg'=>Errors::MISSING_DATA));
        }
    }

    protected function _getFromGoogle($cityName){
        $location = Location::setFromSearchString($cityName);

        if(!$location){
            return FALSE;
        }
        
        /*$cityPage = array(
            'location'=>$location->toArray(),
            'icons'=>array(), 'kinds'=>array(), 'marks'=>array(), 'themes'=>array(), 'types'=>array()
        );*/
		
        $cityPage = array_merge(
                $this->GetByAddress(3, $location->city->id),
                array('location' => $location->toArray())
        );
        return array(
            'data'=>array(), 
            'city' => $location->city,
            'cityPage'=>json_encode($cityPage)
        );
    }
    
    protected function _getFromDb($cityName){
        $city = City::model()->find('name_en=:name_en', array(':name_en'=>$cityName));
        if(empty($city)){
            return FALSE;
        }
        
        $marks = $city->getMarkersForCity();
        $result = $this->getMarksInfo($marks);
        //$rez = array('location'=>array('country' => false, 'region' => false, 'city'=> $city->getAttributes()));
        //$cityPage = array_merge($rez, $this->GetByAddress(3, $city->id));
        $cityPage = $this->_getCityPage($city);
        
        return array(
            'data'=>$result, 
            'city' => $city,
            'cityPage'=>json_encode($cityPage)
        );
    }

    protected function _getCityPage($city) {
        //$region = $this->GetRegionByCityId($city->id);
        
        $arrCity = $city->getAttributes(true, true);
        $arrRegion = false;
        $arrCountry = false;
        if (!empty($city->id_region))
        {
            $arrRegion = $city->idRegion->getAttributes(true, true);
            if (!empty($city->id_country))
            {
                $arrCountry = $city->idCountry->getAttributes(true, true);
            }
        }
        $rez = array('location'=>array(
            'country' => $arrCountry, 
            'region' => $arrRegion, 
            'city'=> $arrCity));
        $cityPage = array_merge($this->GetByAddress(3, $city->id), $rez);
        return $cityPage;
    }
    
    public function sendMail($to, $subject, $message, $dopHeaders = false) {
        $headers= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        /* дополнительные шапки */
        $headers .= "From: onlineMap.org <no-reply@onlineMap.org>\r\n";
        if (is_array($dopHeaders)) $headers = array_merge ($headers, $dopHeaders);
        mail($to, $subject, $message, $headers);
    }
    
    /**
     * Получить всплывающую подсказку. Единоразово - после перезагрузки странички
     */
    protected function getPopup($value) {
        $data = Yii::app()->session->get('popup_' . $value);
        Yii::app()->session->remove('popup_' . $value);
        return ($data === null) ? '' : json_encode($data);
    }
    
    public function logMsg($fileName, $str = null){
        if ($str === null) {
            $str = $fileName;
            $fileName = 'log';
        }
        if ($str === true)  $str = 'true';
        if ($str === false) $str = 'false';
        if ($str === '')    $str = '""';
        if ($str === ' ')   $str = '" "';
        if (is_resource($str)) $str = '{resource}';
        if (is_array($str) or is_object($str)) {
            $str = print_r($str, true);
        }
        file_put_contents(Yii::app()->basePath . '/runtime/' . $fileName . '.txt', date('j.m H:i:s: ') . $str . "\n", FILE_APPEND);
    }
}