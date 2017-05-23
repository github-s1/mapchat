<?php
class Marks
{	
    public static function GetMarkById($id, $is_mobile = false){
        if (!empty($id)){
            $mark = Mark::model()->findByPk($id);
            if($is_mobile) {
                $MarkInfo = self::GetMobileMarkInfo($mark);
            } else {
                $MarkInfo = self::GeMarkInfo($mark);
            }
            return $MarkInfo;
        } else {
            echo json_encode(array('response'=>array('error' => array('error_code' => 2, 'error_msg' => Errors::ERROR_FILDS_EMPTY)))); exit;
        }	
    }
	
	
	public static function GetMobileMarkInfo( Mark $mark)
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
	
	public static function GeMarkInfo(Mark $mark)
	{
            $Rez = array();
            if(!empty($mark)) {  
                $Rez['mark'] = $mark->getAttributes();  

                $Rez['kind'] = Kinds::GetKindInfo($mark->idKind, false);
                $Rez['icon'] = Icons::GetIconInfo($mark->idKind->idIcon);	
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
                $Rez['user'] = Controller::GetUserById($mark->id_user);
                $Rez['comments'] = Controller::GetCommentsByMarkId($mark->id);	
                $Rez['type'] = Controller::GetTypeByKindId( $Rez['kind']['id']);

//                $Rez['city'] = self::getFirstCityMark($mark->id);
                $Rez['location'] = Controller::GetLocationByMarkId($mark->id);

            }	
            return $Rez;
	}
	
