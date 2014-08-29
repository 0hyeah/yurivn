<?php
/*========================================================================*\
|| ###################################################################### ||
|| # 	     	  [HQTH] Friend For Sale v 1.0		     				# ||
|| #              for vBulletin Version 3.8.x                        	# ||
|| #              http://hoiquantinhoc.com                          	# ||
|| #              Coded by tieuquynhi - Designed by Mr.Kun              # ||
|| ###################################################################### ||
\*========================================================================*/

// ######################## SET PHP ENVIRONMENT ###########################
error_reporting(E_ALL & ~E_NOTICE & ~8192);
@set_time_limit(0);
define('THIS_SCRIPT', 'hqth_ffs_upgrade');

// ################### PRE-CACHE TEMPLATES AND DATA ######################

$specialtemplates = array();
// pre-cache templates used by all actions
$globaltemplates = array();
$actiontemplates = array();

// ######################### REQUIRE BACK-END ############################
require_once('./global.php');

// #################### HQTH Friend For Sale Upgrade ####################

$vbulletin->db->hide_errors();

$vbulletin->db->errno = 0;
$record=$vbulletin->db->query_read("SELECT * FROM " . TABLE_PREFIX . "hqth_ffs_action");
if($vbulletin->db->errno())
{
	$vbulletin->db->query_write("CREATE TABLE `" . TABLE_PREFIX . "hqth_ffs_action` (
								`actionid` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
								`actionname` VARCHAR( 200 ) NOT NULL ,
								`actioncat` VARCHAR( 250 ) NOT NULL,
								`actiondelay` INT( 10 ) NOT NULL,
								`money_master` INT( 11 ) NOT NULL ,
								`money_pet` INT( 11 ) NOT NULL
								)");
	echo "Insert action table...<br>";
}


$vbulletin->db->errno = 0;
$record=$vbulletin->db->query_read("SELECT * FROM " . TABLE_PREFIX . "hqth_ffs_action where actionname='buy' AND actioncat='buy'");
if($vbulletin->db->num_rows($record)==0)
{
	$vbulletin->db->query_write("INSERT INTO `".TABLE_PREFIX."hqth_ffs_action` (
								`actionname` ,`actioncat` ,`actiondelay` ,`money_master` ,`money_pet`)
								VALUES ('buy', 'buy', '0', '0', '0')");
	echo "Insert buy to action table...<br>";
}
$vbulletin->db->errno = 0;
$record=$vbulletin->db->query_read("SELECT * FROM " . TABLE_PREFIX . "hqth_ffs_action where actionname='redeem' AND actioncat='redeem'");
if($vbulletin->db->num_rows($record)==0)
{
	$vbulletin->db->query_write("INSERT INTO `".TABLE_PREFIX."hqth_ffs_action` (
								`actionname` ,`actioncat` ,`actiondelay` ,`money_master` ,`money_pet`)
								VALUES ('redeem', 'redeem', '0', '0', '0')");
	echo "Insert redeem to action table...<br>";
}
$vbulletin->db->errno = 0;
$record=$vbulletin->db->query_read("SELECT * FROM " . TABLE_PREFIX . "hqth_ffs_action where actionname='saleoff' AND actioncat='saleoff'");
if($vbulletin->db->num_rows($record)==0)
{
	$vbulletin->db->query_write("INSERT INTO `".TABLE_PREFIX."hqth_ffs_action` (
								`actionname` ,`actioncat` ,`actiondelay` ,`money_master` ,`money_pet`)
								VALUES ('saleoff', 'saleoff', '0', '0', '0')");
	echo "Insert saleoff to action table...<br>";
}
$vbulletin->db->errno = 0;
$record=$vbulletin->db->query_read("SELECT * FROM " . TABLE_PREFIX . "hqth_ffs_action where actionname='freed' AND actioncat='freed'");
if($vbulletin->db->num_rows($record)==0)
{
	$vbulletin->db->query_write("INSERT INTO `".TABLE_PREFIX."hqth_ffs_action` (
								`actionname` ,`actioncat` ,`actiondelay` ,`money_master` ,`money_pet`)
								VALUES ('freed', 'freed', '0', '0', '0')");
	echo "Insert freed to action table...<br>";
}
$vbulletin->db->errno = 0;
$record=$vbulletin->db->query_read("SELECT * FROM " . TABLE_PREFIX . "hqth_ffs_actionlog");
if($vbulletin->db->errno())
{
	$vbulletin->db->query_write("CREATE TABLE `" . TABLE_PREFIX . "hqth_ffs_actionlog` (
								`logid` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
								`userdo` INT( 11 ) NOT NULL ,
								`usertarget` INT( 11 ) NOT NULL ,
								`logtype` INT( 10 ) NOT NULL ,
								`datedo` INT( 10 ) NOT NULL,
								`dateend` INT( 10 ) NOT NULL,
								`active` INT ( 1 ) NOT NULL DEFAULT '1'
								)");
	echo "Insert action log table...<br>";
}
$vbulletin->db->errno = 0;
$record=$vbulletin->db->query_read("SELECT * FROM " . TABLE_PREFIX . "hqth_ffs_sale");
if($vbulletin->db->errno())
{
			$vbulletin->db->query_write("CREATE TABLE `" . TABLE_PREFIX . "hqth_ffs_sale` (
								`saleid` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
								`usersale` INT( 11 ) NOT NULL ,
								`usertarget` INT( 11 ) NOT NULL ,
								`notice` VARCHAR( 200 ) NOT NULL,
								`datebuy` INT( 10 ) NOT NULL,
								`status` BIT( 1 ) NOT NULL,
								`price` DOUBLE NOT NULL
								)");
	echo "Insert sale table...<br>";
}
$vbulletin->db->errno = 0;
$record=$vbulletin->db->query_read("SELECT * FROM " . TABLE_PREFIX . "hqth_ffs_saleoff");
if($vbulletin->db->errno())
{

			$vbulletin->db->query_write("CREATE TABLE `" . TABLE_PREFIX . "hqth_ffs_saleoff` (
								`saleoffid` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
								`usersale` INT( 11 ) NOT NULL ,
								`usertarget` INT( 11 ) NOT NULL ,
								`price` DOUBLE NOT NULL
								)");
	echo "Insert saleoff table...<br>";
}						
$vbulletin->db->errno = 0;
$record=$vbulletin->db->query_read("SELECT active FROM " . TABLE_PREFIX . "hqth_ffs_actionlog");
if($vbulletin->db->errno())
{
	$vbulletin->db->query_write("ALTER TABLE `" . TABLE_PREFIX . "hqth_ffs_actionlog` ADD `active` INT( 1 ) NOT NULL DEFAULT '1'");
	echo "Alter table action log successfully<br>";
}
$vbulletin->db->show_errors();
echo "<br><b>Install HQTH Friend For Sale Successfully<b>";
/*======================================================================*\
|| ####################################################################
|| # CVS: $RCSfile: ffs.php,v $ - $Revision: 1.0.0 $
|| ####################################################################
\*======================================================================*/
?>