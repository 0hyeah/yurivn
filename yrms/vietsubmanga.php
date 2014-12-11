<?php
chdir('./../');
require_once('./global.php');
require_once(DIR . '/includes/functions_user.php');
require_once('yrms/class/vietsubmanga.class.php');
require_once('yrms/class/award.class.php');
require_once('yrms/class/function.php');
require_once('./includes/functions_newpost.php');
require_once('./includes/functions_threadmanage.php');
require_once('./includes/functions_databuild.php');

$currentUserId = $vbulletin->userinfo['userid'];
if(!$currentUserId) {
    print_no_permission ();
}

$shelltemplatename = 'yrms_navbar';

if (!isset($_REQUEST['do'])) $_REQUEST['do'] = 'mangalist';

if ($_REQUEST['do'] == 'mangalist'){
    $pagetitle = $vbphrase['yrms_vietsubmangalist'];
    
    $manga = new Manga;
    $mangalist=$manga->mangalist($_GET['filter'],$_GET['keyword']);
    
    $contenttemplatename = 'yrms_vietsubmanga_manga_list';
}

if ($_REQUEST['do'] == 'mangamyproject'){
    $pagetitle = $vbphrase['yrms_myproject'];
    
    $manga = new Manga;
    $myprojects=$manga->myproject();
    
    $contenttemplatename = 'yrms_vietsubmanga_manga_myproject';
}

if ($_REQUEST['do'] == 'mangaadd'){

}

if ($_REQUEST['do'] == 'mangareward'){
    $pagetitle = $vbphrase['yrms_newmanga_award'];
    
    if(isset($_POST['submitted'])){
        $manga = new Manga;
        
        $_POST['illustration']="[IMG={$vbulletin->options['yrms_main_illustrationwidth']}|]{$_POST['illustration']}[/IMG]";
        $_POST['illustration'] = str_replace("[IMG={$vbulletin->options['yrms_main_illustrationwidth']}|][IMG]", "[IMG={$vbulletin->options['yrms_main_illustrationwidth']}|]", $_POST['illustration']);
        $_POST['illustration'] = str_replace("[IMG={$vbulletin->options['yrms_main_illustrationwidth']}|][IMG={$vbulletin->options['yrms_main_illustrationwidth']}|]", "[IMG={$vbulletin->options['yrms_main_illustrationwidth']}|]", $_POST['illustration']);
        $_POST['illustration'] = str_replace("[/IMG][IMG]", "[/IMG]", $_POST['illustration']);
        
        $mainpost_info = extract_info_from_posturl($_POST['posturl']);
        $manga->postid = $mainpost_info['postid'];
        $manga->illustration = $_POST['illustration']; 
        $manga->mangatitle = $_POST['mangatitle']; 
        $manga->othertitle = $_POST['othertitle']; 
        $manga->author = $_POST['author']; 
        $manga->type = $_POST['type']; 
        $type_check[$_POST['type']] = "checked";
        $manga->numberofchapter = $_POST['numberofchapter'];
        $manga->finishedchapter = $_POST['finishedchapter'];
        $manga->originalcomposition = $_POST['originalcomposition']; 
        $manga->genre = $_POST['genre']; 
        $manga->summary = $_POST['summary']; 
        $manga->fansubname = $_POST['fansubname']; 
        $manga->fansubsite = $_POST['fansubsite']; 
        $manga->fansubnote = $_POST['fansubnote']; 
        
        $error=$manga->validate();
        if($error==""){
            $manga->reward();
            $messagetype = "success";
            $message= construct_phrase($vbphrase['yrms_msg_success_general'],$vbphrase['yrms_reward'],$vbphrase['yrms_manga'],$manga->mangatitle);
            $messagebox = vB_Template::create('yrms_messagebox');
            $messagebox->register('messagetype', $messagetype);
            $messagebox->register('message', $message);
            $contenttemplatename = 'yrms_message';
        }
            
        else{
            $messagetype = "error";
            $message= nl2br(construct_phrase($vbphrase['yrms_msg_error_head'],$vbphrase['yrms_reward'])."\n".$error);
            $messagebox = vB_Template::create('yrms_messagebox');
            $messagebox->register('messagetype', $messagetype);
            $messagebox->register('message', $message);
        }
    }
    
    if(!isset($contenttemplatename))
        $contenttemplatename = 'yrms_vietsubmanga_manga_reward';
}

