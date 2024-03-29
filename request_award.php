<?php
/*======================================================================*\
|| #################################################################### ||
|| # Yet Another Award System v2.1.4 � by HacNho                      # ||
|| # Copyright (C) 2005-2007 by HacNho, All rights reserved.          # ||
|| # ---------------------------------------------------------------- # ||
|| # For use with vBulletin Version 3.6.x                             # ||
|| # http://www.vbulletin.com | http://www.vbulletin.com/license.html # ||
|| # Discussion and support available at                              # ||
|| # http://www.vbulletin.org/forum/showthread.php?t=94836            # ||
|| # ---------------------------------------------------------------- # ||
|| # CVS: $RCSfile: request_award.php,v 2.1.4 - Revision: 070324      # ||
|| #################################################################### ||
\*======================================================================*/

// ####################### SET PHP ENVIRONMENT ###########################
error_reporting(E_ALL & ~E_NOTICE);

$globaltemplates = array(
	'awards_request_form',
	'awards_request_formanswers'
);

$phrasegroups = array('award');

// ######################### REQUIRE BACK-END ############################
require_once('./global.php');

if (!($permissions['awardpermissions'] & $vbulletin->bf_ugp_awardpermissions['canrequestaward']))
{
	print_no_permission();
}

// #######################################################################
// ######################## START MAIN SCRIPT ############################
// #######################################################################

// start navbar
$navbits = array(
	"awards.php?$session[sessionurl]" => $vbphrase[award_formtitle]
);

$navbits = construct_navbits($navbits);
eval('$navbar = "' . fetch_template('navbar') . '";');

if (empty($_REQUEST['do']))
{
	$_REQUEST['do'] = 'request';
}


