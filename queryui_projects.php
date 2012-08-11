<?php 
$DivisionList=$DB->query("SELECT `Name`,`NormalizedName`,`FirstSeenYear`,`LastSeenYear` FROM `divisions`");//fixed
?>

<?php include "queryui_sub_finalist.php";?>
<div class="fieldGroup right" id="projectSearchGroup">
	<fieldset>
		<legend>Project</legend>
		

		<table style="float:left">
			
			<!--<tr>
				<td><label for="pnSearch">Proj. Number:</label></td>
				<td><input type="text" pattern="[0-9]*" maxlength="6" id="pnSearch" name="pnSearch" style="width:4em" placeholder="0101XX"/></td>
			</tr>-->
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
						<option value="2012">2012</option>
						<option value="2011">2011</option>
						<option value="2010">2010</option>
						<option value="2009">2009</option>
						<option value="2008">2008</option>
						<option value="2007">2007</option>
						<option value="2006">2006</option>
						<option value="2005">2005</option>
					</select>
				</td>
			<tr>
				<td><label for="ageSearch">Age:</label></td>
				<td>
					<radiogroup id="ageSearch"><!-- might as well get the making-up-tags ball rolling here -->
						<input type="button" value="Junior" title="Junior (Grades 7-8)" radiovalue="1"/>
						<input type="button" value="Inter" title="Intermediate (Grades 9-10)" radiovalue="2"/>
						<input type="button" value="Senior" title="Senior (Grades 11-12)" radiovalue="3"/>
					</radiogroup>
				</td>
			</tr>
			<tr style="display:none">
				<td><label for="divisionSearch">Division:</label></td>
				<td>
					<select name="divisionSearch" id="divisionSearch">
						<option value="">Any</option>
						<?php
						while ($div=$DivisionList->fetch_object()){
							echo "<option value=\"".$div->NormalizedName."\" startYear=\"".$div->FirstSeenYear."\" endYear=\"".$div->LastSeenYear."\">".$div->Name."</option>";
						}
						?>
					</select>
				</td>
			</tr>
		</table>
		
	</fieldset>

</div>
