<?php //loggerhead_sidebar.php
echo "<div id=\"sidebar\"><table id=\"sidebar_table\"><form method=\"post\">";
$players = getPlayerArray();

echo "<tr><span id=\"sidebar_drop\">Subject: <select name=\"subject\">
<option value=\"biol\">Biology</option>
<option value=\"chem\">Chemistry</option>
<option value=\"phys\">Physics</option>
<option value=\"geol\">Geology</option>
<option value=\"sosc\">SoSci</option>
<option value=\"geog\">Geography</option>
<option value=\"marp\">MarPol</option>
<option value=\"tech\">Tech</option>
</select></span></tr>";

for ($z = 0; $z < sizeof($players) ; $z++)
{
	$FormatPlayerName = ucwords(str_replace("_"," ", $players[$z]));
	echo "<tr id=\"'$playerName[$z]'Buttons\" class=\"sidebar_player_row\">
	<td class=\"sidebar_name\"><a href=\"?player=$playerName[$z]\">$FormatPlayerName</a></td>
	<span class=\"sidebar_buttons\">
	<td><input type=submit value=\"i\" name=\"int$players[$z]\" /></td>
	<td><input type=submit value=\"+\" name=\"cor$players[$z]\" /></td>
	<td><input type=submit value=\"-\" name=\"neg$players[$z]\" /></td>
	<td><input type=submit value=\"0\" name=\"inc$players[$z]\" /></td>
	</span>
	</tr>";
}
echo "</form></table></div>";
?>