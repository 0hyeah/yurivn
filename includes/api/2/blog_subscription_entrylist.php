<?php
/*======================================================================*\
|| #################################################################### ||
|| # vBulletin 4.2.2 Alpha 1 - Licence Number VBFSA2W3VC
|| # ---------------------------------------------------------------- # ||
|| # Copyright �2000-2013 vBulletin Solutions Inc. All Rights Reserved. ||
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
					'post' => array(
						'blogid', 'title', 'blogtitle', 'postedby_username',
						'ratingnum', 'ratingavg', 'lastposttime',
						'lastcommenter_encoded', 'lastcommenter', 'lastblogtextid',
						'notification', 'userid'
					),
					'show' => array(
						'datetime', 'rating', 'private'
					)
				)
			),
			'sub_count',
			'pagenav' => $VB_API_WHITELIST_COMMON['pagenav']
		)
	)
);

function api_result_prerender_2($t, &$r)
{
	switch ($t)
	{
		case 'blog_cp_manage_subscriptions_entry':
			$r['post']['lastposttime'] = $r['post']['lastcomment'];
			break;
	}
}

vB_APICallback::instance()->add('result_prerender', 'api_result_prerender_2', 2);

/*======================================================================*\
|| ####################################################################
|| # Downloaded: 03:13, Sat Sep 7th 2013
|| # CVS: $RCSfile$ - $Revision: 35584 $
|| ####################################################################
\*======================================================================*/