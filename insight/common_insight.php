<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/html; charset=UTF-8");

class ListUtils {
	static function PrintArrayList($list) {
		if (count($list)){
			echo "<ul>";
			foreach ($list as $item) {
				echo "<li>&raquo; <em>".htmlentities(utf8_decode($item))."</em></li>";
			}
			echo "</ul>";
		} else {
			echo "<center>(none)</center>";
		}
	}
	static function PrintArrayListPlural($list){
		if (count($list)){
			echo "<ul>";
			$totalYears=0;
			foreach ($list as $item){
				$totalYears+=count($item->years);
			}
			foreach ($list as $item) {
				echo "<li>&raquo; <em>".htmlentities(utf8_decode($item->key))."</em>";
				if (count($item->years)!=$totalYears)
				{
					//incomplete years
					if (count($item->years)==1){
						echo " (".$item->years[0].")";
					} else {
						sort($item->years, SORT_NUMERIC);
						//contiguous?
						$contiguous=true;
						for ($i=0; $i < count($item->years) ; $i++) { 
							if ($i>0 && $item->years[$i-1]+1!=$item->years[$i]) {
								$contiguous=false;
								break;
							}
						}
						if ($contiguous){
							echo " (".$item->years[0]."-".$item->years[count($item->years)-1].")";
						} else {
							echo " (".implode(', ',$item->years).")";
						}
						
					}
				}
				echo "</li>";
			}
			echo "</ul>";
		} else {
			echo "<center>(none)</center>";
		}
	}
	static function AddPossiblyPluralItem(&$collection, $item, $year){
		foreach ($collection as $exist){
			if ($exist->key==$item){
				$exist->years[]=$year;
				return;
			}
		}
		$newitm=new stdClass;
		$newitm->key=$item;
		$newitm->years=array($year);
		$collection[]=$newitm;
	}
}


class DisplayUtils {
	static function FormatCurrency($val){
		return '$'.number_format($val);
	}
}