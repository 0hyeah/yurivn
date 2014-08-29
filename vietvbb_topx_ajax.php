<?php
if ($_REQUEST['do'] == 'vietvbb_stats' AND $vbulletin->options['vietvbbtopstats_enable_global'])	{
	// Check forum permission
	$vietvbbstatsforumperms = array();
	foreach($vbulletin->forumcache AS $vietvbbtsforum)	{
		$vietvbbstatsforumperms[$vietvbbtsforum["forumid"]] = fetch_permissions($vietvbbtsforum['forumid']);
		if (
			!($vietvbbstatsforumperms[$vietvbbtsforum["forumid"]] & $vbulletin->bf_ugp_forumpermissions['canview'])
			OR (
				!($vietvbbtsforum['options'] & $vbulletin->bf_misc_forumoptions['active']) 
				AND !$vbulletin->options['showprivateforums'] 
				AND !in_array($vbulletin->userinfo['usergroupid'], array(5,6,7))
				)
			)	{
			$vietvbbexclfids .= ','.$vietvbbtsforum['forumid'];
		}
	}
	// Excl
	if ($vbulletin->options['vietvbbtopstats_excl_forums'])	{
		$vietvbbexclfids .= ','.$vbulletin->options['vietvbbtopstats_excl_forums'];
	}
	$vietvbbexclfids = substr($vietvbbexclfids, 1);
	if ($vietvbbexclfids)	{
		$vietvbbexclforums_lastposts = "AND thread.forumid NOT IN($vietvbbexclfids)";
		$vietvbbexclforums_topforums = "AND thread.forumid NOT IN($vietvbbexclfids)";
		$vietvbbexclforums = "AND forumid NOT IN($vietvbbexclfids)";
	}
	if ($vbulletin->options['vietvbbtopstats_excl_groups_listing'])	{
		$vietvbbexclgroups = "AND user.usergroupid NOT IN (".$vbulletin->options['vietvbbtopstats_excl_groups_listing'].")";
	}
	//trim
	$trimthreadtitle = $vbulletin->options['vietvbbtopstats_trim_threadtitles'];
	$trimusername = $vbulletin->options['vietvbbtopstats_trim_usernames'];
	$trimforumtitles = $vbulletin->options['vietvbbtopstats_trim_forumtitles'];
	$vietvbb_afs_bpar = array("<strong>","</strong>","<b>","</b>", "font-weight: bold", "font-weight:bold");	
	// Number result
	switch ($vbulletin->options['vietvbbtopstats_result'])	{		
		case 10: 
			$resultsnr = 10;
			$resultthread = 10;
			break;
		case 15:
			$resultsnr = 15;
			$resultthread = 15;
			break;
		case 20:
			$resultsnr = ($_REQUEST['type']=='tab')? 20 : 9;
			$resultthread = ($_REQUEST['type']=='tab')? 20 : 9;
			break;
	}
	//Custom latest post
	$custom_latest_post = array(
		'vietvbb_latest_posts_custom1',
		'vietvbb_latest_posts_custom2',
		'vietvbb_latest_posts_custom3',
		'vietvbb_latest_posts_custom4',
		'vietvbb_latest_posts_custom5'
		);
	switch ($_REQUEST['top'])	{		
		case 'vietvbb_latest_posts_custom1':
			$custom_forumid = $vbulletin->options['vietvbbtopstats_latest_posts_custom1_forumid'];
			break;
		case 'vietvbb_latest_posts_custom2':
			$custom_forumid = $vbulletin->options['vietvbbtopstats_latest_posts_custom2_forumid'];
			break;
		case 'vietvbb_latest_posts_custom3':
			$custom_forumid = $vbulletin->options['vietvbbtopstats_latest_posts_custom3_forumid'];
			break;
		case 'vietvbb_latest_posts_custom4':
			$custom_forumid = $vbulletin->options['vietvbbtopstats_latest_posts_custom4_forumid'];
			break;
		case 'vietvbb_latest_posts_custom5':
			$custom_forumid = $vbulletin->options['vietvbbtopstats_latest_posts_custom5_forumid'];
			break;
	}
################################################################################
	
	// Latest new
	if ($_REQUEST['top'] =='vietvbb_latest_news' AND $vbulletin->options['vietvbbtopstats_latestnews_newsforumid'])	{
		$get_stats_latestnews = $vbulletin->db->query_read("
			SELECT threadid, title, forumid, views, replycount, dateline, lastpost, visible, open, postusername, lastposter			  
			FROM " . TABLE_PREFIX . "thread AS thread
			WHERE NOT ISNULL(threadid) AND forumid IN (".$vbulletin->options['vietvbbtopstats_latestnews_newsforumid'].") AND visible = '1' AND open!='10'
			ORDER BY dateline DESC
			LIMIT 0, $resultthread
			");
		while ($get_latest_news = $db->fetch_array($get_stats_latestnews))	{
			$get_latest_news[fullthreadtitle] = strip_tags($get_latest_news[title]);
			if ($trimthreadtitle > 0)	{
				$get_latest_news[titletrimmed] = fetch_trimmed_title($get_latest_news[fullthreadtitle], $trimthreadtitle);
			}
			else	{
				$get_latest_news[titletrimmed] = $get_latest_news[fullthreadtitle];
			}
			if ($get_latest_news[lastpost] > $vbulletin->userinfo['lastvisit'])	{
				$get_latest_news[newpost] = true;
			}
			$get_latest_news_forumtitle = strip_tags($vbulletin->forumcache["$get_latest_news[forumid]"]['title_clean']);
			$get_latest_news[fullforumtitle] = strip_tags($get_latest_news_forumtitle);		
			$get_new_postdate = vbdate($vbulletin->options['vietvbbtopstats_date_format'], $get_latest_news[lastpost]);
			$get_new_posttime = vbdate($vbulletin->options['vietvbbtopstats_time_format'], $get_latest_news[lastpost]);
			$get_startdate = vbdate($vbulletin->options['vietvbbtopstats_date_format'], $get_latest_news[dateline]);
			$get_starttime = vbdate($vbulletin->options['vietvbbtopstats_time_format'], $get_latest_news[dateline]);
			eval('$vietvbbtopstats_top .= "' . $vbulletin->templatecache['vietvbb_topstats_latestnews'] . '";');
		}
		if (!$vietvbbtopstats_top)	$vietvbbtopstats_top = 'No Result';
		print_output($vietvbbtopstats_top);
		exit;
	}
	// Newest Member
	if ($_REQUEST['top'] == 'vietvbb_newest_members')	{
		$get_stats_newmem = $vbulletin->db->query_read("
			SELECT userid, usergroupid, displaygroupid, username, joindate, posts
			FROM " . TABLE_PREFIX . "user AS user
			WHERE userid > '0' $vietvbbexclgroups
			ORDER BY joindate DESC
			LIMIT 0, $resultsnr
			");
		while ($get_new_mem = $db->fetch_array($get_stats_newmem))	{
			$get_new_mem[fullusername] = strip_tags($get_new_mem[username]);
			if ($trimusername > 0)	{
				$get_new_mem[username] = fetch_trimmed_title($get_new_mem[fullusername], $trimusername);
			}
			else	{
				$get_new_mem[username] = $get_new_mem[fullusername];
			}
			$get_new_mem[musername] = fetch_musername($get_new_mem);
			if ($vbulletin->options['vietvbbtopstats_bold_remove'])	{
				$get_new_mem[musername] = str_replace($vietvbb_afs_bpar, "", $get_new_mem[musername]);
			}
			if ($get_new_mem[joindate] > $vbulletin->userinfo['lastvisit'])	{
				$get_new_mem[newuser] = true;
			}
			$vietvbbmemberjoined = vbdate($vbulletin->options['vietvbbtopstats_date_format'], $get_new_mem['joindate']);
			eval('$vietvbbtopstats_top .= "' . $vbulletin->templatecache['vietvbb_topstats_member'] . '";');
		}
		if (!$vietvbbtopstats_top)	$vietvbbtopstats_top = 'No Result';
		print_output($vietvbbtopstats_top);
		exit;
	}
	// Top Poster
	if ($_REQUEST['top'] == 'vietvbb_top_posters')	{
		$get_stats_posters = $vbulletin->db->query_read("
			SELECT userid, usergroupid, displaygroupid, username, posts
			FROM " . TABLE_PREFIX . "user AS user
			WHERE posts > '0' $vietvbbexclgroups
			ORDER BY posts DESC
			LIMIT 0, $resultsnr
			");
		while ($getstats_poster = $db->fetch_array($get_stats_posters))	{
			$getstats_poster[fullusername] = strip_tags($getstats_poster[username]);
			if ($trimusername > 0)	{
				$getstats_poster[username] = fetch_trimmed_title($getstats_poster[fullusername], $trimusername);
			}
			else	{
				$getstats_poster[username] = $getstats_poster[fullusername];
			}
			$getstats_poster[musername] = fetch_musername($getstats_poster);
			if ($vbulletin->options['vietvbbtopstats_bold_remove'])	{
				$getstats_poster[musername] = str_replace($vietvbb_afs_bpar, "", $getstats_poster[musername]);
			}
			eval('$vietvbbtopstats_top .= "' . $vbulletin->templatecache['vietvbb_topstats_poster'] . '";');
		}
		if (!$vietvbbtopstats_top)	$vietvbbtopstats_top = 'No Result';
		print_output($vietvbbtopstats_top);
		exit;
	}
	// Top Starter
	if ($_REQUEST['top'] == 'vietvbb_top_starters')	{
		$get_stats_starters = $vbulletin->db->query_read("
			SELECT COUNT(thread.threadid) AS threads, thread.postuserid, thread.dateline, user.userid, user.usergroupid, user.displaygroupid, user.username
			FROM " . TABLE_PREFIX . "thread AS thread
			LEFT JOIN " . TABLE_PREFIX . "user AS user ON (thread.postuserid = user.userid)
			LEFT JOIN " . TABLE_PREFIX . "forum AS forum ON (forum.forumid = thread.forumid)
			WHERE thread.visible='1' AND (forum.options & 4096) AND user.userid > '0' $vietvbbexclgroups
			GROUP BY thread.postuserid
			ORDER BY threads DESC
			LIMIT 0, $resultsnr
			");
		while ($getstats_starter = $db->fetch_array($get_stats_starters))	{
			$getstats_starter[fullusername] = strip_tags($getstats_starter[username]);
			if ($trimusername > 0)	{
				$getstats_starter[username] = fetch_trimmed_title($getstats_starter[fullusername], $trimusername);
			}
			else	{
				$getstats_starter[username] = $getstats_starter[fullusername];
			}
			$getstats_starter[musername] = fetch_musername($getstats_starter);
			if ($vbulletin->options['vietvbbtopstats_bold_remove'])	{
				$getstats_starter[musername] = str_replace($vietvbb_afs_bpar, "", $getstats_starter[musername]);
			}
			eval('$vietvbbtopstats_top .= "' . $vbulletin->templatecache['vietvbb_topstats_starter'] . '";');
		}
		if (!$vietvbbtopstats_top)	$vietvbbtopstats_top = 'No Result';
		print_output($vietvbbtopstats_top);
		exit;
	}
	// Top Referrers
	if ($_REQUEST['top'] == 'vietvbb_top_referrers')	{
		$get_stats_referrers = $vbulletin->db->query_read("
			SELECT COUNT(*) AS refnumber, user.username, user.userid, user.usergroupid, user.displaygroupid, user.referrerid, refs.joindate
			FROM " . TABLE_PREFIX . "user AS refs
			LEFT JOIN " . TABLE_PREFIX . "user AS user ON (refs.referrerid = user.userid)
			WHERE refs.referrerid > '0' AND user.userid > '0' $vietvbbexclgroups
			GROUP BY refs.referrerid
			ORDER BY refnumber DESC
			LIMIT 0, $resultsnr
			");
		while ($getstats_referrer = $db->fetch_array($get_stats_referrers))	{
			$getstats_referrer[fullusername] = strip_tags($getstats_referrer[username]);
			if ($trimusername > 0)	{
				$getstats_referrer[username] = fetch_trimmed_title($getstats_referrer[fullusername], $trimusername);
			}
			else	{
				$getstats_referrer[username] = $getstats_referrer[fullusername];
			}
			$getstats_referrer[musername] = fetch_musername($getstats_referrer);
			if ($vbulletin->options['vietvbbtopstats_bold_remove'])	{
				$getstats_referrer[musername] = str_replace($vietvbb_afs_bpar, "", $getstats_referrer[musername]);
			}
			eval('$vietvbbtopstats_top .= "' . $vbulletin->templatecache['vietvbb_topstats_referrer'] . '";');
		}
		if (!$vietvbbtopstats_top)	$vietvbbtopstats_top = 'No Result';
		print_output($vietvbbtopstats_top);
		exit;
	}
	// Most view thread
	if ($_REQUEST['top'] == 'vietvbb_most_viewed')	{
		$get_stats_mostviewed = $vbulletin->db->query_read("
			SELECT threadid,
				title,
				forumid,
				views,
				dateline,
				visible,
				open,
				lastpost,
				replycount,
				postusername,
				lastposter				
			FROM " . TABLE_PREFIX . "thread AS thread
			WHERE NOT ISNULL(threadid) AND visible = '1' AND views > '0' AND open!='10' $vietvbbexclforums $vietvbbtopstats_timecut_mostviewed
			ORDER BY views DESC
			LIMIT 0, $resultthread
			");		
		while ($get_most_viewed = $db->fetch_array($get_stats_mostviewed))	{
			$get_most_viewed[fullthreadtitle] = strip_tags($get_most_viewed[title]);
			if ($trimthreadtitle > 0)	{
				$get_most_viewed[titletrimmed] = fetch_trimmed_title($get_most_viewed[fullthreadtitle], $trimthreadtitle);
			}
			else	{
				$get_most_viewed[titletrimmed] = $get_most_viewed[fullthreadtitle];
			}
			if ($get_most_viewed[lastpost] > $vbulletin->userinfo['lastvisit'])	{
				$get_most_viewed[newpost] = true;
			}
			$get_most_viewed_forumtitle = strip_tags($vbulletin->forumcache["$get_most_viewed[forumid]"]['title_clean']);
			$get_most_viewed[fullforumtitle] = strip_tags($get_most_viewed_forumtitle);		
			$get_new_postdate = vbdate($vbulletin->options['vietvbbtopstats_date_format'], $get_most_viewed[lastpost]);
			$get_new_posttime = vbdate($vbulletin->options['vietvbbtopstats_time_format'], $get_most_viewed[lastpost]);
			$get_startdate = vbdate($vbulletin->options['vietvbbtopstats_date_format'], $get_most_viewed[dateline]);
			$get_starttime = vbdate($vbulletin->options['vietvbbtopstats_time_format'], $get_most_viewed[dateline]);	
			eval('$vietvbbtopstats_top.= "' . $vbulletin->templatecache['vietvbb_topstats_mostviewed'] . '";');
		}
		if (!$vietvbbtopstats_top)	$vietvbbtopstats_top = 'No Result';
		print_output($vietvbbtopstats_top);
		exit;
	}
	// Hostest Thread
	if ($_REQUEST['top'] == 'vietvbb_hottest_threads')	{
		$get_stats_hottest = $vbulletin->db->query_read("
			SELECT threadid, title, lastpost, forumid, views, replycount, dateline, visible, open, postusername, lastposter
			FROM " . TABLE_PREFIX . "thread AS thread
			WHERE NOT ISNULL(threadid) AND visible = '1' AND replycount > '0' AND open!='10' $vietvbbexclforums
			ORDER BY replycount DESC
			LIMIT 0, $resultthread
			");
		while ($get_hottest_threads = $db->fetch_array($get_stats_hottest))	{
			$get_hottest_threads[fullthreadtitle] = strip_tags($get_hottest_threads[title]);
			if ($trimthreadtitle > 0)	{
				$get_hottest_threads[titletrimmed] = fetch_trimmed_title($get_hottest_threads[fullthreadtitle], $trimthreadtitle);
			}
			else	{
				$get_hottest_threads[titletrimmed] = $get_hottest_threads[fullthreadtitle];
			}
			if ($get_hottest_threads[lastpost] > $vbulletin->userinfo['lastvisit'])	{
				$get_hottest_threads[newpost] = true;
			}
			$get_hottest_threads_forumtitle = strip_tags($vbulletin->forumcache["$get_hottest_threads[forumid]"]['title_clean']);
			$get_hottest_threads[fullforumtitle] = strip_tags($get_hottest_threads_forumtitle);		
			$get_new_postdate = vbdate($vbulletin->options['vietvbbtopstats_date_format'], $get_hottest_threads[lastpost]);
			$get_new_posttime = vbdate($vbulletin->options['vietvbbtopstats_time_format'], $get_hottest_threads[lastpost]);
			$get_startdate = vbdate($vbulletin->options['vietvbbtopstats_date_format'], $get_hottest_threads[dateline]);
			$get_starttime = vbdate($vbulletin->options['vietvbbtopstats_time_format'], $get_hottest_threads[dateline]);	
			eval('$vietvbbtopstats_top .= "' . $vbulletin->templatecache['vietvbb_topstats_hottest'] . '";');
		}
		if (!$vietvbbtopstats_top)	$vietvbbtopstats_top = 'No Result';
		print_output($vietvbbtopstats_top);
		exit;
	}
	// Top Reputation
	if ($_REQUEST['top'] == 'vietvbb_top_reputation')	{
		$get_stats_reputation = $vbulletin->db->query_read("
			SELECT userid, usergroupid, displaygroupid, username, posts, reputation
			FROM " . TABLE_PREFIX . "user AS user
			WHERE reputation > '0' $vietvbbexclgroups
			ORDER BY reputation DESC
			LIMIT 0, $resultsnr
			");
		while ($getstats_rep = $db->fetch_array($get_stats_reputation))	{
			$getstats_rep[fullusername] = strip_tags($getstats_rep[username]);
			if ($trimusername > 0)	{
				$getstats_rep[username] = fetch_trimmed_title($getstats_rep[fullusername], $trimusername);
			}
			else	{
				$getstats_rep[username] = $getstats_rep[fullusername];
			}
			$getstats_rep[musername] = fetch_musername($getstats_rep);
			if ($vbulletin->options['vietvbbtopstats_bold_remove'])	{
				$getstats_rep[musername] = str_replace($vietvbb_afs_bpar, "", $getstats_rep[musername]);
			}
			eval('$vietvbbtopstats_top .= "' . $vbulletin->templatecache['vietvbb_topstats_reputation'] . '";');
		}
		if (!$vietvbbtopstats_top)	$vietvbbtopstats_top = 'No Result';
		print_output($vietvbbtopstats_top);
		exit;
	}
	// Top Thanked
	if ($_REQUEST['top'] == 'vietvbb_thanked_members' AND $vbulletin->options['post_thanks_on_off'])	{
		$get_stats_thanks = $vbulletin->db->query_read("
			SELECT userid, usergroupid, displaygroupid, username, posts, post_thanks_thanked_times
			FROM " . TABLE_PREFIX . "user AS user
			WHERE post_thanks_thanked_times > '0' $vietvbbexclgroups
			ORDER BY post_thanks_thanked_times DESC
			LIMIT 0, $resultsnr
			");
		while ($getstats_thx = $db->fetch_array($get_stats_thanks))	{
			$getstats_thx[fullusername] = strip_tags($getstats_thx[username]);
			if ($trimusername > 0)	{
				$getstats_thx[username] = fetch_trimmed_title($getstats_thx[fullusername], $trimusername);
			}
			else	{
				$getstats_thx[username] = $getstats_thx[fullusername];
			}
			$getstats_thx[musername] = fetch_musername($getstats_thx);
			if ($vbulletin->options['vietvbbtopstats_bold_remove'])	{
				$getstats_thx[musername] = str_replace($vietvbb_afs_bpar, "", $getstats_thx[musername]);
			}
			eval('$vietvbbtopstats_top .= "' . $vbulletin->templatecache['vietvbb_topstats_thanks'] . '";');
		}
		if (!$vietvbbtopstats_top)	$vietvbbtopstats_top = 'No Result';
		print_output($vietvbbtopstats_top);
		exit;
	}
	// Top Forums
	if ($_REQUEST['top'] == 'vietvbb_top_forums')	{
		$get_stats_topforums = $vbulletin->db->query_read("
			SELECT forumid, title_clean, replycount
			FROM " . TABLE_PREFIX . "forum AS forum
			WHERE replycount > '0' $vietvbbexclforums
			ORDER BY replycount DESC
			LIMIT 0, $resultthread
			");
		while ($get_topforums = $db->fetch_array($get_stats_topforums))	{
			$get_topforums[fullforumtitle] = strip_tags($get_topforums[title_clean]);
			if ($trimforumtitles > 0)	{
				$get_topforums[titletrimmed] = fetch_trimmed_title($get_topforums[fullforumtitle], $trimforumtitles);
			}
			else	{
				$get_topforums[titletrimmed] = $get_topforums[fullforumtitle];
			}
			eval('$vietvbbtopstats_top .= "' . $vbulletin->templatecache['vietvbb_topstats_topforums'] . '";');
		}
		if (!$vietvbbtopstats_top)	$vietvbbtopstats_top = 'No Result';
		print_output($vietvbbtopstats_top);
		exit;
	}
	// Top Infractions
	if ($_REQUEST['top'] == 'vietvbb_top_infractions')	{
		$get_stats_infractions = $vbulletin->db->query_read("
			SELECT COUNT(infraction.infractionid) AS infs, SUM(infraction.points) AS infpoints, infraction.userid, user.usergroupid, user.displaygroupid, user.username
			FROM " . TABLE_PREFIX . "infraction AS infraction
			LEFT JOIN " . TABLE_PREFIX . "user AS user ON (infraction.userid = user.userid)
			WHERE infraction.userid > '0' AND infraction.points > '0' $vietvbbexclgroups
			GROUP BY infraction.userid
			ORDER BY infpoints DESC
			LIMIT 0, $resultsnr
			");
		while ($getstats_infraction = $db->fetch_array($get_stats_infractions))	{
			$getstats_infraction[fullusername] = strip_tags($getstats_infraction[username]);
			if ($trimusername > 0)	{
				$getstats_infraction[username] = fetch_trimmed_title($getstats_infraction[fullusername], $trimusername);
			}
			else	{
				$getstats_infraction[username] = $getstats_infraction[fullusername];
			}
			$getstats_infraction[musername] = fetch_musername($getstats_infraction);
			if ($vbulletin->options['vietvbbtopstats_bold_remove'])	{
				$getstats_infraction[musername] = str_replace($vietvbb_afs_bpar, "", $getstats_infraction[musername]);
			}
			eval('$vietvbbtopstats_top .= "' . $vbulletin->templatecache['vietvbb_topstats_infractions'] . '";');
		}
		if (!$vietvbbtopstats_top)	$vietvbbtopstats_top = 'No Result';
		print_output($vietvbbtopstats_top);
		exit;
	}
	// Class ads
	if ($_REQUEST['top'] == 'vietvbb_latest_classads' AND $vbulletin->options['vietvbbtopstats_classads_forums'])	{
		$get_stats_classads = $vbulletin->db->query_read("
			SELECT threadid, title, lastpost, forumid, dateline, visible, open, views, replycount, postusername, lastposter			  
			FROM " . TABLE_PREFIX . "thread AS thread
			WHERE NOT ISNULL(threadid) AND forumid IN (".$vbulletin->options['vietvbbtopstats_classads_forums'].") AND visible = '1' AND open!='10'
			ORDER BY dateline DESC
			LIMIT 0, $resultthread
			");
		while ($get_latest_classads = $db->fetch_array($get_stats_classads))	{
			$get_latest_classads[fullthreadtitle] = strip_tags($get_latest_classads[title]);
			if ($trimthreadtitle > 0)	{
				$get_latest_classads[titletrimmed] = fetch_trimmed_title($get_latest_classads[fullthreadtitle], $trimthreadtitle);
			}
			else	{
				$get_latest_classads[titletrimmed] = $get_latest_classads[fullthreadtitle];
			}
			$get_latest_classads_forumtitle = strip_tags($vbulletin->forumcache["$get_latest_classads[forumid]"]['title_clean']);
			$get_latest_classads[fullforumtitle] = strip_tags($get_latest_classads_forumtitle);
			if ($trimforumtitles > 0)	{
				$get_latest_classads[forumtitle] = fetch_trimmed_title($get_latest_classads[fullforumtitle], $trimforumtitles);
			}
			else	{
				$get_latest_classads[forumtitle] = $get_latest_classads[fullforumtitle];
			}
			if ($get_latest_classads[lastpost] > $vbulletin->userinfo['lastvisit'])	{
				$get_latest_classads[newpost] = true;
			}
			$get_latest_classads_forumtitle = strip_tags($vbulletin->forumcache["$get_latest_classads[forumid]"]['title_clean']);
			$get_latest_classads[fullforumtitle] = strip_tags($get_latest_classads_forumtitle);		
			$get_new_postdate = vbdate($vbulletin->options['vietvbbtopstats_date_format'], $get_latest_classads[lastpost]);
			$get_new_posttime = vbdate($vbulletin->options['vietvbbtopstats_time_format'], $get_latest_classads[lastpost]);
			$get_startdate = vbdate($vbulletin->options['vietvbbtopstats_date_format'], $get_latest_classads[dateline]);
			$get_starttime = vbdate($vbulletin->options['vietvbbtopstats_time_format'], $get_latest_classads[dateline]);
			eval('$vietvbbtopstats_top.= "' . $vbulletin->templatecache['vietvbb_topstats_classads'] . '";');
		}
		if (!$vietvbbtopstats_top)	$vietvbbtopstats_top = 'No Result';
		print_output($vietvbbtopstats_top);
		exit;
	}
	// Newest Blog
	if ($_REQUEST['top'] == 'vietvbb_latest_blogs')	{
		$get_stats_blogs = $vbulletin->db->query_read("
			SELECT blog.blogid, blog.title, blog.userid, user.username, user.usergroupid, blog.dateline, blog.views
			FROM " . TABLE_PREFIX . "blog AS blog
			LEFT JOIN " . TABLE_PREFIX . "user AS user ON (blog.userid = user.userid)
			WHERE state = 'visible'
			ORDER BY dateline DESC
			LIMIT 0, $resultthread
			");
		while ($get_latest_blogs = $db->fetch_array($get_stats_blogs))	{
			$get_latest_blogs[fullblogtitle] = strip_tags($get_latest_blogs[title]);
			if ($trimthreadtitle > 0)	{
				$get_latest_blogs[titletrimmed] = fetch_trimmed_title($get_latest_blogs[fullblogtitle], $trimthreadtitle);
			}
			else	{
				$get_latest_blogs[titletrimmed] = $get_latest_blogs[fullblogtitle];
			}
			$get_latest_blogs[fullusername] = strip_tags($get_latest_blogs[username]);
			if ($trimusername > 0)	{
				$get_latest_blogs[username] = fetch_trimmed_title($get_latest_blogs[fullusername], $trimusername);
			}
			else	{
				$get_latest_blogs[username] = $get_latest_blogs[fullusername];
			}
			$get_latest_blogs[musername] = fetch_musername($get_latest_blogs);
			if ($vbulletin->options['vietvbbtopstats_bold_remove'])	{
				$get_latest_blogs[musername] = str_replace($vietvbb_afs_bpar, "", $get_latest_blogs[musername]);
			}
			if ($get_latest_blogs[dateline] > $vbulletin->userinfo['lastvisit'])	{
				$get_latest_blogs[newpost] = true;
			}
			eval('$vietvbbtopstats_top .= "' . $vbulletin->templatecache['vietvbb_topstats_blogs'] . '";');
		}
		if (!$vietvbbtopstats_top)	$vietvbbtopstats_top = 'No Result';
		print_output($vietvbbtopstats_top);
		exit;
	}
	// Top credit
	if ($_REQUEST['top'] == 'vietvbb_top_credits')	{
		$moneyfield = $vbulletin->options['vietvbbtopstats_money_field'];
		if ($vbulletin->options['vietvbbtopstats_excl_groups_listing'])	{
			$exclgroup = $vbulletin->options['vietvbbtopstats_excl_groups_listing'];
		}
		if ($vbulletin->options['vietvbbtopstats_excl_richer_groups_listing'])	{
			$exclgroup .= ','. $vbulletin->options['vietvbbtopstats_excl_richer_groups_listing'];
		}
		if ($exclgroup)	{
			$giau_tien = "AND usergroupid NOT IN($exclgroup)";
		}
		$get_stats_credits = $vbulletin->db->query_read("
			SELECT userid, usergroupid, displaygroupid, username, $moneyfield
			FROM " . TABLE_PREFIX . "user AS user
			WHERE $moneyfield > 0 $giau_tien
			ORDER BY $moneyfield DESC
			LIMIT 0, $resultsnr
			");
		while ($getstats_credit = $db->fetch_array($get_stats_credits))	{
			$getstats_credit[fullusername] = strip_tags($getstats_credit[username]);
			if ($trimusername > 0)	{
				$getstats_credit[username] = fetch_trimmed_title($getstats_credit[fullusername], $trimusername);
			}
			else	{
				$getstats_credit[username] = $getstats_credit[fullusername];
			}
			$getstats_credit[musername] = fetch_musername($getstats_credit);
			$getstats_credit[credits] = round($getstats_credit[$moneyfield],0);
			if ($vbulletin->options['vietvbbtopstats_bold_remove'])	{
				$getstats_credit[musername] = str_replace($vietvbb_afs_bpar, "", $getstats_credit[musername]);
			}
			eval('$vietvbbtopstats_top .= "' . $vbulletin->templatecache['vietvbb_topstats_credits'] . '";');
		}
		if (!$vietvbbtopstats_top)	$vietvbbtopstats_top = 'No Result';
		print_output($vietvbbtopstats_top);
		exit;
	}
	
	// Latest Post
	if ($_REQUEST['top'] == 'vietvbb_latest_posts')	{
		$get_stats_newposts = $vbulletin->db->query_read("
			SELECT thread.threadid, thread.title, thread.lastpost, thread.forumid, thread.replycount, thread.postusername,thread.lastposter, thread.dateline, thread.views, thread.visible, thread.open, user.username, user.userid, user.usergroupid, user.displaygroupid
			FROM " . TABLE_PREFIX . "thread AS thread
			LEFT JOIN " . TABLE_PREFIX . "user AS user ON (user.username = thread.lastposter)
			WHERE NOT ISNULL(thread.threadid) AND thread.visible = '1' AND thread.open!='10' $vietvbbexclforums_lastposts
			ORDER BY lastpost DESC
			LIMIT 0, $resultthread
			");
		while ($get_new_posts = $db->fetch_array($get_stats_newposts))	{
			$get_new_posts[fullthreadtitle] = strip_tags($get_new_posts[title]);
			if ($trimthreadtitle > 0)	{
				$get_new_posts[titletrimmed] = fetch_trimmed_title($get_new_posts[fullthreadtitle], $trimthreadtitle);
			}
			else	{
				$get_new_posts[titletrimmed] = $get_new_posts[fullthreadtitle];
			}
			if ($get_new_posts[lastpost] > $vbulletin->userinfo['lastvisit'])	{
				$get_new_posts[newpost] = true;
			}
			$get_new_posts[fullusername] = strip_tags($get_new_posts[username]);
			if ($trimusername > 0)	{
				$get_new_posts[username] = fetch_trimmed_title($get_new_posts[fullusername], $trimusername);
			}
			else	{
				$get_new_posts[username] = $get_new_posts[fullusername];
			}
			$get_new_posts[musername] = fetch_musername($get_new_posts);
			$get_new_posts_forumtitle = strip_tags($vbulletin->forumcache["$get_new_posts[forumid]"]['title_clean']);
			$get_new_posts[fullforumtitle] = strip_tags($get_new_posts_forumtitle);		
			if ($vbulletin->options['vietvbbtopstats_bold_remove'])	{
				$get_new_posts[musername] = str_replace($vietvbb_afs_bpar, "", $get_new_posts[musername]); 
			}
			$get_new_postdate = vbdate($vbulletin->options['vietvbbtopstats_date_format'], $get_new_posts[lastpost]);
			$get_new_posttime = vbdate($vbulletin->options['vietvbbtopstats_time_format'], $get_new_posts[lastpost]);
			$get_new_startdate = vbdate($vbulletin->options['vietvbbtopstats_date_format'], $get_new_posts[dateline]);
			$get_new_starttime = vbdate($vbulletin->options['vietvbbtopstats_time_format'], $get_new_posts[dateline]);
			eval('$vietvbbtopstats_top .= "' . $vbulletin->templatecache['vietvbb_topstats_latest_posts'] . '";');
		}
		if (!$vietvbbtopstats_top)	$vietvbbtopstats_top = 'No Result';
		print_output($vietvbbtopstats_top);
		exit;
	}
	
	//Custom Latest Post
	if (in_array($_REQUEST['top'], $custom_latest_post) AND $custom_forumid)	{
		$get_stats_newposts = $vbulletin->db->query_read("
			SELECT thread.threadid, thread.title, thread.lastpost, thread.forumid, thread.replycount, thread.postusername,thread.lastposter, thread.dateline, thread.views, thread.visible, thread.open, user.username, user.userid, user.usergroupid, user.displaygroupid
			FROM " . TABLE_PREFIX . "thread AS thread
			LEFT JOIN " . TABLE_PREFIX . "user AS user ON (user.username = thread.lastposter)
			WHERE 
				NOT ISNULL(thread.threadid) 
				AND thread.visible = '1' 
				AND thread.open!='10'
				AND thread.forumid IN ($custom_forumid)
				$vietvbbexclforums_lastposts
			ORDER BY lastpost DESC
			LIMIT 0, $resultthread
			");
		while ($get_new_posts = $db->fetch_array($get_stats_newposts))	{
			$get_new_posts[fullthreadtitle] = strip_tags($get_new_posts[title]);
			if ($trimthreadtitle > 0)	{
				$get_new_posts[titletrimmed] = fetch_trimmed_title($get_new_posts[fullthreadtitle], $trimthreadtitle);
			}
			else	{
				$get_new_posts[titletrimmed] = $get_new_posts[fullthreadtitle];
			}
			if ($get_new_posts[lastpost] > $vbulletin->userinfo['lastvisit'])	{
				$get_new_posts[newpost] = true;
			}
			$get_new_posts[fullusername] = strip_tags($get_new_posts[username]);
			if ($trimusername > 0)	{
				$get_new_posts[username] = fetch_trimmed_title($get_new_posts[fullusername], $trimusername);
			}
			else	{
				$get_new_posts[username] = $get_new_posts[fullusername];
			}
			$get_new_posts[musername] = fetch_musername($get_new_posts);
			$get_new_posts_forumtitle = strip_tags($vbulletin->forumcache["$get_new_posts[forumid]"]['title_clean']);
			$get_new_posts[fullforumtitle] = strip_tags($get_new_posts_forumtitle);		
			if ($vbulletin->options['vietvbbtopstats_bold_remove'])	{
				$get_new_posts[musername] = str_replace($vietvbb_afs_bpar, "", $get_new_posts[musername]); 
			}
			$get_new_postdate = vbdate($vbulletin->options['vietvbbtopstats_date_format'], $get_new_posts[lastpost]);
			$get_new_posttime = vbdate($vbulletin->options['vietvbbtopstats_time_format'], $get_new_posts[lastpost]);
			$get_new_startdate = vbdate($vbulletin->options['vietvbbtopstats_date_format'], $get_new_posts[dateline]);
			$get_new_starttime = vbdate($vbulletin->options['vietvbbtopstats_time_format'], $get_new_posts[dateline]);
			eval('$vietvbbtopstats_top .= "' . $vbulletin->templatecache['vietvbb_topstats_latest_posts'] . '";');
		}
		if (!$vietvbbtopstats_top)	$vietvbbtopstats_top = 'No Result';
		print_output($vietvbbtopstats_top);
		exit;
	}
}
?>