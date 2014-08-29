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

define('VB_API_LOADLANG', true);

loadCommonWhiteList();

$VB_API_WHITELIST = array(
	'response' => array(
		'attachmentoption' => $VB_API_WHITELIST_COMMON['attachmentoption'],
		'disablesmiliesoption', 'emailchecked', 'folderbits',
		'explicitchecked', 'podcastauthor', 'podcastkeywords',
		'podcastsize', 'podcastsubtitle', 'podcasturl', 'posthash',
		'posticons',
		'postinfo' => array(
			'postid', 'username', 'userid', 'postdate', 'posttime'
		),
		'postpreview', 'poststarttime', 'prefix_options',
		'selectedicon',
		'threadinfo' => $VB_API_WHITELIST_COMMON['threadinfo'],
		'title', 'htmloption',
		'messagearea' => array(
			'newpost'
		)
	),
	'show' => array(
		'subscriptionfolders', 'empty_prefix_option', 'posticons', 'deletepostoption',
		'openclose', 'stickunstick', 'closethread', 'unstickthread', 'physicaldeleteoption',
		'keepattachmentsoption', 'firstpostnote', 'parseurl', 'misc_options', 'smiliebox', 'attach'
	)
);

/*======================================================================*\
|| ####################################################################
|| # Downloaded: 03:13, Sat Sep 7th 2013
|| # CVS: $RCSfile$ - $Revision: 35584 $
|| ####################################################################
\*======================================================================*/