if ($_REQUEST['do'] == 'mangaedit'){  
    $pagetitle = $vbphrase['yrms_edit'];
    
    $manga = new Manga($_GET['mangaid']);
    $type_check[$manga->type] = "checked";
    $status_check[$manga->status] = "checked";
    if(isset($_POST['submitted'])){
        $manga->postid = $_POST['postid']; 
        $manga->illustration = $_POST['illustration']; 
        $manga->mangatitle = $_POST['mangatitle']; 
        $manga->othertitle = $_POST['othertitle']; 
        $manga->author = $_POST['author']; 
        $manga->type = $_POST['type']; 
        $manga->numberofchapter = $_POST['numberofchapter'];
        $manga->finishedchapter = $_POST['finish edchapter'];
        $manga->originalcomposition = $_POST['originalcomposition']; 
        $manga->status = $_POST['status']; 
        $manga->genre = $_POST['genre']; 
        $manga->summary = $_POST['summary']; 
        $manga->fansubname = $_POST['fansubname']; 
        $manga->fansubsite = $_POST['fansubsite']; 
        $manga->fansubnote = $_POST['fansubnote'];   
        
        $error=$manga->validate();
        if($error==""){
            $manga->update();
            $messagetype = "success";
            $message= construct_phrase($vbphrase['yrms_msg_success_general'],$vbphrase['yrms_edit'],$vbphrase['yrms_manga'],$manga->mangatitle);
            $messagebox = vB_Template::create('yrms_messagebox');
            $messagebox->register('messagetype', $messagetype);
            $messagebox->register('message', $message);
            $contenttemplatename = 'yrms_message';
        }
            
        else{
            $messagetype = "error";
            $message= nl2br(construct_phrase($vbphrase['yrms_msg_error_head'],$vbphrase['yrms_edit'])."\n".$error);
            $messagebox = vB_Template::create('yrms_messagebox');
            $messagebox->register('messagetype', $messagetype);
            $messagebox->register('message', $message);
        }
        
    }
    
    if(!isset($contenttemplatename))
        $contenttemplatename = 'yrms_vietsubmanga_manga_edit';

}

if ($_REQUEST['do'] == 'mangareport'){
    $pagetitle = $vbphrase['yrms_brokenlink_report'];
    
    $page_templater = vB_Template::create('yrms_vietsubmanga_manga_report');
}

if ($_REQUEST['do'] == 'mangadelete'){
    $pagetitle = $vbphrase['yrms_vietsubmangalist'];
    
    $page_templater = vB_Template::create('yrms_vietsubmanga_manga_list');
}

if ($_REQUEST['do'] == 'chapterlist'){
    
    $pagetitle = $vbphrase['yrms_chaptermanage'];
    
    $manga = new Manga($_GET['mangaid']);
    $chapter = new Chapter($manga);
    $chapterlist=$chapter->chapterlist();
    
    $contenttemplatename = 'yrms_vietsubmanga_chapter_list';
}

