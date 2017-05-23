<?php
class Icons
{	

	public static function GetIconInfo( Icon $icons)
	{
		$Rez = array();
		if(!empty($icons)) {
			$Rez = $icons->getAttributes(); 			
			$Rez['icon_url'] =  Yii::app()->getBaseUrl(true).'/img/mark_icons/'.$Rez['name'];
			//unset($Rez['name']);
		}	
		return $Rez;
	}
	
	public static function getAllIcons(){
        $icons = Icon::model()->findAll();
        $icons_array = array();
		if(!empty($icons)) {
			foreach($icons as $icon) {
				$icons_array[] = self::GetIconInfo($icon);
			}
		}

        return $icons_array;
    }
	
	public static function hex2rgb($hex) {
	   $hex = str_replace("#", "", $hex);
	
	   if(strlen($hex) == 3) {
		  $r = hexdec(substr($hex,0,1).substr($hex,0,1));
		  $g = hexdec(substr($hex,1,1).substr($hex,1,1));
		  $b = hexdec(substr($hex,2,1).substr($hex,2,1));
	   } else {
		  $r = hexdec(substr($hex,0,2));
		  $g = hexdec(substr($hex,2,2));
		  $b = hexdec(substr($hex,4,2));
	   }
	   $rgb = array($r, $g, $b);
	   //return implode(",", $rgb); // returns the rgb values separated by commas
	   return $rgb; // returns an array with the rgb values
	}

    /**
     * Добавить иконку для нового вида
     * @param string $kindName имя картинки - /img/mark_icons/$kindName.png
     */
    public static function addIconToNewKind($kindName, $returnObj = false, $create_Y = false) {
        $imgUrl = Yii::getPathOfAlias('webroot.img.mark_icons'); // папка где лежат иконки
        $defaultFile = $imgUrl . '/general_kind.png';
        $newFile = $imgUrl . '/' . $kindName . '.png';
		if(!$create_Y) {
        	if (!copy($defaultFile, $newFile)) return false;
		} else {
			

			$color = self::hex2rgb($create_Y);
			
			
			// Создание пустого изображения
			$size = 100;
			$image = imagecreatetruecolor($size, $size);
			imagefill($image, 0, 0, imagecolorallocate($image, 255, 255, 255));
			
			// Создание цвета полигона
			$col_poly = imagecolorallocate($image, $color[0], $color[1], $color[2]);
			
			imagesetthickness($image, 5);
			// Рисование
			imagepolygon($image, array(
					$size/100,   $size/100,
					$size/2-$size/100, $size/2-$size/100,
					$size/2-$size/100, $size-$size/100,
					$size/2-$size/100, $size/2-$size/100,
					$size-$size/100, $size/100,
					$size/2-$size/100, $size/2-$size/100
				),
				6,
				$col_poly);
			imagepng($image, $newFile);
		}
        
        $icon = new Icon();
        $icon->name = $kindName . '.png';
        $icon->width = 30;
        $icon->height = 30;
        if ($icon->save()) {
            if ($returnObj) return $icon;
            return $icon->id;
        }
        return false;
    }
	
}
