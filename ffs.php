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
define('THIS_SCRIPT', 'hqth_ffs');

// ################### PRE-CACHE TEMPLATES AND DATA ######################

$specialtemplates = array();
// pre-cache templates used by all actions
$globaltemplates = array(
        'hqth_ffs_anui',
		'hqth_ffs_batnat',
		'hqth_ffs_chuocthan',
		'hqth_ffs_giamgia',
		'hqth_ffs_home',
		'hqth_ffs_ninhchu',
		'hqth_ffs_sieuthi',
		'hqth_ffs_thatudo',
		'hqth_ffs_posbit_title',
		'hqth_ffs_main',
		'hqth_ffs_menutop',
		'hqth_ffs_menuleft',
		'hqth_ffs_pet_bit',
		'hqth_ffs_oldmaster_bit',
		'hqth_ffs_action_bit',
		'hqth_ffs_logusing_bit',
		'hqth_ffs_pet_bit'
);
$actiontemplates = array();

// ######################### REQUIRE BACK-END ############################
require_once('./global.php');

// #################### HQTH Friend For Sale Funcions ####################

require_once('./includes/functions_ffs.php');

// #################### HQTH Friend For Sale Main ########################

if($vbulletin->options['hqthffs_enable'])
{
	if($vbulletin->userinfo['userid']==0)
	{
		print_no_permission();
	}
	
	$vbulletin->input->clean_array_gpc('r', array(
		'do'    	=> TYPE_STR,
		'userid' 	=> TYPE_INT,
		'page'		=> TYPE_INT,
	));
	
	$currentpage=$vbulletin->GPC['page'];
	$perpage=10;
	
	if($vbulletin->GPC['userid']==0)
		$userid=$vbulletin->userinfo['userid'];
	else
		$userid=$vbulletin->GPC['userid'];
	
	//BUILD MENU LEFT
	if(($vbulletin->GPC['do']=="")||(($vbulletin->GPC['do']=="view")))
		$current_userid=$userid;
	else
		$current_userid=$vbulletin->userinfo['userid'];
		
	//----Get master
	$getmaster=$vbulletin->db->query_first("SELECT * FROM ".TABLE_PREFIX."hqth_ffs_sale WHERE usertarget=".$current_userid." AND status=1");
	if($getmaster)
	{
		$hqthffs_hasmaster=1;
		$hqthffs_master_userid=$getmaster['usersale'];
		$hqthffs_master_username=hqth_get_muusername($getmaster['usersale']);
		$hqthffs_master_masterstatus="(".$getmaster['notice'].")";
		$hqthffs_master_property=hqth_get_format_credits(hqth_get_credits($getmaster['usersale']));
		$hqthffs_master_value=hqth_get_format_credits(hqth_get_value($getmaster['usersale']));
		$hqthffs_master_avatar=hqth_get_avatar($getmaster['usersale']);
		if($userid==$vbulletin->userinfo['userid'])
		{
			$hqthffs_is_pet=1;
		}
	}
	else
	{
		$hqthffs_hasmaster=0;
	}
	
	//----Get old master
	$getoldmaster=$vbulletin->db->query_read("SELECT DISTINCT usersale FROM ".TABLE_PREFIX."hqth_ffs_sale WHERE usertarget=".$current_userid." AND status=0 ORDER BY saleid DESC LIMIT 2");
	if($vbulletin->db->num_rows($getoldmaster)==0)
	{
		$hqthffs_hasoldmaster=0;
		$hqthffs_pre_id=0;
		$hqthffs_next_id=0;
	}
	else
	{
		$hqthffs_hasoldmaster=1;
		$hqthffs_list_oldmaster="";
		while($oldmaster_bit=$vbulletin->db->fetch_array($getoldmaster))
		{
			$hqthffs_oldmasterid=$oldmaster_bit['usersale'];
			$hqthffs_oldmastername=hqth_get_username($oldmaster_bit['usersale']);
			$hqthffs_oldmasteravatar=hqth_get_avatar($oldmaster_bit['usersale']);
			eval('$hqthffs_list_oldmaster .= "' . fetch_template('hqth_ffs_oldmaster_bit') . '";');
		}
		$hqthffs_pre_id=0;
		$hqthffs_next_id=2;
	}
	eval('$hqthffs_oldmaster_table = "' . fetch_template('hqth_ffs_oldmaster_ajax') . '";');
	
	//----Get pets
	$getpet=$vbulletin->db->query_read("SELECT * FROM ".TABLE_PREFIX."hqth_ffs_sale WHERE usersale=".$current_userid." AND status=1 ORDER BY datebuy DESC LIMIT 2");
	if($vbulletin->db->num_rows($getpet)==0)
	{
		$hqthffs_haspet=0;
		$hqthffs_pre_id=0;
		$hqthffs_next_id=0;
	}
	else
	{
		$hqthffs_haspet=1;
		$hqthffs_list_oldmaster="";
		while($pet_bit=$vbulletin->db->fetch_array($getpet))
		{
			$hqthffs_pre_id=0;
			$hqthffs_next_id=2;
			$hqthffs_pet_userid=$pet_bit['usertarget'];
			$hqthffs_pet_username=hqth_get_muusername($pet_bit['usertarget']);
			$hqthffs_pet_avatar=hqth_get_avatar($pet_bit['usertarget']);
			$hqthffs_pet_notice=$pet_bit['notice'];
			$hqthffs_pet_value=hqth_get_format_credits(hqth_get_value($pet_bit['usertarget']));
			$hqthffs_pet_cash=hqth_get_format_credits(hqth_get_credits($pet_bit['usertarget']));
			$hqthffs_pet_price=hqth_get_format_credits(hqth_get_price($pet_bit['usertarget']));
			$hqthffs_pet_time=vbdate($vbulletin->options['hqthffs_formatdate'],$pet_bit['datebuy']);
			if($vbulletin->userinfo['userid']==$userid)
			{
				$hqthffs_is_master=1;
			}
			elseif($vbulletin->userinfo['userid']==$pet_bit['usertarget'])
			{
				$hqthffs_is_master=2;
			}
			elseif(hqth_check_ispet($pet_bit['usertarget']))
			{
				$hqthffs_is_master=3;
			}
			else
			{
				$hqthffs_is_master=1;
			}
			
			eval('$hqthffs_list_pets .= "' . fetch_template('hqth_ffs_mypet_bit') . '";');
		}
	}
	eval('$hqthffs_pet_table = "' . fetch_template('hqth_ffs_pet_ajax') . '";');
	
	eval('$hqthffs_menuleft = "' . fetch_template('hqth_ffs_menuleft') . '";');
	//---------------------------
	
	switch($vbulletin->GPC['do'])
	{
		case "":
		case "view":
			$hqthffs_cash=hqth_get_format_credits(hqth_get_credits($userid));
			$hqthffs_userid=$userid;
			$hqthffs_avatar=hqth_get_avatar($userid);
			$hqthffs_username=hqth_get_muusername($userid);
			$hqthffs_value=hqth_get_format_credits(hqth_get_value($userid));
			$hqthffs_pets_number=hqth_get_pet_buyed($userid);
			$hqthffs_price=hqth_get_format_credits(hqth_get_price($userid));
			$hqthffs_buyed=hqth_get_num_buyed($userid);
			
			if((hqth_get_price($userid)<=hqth_get_credits($vbulletin->userinfo['userid']))&&($userid!=$vbulletin->userinfo['userid'])&&(!hqth_check_ispet($userid)))
			{
				$hqth_can_buy=1;
			}
			if(hqth_check_ispet($userid))
			{
				$hqth_can_bullying=1;
				$hqth_can_comfort=1;
				$hqth_can_saleoff=1;
				$hqth_can_freed=1;
			}
			if(hqth_check_ismaster($userid))
			{
				$hqth_can_wheedle=1;
			}
			if(($userid==$vbulletin->userinfo['userid'])&&(hqth_check_buyed($userid)))
			{
				$hqth_can_redeem=1;
			}
			
			//Get About me
			$get_aboutme=$vbulletin->db->query_first("SELECT * FROM ".TABLE_PREFIX."userfield WHERE userid=".$userid);
			if($get_aboutme)
			{
				$hqthffs_aboutme=$get_aboutme['field1'];
			}

			//Gen log using page nav
			$get_num=$vbulletin->db->query_read("SELECT * FROM ".TABLE_PREFIX."hqth_ffs_actionlog INNER JOIN ".TABLE_PREFIX."hqth_ffs_action ON ".TABLE_PREFIX."hqth_ffs_actionlog.logtype=".TABLE_PREFIX."hqth_ffs_action.actionid WHERE userdo=".$userid." OR usertarget=".$userid);
			$numrecord=$vbulletin->db->num_rows($get_num);
			if($numrecord>$perpage)
			{
				if($currentpage==0) $currentpage=1;
				if($numrecord<$currentpage*$perpage-$perpage) $currentpage=round($numrecord/$perpage);
				$pagenav = construct_page_nav($currentpage, $perpage, $numrecord, "ffs.php?do=view".($userid!=$vbulletin->userinfo['userid']?("&userid=".$userid):""));
				$pagegen=" LIMIT ".($currentpage*$perpage-$perpage).",".$perpage;
			}
			
			//Get Log Using
			$get_log=$vbulletin->db->query_read("SELECT * FROM ".TABLE_PREFIX."hqth_ffs_actionlog INNER JOIN ".TABLE_PREFIX."hqth_ffs_action ON ".TABLE_PREFIX."hqth_ffs_actionlog.logtype=".TABLE_PREFIX."hqth_ffs_action.actionid WHERE userdo=".$userid." OR usertarget=".$userid." ORDER BY datedo DESC".$pagegen);
			$hqthffs_list_logusing="";
			while($logbit=$vbulletin->db->fetch_array($get_log))
			{
				$hqthffs_master_id=$logbit['userdo'];
				$hqthffs_master_name=hqth_get_muusername($logbit['userdo']);
				$hqthffs_pet_id=$logbit['usertarget'];
				$hqthffs_pet_name=hqth_get_muusername($logbit['usertarget']);
				$hqthffs_action=$logbit['actionname'];
				if($logbit['actioncat']=="buy")
				{
					$hqthffs_was=$vbphrase['hqthffs_buy'];
					$hqthffs_action="";
				}
				elseif($logbit['actioncat']=="redeem")
				{
					$hqthffs_was=$vbphrase['hqthffs_redeem'];
					$hqthffs_pet_id=$logbit['usertarget'];
					$hqthffs_pet_name="";
					$hqthffs_action="";
				}
				elseif($logbit['actioncat']=="saleoff")
				{
					$hqthffs_was=$vbphrase['hqthffs_saleoff'];
					$hqthffs_action="";
				}
				elseif($logbit['actioncat']=="wheedle")
				{
					$hqthffs_was=$vbphrase['hqthffs_wheedle'];
				}
				elseif($logbit['actioncat']=="freed")
				{
					$hqthffs_was=$vbphrase['hqthffs_freed'];
					$hqthffs_action="";
				}
				else
				{
					$hqthffs_was=$vbphrase['hqthffs_was'];
				}
				$hqthffs_time=vbdate($vbulletin->options['hqthffs_formatdate'],$logbit['datedo']);
				$hqthffs_master_money="";
				$hqthffs_pet_money="";
				$hqthffs_money_master=0;
				$hqthffs_money_pet=0;
				if($logbit['money_master']>0)
				{
					$hqthffs_money_master=1;
					$hqthffs_master_money=hqth_get_format_credits($logbit['money_master']);
				}
				if($logbit['money_pet']>0)
				{
					$hqthffs_money_pet=1;
					$hqthffs_pet_money=hqth_get_format_credits($logbit['money_pet']);
				}
				eval('$hqthffs_list_logusing .= "' . fetch_template('hqth_ffs_logusing_bit') . '";');
			}
			eval('$hqthffs_content = "' . fetch_template('hqth_ffs_home') . '";');
			break;
		case "comfort":
			if(hqth_check_ispet($vbulletin->userinfo['userid']))
			{
				print_no_permission();
			}
			$hqthffs_pet_userid=$userid;
			$hqthffs_pet_avatar=hqth_get_avatar($userid);
			$hqthffs_pet_username=hqth_get_muusername($userid);
			$checkcomport=$vbulletin->db->query_first("SELECT * FROM ".TABLE_PREFIX."hqth_ffs_actionlog INNER JOIN ".TABLE_PREFIX."hqth_ffs_action ON ".TABLE_PREFIX."hqth_ffs_actionlog.logtype=".TABLE_PREFIX."hqth_ffs_action.actionid WHERE dateend>=".mktime(0,0,0,date("m"),date("d"),date("Y"))." AND active=1 AND actioncat='comfort' AND userdo=".$vbulletin->userinfo['userid']." AND usertarget=".$userid);
			if($checkcomport)
			{
				$hqthffs_list_comfort = "<center>".$vbphrase['hqthffs_comforting']."</center>";
			}
			else
			{
				$vbulletin->input->clean_gpc('p', 'chkAction', TYPE_INT);
				if($vbulletin->GPC['chkAction']>0)
				{
					hqth_actionlog($userid,$vbulletin->GPC['chkAction']);
					exec_header_redirect("ffs.php?do=view&userid=".$userid);
				}
				$get_comfort=$vbulletin->db->query_read("SELECT * FROM ".TABLE_PREFIX."hqth_ffs_action WHERE actioncat='comfort' ORDER BY actionname");
				$hqthffs_list_comfort="";
				while($comfortbit=$vbulletin->db->fetch_array($get_comfort))
				{
					$hqthffs_action_id=$comfortbit['actionid'];
					$hqthffs_action_name=$comfortbit['actionname'];
					eval('$hqthffs_list_comfort .= "' . fetch_template('hqth_ffs_action_bit') . '";');
				}
			}
			eval('$hqthffs_content = "' . fetch_template('hqth_ffs_anui') . '";');
			break;
		case "bullying":
			if(!hqth_check_ispet($userid))
			{
				print_no_permission();
			}
			$hqthffs_pet_userid=$userid;
			$hqthffs_pet_avatar=hqth_get_avatar($userid);
			$hqthffs_pet_username=hqth_get_muusername($userid);
			$checkbullying=$vbulletin->db->query_first("SELECT * FROM ".TABLE_PREFIX."hqth_ffs_actionlog INNER JOIN ".TABLE_PREFIX."hqth_ffs_action ON ".TABLE_PREFIX."hqth_ffs_actionlog.logtype=".TABLE_PREFIX."hqth_ffs_action.actionid WHERE dateend>=".mktime(0,0,0,date("m"),date("d"),date("Y"))." AND active=1 AND actioncat='bullying' AND userdo=".$vbulletin->userinfo['userid']." AND usertarget=".$userid);
			if($checkbullying)
			{
				$hqthffs_list_bullying = "<center>".$vbphrase['hqthffs_doing_bullying']."</center>";
			}
			else
			{
				$vbulletin->input->clean_gpc('p', 'chkAction', TYPE_INT);
				if($vbulletin->GPC['chkAction']>0)
				{
					hqth_actionlog($userid,$vbulletin->GPC['chkAction']);
					exec_header_redirect("ffs.php?do=view&userid=".$userid);
				}
				$get_bullying=$vbulletin->db->query_read("SELECT * FROM ".TABLE_PREFIX."hqth_ffs_action WHERE actioncat='bullying' ORDER BY actionname");
				$hqthffs_list_bullying="";
				while($bullyingbit=$vbulletin->db->fetch_array($get_bullying))
				{
					$hqthffs_action_id=$bullyingbit['actionid'];
					$hqthffs_action_name=$bullyingbit['actionname'];
					eval('$hqthffs_list_bullying .= "' . fetch_template('hqth_ffs_action_bit') . '";');
				}
			}
			eval('$hqthffs_content = "' . fetch_template('hqth_ffs_batnat') . '";');
			break;
		case "redeem":
			if((!hqth_check_buyed($vbulletin->userinfo['userid']))||($vbulletin->userinfo['userid']!=$userid))
			{
				print_no_permission();
			}
			$checkoldprice=$vbulletin->db->query_first("SELECT * FROM ".TABLE_PREFIX."hqth_ffs_sale WHERE usertarget=".$userid." AND status=1");
			$vbulletin->input->clean_gpc('p', 'btnsubmit', TYPE_STR);
			if($vbulletin->GPC['btnsubmit']!="")
			{
				if(hqth_get_value($userid)>hqth_get_credits($userid))
				{
					standard_error($vbphrase['hqthffs_not_enought_money']);
				}
				else
				{
					hqth_actionlog($vbulletin->userinfo['userid'],2);
					$get_master=$vbulletin->db->query_first("SELECT * FROM ".TABLE_PREFIX."hqth_ffs_sale WHERE usertarget=".$userid." AND status=1");
					$vbulletin->db->query_write("UPDATE ".TABLE_PREFIX."hqth_ffs_sale SET status=0 WHERE usertarget=".$userid." AND status=1");
					$vbulletin->db->query_write("DELETE FROM ".TABLE_PREFIX."hqth_ffs_saleoff WHERE usertarget=".$vbulletin->userinfo['userid']);
					$vbulletin->db->query_write("UPDATE ".TABLE_PREFIX."user SET ".$vbulletin->options['hqthffs_credits']."=".$vbulletin->options['hqthffs_credits']."-".hqth_get_value($userid)." WHERE userid=".$userid);
					if($checkoldprice)
					{
						$vbulletin->db->query_write("UPDATE ".TABLE_PREFIX."user SET ".$vbulletin->options['hqthffs_credits']."=".$vbulletin->options['hqthffs_credits']."+".($vbulletin->options['hqthffs_fee_oldmaster']+hqth_get_value($userid)-$vbulletin->options['hqthffs_fee_page'])." WHERE userid=".$checkoldprice['usersale']);
						$vbulletin->db->query_write("UPDATE ".TABLE_PREFIX."hqth_ffs_actionlog SET active=0 WHERE active=1 AND usertarget=".$userid." AND userdo=".$checkoldprice['usersale']);
					}
					
					$pm =& datamanager_init('PM', $vbulletin, ERRTYPE_ARRAY); 
	
					$hqthffs_userid=$get_master['userid'];
					$hqthffs_username=hqth_get_username($get_master['userid']);
					$hqthffs_petid=$vbulletin->userinfo['userid'];
					$hqthffs_petname=$vbulletin->userinfo['username'];
					// evaluate the message
					eval('$pmcontents = "' . fetch_template('hqth_ffs_redeem_pmcontent') . '";');
					//echo $pmcontents;
					// Force the PM through
					$doit['adminpermissions'] = 2;
			
					// create the DM to do error checking and insert the new PM
					$pm->set('fromuserid', $vbulletin->options['hqthffs_sentmessage_userid']);
					$pm->set('fromusername', hqth_get_username($vbulletin->options['hqthffs_sentmessage_userid']));
					$pm->set('title', $vbphrase['hqthffs_petredeem_pm_title']);
					$pm->set('message', $pmcontents);
					$pm->set_recipients($hqthffs_username, $do);
					$pm->set('dateline', TIMENOW);
					$pm->save();
					exec_header_redirect("ffs.php?do=view&userid=".$userid);
				}
			}
			$hqthffs_pet_userid=$userid;
			$hqthffs_pet_avatar=hqth_get_avatar($userid);
			$hqthffs_pet_username=hqth_get_muusername($userid);
			$hqthffs_redeem_price=hqth_get_format_credits(hqth_get_value($userid));
			$hqthffs_assets=hqth_get_format_credits(hqth_get_credits($userid));
			$hqthffs_fee_oldmaster=hqth_get_format_credits($vbulletin->options['hqthffs_fee_oldmaster']);
			$hqthffs_fee_redeemer=hqth_get_format_credits($vbulletin->options['hqthffs_fee_redeemer']);
			$hqthffs_fee_pagers=hqth_get_format_credits($vbulletin->options['hqthffs_fee_page']);
			if($checkoldprice)
			{
				$hqthffs_old_price=hqth_get_format_credits($checkoldprice['price']);
				$hqthffs_interest_oldmaster=hqth_get_value($userid)-$checkoldprice['price']-$vbulletin->options['hqthffs_fee_oldmaster']-$vbulletin->options['hqthffs_fee_redeemer']-$vbulletin->options['hqthffs_fee_page'];
				if($hqthffs_interest_oldmaster<0)
				{
					$hqthffs_old_price=$hqthffs_old_price+$hqthffs_interest_oldmaster;
					$hqthffs_old_price=hqth_get_format_credits($hqthffs_old_price);
					$hqthffs_interest_oldmaster=0;
				}
				$hqthffs_interest_oldmaster=hqth_get_format_credits($hqthffs_interest_oldmaster);
			}
			else
			{
				$hqthffs_old_price=hqth_get_format_credits($vbulletin->options['hqthffs_default_money']);
				$hqthffs_interest_oldmaster=hqth_get_format_credits(hqth_get_value($userid)-$vbulletin->options['hqthffs_default_money']-$vbulletin->options['hqthffs_fee_oldmaster']-$vbulletin->options['hqthffs_fee_redeemer']-$vbulletin->options['hqthffs_fee_page']);
			}
			eval('$hqthffs_content = "' . fetch_template('hqth_ffs_chuocthan') . '";');
			break;
		case "saleoff":
			if(!hqth_check_ispet($userid))
			{
				print_no_permission();
			}
			$hqthffs_pet_userid=$userid;
			$hqthffs_pet_avatar=hqth_get_avatar($userid);
			$hqthffs_pet_username=hqth_get_muusername($userid);
			$vbulletin->input->clean_array_gpc('p', array(
													'ddl_percent'   => TYPE_NUM,
													'btnsubmit' 	=> TYPE_STR,
												));
			if(($vbulletin->GPC['ddl_percent']>=0)&&($vbulletin->GPC['btnsubmit']!=""))
			{
				hqth_actionlog($userid,3);
				$vbulletin->db->query_write("INSERT INTO ".TABLE_PREFIX."hqth_ffs_saleoff(usersale,usertarget,price) VALUES(".$vbulletin->userinfo['userid'].",".$userid.",".$vbulletin->GPC['ddl_percent'].")");
				exec_header_redirect("ffs.php?do=view&userid=".$userid);
			}
			eval('$hqthffs_content = "' . fetch_template('hqth_ffs_giamgia') . '";');
			break;
		case "wheedle":
			if(!hqth_check_ismaster($userid))
			{
				print_no_permission();
			}
			$hqthffs_pet_userid=$userid;
			$hqthffs_pet_avatar=hqth_get_avatar($userid);
			$hqthffs_pet_username=hqth_get_muusername($userid);
			$checkwheedle=$vbulletin->db->query_first("SELECT * FROM ".TABLE_PREFIX."hqth_ffs_actionlog INNER JOIN ".TABLE_PREFIX."hqth_ffs_action ON ".TABLE_PREFIX."hqth_ffs_actionlog.logtype=".TABLE_PREFIX."hqth_ffs_action.actionid WHERE dateend>=".mktime(0,0,0,date("m"),date("d"),date("Y"))." AND active=1 AND actioncat='wheedle' AND userdo=".$vbulletin->userinfo['userid']." AND usertarget=".$userid);
			if($checkwheedle)
			{
				$hqthffs_list_wheedle = "<center>".$vbphrase['hqthffs_wheedling']."</center>";
			}
			else
			{
				$vbulletin->input->clean_gpc('p', 'chkAction', TYPE_INT);
				if($vbulletin->GPC['chkAction']>0)
				{
					hqth_actionlog($userid,$vbulletin->GPC['chkAction']);
					exec_header_redirect("ffs.php?do=view&userid=".$userid);
				}
				$get_wheedle=$vbulletin->db->query_read("SELECT * FROM ".TABLE_PREFIX."hqth_ffs_action WHERE actioncat='wheedle' ORDER BY actionname");
				$hqthffs_list_wheedle="";
				while($wheedlebit=$vbulletin->db->fetch_array($get_wheedle))
				{
					$hqthffs_action_id=$wheedlebit['actionid'];
					$hqthffs_action_name=$wheedlebit['actionname'];
					eval('$hqthffs_list_wheedle .= "' . fetch_template('hqth_ffs_action_bit') . '";');
				}
			}
			eval('$hqthffs_content = "' . fetch_template('hqth_ffs_ninhchu') . '";');
			break;
		case "supermarket":
			$hqthffs_cash=hqth_get_format_credits(hqth_get_credits($userid));
			if($vbulletin->options['hqthffs_using_sexual'])
				$hqthffs_option_sexual="";
			else
				$hqthffs_option_sexual='disabled="disabled"';
			
			$vbulletin->input->clean_array_gpc('g', array(
				'chkSexual'    	=> TYPE_NOHTML,
				'chkStatus' 	=> TYPE_NOHTML,
				'chkPrice'		=> TYPE_NOHTML,
				'hqthffs_frienduser'	=> TYPE_NOHTML,
			));
			
			$chk_sexual=trim($vbulletin->GPC['chkSexual']);
			$chk_status=trim($vbulletin->GPC['chkStatus']);
			$chk_price=trim($vbulletin->GPC['chkPrice']);
			$search_user=trim($vbulletin->GPC['hqthffs_frienduser']);
			
			if($chk_sexual=="male")
			{
				$hqthffs_sexual_male_checked='checked';
			}
			if($chk_sexual=="female")
			{
				$hqthffs_sexual_female_checked='checked';
			}
			if($chk_sexual=="bold")
			{
				$hqthffs_sexual_bold_checked='checked';
			}
			if($chk_status=="nonmaster")
			{
				$hqthffs_status_nonmaster_checked='checked';
			}
			if($chk_status=="mastered")
			{
				$hqthffs_status_mastered_checked='checked';
			}
			if($chk_status=="all")
			{
				$hqthffs_status_all_checked='checked';
			}
			if($chk_price=="saleoff")
			{
				$hqthffs_price_saleoff_checked='checked';
			}
			if($chk_price=="canbuy")
			{
				$hqthffs_price_canbuy_checked='checked';
			}
			if($chk_price=="all")
			{
				$hqthffs_price_all_checked='checked';
			}
			
			$sql="SELECT * FROM ".TABLE_PREFIX."user";
			$where=" WHERE (1=1)";
			if(($search_user!="")&&($search_user!=$vbphrase['hqthffs_input_friend_name']))
			{
				$where.=" AND username='".$vbulletin->db->escape_string($search_user)."'";
			}
			else
			{

				if($vbulletin->option['hqthffs_using_sexual'])
				{
					if($chk_sexual=="male")
					{
						$where.=" AND (userid IN (SELECT userid FROM ".TABLE_PREFIX."userfield WHERE ".$vbulletin->options['hqthffs_field_sexual']."='".$vbulletin->options['hqthffs_field_sexual_boy']."'))";
					}
					if($chk_sexual=="female")
					{
						$where.=" AND (userid IN (SELECT userid FROM ".TABLE_PREFIX."userfield WHERE ".$vbulletin->options['hqthffs_field_sexual']."='".$vbulletin->options['hqthffs_field_sexual_girl']."'))";
					}
				}
				
				if($chk_status!="")
				{
					if($chk_status=="nonmaster")
					{
						$where.=" AND (userid NOT IN (SELECT usertarget FROM ".TABLE_PREFIX."hqth_ffs_sale WHERE status=1))";
					}
					if($chk_status=="mastered")
					{
						$where.=" AND (userid IN (SELECT usertarget FROM ".TABLE_PREFIX."hqth_ffs_sale WHERE status=1))";
					}
				}
				if($chk_price!="")
				{
					if($chk_price=="saleoff")
					{
						$where.=" AND (userid IN (SELECT usertarget FROM ".TABLE_PREFIX."hqth_ffs_saleoff))";
					}
					if($chk_price=="canbuy")
					{
						//$where.=" AND (userid IN (SELECT userid FROM ".TABLE_PREFIX."hqth_ffs_saleoff))";
					}
				}
			}
			
			//Gen page
			$get_user=$vbulletin->db->query_read($sql.$where);
			$numuser=$vbulletin->db->num_rows($get_user);
			if($numuser>$perpage)
			{
				if($currentpage==0) $currentpage=1;
				if($numuser<$currentpage*$perpage-$perpage) $currentpage=round($numuser/$perpage);
				$pagenav = construct_page_nav($currentpage, $perpage, $numuser, "ffs.php?".$_SERVER['QUERY_STRING']);
				$pagegen=" LIMIT ".($currentpage*$perpage-$perpage).",".$perpage;
			}

			$get_user=$vbulletin->db->query_read($sql.$where.$pagegen);
			$hqthffs_list_pet="";
			$i=0;
			while($userbit=$vbulletin->db->fetch_array($get_user))
			{
				$addpet=1;
				if($chk_price=="canbuy")
				{
					if(hqth_get_credits($userid)<hqth_get_value($userbit['userid']))
					{
						$addpet=0;
					}
				}
				if($addpet)
				{
					$i++;
					if($i==1)
					{
						$hqthffs_list_pet.="<tr>";
					}
					if($i==2)
					{
						$hqthffs_list_pet.='<td><img src="clear.gif" width="10"></td>';
					}
					if($i==3)
					{
						$hqthffs_list_pet.="</tr><tr>";
						$i=1;
					}
					if($vbulletin->options['hqthffs_require_friend'])
						$hqthffs_fromwho=$vbphrase['hqthffs_from_friend'];
					else
						$hqthffs_fromwho=$vbphrase['hqthffs_from_all'];
					$checkoldprice=$vbulletin->db->query_first("SELECT * FROM ".TABLE_PREFIX."hqth_ffs_sale WHERE usertarget=".$userbit['userid']." AND status=1");
					if(hqth_check_buyed($userbit['userid']))
					{
						if($checkoldprice)
							$hqthffs_pet_status=$vbphrase['hqthffs_old_price']." ".hqth_get_format_credits($checkoldprice['price']);
						else
							$hqthffs_pet_status="";
					}
					else
						$hqthffs_pet_status=$vbphrase['hqthffs_nonmaster'];
					$hqthffs_pet_userid=$userbit['userid'];
					$hqthffs_pet_avatar=hqth_get_avatar($userbit['userid']);
					$hqthffs_pet_username=hqth_get_muusername($userbit['userid']);
					$hqthffs_pet_cash=hqth_get_format_credits(hqth_get_credits($userbit['userid']));
					$hqthffs_pet_pets_number=hqth_get_pet_buyed($userbit['userid']);
					$hqthffs_pet_buyed=hqth_get_num_buyed($userbit['userid']);
					if($checkoldprice)
						$hqthffs_pet_master='<a href="ffs.php?do=view&userid='.$checkoldprice['usersale'].'">'.hqth_get_muusername($checkoldprice['usersale']).'</a>';
					else
						$hqthffs_pet_master=$vbphrase['hqthffs_nonmaster'];
					$hqthffs_pet_value=hqth_get_format_credits(hqth_get_price($userbit['userid']));
					if((!hqth_check_ispet($userbit['userid']))&&($vbulletin->userinfo['userid']!=$userbit['userid']))
						$hqthffs_can_buy=1;
					else
						$hqthffs_can_buy=0;
					eval('$hqthffs_list_pet .= "' . fetch_template('hqth_ffs_pet_bit') . '";');
				}
			}
			$hqthffs_list_pet.="</tr>";
			
			eval('$hqthffs_content = "' . fetch_template('hqth_ffs_sieuthi') . '";');
			break;
		case "freed":
			if(!hqth_check_ispet($userid))
			{
				print_no_permission();
			}
			$vbulletin->input->clean_gpc('p', 'btnsubmit', TYPE_STR);
			if($vbulletin->GPC['btnsubmit']!="")
			{
					hqth_actionlog($userid,4);
					$vbulletin->db->query_write("UPDATE ".TABLE_PREFIX."hqth_ffs_sale SET status=0 WHERE usertarget=".$userid." AND usersale=".$vbulletin->userinfo['userid']." AND status=1");
					$vbulletin->db->query_write("DELETE FROM ".TABLE_PREFIX."hqth_ffs_saleoff WHERE usertarget=".$userid." AND usersale=".$vbulletin->userinfo['userid']);
					$vbulletin->db->query_write("UPDATE ".TABLE_PREFIX."hqth_ffs_actionlog SET active=0 WHERE active=1 AND usertarget=".$userid." AND userdo=".$vbulletin->userinfo['userid']);
					$vbulletin->db->query_write("UPDATE ".TABLE_PREFIX."user SET ".$vbulletin->options['hqthffs_credits']."=".$vbulletin->options['hqthffs_credits']."+".$vbulletin->options['hqthffs_money_when_freed']." WHERE userid=".$vbulletin->userinfo['userid']);
					exec_header_redirect("ffs.php?do=view&userid=".$userid);
			}
			$hqthffs_pet_userid=$userid;
			$hqthffs_pet_avatar=hqth_get_avatar($userid);
			$hqthffs_pet_username=hqth_get_muusername($userid);
			$hqthffs_reward=hqth_get_format_credits($vbulletin->options['hqthffs_money_when_freed']);
			eval('$hqthffs_content = "' . fetch_template('hqth_ffs_thatudo') . '";');
			break;
		case "rank":
			$vbulletin->input->clean_gpc('p', 'rank_filter', TYPE_STR);
			$rank_filter=$vbulletin->GPC['rank_filter'];
			if($rank_filter=="")
				$rank_filter="topmoney";
			switch($rank_filter)
			{
				case "topmoney":
					$sql="SELECT * FROM ".TABLE_PREFIX."user ORDER BY ".$vbulletin->options['hqthffs_credits']." DESC LIMIT 10";
					$get_user=$vbulletin->db->query_read($sql);
					$k=0;
					while($getuserbit=$vbulletin->db->fetch_array($get_user))
					{
						$userbit[$k]=$getuserbit['userid'];
						$k++;
					}
					break;
				case "topprice":
					$sql="SELECT * FROM ".TABLE_PREFIX."user";
					$get_user=$vbulletin->db->query_read($sql);
					$k=0;
					while($getuserbit=$vbulletin->db->fetch_array($get_user))
					{
						$temp_userbit[$k][0]=$getuserbit['userid'];
						$temp_userbit[$k][1]=hqth_get_value($getuserbit['userid']);
						$k++;
					}
					
					for($i=0;$i<sizeof($temp_userbit)-1;$i++)
					{
						for($j=$i+1;$j<sizeof($temp_userbit);$j++)
						{
							if($temp_userbit[$i][1]<$temp_userbit[$j][1])
							{
								$temp=$temp_userbit[$i];
								$temp_userbit[$i]=$temp_userbit[$j];
								$temp_userbit[$j]=$temp;
							}
						}
					}
					if(sizeof($temp_userbit)<10)
					{
						$sizeof_temp=sizeof($temp_userbit);
					}
					else
					{
						$sizeof_temp=10;
					}
					for($i=0;$i<$sizeof_temp;$i++)
					{
						$userbit[$i]=$temp_userbit[$i][0];
					}
					break;
				case "topbuy":
					$sql="SELECT userid,COUNT(usertarget) AS number FROM ".TABLE_PREFIX."user INNER JOIN ".TABLE_PREFIX."hqth_ffs_sale ON ".TABLE_PREFIX."user.userid=".TABLE_PREFIX."hqth_ffs_sale.usersale GROUP BY userid ORDER BY number DESC LIMIT 10";
					$get_user=$vbulletin->db->query_read($sql);
					$k=0;
					while($getuserbit=$vbulletin->db->fetch_array($get_user))
					{
						$userbit[$k]=$getuserbit['userid'];
						$k++;
					}
					break;
				case "topbuyed":
					$sql="SELECT userid,COUNT(usersale) AS number FROM ".TABLE_PREFIX."user INNER JOIN ".TABLE_PREFIX."hqth_ffs_sale ON ".TABLE_PREFIX."user.userid=".TABLE_PREFIX."hqth_ffs_sale.usertarget GROUP BY userid ORDER BY number DESC LIMIT 10";
					$get_user=$vbulletin->db->query_read($sql);
					$k=0;
					while($getuserbit=$vbulletin->db->fetch_array($get_user))
					{
						$userbit[$k]=$getuserbit['userid'];
						$k++;
					}
					break;
				case "toppet":
					$sql="SELECT userid,COUNT(usertarget) AS number FROM ".TABLE_PREFIX."user INNER JOIN ".TABLE_PREFIX."hqth_ffs_sale ON ".TABLE_PREFIX."user.userid=".TABLE_PREFIX."hqth_ffs_sale.usersale WHERE status=1 GROUP BY userid ORDER BY number DESC LIMIT 10";
					$get_user=$vbulletin->db->query_read($sql);
					$k=0;
					while($getuserbit=$vbulletin->db->fetch_array($get_user))
					{
						$userbit[$k]=$getuserbit['userid'];
						$k++;
					}
					break;
			}
			
			$hqthffs_list_mem_rank="";
			$i=0;
			$j=1;
			for($k=0;$k<sizeof($userbit);$k++)
			{
				$hqthffs_cur_rank='<img src="kun/no'.$j.'.jpg">';
				$i++;
				if($i==1)
				{
					$hqthffs_list_mem_rank.="<tr>";
				}
				if($i==2)
				{
					$hqthffs_list_mem_rank.='<td><img src="clear.gif" width="10"></td>';
				}
				if($i==3)
				{
					$hqthffs_list_mem_rank.="</tr><tr>";
					$i=1;
				}
				if($vbulletin->options['hqthffs_require_friend'])
					$hqthffs_fromwho=$vbphrase['hqthffs_from_friend'];
				else
					$hqthffs_fromwho=$vbphrase['hqthffs_from_all'];
				$checkoldprice=$vbulletin->db->query_first("SELECT * FROM ".TABLE_PREFIX."hqth_ffs_sale WHERE usertarget=".$userbit[$k]." AND status=1");
				if(hqth_check_buyed($userbit[$k]))
				{
					if($checkoldprice)
						$hqthffs_pet_status=$vbphrase['hqthffs_old_price']." ".hqth_get_format_credits($checkoldprice['price']);
					else
						$hqthffs_pet_status="";
				}
				else
					$hqthffs_pet_status=$vbphrase['hqthffs_nonmaster'];
				$hqthffs_pet_userid=$userbit[$k];
				$hqthffs_pet_avatar=hqth_get_avatar($userbit[$k]);
				$hqthffs_pet_username=hqth_get_muusername($userbit[$k]);
				$hqthffs_pet_cash=hqth_get_format_credits(hqth_get_credits($userbit[$k]));
				$hqthffs_pet_pets_number=hqth_get_pet_buyed($userbit[$k]);
				$hqthffs_pet_buyed=hqth_get_num_buyed($userbit[$k]);
				if($checkoldprice)
					$hqthffs_pet_master='<a href="ffs.php?do=view&userid='.$checkoldprice['usersale'].'">'.hqth_get_muusername($checkoldprice['usersale']).'</a>';
				else
					$hqthffs_pet_master=$vbphrase['hqthffs_nonmaster'];
				$hqthffs_pet_value=hqth_get_format_credits(hqth_get_price($userbit[$k]));
				if((!hqth_check_ispet($userbit[$k]))&&($vbulletin->userinfo['userid']!=$userbit[$k]))
					$hqthffs_can_buy=1;
				else
					$hqthffs_can_buy=0;
				eval('$hqthffs_list_mem_rank .= "' . fetch_template('hqth_ffs_pet_bit') . '";');
				$j++;
			}
			$hqthffs_list_mem_rank.="</tr>";
			eval('$hqthffs_content = "' . fetch_template('hqth_ffs_rank') . '";');
			break;
		case "guild":
			exec_header_redirect($vbulletin->options['hqthffs_guild_topic']);
			break;
		case "buy":
			if($vbulletin->userinfo['userid']==$userid)
			{
				print_no_permission();
			}
			if((!hqth_check_buddy($userid))&&($vbulletin->options['hqthffs_require_friend']))
			{
				standard_error($vbphrase['hqthffs_friend_need_to_buy']);
			}
			if(hqth_check_ispet($userid))
			{
				print_no_permission();
			}
			$checkoldprice=$vbulletin->db->query_first("SELECT * FROM ".TABLE_PREFIX."hqth_ffs_sale WHERE usertarget=".$userid." AND status=1");
			$vbulletin->input->clean_array_gpc('p', array(
										'btnsubmit'    	=> TYPE_STR,
										'txt_notice' 	=> TYPE_NOHTML,
										));
			if($vbulletin->GPC['btnsubmit']!="")
			{
				if(hqth_get_price($userid)>hqth_get_credits($vbulletin->userinfo['userid']))
				{
					standard_error($vbphrase['hqthffs_not_enought_money']);
				}
				else
				{
					hqth_actionlog($userid,1);
					$vbulletin->db->query_write("DELETE FROM ".TABLE_PREFIX."hqth_ffs_saleoff WHERE usertarget=".$userid);
					$vbulletin->db->query_write("UPDATE ".TABLE_PREFIX."user SET ".$vbulletin->options['hqthffs_credits']."=".$vbulletin->options['hqthffs_credits']."-".hqth_get_price($userid)." WHERE userid=".$vbulletin->userinfo['userid']);
					$vbulletin->db->query_write("UPDATE ".TABLE_PREFIX."user SET ".$vbulletin->options['hqthffs_credits']."=".$vbulletin->options['hqthffs_credits']."+".$vbulletin->options['hqthffs_money_when_buyed']." WHERE userid=".$userid);
					if($checkoldprice)
					{
						$vbulletin->db->query_write("UPDATE ".TABLE_PREFIX."hqth_ffs_sale SET status=0 WHERE usertarget=".$checkoldprice['usertarget']." AND status=1");
						$vbulletin->db->query_write("UPDATE ".TABLE_PREFIX."user SET ".$vbulletin->options['hqthffs_credits']."=".$vbulletin->options['hqthffs_credits']."+".(hqth_get_value($userid)+$vbulletin->options['hqthffs_fee_oldmaster'])." WHERE userid=".$checkoldprice['usersale']);
						$vbulletin->db->query_write("UPDATE ".TABLE_PREFIX."hqth_ffs_actionlog SET active=0 WHERE active=1 AND usertarget=".$userid." AND userdo=".$checkoldprice['usersale']);
					}
					$vbulletin->db->query_write("INSERT INTO ".TABLE_PREFIX."hqth_ffs_sale(usersale,usertarget,notice,datebuy,status,price) VALUES(".$vbulletin->userinfo['userid'].",".$userid.",'".$vbulletin->GPC['txt_notice']."',".time().",1,".hqth_get_value($userid).")");
					$pm =& datamanager_init('PM', $vbulletin, ERRTYPE_ARRAY); 
	
					$hqthffs_userid=$userid;
					$hqthffs_username=hqth_get_username($userid);
					$hqthffs_masterid=$vbulletin->userinfo['userid'];
					$hqthffs_mastername=$vbulletin->userinfo['username'];
					// evaluate the message
					eval('$pmcontents = "' . fetch_template('hqth_ffs_buyed_pmcontent') . '";');
					
					// Force the PM through
					$doit['adminpermissions'] = 2;
			
					// create the DM to do error checking and insert the new PM
					$pm->set('fromuserid', $vbulletin->options['hqthffs_sentmessage_userid']);
					$pm->set('fromusername', hqth_get_username($vbulletin->options['hqthffs_sentmessage_userid']));
					$pm->set('title', $vbphrase['hqthffs_buypet_pm_title']);
					$pm->set('message', $pmcontents);
					$pm->set_recipients($hqthffs_username, $do);
					$pm->set('dateline', TIMENOW);
					$pm->save();
					exec_header_redirect("ffs.php?do=view&userid=".$userid);
				}
			}
			$hqthffs_pet_userid=$userid;
			$hqthffs_pet_avatar=hqth_get_avatar($userid);
			$hqthffs_pet_username=hqth_get_muusername($userid);
			$hqthffs_buy_price=hqth_get_format_credits(hqth_get_value($userid));
			$hqthffs_assets=hqth_get_format_credits(hqth_get_credits($vbulletin->userinfo['userid']));
			$hqthffs_fee_pet=hqth_get_format_credits($vbulletin->options['hqthffs_money_when_buyed']);
			$hqthffs_fee_pagers=hqth_get_format_credits($vbulletin->options['hqthffs_fee_page']);
			$hqthffs_old_price=hqth_get_format_credits(hqth_get_value($userid)-$vbulletin->options['hqthffs_money_when_buyed']-$vbulletin->options['hqthffs_fee_page']);
			eval('$hqthffs_content = "' . fetch_template('hqth_ffs_muathu') . '";');
			break;

	}
	$navbits = array(); 
	$navbits[$parent] = $vbphrase['hqthffs'];
	$navbits = construct_navbits($navbits); 
	eval('$navbar = "' . fetch_template('navbar') . '";');
	eval('$hqthffs_menutop = "' . fetch_template('hqth_ffs_menutop') . '";');
	eval('print_output("' . fetch_template('hqth_ffs_main') . '");');
}
else
{
	standard_error($vbulletin->options['hqthffs_message']);
}
/*======================================================================*\
|| ####################################################################
|| # CVS: $RCSfile: ffs.php,v $ - $Revision: 1.0.0 $
|| ####################################################################
\*======================================================================*/
?>