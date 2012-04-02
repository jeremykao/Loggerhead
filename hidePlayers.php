<?php //hidePlayers

$hideArr = hidePlayers();
$getPlayersQ = mysql_query("SHOW TABLES FROM $db_database");

echo "<div id=\"hideThese\"><h3>Hide These:</h3><form method=\"post\" action=\"loggerhead_main.php\">";
while($row = mysql_fetch_row($getPlayersQ))
{
	$formatName = ucwords(str_replace('_',' ',$row[0]));
	if (in_array($row[0], $hideArr))
	{
		if ($row[0] == 'totaltable') continue;
		if ($row[0] == 'teamplayers') continue;
		if ($row[0] == 'hidetable') continue;
		echo "<label><input type=\"checkbox\" name=\"$row[0]\" checked=\"checked\" />$formatName</label>";
	}
	else
	{
		echo "<label><input type=\"checkbox\" name=\"$row[0]\" />$formatName</label>";
	}
}
echo "<br /><input type=\"submit\" name=\"hiding\" value=\"Update\" />
</form></div>";
if (in_array('teams', $hideArr)) 
{
	$teamsOn = 'false';
}
else 
{
	$teamsOn = 'true';
}
?>