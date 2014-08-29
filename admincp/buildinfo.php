<?php
/*======================================================================*\
|| #################################################################### ||
|| # vBulletin 4.2.2 Alpha 1 - Licence Number VBFSA2W3VC
|| # ---------------------------------------------------------------- # ||
|| # Copyright ©2000-2013 vBulletin Solutions Inc. All Rights Reserved. ||
|| # This file may not be redistributed in whole or significant part. # ||
|| # ---------------- VBULLETIN IS NOT FREE SOFTWARE ---------------- # ||
|| # http://www.vbulletin.com | http://www.vbulletin.com/license.html # ||
|| #################################################################### ||
\*======================================================================*/

// ######################## SET PHP ENVIRONMENT ###########################
error_reporting(E_ALL & ~E_NOTICE);

// ##################### DEFINE IMPORTANT CONSTANTS #######################
define('CVS_REVISION', '$RCSfile$ - $Revision: 36270 $');

// #################### PRE-CACHE TEMPLATES AND DATA ######################
$phrasegroups = array();
$specialtemplates = array();

// ########################## REQUIRE BACK-END ############################
require_once(dirname(__FILE__) . '/global.php');

// #############################################################################
// ########################### START MAIN SCRIPT ###############################
// #############################################################################


$version = "422a1";
$svn_branch = "svn://svn.jelsoft.com/vbulletin/suite/4.2/tags/2013-09-06-422_01";
$svn_last_checkin = "77418";
$svn_last_checkin_date = "1378503333";

$rows = array (
	array("version", $version),
	array("branch", "<a href=\"$svn_branch\">$svn_branch</a>"),
	array("last check in", 	$svn_last_checkin . " (" . date("m-d-y H:i:s T", $svn_last_checkin_date) . ")"),
);


print_cp_header();
print_form_header('index', 'home');
print_table_header("Build Information", 2);
foreach($rows as $row)
{
	print_cells_row($row, 0, 0, -5, 'top', 1, 1);
}
print_table_footer();
print_cp_footer();
