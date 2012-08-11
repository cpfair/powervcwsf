<?php

class QueryBuilder {
	public $SQLCount=true;

	protected $Table=null;
	protected $IgnoreParams=array("sortasc","sortdesc","mode","page","debug");
	protected $SplitFields=array();
	protected $OrderClauses=array();
	protected $WhereClauses=array();
	protected $SelectClauses=array("*");
	protected $JoinClauses=array();

	private $StartIndex=0;
	private $PageLength=50;


	private function PopulateOrderClausesFromParam($string,$order){
		global $DB;
		$columns=explode(',',$string);
		foreach ($columns as $col) {
			$col=$DB->real_escape_string($col);
			$this->OrderClauses[]="`$col` $order";
		}
	}
	function BuildOrderClauses($params){
		//params sortasc and sortdesc
		if (isset($params["sortasc"])) $this->PopulateOrderClausesFromParam($params["sortasc"],"ASC");
		if (isset($params["sortdesc"])) $this->PopulateOrderClausesFromParam($params["sortdesc"],"DESC");
	}

	function BuildWhereClauses($params){
		global $DB;
		foreach ($params as $pkey=>$pvalue){
			if (in_array($pkey,$this->IgnoreParams)) continue;
			$searchTerms=preg_split("!\s?&&\s?!",$pvalue);//allow for spaces around the && since that looks nice
			
			//format key so thi's.that turns into `thi\'s`.`that`

			$searchKey=preg_replace("/([^_]+)/","`$1`",$DB->real_escape_string($pkey));
			$searchKey=preg_replace("/_/",".",$searchKey);
			
			foreach ($searchTerms as $term){
				$term=$DB->real_escape_string($term);

				if (isset($this->SplitFields[$pkey])){
					$splitFields=array();
					foreach ($this->SplitFields[$pkey] as $resolvedField){
						array_push($splitFields,$resolvedField." LIKE '%$term%'");
					}
					array_push($this->WhereClauses,"(".join(" OR ",$splitFields).")");
				} else {
					array_push($this->WhereClauses,"$searchKey LIKE '%$term%'");
				}
			}
			

		}
	}

	function SetupOtherClauses(){
		//...
	}
	function Compile($fastcount=false){
		$query="SELECT ";
		if ($this->SQLCount && !$fastcount) $query.="SQL_CALC_FOUND_ROWS ";

		if (!$fastcount){
			$query.=join(",",$this->SelectClauses);
		} else {
			$query.="COUNT(*) as `Count`";
		}
		$query.=" FROM `".$this->Table."`";
		$query.=join(" ",$this->JoinClauses);
		if (count($this->WhereClauses)>0) $query.=" WHERE ".join(" AND ",$this->WhereClauses);
		if (count($this->OrderClauses)>0 && !$fastcount) $query.=" ORDER BY ".join(",",$this->OrderClauses);

		$query.=" LIMIT ".$this->StartIndex.",".$this->PageLength;

		if (isset($_REQUEST["debug"])) die($query);
		return $query;
	}
	function Prepare($params){

		$this->BuildOrderClauses($params);
		$this->BuildWhereClauses($params);
		$this->SetupOtherClauses();

		//pagination
		
		$page=0;
		if (isset($params["page"])) $page=intval($params["page"]);
		$this->StartIndex=$page*$this->PageLength;
		
	
		
	}

	function PostProcessRow($row){
		//...
		return $row;
	}




};

class ProjectsQueryBuilder extends QueryBuilder {
	function __construct(){
		$this->Table="projects";
		$this->SplitFields["Finalists"]=array("`PartA`.`Name`","`PartB`.`Name`");
		$this->SplitFields["Divisions"]=array("`DivA`.`NormalizedName`","`DivB`.`NormalizedName`");
		

	}

	function SetupOtherClauses(){
		
		$this->JoinClauses[]="LEFT JOIN `participants` PartA ON `PartA`.`NormalizedName`=`projects`.`ParticipantA`";
		$this->JoinClauses[]="LEFT JOIN `participants` PartB ON `PartB`.`NormalizedName`=`projects`.`ParticipantB`";
		$this->JoinClauses[]="LEFT JOIN `regions` ON `regions`.`NormalizedName`=`projects`.`Region`";
		$this->JoinClauses[]="LEFT JOIN `divisions` DivA ON `DivA`.`NormalizedName`=`projects`.`DivisionA`";
		$this->JoinClauses[]="LEFT JOIN `divisions` DivB ON `DivB`.`NormalizedName`=`projects`.`DivisionB`";
		$this->SelectClauses[]="`projects`.*,`PartA`.`Name` AS `FinalistAName`,`PartB`.`Name` AS `FinalistBName`,`DivA`.`Name` AS `DivisionAName`,`DivB`.`Name` AS `DivisionBName`, `regions`.`Name` AS `RegionName`, `CashAwardsValue`+`ScholarshipAwardsValue`+`OtherAwardsValue` AS `TotalAwardsValue`";//ikr
	}

	function BuildOrderClauses($params){
		parent::BuildOrderClauses($params);
		$this->OrderClauses[]="`projects`.`Year` DESC";
		$this->OrderClauses[]="`projects`.`Name` ASC";
	}
	function PostProcessRow($row){
		//just clean this up a bit for the JS
		$row["FinalistNames"]=array($row["FinalistAName"],$row["FinalistBName"]);
		unset($row["FinalistAName"]);
		unset($row["FinalistBName"]);
		$row["Participants"]=array($row["ParticipantA"],$row["ParticipantB"]);
		unset($row["ParticipantA"]);
		unset($row["ParticipantB"]);
		$row["DivisionNames"]=array($row["DivisionAName"],$row["DivisionBName"]);
		unset($row["DivisionAName"]);
		unset($row["DivisionBName"]);
		$row["Divisions"]=array($row["DivisionA"],$row["DivisionB"]);
		unset($row["DivisionA"]);
		unset($row["DivisionB"]);
		return $row;
	}
}
