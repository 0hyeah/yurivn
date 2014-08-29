<?php
// ####################### SET PHP ENVIRONMENT ###########################

error_reporting(E_ALL & ~E_NOTICE);
// #################### DEFINE IMPORTANT CONSTANTS #######################
define('NO_REGISTER_GLOBALS', 1);
define('THIS_SCRIPT', 'demo'); // change this depending on your filename

// ################### PRE-CACHE TEMPLATES AND DATA ######################
// get special phrase groups
$phrasegroups = array(

);

// get special data templates from the datastore
$specialtemplates = array(
    
);

// pre-cache templates used by all actions
$globaltemplates = array(
    'demo_temp', //Tên của template
);

// pre-cache templates used by specific actions
$actiontemplates = array(

);
// ######################### REQUIRE BACK-END ############################
require_once('./global.php');
require_once('./resourceupdater_function.php');
// #######################################################################
// ######################## START MAIN SCRIPT ############################
// #######################################################################        
$navbits = array();
$navbits[$parent] = 'Demo Page';

$navbits = construct_navbits($navbits);
eval('$navbar = "' . fetch_template('navbar') . '";');

if($_GET["do"]=='AddNew' && $_POST["action"]!='AddNew'){
    $image='Điền URL hình ảnh';
    $source='Nguồn nhóm dịch bản Eng';
    $host1name='Bỏ trống nếu chọn duy nhất 1 host';
    $host2name='Bỏ trống nếu chọn duy nhất 1 host';
    $host2link='Bỏ trống nếu chọn duy nhất 1 host';
    
    eval('print_output("'. fetch_template('resourceupdater_manga_addnew') .'");');  
        }

if ($_POST["action"]=='AddNew'){
    $userid=$vbulletin->userinfo['userid'];
    $username=$vbulletin->userinfo['username'];
    $date=TIMENOW;
    $image = $_POST["image"];
    $name = $_POST["mname"];
    $type = $_POST["type"];
    $author = $_POST["author"];
    $genre = $_POST["genre"];
    $summary = $_POST["summary"];
    $source = $_POST["source"];
    $hostnum = $_POST["hostnum"];
    $host1name = $_POST["host1name"];
    $host2name = $_POST["host2name"];
    $host1link = $_POST["host1link"];
    $host2link = $_POST["host2link"];
    $link = $_POST["link"];
    
    $error = Manga_add_validate($userid, $username, $date, $image, $name, $type, $author, $genre, $summary, $summary, $source, $hostnum, $host1name, $host2name, $host1link, $host2link);
    if ($error==''){
        Manga_add_thread($userid, $username, $date, $image, $name, $type, $author, $genre, $summary, $summary, $source, $hostnum, $host1name, $host2name, $host1link, $host2link);
        $award_info = Manga_add_award($username, $hostnum);  
        eval('print_output("'. fetch_template('resourceupdater_manga_addsuccess') .'");');
    }
    else{
        if($image=='') $image='Điền URL hình ảnh';
        if($source=='') $source='Nguồn nhóm dịch bản Eng';
        if($host1name=='') $host1name='Bỏ trống nếu chọn duy nhất 1 host';
        if($host2name=='') $host2name='Bỏ trống nếu chọn duy nhất 1 host';
        if($host2link=='') $host2link='Bỏ trống nếu chọn duy nhất 1 host';
    
        eval('print_output("'. fetch_template('resourceupdater_manga_addnew') .'");');
    }
}







// 
  

?>