if ($_REQUEST['do']=='submit') 
{
	require_once(DIR . '/includes/functions_newpost.php');
	$vbulletin->input->clean_array_gpc('p', array(
			'award_id' => TYPE_UINT,
			'award_request_name' => TYPE_STR,
			'award_request_recipient_name' => TYPE_STR,
			'award_request_reason' => TYPE_STR,
	));

	if ($vbulletin->GPC['award_request_name'] == '' OR $vbulletin->GPC['award_request_recipient_name'] == '' OR $vbulletin->GPC['award_request_reason'] == '')
	{
		$errormessage = $vbphrase['award_request_field_missing'];
		eval('print_output("' . fetch_template('STANDARD_ERROR') . '");');
		exit();
	}

	if (!empty($vbulletin->GPC['award_id']))
	{
	$award = $db->query_first("SELECT * FROM " . TABLE_PREFIX . "award WHERE award_id = ". $vbulletin->GPC['award_id'] ."");
	}

	$award_request_name = $vbulletin->GPC['award_request_name'];
	$award_request_recipient_name = $vbulletin->GPC['award_request_recipient_name'];
	$award_request_reason = $vbulletin->GPC['award_request_reason'];
	eval('$formsend = "' . fetch_template('awards_request_formanswers') . '";');

	$posttitle = construct_phrase($vbphrase['award_request_post_title'], "$award_request_recipient_name");
	$award_send = 0;

		if ($vbulletin->options[award_request_formforumid] > 0)
    {
        $foruminfo = verify_id('forum', $vbulletin->options[award_request_formforumid], 0, 1);
        $forumperms = fetch_permissions($foruminfo[forumid]);
        $newpost['message'] =& $formsend;
        $newpost['title'] =& $posttitle;
        $newpost['parseurl'] = '1';
        $newpost['emailupdate'] = '9999';

				if ($vbulletin->userinfo['signature'] != '')
				{
					$newpost['signature'] = '1';
				}
				else
				{
					$newpost['signature'] = '0';
				}

	    	build_new_post('thread', $foruminfo, array(), array(), $newpost, $errors);
	      $award_send += 1;

        if ($vbulletin->options[award_request_formpoll]  == "1")
        {
					$polloption[1] = "Yes";
					$polloption[2] = "No";
					$polloption[3] = "Maybe";

            $threadinfo = verify_id('thread', $newpost[threadid], 0, 1);
            $polloptions = count($polloption);
            $question = $posttitle;
            $vbulletin->GPC['options'] = $polloption;

            $counter = 0;
            $optioncount = 0;
            $badoption = '';
            while ($counter++ < $polloptions)
            { // 0..Pollnum-1 we want, as arrays start with 0
                if ($vbulletin->options['maxpolllength'] AND vbstrlen($vbulletin->GPC['options']["$counter"]) > $vbulletin->options['maxpolllength'])
                {
                    $badoption .= iif($badoption, ', ') . $counter;
                }
                if (!empty($vbulletin->GPC['options']["$counter"]))
                {
                    $optioncount++;
                }
            }

            // Add the poll
            $poll =& datamanager_init('Poll', $vbulletin, ERRTYPE_STANDARD);

            $counter = 0;
            while ($counter++ < $polloptions)
            {
                if ($vbulletin->GPC['options']["$counter"] != '')
                {
                    $poll->set_option($vbulletin->GPC['options']["$counter"]);
                }
            }

            $poll->set('question',	$question);
            $poll->set('dateline',	TIMENOW);
            $poll->set('active',	'1');

            $pollid = $poll->save();
            //end create new poll

            // update thread
            $threadman =& datamanager_init('Thread', $vbulletin, ERRTYPE_STANDARD, 'threadpost');
            $threadman->set_existing($threadinfo);
            $threadman->set('pollid', $pollid);
            $threadman->save();
        }
    }


    if ($vbulletin->options['award_request_formreplythreadid'] > 0)
    {
        $threadinfo = verify_id('thread', $vbulletin->options['award_request_formreplythreadid'], 0, 1);
        $forumperms = fetch_permissions($threadinfo[forumid]);
        $newpost['message'] =& $formsend;
        $newpost['title'] =& $posttitle;
        $newpost['parseurl'] = "1";
        $newpost['emailupdate'] = '9999';

				if ($vbulletin->userinfo['signature'] != '')
				{
					$newpost['signature'] = '1';
				}
				else
				{
					$newpost['signature'] = '0';
				}
        build_new_post('reply', $foruminfo, $threadinfo, $postinfo, $newpost, $errors);
        $award_send += 2;
    }
			
    if (!empty($vbulletin->options['award_request_formpmname']))
    {
        $vbulletin->GPC['message'] =& $formsend;
        $vbulletin->GPC['title'] =& $posttitle;
        $vbulletin->GPC['recipients'] =& $vbulletin->options['award_request_formpmname'];

        $pm['message'] =& $vbulletin->GPC['message'];
        $pm['title'] =& $vbulletin->GPC['title'];
        $pm['recipients'] =& $vbulletin->GPC['recipients'];


        // create the DM to do error checking and insert the new PM
        $pmdm =& datamanager_init('PM', $vbulletin, ERRTYPE_ARRAY);

        $pmdm->set('fromuserid', $vbulletin->userinfo['userid']);
        $pmdm->set('fromusername', $vbulletin->userinfo['username']);
        $pmdm->setr('title', $pm['title']);
        $pmdm->setr('message', $pm['message']);
        $pmdm->set_recipients($pm['recipients'], $permissions);
        $pmdm->set('dateline', TIMENOW);
				$pmdm->pre_save();

				// process errors if there are any
				$errors = $pmdm->errors;

				if (!empty($errors))
				{
					$error = construct_errors($errors); // this will take the preview's place
					eval(standard_error($error));
				}
				else
				{
					// everything's good!
					$pmdm->save();
					unset($pmdm);
	        $award_send += 4;
				}
    }

		if (!empty($vbulletin->options['award_request_formemailaddress']))
    {
        vbmail($vbulletin->options['award_request_formemailaddress'], $posttitle, $formsend);
        $award_send += 8;
    }

	if ($award_send > 0)
	{
		$errormessage = $vbphrase['award_request_completed'];
	}
	else
	{
		$errormessage = $vbphrase['award_request_incompleted'];
	}

	eval('print_output("' . fetch_template('STANDARD_ERROR') . '");');
	exit();
}

if ($_REQUEST['do'] == 'request')
{
	$vbulletin->input->clean_array_gpc('r', array(
			'award_id' => TYPE_UINT
	));

	if (empty($vbulletin->GPC['award_id']))
	{
    $errormessage = $vbphrase['award_request_noawardid'];
		eval('print_output("' . fetch_template('STANDARD_ERROR') . '");');
    exit();
	}
	$award = $db->query_first("SELECT * FROM " . TABLE_PREFIX . "award WHERE award_id = ".$vbulletin->GPC['award_id'] ."");
	eval('print_output("' . fetch_template('awards_request_form') . '");');
}
?>