	public static function AddMarkPoints($mark_id, $points, $mark = false)
	{
		if (is_array($points)){
			$id_countries = array();
			$id_regions = array();
			$id_cities = array();
			
			Point::model()->deleteAllByAttributes(array('id_mark'=>$mark_id));
			MarkCity::model()->deleteAllByAttributes(array('id_mark'=>$mark_id));
			MarkRegion::model()->deleteAllByAttributes(array('id_mark'=>$mark_id));
			MarkCountry::model()->deleteAllByAttributes(array('id_mark'=>$mark_id));
		
			foreach ($points as $p) { 
				$order = 0;
				if(isset($p['order'])) {
					$order = $p['order'];
				}
                
				addData::addPoint($mark_id,$p['lat'], $p['lng'], $order);
                
                                // Тут и ДУБЛИРУЮТСЯ (dnipropetrovsk != dnepropetrovsk)
                                $res = addData::GetAdressByCoordinats($p['lat'], $p['lng']);
                                //var_dump($res['addressMark']); die;
                                if ($mark !== false and isset($res['addressMark'])) {
                                    $mark->address = $res['addressMark'];
                                    $mark->save();
                                }


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
                        
			//!!! тут уже города дублируются в БД
            
			$id_countries = array_unique($id_countries );
			$id_regions = array_unique($id_regions );
			$id_cities = array_unique($id_cities);
			
			
			if(isset($id_countries)) {
				foreach($id_countries as $id) {
					addData::addMarkCountry($mark_id, $id);
				}
			}
			if(isset($id_regions)) {
				foreach($id_regions as $id) {
					addData::addMarkRegion($mark_id, $id);
				}
			}
			if(isset($id_cities)) {
				foreach($id_cities as $id) {
					addData::addMarkCity($mark_id, $id);
				}	
			}
			return true;
		} else {  
			return false;
		}
	}
	
	public static function getFirstCityMark($id_mark) {
		$city = MarkCity::model()->findByAttributes(array('id_mark' => $id_mark), array('order'=>'id ASC'), array('limit'=>1));
		if(!empty($city)) {
			return $city->city->getAttributes();
		} else {
			return NULL;
		}
	}
	
	public static function addMarkWeb($id_kind, $id_user, $createDatatime, $description, $address, $anonymous, $point, $period, $is_mobile = false)
	{
            if (isset($id_kind, $id_user, $point, $createDatatime)){
                $mark = new Mark;

                $attr = array('id_kind' => $id_kind, 'id_user' => $id_user, 'createDatatime' => $createDatatime, 'description' => $description, 'address' => $address, 'anonymous' => $anonymous, 'period' => $period, 'active' => 'Y');
                $mark->SetParams($attr);

                $objKind = Kind::model()->findByPk($mark->id_kind);
                if (($objKind->id_type==1)&&(count($point)>1)) { 
                    return array('error'=>array('error_code'=>2,'error_msg'=>addData::ERROR_MANY_POINTS)); 
                }

                if($mark->save()){

                    if(!self::AddMarkPoints($mark->id, $point)) {
                        return array('error'=>array('error_code'=>2, 'error_msg'=>'Точки должны передаваться в виде массива.'));
                    } 
                    if($is_mobile) {
                        $MarkInfo = self::GetMobileMarkInfo($mark);
                    } else {
                        $MarkInfo = self::GeMarkInfo($mark);
                    }
                    return $MarkInfo;	
                }
                else {  
                    return array('error'=>array('error_code'=>2,'error_msg' =>addData::ERROR_SAVE));
                }
            }
            else {
                $result = array('error'=>array('error_code'=>2,'error_msg'=>updateData::ERROR_FILDS_EMPTY));
            }

	}
	
    /**
     * добавить метку в бд
     */
	public static function addMark($id_kind, $id_user, $createDatatime, $description, $address, $anonymous, $point, $period, $is_mobile = false)
	{
        $createDatatime = time(); // Какого-то хера время создания передавалось из js
		if (isset($id_kind, $id_user, $point, $createDatatime)){
			$mark = new Mark;
			$objKind = Kind::model()->findByPk($id_kind);
			
			$attr = array('id_kind' => $id_kind, 'id_user' => $id_user, 'createDatatime' => $createDatatime, 'description' => $description, 'address' => $address, 'anonymous' => $anonymous, 'period' => $period, 'active' => 'Y', 'color' => $objKind->color);
			$mark->SetParams($attr);

			if (($objKind->id_type==1)&&(count($point)>1)) { 
			    
				return array('error'=>array('error_code'=>2,'error_msg'=>addData::ERROR_MANY_POINTS)); 
			}
            
			if($mark->save()){
				//return 2111111111;
				if(!self::AddMarkPoints($mark->id, $point, $mark)) { // ТУТ КОСЯК
					return array('error'=>array('error_code'=>2, 'error_msg'=>'Точки должны передаваться в виде массива.'));
				} //return 1111111111;
				if($is_mobile) {
					$MarkInfo = self::GetMobileMarkInfo($mark);
				} else {
					$MarkInfo = self::GeMarkInfo($mark);
				}
                return $MarkInfo;	
			}
			else {  
			   	return array('error'=>array('error_code'=>2,'error_msg' =>addData::ERROR_SAVE));
			}
		}
        else {
            $result = array('error'=>array('error_code'=>2,'error_msg'=>updateData::ERROR_FILDS_EMPTY));
        }

	}
	
	public static function updateMark($id = null, $id_kind = null, $id_user = null, $description = null, $address = null, $point = null, $active = null, $period = null, $is_mobile = false)
	{
		if (isset($id)) {
            $mark = Mark::model()->findByPk($id);
            if ($mark->id_user == $id_user){
				if (isset($id_kind)){
					$objKind = Kind::model()->findByPk($id_kind);
					if (empty($objKind)) {
						return array('error'=>array('error_code'=>13,'error_msg'=>Errors::ERROR_KIND_NOT_EXIST));
					} 
				}
				if (isset($id_user)){
					$objUser = Users::model()->findByPk($id_user);
					if (empty($objUser)) {
						return array('error'=>array('error_code'=>13,'error_msg'=>Errors::ERROR_USER_NOT_EXIST));
					} 
				}
				$attr = array('id_kind' => $id_kind, 'id_user' => $id_user, 'description' => $description, /*'address' => $address,*/ 'active' => $active, 'period' => $period);
				$mark->SetParams($attr);
				
				if ($mark->save()){
					
					if (isset($point)){
						if(!self::AddMarkPoints($mark->id, $point)) {
							return array('error'=>array('error_code'=>2, 'error_msg'=>'Точки должны передаваться в виде массива.'));
						}
					}
					
					if($is_mobile) {
						$MarkInfo = self::GetMobileMarkInfo($mark);
					} else {
						$MarkInfo = self::GeMarkInfo($mark);
					}
					return $MarkInfo;
				}
                else {
                    return array('error'=>array('error_code'=>2,'error_msg'=>Errors::ERROR_SAVE));
                }
            }
            else{
                return array('error'=>array('error_code'=>2,'error_msg'=>Errors::ERROR_USER_MARK));
            }
        }
		else {
			return array('error'=>array('error_code'=>2,'error_msg'=>Errors::ERROR_FILDS_EMPTY));
		}
		
	}
	
	public static function GetMarksBy($id_theme = null, $id_city = null, $code_sorting = 0, $is_mobile = false, $bounds = false)
	{
        
        $criteria = new CDbCriteria;

        $criteria->condition = 'm.click_spam < 3';
        if ($id_city) $criteria->condition .= ' AND id_city='.$id_city;
        $criteria->join = 'INNER JOIN mark AS m ON m.id=id_mark';
        if (is_array($bounds)) {
            $criteria->join .= ' INNER JOIN point AS p ON p.id_mark=t.id_mark';
            $criteria->condition .= ' AND p.lat<' . $bounds[1][0] . ' AND p.lat>' . $bounds[0][0] . ' AND p.lng<' . $bounds[1][1] . ' AND p.lng>' . $bounds[0][1];
        }
        $criteria->order = 'id ASC';
		return MarkCity::model()->findAll($criteria);
        
		/*if ($code_sorting == 1){
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
		*
		$marks = Mark::model()->findAllBySql($query);
		return $marks;
		$mark_array = array();
		if (!empty($marks)){
            foreach($marks as $mark) {
				if($is_mobile) {
					$mark_array[] = self::GetMobileMarkInfo($mark);
				} else {
					$mark_array[] = self::GeMarkInfo($mark);
				}
			}	
		} 
		
		return $mark_array;*/
		
	}
	
}
