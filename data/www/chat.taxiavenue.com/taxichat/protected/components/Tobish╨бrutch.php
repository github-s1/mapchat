<?php
//класс содержащий костыли для язей Android-щиков
class TobishСrutch
{
    public static function СrutchFormatData(array &$arr)
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
	
	public static function ParseString($param)
    {	
	    $res = $param;
		if(is_string($param)) {
			$res = explode(',', $param);	
		}
		return $res;
    }
}