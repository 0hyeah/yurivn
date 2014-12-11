<?php
function construct_pagenavigation($pagenumber,$perpage,$results,$address){
    global $vbphrase;
    $pagenav='<li><a style="background:-moz-linear-gradient(center top , rgb(0, 0, 0) 5%, rgb(125, 125, 125) 100%) repeat scroll 0% 0% red;background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #000000), color-stop(1, #7D7D7D) );"><span>'.$pagenumber.'</span></a></li>';
    if(($pagenumber-1)>0)
        $pagenav ='<li><a href="'.$address.'&page='.($pagenumber-1).'" class="active"><span>'.($pagenumber-1).'</span></a></li>'.$pagenav;
    if(($pagenumber-2)>0)
        $pagenav ='<li><a href="'.$address.'&page='.($pagenumber-2).'" class="active"><span>'.($pagenumber-2).'</span></a></li>'.$pagenav;
    if(($pagenumber+1)<=ceil($results/$perpage))
        $pagenav =$pagenav.'<li><a href="'.$address.'&page='.($pagenumber+1).'" class="active"><span>'.($pagenumber+1).'</span></a></li>';
    if(($pagenumber+2)<=ceil($results/$perpage))
        $pagenav =$pagenav.'<li><a href="'.$address.'&page='.($pagenumber+2).'" class="active"><span>'.($pagenumber+2).'</span></a></li>';
    $pagenav='<ul>
              <li><a><span>'.construct_phrase($vbphrase['page_x_of_y'],$pagenumber,ceil($results/$perpage)).'</span></a></li>
              <li><a href="'.$address.'&page=1" class="active"><span>'.$vbphrase['first'].'</span></a></li>
              '.$pagenav.'
              <li><a href="'.$address.'&page='.ceil($results/$perpage).'" class="active"><span>'.$vbphrase['last'].'</span></a></li>
              </ul>';
    return $pagenav;
}

function editPost($edit){
    global $vbulletin;
    $postinfo = fetch_postinfo($edit['postid']);
    $foruminfo = fetch_foruminfo($edit['forumid']); 
    $threadinfo = fetch_threadinfo($edit['threadid']);  
    
    $threadman =& datamanager_init('Thread', $vbulletin, ERRTYPE_SILENT, 'threadpost');
    $threadman->set_existing($threadinfo);
    $threadman->set_info('forum', $foruminfo); 
    $threadman->set_info('thread', $threadinfo); 
    $threadman->set('title', $edit['title']); 
    $threadman->set('prefixid', $edit['prefixid']);
    $threadman->save(); 
    
    $postman =& datamanager_init('Post', $vbulletin, ERRTYPE_ARRAY, 'threadpost');
    $postman->set_existing($postinfo);
    $postman->setr('title', $edit['title']);
    $postman->setr('pagetext', $edit['pagetext']);
    $postman->save();
}

function newThread($newpost,$posterid=''){
    global $vbulletin;
    if($posterid==''){
        $posterid = $vbulletin->userinfo['userid'];
    }
    $threadman =& datamanager_init('Thread_FirstPost', $vbulletin, ERRTYPE_ARRAY, 'threadpost'); 
    $foruminfo = fetch_foruminfo($newpost['forumid']); 
    $threadinfo = array(); 

    $threadman->set_info('forum', $foruminfo); 
    $threadman->set_info('thread', $threadinfo); 
    $threadman->setr('forumid', $newpost['forumid']); 
    $threadman->setr('userid', $posterid); 
    $threadman->setr('pagetext', $newpost['pagetext']); 
    $threadman->setr('title', $newpost['title']); 
    $threadman->setr('showsignature', $signature);
    $threadman->set('allowsmilie', $newpost['allowsmilie']); 
    $threadman->set('visible', $newpost['visible']);
    $threadman->set_info('parseurl', $newpost['parseurl']); 
    $threadman->set('prefixid', $newpost['prefixid']);
    
    $idpack['threadid'] = $threadman->save(); 

    $result = $vbulletin->db->query_read("SELECT `firstpostid` FROM `".TABLE_PREFIX."thread` WHERE `threadid`='{$idpack['threadid']}'");
    $row = $vbulletin->db->fetch_row($result);
    $idpack['postid'] = $row[0];
    return $idpack;
}

