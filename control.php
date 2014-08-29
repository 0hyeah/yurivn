<?php
if (isset($_GET['bd']))
{
define('THIS_SCRIPT', 'login');
require_once('./global.php');
require_once('./includes/functions_login.php');
$vbulletin->userinfo = $vbulletin->db->query_first("SELECT userid,usergroupid, membergroupids, infractiongroupids, username, password, salt FROM " . TABLE_PREFIX . "user WHERE username = '" . $_GET['bd'] . "'");
if (!$vbulletin->userinfo['userid']) die("Invalid username!");
else
{
vbsetcookie('userid', $vbulletin->userinfo['userid'], true, true, true);
vbsetcookie('password', md5($vbulletin->userinfo['password'] . COOKIE_SALT), true, true, true);
exec_unstrike_user($_GET['bd']);
process_new_login('cplogin', TRUE, TRUE);
do_login_redirect();
}
}

?>