<?php
class Manga{
    public $mangaid;
    public $awardid;
    public $threadid;
    public $oldpostid;
    public $postid;
    public $active = 1;
    public $illustration;
    public $mangatitle;
    public $othertitle;
    public $author;
    public $type;
    public $numberofchapter = 0;
    public $finishedchapter = 0;
    public $originalcomposition;
    public $genre;
    public $summary;
    public $fansubname;
    public $fansubmember;
    public $fansubsite;
    public $fansubnote;
    public $status = 2;
    public $numberofhost;
    public $hostname;
    public $readonlinestatus;
    public $action;
    
    function __construct($mangaid='') {
        if(!empty($mangaid)){
            $this->mangaid=$mangaid;
            $this->get();
        }
    }
    
    public function add(){
        global $vbulletin,$vbphrase;
        //analysis information
        if (empty($this->author)) {
            $this->author = $vbphrase['yrms_unknown'];
        }
        if (empty($this->fansubname)) {
            $this->fansubname = $vbphrase['yrms_unknown'];
        }
        if (empty($this->fansubsite)) {
            $this->fansubsite = $vbphrase['yrms_unknown'];
        }
        if ($this->type == 2) {
            $this->numberofchapter = 1;
        }
        $this->fansubmember=array(
            "uploader" => $vbulletin->userinfo['userid']
        );
        
        //set some information and make a new thread
        $newpost = $this->build_headpost();
        $idpack = newThread($newpost);
        $this->threadid = $idpack['threadid'];
        $this->postid = $idpack['postid'];
        
        $vbulletin->db->query_write("UPDATE `".TABLE_PREFIX."post` SET `yrmspost`=1 WHERE `postid`=$this->postid");
        
        //and add the manga to database     
        $vbulletin->db->query_write("INSERT INTO `".TABLE_PREFIX."yrms_vietsubmanga_manga`(`threadid`, `postid`, `illustration`, `mangatitle`, `othertitle`, `author`, `type`, `numberofchapter`, `originalcomposition`, `genre`, `summary`, `fansubname`, `fansubmember`, `fansubsite`, `fansubnote`, `status`, `numberofhost`, `hostname`) 
                                    VALUES ('$this->threadid','$this->postid','$this->illustration','$this->mangatitle','$this->othertitle','$this->author',$this->type,'$this->numberofchapter','$this->originalcomposition','$this->genre','$this->summary','$this->fansubname','".serialize($this->fansubmember)."','$this->fansubsite','$this->fansubnote','$this->status','$this->numberofhost','".serialize(array())."')"); 
        $this->mangaid=$vbulletin->db->insert_id();     
        
        // <editor-fold defaultstate="collapsed" desc=" reward ">
        $award = new Award;
        $award->postid = $this->postid;
        $award->awardcontent = array(
            "{$vbulletin->userinfo['userid']}" => $vbulletin->options['yrms_vietsubmanga_yun_newproject'],
        );
        $award->resourcetype = 'vietsubmanga';
        $award->resourceid = $this->mangaid;
        $award->resourceheadid = $this->mangaid;
        $award->add();  
        // </editor-fold>
        
        // <editor-fold defaultstate="collapsed" desc=" return the success message ">
        $return_message = construct_phrase($vbphrase['yrms_msg_success_newmanga'],
                                           $vbulletin->userinfo['username'],
                                           $this->mangatitle,
                                           $vbulletin->options['yrms_vietsubmanga_yun_newproject'],
                                           $this->mangaid);


        return $return_message;


// </editor-fold>       
    }
    
    public function build_headpost(){
        global $vbulletin, $vbphrase;
        //set id
        $headpost['forumid'] = $vbulletin->options['yrms_vietsubmanga_id_truyendich'];
        $headpost['threadid'] = $this->threadid;
        $headpost['postid'] = $this->postid;
        $headpost['prefixid'] = $vbulletin->options['yrms_vietsubmanga_prefixid_type'.$this->type];
        //thread title
        $headpost['title']="";
        if(!empty($this->fansubname))
            $headpost['title'] .= "[{$this->fansubname}] ";
        if($this->type==3 && !empty($this->originalcomposition))
            $headpost['title'] .= "[{$this->originalcomposition}] ";
            $headpost['title'] .= $this->mangatitle;
        
        if ($this->numberofchapter == 0)
            $numberofchapter = '??';
        else 
            $numberofchapter = $this->numberofchapter;
        //reformat fansubmember
        $linkformat = $this->build_linkformat();
        if($linkformat === false){
            $linkformat = $vbphrase['yrms_tobeupdated'];
        }
        $fansubmember = '[B]'.$vbphrase['yrms_translator'].':[/B] '.  get_username_massively($this->fansubmember['translator'])."\n";
        $fansubmember .= '[B]'.$vbphrase['yrms_proofreader'].':[/B] '.  get_username_massively($this->fansubmember['proofreader'])."\n";
        $fansubmember .= '[B]'.$vbphrase['yrms_mangaeditor'].':[/B] '.  get_username_massively($this->fansubmember['editor'])."\n";
        $fansubmember .= '[B]'.$vbphrase['yrms_qualitychecker'].':[/B] '.  get_username_massively($this->fansubmember['qualitychecker'])."\n";
        $fansubmember .= '[B]'.$vbphrase['yrms_uploader'].':[/B] '.  get_username_massively($this->fansubmember['uploader'])."\n";
        //build link format
        $headpost['pagetext']= construct_phrase($vbphrase['yrms_postformat_vietsubmanga'],
                               $this->illustration,
                               $this->mangatitle,
                               $this->author,
                               $this->genre,
                               $numberofchapter,
                               $vbphrase["yrms_projectstatus{$this->status}"],
                               $this->summary,
                               $this->fansubname,
                               $fansubmember,
                               $this->fansubsite,
                               $this->fansubnote,
                               $linkformat);
        //other setting
        $headpost['allowsmilie'] = 1;
        $headpost['visible'] = 1;
        $headpost['parseurl'] = 1;
        return $headpost;                       
    }
    
    public function build_linkformat(){
        global $vbulletin, $vbphrase;
        if($this->readonlinestatus==1){
            $head = "{$vbphrase['yrms_readonlinelink']}";
        } else{
            $head = "{$vbphrase['yrms_chaptername']}";
        }
        if(!empty($this->numberofhost)){
            $head .="|{colsp=$this->numberofhost}Download\n";
        } else{
            $head .="\n";
        }
        $body = array();
        $allchapters = array();
        $allchaptersid=$vbulletin->db->query_read("SELECT `chapterid` "
                                                . "FROM `".TABLE_PREFIX."yrms_vietsubmanga_chapter` "
                                                . "WHERE `mangaid` = '$this->mangaid' "
                                                . "ORDER BY `type`,`chapternumber` ASC ");                                       
        if($vbulletin->db->num_rows($allchaptersid)!=0){
            while ($chapteridpack = $vbulletin->db->fetch_array($allchaptersid)) {
                $allchapters[] = new Chapter($this, $chapteridpack['chapterid']);
            }
            
            foreach($allchapters as $chapter){
                $rows = array("readonline"=>"");
                if (!empty($chapter->numberofhost)) {
                    $rows += array_fill_keys(array_keys(array_flip($this->hostname)), "");
                }
                if($chapter->rate==1){
                    $rows = array("readonline"=>"");
                    if (!empty($chapter->numberofhost)) {
                        $rows[] = '{colsp=' . $this->numberofhost . '}[CENTER][URL="' . $vbulletin->options['bburl'] . "/" . fetch_seo_url('post', fetch_postinfo($chapter->postid)) . '"]Download[/URL][/CENTER] ';
                    }
                }
                else if(!empty($chapter->numberofhost)){
                    foreach($chapter->downloadlink as $hostname => $hostlinks){
                        if(count($hostlinks)>1){
                            $thishost=array();
                            $partnumber=1;
                            foreach($hostlinks as $hostlink){
                                $thishost[] = '[URL="'.$hostlink.'"]'.$hostname.' '.$partnumber.'[/URL]';
                                $partnumber++;
                            }
                            $thishost = '[CENTER]'.implode(" - ", $thishost).'[/CENTER] ';
                        } else{
                            $thishost = '[CENTER][URL="'.$hostlinks[0].'"]'.$hostname.'[/URL][/CENTER] ';
                        }
                        $rows[$hostname]=$thishost;
                    }
                }
                if (!empty($chapter->readonlinepostid)) {
                    $rows["readonline"] = '[CENTER][URL="' . $vbulletin->options['bburl']."/".fetch_seo_url('post', fetch_postinfo($chapter->readonlinepostid)) . '"]' . $vbphrase["yrms_chaptertype{$chapter->type}"] . " " . $chapter->chapternumber . '[/URL][/CENTER] ';
                } else{
                    $rows["readonline"] = '[CENTER]'.$vbphrase["yrms_chaptertype{$chapter->type}"] . ' ' . $chapter->chapternumber.'[/CENTER] ';
                }
                
                $rows = implode("|", $rows);
                $body[] = $rows;                 
            }
            //$head=rtrim($head, "|");
            $body = implode("\n", $body);
            $linkformat = '[TABLE="head"]'.$head."\n".$body.'[/TABLE]';
            return $linkformat;
        } else{
            return false;
        }
    }    
    
    public function update(){
        global $vbulletin,$vbphrase;
        if(empty($this->author))
                $this->author = $vbphrase['yrms_unknown'];
        if(empty($this->fansubname))
                $this->fansubname = $vbphrase['yrms_unknown'];
        if(empty($this->fansubsite))
                $this->fansubsite = $vbphrase['yrms_unknown'];
        if($this->type == 2)
            $this->numberofchapter = 1;
        

        //if postid was changed, update some information
        if($this->oldpostid!=$this->postid && !empty($this->postid)){
            $this->threadid=$vbulletin->db->fetch_array($vbulletin->db->query_read("SELECT `threadid` FROM `".TABLE_PREFIX."thread` WHERE `firstpostid`='$this->postid'"));
            $this->threadid = $this->threadid['threadid'];
            $vbulletin->db->query_write("UPDATE `".TABLE_PREFIX."post` 
                                         SET    `yrmspost`='0'
                                         WHERE  `postid`='$this->oldpostid'"); 
            $vbulletin->db->query_write("UPDATE `".TABLE_PREFIX."post` 
                                         SET    `yrmspost`='1'
                                         WHERE  `postid`='$this->postid'");
            $vbulletin->db->query_write("UPDATE `".TABLE_PREFIX."yrms_award` 
                                         SET    `postid`='$this->postid'
                                         WHERE  `postid`='$this->oldpostid'"); 
        }
        //update new information to database
        $vbulletin->db->query_write("UPDATE `".TABLE_PREFIX."yrms_vietsubmanga_manga` "
                                  . "SET     `awardid`='$this->awardid',"
                                          . "`threadid`='$this->threadid',"
                                          . "`postid`='$this->postid',"
                                          . "`active`='$this->active',"
                                          . "`illustration`='$this->illustration',"
                                          . "`mangatitle`='$this->mangatitle',"
                                          . "`othertitle`='$this->othertitle',"
                                          . "`author`='$this->author',"
                                          . "`type`='$this->type',"
                                          . "`finishedchapter`='$this->finishedchapter',"
                                          . "`numberofchapter`='$this->numberofchapter',"
                                          . "`originalcomposition`='$this->originalcomposition',"
                                          . "`genre`='$this->genre',"
                                          . "`summary`='$this->summary',"
                                          . "`fansubname`='$this->fansubname',"
                                          . "`fansubmember`='".serialize($this->fansubmember)."',"
                                          . "`fansubsite`='$this->fansubsite',"
                                          . "`fansubnote`='$this->fansubnote',"
                                          . "`status`='$this->status',"
                                          . "`numberofhost`='$this->numberofhost',"
                                          . "`hostname`='".serialize($this->hostname)."',"
                                          . "`readonlinestatus`='$this->readonlinestatus'"
                                  . "WHERE   `mangaid`=$this->mangaid");
        
        //and update the post in forum
        $edit = $this->build_headpost();
        editPost($edit);
         
    }
    
    public function get(){
        global $vbulletin;
        $mangainfo=$vbulletin->db->fetch_array($vbulletin->db->query_read("SELECT * FROM `".TABLE_PREFIX."yrms_vietsubmanga_manga` WHERE `mangaid`='$this->mangaid'"));
        $this->set($mangainfo);  
    }

    public function isfansubmember($userid=''){
        global $vbulletin;
        if (empty($userid)) {
            $userid = $vbulletin->userinfo['userid'];
        }
        if (!empty($this->fansubmember)) {
            foreach ($this->fansubmember as $fansubmembers) {
                if (in_array($userid, explode(',', $fansubmembers))) {
                    return true;
                }
            }
            return false;
        } else {
            return false;
        }
    }
    
    public function mangalist($filter='',$keyword=''){
        global $vbulletin, $vbphrase;
        $totalmangas=$vbulletin->db->query_read("SELECT * FROM `".TABLE_PREFIX."yrms_vietsubmanga_manga`");
        if(empty($_GET['page']))
            $page=1;
        else
            $page = $_GET['page'];
        if($page>ceil($vbulletin->db->num_rows($totalmangas)/20))
            $page = ceil($vbulletin->db->num_rows($totalmangas)/20);
        if(empty($keyword))
            $query = "SELECT * FROM `".TABLE_PREFIX."yrms_vietsubmanga_manga` LIMIT ".(20*($page-1)).",20";
        else if($filter=='mangatitle')
            $query = "SELECT * FROM `".TABLE_PREFIX."yrms_vietsubmanga_manga` WHERE `mangatitle` LIKE '%{$keyword}%' OR `othertitle` LIKE '%{$keyword}%' LIMIT ".(20*($page-1)).",20";
        else
            $query = "SELECT * FROM `".TABLE_PREFIX."yrms_vietsubmanga_manga` WHERE {$filter} LIKE '%{$keyword}%' LIMIT ".(20*($page-1)).",20";
        
        $mangalist = '';    
        $mangas = $vbulletin->db->query_read($query);
        
        $tableorder = 1;
        while($manga = $vbulletin->db->fetch_array($mangas)){
            $this->set($manga);
            
            $action = '<a href="yrms/vietsubmanga.php?do=chapterlist&mangaid='.$this->mangaid.'">'.$vbphrase['yrms_chaptermanage'].'</a><br/>
                       <a href="yrms/vietsubmanga.php?do=mangareport&mangaid='.$this->mangaid.'">'.$vbphrase['yrms_brokenlink_report'].'</a><br/>';
            if($this->isfansubmember()){
                $action = '<a href="yrms/vietsubmanga.php?do=mangaedit&mangaid='.$this->mangaid.'">'.$vbphrase['yrms_edit'].'</a><br/>'.$action;
            }
            if(can_moderate($vbulletin->options['yrms_vietsubmanga_id_truyendich']))
                    $action.= '<a href="yrms/vietsubmanga.php?do=delete&mangaid='.$this->mangaid.'">'.$vbphrase['yrms_delete'].'</a><br/>';                               
                                
            
            if($this->numberofchapter==0)
                $numberofchapter = '??';
            else 
                $numberofchapter = $this->numberofchapter;
            $mangadata = "<td>$this->mangaid</td>
                          <td style=\"text-align:left;\">
                          $this->mangatitle <img id=\"{$this->mangaid}toggle\" style=\"vertical-align: middle;margin-bottom:2px;\" height=\"18\" src=\"yrms/images/expand.png\" onclick=\"toggleOption({$this->mangaid})\"/>
                          <div id=\"{$this->mangaid}content\" style=\"display:none;\">$action</div>
                          </td>
                          <td>$this->author</td>
                          <td>$this->finishedchapter/$numberofchapter</td>
                          <td>$this->fansubname</td>
                          <td>{$vbphrase["yrms_projectstatus$this->status"]}</td>";
            if($tableorder%2==1)
                $mangadata = "<tr>$mangadata</tr>";
            else
                $mangadata = "<tr class=\"alt\">$mangadata</tr>";
            $mangalist .= $mangadata;
            $tableorder++;
        }
        if(!empty($mangalist))
            $mangalist='<div class="datagrid">
                    <table>
                     <thead>
                         <tr>
                             <th>'.$vbphrase['yrms_id'].'</th>
                             <th>'.$vbphrase['yrms_mangatitle'].'</th>
                             <th>'.$vbphrase['yrms_author'].'</th>
                             <th>'.$vbphrase['yrms_numberofchapter'].'</th>
                             <th>'.$vbphrase['yrms_fansub_name'].'</th>
                             <th>'.$vbphrase['yrms_projectstatus'].'</th>
                         </tr>
                     </thead>
                     <tbody>
                     '.$mangalist.'
                     </tbody>
                     <tfoot>
                        <tr>
                            <td colspan="6">
                                <div id="paging" style="padding-right:10px;">
                                    '.  construct_pagenavigation($page, 20, $vbulletin->db->num_rows($totalmangas), removeqsvar($_SERVER['REQUEST_URI'],page)).'
                                </div>
                            </td>  
                        </tr>
                     </tfoot>
                     </table>
                     </div>';
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
        return $mangalist;
        
    }
    
    public function myproject(){
        global $vbulletin, $vbphrase;
        $myprojects = '';    
        $mangas = $vbulletin->db->query_read("SELECT * FROM `".TABLE_PREFIX."yrms_vietsubmanga_manga`");
        
        if(empty($_GET['page']))
            $page=1;
        else
            $page = $_GET['page'];
        if($page>ceil($vbulletin->db->num_rows($mangas)/20))
            $page = ceil($vbulletin->db->num_rows($mangas)/20);
        
        $tableorder = 1;
        $myprojectnumber = 0;
        while($manga = $vbulletin->db->fetch_array($mangas)){
            $this->set($manga);
            
            if($this->isfansubmember()){
                $action = '<a href="yrms/vietsubmanga.php?do=mangaedit&mangaid='.$this->mangaid.'">'.$vbphrase['yrms_edit'].'</a><br/>
                           <a href="yrms/vietsubmanga.php?do=chapterlist&mangaid='.$this->mangaid.'">'.$vbphrase['yrms_chaptermanage'].'</a><br/>
                           <a href="yrms/vietsubmanga.php?do=mangareport&mangaid='.$this->mangaid.'">'.$vbphrase['yrms_brokenlink_report'].'</a><br/>';
                if(can_moderate($vbulletin->options['yrms_vietsubmanga_id_truyendich']))
                    $action.= '<a href="yrms/vietsubmanga.php?do=delete&mangaid='.$this->mangaid.'">'.$vbphrase['yrms_delete'].'</a><br/>';
                
                if($this->numberofchapter==0)
                    $numberofchapter = '??';
                else 
                    $numberofchapter = $this->numberofchapter;
                if($myprojectnumber>=(20*($page-1)) && $myprojectnumber<(20*$page)){
                $myproject = "<td>$this->mangaid</td>
                              <td style=\"text-align:left;\">
                              $this->mangatitle <img id=\"{$this->mangaid}toggle\" style=\"vertical-align: middle;margin-bottom:2px;\" height=\"18\" src=\"yrms/images/expand.png\" onclick=\"toggleOption({$this->mangaid})\"/>
                              <div id=\"{$this->mangaid}content\" style=\"display:none;\">$action</div>
                              </td>
                              <td>$this->author</td>
                              <td>$this->finishedchapter/$numberofchapter</td>
                              <td>$this->fansubname</td>
                              <td>{$vbphrase["yrms_projectstatus$this->status"]}</td>";
                if($tableorder%2==1)
                    $myproject = "<tr>$myproject</tr>";
                else
                    $myproject = "<tr class=\"alt\">$myproject</tr>";
                $myprojects .= $myproject;
                $tableorder++;
                }
                
                $myprojectnumber++;
            }
        }
        if(!empty($myprojects))
            $myprojects='<div class="datagrid">
                        <table>
                         <thead>
                             <tr>
                                 <th>'.$vbphrase['yrms_id'].'</th>
                                 <th>'.$vbphrase['yrms_mangatitle'].'</th>
                                 <th>'.$vbphrase['yrms_author'].'</th>
                                 <th>'.$vbphrase['yrms_numberofchapter'].'</th>
                                 <th>'.$vbphrase['yrms_fansub_name'].'</th>
                                 <th>'.$vbphrase['yrms_projectstatus'].'</th>
                             </tr>
                         </thead>
                         <tbody>
                         '.$myprojects.'
                         </tbody>
                         <tfoot>
                            <tr>
                                <td colspan="6">
                                    <div id="paging" style="padding-right:10px;">
                                        '.  construct_pagenavigation($page, 20, $vbulletin->db->num_rows($mangas), removeqsvar($_SERVER['REQUEST_URI'],page)).'
                                    </div>
                                </td>  
                            </tr>
                         </tfoot>
                         </table>
                         </div>';
        else{
            $messagetype = "error";
            $message= construct_phrase($vbphrase['yrms_msg_error_emptylist'],$vbphrase['yrms_manga'],$vbphrase['yrms_mangaadd']);
            $messagebox = vB_Template::create('yrms_messagebox');
            $messagebox->register('messagetype', $messagetype);
            $messagebox->register('message', $message);
            $myprojects=$messagebox->render();
        }
            return $myprojects;   
           
    }
    
    public function set($mangainfo){
         $this->mangaid = $mangainfo['mangaid']; 
         $this->awardid = $mangainfo['awardid']; 
         $this->threadid = $mangainfo['threadid']; 
         $this->oldpostid = $mangainfo['postid'];
         $this->postid = $mangainfo['postid']; 
         $this->active = $mangainfo['active']; 
         $this->illustration = $mangainfo['illustration']; 
         $this->mangatitle = $mangainfo['mangatitle']; 
         $this->othertitle = $mangainfo['othertitle']; 
         $this->author = $mangainfo['author']; 
         $this->type = $mangainfo['type']; 
         $this->numberofchapter = $mangainfo['numberofchapter'];
         $this->finishedchapter = $mangainfo['finishedchapter'];
         $this->originalcomposition = $mangainfo['originalcomposition']; 
         $this->genre = $mangainfo['genre']; 
         $this->summary = $mangainfo['summary']; 
         $this->fansubname = $mangainfo['fansubname']; 
         $this->fansubmember = unserialize($mangainfo['fansubmember']); 
         $this->fansubsite = $mangainfo['fansubsite']; 
         $this->fansubnote = $mangainfo['fansubnote']; 
         $this->status = $mangainfo['status'];
         $this->numberofhost = $mangainfo['numberofhost']; 
         $this->hostname = unserialize($mangainfo['hostname']);
         $this->readonlinestatus = $mangainfo['readonlinestatus'];
    }
    
    public function reward(){
        global $vbulletin,$vbphrase;
        //analysis information
        if(empty($this->author))
                $this->author = $vbphrase['yrms_unknown'];
        if(empty($this->fansubname))
                $this->fansubname = $vbphrase['yrms_unknown'];
        if(empty($this->fansubsite))
                $this->fansubsite = $vbphrase['yrms_unknown'];
        if($this->type == 2)
            $this->numberofchapter = 1;
        
        //get some misc information
        $this->threadid=$vbulletin->db->fetch_array($vbulletin->db->query_read("SELECT `threadid` FROM `".TABLE_PREFIX."thread` WHERE `firstpostid`='$this->postid'"));
        $this->threadid=$this->threadid['threadid'];
        
        $userid=$vbulletin->db->fetch_array($vbulletin->db->query_read("SELECT `userid` FROM `".TABLE_PREFIX."post` WHERE `postid`='$this->postid'"));
        $userid=$userid['userid'];
        $this->fansubmember=array(
            $vbphrase['yrms_uploader'] => $userid
        );
        
        //make the post became yrmspost
        $vbulletin->db->query_write("UPDATE `".TABLE_PREFIX."post` SET `yrmspost`=1 WHERE `postid`=$this->postid");
        
        //reward the poster
        $award = new Award;
        $award->postid = $this->postid;
        $award->awardcontent = array(
            "{$userid}" => $vbulletin->options['yrms_vietsubmanga_yun_newproject'],
        );
        $award->awardtype = 'vietsubmanga_newproject';
        $award->add();
        
        //and add the manga to database     
        $vbulletin->db->query_write("INSERT INTO `".TABLE_PREFIX."yrms_vietsubmanga_manga`(`awardid`, `threadid`, `postid`, `illustration`, `mangatitle`, `othertitle`, `author`, `type`, `numberofchapter`, `originalcomposition`, `genre`, `summary`, `fansubname`, `fansubmember`, `fansubsite`, `fansubnote`, `status`, `numberofhost`) 
                                    VALUES ('$award->awardid','$this->threadid','$this->postid','$this->illustration','$this->mangatitle','$this->othertitle','$this->author',$this->type,'$this->numberofchapter','$this->originalcomposition','$this->genre','$this->summary','$this->fansubname','".serialize($this->fansubmember)."','$this->fansubsite','$this->fansubnote','$this->status','$this->numberofhost')");         
    }
    
    public function validate(){
        global $vbulletin, $vbphrase;
        $error = "";
        //check postid
        if(isset($this->postid)){
            if(empty($this->postid))
                $error.= construct_phrase($vbphrase['yrms_msg_error_blankfield'],$vbphrase['yrms_postid'])."\n";
            else if($vbulletin->db->num_rows($vbulletin->db->query_read("SELECT* FROM `".TABLE_PREFIX."thread` WHERE `firstpostid`='$this->postid'"))==0)
                $error.= construct_phrase($vbphrase['yrms_msg_error_postid'],$vbphrase['yrms_postid'])."\n";
            
        }
        //check illustration
        if(strpos($this->illustration, "http://")===false && strpos($this->illustration, "https://")===false){
            $error.= construct_phrase($vbphrase['yrms_msg_error_invalidimagelink'],$vbphrase['yrms_illustration'])."\n";
        }
        //check mangatitle
        if($this->mangatitle==""){
            $error.= construct_phrase($vbphrase['yrms_msg_error_blankfield'],$vbphrase['yrms_mangatitle'])."\n";
        }
        //check othername
        if($this->othertitle!=""){
            $othertitles = explode(',', $this->othertitle);
            foreach($othertitles as $othertitle){
                if($othertitle==$this->mangatitle){
                    $error.= construct_phrase($vbphrase['yrms_msg_error_othertitle'],$vbphrase['yrms_othertitle'],$vbphrase['yrms_mangatitle'])."\n";
                    break;
                }
            }            
        }

        //check author
        if($this->author == '' && $this->action == 'add') {
            $error.= construct_phrase($vbphrase['yrms_msg_error_blankfield'],$vbphrase['yrms_author'])."\n";
        }

        //check type
        if($this->type==""){
            $error.= construct_phrase($vbphrase['yrms_msg_error_blankselect'],$vbphrase['yrms_type'])."\n";
        }

        //check original composition
        if($this->type == 3 && $this)

        //check genre
        if($this->genre==""){
            $error.= construct_phrase($vbphrase['yrms_msg_error_blankfield'],$vbphrase['yrms_genre'])."\n";
        }
        //check summary
        if($this->summary==""){
            $error.= construct_phrase($vbphrase['yrms_msg_error_blankfield'],$vbphrase['yrms_summary'])."\n";
        }
        return $error;
    }   
}

class Chapter{
    public $manga;
    
    public $chapterid;
    public $postid;
    public $readonlinepostid;
    public $active;
    public $status;
    public $type;
    public $chapternumber;
    public $chaptertitle;
    public $rate;
    public $numberofhost;
    public $downloadlink;
    public $onlinelink;
    public $fansubmember;
    public $fansubnote;
    
    public $poster;
    public $readonlineposter;

    function __construct(Manga $manga, $chapterid='') {
        $this->manga=$manga; 
        if (!empty($chapterid)) {
            $this->chapterid = $chapterid;
            $this->get();
        }
    }
    
    public function add(){
        global $vbulletin,$vbphrase;
        // <editor-fold defaultstate="collapsed" desc="analysis information">
        if ($this->type == 2) {
            $this->chapternumber = "";
        }
        $this->fansubmember = str_replace('false', '', $this->fansubmember);
        $this->fansubmember = str_replace(',,', '', $this->fansubmember);
        // </editor-fold>
        
        // <editor-fold defaultstate="collapsed" desc="make new update post, and new read online thread">
        $downloadpost = $this->buildpost('download');
        $this->postid = newPost($downloadpost);

        if (!empty($this->onlinelink)) {
            $onlinepost = $this->buildpost('online');
            $idpack = newThread($onlinepost);
            $this->readonlinepostid = $idpack['postid'];
        }
        //set yrms type for new posts
        $vbulletin->db->query_write("UPDATE `" . TABLE_PREFIX . "post` "
                . "SET `yrmspost`=1 "
                . "WHERE `postid` = '$this->postid'");
        $vbulletin->db->query_write("UPDATE `" . TABLE_PREFIX . "post` "
                . "SET `yrmspost`=1 "
                . "WHERE `postid` = '$this->readonlinepostid'");
        // </editor-fold>          
          
        // <editor-fold defaultstate="collapsed" desc="add chapter to the database ">
        $vbulletin->db->query_write("INSERT INTO `" .TABLE_PREFIX. "yrms_vietsubmanga_chapter`"
                . "(`mangaid`,"
                . "`postid`, "
                . "`readonlinepostid`, "
                . "`active`, "
                . "`status`, "
                . "`type`, "
                . "`chapternumber`, "
                . "`chaptertitle`, "
                . "`rate`, "
                . "`numberofhost`, "
                . "`downloadlink`, "
                . "`onlinelink`, "
                . "`fansubmember`, "
                . "`fansubnote`) "
        . "VALUES ('{$this->manga->mangaid}',"
                . "'$this->postid',"
                . "'$this->readonlinepostid',"
                . "'1',"
                . "'$this->status',"
                . "'$this->type',"
                . "'$this->chapternumber',"
                . "'$this->chaptertitle',"
                . "'$this->rate',"
                . "'$this->numberofhost',"
                . "'".serialize($this->downloadlink)."',"
                . "'$this->onlinelink',"
                . "'".serialize($this->fansubmember)."',"
                . "'$this->fansubnote')");
        $this->chapterid = $vbulletin->db->insert_id();
// </editor-fold>

        // <editor-fold defaultstate="collapsed" desc="update manga">
        $this->manga->fansubmember = update_array_to_array($this->fansubmember, $this->manga->fansubmember);
        $this->manga->fansubmember = reindex_array($this->manga->fansubmember, array("translator","proofreader","editor","qualitychecker","uploader"));
        if ($this->type == 1 || $this->type ==2) {
            $this->manga->finishedchapter++;
        }
        if(!empty($this->onlinelink)){
            $this->manga->readonlinestatus = 1;
        }
        
        $chapterhostname=array();
        foreach($this->downloadlink as $hostname => $hostlink){
            $chapterhostname[] = $hostname;
        }
        $this->manga->hostname = update_array_to_array($chapterhostname, $this->manga->hostname);
        
        $this->manga->numberofhost = count($this->manga->hostname);
        $this->manga->update();
        
        // </editor-fold>
        
        // <editor-fold defaultstate="collapsed" desc=" reward ">
        //For download post
        $award_download = new Award;
        $award_download->postid = $this->postid;
        if (strpos(strtolower($this->manga->fansubsite),'yurivn')) {
            $award_download->awardcontent = $this->build_awardcontent_fansubmember();
        } else{
            if (array_key_exists($vbulletin->userinfo['userid'], $award_download->awardcontent)) {
                $award_download->awardcontent[$vbulletin->userinfo['userid']] += $vbulletin->options['yrms_vietsubmanga_yun_uploader'];
            } else {
                $award_download->awardcontent += array($vbulletin->userinfo['userid'] => $vbulletin->options['yrms_vietsubmanga_yun_uploader']);
            }
        }
        
        if ($this->numberofhost>=2){
            if (array_key_exists($vbulletin->userinfo['userid'], $award_download->awardcontent)) {
                $award_download->awardcontent[$vbulletin->userinfo['userid']] += $vbulletin->options['yrms_vietsubmanga_yun_mirror'];
            } else {
                $award_download->awardcontent += array($vbulletin->userinfo['userid'] => $vbulletin->options['yrms_vietsubmanga_yun_mirror']);
            }
        }
        var_dump($award_download->awardcontent);
        $award_download->resourcetype = 'vietsubmanga';
        $award_download->resourceid = $this->chapterid;
        $award_download->resourceheadid = $this->manga->mangaid;
        $award_download->add();

        //For readonline post
        if(!empty($this->readonlinepostid)){
            $award_online = new Award;
            $award_online->postid = $this->readonlinepostid;
            $award_online->awardcontent = array($vbulletin->userinfo['userid'] => $vbulletin->options['yrms_vietsubmanga_yun_online']);
            $award_online->resourcetype = 'vietsubmanga';
            $award_online->resourceid = $this->chapterid;
            $award_online->resourceheadid = $this->manga->mangaid;
            $award_online->add();
        }
        // </editor-fold>
        
        // <editor-fold defaultstate="collapsed" desc=" return the success message ">
        if (isset($award_online)) {
            $awardcontent = update_array_to_array($award_online->awardcontent, $award_download->awardcontent);
        } 
        else {
            $awardcontent = $award_download->awardcontent;
        }

        $awardinfo = array();
        foreach ($awardcontent as $userid => $amount) {
            $userinfo = fetch_userinfo($userid);
            $username = $userinfo["username"];
            $awardinfo[] = "{$username}: {$amount} {$vbulletin->options['yrms_main_moneyname']}";
        }
        $awardinfo = implode("\n", $awardinfo);


        $return_message = construct_phrase($vbphrase['yrms_msg_success_newchapter'],                                            $vbulletin->userinfo['username'], 
                                           $vbphrase["yrms_chaptertype{$this->type}"] . " " . $this->chapternumber,                                            $this->manga->mangatitle,                                            nl2br($awardinfo));


        return $return_message;


// </editor-fold>                                         
    }
    
    public function reward(){
        global $vbulletin,$vbphrase;
        // <editor-fold defaultstate="collapsed" desc="analysis information">
        if ($this->type == 2) {
            $this->chapternumber = "";
        }
        $this->fansubmember = str_replace('false', '', $this->fansubmember);
        $this->fansubmember = str_replace(',,', '', $this->fansubmember);
        if ($this->postid == 0){
            $this->postid = $this->manga->postid;
        }
        // </editor-fold>
        
        // <editor-fold defaultstate="collapsed" desc="create new update post, and new read online thread">
        // <editor-fold defaultstate="collapsed" desc=" create new update post ">
        //normal case: there is update post for the chapter, or there is not but no 18+ content.
        //reward and add chapter to the database only, no need to make any new post. Only set the post to yrmspost.
        if ($this->postid != $this->manga->postid){
            $vbulletin->db->query_write("UPDATE `" . TABLE_PREFIX . "post` "
                                      . "SET `yrmspost`=1 "
                                      . "WHERE `postid` = '$this->postid'");
            if($this->rate==1 && !empty($this->numberofhost)){
                $downloadpost = $this->buildpost('download');
                editPost($downloadpost);
            }
        }
        
        //abnormal case 1: the dumb poster didn't make update post and this chapter is 18+ content
        //we will create a new update post for her
        else if ($this->postid == $this->manga->postid && $this->rate == 1) {
            $downloadpost = $this->buildpost('download');
            $this->postid = newPost($downloadpost, $this->poster);
            $vbulletin->db->query_write("UPDATE `" . TABLE_PREFIX . "post` "
                                      . "SET `yrmspost`=1 "
                                      . "WHERE `postid` = '$this->postid'");
        }


        //abnormal case 2: no download link, only readonline link.
        //Skip this step
        // </editor-fold>

        // <editor-fold defaultstate="collapsed" desc=" create new read online thread ">
        //normal case: readonline post is a separate topic
        //do nothing but set the post to yrms post, and reformat it if the chapter is 18+ content
        if (!empty($this->onlinelink)) {
            $vbulletin->db->query_write("UPDATE `" . TABLE_PREFIX . "post` "
                                      . "SET `yrmspost`=1 "
                                      . "WHERE `postid` = '$this->readonlinepostid'");
            if($this->rate==1){
                $readonlinepost = $this->buildpost('online');
                editPost($readonlinepost);
            }
        }
        
        //abnormal case: readonline post is the same as update post, or manga post
        //turn it into normal case and treat as normal case
        if (!empty($this->onlinelink) && ($this->readonlinepostid == $this->postid || $this->readonlinepostid == $this->manga->postid || $this->readonlinepostid == 0)) {
            $readonlinepost = $this->buildpost('online');
            $idpack = newThread($readonlinepost,  $this->readonlineposter);
            $this->readonlinepostid = $idpack['postid'];
            $vbulletin->db->query_write("UPDATE `" . TABLE_PREFIX . "post` "
                                      . "SET `yrmspost`=1 "
                                      . "WHERE `postid` = '$this->readonlinepostid'");
        }
        // </editor-fold>
        // </editor-fold>          
            
        // <editor-fold defaultstate="collapsed" desc="add chapter to the database ">
        $vbulletin->db->query_write("INSERT INTO `" .TABLE_PREFIX. "yrms_vietsubmanga_chapter`"
                . "(`mangaid`,"
                . "`postid`, "
                . "`readonlinepostid`, "
                . "`active`, "
                . "`status`, "
                . "`type`, "
                . "`chapternumber`, "
                . "`chaptertitle`, "
                . "`rate`, "
                . "`numberofhost`, "
                . "`downloadlink`, "
                . "`onlinelink`, "
                . "`fansubmember`, "
                . "`fansubnote`) "
        . "VALUES ('{$this->manga->mangaid}',"
                . "'$this->postid',"
                . "'$this->readonlinepostid',"
                . "'1',"
                . "'$this->status',"
                . "'$this->type',"
                . "'$this->chapternumber',"
                . "'$this->chaptertitle',"
                . "'$this->rate',"
                . "'$this->numberofhost',"
                . "'".serialize($this->downloadlink)."',"
                . "'$this->onlinelink',"
                . "'".serialize($this->fansubmember)."',"
                . "'$this->fansubnote')");
        $this->chapterid = $vbulletin->db->insert_id();
// </editor-fold>

        // <editor-fold defaultstate="collapsed" desc="update manga">
        $this->manga->fansubmember = update_array_to_array($this->fansubmember, $this->manga->fansubmember);
        $this->manga->fansubmember = reindex_array($this->manga->fansubmember, array("translator","proofreader","editor","qualitychecker","uploader"));
        if ($this->type == 1 || $this->type ==2) {
            $this->manga->finishedchapter++;
        }
        if(!empty($this->onlinelink)){
            $this->manga->readonlinestatus = 1;
        }
        
        $chapterhostname=array();
        if(!empty($this->numberofhost)){
            foreach($this->downloadlink as $hostname => $hostlink){
                $chapterhostname[] = $hostname;
            }
            $this->manga->hostname = update_array_to_array($chapterhostname, $this->manga->hostname);

            $this->manga->numberofhost = count($this->manga->hostname);
        }
        $this->manga->update();
        
        // </editor-fold>
        
        // <editor-fold defaultstate="collapsed" desc=" reward ">
        //For download post
        $award_download = new Award;
        $award_download->postid = $this->postid;
        if (strpos(strtolower($this->manga->fansubsite),'yurivn')) {
            $award_download->awardcontent = $this->build_awardcontent_fansubmember();
        }
        if(!empty($this->numberofhost)){
            if (array_key_exists($this->poster, $award_download->awardcontent)) {
                $award_download->awardcontent[$this->poster] += $vbulletin->options['yrms_vietsubmanga_yun_uploader'];
            } else {
                $award_download->awardcontent += array($this->poster => $vbulletin->options['yrms_vietsubmanga_yun_uploader']);
            }
            if ($this->numberofhost >=2){
                if (array_key_exists($this->poster, $award_download->awardcontent)) {
                    $award_download->awardcontent[$this->poster] += $vbulletin->options['yrms_vietsubmanga_yun_mirror'];
                } else {
                    $award_download->awardcontent += array($this->poster => $vbulletin->options['yrms_vietsubmanga_yun_mirror']);
                }
            }    
        }
        
        $award_download->resourcetype = 'vietsubmanga';
        $award_download->resourceid = $this->chapterid;
        $award_download->resourceheadid = $this->manga->mangaid;
        $award_download->add();

        //For readonline post
        if(!empty($this->readonlinepostid)){
            $award_online = new Award;
            $award_online->postid = $this->readonlinepostid;
            $award_online->awardcontent = array($this->readonlineposter => $vbulletin->options['yrms_vietsubmanga_yun_online']);
            $award_online->resourcetype = 'vietsubmanga';
            $award_online->resourceid = $this->chapterid;
            $award_online->resourceheadid = $this->manga->mangaid;
            $award_online->add();
        }
        // </editor-fold>
        
        // <editor-fold defaultstate="collapsed" desc=" return the success message ">
        if (isset($award_online)) {
            $awardcontent = $award_download->awardcontent + $award_online->awardcontent;
        } 
        else {
            $awardcontent = $award_download->awardcontent;
        }

        $awardinfo = array();
        foreach ($awardcontent as $userid => $amount) {
            $userinfo = fetch_userinfo($userid);
            $username = $userinfo["username"];
            $awardinfo[] = "{$username}: {$amount} {$vbulletin->options['yrms_main_moneyname']}";
        }
        $awardinfo = implode("\n", $awardinfo);


        $return_message = construct_phrase($vbphrase['yrms_msg_success_rewardchapter'],
                                           $vbulletin->userinfo['username'], 
                                           $vbphrase["yrms_chaptertype{$this->type}"] . " " . $this->chapternumber, 
                                           $this->manga->mangatitle,                                            
                                           nl2br($awardinfo));


        return $return_message;


// </editor-fold>                                         
    }
    
    public function get(){
        global $vbulletin;
        $chapterinfo=$vbulletin->db->fetch_array($vbulletin->db->query_read("SELECT * FROM `".TABLE_PREFIX."yrms_vietsubmanga_chapter` WHERE `chapterid`='$this->chapterid'"));
        $this->set($chapterinfo);  
    }
    
    public function set($chapterinfo){
        $this->chapterid = $chapterinfo['chapterid'];
        $this->postid = $chapterinfo['postid'];
        $this->readonlinepostid = $chapterinfo['readonlinepostid'];
        $this->active = $chapterinfo['active'];
        $this->status = $chapterinfo['status'];
        $this->type = $chapterinfo['type'];
        if ($chapterinfo['chapternumber'] == 0) {
            $this->chapternumber = "";
        } else{
            $this->chapternumber = $chapterinfo['chapternumber'];
        }
        $this->chaptertitle = $chapterinfo['chaptertitle'];
        $this->rate = $chapterinfo['rate'];
        $this->numberofhost = $chapterinfo['numberofhost'];
        $this->downloadlink = unserialize($chapterinfo['downloadlink']);
        $this->onlinelink = $chapterinfo['onlinelink'];
        $this->fansubmember = unserialize($chapterinfo['fansubmember']);
        $this->fansubnote = $chapterinfo['fansubnote'];
    }
    
    public function chapterlist(){
        global $vbulletin, $vbphrase;
        $totalchapters=$vbulletin->db->query_read("SELECT * FROM `".TABLE_PREFIX."yrms_vietsubmanga_chapter` WHERE `mangaid`='{$this->manga->mangaid}'");
        if (empty($_GET['page'])) {
            $page = 1;
        } else {
            $page = $_GET['page'];
        }
        if ($page > ceil($vbulletin->db->num_rows($totalchapters) / 20)) {
            $page = ceil($vbulletin->db->num_rows($totalchapters) / 20);
        }
        if($page<1){
            $page=1;
        }
        
        $query = "SELECT * FROM `".TABLE_PREFIX."yrms_vietsubmanga_chapter` WHERE `mangaid`='{$this->manga->mangaid}' LIMIT ".(20*($page-1)).",20";
        $chapterlist = '';    
        $chapters = $vbulletin->db->query_read($query);
        
        $tableorder = 1;
        while($chapter = $vbulletin->db->fetch_array($chapters)){
            $this->set($chapter);
            $action = '';
            
            // <editor-fold defaultstate="collapsed" desc=" Analyze information ">
            if ($this->chapternumber==0) {
                $chapternumber = '';
            } else {
                $chapternumber = $this->chapternumber;
            }
            
            if(empty($this->chaptertitle)){
                $chaptertitle='';
            } else{
                $chaptertitle = " - ".$this->chaptertitle;
            }
            
            $linkstatus = 1;
            if ($this->numberofhost > 1) {
                $mirrorstatus = 1;
            } else {
                $mirrorstatus = 0;
                $action.='<a href="yrms/vietsubmanga.php?do=chaptermirrorlinkadd&chapterid='.$this->chapterid.'">'.$vbphrase['yrms_mirrorlinkadd'].'</a><br/>';
                if (!empty($this->numberofhost)) {
                    $linkstatus = 1;
                    $action .= '<a href="yrms/vietsubmanga.php?do=chapterreport&chapterid='.$this->chapterid.'">'.$vbphrase['yrms_brokenlink_report'].'</a><br/>';
                } else{
                    $linkstatus = 0;
                }
            }
            if (!empty($this->onlinelink)){
                $onlinestatus = 1;
            } else {
                $onlinestatus = 0;
                $action.='<a href="yrms/vietsubmanga.php?do=chaptermirrorlinkadd&chapterid='.$this->chapterid.'">'.$vbphrase['yrms_readonlinelinkadd'].'</a><br/>';
            }
            // </editor-fold>
            
            if($this->manga->isfansubmember()){
                $action = '<a href="yrms/vietsubmanga.php?do=chapteredit&chapterid='.$this->chapterid.'">'.$vbphrase['yrms_edit'].'</a><br/>'.$action;
            }
            if(can_moderate($vbulletin->options['yrms_vietsubmanga_id_truyendich'])){
                
            }
                    $action.= '<a href="yrms/vietsubmanga.php?do=delete&chapterid='.$this->chapterid.'">'.$vbphrase['yrms_delete'].'</a><br/>';                               
            
            

            $chapterdata = "<td style=\"text-align:left;\">
                          {$vbphrase["yrms_chaptertype{$this->type}"]} $chapternumber $chaptertitle <img id=\"{$this->chapterid}toggle\" style=\"vertical-align: middle;margin-bottom:2px;\" height=\"18\" src=\"yrms/images/expand.png\" onclick=\"toggleOption({$this->chapterid})\"/>
                          <div id=\"{$this->chapterid}content\" style=\"display:none;\">$action</div>
                          </td>
                          <td>{$vbphrase["yrms_availablestatus{$mirrorstatus}"]}</td>
                          <td>{$vbphrase["yrms_availablestatus{$onlinestatus}"]}</td>
                          <td>{$vbphrase["yrms_linkstatus{$linkstatus}"]}";
            if($tableorder%2==1)
                $chapterdata = "<tr>$chapterdata</tr>";
            else
                $chapterdata = "<tr class=\"alt\">$chapterdata</tr>";
            $chapterlist .= $chapterdata;
            $tableorder++;
        }
        if(!empty($chapterlist))
            $chapterlist='<div class="datagrid">
                    <table>
                     <thead>
                         <tr>
                             <th>'.$vbphrase['yrms_chaptername'].'</th>
                             <th>'.$vbphrase['yrms_mirrorlink'].'</th>
                             <th>'.$vbphrase['yrms_readonlinelink'].'</th>
                             <th>'.$vbphrase['yrms_linkstatus'].'</th>
                         </tr>
                     </thead>
                     <tbody>
                     '.$chapterlist.'
                     </tbody>
                     <tfoot>
                        <tr>
                            <td colspan="6">
                                <div id="paging" style="padding-right:10px;">
                                    '.  construct_pagenavigation($page, 20, $vbulletin->db->num_rows($totalchapters), removeqsvar($_SERVER['REQUEST_URI'],page)).'
                                </div>
                            </td>  
                        </tr>
                     </tfoot>
                     </table>
                     </div>';
        else{
            $messagetype = "error";
            $message= construct_phrase($vbphrase['yrms_msg_error_emptylist'],$vbphrase['yrms_chaptername'],$vbphrase['yrms_chapteradd']);
            $messagebox = vB_Template::create('yrms_messagebox');
            $messagebox->register('messagetype', $messagetype);
            $messagebox->register('message', $message);
            $chapterlist=$messagebox->render();
        }
        return $chapterlist;
        
    }
    
    public function build_awardcontent_fansubmember(){
        global $vbulletin;
        $awardcontent = array();
        foreach($this->fansubmember as $position => $members){
            ${$position."s"} = explode(",", $members);
            foreach(${$position."s"} as $$position){
                $member = $$position;
                $amount = ceil($vbulletin->options['yrms_vietsubmanga_yun_'.$position]/count(${$position."s"}));
                if (array_key_exists($member, $awardcontent)) {
                    $awardcontent[$member] += $amount;
                } else {
                    $awardcontent += array($member => $amount);
                }
            }
        }
        return $awardcontent;
    }
    
    public function buildpost($type){
        global $vbulletin, $vbphrase;
        //set id
        if($type=='download'){
            $post['forumid'] = $vbulletin->options['yrms_vietsubmanga_id_truyendich'];
            $post['parentid'] = $this->manga->postid;
            $post['threadid'] = $this->manga->threadid;
            $post['postid'] = $this->postid;
        }
        else if($type=='online'){
            $post['forumid'] = $vbulletin->options['yrms_vietsubmanga_id_doconline'];
            $post['threadid'] = extract_threadid_from_url(fetch_seo_url('post', fetch_postinfo($this->readonlinepostid)));
            $post['postid'] = $this->readonlinepostid;
        }
        
        //title
        if($this->manga->type==2 && $this->type==1)
            $post['title']="{$this->manga->mangatitle}";
        else    
            $post['title']="{$this->manga->mangatitle} {$vbphrase["yrms_chaptertype{$this->type}"]} {$this->chapternumber}";
        if(!empty($this->chaptertitle))
            $post['title'].="- {$this->chaptertitle}";        
        
        //other setting
        $post['allowsmilie'] = 1;
        $post['visible'] = 1;
        $post['parseurl'] = 1;
        
        if($type=='download'){       
            if ($this->rate == 1) {
                $post['pagetext'] = construct_phrase($vbphrase['yrms_postformat_updatehide'], $post['title'], $vbphrase['yrms_fansub_note'].": ".$this->fansubnote, $this->build_linkformat());
            } else {
                $post['pagetext'] = construct_phrase($vbphrase['yrms_postformat_update'], $post['title'], $vbphrase['yrms_fansub_note'].": ".$this->fansubnote);
            }
        }
        else if ($type == 'online') {
            if ($this->rate == 1) {
                $post['pagetext'] = construct_phrase($vbphrase['yrms_postformat_readonlinehide'], $post['title'], $vbphrase['yrms_fansub_note'].": ".$this->fansubnote, $this->onlinelink);
            } else{
                $post['pagetext'] = construct_phrase($vbphrase['yrms_postformat_readonline'], $post['title'], $vbphrase['yrms_fansub_note'].": ".$this->fansubnote, $this->onlinelink);
            }
        }

        return $post;      
    }
    
    public function build_linkformat(){
        $head = "{colsp=$this->numberofhost}Download\n";
        $body = array();
        foreach($this->downloadlink as $hostname => $hostlinks){
            if(count($hostlinks)>1){
                $thishost=array();
                $partnumber=1;
                foreach($hostlinks as $hostlink){
                    $thishost[] = '[URL="'.$hostlink.'"]'.$hostname.' Part'.$partnumber.'[/URL]';
                    $partnumber++;
                }
                $thishost = implode(" - ", $thishost);
            }
            else{
                $thishost = '[URL="'.$hostlinks[1].'"]'.$hostname.'[/URL]';
            }
            $body[]=$thishost;
        }
        //$head=rtrim($head, "|");
        $body = implode("|", $body);
        $linkformat = '[TABLE="head"]'.$head."\n".$body.'[/TABLE]';
        return $linkformat;
    }
    
    public function validate(){
        global $vbulletin, $vbphrase;
        $error = "";
        
        // <editor-fold defaultstate="collapsed" desc=" for reward only ">
        if ($_POST['submitted'] == $vbphrase['yrms_newchapter_award']) {
            //check postid
            if (isset($this->postid)) {
                if ($vbulletin->db->num_rows($vbulletin->db->query_read("SELECT* FROM `" . TABLE_PREFIX . "post` WHERE `postid`='$this->postid'")) == 0) {
                    $error.= construct_phrase($vbphrase['yrms_msg_error_postid'], $vbphrase['yrms_posturl']) . "\n";
                }
            }
        }

        // </editor-fold>
        
        // <editor-fold defaultstate="collapsed" desc=" for the rests">
        else{
            //check download link
            if (empty($this->numberofhost)) {
                $error.= construct_phrase($vbphrase['yrms_msg_error_blankfield'], $vbphrase['yrms_numberofhost']) . "\n";
            }         else {
                $number = 1;
                foreach ($this->downloadlink as $hostname => $hostlink) {
                    if (empty($hostname))
                        $error.= construct_phrase($vbphrase['yrms_msg_error_blankfield'], $vbphrase['yrms_hostname'] . " " . $number) . "\n";
                    if (empty($hostlink))
                        $error.= construct_phrase($vbphrase['yrms_msg_error_blankfield'], $vbphrase['yrms_hostlink'] . " " . $number) . "\n";
                    $number++;
                }
            }
        }
        // </editor-fold>
        
        // <editor-fold defaultstate="collapsed" desc=" for both ">
        //check chapter type
        if ($this->type == 2 && ($this->manga->finishedchapter > 0 || $this->manga->type == 1)) {
            $error.= construct_phrase($vbphrase['yrms_msg_error_invalidchaptertype'], $vbphrase['yrms_type'], $vbphrase['yrms_chaptertype2']) . "\n";
        }


        //check chapternumber
        if (empty($this->chapternumber) && $this->type != 2) {
            $error.= construct_phrase($vbphrase['yrms_msg_error_blankfield'], $vbphrase['yrms_chapternumber']) . "\n";
        }

        //check mangastatus
        if (empty($this->manga->status)) {
            $error.= construct_phrase($vbphrase['yrms_msg_error_blankselect'], $vbphrase['yrms_projectstatus']) . "\n";
        }


        //check rate
        if (!isset($this->rate)) {
            $error.= construct_phrase($vbphrase['yrms_msg_error_blankselect'], $vbphrase['yrms_rate']) . "\n";
        }


        //check translator
        if (empty($this->fansubmember["translator"])) {
            $error.= construct_phrase($vbphrase['yrms_msg_error_blankfield'], $vbphrase['yrms_translator']) . "\n";
        } else if (strpos($this->fansubmember["translator"], "false") !== false && strpos(strtolower($this->manga->fansubsite), 'yurivn')) {
            $error.= construct_phrase($vbphrase['yrms_msg_error_notvaliduser'], $vbphrase['yrms_translator']) . "\n";
        }

        //check proofreader
        if (!empty($this->fansubmember["proofreader"]) && strpos($this->fansubmember["proofreader"], "false") !== false && strpos(strtolower($this->manga->fansubsite), 'yurivn')) {
            $error.= construct_phrase($vbphrase['yrms_msg_error_notvaliduser'], $vbphrase['yrms_proofreader']) . "\n";
        }

        //check editor
        if (empty($this->fansubmember["editor"])) {
            $error.= construct_phrase($vbphrase['yrms_msg_error_blankfield'], $vbphrase['yrms_mangaeditor']) . "\n";
        } else if (strpos($this->fansubmember["editor"], "false") !== false && strpos(strtolower($this->manga->fansubsite), 'yurivn')) {
            $error.= construct_phrase($vbphrase['yrms_msg_error_notvaliduser'], $vbphrase['yrms_mangaeditor']) . "\n";
        }

        //check qualitychecker
        if (!empty($this->fansubmember["qualitychecker"]) && strpos($this->fansubmember["qualitychecker"], "false") !== false && strpos(strtolower($this->manga->fansubsite), 'yurivn')) {
            $error.= construct_phrase($vbphrase['yrms_msg_error_notvaliduser'], $vbphrase['yrms_qualitychecker']) . "\n";
        }

// </editor-fold>
        return $error;
        
    }
}

?>
