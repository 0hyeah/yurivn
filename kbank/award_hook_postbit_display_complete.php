<?php
/*======================================================================*\
|| #################################################################### ||
|| # kBank 4.0
|| # Coded by mrpaint
|| # Contact: mrpaint@gmail.com
|| # I'm a Vietnamese! Thank you for using this script
|| # Last Updated: 15:40 Mar 20, 2010
|| #################################################################### ||
\*======================================================================*/
global $vbulletin;
if (defined('VB_AREA') 
	AND $vbulletin->kbank['award']['enabled']) {
	
	if (in_array(THIS_SCRIPT,$vbulletin->kbank['award']['AllowedScript'])) {
		//Our control only display in some script (predefined by AllowedScript), not everything using postbit_display_complete
		include_once(DIR . '/kbank/award_functions_generate.php');
		global $threadinfo;
		
		list($html_display_current,$html_display_buttons) = kbank_award_generate(
			$vbulletin->kbank['award']['award_options']['post']
			,$vbulletin->kbank['award']['display_options']['post']
			,$post
			,$threadinfo
		);
		
		$template_hook['postbit_signature_start'] .= $html_display_current;
		$template_hook['postbit_controls'] .= $html_display_buttons;
		if (empty($post['signature'])) $post['signature'] = '&nbsp;'; //to make sure our stuff is rendered
	}
	
	$kbankname = $vbulletin->kbank['name'];
	if ($vbulletin->kbank['postbit_elements'] & $vbulletin->kbank['bitfield']['display_elements']['kbank_show_award']) {
		$awardedtimes = vb_number_format($post[$vbulletin->kbank['award']['awardedtimes']]);
		$awardedamount = vb_number_format($post[$vbulletin->kbank['award']['awardedamount']],$vbulletin->kbank['roundup']);
	}
	if ($vbulletin->kbank['postbit_elements'] & $vbulletin->kbank['bitfield']['display_elements']['kbank_show_thank']) {
		$thanksenttimes = vb_number_format($post[$vbulletin->kbank['award']['thanksenttimes']]);
		$thanksentamount = vb_number_format($post[$vbulletin->kbank['award']['thanksentamount']],$vbulletin->kbank['roundup']);
		$thankreceivedtimes = vb_number_format($post[$vbulletin->kbank['award']['thankreceivedtimes']]);
		$thankreceivedamount = vb_number_format($post[$vbulletin->kbank['award']['thankreceivedamount']],$vbulletin->kbank['roundup']);
	}
	
	$templater = vB_Template::create('kbank_award_postbit_right_after_posts');
		$templater->register('awardedtimes', $awardedtimes);
		$templater->register('awardedamount', $awardedamount);
		$templater->register('thanksenttimes', $thanksenttimes);
		$templater->register('thanksentamount', $thanksentamount);
		$templater->register('thankreceivedtimes', $thankreceivedtimes);
		$templater->register('thankreceivedamount', $thankreceivedamount);
		$templater->register('post', $post);
		$templater->register('kbankname', $kbankname);
	$template_hook['postbit_userinfo_right_after_posts'] .= $templater->render();
}
?>