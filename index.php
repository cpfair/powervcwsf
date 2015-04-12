<?php
require_once "common.php";
header("Content-Type: text/html; charset=UTF-8");
?>
<!doctype html>
<html>
	<head>
		<title>Canada-Wide Science Fair <?php echo $UpToDateYear;?> Results - Search CWSF projects from 2007 - <?php echo $UpToDateYear;?></title>
		<meta charset="UTF-8">
		<meta http-equiv="Content-Type" value="text/html; charset=UTF-8">
		<meta name="Description" content="Search, filter, and sort all Canada-Wide Science Fair results.">
		<meta name="Keywords" content="Canada Wide Science Fair, <?php echo $UpToDateYear;?>, CWSF, ESPC, Results, Awards, Search, Filter, Historical, Statistics, Database, Records, Sort, Order, youth science, youth science canada">

		<link rel="stylesheet" type="text/css" href="min/?g=css">
		
		<script type="text/javascript" src="min/?g=js,<?php echo $MODE==MODE_PROJECTS?"js-proj":"js-part";?>"></script>
		<script type="text/javascript" src="http://code.highcharts.com/highcharts.js"></script>
    	<script type="text/javascript">
			Query.Mode=<?php echo $MODE;?>;
			$(document).ready(Query.Init);
			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', 'UA-16823186-2']);
			_gaq.push(['_trackPageview']);

			(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			})();

		</script>
		
		<link rel="icon" type="image/png" href="/favicon.png">
	</head>
	<body>
		<div id="searchFields">
			<?php
				if ($MODE==MODE_PROJECTS){
					include "queryui_projects.php";
				} else {
					include "queryui_participants.php";
				}
			?>
			<div style="clear:both"> </div>
			<div id="searchTips">Use <i>&quot;this && that&quot;</i> to require multiple terms</div>
		</div>
		<div id="introduction">
			<h1>Welcome to Power VCWSF</h1>
			<p>
				Here, you can search every <a href="http://cwsf.youthscience.ca" target="_blank">Canada-Wide Science Fair</a> project from 2005 to <?php echo $UpToDateYear;?>. You can also sort projects, find all the projects that won a specific award, view a participant's science fair history, and more.
			</p>
			<p>
				These results are derived from <a href="https://secure.youthscience.ca/virtualcwsf/" target="_blank">the official CWSF results system</a>.
			</p>
						<h2>Update:</h2>
			<p>
				Thanks to <a href="http://layeh.com/" target="_blank">Tim Cooper</a>, you can now search by divisions and challenges!
			</p>
		</div>
		<div id="resultsTable">
			<table style="width:100%;">
				<thead>
				<tr id="resultsHeaders">
				</tr>
				</thead>
				<tbody id="resultsTableBody">
				</tbody>
			</table>
			<div id="resultsContainer">
			</div>
		</div>
		<div id="footer">
			<div id="about"><a href="#" onclick="this.endpoint='system';Query.InsightButton.call(this);return false;">About PowerVCWSF</a> | <a href="mailto:cpf@cpfx.ca">Report an issue</a> | Data last updated Aug 9 2014</div>
			<div id="resultCt">Loading...</div>
			<div id="loading">Loading...</div>
			<div id="attrib">Please note that these data and calculations are not warranted for accuracy.<br/>This system is not affiliated with Youth Science Canada.</div>
		</div>
	</body>
</html>