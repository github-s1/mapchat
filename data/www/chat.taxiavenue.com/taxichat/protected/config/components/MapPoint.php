<?php

class MapPoint {

	public $lat;

	public $lng;

	public function __construct($lat, $lng) {
		$this->lat = $lat;
		$this->lng = $lng;
	}
	
	public function fPointInsidePolygon($aPolygon = "")
	{
		$_PolygonSize = count($aPolygon);

		if($_PolygonSize <= 1) {
			$result = false;
		} else {
			$_intersections_num = 0;

			$_prev = $_PolygonSize - 1;
			$_prev_under = $aPolygon[$_prev]->lng < $this->lng;

			for ($i = 0; $i < $_PolygonSize; ++$i) {
				$_cur_under   = $aPolygon[$i]->lng < $this->lng;
				
				$a = new MapPoint($aPolygon[$_prev]->lat - $this->lat, $aPolygon[$_prev]->lng - $this->lng);
				
				$b = new MapPoint($aPolygon[$i]->lat - $this->lat, $aPolygon[$i]->lng  - $this->lng);
			
				$t = ($a->lat*($b->lng - $a->lng) - $a->lng*($b->lat - $a->lat));

				if(($_cur_under == true) and (!$_prev_under == true)) {
					if ($t > 0) {
					  $_intersections_num++;
					}
				}

				if ((!$_cur_under == true) and ($_prev_under == true)) {
					if($t < 0) {
						$_intersections_num++;
					}
				}

				$_prev = $i;
				$_prev_under = $_cur_under;
			}

			$result = !($_intersections_num & 1) == 0;
		}
	  return $result;
	}
	
	static function CreateZone(TariffZones $zone)
	{
		$rez = array();
		if(!empty($zone->points)) { 
			$points = explode(";", $zone->points);
			if(!empty($points)) {
				foreach($points as $p) {
					$coord = explode(",", $p);
					$rez[] = new MapPoint($coord[0], $coord[1]);
				}	
			}
		}
		return $rez;
	}

}
