<?php //loggerhead_functions.php
function createTable($player, $date)
{
	if (tableExists($player))
	{
		echo 'Homie, Player already exists...';
	}
	else 
	{
		$columns = 'choice VARCHAR(12), biol SMALLINT SIGNED, chem SMALLINT SIGNED, phys SMALLINT SIGNED, geol SMALLINT SIGNED, sosc SMALLINT SIGNED, geog SMALLINT SIGNED, marp SMALLINT SIGNED, tech SMALLINT SIGNED, count SMALLINT SIGNED, date VARCHAR(64), id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY';  
		mysql_query("CREATE TABLE $player ($columns) ENGINE MyISAM")
			or die('Cannot Create Table1...: ' . mysql_error());//Create table for each player with everything but totalscore
		mysql_query("INSERT INTO totalTable VALUES('$player', 0)")
			or die('Cannot Create Table3...: ' . mysql_error()); //Add player into totalScore Table
		mysql_query("INSERT INTO $player (choice, biol, chem, phys, geol, sosc, geog, marp, tech, count, date) VALUES ('interrupt',0,0,0,0,0,0,0,0,0,$date),('correct',0,0,0,0,0,0,0,0,0,$date),('neg',0,0,0,0,0,0,0,0,0,$date),('incorrect',0,0,0,0,0,0,0,0,0,$date)")
			or die('Cannot Create Table4...: ' . mysql_error()); //Add default values into player's table
	}
}
function createSmallTable($name)
{
	if (!tableExists($name))
	{
		if ($name == 'teams') mysql_query('CREATE TABLE teams(team VARCHAR(128) PRIMARY KEY, num TINYINT) ENGINE MyISAM') or die("dang".mysql_error());
		else mysql_query("CREATE TABLE $name (player VARCHAR(64), num TINYINT) ENGINE MyISAM") or die("yuck".mysql_error());
	}
}
function tableExists($player)
{
	$query = mysql_query("SHOW TABLES LIKE '$player'");
	return mysql_num_rows($query);
}
function sanitizeInput($input)
{
	$input = strip_tags($input);
	$input = htmlentities($input, ENT_QUOTES);
	$input = stripslashes($input);
	$input = str_replace(' ', '', $input);
	return mysql_real_escape_string($input);
}
function displayDelete()
{
	$showPlayerQuery = 'SELECT player FROM totaltable';
	$showPlayerResult = mysql_query($showPlayerQuery);
	echo '<div id="delete_players"><form action="loggerhead_main.php" method="post">';
	while($playerName = mysql_fetch_row($showPlayerResult))
	{
		echo "<label><input type=\"checkbox\" name=\"players[]\" value='$playerName[0]' />$playerName[0]</label><br />";
	}
	echo '<input type="submit" name="deletePlayers" value="Delete Players"></form></div>';
}
function parseAndUpdate($raw, $subject, $date)
{
	$choice = substr($raw, 0, 3);
	$player = substr($raw, 3);
	switch ($choice)
	{	
		case("int"):
			$choice = "interrupt";
			$updatePlayerQuery = "UPDATE $player SET $subject=$subject+1, count=count+1 WHERE choice='$choice' AND date=$date";
			break;
		case("cor"):
			$choice = "correct";
			$updatePlayerQuery = "UPDATE $player SET $subject=$subject+1, count=count+1 WHERE choice='$choice' AND date=$date";
			break;
		case("neg"):
			$choice = "neg";
			$updatePlayerQuery = "UPDATE $player SET $subject=$subject+1, count=count+1 WHERE choice='$choice' AND date=$date";
			break;
		case("inc"):
			$choice = "incorrect";
			$updatePlayerQuery = "UPDATE $player SET $subject=$subject+1, count=count+1 WHERE choice='$choice' AND date=$date";
			break;
	}
	$updatePlayerResult = mysql_query($updatePlayerQuery) or die ("shit ass mofo, can to player : " . mysql_error());
}
function getScore($player, $recentDate)
{
	$intCountQ = mysql_query("SELECT count FROM $player WHERE choice='interrupt' AND date=$recentDate");
	$corCountQ = mysql_query("SELECT count FROM $player WHERE choice='correct' AND date=$recentDate");
	$negCountQ = mysql_query("SELECT count FROM $player WHERE choice='neg' AND date=$recentDate");
	$incCountQ = mysql_query("SELECT count FROM $player WHERE choice='incorrect' AND date=$recentDate");
	
	$intCountR =  mysql_result($intCountQ,0);
	$corCountR =  mysql_result($corCountQ,0);
	$negCountR =  mysql_result($negCountQ,0);
	$incCountR =  mysql_result($incCountQ,0);
	
	return 4*($intCountR) + 4*($corCountR) - 4*($negCountR) + 0*($incCountR);
}
function getPlayerArray()
{
	$hide = hidePlayers();
	$tables = mysql_query('SHOW TABLES');
	$tablesNumRow = mysql_num_rows($tables);
	$playerArray = array();
	for ($u = 0; $u < $tablesNumRow; $u++)
	{
		$row = mysql_fetch_row($tables);
		if (in_array($row[0], $hide)) continue;
		if ($row[0] == 'teams') continue;
		$playerArray[] = $row[0];
	}
	return $playerArray;
}
function hidePlayers()
{
	$hide = array("totaltable","teamplayers","hidetable");
	$selectHides = mysql_query("SELECT person FROM hideTable");
	$hideNumRows = mysql_num_rows($selectHides);
	if ($hideNumRows)
	{
		while ($p = mysql_fetch_row($selectHides))
		{
			$hide[] = $p[0];
		}
	}
	return $hide;
}
function getRecentDate()
{
	$getPlayer = mysql_query('SELECT player FROM totaltable LIMIT 1') or die ("lol".mysql_error());
	$player = mysql_result($getPlayer,0);
	$getDate = mysql_query("SELECT date FROM $player ORDER BY id DESC") or die ("eee".mysql_error());
	return mysql_result($getDate,0);
}
function updateToNewDate($todayDate)
{
	$recentDate = getRecentDate();
	$playerArray = getPlayerArray();
	foreach ($playerArray as $p)
	{
		$getDateQ = mysql_query("SELECT date FROM $p ORDER BY ID DESC");
		$getRecent = mysql_result($getDateQ, 0);
		if ($getRecent != $todayDate)
		{
			mysql_query("INSERT INTO $p(choice, biol, chem, phys, geol, sosc, geog, marp, tech, count, date) VALUES ('interrupt',0,0,0,0,0,0,0,0,0,$todayDate),('correct',0,0,0,0,0,0,0,0,0,$todayDate),('neg',0,0,0,0,0,0,0,0,0,$todayDate),('incorrect',0,0,0,0,0,0,0,0,0,$todayDate)")
				or die('Cannot Create arghhh: ' . mysql_error());
		}
	}
	echo   "<script type=\"text/javascript\" language=\"javascript\">window.location.reload();</script>";
}
function deleteFromDatabase($playerToDelete,$numPlayers)
{
	$deleteTableQuery = "DROP TABLE $playerToDelete";
	$deleteTableResult = mysql_query($deleteTableQuery);
	$deleteEntryQuery = "DELETE FROM totaltable WHERE player='$playerToDelete'";
	$deleteEntryResult = mysql_query($deleteEntryQuery);
}
?>