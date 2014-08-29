<?php
/*========================================================================*\
|| ###################################################################### ||
|| # 	     	  [HQTH] Friend For Sale v 1.0		     				# ||
|| #              for vBulletin Version 3.8.x                        	# ||
|| #              http://hoiquantinhoc.com                          	# ||
|| #              Coded by tieuquynhi - Designed by Mr.Kun              # ||
|| ###################################################################### ||
\*========================================================================*/

//Get username in HTML
function hqth_get_muusername($userid)
{
	global $vbulletin;
	$userinfo = fetch_userinfo($userid);
	return fetch_musername($userinfo);
}

//Get username
function hqth_get_username($userid)
{
	global $vbulletin;
	$userinfo = fetch_userinfo($userid);
	return $userinfo['username'];
}

//Get user credits
function hqth_get_credits($userid)
{
	global $vbulletin;
	$userinfo = fetch_userinfo($userid);
	return $userinfo[$vbulletin->options['hqthffs_credits']];
}

//Get credit formated
function hqth_get_format_credits($credits)
{
	global $vbulletin;
	return number_format($credits)." ".$vbulletin->options['hqthffs_credits_name'];
}

//Get num buyed
function hqth_get_num_buyed($userid)
{
	global $vbulletin;
	$numbuyed = $vbulletin->db->query_first("SELECT COUNT(saleid) AS numbuyed FROM ".TABLE_PREFIX."hqth_ffs_sale WHERE usertarget=".$userid);
	return $numbuyed['numbuyed'];
}

//Get num pet buyed
function hqth_get_pet_buyed($userid)
{
	global $vbulletin;
	$numbuyed = $vbulletin->db->query_first("SELECT COUNT(saleid) AS numbuyed FROM ".TABLE_PREFIX."hqth_ffs_sale WHERE usersale=".$userid." AND status=1");
	return $numbuyed['numbuyed'];
}

//Get value of pet
function hqth_get_value($userid)
{
	global $vbulletin;
	$userinfo = fetch_userinfo($userid);
	$user_post_number=$userinfo['posts'];
	$user_num_buyed=hqth_get_num_buyed($userid);
	$user_oldmaster_fee=$vbulletin->options['hqthffs_fee_oldmaster'];
	$ffs_default_money=$vbulletin->options['hqthffs_default_money'];
	$user_page_fee=$vbulletin->options['hqthffs_fee_page'];
	$user_redeemer_fee=$vbulletin->options['hqthffs_fee_redeemer'];
	eval('$userprice = ' . $vbulletin->options['hqthffs_pet_cast'] . ';');
	return $userprice;
}

//Get avatar url
function hqth_get_avatar($userid)
{
	global $vbulletin;
	require_once(DIR . '/includes/functions_user.php');
	$avatar=fetch_avatar_url($userid);
	if($avatar=='') 
	{
		$milano = $vbulletin->db->query_first("SELECT yahoo FROM ".TABLE_PREFIX."user WHERE userid=".$userid);
		if($milano['yahoo']!='')
		{
			$link = 'http://img.msg.yahoo.com/avatar.php?yids='.$milano['yahoo'];
		}
		else
		{
			$link = 'images/avatars/noavatar.gif';
		}	
	}
	else 
		$link = $avatar[0];
	return $link;
}

//Get real price of pet
function hqth_get_price($userid)
{
	global $vbulletin;
	$checksaleoff=$vbulletin->db->query_first("SELECT * FROM ".TABLE_PREFIX."hqth_ffs_saleoff WHERE usertarget=".$userid);
	if($checksaleoff)
	{
		return hqth_get_value($userid)*$checksaleoff['price'];
	}
	else
	{
		return hqth_get_value($userid);
	}
}

//Check is pet?
function hqth_check_ispet($userid)
{
	global $vbulletin;
	$getpet=$vbulletin->db->query_first("SELECT * FROM ".TABLE_PREFIX."hqth_ffs_sale WHERE usersale=".$vbulletin->userinfo['userid']." AND usertarget=".$userid." AND status=1");
	if($getpet)
	{
		return true;
	}
	else
	{
		return false;
	}
}

//Check is master?
function hqth_check_ismaster($userid)
{
	global $vbulletin;
	$getmaster=$vbulletin->db->query_first("SELECT * FROM ".TABLE_PREFIX."hqth_ffs_sale WHERE usertarget=".$vbulletin->userinfo['userid']." AND usersale=".$userid." AND status=1");
	if($getmaster)
	{
		return true;
	}
	else
	{
		return false;
	}
}

//Check is buyed?
function hqth_check_buyed($userid)
{
	global $vbulletin;
	$getbuyed=$vbulletin->db->query_first("SELECT * FROM ".TABLE_PREFIX."hqth_ffs_sale WHERE usertarget=".$userid." AND status=1");
	if($getbuyed)
	{
		return true;
	}
	else
	{
		return false;
	}
}

//Add day in current date
function hqth_dateadd($date,$interval) 
{
	$curdate = getdate($date);
	$cday = $curdate['mday']+$interval;
	$cmonth = $curdate['mon'];
	$cyear = $curdate['year'];
	
	if ($cday > 30)
	{
		$cmonth = $cmonth + 1;
		$cday = $cday - 30;		
		if ($cmonth == 13)
		{
			$cyear = $cyear + 1;
			$cmonth = 1;
		}
	}
	$ourDate = mktime($curdate['hours'],$curdate['minutes'],$curdate['seconds'],$cmonth,$cday,$cyear);
	return $ourDate;
}

//Save action log
function hqth_actionlog($usertarget,$actionid)
{
	global $vbulletin;
	$getaction=$vbulletin->db->query_first("SELECT * FROM ".TABLE_PREFIX."hqth_ffs_action WHERE actionid=".$actionid);
	if($getaction)
	{
		$daynow=time();
		$dayend=hqth_dateadd($daynow,$getaction['actiondelay']);
		$vbulletin->db->query_write("INSERT INTO ".TABLE_PREFIX."hqth_ffs_actionlog(userdo,usertarget,logtype,datedo,dateend,active) VALUES(".$vbulletin->userinfo['userid'].",".$usertarget.",".$actionid.",'".$daynow."','".$dayend."',1)");
		$vbulletin->db->query_write("UPDATE ".TABLE_PREFIX."user SET ".$vbulletin->options['hqthffs_credits']."=".$vbulletin->options['hqthffs_credits']."+".$getaction['money_master']." WHERE userid=".$vbulletin->userinfo['userid']);
		$vbulletin->db->query_write("UPDATE ".TABLE_PREFIX."user SET ".$vbulletin->options['hqthffs_credits']."=".$vbulletin->options['hqthffs_credits']."+".$getaction['money_pet']." WHERE userid=".$usertarget);
	}
}

//Check buddy
function hqth_check_buddy($userid)
{
	global $vbulletin;
	$checkbuddy=$vbulletin->db->query_first("SELECT * FROM ".TABLE_PREFIX."userlist WHERE ((userid=".$vbulletin->userinfo['userid']." AND relationid=".$userid.") OR (relationid=".$vbulletin->userinfo['userid']." AND userid=".$userid.")) AND type='buddy' AND friend='yes'");
	if($checkbuddy)
		return true;
	else
		return false;
}
/*======================================================================*\
|| ####################################################################
|| # CVS: $RCSfile: ffs.php,v $ - $Revision: 1.0.0 $
|| ####################################################################
\*======================================================================*/
?>