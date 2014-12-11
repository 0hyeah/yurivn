<?php
chdir('./../');
require_once('./global.php');
require_once(DIR . '/includes/functions_user.php');
require_once(DIR . '/includes/functions_threadmanage.php');
require_once('yrms/class/vietsubmanga_class.php');
require_once('yrms/class/database_class.php');
require_once('yrms/class/forumpost_class.php');
require_once('yrms/class/award_class.php');
require_once('yrms/include/function.php');

if(getParam('mangaid')) {
    $currentAction = 'edit';
} elseif(getParam('do') == 'reward') {
    $currentAction = 'reward';
} else {
    $currentAction = 'add';
}

$manga = new Manga;
switch($currentAction) {
    case 'add':
        if(!$vbulletin->userinfo['userid']) {
            print_no_permission();
        }
        $pageTitle = $vbphrase['yrms_mangaadd'];
        $messagetype = "info";
        $message= $vbphrase['yrms_inputtip'];
        break;
    case 'edit':
        $manga->load(getParam('mangaid'));
        if(!$manga->checkOwner()) {
            print_no_permission();
        }

        $pageTitle = $vbphrase['yrms_edit'];
        $messagetype = "info";
        $message= $vbphrase['yrms_inputtip'];

        $inputData = $manga->getData();
        break;
    case 'reward':
        if(!can_moderate($manga->getForumId())) {
            print_no_permission();
        }

        $pageTitle = $vbphrase['yrms_newmanga_award'];
        $messagetype = "info";
        $message= $vbphrase['yrms_rewardtip'];
}

if(isPost()){
    $inputData = getPost();
    $error = findInputError($inputData, $currentAction);

    if(!$error){
        $manga->setData($inputData);
        if($inputData['posturl']) {
            $threadId = extract_threadid_from_url($inputData['posturl']);
            $threadInfo = fetch_threadinfo($threadId);

            if($threadInfo['forumid'] == $manga->getForumId()) {
                $manga->setThreadId($threadInfo['threadid'])
                    ->setPostId($threadInfo['firstpostid'])
                    ->setPosterId($threadInfo['postuserid']);
            } elseif($threadInfo['forumid'] == $manga->getOnlineForumId()) {
                $manga->setPosterId($threadInfo['postuserid']);
            }
        }

        $manga->save();
        $messagetype = "success";

        switch($currentAction) {
            case 'add':
                $posterId = $manga->getPosterId();
                $posterInfo = fetch_userinfo($posterId);
                $message = construct_phrase($vbphrase['yrms_msg_success_newmanga'],
                                            $posterInfo['username'],
                                            $manga->getMangaTitle(),
                                            $vbulletin->options['yrms_vietsubmanga_yun_newproject'],
                                            $manga->getMangaId());
                break;
            case 'edit':
                $message = construct_phrase($vbphrase['yrms_msg_success_general'],
                                            $vbphrase['yrms_edit'],
                                            $vbphrase['yrms_manga'],
                                            $manga->getMangaTitle());
                break;
            case 'reward':
                $message = construct_phrase($vbphrase['yrms_msg_success_general'],
                                            $vbphrase['yrms_reward'],
                                            $vbphrase['yrms_manga'],
                                            $manga->getMangaTitle());
                break;
        }
        $contenttemplatename = 'yrms_message';
    }

    else{
        $messagetype = "error";
        $message= nl2br(construct_phrase($vbphrase['yrms_msg_error_head'],$vbphrase['yrms_mangaadd'])."\n".$error);
    }

}

if(!isset($contenttemplatename))
    $contenttemplatename = 'yrms_vietsubmanga_manga_save';
if($inputData['fansubname'] == '')
    $inputData['fansubname'] = "Yurivn";
if($inputData['fansubsite'] == '')
    $inputData['fansubsite'] = "http://yurivn.net";

$messagebox = vB_Template::create('yrms_messagebox');
$messagebox->register('messagetype', $messagetype);
$messagebox->register('message', $message);

$type_check[$inputData['type']] = "checked";
$page_templater = vB_Template::create($contenttemplatename);
$page_templater->register('pageTitle',$pageTitle);
$page_templater->register('currentAction',$currentAction);
$page_templater->register('inputData',$inputData);
$page_templater->register('type_check',$type_check);
$page_templater->register('messagebox',$messagebox->render());

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

function findInputError($inputData, $currentAction)
{
    global $vbphrase, $manga;
    $error = "";
    //check post url
    if($currentAction == 'reward') {
        if(strpos($inputData['posturl'], "http://")===false && strpos($inputData['posturl'], "https://")===false){
            $error.= construct_phrase($vbphrase['yrms_msg_error_invalidlink'],$vbphrase['yrms_posturl'])."\n";
        } else {
            $threadId = extract_threadid_from_url($inputData['posturl']);
            $threadInfo = fetch_threadinfo($threadId);
            if (!$threadInfo || $threadInfo['forumid'] != $manga->getForumId()) {
                $error.= construct_phrase($vbphrase['yrms_msg_error_postid'],$vbphrase['yrms_posturl'],$vbphrase['yrms_vietsubmanga'])."\n";
            }
        }
    }

    //check illustration
    if(strpos($inputData['illustration'], "http://")===false && strpos($inputData['illustration'], "https://")===false){
        $error.= construct_phrase($vbphrase['yrms_msg_error_invalidlink'],$vbphrase['yrms_illustration'])."\n";
    }
    //check mangatitle
    if($inputData['mangatitle']==""){
        $error.= construct_phrase($vbphrase['yrms_msg_error_blankfield'],$vbphrase['yrms_mangatitle'])."\n";
    }
    //check othername
    if($inputData['othertitle']!=""){
        $othertitles = explode(',', $inputData['othertitle']);
        foreach($othertitles as $othertitle){
            if($othertitle==$inputData['mangatitle']){
                $error.= construct_phrase($vbphrase['yrms_msg_error_othertitle'],$vbphrase['yrms_othertitle'],$vbphrase['yrms_mangatitle'])."\n";
                break;
            }
        }
    }

    //check author
    if($inputData['author'] == '' && $currentAction == 'add') {
        $error.= construct_phrase($vbphrase['yrms_msg_error_blankfield'],$vbphrase['yrms_author'])."\n";
    }

    //check type
    if($inputData['type']==''){
        $error.= construct_phrase($vbphrase['yrms_msg_error_blankselect'],$vbphrase['yrms_type'])."\n";
    }

    //check original composition
    if($inputData['type'] == 3 && $inputData['originalcomposition']==''){
        $error.= construct_phrase($vbphrase['yrms_msg_error_blankfield'],$vbphrase['yrms_originalcomposition'])."\n";
    }

    //check genre
    if($inputData['genre']==''){
        $error.= construct_phrase($vbphrase['yrms_msg_error_blankfield'],$vbphrase['yrms_genre'])."\n";
    }

    //check summary
    if($inputData['summary']==''){
        $error.= construct_phrase($vbphrase['yrms_msg_error_blankfield'],$vbphrase['yrms_summary'])."\n";
    }

    return $error;
}