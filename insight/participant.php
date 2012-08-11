<div class="pInsight">
<?php
include "../common.php";
include "../query_build.php";
include "common_insight.php";
$NormalizedName=$DB->real_escape_string($_REQUEST["param"]);
$ParticipantRecord=$DB->Query("SELECT * FROM `participants` WHERE `NormalizedName`='$NormalizedName'");
$ParticipantRecord=$ParticipantRecord->fetch_object();


$qb=new ProjectsQueryBuilder();
$qb->Prepare(array("Finalists"=>$ParticipantRecord->Name));
$query=$qb->Compile(false);

//$ProjectsQ=$DB->Query("SELECT `projects`.*,`regions`.`Name`  AS `RegionName`,`PartA`.`Name` AS `FinalistAName`,`PartB`.`Name` AS `FinalistBName` FROM `projects` LEFT JOIN `participants` PartA ON `PartA`.`NormalizedName`=`projects`.`ParticipantA` LEFT JOIN `participants` PartB ON `PartB`.`NormalizedName`=`projects`.`ParticipantB` LEFT JOIN `regions` ON `regions`.`NormalizedName`=`projects`.`Region` WHERE `ParticipantA`='$NormalizedName' OR `ParticipantB`='$NormalizedName' ORDER BY `Year` DESC");//was gonna make this all one query, but it's like 10 PM and I can't be bothered to wrap my head around how that join would ever work
$ProjectsQ=$DB->Query($query);

if (!$ProjectsQ) die($DB->error);

function AddPossiblyPluralItem(&$collection, $item, $year){
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

$IsQC=false;
$Projects=array();
$Partners=array();
$Regions=array();
$Fairs=array();
$Divisions=array();
$Grades=array();
while ($row=$ProjectsQ->fetch_object()){
	$Projects[]=$row;
	AddPossiblyPluralItem($Regions,$row->RegionName, $row->Year);
	if ($row->FinalistAName!=null && $row->FinalistAName!=$ParticipantRecord->Name) AddPossiblyPluralItem($Partners,$row->FinalistAName,$row->Year);
	if ($row->FinalistBName!=null && $row->FinalistBName!=$ParticipantRecord->Name) AddPossiblyPluralItem($Partners,$row->FinalistBName,$row->Year);
	AddPossiblyPluralItem($Fairs, $FairYears[$row->Year], $row->Year);
	if ($row->DivisionAName!=null) AddPossiblyPluralItem($Divisions,$row->DivisionAName,$row->Year);
	if ($row->DivisionBName!=null) AddPossiblyPluralItem($Divisions, $row->DivisionBName, $row->Year);
	if ($ParticipantRecord->GradeAnchor!=null) AddPossiblyPluralItem($Grades,$row->Year-$ParticipantRecord->GradeAnchor, $row->Year);

	if ($row->ProvTerr=="QC") $IsQC=true;
}
//$Regions=array_unique($Regions);
//$Partners=array_unique($Partners);
//$Divisions=array_unique($Divisions);

$Grades=array_reverse($Grades);

$GradesString="";

if (!$IsQC || true){
	$GradesString="Attended in Grade";
	if (count($Grades)>1) $GradesString.="s";
	$GradesString.=" ";
	for ($gidx=0;$gidx<count($Grades);$gidx++){
		$GradesString.=$Grades[$gidx]->key;
		if ($gidx==count($Grades)-2) {
			$GradesString.=" and ";
		} else if ($gidx!=count($Grades)-1) {
			$GradesString.=", ";
		}
	}
}


$LatestProject=$Projects[0];

echo "<table><tr><td valign=\"top\"><div class=\"pInsightPhotos\">";
foreach ($Projects as $proj){
	echo "<img src=\"${ROOT}imgcache/".$proj->RegID."_project.jpg\" alt=\"Participant Image\" style=\"float:left;\">";
}
echo "</div></td><td valign=\"top\"><h1>".htmlentities($ParticipantRecord->Name)."</h1>";


?>
<div class="insightBlock wide" id="pStats">
	<h1>Stats</h1>
	<ul>
		<li>Total winnings: <?php echo FormatCurrency($ParticipantRecord->Winnings); if ($ParticipantRecord->Winnings!=$ParticipantRecord->UndividedWinnings){echo " (".FormatCurrency($ParticipantRecord->UndividedWinnings)." without partner split)";}?> </li>
		<li>Times in attendence: <?php echo count($Fairs);?></li>
		<?php if (count($Grades)>0){?><li><?php echo $GradesString;?></li><?php } ?>
	</ul>
</div>
<!--<div class="insightBlock wide" id="pWTFStats">
	<h1>Useless Stats</h1>
	<ul>
		<li title="Who got the fun job of looking at 2000+ participants to deduplicate names? Me">Times changed name in the FMS: 0 (0 minor changes)</li>
		<li title="Not from personal experience">Ratio of attendences vs. updated biographies: 1:3</li>
	</ul>
</div>-->
<div class="insightBlock" id="pRegions"><h1>Region(s)</h1><?php ArrayListPlural($Regions);?></div>
<!--<div class="insightBlock" id="pPartners"><h1>Partner(s)</h1><?php ArrayList($Partners);?></div>-->
<div class="insightBlock" id="pFairs"><h1>Fair(s)</h1><?php ArrayListPlural($Fairs);?></div>
<div class="insightBlock" id="pDivisions"><h1>Division(s)</h1><?php ArrayListPlural($Divisions);?></div>
</div>


</td></tr></table>

<script type="text/javascript">
	$(".pInsightPhotos").cycle('fade');

</script>