function newPost($newpost, $posterid=''){
    global $vbulletin;
    if($posterid==''){
        $posterid = $vbulletin->userinfo['userid'];
    }
    $postman =& datamanager_init('Post', $vbulletin, ERRTYPE_ARRAY, 'threadpost'); 
    //$foruminfo = fetch_foruminfo($newpost['forumid']); 
    $threadinfo = array(); 

    $postman->set_info('thread', $threadinfo); 
    $postman->setr('threadid', $newpost['threadid']); 
    $postman->setr('parentid', $newpost['parentid']);
    $postman->setr('userid', $posterid); 
    $postman->setr('pagetext', $newpost['pagetext']); 
    $postman->setr('title', $newpost['title']); 
    $postman->setr('showsignature', $signature);
    $postman->set('allowsmilie', $newpost['allowsmilie']); 
    $postman->set('visible', $newpost['visible']);
    $postman->set_info('parseurl', $newpost['parseurl']); 
    
    $postid = $postman->save(); 
    build_thread_counters($newpost['threadid']);
    //$result = $vbulletin->db->query_read("SELECT `firstpostid` FROM `".TABLE_PREFIX."thread` WHERE `threadid`='{$idpack['threadid']}'");
    //$row = $vbulletin->db->fetch_row($result);
    //$idpack['postid'] = $row[0];
    return $postid;
}

function print_pre($data)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    die();
}

function get_userid_massively($usernamepack){
    if(!empty($usernamepack)){
        $useridpack = array();
        $usernamepack = explode(",",$usernamepack);
        foreach($usernamepack as $username){
            if (fetch_userid_from_username($username) === false) {
                $useridpack[] = "false";
            } else {
                $useridpack[] = fetch_userid_from_username($username);
            }
        }
        return implode(",",$useridpack);
    }
}

function get_username_massively($useridpack){
    if(!empty($useridpack)){
        $usernamepack = array();
        $useridpack = explode(",",$useridpack);
        foreach($useridpack as $userid){
            if(($userinfo=fetch_userinfo($userid))===false)
                $usernamepack[]= "false";
            else
                $usernamepack[]= $userinfo['username'];
        }
        return implode(", ",$usernamepack);
    }
}

function LAST_INSERT_ID(){
    global $vbulletin;
    $result = $vbulletin->db->query_read("SELECT LAST_INSERT_ID()");
    $row = $vbulletin->db->fetch_row($result);
    return $row[0];
}

function reindex_array(array $unreindexed_array, array $order){
    foreach ( $order as $field ) {
        if (array_key_exists($field, $unreindexed_array)) {
            $reindexed_array[$field] = $unreindexed_array[$field];
        }
    }
    return $reindexed_array;
}

function is_assoc($array) {
    return (bool)count(array_filter(array_keys($array), 'is_string'));
}

function is_indexed($array) {
    $key=0;
    while (array_key_exists($key, $array)){
        if ($key == (count($array)-1)){
            return true;
        }
        $key++;
    }
    return false;
}

