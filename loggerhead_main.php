<?php //setUpTables

/*******login details*******/
require_once 'login.php';
/********* END ************/
/*********** Functions ***************/
require_once 'loggerhead_functions.php';
/*********** END *************/

echo "<!DOCTYPE html><html><head><title>Loggerhead 2.0</title>
<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\"></head><body>
<div id=\"header\"><h1>Loggerhead 2.0</h1></div>";

if (!tableExists('totalTable'))
{
	$query = 'CREATE TABLE totaltable(player VARCHAR(128) PRIMARY KEY, totalScore SMALLINT SIGNED) ENGINE MyISAM';
	$result = mysql_query($query);
	if (!$result) die ("Cannot create table for Total Score : " . mysql_error());
}
if (!tableExists('hideTable'))
{
	mysql_query("CREATE TABLE hideTable(person VARCHAR(30)) ENGINE MyISAM");
}
createSmallTable('teams');
createSmallTable('teamPlayers');
/***** Add Player ********/
if (isset($_POST['fname']) || isset($_POST['lname']))
{
	$player = strtolower($_POST['fname'] . "_" . $_POST['lname']);
	if ($player == eric_cheng) echo "<script>window.alert('Nigga Plz')</script>";
	$player = sanitizeInput($player);
	createTable($player, $todayDate);
}	
/*********** Delete Player **********/
if ($_POST['delete']) displayDelete();
if ($_POST['deletePlayers'])
{
	$playerToDelete = $_POST['players'];
	for ($num = 0; $num < sizeof($playerToDelete); $num++)
	{
		$player = $playerToDelete[$num];
		deleteFromDatabase(strtolower($player), $playerToDelete);
	}
} 
/*********** END ********************/
/***** Hide Players *******/
$teamson = 'false';
if ($_POST['hiding'])
{
	mysql_query("DELETE FROM hidetable");
	$hideThis = array_keys($_POST);
	$hideInsertSQL = implode('\'),(\'',$hideThis);
	mysql_query("INSERT INTO hidetable(person) VALUES ('$hideInsertSQL')") or die('shit' . mysql_error());
}
include 'hidePlayers.php';
/******End HidePlayers *******/
/***** Setting Most Recent Date *******/
if (count(getPlayerArray()) > 0)
{ 
	$recentDate = getRecentDate();
	echo "<div id=\"clearBoard\"><form method=\"post\"><input type=\"submit\" name=\"clearBoard\" value=\"Clear Board\" /></form></div>"; //Clear Board Button
	if ($recentDate != $todayDate) updateToNewDate($todayDate);
	
}
/****** Check for button click; Parse Name; Call correct update function ******/
$interrupt = '/^int.*/';
$correct = '/^cor.*/';
$neg = '/^neg.*/';
$incorrect = '/^inc.*/';

$subject = $_POST['subject'];
$checkPostArray = array_keys($_POST);
foreach ($checkPostArray as $name)
{
	if (preg_match($interrupt, $name) || preg_match($correct, $name) || preg_match($neg, $name) || preg_match($incorrect, $name))
	{
		
		$goodName = $name;
		parseAndUpdate($goodName, $subject, $recentDate);
	}
}
/*********** END *************/
/********* Clear Board *********/
if ($_POST['clearBoard'])
{
	updateToNewDate($todayDate);
}
/******* Display Score **********/
echo"
<div id=\"scoreboard_wrapper\">
<table id=\"scoreboard\">
<tr>
	<th>Player</th>
	<th>Int</th><th>Cor</th><th>Neg</th><th>Inc</th>
	<th>Biol</th><th>Chem</th><th>Phys</th>
	<th>Geol</th><th>SoSc</th><th>Geog</th>
	<th>MarP</th><th>Tech</th><th>Score</th>
