<?php 
require_once "common.php";
$DivisionList=$DB->query("SELECT `Name`,`NormalizedName`,`FirstSeenYear`,`LastSeenYear` FROM `divisions`");//fixed
include "queryui_sub_finalist.php";
?>
<div class="fieldGroup right" id="projectSearchGroup">
	<fieldset>
		<legend>Project</legend>
		<table style="float:left">
			<tr>
				<td><label for="titleSearch">Title:</label></td>
				<td><input type="text" id="titleSearch" name="titleSearch"/></td>
			</tr>
			<tr>
				<td><label for="synopsisSearch">Abstract:</label></td>
				<td><input type="text" id="synopsisSearch" name="synopsisSearch"/></td>
			</tr>
			<tr>
				<td><label for="awardsSearch">Awards:</label></td>
				<td><input type="text" id="awardsSearch" name="awardsSearch"/></td>
			</tr>
		</table>
		<table style="margin-left:15px;float:left;">
			<tr>
				<td><label for="yearSearch">Year:</label></td>
				<td>
					<select name="yearSearch" id="yearSearch">
						<option value="">Any</option>
						<?php
							for ($year=$UpToDateYear; $year >= 2005; $year--) { 
								echo "<option value=\"$year\">$year - ".$FairYears[$year]."</option>";
							}
						?>
					</select>
				</td>
			<tr>
				<td><label for="ageSearch">Age:</label></td>
				<td class="positionClearShortcut">
					<radiogroup id="ageSearch"><!-- might as well get the making-up-tags ball rolling here -->
						<input type="button" value="Junior" title="Junior (Grades 7-8)" radiovalue="1"/>
						<input type="button" value="Inter" title="Intermediate (Grades 9-10)" radiovalue="2"/>
						<input type="button" value="Senior" title="Senior (Grades 11-12)" radiovalue="3"/>
					</radiogroup>

				</td>
			</tr>
			<tr style="display:none;" id="divisionSearchRow">
				<td><label for="divisionSearch">Division:</label></td>
				<td>
					<select name="divisionSearch" id="divisionSearch">
						<option value="">Any</option>
						<?php
						while ($div=$DivisionList->fetch_object()){
							if ($div->FirstSeenYear == $div->LastSeenYear) {
								$rangeStr = $div->FirstSeenYear;
							} else {
								$rangeStr = "'".str_pad(($div->FirstSeenYear-2000),2, '0', STR_PAD_LEFT) . "-";
								if ($div->LastSeenYear == $UpToDateYear){
									$rangeStr .= "today";
								} else {
									$rangeStr .= "'" . str_pad(($div->LastSeenYear-2000),2, '0', STR_PAD_LEFT);
								}
							}
							
							echo "<option value=\"".$div->NormalizedName."\" startYear=\"".$div->FirstSeenYear."\" endYear=\"".$div->LastSeenYear."\">".(array_key_exists($div->NormalizedName, $CategoryShortNames)?$CategoryShortNames[$div->NormalizedName]: $div->Name)."</option>";
						}
						?>
					</select>
				</td>
			</tr>
		</table>
	</fieldset>
</div>
