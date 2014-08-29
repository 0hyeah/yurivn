<?php
// ######################## SET PHP ENVIRONMENT ###########################
error_reporting(E_ALL & ~E_NOTICE);

// ########################## REQUIRE BACK-END ############################
require_once('./global.php');
global $vbulletin;   

// #######################################################################
// ######################## START MAIN SCRIPT ############################
// #######################################################################

$result = mysql_query("SELECT award_id, issue_id FROM " . TABLE_PREFIX . "award_user WHERE userid = $userinfo[userid] ORDER BY ".$vbulletin->options['aw_awardorder']."");
while ($row = mysql_fetch_array($result))
	{
	$images = mysql_query("SELECT award_name, award_icon_url FROM " . TABLE_PREFIX . "award WHERE award_id = $row[award_id]");
	while ($row2 = mysql_fetch_array($images))
		{
		echo "<a href='member.php?u=$userinfo[userid]&tab=myawards#aw_issue$row[issue_id]'><img src='$row2[award_icon_url]' alt='$row2[award_name]' border='0' /></a> &nbsp";
		}
	}
?>