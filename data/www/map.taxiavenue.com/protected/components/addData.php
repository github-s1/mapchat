<?php
class addData extends Errors{       
	/*     *     */    
	public function addIcon($file, $hash){      
		if(!empty($file)){
			if ($hash==Yii::app()->session->getSessionID()){  
				$model=new Icon;    
				foreach ($_FILES as $file){
					$fName = $file['name'];
                    $tmpName = $file['tmp_name'];  
				}
                $uploadFile = $_SERVER['DOCUMENT_ROOT'].Yii::app()->request->baseUrl.'/img/mark_icons/'.basename($fName); 
				$model->name = $fName;
                $model->width=0; 
				$model->height=0; 
				if($model->save()){ 
					if (move_uploaded_file($tmpName, $uploadFile)){
						//загружаем только что созданную запись в бд
						$lastId = Yii::app()->db->getLastInsertId(); 
						$objIcon = Icon::model()->findByPk($lastId); 
						//узнаем размеры загруженного файла
                        $imageSize = getimagesize($uploadFile);
                        //записываем эти размеры 
						$objIcon->width = $imageSize[0];  
						$objIcon->height = $imageSize[1]; 
						if ($objIcon->save()){
							//Загружаем уже обновленную запись с реальными размерами
							$objIcon = Icon::model()->findByPk($lastId);  
							$conv = new Converting;  
							$arIcon = $conv->convertModelToArray($objIcon);  
							return $arIcon; 
						}
						return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_SAVE));
					}
				}
				return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_SAVE));
			} 
			else 
				return array('error'=>array('error_code'=>1,'error_msg'=>self::ERROR_AUTH));
		}
		else
			return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_FILDS_EMPTY));
	}
	/*
    public function addMark($id_kind, $id_user, $description, $address, $anonymous=null, $point, $hash, $period){        
		if (isset($id_kind)) { 
			if ($hash==Yii::app()->session->getSessionID()){  
				//$id_user = Yii::app()->user->id; 
				$model = new Mark; 
				$model->id_kind = $id_kind; 
				$model->description = $description;
				$model->period = $period;    
				if (isset($id_user)){
					$model->id_user = $id_user;   
				} 
				else {  
					$model->id_user = Yii::app()->user->id;  
				}
                if (isset($anonymous)&&($anonymous=='y')){  
					$model->anonymous=$anonymous; 
				}
                else
					$model->anonymous='n';
				$model->address = $address;
                $model->active = 'Y';   
				$model->createDatatime = time(); 
				$objKind = Kind::model()->findByPk($model->id_kind);
                if (($objKind->id_type==1)&&(count($point)>1))  
					return array('error'=>array('error_code'=>6,'error_msg'=>self::ERROR_MANY_POINTS)); 
				if($model->save()){  
					$lastId = Yii::app()->db->getLastInsertId();   
					$objMark = Mark::model()->findByPk($lastId); 
					$conv = new Converting;  
					$arMark = $conv->convertModelToArray($objMark);  
					
					$arKind = $conv->convertModelToArray($objKind);    
					 
					$id_icon = $objKind->id_icon;  
					$objIcon = Icon::model()->findByPk($id_icon);  
					$arKind['icon_url']='http://' . $_SERVER['SERVER_NAME'] . Yii::app()->request->baseUrl . '/img/mark_icons/' . $objIcon->name;
                    $arMark['kind'] = $arKind;  
					if (is_array($point)){
						foreach ($point as $one_point) {  
							$arMark['points'][] = $this->addPoint($lastId, $one_point['id_city'], $one_point['lat'], $one_point['lng'], $one_point['order'], $hash);   
						}
					}
                    return $arMark;  
				}
                else {  
					return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_SAVE));
				}
			}
            else 
				return array('error'=>array('error_code'=>1,'error_msg'=>self::ERROR_AUTH));
		}
		else  
			return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_FILDS_EMPTY)); 
	}
	*/
	
	public function addTheme($name=null){
		if (isset($name)){  
			$model = new Theme;  
			$model->name = $name; 
			if ($model->save()){  
				$lastId = Yii::app()->db->getLastInsertId();   
				$objTheme = Theme::model()->findByPk($lastId);  
				$conv = new Converting;  
				$arTheme = $conv->convertModelToArray($objTheme);
                return $arTheme; 
			}
            else {
				return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_SAVE));
			}
		}
        else
			return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_FILDS_EMPTY));
	}
	
	
	public static function addCountry($name_ru=null, $name_en=null, $lat=null, $lng=null){ 
		if(isset($name_ru, $lat, $lng)){ 
			$model = new Country; 
			$model->name_ru = ucfirst($name_ru);  
			if (isset($name_en))
				$model->name_en=ucfirst($name_en);
			else
				$model->name_en=ucfirst(Transliteration::file($name_ru));  
			$model->lat=$lat;
            $model->lng=$lng; 
			if ($model->save()){   
				$conv = new Converting;
               // $arCountry = $conv->convertModelToArray($model);  
				return $model; 
			}
            else 
				return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_SAVE));  
		} 
		else
			return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_FILDS_EMPTY));
	}

    public static function addRegion($name_ru=null, $name_en=null, $lat=null, $lng=null, $id_country=null){ 
		if(isset($name_ru, $lat, $lng, $id_country)){  
			$model = new Region; 
			$model->name_ru = ucfirst($name_ru); 
			if (isset($name_en))
				$model->name_en=ucfirst($name_en);
			else
				$model->name_en=ucfirst(Transliteration::file($name_ru)); 
			$model->lat=$lat;
            $model->lng=$lng;   
			$model->id_country=$id_country;
            if ($model->save()){ 
				$lastId = Yii::app()->db->getLastInsertId();  
				$objRegion = Region::model()->findByPk($lastId); 
				$conv = new Converting;  
				//$arRegion = $conv->convertModelToArray($objRegion);
                return $objRegion; 
			}
			else
				return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_SAVE));  
		}
        else
			return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_FILDS_EMPTY));
	}

    public static function addCity($name_ru=null, $name_en=null, $lat=null, $lng=null, $id_region=null, $northeast_lat=null, $northeast_lng=null, $southwest_lat=null, $southwest_lng=null){

		if(isset($name_ru, $lat, $lng, $id_region)){  
			$model = new City; 
			$model->name_ru = ucfirst($name_ru);  
			if (isset($name_en))  
				$model->name_en=ucfirst($name_en);  
			else
				$model->name_en=ucfirst(Transliteration::file($name_ru));   
			$model->lat=$lat;  
			$model->lng=$lng;
			$model->northeast_lat=$northeast_lat;
			$model->northeast_lng=$northeast_lng;
			$model->southwest_lat=$southwest_lat;
			$model->southwest_lng=$southwest_lng;
			$model->id_region=$id_region; 
			
			if ($model->save()){  
				$objCity = City::model()->findByPk($model->id);  
				$conv = new Converting;
               // $arCity = $conv->convertModelToArray($objCity);   
				return $objCity; 
			}  
			else 
				return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_SAVE));
		}
		else
			return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_FILDS_EMPTY));
	}   

	/**     
         * Функция добавляет город, регион, страну по обращению к гугл геокодеру     
         * @param $LocName название локации, например Киев или Украина     
         * @param $LocType тип локации есть три варианта city, region, country 
         * @param null $nameDoch название дочерней локации
         * @param null $latDoch координаты дочерней локации, думаю что можно убрать 
         * @param null $lngDoch координаты дочерней локации, думаю что можно убрать
         * @param null $id id - родительской локации которая есть уже в базе
         * @param null $cityName - название города     
         * @return array|string
         */
	public static function addLocalityFromGeocoder($LocName, $LocType, $nameDoch=null, $latDoch=null, $lngDoch=null, $id=null, $cityName=null){        
            switch ($LocType){
                case 'country': 
                    $criteria = 'country'; 
                    $model='Country'; 
                    break; 
                case 'region':  
                    $criteria = 'administrative_area_level_1'; 
                    $model='Region';  
                    break; 
                case 'city':
                    $criteria = 'locality'; 
                    $model='City'; 
                    break;
            }
            if (isset($cityName)){
                $LocName=$cityName;
            }
            $params = array(
                'address' =>$LocName,
            //               'sensor' => 'false',
                'language' => 'ru',  
            );  
            //отправляем запрос к геокодеру 
            $response = json_decode(file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?' . http_build_query($params, '', '&')));
		
            if (!empty($response->results)) {	
                foreach ($response->results[0]->address_components as $address){
                    //проверяем что бы локация была того типа который надо 
                    if ($address->types[0]==$criteria){
                        $name = $address->long_name;   
                        $lat = $response->results[0]->geometry->location->lat;  
                        $lng = $response->results[0]->geometry->location->lng; 
                        if($LocType == 'city') {
                            $northeast_lat = $response->results[0]->geometry->bounds->northeast->lat;
                            $northeast_lng = $response->results[0]->geometry->bounds->northeast->lng;
                            $southwest_lat = $response->results[0]->geometry->bounds->southwest->lat;
                            $southwest_lng = $response->results[0]->geometry->bounds->southwest->lng;	
                        }
                        $criteria = new CDbCriteria();  
                        $criteria->condition = 'name_ru=:name or name_en=:name';  
                        $criteria->params = array(':name'=>$name);   
                        $objRes=$model::model()->find($criteria); 

                        if (!isset($objRes)){
                            switch ($LocType){   
                                case 'region':  
                                    if (isset($id)){  
                                        $arAdd = self::addRegion($name, Transliteration::file($name), $lat, $lng, $id);
                                        return self::addLocalityFromGeocoder($arAdd['name_ru'],'city', null, null, null, $arAdd['id'], $cityName);break;
                                    }
                                    return self::addLocalityFromGeocoder($name,'country', $name, $lat, $lng, null, $cityName);break;
                                case 'city':   
                                    $cityName = $name;  
                                    if (isset($id)){ 
                                        $arAdd = self::addCity($name, Transliteration::file($name), $lat, $lng, $id, $northeast_lat, $northeast_lng, $southwest_lat, $southwest_lng);
                                        return $arAdd; 
                                    } 
                                    return self::addLocalityFromGeocoder($name,'region', $name, $lat, $lng, null, $cityName);break;
                                case 'country':  
                                    $arAdd = self::addCountry($name,Transliteration::file($name),$lat,$lng); 
                                    return self::addLocalityFromGeocoder($nameDoch,'region', null, null, null, $arAdd['id'],$cityName);break;
                            }
                        }
                        else {
                            switch ($LocType){ 
                                case 'region': 
                                    return self::addLocalityFromGeocoder($nameDoch,'city', null, null, null, $objRes->id, $cityName); break; 
                                case 'country': 
                                    return self::addLocalityFromGeocoder($nameDoch,'region', null, null, null, $objRes->id, $cityName); break;
                            }
                        } 
                    }
                }
            }	
            else {
                return 'false';
            }
            $conv = new Converting; 
            return $conv->convertModelToArray($objRes); 
	} 
        
        public function addComment($id_mark, $text, $hash){  
            
            if ($hash==Yii::app()->session->getSessionID()){  
                $model = new Comments;      
                $model->id_mark = $id_mark; 
                $model->id_user = Yii::app()->user->id;   
                $model->text = $text;    
                $model->active = 'Y';    
                $model->createDatatime = time();           
                if($model->save()){ 
                    $lastId = Yii::app()->db->getLastInsertId(); 
                    $obj = Comments::model()->findByPk($lastId);  
                    $conv = new Converting;  
                    $arRes = $conv->convertModelToArray($obj);  
                    $mark = Mark::model()->findByPk($id_mark);   
                    $point = Point::model()->findByAttributes(array('id_mark'=>$id_mark));   
                    $city = City::model()->findByPk($point->id_city);   
                    $kind = Kind::model()->findByPk($mark->id_kind);   
                    $userComment = Users::model()->findByPk($model->id_user);   
                    $userMark = Users::model()->findByPk($mark->id_user);  
                    $link = 'http://'.$_SERVER['SERVER_NAME'] . Yii::app()->request->baseUrl .'/'.$city->name_en.'/'.$kind->code.'/'.$id_mark;
    /*отправка письма */   

                    if (isset($userMark->email)&&($userMark->email!='')){   
                            $to= $userMark->email; 
                            /* тема/subject */ 
                            $subject = "Добавлен новый комментарий к метке";
        /* сообщение */ //
                            $message = 'Добрый день! К Вашему <a href="'.$link.'">значку</a> был добавлен новый комментарий от пользователя '.$userComment->name.' ' .$userComment->family.'. Перейдите на сайт onlineMap.org для просмотра комментария.';
        /* Для отправки HTML-почты вы можете установить шапку Content-type. */   
                            $headers= "MIME-Version: 1.0\r\n";   
                            $headers .= "Content-type: text/html; charset=UTF-8\r\n";  
                            /* дополнительные шапки */   
                            $headers .= "From: onlineMap.org <no-reply@onlineMap.org>\r\n";   
                            /* и теперь отправим из */ 
                            mail($to, $subject, $message, $headers);
							
                            //echo 'send';   
                    }  
            /*---*/
                    return $arRes; 
                }   
                else {  
                    return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_SAVE)); 
                }
            }
            else{
                return array('error'=>array('error_code'=>1,'error_msg'=>self::ERROR_AUTH)); 
            }
	}
        
        public function addComment2($id_mark, $text, $user_id){  
            if ($user_id){  
                $model = new Comments;      
                $model->id_mark = $id_mark; 
                $model->id_user = $user_id;   
                $model->text = $text;    
                $model->active = 'Y';    
                $model->createDatatime = time();           
                if($model->save()){ 
                    $lastId = Yii::app()->db->getLastInsertId(); 
                    $obj = Comments::model()->findByPk($lastId);  
                    $conv = new Converting;  
                    $arRes = $conv->convertModelToArray($obj);  
                    $mark = Mark::model()->findByPk($id_mark);   
                    $point = Point::model()->findByAttributes(array('id_mark'=>$id_mark));   
                    if(!isset($point->id_city)) {
                        $city = City::model()->findByPk(1); 
                    } else {
                        $city = City::model()->findByPk($point->id_city); 
                    }
                    $kind = Kind::model()->findByPk($mark->id_kind);   
                    $userComment = Users::model()->findByPk($model->id_user);   
                    $userMark = Users::model()->findByPk($mark->id_user);  
                    $link = 'http://'.$_SERVER['SERVER_NAME'] . Yii::app()->request->baseUrl .'/'.$city->name_en.'/'.$kind->code.'/'.$id_mark;
    /*отправка письма */   

                    if (isset($userMark->email)&&($userMark->email!='')){   
                            $to= $userMark->email; 
                            /* тема/subject */ 
                            $subject = "Добавлен новый комментарий к метке";
        /* сообщение */ //
                            $message = 'Добрый день! К Вашему <a href="'.$link.'">значку</a> был добавлен новый комментарий от пользователя '.$userComment->name.' ' .$userComment->family.'. Перейдите на сайт onlineMap.org для просмотра комментария.';
        /* Для отправки HTML-почты вы можете установить шапку Content-type. */   
                            $headers= "MIME-Version: 1.0\r\n";   
                            $headers .= "Content-type: text/html; charset=UTF-8\r\n";  
                            /* дополнительные шапки */   
                            $headers .= "From: onlineMap.org <no-reply@onlineMap.org>\r\n";   
                            /* и теперь отправим из */ 
                            mail($to, $subject, $message, $headers);
							
                            //echo 'send';   
                    }  
            /*---*/
                    return $arRes; 
                }   
                else {  
                    return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_SAVE)); 
                }
            }
            else{
                return array('error'=>array('error_code'=>1,'error_msg'=>self::ERROR_AUTH)); 
            }
	}
	
	/**     
         * позволяет добавить комментарий к метке
         * @param $id_mark
         * @param $text
         * @return array|string
         */    
	public function addCommentWeb($id_mark, $text){  
            
            if(empty(Yii::app()->user->id)){
                return array('error'=>array('error_code'=>1,'error_msg'=>self::ERROR_AUTH));
            }

            $model = new Comments;      
            $model->id_mark = $id_mark; 
            $model->id_user = Yii::app()->user->id; 
            $model->text = $text;    
            $model->active = 'Y';    
            $model->createDatatime = time();           
            if($model->save()){ 
                $lastId = Yii::app()->db->getLastInsertId(); 
                $obj = Comments::model()->findByPk($lastId);  
                $conv = new Converting;  
                $arRes = $conv->convertModelToArray($obj);
				
				
				$mark = Mark::model()->findByPk($id_mark);   
                    $point = Point::model()->findByAttributes(array('id_mark'=>$id_mark));   
                   if(isset($point->id_city)) $city = City::model()->findByPk($point->id_city);   
                    $kind = Kind::model()->findByPk($mark->id_kind);   
                    $userComment = Users::model()->findByPk($model->id_user);   
                    $userMark = Users::model()->findByPk($mark->id_user);  
                   if(isset($city->name_en)) $link = 'http://'.$_SERVER['SERVER_NAME'] . Yii::app()->request->baseUrl .'/'.$city->name_en.'/'.$kind->code.'/'.$id_mark;
				   else $link = "http://".$_SERVER['SERVER_NAME']."/Dnepropetrovsk/general/".$id_mark;
    			/*отправка письма */   

					$query = "SELECT login 
			FROM  `users` 
			WHERE  `id` = " . intval($mark->id_user);
					 $command = Yii::app()->db->createCommand($query);
					 $dataReader = $command->query();
					 $data=$dataReader->readAll();
					 
					 $query = "SELECT * 
			FROM  `users` 
			WHERE  `id` = " . intval($model->id_user);
					 $command = Yii::app()->db->createCommand($query);
					 $dataReader = $command->query();
					 $data2=$dataReader->readAll();

                    if (isset($data[0]['login'])&&($data[0]['login']!='')){   
                            $to= $data[0]['login']; 
                            /* тема/subject */ 
                            $subject = "Добавлен новый комментарий к метке";
        /* сообщение */ //
                            $message = 'Добрый день! К Вашему <a href="'.$link.'">значку</a> был добавлен новый комментарий от пользователя '.$data2[0]['name'].' ' .$data2[0]['family'].'. Перейдите на сайт onlineMap.org для просмотра комментария.';
        /* Для отправки HTML-почты вы можете установить шапку Content-type. */   
                            $headers= "MIME-Version: 1.0\r\n";   
                            $headers .= "Content-type: text/html; charset=UTF-8\r\n";  
                            /* дополнительные шапки */   
                            $headers .= "From: onlineMap.org <no-reply@onlineMap.org>\r\n";   
                            /* и теперь отправим из */ 
                            mail($to, $subject, $message, $headers);
							
                            //echo 'send'.$to;   
                    } 
				
				
                return $arRes; 
            }   
            else {  
                return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_SAVE)); 
            }
	}

	public static function addMarkCountry($id_mark, $id_country){
		$mark_country = new MarkCountry;
		$mark_country->id_mark = $id_mark;
		$mark_country->id_country = $id_country;
		if($mark_country->save()) {
			return $mark_country;
		} else {
			return false;
		}
	}
	
	public static function addMarkRegion($id_mark, $id_region){
		$mark_region = new MarkRegion;
		$mark_region->id_mark = $id_mark;
		$mark_region->id_region = $id_region;
		if($mark_region->save()) {
			return $mark_region;
		} else {
			return false;
		}
	}
	
	public static function addMarkCity($id_mark, $id_city){
		$mark_city = new MarkCity;
		$mark_city->id_mark = $id_mark;
		$mark_city->id_city = $id_city;
		if($mark_city->save()) {
			return $mark_city;
		} else {
			return false;
		}
	}

    public static function addPoint($id_mark, $lat, $lng, $order=0){   
		$model = new Point();         
		$model->id_mark = $id_mark;  
		$model->lat = $lat;   
		$model->lng = $lng;  
		$model->order = $order;  
		if ($model->save()){   
			$conv = new Converting;  
			$arRes = $conv->convertModelToArray($model);  
			return $arRes;
		}
		else {
			return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_SAVE)); 
		}
	}   


	public function addPhoto($id_mark, $file){ 
		if(!empty($file)){ 
			$model=new Photo;   
			foreach ($_FILES as $file){  
				$fName = $file['name']; 
				$tmpName = $file['tmp_name'];  
			}
			$uploadFile = $_SERVER['DOCUMENT_ROOT'].Yii::app()->request->baseUrl.'/img/mark_photos/'.basename($fName);
			$model->id_mark = $id_mark;  
			$model->name = $fName;
			//return $model->getAttributes();
			if($model->save()){ 
			//echo Yii::app()->request->baseUrl.'/img/mark_icons/';   
			//echo $_SERVER['DOCUMENT_ROOT'].Yii::app()->request->baseUrl.'/img/mark_icons/';   
				if(move_uploaded_file($tmpName, $uploadFile)){    
					//узнаем размеры загруженного файла
					$conv = new Converting;   
					$arPhoto = $conv->convertModelToArray($model);   
					return $arPhoto;
				}
				else {
					return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_FILE_SAVE)); 
				}
			} 
			else {
				return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_SAVE));
			}	
		} 
		else   
			return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_FILE));
	}

    /**Загружает и добавляет в бд аудиозапись вида     * @param $id_mark     * @param $file     * @return array|string     */   

	public function addAudio($id_mark, $file, $hash){   
		if ($hash==Yii::app()->session->getSessionID()){   
			if(!empty($file)){ 
				//echo '<pre>'; print_r($file); echo '</pre>';    
				$model=new Audio; 
				foreach ($_FILES as $file){  
					if ($file['size']<=15000000){   
						$fName = $file['name'];  
						$tmpName = $file['tmp_name'];
					}
					else
						return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_FILE));  
				}
				$uploadFile = $_SERVER['DOCUMENT_ROOT'].Yii::app()->request->baseUrl.'/audio/'.basename($fName); 
				$model->id_mark = $id_mark; 
				$model->name = $fName;  
				if($model->save()){  
					if (move_uploaded_file($tmpName, $uploadFile)){   
						//загружаем только что созданную запись в бд   
						$lastId = Yii::app()->db->getLastInsertId();   
						$objData = Audio::model()->findByPk($lastId);   
						$conv = new Converting;  
						$arData = $conv->convertModelToArray($objData); 
						return $arData;
					}  
					return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_FILE_SAVE));
				}
				
				return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_SAVE));  
			}

			return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_FILE));
		}
		else   
		return array('error'=>array('error_code'=>1,'error_msg'=>self::ERROR_AUTH));
	}
	/*
    public function addAvatar($id_user, $file){   
			if(!empty($file)){       
				$User = Users::model()->findByPk($id_user); 
				
				$model = new Avatar;   
				
				foreach ($_FILES as $file){  
					$fName = $file['name'];  
					$tmpName = $file['tmp_name'];     
				}    
				$uploadFile = $_SERVER['DOCUMENT_ROOT'].Yii::app()->request->baseUrl.'/img/users_avatar/'.basename($fName);  
				$model->big_photo = $fName;
                $file = explode('.', $fName);    
				$previewName =  $file[0].'_small.'.$file[1];   
				$model->small_photo = $previewName; 
			
				if($model->save()){   
					//echo Yii::app()->request->baseUrl.'/img/mark_icons/'; 
					//echo $_SERVER['DOCUMENT_ROOT'].Yii::app()->request->baseUrl.'/img/mark_icons/';
                    if (move_uploaded_file($tmpName, $uploadFile)){ 
						//$ih = new CImageHandler(); 
						
						//Инициализация Yii::app()->ih->load($uploadFile) 
						//Загрузка оригинала картинки                            ->thumb('24', '24') 
						//Создание превьюшки шириной 200px                            ->save($_SERVER['DOCUMENT_ROOT'].Yii::app()->request->baseUrl.'/img/users_avatar/small/'.basename($previewName)); //Сохранение превьюшки в папку thumbs                       
						//загружаем только что созданную запись в бд  
						$User->id_avatar = $model->id;   
						if($User->save()) {
							Avatar::model()->deleteByPk($User->id_avatar);
						}
						
						//узнаем размеры загруженного файла  
						$conv = new Converting;   
						$arAvatar = $conv->convertModelToArray($model);
						
						  
						
                        return $arAvatar;       
					}      
					else  {      
						return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_FILE_SAVE));
					}	
				}       
				else {
					return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_SAVE));
				}	
			}      
			else {
				return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_FILE)); 
			}	
	}
	*/
	
	  public function addAvatar($id_user, $file){   
			if(!empty($file)){  
				
				$User = Users::model()->findByPk($id_user); 
			
				$_FILES = Controller::FilesProcessing("Avatar", "big_photo");
				
				$model = Avatar::model()->findByPk($User->id_avatar);
				if(empty($model)) {
					$model = new Avatar;  
					$model->big_photo = 'avatar';
					$model->small_photo = 'avatar';				
				}
				if($model->save()){  
					$old_avatar = $User->id_avatar;
					$User->id_avatar = $model->id;   
					$User->save();
					//узнаем размеры загруженного файла  
					$conv = new Converting;   
					$arAvatar = $conv->convertModelToArray($model);

					return $arAvatar;       	
				}       
				else {
					return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_SAVE));
				}	
			}      
			else {
				return array('error'=>array('error_code'=>2,'error_msg'=>self::ERROR_FILE)); 
			}	
	}
	
    /**
     * 
     */
    public static function getObjResponse($response) {
        //if (empty($response->results[1]) or empty($response->results[0])) return false;
        $isFirst = false;
        if (!empty($response->results[0])) {
            foreach ($response->results[0]->address_components as $address){
                if ($address->types[0] == 'locality') {
                    $isFirst = true;
                    break;
                }
            }
        }
        if ($isFirst) return $response->results[0];
        if (!empty($response->results[1])) return $response->results[1];
        return false;
    }

    public static function GetAdressByCoordinats($lat=null,$lng=null) {

	$result = array('id_country' => false, 'id_region' => false, 'id_city' => false);
	if (isset($lat) && (isset($lng))){
            $params = array(
                'latlng' => $lat .','. $lng,
                'sensor' => 'false',
                'language'=>'ru'
            );
            $response = json_decode(file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?' . http_build_query($params, '', '&')));
            $objResponse = self::getObjResponse($response); // return results[0] | results[1]

            if ($objResponse !== false) {
				$city = null;
				$region = null;
				$country = null;
                $addressMark = ''; // дом, улица значка
				foreach ($objResponse->address_components as $address){
					
					switch ($address->types[0]){
						case 'locality':  
							$city = $address->long_name;
							break;
						case 'administrative_area_level_1': 
							$region = $address->long_name;							
							break;	
						case 'country': 
							$country = $address->long_name;
							break; 
					}
					if ($address->types[0] == 'street_number') $addressMark .= $address->long_name.', ';
                    if ($address->types[0] == 'route') $addressMark .= $address->long_name;
				}
                                
                /*------------------------------------------------------------------------*/
                $params = array(
    				'address' => $city,
    				//'sensor' => 'false',
    				'language' => 'ru',  
    			);
                $objResponse = json_decode(file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?' . http_build_query($params, '', '&')));
                
                $objResponse = self::getObjResponse($objResponse); // return results[0] | results[1]
                //var_dump($objResponse); die;
                $city1 = null;
				$region1 = null;
				$country1 = null;
                                if($objResponse) {
                                    foreach ($objResponse->address_components as $address){

                                            switch ($address->types[0]){
                                                    case 'locality':  
                                                            $city1 = $address->long_name;
                                                            break;
                                                    case 'administrative_area_level_1': 
                                                            $region1 = $address->long_name;							
                                                            break;	
                                                    case 'country': 
                                                            $country1 = $address->long_name;
                                                            break; 
                                            }

                                    }
                                }
                /*--------------------------------------------------------------------------*/
                //var_dump($region); die; //echo json_encode($city);exit; 
                if ($addressMark == '') {
                    if (!empty($city)) $addressMark = $city;
                        else $addressMark = $region;
                }
                $result['addressMark'] = $addressMark;

				if(!empty($country1)) {
					$obj_country = self::SearchLocality($country1, 'country');
					$result['id_country'] = $obj_country->id;
					
					if(!empty($region1)) {
						$obj_region = self::SearchLocality($region1, 'region', $obj_country->id);
						$result['id_region'] = $obj_region->id;
						
						if(!empty($city1)) {
							$obj_city = self::SearchLocality($city1, 'city', $obj_region->id);
							$result['id_city'] = $obj_city->id;
						
						}
					}
				}
            } 	
        } 
		/* Меняем формат адреса по http://alcopribor.ru/mantis/view.php?id=79*/
		if(strpos($result['addressMark'], ",") != false) {
			$v = explode(",", $result['addressMark']);
			$result['addressMark'] = trim($v[1]) . " " . trim($v[0]);
		}
		return $result;		
	}
	
	public static function SearchLocality($name, $type, $id_loc = null){        
		switch ($type){
			case 'country': 
				$criteria = 'country'; 
				$model='Country'; 
				break; 
			case 'region':  
				$criteria = 'administrative_area_level_1'; 
				$model='Region';  
				break; 
			case 'city':
				$criteria = 'locality'; 
				$model='City'; 
				break; 	
		}
		$cr = new CDbCriteria();
		$cr->condition = 'name_ru=:name or name_en=:name';  
		$cr->params = array(':name'=>$name);   
		$objRes = $model::model()->find($cr);
	
		if (empty($objRes)){
			$params = array(
				'address' =>$name,
				//'sensor' => 'false',
				'language' => 'ru',  
			);
			
			$response = json_decode(file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?' . http_build_query($params, '', '&')));
			if (!empty($response->results))
			{	
				foreach ($response->results[0]->address_components as $address){
					//проверяем что бы локация была того типа который надо 
					if ($address->types[0]==$criteria){    
						$name = $address->long_name;   
						$lat = $response->results[0]->geometry->location->lat;  
						$lng = $response->results[0]->geometry->location->lng; 
						if($type == 'city') {
							$northeast_lat = $response->results[0]->geometry->bounds->northeast->lat;
							$northeast_lng = $response->results[0]->geometry->bounds->northeast->lng;
							$southwest_lat = $response->results[0]->geometry->bounds->southwest->lat;
							$southwest_lng =  $response->results[0]->geometry->bounds->southwest->lng;	
						}
						 
						
						switch ($type){   
							case 'region':  
								$objRes = self::addRegion($name, Transliteration::file($name), $lat, $lng, $id_loc);
								return $objRes;
							case 'city':   
								$objRes = self::addCity($name, Transliteration::file($name), $lat, $lng, $id_loc, $northeast_lat, $northeast_lng, $southwest_lat, $southwest_lng);
								return $objRes;
							case 'country':  
								$objRes = self::addCountry($name,Transliteration::file($name),$lat,$lng); 
								return $objRes;
						} 
					}
				}
			}	
				
		} else {
			return $objRes;
		}
		  
		//отправляем запрос к геокодеру 
	}
}