if ($_REQUEST['do'] == 'chapteradd'){
    $pagetitle = $vbphrase['yrms_chapteradd'];
    $messagetype = "info";
    $message= $vbphrase['yrms_inputtip'];
    
    $manga = new Manga($_GET['mangaid']);
    if(isset($_POST['submitted'])){       
        $chapter = new Chapter($manga);
        $chapter->mangaid = $manga->mangaid;
        $chapter->type = $_POST['type'];
        $type_select[$_POST['type']] = "selected";
        $chapter->chapternumber = $_POST['chapternumber'];
        $chapter->chaptertitle = $_POST['chaptertitle'];
        $manga->status = $_POST['status'];
        $status_check[$_POST['status']] = "checked"; 
        $chapter->rate = $_POST['rate'];
        $rate_check[$_POST['rate']] = "checked";
        $chapter->fansubmember = array();
        $chapter->fansubnote = $_POST['fansubnote'];
        
        $chapter->downloadlink = array();
        $chapter->onlinelink = $_POST['onlinelink'];
        
        $realnumberofhost = 0;
        for($number=1;$number<=$_POST['numberofhost'];$number++){
            if(array_key_exists(ucfirst(strtolower($_POST["hostname$number"])),$chapter->downloadlink)){
                $chapter->downloadlink[ucfirst(strtolower($_POST["hostname$number"]))][] = $_POST["hostlink$number"];
            }
            else{
                $chapter->downloadlink += array(ucfirst(strtolower($_POST["hostname$number"])) => array("{$_POST["hostlink$number"]}"));
                $realnumberofhost++;
            }
        }  
        $chapter->numberofhost = $realnumberofhost;
        
        $chapter->fansubmember += array('translator' => get_userid_massively($_POST["translator"]));
        if (!empty($_POST["proofreader"])) {
            $chapter->fansubmember += array('proofreader' => get_userid_massively($_POST["proofreader"]));
        }
        $chapter->fansubmember += array('editor' => get_userid_massively($_POST["editor"]));
        if (!empty($_POST["qualitychecker"])) {
            $chapter->fansubmember += array('qualitychecker' => get_userid_massively($_POST["qualitychecker"]));
        }
        $chapter->fansubmember += array('uploader' => $vbulletin->userinfo['userid']);
        
        $error=$chapter->validate();
        if($error==""){
            $messagetype = "success";
            $message= $chapter->add();
            $contenttemplatename = 'yrms_message';
        }
            
        else{
            $messagetype = "error";
            $message= nl2br(construct_phrase($vbphrase['yrms_msg_error_head'],$vbphrase['yrms_chapteradd'])."\n".$error);
        }
        
    }
    
    if (!isset($contenttemplatename)) {
        $contenttemplatename = 'yrms_vietsubmanga_chapter_add';
    }
    $messagebox = vB_Template::create('yrms_messagebox');
    $messagebox->register('messagetype', $messagetype);
    $messagebox->register('message', $message);
}

if ($_REQUEST['do'] == 'chapterreward'){
    $pagetitle = $vbphrase['yrms_newchapter_award'];
    $messagetype = "info";
    $message= $vbphrase['yrms_inputtip'];
    
    if(isset($_POST['submitted'])){
        $mainpost_info = extract_info_from_posturl($_POST['mainposturl']);
        $onlinepost_info = extract_info_from_posturl($_POST['onlineposturl']);
        
        $manga = new Manga($_POST['mangaid']);
        $chapter = new Chapter($manga);
        
        $chapter->postid = $mainpost_info['postid'];
        $chapter->poster = $mainpost_info['userid'];
        $chapter->readonlinepostid = $onlinepost_info['postid'];
        $chapter->readonlineposter = $onlinepost_info['userid'];
        $chapter->mangaid = $manga->mangaid;
        $chapter->type = $_POST['type'];
        $type_select[$_POST['type']] = "selected";
        $chapter->chapternumber = $_POST['chapternumber'];
        $chapter->chaptertitle = $_POST['chaptertitle'];
        $manga->status = $_POST['status'];
        $status_check[$_POST['status']] = "checked"; 
        $chapter->rate = $_POST['rate'];
        $rate_check[$_POST['rate']] = "checked";
        $chapter->fansubmember = array();
        $chapter->fansubnote = $_POST['fansubnote'];
        
        $chapter->downloadlink = array();
        $chapter->onlinelink = $_POST['onlinelink'];
        $realnumberofhost = 0;
        for($number=1;$number<=$_POST['numberofhost'];$number++){
            if(array_key_exists(ucfirst(strtolower($_POST["hostname$number"])),$chapter->downloadlink)){
                $chapter->downloadlink[ucfirst(strtolower($_POST["hostname$number"]))][] = $_POST["hostlink$number"];
            }
            else{
                $chapter->downloadlink += array(ucfirst(strtolower($_POST["hostname$number"])) => array("{$_POST["hostlink$number"]}"));
                $realnumberofhost++;
            }
        }  
        $chapter->numberofhost = $realnumberofhost;
        
        $chapter->fansubmember += array('translator' => get_userid_massively($_POST["translator"]));
        if (!empty($_POST["proofreader"])) {
            $chapter->fansubmember += array('proofreader' => get_userid_massively($_POST["proofreader"]));
        }
        $chapter->fansubmember += array('editor' => get_userid_massively($_POST["editor"]));
        if (!empty($_POST["qualitychecker"])) {
            $chapter->fansubmember += array('qualitychecker' => get_userid_massively($_POST["qualitychecker"]));
        }
        
        if(empty($mainpost_info)){
            $chapter->fansubmember += array('uploader' => $chapter->readonlineposter);
        } else{
            $chapter->fansubmember += array('uploader' => $chapter->poster);
        }
        
        $error=$chapter->validate();
        if($error==""){
            $messagetype = "success";
            $message= $chapter->reward();
            $contenttemplatename = 'yrms_message';
        }
            
        else{
            $messagetype = "error";
            $message= nl2br(construct_phrase($vbphrase['yrms_msg_error_head'],$vbphrase['yrms_chapteradd'])."\n".$error);
        }
        
    }
    
    if (!isset($contenttemplatename)) {
        $contenttemplatename = 'yrms_vietsubmanga_chapter_reward';
    }
    $messagebox = vB_Template::create('yrms_messagebox');
    $messagebox->register('messagetype', $messagetype);
    $messagebox->register('message', $message);
}

