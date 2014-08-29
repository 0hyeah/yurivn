<?php
function send_pm($fromuser, $fromid, $touser, $title, $message)
{
	global $vbulletin, $permissions;

	$permissions['pmsendmax'] = 10;
	$pmdm =& datamanager_init('PM', $vbulletin, ERRTYPE_ARRAY);
	$pmdm->set('fromuserid', $fromid);
	$pmdm->set('fromusername', $fromuser);
	$pmdm->set('title', $title);
	$pmdm->set('message', $message);
	$pmdm->set_recipients($touser, $permissions);
	$pmdm->set('dateline', TIMENOW);
	$pmdm->set('showsignature', 1);
	$pmdm->set_info('savecopy', 0);
	$pmdm->pre_save();

	if (empty($pmdm->errors))
	{
		$pmdm->save();
	}
}

?>