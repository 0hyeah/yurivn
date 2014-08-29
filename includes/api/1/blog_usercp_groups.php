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
		'content' => array(
			'blogbits' => array(
				'*' => array(
					'blog' => array(
						'bloguserid', 'title', 'username', 'jointime',
						'joindate'
					),
					'show' => array('username', 'pending')
				)
			),
			'memberlist' => array(
				'*' => array(
					'member' => array(
						'userid', 'avatarurl', 'username'
					),
					'show' => array('pending')
				)
			), 'showavatarchecked'
		)
	)
);

/*======================================================================*\
|| ####################################################################
|| # Downloaded: 03:13, Sat Sep 7th 2013
|| # CVS: $RCSfile$ - $Revision: 35584 $
|| ####################################################################
\*======================================================================*/