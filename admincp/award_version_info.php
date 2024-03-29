<?php

// ######################## SET PHP ENVIRONMENT ###########################
error_reporting(E_ALL & ~E_NOTICE);

// ##################### DEFINE IMPORTANT CONSTANTS #######################
define('NO_REGISTER_GLOBALS', 1);
define('THIS_SCRIPT', 'award_version_info.php');

// #################### PRE-CACHE TEMPLATES AND DATA ######################
$phrasegroups = array();
$specialtemplates = array();

// ########################## REQUIRE BACK-END ############################
require_once('./global.php');
require_once(DIR . '/includes/class_bbcode.php');
$bbcode_parser =& new vB_BbCodeParser($vbulletin, fetch_tag_list());

$this_script = 'award_version_info';

// ######################## CHECK ADMIN PERMISSIONS #######################
if (!can_administer('canadminusers'))
{
	print_cp_no_permission();
}

// ########################################################################
// ######################### START MAIN SCRIPT ############################
// ########################################################################
print_cp_header($vbphrase['awards']);

// Get Latest Version from vBulletin.org
$productVerCheckURL = "http://www.vbulletin.org/forum/misc.php?do=productcheck&pid=YaAS4-38";
$latestVersion = file_get_contents($productVerCheckURL);
$latestVersion = ereg_replace("[^A-Za-z0-9.]", "", $latestVersion );
$latestVersion = substr($latestVersion, 23);
$latestVersion = ereg_replace("[^0-9.]", "", $latestVersion );

// Get Current Version
$array = $db->query_first("SELECT version FROM " . TABLE_PREFIX . "product WHERE productid = 'yet_another_award_system' LIMIT 1");  
$currentVersion = $array[version];

// Begin Output
echo "Current Version:  $currentVersion";
echo "<br />";
echo "Latest Version:  $latestVersion";
echo "<br />";
echo "<br />";

if ( $currentVersion > $latestVersion ) {
	echo "This is a beta build.";
	}
	
if ( $currentVersion == $latestVersion ) {
	echo "This build is current.";
	}
	
if ( $currentVersion < $latestVersion ) {
	echo "An updated version has been released.";
	}

// #############################################################################

print_cp_footer();

?>