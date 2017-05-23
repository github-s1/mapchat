<?php
//класс содержащий костыли для Великого разработчика приложений под Android - Дашкевича тобишь Дмитрия
class TobishСrutch
{
    static function СrutchFormatData(array &$arr)
    {	
	    if(!empty($arr)) {
			$post = array();
			foreach($arr as $idx => $str) {
				$rez = stripcslashes($str);
				$post[$idx] = (array)json_decode($rez);
			}
			$arr = $post;
		}
    }
}