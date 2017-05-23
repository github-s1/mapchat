<?php
class Kinds
{	

	public static function GetKindInfo( Kind $kind, $is_mobile = false)
	{
		$Rez = array();
		if(!empty($kind)) {  
			$Rez = $kind->getAttributes(); 
			unset($Rez['id_user']);
			//unset($Rez['id_icon']);
			if($is_mobile) {
				$Rez['icon_url'] = Icons::GetIconInfo($kind->idIcon)['icon_url'];
			} 
		}	
		return $Rez;
	}
	
    /**
     * Получить все существующие виды
     */
	public static function getAllKindsNoStatic(){
        $arKinds=Kind::model()->findAll();
		$arNewKinds = array();
		if(!empty($arKinds)) {
			foreach($arKinds as $i => $kind){
				$arNewKinds[$i] = Kinds::GetKindInfo($kind, true);
			}
		}	
        return $arNewKinds;
    }
	
	public static function addKind($id_theme = -1, $id_icon = -1, $id_type=null, $name_ru=null,$code=null,$description=null, $id_user=null, $is_mobile = false, $color = "#ff0000"){
		if (isset($id_theme, $name_ru, $id_type, $id_user)){ 
				$criteria = new CDbCriteria;
                $criteria->condition='name_ru=:name_ru and id_theme=:id_theme and id_type=:id_type'; 
				$criteria->params=array('name_ru'=>$name_ru,'id_theme'=>$id_theme,'id_type'=>$id_type);
                $objKind = Kind::model()->find($criteria);
				
                if(empty($objKind)){
					$objKind = new Kind;
					/*
					if ($id_theme == -1) {
						return array('error'=>array('error_code'=>2,'error_msg'=>Errors::ERROR_NOT_SAVE_IN_THIS_THEME)); 
					} 
					*/
					if ((empty($id_icon)) || ($id_icon == 0)){ 
						$id_icon = -1;
					} 
					
					if($color == '') $color = "#ff0000";
					
					$attr = array('id_theme' => $id_theme, 'id_icon' => $id_icon, 'id_type' => $id_type, 'name_ru' => $name_ru, 'code' => $code, 'description' => $description, 'id_user' => $id_user, 'color' => $color);
					
					$objKind->SetParams($attr);
					
					if($id_type == 2) $create_Y = $color; else $create_Y = false;
					
					$idIcon = Icons::addIconToNewKind($objKind->code, true, $create_Y);
					if($id_icon == -1) {
						if ($idIcon == false) return array('error'=>array('error_code'=>5,'error_msg'=>'Не удалось сохранить изображение для этого вида.'));
	
						$objKind->id_icon = $idIcon->id;
					} else {
						$objKind->id_icon = $id_icon;
					}
                    if($objKind->save()){
                        $result['kind'] = Kinds::GetKindInfo($objKind, $is_mobile);
                        $result['icon'] = $idIcon->getAttributes();
                        return $result;
					}
                    else{
						return array('error'=>array('error_code'=>2,'error_msg'=>Errors::ERROR_SAVE)); 
					} 
				} 
				else {
					return array('error'=>array('error_code'=>13,'error_msg'=>Errors::ERROR_KIND_EXIST));
				}	
		}
        else
			return array('error'=>array('error_code'=>2,'error_msg'=>Errors::ERROR_FILDS_EMPTY)); 
	}
	
}
