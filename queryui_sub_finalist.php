<?php
require_once "common.php";
$RegionList=$DB->query("SELECT `regions`.`Name`,`regions`.`NormalizedName`,`regions`.`ProvTerr` FROM `regions`");

?>
<div class="fieldGroup" id="participantSearchGroup">
	<fieldset>
		<legend>Finalist</legend>
		<table>

			<tr>
				<td><label for="provSelect">Prov/Terr:</label></td>
				<td>
					<select name="provSelect" id="provSelect">
						<option value="">Any</option>
						<option value="AB">Alberta</option>
						
						<option value="BC">British Columbia</option>
						<option value="MB">Manitoba</option>

						<option value="NB">New Brunswick</option>
						<option value="NL">Newfoundland</option>
						<option value="NS">Nova Scotia</option>
						<option value="NT">Northwest Territories</option>
						<option value="NU">Nunavut</option>
						
						<option value="ON">Ontario</option>
						<option value="PE">Prince Edward Island</option>
						<option value="QC">Qu&eacute;bec</option>
						<option value="SK">Saskatchewan</option>
						<option value="YK">Yukon</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="regionSelect">Region:</label></td>
				<td><select name="regionSelect" id="regionSelect">
					<option value="">Any</option>
					<?php
						while ($region=$RegionList->fetch_object()){
							$display=$region->Name;
							if ($display==""){
								$display="Unknown";//hax
							}
							echo "<option value=\"".$region->NormalizedName."\" province=\"".$region->ProvTerr."\">".htmlentities(utf8_decode($display))."</option>";
						}
					?>
				</select></td>
			</tr>
			<tr>
				<td><label for="nameSearch">Name:</label></td>
				<td><input type="text" id="nameSearch" name="nameSearch"/></td>
			</tr>
		</table>
	</fieldset>
</div>