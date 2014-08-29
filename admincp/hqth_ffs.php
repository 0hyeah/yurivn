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

// ##################### DEFINE IMPORTANT CONSTANTS #######################
define('NO_REGISTER_GLOBALS', 1);
define('THIS_SCRIPT', 'hqthffs_admin');

// ################### PRE-CACHE TEMPLATES AND DATA ######################
$phrasegroups = array('cpuser', 'user');
$specialtemplates = array();
$globaltemplates = array();
$actiontemplates = array();

// ########################## REQUIRE BACK-END ############################
require_once('./global.php');

// ######################## CHECK ADMIN PERMISSIONS #######################
if (!can_administer('canadminusers'))
{
        print_cp_no_permission();
}

// ############################# LOG ACTION ###############################
log_admin_action(iif($_REQUEST['id'],'do=' . $_REQUEST['do'] . '&id=' . $_REQUEST['id']));

// ########################################################################
// ######################### START MAIN SCRIPT ############################
// ########################################################################
print_cp_header($vbphrase['hqthffs']);

if ($_REQUEST['do'] == "comfort")
{
	if($_REQUEST['act']=="insert")
	{
		$vbulletin->input->clean_gpc('p','actionname', TYPE_NOHTML);
		$vbulletin->input->clean_gpc('p','actiondelay', TYPE_INT);
		$vbulletin->input->clean_gpc('p','money_master', TYPE_INT);
		$vbulletin->input->clean_gpc('p','money_pet', TYPE_INT);
		$checkexits=$vbulletin->db->query_first("SELECT * FROM " . TABLE_PREFIX . "hqth_ffs_action WHERE actionname='".$vbulletin->GPC['actionname']."' AND actioncat='".$_REQUEST['do']."'");
		if($checkexits)
		{
			print_cp_message($vbphrase['hqthffs_item_exist'], 'hqth_ffs.php?' . $vbulletin->session->vars['sessionurl'] . 'do='.$_REQUEST['do']);
		}
		else
		{
			$db->query_write("INSERT INTO " . TABLE_PREFIX . "hqth_ffs_action 					
							(actionname,actioncat,actiondelay,money_master,money_pet)
							VALUES('".$vbulletin->GPC['actionname']."','".$_REQUEST['do']."',".$vbulletin->GPC['actiondelay'].",".$vbulletin->GPC['money_master'].",".$vbulletin->GPC['money_pet'].")");
			print_cp_message($vbphrase['hqthffs_item_add_success'], 'hqth_ffs.php?' . $vbulletin->session->vars['sessionurl'] . 'do='.$_REQUEST['do']);
		}
	}
	elseif($_REQUEST['act']=="add")
	{
		print_form_header('hqth_ffs',$_REQUEST['do']);
		print_table_header($vbphrase['new'].' '.$vbphrase['hqthffs_'.$_REQUEST['do']], 0);
		construct_hidden_code('act', 'insert');
		print_input_row($vbphrase['hqthffs_actionname'], 'actionname');
		print_input_row($vbphrase['hqthffs_actiondelay'], 'actiondelay');
		print_input_row($vbphrase['hqthffs_moneymaster'], 'money_master');
		print_input_row($vbphrase['hqthffs_moneypet'], 'money_pet');
		print_submit_row();
		print_table_footer();
	}
	elseif($_REQUEST['act']=="update")
	{
		$vbulletin->input->clean_gpc('p','actionid', TYPE_INT);
		$vbulletin->input->clean_gpc('p','actionname', TYPE_NOHTML);
		$vbulletin->input->clean_gpc('p','actiondelay', TYPE_INT);
		$vbulletin->input->clean_gpc('p','money_master', TYPE_INT);
		$vbulletin->input->clean_gpc('p','money_pet', TYPE_INT);
		$db->query_write("UPDATE " . TABLE_PREFIX . "hqth_ffs_action 					
						SET actionname='".$vbulletin->GPC['actionname']."',actiondelay=".$vbulletin->GPC['actiondelay'].",money_master=".$vbulletin->GPC['money_master'].",money_pet=".$vbulletin->GPC['money_pet']." WHERE actionid=".$_REQUEST['actionid']);
		print_cp_message($vbphrase['hqthffs_item_update_success'], 'hqth_ffs.php?' . $vbulletin->session->vars['sessionurl'] . 'do='.$_REQUEST['do']);
	}
	elseif($_REQUEST['act']=="edit")
	{
		$checkexits=$vbulletin->db->query_first("SELECT * FROM " . TABLE_PREFIX . "hqth_ffs_action WHERE actionid='".$_REQUEST['id']."' AND actioncat='".$_REQUEST['do']."'");
		if(!$checkexits)
		{
			print_cp_message($vbphrase['hqthffs_item_exist'], 'hqth_ffs.php?' . $vbulletin->session->vars['sessionurl'] . 'do='.$_REQUEST['do']);
		}
		else
		{
			print_form_header('hqth_ffs',$_REQUEST['do']);
			print_table_header($vbphrase['edit'].' '.$vbphrase['hqthffs_'.$_REQUEST['do']], 0);
			construct_hidden_code('act', 'update');
			construct_hidden_code('actionid', $_REQUEST['id']);
			print_input_row($vbphrase['hqthffs_actionname'], 'actionname',$checkexits['actionname']);
			print_input_row($vbphrase['hqthffs_actiondelay'], 'actiondelay',$checkexits['actiondelay']);
			print_input_row($vbphrase['hqthffs_moneymaster'], 'money_master',$checkexits['money_master']);
			print_input_row($vbphrase['hqthffs_moneypet'], 'money_pet',$checkexits['money_pet']);
			print_submit_row();
			print_table_footer();
		}
	}
	elseif($_REQUEST['act'] == "del")
	{
		$vbulletin->db->query_write("DELETE FROM ".TABLE_PREFIX."hqth_ffs_action WHERE actioncat='".$_REQUEST['do']."' AND actionid=".$_REQUEST['id']);
		print_cp_message($vbphrase['hqthffs_item_del_success'], 'hqth_ffs.php?' . $vbulletin->session->vars['sessionurl'] . 'do='.$_REQUEST['do']);
	}
	else
	{
		$catlist = $db->query_read("SELECT *
					   FROM " . TABLE_PREFIX . "hqth_ffs_action
					   WHERE actioncat='".$_REQUEST['do']."'
					   ORDER BY actionname");
	
		print_form_header();
		print_table_header($vbphrase['hqthffs_'.$_REQUEST['do']], 5);
	
		$header = array();
		$header[] = $vbphrase['hqthffs_actionname'];
		$header[] = $vbphrase['hqthffs_actiondelay'];
		$header[] = $vbphrase['hqthffs_moneymaster'];
		$header[] = $vbphrase['hqthffs_moneypet'];
		$header[] = $vbphrase['option'];
		print_cells_row($header, 1, 0, 1);
		$cell = array();
	
		while ($last = $db->fetch_array($catlist))
		{
		   $cell[] = $last['actionname'];
		   $cell[] = $last['actiondelay'];
		   $cell[] = $last['money_master'];
		   $cell[] = $last['money_pet'];
		   $cell[] = "<a href=hqth_ffs.php?do=".$_REQUEST['do']."&act=del&id=" . $last['actionid'] . ">" . $vbphrase['delete'] . "</a> <a href=hqth_ffs.php?do=".$_REQUEST['do']."&act=edit&id=" . $last['actionid'] . ">" . $vbphrase['edit'] . "</a>";
		   print_cells_row($cell, 0, 0, 1,1, 0, 1);
		   unset($cell);
		   $cell = array();
		}
		print_table_footer();
		print_label_row("<br><center><a href='hqth_ffs.php?do=".$_REQUEST['do']."&act=add'>".$vbphrase['new']." ".$vbphrase['hqthffs_'.$_REQUEST['do']]."</a>");
	}
}

//***********************************************************
elseif ($_REQUEST['do'] == "bullying")
{
	if($_REQUEST['act']=="insert")
	{
		$vbulletin->input->clean_gpc('p','actionname', TYPE_NOHTML);
		$vbulletin->input->clean_gpc('p','actiondelay', TYPE_INT);
		$vbulletin->input->clean_gpc('p','money_master', TYPE_INT);
		$vbulletin->input->clean_gpc('p','money_pet', TYPE_INT);
		$checkexits=$vbulletin->db->query_first("SELECT * FROM " . TABLE_PREFIX . "hqth_ffs_action WHERE actionname='".$vbulletin->GPC['actionname']."' AND actioncat='".$_REQUEST['do']."'");
		if($checkexits)
		{
			print_cp_message($vbphrase['hqthffs_item_exist'], 'hqth_ffs.php?' . $vbulletin->session->vars['sessionurl'] . 'do='.$_REQUEST['do']);
		}
		else
		{
			$db->query_write("INSERT INTO " . TABLE_PREFIX . "hqth_ffs_action 					
							(actionname,actioncat,actiondelay,money_master,money_pet)
							VALUES('".$vbulletin->GPC['actionname']."','".$_REQUEST['do']."',".$vbulletin->GPC['actiondelay'].",".$vbulletin->GPC['money_master'].",".$vbulletin->GPC['money_pet'].")");
			print_cp_message($vbphrase['hqthffs_item_add_success'], 'hqth_ffs.php?' . $vbulletin->session->vars['sessionurl'] . 'do='.$_REQUEST['do']);
		}
	}
	elseif($_REQUEST['act']=="add")
	{
		print_form_header('hqth_ffs',$_REQUEST['do']);
		print_table_header($vbphrase['new'].' '.$vbphrase['hqthffs_'.$_REQUEST['do']], 0);
		construct_hidden_code('act', 'insert');
		print_input_row($vbphrase['hqthffs_actionname'], 'actionname');
		print_input_row($vbphrase['hqthffs_actiondelay'], 'actiondelay');
		print_input_row($vbphrase['hqthffs_moneymaster'], 'money_master');
		print_input_row($vbphrase['hqthffs_moneypet'], 'money_pet');
		print_submit_row();
		print_table_footer();
	}
	elseif($_REQUEST['act']=="update")
	{
		$vbulletin->input->clean_gpc('p','actionid', TYPE_INT);
		$vbulletin->input->clean_gpc('p','actionname', TYPE_NOHTML);
		$vbulletin->input->clean_gpc('p','actiondelay', TYPE_INT);
		$vbulletin->input->clean_gpc('p','money_master', TYPE_INT);
		$vbulletin->input->clean_gpc('p','money_pet', TYPE_INT);
		$db->query_write("UPDATE " . TABLE_PREFIX . "hqth_ffs_action 					
						SET actionname='".$vbulletin->GPC['actionname']."',actiondelay=".$vbulletin->GPC['actiondelay'].",money_master=".$vbulletin->GPC['money_master'].",money_pet=".$vbulletin->GPC['money_pet']." WHERE actionid=".$_REQUEST['actionid']);
		print_cp_message($vbphrase['hqthffs_item_update_success'], 'hqth_ffs.php?' . $vbulletin->session->vars['sessionurl'] . 'do='.$_REQUEST['do']);
	}
	elseif($_REQUEST['act']=="edit")
	{
		$checkexits=$vbulletin->db->query_first("SELECT * FROM " . TABLE_PREFIX . "hqth_ffs_action WHERE actionid='".$_REQUEST['id']."' AND actioncat='".$_REQUEST['do']."'");
		if(!$checkexits)
		{
			print_cp_message($vbphrase['hqthffs_item_exist'], 'hqth_ffs.php?' . $vbulletin->session->vars['sessionurl'] . 'do='.$_REQUEST['do']);
		}
		else
		{
			print_form_header('hqth_ffs',$_REQUEST['do']);
			print_table_header($vbphrase['edit'].' '.$vbphrase['hqthffs_'.$_REQUEST['do']], 0);
			construct_hidden_code('act', 'update');
			construct_hidden_code('actionid', $_REQUEST['id']);
			print_input_row($vbphrase['hqthffs_actionname'], 'actionname',$checkexits['actionname']);
			print_input_row($vbphrase['hqthffs_actiondelay'], 'actiondelay',$checkexits['actiondelay']);
			print_input_row($vbphrase['hqthffs_moneymaster'], 'money_master',$checkexits['money_master']);
			print_input_row($vbphrase['hqthffs_moneypet'], 'money_pet',$checkexits['money_pet']);
			print_submit_row();
			print_table_footer();
		}
	}
	elseif($_REQUEST['act'] == "del")
	{
		$vbulletin->db->query_write("DELETE FROM ".TABLE_PREFIX."hqth_ffs_action WHERE actioncat='".$_REQUEST['do']."' AND actionid=".$_REQUEST['id']);
		print_cp_message($vbphrase['hqthffs_item_del_success'], 'hqth_ffs.php?' . $vbulletin->session->vars['sessionurl'] . 'do='.$_REQUEST['do']);
	}
	else
	{
		$catlist = $db->query_read("SELECT *
					   FROM " . TABLE_PREFIX . "hqth_ffs_action
					   WHERE actioncat='".$_REQUEST['do']."'
					   ORDER BY actionname");
	

		print_form_header();
		print_table_header($vbphrase['hqthffs_'.$_REQUEST['do']], 5);
	
		$header = array();
		$header[] = $vbphrase['hqthffs_actionname'];
		$header[] = $vbphrase['hqthffs_actiondelay'];
		$header[] = $vbphrase['hqthffs_moneymaster'];
		$header[] = $vbphrase['hqthffs_moneypet'];
		$header[] = $vbphrase['option'];
		print_cells_row($header, 1, 0, 1);
		$cell = array();
	
		while ($last = $db->fetch_array($catlist))
		{
		   $cell[] = $last['actionname'];
		   $cell[] = $last['actiondelay'];
		   $cell[] = $last['money_master'];
		   $cell[] = $last['money_pet'];
		   $cell[] = "<a href=hqth_ffs.php?do=".$_REQUEST['do']."&act=del&id=" . $last['actionid'] . ">" . $vbphrase['delete'] . "</a> <a href=hqth_ffs.php?do=".$_REQUEST['do']."&act=edit&id=" . $last['actionid'] . ">" . $vbphrase['edit'] . "</a>";
		   print_cells_row($cell, 0, 0, 1,1, 0, 1);
		   unset($cell);
		   $cell = array();
		}
		print_table_footer();
		print_label_row("<br><center><a href='hqth_ffs.php?do=".$_REQUEST['do']."&act=add'>".$vbphrase['new']." ".$vbphrase['hqthffs_'.$_REQUEST['do']]."</a>");
	}
}

//***********************************************************
elseif ($_REQUEST['do'] == "wheedle")
{
	if($_REQUEST['act']=="insert")
	{
		$vbulletin->input->clean_gpc('p','actionname', TYPE_NOHTML);
		$vbulletin->input->clean_gpc('p','actiondelay', TYPE_INT);
		$vbulletin->input->clean_gpc('p','money_master', TYPE_INT);
		$vbulletin->input->clean_gpc('p','money_pet', TYPE_INT);
		$checkexits=$vbulletin->db->query_first("SELECT * FROM " . TABLE_PREFIX . "hqth_ffs_action WHERE actionname='".$vbulletin->GPC['actionname']."' AND actioncat='".$_REQUEST['do']."'");
		if($checkexits)
		{
			print_cp_message($vbphrase['hqthffs_item_exist'], 'hqth_ffs.php?' . $vbulletin->session->vars['sessionurl'] . 'do='.$_REQUEST['do']);
		}
		else
		{
			$db->query_write("INSERT INTO " . TABLE_PREFIX . "hqth_ffs_action 					
							(actionname,actioncat,actiondelay,money_master,money_pet)
							VALUES('".$vbulletin->GPC['actionname']."','".$_REQUEST['do']."',".$vbulletin->GPC['actiondelay'].",".$vbulletin->GPC['money_pet'].",".$vbulletin->GPC['money_master'].")");
			print_cp_message($vbphrase['hqthffs_item_add_success'], 'hqth_ffs.php?' . $vbulletin->session->vars['sessionurl'] . 'do='.$_REQUEST['do']);
		}
	}
	elseif($_REQUEST['act']=="add")
	{
		print_form_header('hqth_ffs',$_REQUEST['do']);
		print_table_header($vbphrase['new'].' '.$vbphrase['hqthffs_'.$_REQUEST['do']], 0);
		construct_hidden_code('act', 'insert');
		print_input_row($vbphrase['hqthffs_actionname'], 'actionname');
		print_input_row($vbphrase['hqthffs_actiondelay'], 'actiondelay');
		print_input_row($vbphrase['hqthffs_moneymaster'], 'money_master');
		print_input_row($vbphrase['hqthffs_moneypet'], 'money_pet');
		print_submit_row();
		print_table_footer();
	}
	elseif($_REQUEST['act']=="update")
	{
		$vbulletin->input->clean_gpc('p','actionid', TYPE_INT);
		$vbulletin->input->clean_gpc('p','actionname', TYPE_NOHTML);
		$vbulletin->input->clean_gpc('p','actiondelay', TYPE_INT);
		$vbulletin->input->clean_gpc('p','money_master', TYPE_INT);
		$vbulletin->input->clean_gpc('p','money_pet', TYPE_INT);
		$db->query_write("UPDATE " . TABLE_PREFIX . "hqth_ffs_action 					
						SET actionname='".$vbulletin->GPC['actionname']."',actiondelay=".$vbulletin->GPC['actiondelay'].",money_master=".$vbulletin->GPC['money_master'].",money_pet=".$vbulletin->GPC['money_pet']." WHERE actionid=".$_REQUEST['actionid']);
		print_cp_message($vbphrase['hqthffs_item_update_success'], 'hqth_ffs.php?' . $vbulletin->session->vars['sessionurl'] . 'do='.$_REQUEST['do']);
	}
	elseif($_REQUEST['act']=="edit")
	{
		$checkexits=$vbulletin->db->query_first("SELECT * FROM " . TABLE_PREFIX . "hqth_ffs_action WHERE actionid='".$_REQUEST['id']."' AND actioncat='".$_REQUEST['do']."'");
		if(!$checkexits)
		{
			print_cp_message($vbphrase['hqthffs_item_exist'], 'hqth_ffs.php?' . $vbulletin->session->vars['sessionurl'] . 'do='.$_REQUEST['do']);
		}
		else
		{
			print_form_header('hqth_ffs',$_REQUEST['do']);
			print_table_header($vbphrase['edit'].' '.$vbphrase['hqthffs_'.$_REQUEST['do']], 0);
			construct_hidden_code('act', 'update');
			construct_hidden_code('actionid', $_REQUEST['id']);
			print_input_row($vbphrase['hqthffs_actionname'], 'actionname',$checkexits['actionname']);
			print_input_row($vbphrase['hqthffs_actiondelay'], 'actiondelay',$checkexits['actiondelay']);
			print_input_row($vbphrase['hqthffs_moneymaster'], 'money_master',$checkexits['money_pet']);
			print_input_row($vbphrase['hqthffs_moneypet'], 'money_pet',$checkexits['money_master']);
			print_submit_row();
			print_table_footer();
		}
	}
	elseif($_REQUEST['act'] == "del")
	{
		$vbulletin->db->query_write("DELETE FROM ".TABLE_PREFIX."hqth_ffs_action WHERE actioncat='".$_REQUEST['do']."' AND actionid=".$_REQUEST['id']);
		print_cp_message($vbphrase['hqthffs_item_del_success'], 'hqth_ffs.php?' . $vbulletin->session->vars['sessionurl'] . 'do='.$_REQUEST['do']);
	}
	else
	{
		$catlist = $db->query_read("SELECT *
					   FROM " . TABLE_PREFIX . "hqth_ffs_action
					   WHERE actioncat='".$_REQUEST['do']."'
					   ORDER BY actionname");
	

		print_form_header();
		print_table_header($vbphrase['hqthffs_'.$_REQUEST['do']], 5);
	
		$header = array();
		$header[] = $vbphrase['hqthffs_actionname'];
		$header[] = $vbphrase['hqthffs_actiondelay'];
		$header[] = $vbphrase['hqthffs_moneymaster'];
		$header[] = $vbphrase['hqthffs_moneypet'];
		$header[] = $vbphrase['option'];
		print_cells_row($header, 1, 0, 1);
		$cell = array();
	
		while ($last = $db->fetch_array($catlist))
		{
		   $cell[] = $last['actionname'];
		   $cell[] = $last['actiondelay'];
		   $cell[] = $last['money_pet'];
		   $cell[] = $last['money_master'];
		   $cell[] = "<a href=hqth_ffs.php?do=".$_REQUEST['do']."&act=del&id=" . $last['actionid'] . ">" . $vbphrase['delete'] . "</a> <a href=hqth_ffs.php?do=".$_REQUEST['do']."&act=edit&id=" . $last['actionid'] . ">" . $vbphrase['edit'] . "</a>";
		   print_cells_row($cell, 0, 0, 1,1, 0, 1);
		   unset($cell);
		   $cell = array();
		}
		print_table_footer();
		print_label_row("<br><center><a href='hqth_ffs.php?do=".$_REQUEST['do']."&act=add'>".$vbphrase['new']." ".$vbphrase['hqthffs_'.$_REQUEST['do']]."</a>");
	}
}

print_cp_footer();
?>