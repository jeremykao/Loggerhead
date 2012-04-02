<?php //createTeams.php

require_once 'login.php';

echo "<form method=\"post\" action=\"loggerhead_main.php\">
<label>Team 1 Name : <input type=\"text\" name=\"team1Name\" /></label>
<label>Team 2 Name : <input type=\"text\" name=\"team2Name\" /></label>
<table><tr><th>Player</th><th>Team 1</th><th>Team 2</th></tr>";
$showPlayerQuery = 'SELECT player FROM totaltable';
$showPlayerResult = mysql_query($showPlayerQuery);
while($showPlayerName = mysql_fetch_row($showPlayerResult))
{
	$name = ucwords(str_replace('_',' ',$showPlayerName[0]));
	echo "<tr><td>$name</td>
	<td><input type=\"checkbox\" name=\"team1Array[]\" value=\"$showPlayerName[0]\" /></td>
	<td><input type=\"checkbox\" name=\"team2Array[]\" value=\"$showPlayerName[0]\" /></td>
	</tr>";
}
echo "</table><input type=\"submit\" value=\"Create Teams\" /></form>";
?>