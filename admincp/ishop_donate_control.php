<?php
  /*======================================================================*\
   || #################################################################### ||
   || # IShop                                                            # ||
   || # Copyright Blaine0002(C) 2005 All rights reserved.                # ||
   || # ---------------------------------------------------------------- # ||
   || # For use with vBulletin Version 3.5.4                             # ||
   || # http://www.vbulletin.com | http://www.vbulletin.com/license.html # ||
   || # Discussion and support available at                              # ||
   || # http://www.vbulletin.org/forum/showthread.php?t=100344           # ||
   || #################################################################### ||
   \*======================================================================*/
require_once("ishop_functions.php");
// ###################### Donate To All Members ########################
if ($_GET['act'] == "donate_to_all") {
print_cp_header("Donate To Members");


$uoption="<option value='All'>All Usergroups</option>";
$usergroups = $db->query("SELECT * FROM " . TABLE_PREFIX . "usergroup ORDER BY title");
while ($usergroup = $db->fetch_array($usergroups)){
$uoption.="<option value='{$usergroup['usergroupid']}'>{$usergroup['title']}</option>";
}


	print_form_header('ishop_donate_control', 'do_donate_all');
	print_table_header("Donate To Members");


	print_input_row("Amount of {$vbphrase['money']} to donate", 'amount','0');
	print_label_row("Choose Usergroup<dfn>Will only donate to members within this usergroup</dfn>", '<select name="usergroup" class="bginput">'.$uoption.'</select>');

	print_submit_row("Donate {$vbphrase['money']} To Members", 0);
	
	//member
		print_form_header('ishop_donate_control', 'do_donate_member');
		print_table_header("Donate To Member");


	print_input_row("Amount of {$vbphrase['money']} to donate", 'amount2','0');
	print_input_row("Choose Username", 'username','');

	print_submit_row("Donate {$vbphrase['money']} To Member", 0);
	
	print_cp_footer();
	exit;
}

// ###################### Do Donate To All Members ########################
if ($_POST['do'] == "do_donate_all") {
print_cp_header("Donate To Members");

if($_POST['usergroup']!="All"){
	if(!$db->query_first("SELECT * FROM " . TABLE_PREFIX . "usergroup where usergroupid='{$_POST['usergroup']}'")){
	echo "Invalid Usergroup Specified. [ <a href='javascript:history.go(-1)'>Back</a> ]";
	exit;
	}
}

$_POST['amount']=(int)$_POST['amount'];
if($_POST['usergroup']=="All"){
$db->query("update " . TABLE_PREFIX . "user set " . $vbulletin->options['ishop_pointfield'] . "=" . $vbulletin->options['ishop_pointfield'] . "+'{$_POST['amount']}'");
} else {
$mcol=$db->query("select * from ".TABLE_PREFIX."user where usergroupid='{$_POST['usergroup']}'");
$membersc=array();
	while($them = $db->fetch_array($mcol)){
	$membersc[]=$them;
	}
	if($membersc[0]){ 
		foreach($membersc as $the_memberg){
		$db->query("update " . TABLE_PREFIX . "user set " . $vbulletin->options['ishop_pointfield'] . "=" . $vbulletin->options['ishop_pointfield'] . "+'{$_POST['amount']}' where userid='{$the_memberg['userid']}'");
		}
	}
}

	define('CP_REDIRECT', 'ishop_donate_control.php?act=donate_to_all');
	print_stop_message('ishop_donate_members');
}

// ###################### Do Donate To Member ########################
if ($_POST['do'] == "do_donate_member") {
print_cp_header("Donate To Member");


$_POST['amount2']=(int)$_POST['amount2'];

$db->query("update " . TABLE_PREFIX . "user set " . $vbulletin->options['ishop_pointfield'] . "=" . $vbulletin->options['ishop_pointfield'] . "+'{$_POST['amount2']}' where username='{$_POST['username']}'");


	define('CP_REDIRECT', 'ishop_donate_control.php?act=donate_to_all');
	print_stop_message('ishop_donate_members');
}
?>