if ($_REQUEST['do'] == 'chapteredit'){

    $pagetitle = $vbphrase['yrms_edit'];
    
    
    $page_templater = vB_Template::create('yrms_vietsubmanga_chapter_edit');
}

if ($_REQUEST['do'] == 'chaptermirrorlinkadd'){

    $pagetitle = $vbphrase['yrms_mirrorlinkadd'];
    
    
    $page_templater = vB_Template::create('yrms_vietsubmanga_chapter_mirrorlinkadd');
}

if ($_REQUEST['do'] == 'chapteronlinelinkadd'){

    $pagetitle = $vbphrase['yrms_readonlinelinkadd'];
    
    
    $page_templater = vB_Template::create('yrms_vietsubmanga_chapter_onlinelinkadd');
}

//construct_page_nav($pagenumber, $perpage, $results, $address);

    $page_templater = vB_Template::create($contenttemplatename);
    if($contenttemplatename == 'yrms_vietsubmanga_manga_add'){

    }
    if($contenttemplatename == 'yrms_vietsubmanga_manga_list'){
        $page_templater->register('mangalist',$mangalist);
    }
    if($contenttemplatename == 'yrms_vietsubmanga_manga_myproject'){
        $page_templater->register('myprojects',$myprojects);
    }
    if($contenttemplatename == 'yrms_vietsubmanga_manga_reward'){
        $page_templater->register('postid',$_POST['postid']);
        $page_templater->register('illustration',$_POST['illustration']);
        $page_templater->register('mangatitle',$_POST['mangatitle']);
        $page_templater->register('othertitle',$_POST['othertitle']);
        $page_templater->register('author',$_POST['author']);
        $page_templater->register('type_check',$type_check);
        $page_templater->register('numberofchapter',$_POST['numberofchapter']);
        $page_templater->register('originalcomposition',$_POST['originalcomposition']);
        $page_templater->register('genre',$_POST['genre']);
        $page_templater->register('summary',$_POST['summary']);
        $page_templater->register('fansubname',$_POST['fansubname']);
        $page_templater->register('fansubsite',$_POST['fansubsite']);
        $page_templater->register('fansubnote',$_POST['fansubnote']);   
        if(isset($_POST['submitted']))
            $page_templater->register('messagebox',$messagebox->render());  
    }
    if($contenttemplatename == 'yrms_vietsubmanga_manga_edit'){
        $page_templater->register('postid',$manga->postid);
        $page_templater->register('illustration',$manga->illustration);
        $page_templater->register('mangatitle',$manga->mangatitle);
        $page_templater->register('othertitle',$manga->othertitle);
        $page_templater->register('author',$manga->author);
        $page_templater->register('type_check',$type_check);
        $page_templater->register('numberofchapter',$manga->numberofchapter);
        $page_templater->register('originalcomposition',$manga->originalcomposition);
        $page_templater->register('status_check',$status_check);
        $page_templater->register('genre',$manga->genre);
        $page_templater->register('summary',$manga->summary);
        $page_templater->register('fansubname',$manga->fansubname);
        $page_templater->register('fansubsite',$manga->fansubsite);
        $page_templater->register('fansubnote',$manga->fansubnote);   
        if(isset($_POST['submitted']))
            $page_templater->register('messagebox',$messagebox->render());  
    }
    if($contenttemplatename == 'yrms_vietsubmanga_chapter_add'){
        $page_templater->register('mangatitle',$manga->mangatitle); 
        $page_templater->register('type_select',$type_select);
        $page_templater->register('chapternumber',$_POST['chapternumber']);
        $page_templater->register('chaptertitle',$_POST['chaptertitle']);
        $page_templater->register('status_check',$status_check);
        $page_templater->register('rate_check',$rate_check);
        $page_templater->register('translator',$_POST['translator']);
        $page_templater->register('proofreader',$_POST['proofreader']);
        $page_templater->register('editor',$_POST['editor']);
        $page_templater->register('qualitychecker',$_POST['qualitychecker']);
        $page_templater->register('fansubnote',$_POST['fansubnote']);  
        $page_templater->register('numberofhost',$_POST['numberofhost']); 
        for($number=1;$number<=$chapter->numberofhost;$number++){
            $page_templater->register('hostname'.$number,$_POST["hostname$number"]); 
            $page_templater->register('hostlink'.$number,$_POST["hostlink$number"]);
        }
        $page_templater->register('onlinelink',$_POST['onlinelink']);
        if(isset($_POST['submitted']))
            $page_templater->register('messagebox',$messagebox->render());  
    }
    if($contenttemplatename == 'yrms_vietsubmanga_chapter_list'){
        $page_templater->register('mangatitle',$manga->mangatitle);
        $page_templater->register('mangaid',$manga->mangaid);
        $page_templater->register('chapterlist',$chapterlist);
    }
    if($contenttemplatename == 'yrms_vietsubmanga_chapter_reward'){
        $page_templater->register('mainposturl',$_POST['mainposturl']); 
        $page_templater->register('type_select',$type_select);
        $page_templater->register('chapternumber',$_POST['chapternumber']);
        $page_templater->register('chaptertitle',$_POST['chaptertitle']);
        $page_templater->register('status_check',$status_check);
        $page_templater->register('rate_check',$rate_check);
        $page_templater->register('translator',$_POST['translator']);
        $page_templater->register('proofreader',$_POST['proofreader']);
        $page_templater->register('editor',$_POST['editor']);
        $page_templater->register('qualitychecker',$_POST['qualitychecker']);
        $page_templater->register('fansubnote',$_POST['fansubnote']);  
        $page_templater->register('numberofhost',$_POST['numberofhost']); 
        for($number=1;$number<=$chapter->numberofhost;$number++){
            $page_templater->register('hostname'.$number,$_POST["hostname$number"]); 
            $page_templater->register('hostlink'.$number,$_POST["hostlink$number"]);
        }
        $page_templater->register('onlineposturl',$_POST['onlineposturl']); 
        $page_templater->register('onlinelink',$_POST['onlinelink']);
        $page_templater->register('messagebox', $messagebox->render());
    
}
    
    if($contenttemplatename == 'yrms_message'){
        $page_templater->register('messagebox',$messagebox->render());
        $page_templater->register('pagetitle', $pagetitle);
    }



$onload = '';
$navbits = construct_navbits($navbits);
$navbar = render_navbar_template($navbits);
$includecss = array();

$templater = vB_Template::create($shelltemplatename);
$templater->register_page_templates();
$templater->register('includecss', $includecss);
$templater->register('cpnav', $cpnav);
$templater->register('HTML', $page_templater->render());
$templater->register('navbar', $navbar);
$templater->register('navclass', $navclass);
$templater->register('onload', $onload);
$templater->register('pagetitle', $pagetitle);
$templater->register('template_hook', $template_hook);
$templater->register('clientscripts', $clientscripts);
print_output($templater->render());
