<?php
include "../common.php";
include "../query_build.php";
include "common_insight.php";

$NormalizedName=$DB->real_escape_string($_REQUEST["param"]); // should use PDO
$ParticipantRecord=$DB->Query("SELECT * FROM `participants` WHERE `NormalizedName`='$NormalizedName'");
$ParticipantRecord=$ParticipantRecord->fetch_object();

$qb=new ProjectsQueryBuilder();
$qb->Prepare(array("Finalists"=>$ParticipantRecord->Name));
$query=$qb->Compile(false);

$ProjectsQ=$DB->Query($query);

if (!$ProjectsQ) die($DB->error);

$IsQC=false;
$Projects=array();
$Partners=array();
$Regions=array();
$Fairs=array();
$Divisions=array();
$Grades=array();
while ($row=$ProjectsQ->fetch_object()){
	$Projects[]=$row;
	
	ListUtils::AddPossiblyPluralItem($Regions,$row->RegionName, $row->Year);
	if ($row->FinalistAName!=null && $row->FinalistAName!=$ParticipantRecord->Name) ListUtils::AddPossiblyPluralItem($Partners,$row->FinalistAName,$row->Year);
	if ($row->FinalistBName!=null && $row->FinalistBName!=$ParticipantRecord->Name) ListUtils::AddPossiblyPluralItem($Partners,$row->FinalistBName,$row->Year);
	ListUtils::AddPossiblyPluralItem($Fairs, $FairYears[$row->Year], $row->Year);
	if ($row->DivisionAName!=null) ListUtils::AddPossiblyPluralItem($Divisions,$row->DivisionAName,$row->Year);
	if ($row->DivisionBName!=null) ListUtils::AddPossiblyPluralItem($Divisions, $row->DivisionBName, $row->Year);
	if ($ParticipantRecord->GradeAnchor!=null) ListUtils::AddPossiblyPluralItem($Grades,$row->Year-$ParticipantRecord->GradeAnchor, $row->Year);

	if ($row->ProvTerr=="QC") $IsQC=true;
}

$Grades=array_reverse($Grades);

$GradesString=""; //TODO: system doesn't account for CEGEP now - sorry QCers
$GradesString="Attended in Grade";
if (count($Grades)>1) $GradesString.="s";
$GradesString.=" <em>";
for ($gidx=0;$gidx<count($Grades);$gidx++){
	$GradesString.=$Grades[$gidx]->key;
	if ($gidx==count($Grades)-2) {
		$GradesString.=" and ";
	} else if ($gidx!=count($Grades)-1) {
		$GradesString.=", ";
	}
}
$GradesString.="</em>";

$LatestProject=$Projects[0];
?>
<div class="pInsight">
	<table>
		<tr>
			<td valign="top">
				<div class="pInsightPhotos">
					<?php
					foreach ($Projects as $proj){
						echo "<img src=\"imgcache/".$proj->RegID."_project.jpg\" alt=\"Participant Image\" style=\"float:left;\">";
					}
					?>
				</div>
			</td>
			<td valign="top">
				<?php echo "<h1>".htmlentities($ParticipantRecord->Name)."</h1>";?>
				<div class="insightBlock wide" id="pStats">
					<h1>Stats</h1>
					<ul>
						<li>Total winnings: <em><?php echo DisplayUtils::FormatCurrency($ParticipantRecord->Winnings); if ($ParticipantRecord->Winnings!=$ParticipantRecord->UndividedWinnings){echo " (".DisplayUtils::FormatCurrency($ParticipantRecord->UndividedWinnings)." without partner split)";}?></em> </li>
						<li>Times in attendence: <em><?php echo count($Fairs);?></em></li>
						<?php if (count($Grades)>0){?><li><?php echo $GradesString;?></li><?php } ?>
					</ul>
				</div>
				<div class="insightBlock" id="pRegions"><h1>Region(s)</h1><?php ListUtils::PrintArrayListPlural($Regions);?></div>
				<div class="insightBlock" id="pFairs"><h1>Fair(s)</h1><?php ListUtils::PrintArrayListPlural($Fairs);?></div>
				<div class="insightBlock" id="pDivisions"><h1>Division(s)</h1><?php ListUtils::PrintArrayListPlural($Divisions);?></div>
			</td>
		</tr>
	</table>
</div>

<script type="text/javascript">
	$(".pInsightPhotos").cycle('fade');
</script>