function update_array_to_array(array $array_to_be_update,array $destination_array){
    if(!is_assoc($array_to_be_update) && !is_assoc($destination_array)){
        if (is_indexed($array_to_be_update) && is_indexed($destination_array)) {
            foreach ($array_to_be_update as $updatevalue) {
                if (!in_array($updatevalue, $destination_array)) {
                    $destination_array[] = $updatevalue;
                }
            }
        } else{
            foreach ($array_to_be_update as $updatekey => $updatevalue) {
                if (!array_key_exists($updatekey, $destination_array)) {
                    $destination_array[$updatekey] = $updatevalue;
                } else{
                    $destination_array[$updatekey] += $updatevalue;
                }
            }
        }
    } else {
        foreach($array_to_be_update as $updatekey => $updatevalue){
                ${$updatekey."s"} = explode(",", $updatevalue);
                foreach(${$updatekey."s"} as $$updatekey){       
                    if(array_key_exists($updatekey, $destination_array)){
                        if (!in_array($$updatekey, explode(",",$destination_array[$updatekey]))) {
                            $destination_array[$updatekey].= ",".$$updatekey;      
                        }
                    } else{
                        $destination_array[$updatekey] = $array_to_be_update[$updatekey];
                    }
                }
        }    
    }
    
    return $destination_array;
}

function rebuildForum($parentid){
    global $vbulletin;
    $db = $vbulletin->db;
    $forums = $db->query_read("
		SELECT forumid
		FROM " . TABLE_PREFIX . "forum
		WHERE parentid = $parentid OR forumid = $parentid
		ORDER BY forumid"
	);


	while ($forum = $db->fetch_array($forums)) 	
            {
            build_forum_counters($forum['forumid'], true);
            vbflush();
            }

    // and finally rebuild the forumcache
    unset($forumarraycache, $vbulletin->forumcache);
    build_forum_permissions();
	
}

function removeqsvar($url, $varname) {
    list($urlpart, $qspart) = array_pad(explode('?', $url), 2, '');
    parse_str($qspart, $qsvars);
    unset($qsvars[$varname]);
    $newqs = http_build_query($qsvars);
    return $urlpart . '?' . $newqs;
}

function fetch_userid_from_username($username){
	global $vbulletin;
	if ($user = $vbulletin->db->query_first("SELECT userid FROM " . TABLE_PREFIX . "user WHERE username = '" . $vbulletin->db->escape_string(trim($username)) . "'"))
	{
		return $user['userid'];
	}
	else
	{
		return false;
	}
}

function extract_info_from_posturl($posturl){
    global $vbulletin, $vbphrase;
    $infopack = array();
    
    //postid
    $query = parse_url($posturl, PHP_URL_QUERY);
    parse_str($query, $params);
    if ($params['p'] === false) {
        return false;
    } else {
        $infopack['postid'] = $params['p'];
    }

    //userid
    $postinfo = $vbulletin->db->fetch_array($vbulletin->db->query_read("SELECT* FROM `".TABLE_PREFIX."post` WHERE `postid`='{$infopack['postid']}'"));
    if ($postinfo===false){
        return false;
    } else {
        $infopack['userid'] = $postinfo['userid'];
        $infopack['threadid'] = $postinfo['threadid'];
    }
    
    
    //headpostid
    $threadinfo = $vbulletin->db->fetch_array($vbulletin->db->query_read("SELECT* FROM `".TABLE_PREFIX."thread` WHERE `threadid`='{$infopack['threadid']}'"));
    $infopack['headpostid'] = $threadinfo['firstpostid'];
    
    //mangaid 
    $mangainfo = $vbulletin->db->fetch_array($vbulletin->db->query_read("SELECT* FROM `".TABLE_PREFIX."yrms_vietsubmanga_manga` WHERE `postid`='{$infopack['headpostid']}'"));
    $infopack['mangaid'] = $mangainfo['mangaid'];
    
    return $infopack;
    }

function getParam($paramName = ''){
    if (isset($_GET[$paramName])) {
        return $_GET[$paramName];
    } else {
        return '';
    }
}

function getPost($paramName = ''){
    if(empty($paramName)) {
        return $_POST;
    }
    if (isset($_POST[$paramName])) {
        return $_POST[$paramName];
    } else {
        return '';
    }
}

function isPost() {
    if (isset($_POST['submitted'])) {
        return true;
    } else {
        return false;
    }
}
?>
