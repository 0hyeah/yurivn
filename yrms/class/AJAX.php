<?php
require_once('.../global.php');
require_once 'vietsubmanga.class.php';
require_once 'function.php';
switch ($_REQUEST['ajax']){
    case 'checkVietSubManga': 
        $manganame = $_REQUEST['manganame'];
        $vbulletin->db->query_read();
        break;
}
