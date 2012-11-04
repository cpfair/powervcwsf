<div class="rInsight">
<?php
include "../common.php";
include "common_insight.php";
$DivisionListQuery=$DB->query("SELECT `Name`,`NormalizedName`,`FirstSeenYear`,`LastSeenYear` FROM `divisions`");
$DivisionList=array();//really should have MVC'd this part...
while ($div=$DivisionListQuery->fetch_object()){
	$DivisionList[$div->NormalizedName]=$div;
}

$NormalizedName=$DB->real_escape_string($_REQUEST["param"]);
$RegionRecord=$DB->Query("SELECT * FROM `regions` WHERE `NormalizedName`='$NormalizedName'"); //should really switch to PDO...
$RegionRecord=$RegionRecord->fetch_object();

$FirstSeen=$RegionRecord->FirstSeenYear;
if ($RegionRecord->FirstSeenYear==2005) $FirstSeen.=" (or before)";
$LastSeen=$RegionRecord->LastSeenYear;

$YearAggregateRes=$DB->Query("SELECT `Year`,SUM(IF(`ParticipantB` IS NOT NULL,2,1)) FROM `projects` WHERE `region`='$NormalizedName' GROUP BY `Year`");
$YearAggregate=array();
while ($ya=$YearAggregateRes->fetch_array()){
	$YearAggregate[intval($ya[0])]=intval($ya[1]);
}
$FinalistCt=$YearAggregate[$RegionRecord->LastSeenYear];
for ($yr=$RegionRecord->LastSeenYear; $yr >=$RegionRecord->FirstSeenYear ; $yr--) { 
	if (empty($YearAggregate[$yr])) continue;
	if ($YearAggregate[$yr]!=$FinalistCt){
		$FinalistCt.=" (";
		if ($YearAggregate[$yr]>$FinalistCt){
			$FinalistCt.="down";
		} else {
			$FinalistCt.="up";
		}
		$FinalistCt.="  from ".$YearAggregate[$yr]." in $yr)";
		break;
	}
}



$DivisionAggregateA=$DB->Query("SELECT `DivisionA`,`Year`,COUNT(*) from `projects` WHERE `region`='$NormalizedName' GROUP BY `DivisionA`, `Year`");
$DivisionAggregateB=$DB->Query("SELECT `DivisionB`,`Year`,COUNT(*) from `projects` WHERE `region`='$NormalizedName' GROUP BY `DivisionB`, `Year`");
$DivisionABAggregate=array();
$ChallengeABAggregate=array();
//this is wacky because of the international division/challenge...
while ($da=$DivisionAggregateA->fetch_array()){
	if (empty($da[0])) continue;
	if (intval($da[1])<=2010){
		if (!isset($DivisionABAggregate[$da[0]])) $DivisionABAggregate[$da[0]]=0;
		$DivisionABAggregate[$da[0]]+=$da[2];
	} else {
		if (!isset($ChallengeABAggregate[$da[0]])) $ChallengeABAggregate[$da[0]]=0;
		$ChallengeABAggregate[$da[0]]+=$da[2];
	}
}
while ($da=$DivisionAggregateB->fetch_array()){
	if (empty($da[0])) continue;
	if (intval($da[1])<=2010){
		if (!isset($DivisionABAggregate[$da[0]])) $DivisionABAggregate[$da[0]]=0;
		$DivisionABAggregate[$da[0]]+=$da[2];
	} else {
		if (!isset($ChallengeABAggregate[$da[0]])) $ChallengeABAggregate[$da[0]]=0;
		$ChallengeABAggregate[$da[0]]+=$da[2];
	}
}



$DivisionAggregate=array();
$ChallengeAggregate=array();
foreach ($DivisionABAggregate as $id => $count) {
	$DivisionAggregate[]=array($DivisionList[$id]->Name,$count);
}
foreach ($ChallengeABAggregate as $id => $count) {
	$ChallengeAggregate[]=array($DivisionList[$id]->Name,$count);
}
echo "<h1>".$RegionRecord->Name."</h1>";
?>
<table>
	<tr>
		<td>
			<div class="insightBlock" id="rStats">
				<h1>Region stats</h1>
				<ul>
					<li>Active since: <em><?php echo $FirstSeen;?></em></li>
					<li>Last seen: <em><?php echo $LastSeen;?></em></li>
					<li>Current number of finalists: <em><?php echo $FinalistCt;?></em></li>
				</ul>
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<div class="insightBlock divisionBreakdown">
				<h1>Division breakdown <span class="small">(2005-2010)</a></h1>
				<div id="divisionChart" class="chart" style="width: 100%; height: 300px;">
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<div class="insightBlock divisionBreakdown">
				<h1>Challenge breakdown <span class="small">(2011-<?php echo $UpToDateYear;?>)</a></h1>
				<div id="challengeChart" class="chart" style="width: 100%; height: 300px;">
			</div>
		</td>
	</tr>
</table>
<b>Note about division stats:</b> Currently the YSC results system is returning incorrect divisions for many projects, and not even the magic of pie charts can fix this. You have been warned.
</div>

<script type="text/javascript">
	function chartOpts(series,container){
		return {
	        chart: {
	        	animation: false,
	            renderTo: container,
	            plotBackgroundColor: null,
	            backgroundColor: null,
	            plotBorderWidth: null,
	            plotShadow: false
	        },
	        title:null,
	        colors:[
	        	'#333',
	        	'#555',
	        	'#444',
	        	'#666',
	        	'#555',
	        	'#888',
	        	'#777',
	        	'#454545',
	        	'#656565',
	        ],
	        tooltip: {
	            pointFormat: '{series.name}: <b>{point.percentage}%</b>',
	            percentageDecimals: 1,
	            enabled:false
	        },
	        area:{
	        	states:{
	        		hover:{
	        			enabled:false
	        		}
	        	}
	        },
	        plotOptions: {
	            pie: {
	            	states:{
	            		hover:{
	            			enabled:false
	            		}
	            	},
	                allowPointSelect: false,
	                cursor: 'default',
	                dataLabels: {
	                    enabled: true,
	                    color: '#ccc',
	                    softConnector:true,
	                    style:{
	                    	fontSize:'13px',
	                    },
	                    connectorColor: '#aaaaaa',
	                    formatter: function() {
	                        return ''+ this.point.name +': '+ Math.round(this.percentage) +' %';
	                    }
	                }
	            }
	        },
	        series: series
	    };
	}
	var dcOpts=chartOpts([{type:"pie",animation:false,data:<?php echo json_encode($DivisionAggregate);?>}],'divisionChart');
	var divChart = new Highcharts.Chart(dcOpts);
	var chalOpts=chartOpts([{type:"pie",animation:false,data:<?php echo json_encode($ChallengeAggregate);?>}],'challengeChart');
	var chalChart = new Highcharts.Chart(chalOpts);
</script> 