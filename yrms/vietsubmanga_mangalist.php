<?php
chdir('./../');
require_once('./global.php');
require_once(DIR . '/includes/functions_user.php');
require_once('yrms/class/vietsubmanga_class.php');
require_once('yrms/class/database_class.php');
require_once('yrms/include/function.php');

$currentUserId = $vbulletin->userinfo['userid'];
if(!$currentUserId) {
    print_no_permission();
}

$owner = getParam('owner');
$limit = 20;
$filter = getParam('filter');
$keyword = getParam('keyword');
$page = getParam('page');
if (!$page) {
    $page = 1;
}

$mangaObject = new Manga;
$totalManga = $mangaObject->getTotal();
$mangaCollection = $mangaObject->setPage($page)->setLimit($limit)->setFilter($filter)->setKeyword('%'.$keyword.'%')->getCollection();
$mangaDatas = array();
$tableorder = 1;
if(!empty($mangaCollection)){
    foreach ($mangaCollection as $manga) {
        $mangaData = $manga->getData();
        $mangaData['status'] = $vbphrase["yrms_projectstatus{$mangaData['status']}"];

        if($mangaData['numberofchapter'] == 0)
            $mangaData['numberofchapter'] = '??';

        if($tableorder%2==1)
            $mangaData['rowtype'] = 'even';
        else
            $mangaData['rowtype'] = 'odd';
        $mangaDatas[] = $mangaData;

        $tableorder++;
    }
    $pagenav = construct_pagenavigation($page, $limit, $totalManga, removeqsvar($_SERVER['REQUEST_URI'],page));
}
else{
    $messagetype = "error";
    if(!empty($keyword))
        $message= construct_phrase($vbphrase['yrms_msg_error_notfound'],$vbphrase['yrms_manga'],$keyword);
    else
        $message= construct_phrase($vbphrase['yrms_msg_error_emptylist'],$vbphrase['yrms_manga'],$vbphrase['yrms_mangaadd']);
    $messagebox = vB_Template::create('yrms_messagebox');
    $messagebox->register('messagetype', $messagetype);
    $messagebox->register('message', $message);
    $mangalist=$messagebox->render();
}

$pageTitle = $vbphrase['yrms_vietsubmangalist'];

$page_templater = vB_Template::create('yrms_vietsubmanga_manga_list');
$page_templater->register('pageTitle',$pageTitle);
$page_templater->register('mangas',$mangaDatas);
$page_templater->register('pagenav',$pagenav);

$navbits = construct_navbits($navbits);
$navbar = render_navbar_template($navbits);
$includecss = array();

$templater = vB_Template::create('yrms_navbar');
$templater->register_page_templates();
$templater->register('includecss', $includecss);
$templater->register('cpnav', $cpnav);
$templater->register('HTML', $page_templater->render());
$templater->register('navbar', $navbar);
$templater->register('onload', '');
$templater->register('pagetitle', $pageTitle);
$templater->register('template_hook', $template_hook);
$templater->register('clientscripts', $clientscripts);
print_output($templater->render());