</tr>";
$playerName = getPlayerArray();
$numPlayers = sizeof($playerName); //find number of tables which is all users + totaltable
//echo "<script>window.alert($numPlayers)</script>";
for ($count = 0; $count < $numPlayers; $count++) // iterates for how many players
{	
	$selectPosSumQuery = "SELECT SUM(biol), SUM(chem), SUM(phys), SUM(geol), SUM(sosc), SUM(geog), SUM(marp), SUM(tech) FROM $playerName[$count] WHERE choice!='neg' AND choice!='incorrect' AND date=$recentDate";
	$selectNegSumQuery = "SELECT SUM(biol), SUM(chem), SUM(phys), SUM(geol), SUM(sosc), SUM(geog), SUM(marp), SUM(tech) FROM $playerName[$count] WHERE choice!='interrupt' AND choice!='correct' AND choice!='incorrect' AND date=$recentDate";
	$selectCountQuery = "SELECT count FROM $playerName[$count] WHERE date=$recentDate";
	
	$selectPosSumResult = mysql_query($selectPosSumQuery);
	$selectNegSumResult = mysql_query($selectNegSumQuery);
	$selectCountResult = mysql_query($selectCountQuery);
	
	echo "<tr id=\"$playerName[$count]Score\">";
	//$getTotalQuery = "SELECT totalscore FROM totaltable WHERE player='$playerName[$count]'";
	//$getTotalResult = mysql_query($getTotalQuery);
	
	// gets number of rows for sum scoreboard
	$FormattedPlayerName = ucwords(str_replace('_',' ',$playerName[$count]));
	$playerScore = getScore($playerName[$count],$recentDate);
	mysql_query("UPDATE totaltable SET totalScore=$playerScore WHERE player='$playerName[$count]'");
	echo "<td><p class=\"Pscore\">$FormattedPlayerName</p></td>";
	while ($countRow = mysql_fetch_row($selectCountResult))
	{
		echo "<td><p class=\"score\">$countRow[0]</p></td>";
	}
	$posScores = mysql_fetch_array($selectPosSumResult);
	$negScores = mysql_fetch_array($selectNegSumResult);
	for ($j = 0; $j < 8; $j++)
	{
		echo "<td><p class=\"score\"><span class=\"positive\">$posScores[$j]</span>/<span class=\"negative\">$negScores[$j]</span></p></td>";
	}
	echo "<td><p class=\"score\">$playerScore</p></td>";
	echo "</tr>";
}
echo "</table></div>";

/*******Sidebar*********/
require 'loggerhead_sidebar.php';
/************Add Player Bar *************/
echo <<<_END
<div id="addDeleteClick">
<div id="addPlayerBar">
<form method="post">
	First Name: <input type="text" name="fname" /> \t Last Name: <input type="text" name="lname" /><input type="submit" value="Add Player" />
</form>
<form method="post">
<input type="submit" name="delete" value="Delete a Player" />
</form>
</div>
<p>Add or Delete Players</p>
</div>
_END;
 
/************** Create Team and Display Team Scores ***************/
echo "<div id=\"createTeams\"><form method=\"post\" action=\"createTeams.php\"><input type=\"submit\" name=\"createTeams\" value=\"Create Teams\" /></form></div>";

if ($_POST['team2Name'] && $_POST['team1Name'])
{
	$dropTable = mysql_query('DROP TABLE teams') or die ('Cannot drop teams : ' . mysql_error());
	$dropTable = mysql_query('DROP TABLE teamPlayers') or die ('Cannot drop teams : ' . mysql_error());
	createSmallTable('teams');
	createSmallTable('teamPlayers');
	$team1Name = sanitizeInput($_POST['team1Name']);
	$team2Name = sanitizeInput($_POST['team2Name']);
	$saveTeamQuery = "INSERT INTO teams(team, num) VALUES ('$team1Name',1), ('$team2Name',2)";
	$saveTeamResult = mysql_query($saveTeamQuery) or die ('Cannot create Teams : ' . mysql_error());
	
	$team1Array = $_POST['team1Array'];
	$team2Array = $_POST['team2Array'];
	
	foreach ($team1Array as $player)
	{
		mysql_query("INSERT INTO teamPlayers (player,num) VALUES ('$player',1)") or die("Cannot insert player1 into teamPlayer" . mysql_error());
	}
	foreach ($team2Array as $player)
	{
		mysql_query("INSERT INTO teamPlayers (player,num) VALUES ('$player',2)") or die("Cannot insert player2 into teamPlayer" . mysql_error());

	}
}
if ($teamsOn == 'true')
{
	$selectTeam1 = mysql_query("SELECT team FROM teams WHERE num='1'") or die('Cannot select team 1 : ' . mysql_error());
	$selectTeam2 = mysql_query("SELECT team FROM teams WHERE num='2'") or die('Cannot select team 2 : ' . mysql_error());

	$team1 = mysql_result($selectTeam1,0);
	$team2 = mysql_result($selectTeam2,0);

	$selectTeam1Score = mysql_query("SELECT SUM(totalScore) FROM teamPlayers NATURAL JOIN totaltable WHERE num='1'") or die('line159' . mysql_error());
	$selectTeam2Score = mysql_query("SELECT SUM(totalScore) FROM teamPlayers NATURAL JOIN totaltable WHERE num='2'") or die('line159' . mysql_error());
	
	$team1Score = mysql_result($selectTeam1Score,0);
	$team2Score = mysql_result($selectTeam2Score,0);
}	
	echo "<div id=\"teamScores\"><table id=\"teamScores_table\">
	<tr>
		<th>$team1</th>
	</tr>

	<tr>
		<td>$team1Score</td>
	</tr>
	
	<tr>
		<th>$team2</th>
	</tr>

	<tr>
		<td>$team2Score</td>
	</tr>
	</table></div>";
/**************End Create Team and Display Teams *************/
echo"
<div id=\"footer\"><div id=\"footer_text\">&#169;2011 Jeremy Kao. All Rights Reserved.</div></div>
</body>
</html>";

?>