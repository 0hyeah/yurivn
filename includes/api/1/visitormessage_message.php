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
if (!VB_API) die;

$VB_API_WHITELIST = array(
	'response' => array(
		'human_verify',
		'messagearea' => array(
			'newpost'
		),
		'messagebits',
		'messageinfo', 'posthash', 'postpreview',
		'userinfo' => array('userid', 'username')
	),
	'vboptions' => array(
		'postminchars', 'titlemaxchars', 'maxposts'
	),
	'show' => array(
		'edit', 'parseurl', 'misc_options', 'additional_options', 'physicaldeleteoption',
		'smiliebox', 'delete'
	)
);

/*======================================================================*\
|| ####################################################################
|| # Downloaded: 03:13, Sat Sep 7th 2013
|| # CVS: $RCSfile$ - $Revision: 35584 $
|| ####################################################################
\*======================================================================*/