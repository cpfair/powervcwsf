<?php
include "common.php";
header("Content-Type: text/html; charset=UTF-8");
?>
<!doctype html>
<html>
	<head>
		<title>Power Virtual Canada-Wide Science Fair</title>
		<meta charset="UTF-8">
		<meta http-equiv="Content-Type" value="text/html; charset=UTF-8">
		<meta name="Description" content="Search, filter, and sort all Canada-Wide Science Fair results.">
		<meta name="Keywords" content="Canada Wide Science Fair, CWSF, ESPC, Results, Awards, Search, Filter, Historical, Statistics, Database, Records, Sort, Order, youth science, youth science canada">

		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="stylesheet" type="text/css" href="style_results.css">
		<link rel="stylesheet" type="text/css" href="style_insight.css">
		<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" src="js/jquery.address-1.4.min.js"></script>
		<script type="text/javascript" src="js/jquery.cycle.lite.js"></script>
		<script type="text/javascript" src="js/jquery.imageloader.min.js"></script>
		<script type="text/javascript" src="js/displayutils.js"></script>
		<script type="text/javascript" src="js/query.js"></script>
		<script type="text/javascript" src="js/insight.js"></script>
		<?php
			if ($MODE==MODE_PROJECTS){
		?>
			<script type="text/javascript" src="js/query_projects.js"></script>
		<?php
			} else {
		?>
			<script type="text/javascript" src="js/query_participants.js"></script>
		<?php 
			} 
		?>
		<script type="text/javascript">
			Query.Mode=<?php echo $MODE;?>;
			$(document).ready(Query.Init);
		</script>
		<script type="text/javascript">

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-16823186-2']);
		  _gaq.push(['_trackPageview']);

		  (function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();

		</script>
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
				Here, you can search every <a href="http://cwsf.youthsciece.ca" target="_blank">Canada-Wide Science Fair</a> project from 2005 to today. You can also sort projects by nearly anything, find all the projects that won a specific award, view a participant's science fair history, and more.
			</p>
			<p>
				These results are derived from <a href="http://apps.ysf-fsj.ca/virtualcwsf/" target="_blank">the official CWSF results system</a>.
			</p>
			<h2>Update:</h2>
			<p>
				As of August 2012, there is a bug in the official CWSF results system that returns incorrect divisions for some projects. Because of this, I have temporarily removed the ability to search by project division. 
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
			<div id="about"><a href="#" onclick="this.endpoint='system';Query.InsightButton.call(this);return false;">About PowerVCWSF</a> | <a href="mailto:cpf@cpfx.ca">Report an issue</a> | Data last updated May 18 2012</div>
			<div id="resultCt">Loading...</div>
			<div id="loading">Loading...</div>
			<div id="attrib">Please note that these data and calculations are not warranted for accuracy.<br/>This system is not affiliated with Youth Science Canada.</div>
		</div>
	</body>
</html>