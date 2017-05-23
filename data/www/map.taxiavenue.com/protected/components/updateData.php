<?php

class updateData extends Errors{
    public function loadModel($nameModel, $id)
    {
        $model=$nameModel::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

    public function updateTheme($id_theme=null,$name=null){
        if ((isset($name)) && (isset($id_theme))){
            $objTheme = Theme::model()->findByPk($id_theme);
            if ($objTheme){
                $model=$this->loadModel('Theme',$id_theme);
                $model->name=$name;
                if ($model->save()){
                    $objTheme = Theme::model()->findByPk($id_theme);
                    $conv = new Converting;
                    $arTheme= $conv->convertModelToArray($objTheme);
                    return $arTheme;
                }
                else {
                    return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_SAVE));
                }
            }
            else{
                return array('error'=>array('error_code'=>13,'error_msg'=>self::ERROR_THEME_NOT_EXIST));
            }
        }
        else
            return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_FILDS_EMPTY));
    }



    public function updateKind($id_kind=null, $id_user=null, $id_theme=null,$id_icon=null, $id_type=null, $name_ru=null,$code=null,$description=null, $site=null, $lider=null, $hash){
        if ((isset($id_kind))&&(isset($id_user))) {
            if ($hash==Yii::app()->session->getSessionID()){
                $model=$this->loadModel('Kind',$id_kind);
                if ($model->id_user == Yii::app()->user->id){
                    if (isset($name_ru))
                        $model->name_ru=$name_ru;
                    if (isset($id_type)){
                        $objType = Type::model()->findByPk($id_type);
                        if ($objType)
                            $model->id_type=$id_type;
                        else
                            return array('error'=>array('error_code'=>13,'error_msg'=>self::ERROR_TYPE_NOT_EXIST));
                    }
                    if (isset($id_theme)){
                        $objTheme = Theme::model()->findByPk($id_theme);
                        if ($objTheme)
                            $model->id_theme=$id_theme;
                        else
                            return array('error'=>array('error_code'=>13,'error_msg'=>self::ERROR_THEME_NOT_EXIST));

                    }
                    if (isset($id_icon)){
                        $objIcon = Icon::model()->findByPk($id_icon);
                        if ($objIcon)
                            $model->id_icon=$id_icon;
                        else
                            return array('error'=>array('error_code'=>13,'error_msg'=>self::ERROR_ICON_NOT_EXIST));
                    }

                    if ((isset($code))&&($code!='')){
                        $criteria = new CDbCriteria;
                        $criteria->condition='code=:code';
                        $criteria->params=array(':code'=>$code);
                        $objKind = Kind::model()->find($criteria);
                        if (!isset($objKind)||($objKind->id==$id_kind)){
                            $model->code=$code;
                        }
                        else
                            return array('error'=>array('error_code'=>13,'error_msg'=>self::ERROR_CODE_EXIST));
                    }
                    else{
                        $model->code=Transliteration::file($name_ru);
                    }
                    if (isset($description)){
                        $model->description=$description;
                    }
                    if (isset($site)){
                        $model->site=$site;
                    }
                    if (isset($lider)){
                        $model->lider=$lider;
                    }
                    if ($model->save()){
                        $objKind = Kind::model()->findByPk($id_kind);
                        $conv = new Converting;
                        $arKind = $conv->convertModelToArray($objKind);
                        return $arKind;
                    }
                else {
                    return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_SAVE));
                }
            }
                else
                    return array('error'=>array('error_code'=>1,'error_msg'=>self::ERROR_USER_KIND));
            }
            else
                return array('error'=>array('error_code'=>1,'error_msg'=>self::ERROR_AUTH));
        }
        else
            return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_FILDS_EMPTY));
    }

    public static function updateMark($id=null, $id_kind=null, $id_user=null, $description=null, $address=null, $active=null, $point=null, $period){
        if (isset($id)) {
            $mark = $this->loadModel('Mark',$id);
            if ($mark->id_user == $id_user){
				if (isset($id_kind)){
					$objKind = Kind::model()->findByPk($id_kind);
					if ($objKind) {
						$mark->id_kind = $id_kind;
					} else {
						return array('error'=>array('error_code'=>13,'error_msg'=>self::ERROR_KIND_NOT_EXIST));
					}
				}
				if (isset($id_user)){
					$objUser = Users::model()->findByPk($id_user);
					if ($objUser)
						$mark->id_user = $id_user;
					else
						return array('error'=>array('error_code'=>13,'error_msg'=>self::ERROR_USER_NOT_EXIST));
				}
				if (isset($description)){
					$mark->description = $description;
				}
				if (isset($period)){
					$mark->period = $period;
				}
				 
				if (isset($address)){
					$mark->address = $address;
				}
				if (isset($active)){
					$mark->active = $active;
				}
				if ($mark->save()){
					
					$conv = new Converting;  
					$arMark = $conv->convertModelToArray($mark);  
					$objKind = $mark->idKind;
					$arKind = $conv->convertModelToArray($objKind);    
					 
					$arMark['kind'] = $arKind;
					$arMark['kind']['icon'] = $objKind->idIcon->getAttributes(); 			
					$arMark['kind']['icon']['icon_url'] =   'http://'.Yii::app()->params->baseUrl.'/img/mark_icons/'.$arMark['kind']['icon']['name'];
					unset($arMark['kind']['icon']['name']);
		
					/*апдейтим точки или выводим старые*/
	
					if (isset($point)){						
						$id_countries = array();
						$id_regions = array();
						$id_cities = array();
						Point::model()->deleteAllByAttributes(array('id_mark'=>$mark->id));
						MarkCity::model()->deleteAllByAttributes(array('id_mark'=>$mark->id));
						MarkRegion::model()->deleteAllByAttributes(array('id_mark'=>$mark->id));
						MarkCountry::model()->deleteAllByAttributes(array('id_mark'=>$mark->id));
						
						foreach ($point as $p) { 
							$order = 0;
							if(isset($p['order'])) {
								$order = $p['order'];
							}
							$arMark['points'][] = addData::addPoint($mark->id,$p['lat'], $p['lng'], $order);  
							$res = addData::GetAdressByCoordinats($p['lat'], $p['lng']); 
							if(!empty($res['id_country'])) {
								$id_countries[] = $res['id_country'];
							} 
							if(!empty($res['id_region'])) {
								$id_regions[] = $res['id_region'];
							}
							if(!empty($res['id_city'])) {
								$id_cities[] = $res['id_city'];
							}
						}
						
						$id_countries = array_unique($id_countries );
						$id_regions = array_unique($id_regions );
						$id_cities = array_unique($id_cities);
						
						
						if(isset($id_countries)) {
							foreach($id_countries as $id) {
								addData::addMarkCountry($mark->id, $id);
							}
						}
						if(isset($id_regions)) {
							foreach($id_regions as $id) {
								addData::addMarkRegion($mark->id, $id);
							}
						}
						if(isset($id_cities)) {
							foreach($id_cities as $id) {
								addData::addMarkCity($mark->id, $id);
							}	
						} 
					}	
					return $arMark;
				}
                else {
                    return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_SAVE));
                }
            }
            else{
                return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_USER_MARK));
            }
        }
		else {
			return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_FILDS_EMPTY));
		}
    }

    public function updatePoint($id_point, $id_mark=null, $id_city=null, $lat=null, $lng=null, $order=0, $hash){
        if ($hash==Yii::app()->session->getSessionID()){
            $model=$this->loadModel('Point',$id_point);
            if (isset($id_mark)){
                $objMark = Mark::model()->findByPk($id_mark);
                if (isset($objMark))
                    $model->id_mark = $id_mark;
                else
                    return array('error'=>array('error_code'=>13,'error_msg'=>self::ERROR_MARK_NOT_EXIST));
            }
            if (isset($id_city)){
                $objCity = City::model()->findByPk($id_city);
                if (isset($objCity))
                    $model->id_city = $id_city;
                else
                    return array('error'=>array('error_code'=>13,'error_msg'=>self::ERROR_CITY_NOT_EXIST));
            }
            if (isset($lat))
                $model->lat = $lat;
            if (isset($lng))
                $model->lng = $lng;
            if (isset($order))
                $model->order = $order;
            if ($model->save()){
                $obj = Point::model()->findByPk($model->id);
                $conv = new Converting;
                $arRes = $conv->convertModelToArray($obj);
                return $arRes;
            }
            else
                return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_SAVE));
        } else
            return array('error'=>array('error_code'=>1,'error_msg'=>self::ERROR_AUTH));
    }

    /**
     * функция увеличивает счетчик "это спам", и если этот счетчик больше двух
     * то снимает флаг активности метки
     * @param $id
     * @return array|string
     */
    public function clickSpam($id, $isMobile = false){
		
	   $model = Mark::model()->findByPk($id);
	   if(isset($_SESSION['clicked'][$id])) {
		   if ($isMobile)  return array('error'=>array('error_code'=>2,'error_msg'=>'Вы уже отправляли заявку'));
		   return array('error'=>array('error_code'=>3));
	   }
		if(!empty($model)) {
			$_SESSION['clicked'][$id] = true;
			$model->click_spam++;
			if ($model->click_spam >= 2){
				$model->active='N';
			}
			if ($model->save()){
                if ($isMobile) return 'success';
				return array(
                    'res' => 'success',
                    'clicked' => $model->click_spam
                );
			} else {
				return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_SAVE));
			}
		} else {
			return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_SAVE));
		}
    }
    
    /**
     * Обновление пользоваьеля через МОБИЛЬНЫЙ
     * дя веба отдельно
     */
    public function updateUser($id_user = false){
        if ($id_user === false) $id_user = Yii::app()->user->id;
        $model = $this->loadModel('Users', $id_user);
        foreach ($model->getAttributes() as $key => $value) {
            if ($v = Yii::app()->request->getPost($key)) $model->$key = $v;
        }
        $model->save();
        return $model->getAttributes(true, true);
    }


    public function updateStatus($id_user,$status, $hash){
        if ($hash==Yii::app()->session->getSessionID()&&($id_user == Yii::app()->user->id)){
            $model = $this->loadModel('StatusUser',$id_user);
            $model -> status = $status;
            $model -> createDatatime = date('Y-m-d H:i:s');
            if ($model->save()){
                $obj = StatusUser::model()->findByPk($model->id);
                $conv = new Converting;
                $arRes = $conv->convertModelToArray($obj);
                return $arRes;
            }
            else {
                return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_SAVE));
            }
        }
        else
            return array('error'=>array('error_code'=>1,'error_msg'=>self::ERROR_AUTH));
    }


}