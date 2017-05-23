<?php
class Themes
{	

	public static function getAllThemes(){
        $arTheme = Theme::model()->findAll();
		$arrayTheme = array();
		if(!empty($arTheme)) {
			foreach($arTheme as $i => $theme){
				$arrayTheme[$i] = $theme->getAttributes();
			}
		}	
        return $arrayTheme;
    }
	
}
